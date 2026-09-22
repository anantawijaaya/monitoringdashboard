<?php

namespace App\Http\Controllers\BudgetBK;

use App\Http\Controllers\Controller;
use App\Models\CultureProgramAllocation;
use App\Models\CultureProgramExpense;
use App\Services\BudgetCalculatorService;
use App\Services\CultureProgramImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CultureProgramController extends Controller
{
    protected BudgetCalculatorService $calculatorService;

    public function __construct(BudgetCalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    public function index(Request $request)
    {
        $clusterMap = [
            'all' => 'Semua Cluster',
            'BALI BARAT' => 'BALI BARAT',
            'BALI TENGAH' => 'BALI TENGAH',
            'BALI TIMUR' => 'BALI TIMUR',
            'ENDE SIKKA' => 'ENDE SIKKA',
            'FLORES TIMUR' => 'FLORES TIMUR',
            'MANGGARAI' => 'MANGGARAI',
            'KUPANG ROTE' => 'KUPANG ROTE',
            'MALAKA TIMTIM BELU' => 'MALAKA TIMTIM BELU',
            'SUMBA' => 'SUMBA',
            'LOMBOK' => 'LOMBOK',
            'SUMBAWA BARAT' => 'SUMBAWA BARAT',
            'SUMBAWA TIMUR' => 'SUMBAWA TIMUR',
        ];

        $user = Auth::user();
        $selectedCluster = $request->get('cluster', 'all');
        $selectedHistoryCluster = $request->get('history_cluster', 'all');

        // Apply Cluster Scope Lock for Cluster Visitor Accounts
        if ($user && $user->isVisitor() && $user->hasClusterLock()) {
            $selectedCluster = $user->cluster_name;
            $selectedHistoryCluster = $user->cluster_name;
        }

        $selectedProgram = $request->get('program', 'all');
        $selectedSort = $request->get('sort', 'default');

        // Stats Summary (Total Alokasi, Total Realisasi, % Realisasi, Sisa Budget)
        $summary = $this->calculatorService->calculateSummary(
            CultureProgramAllocation::class,
            CultureProgramExpense::class,
            $selectedCluster
        );
        $totalAlokasi = $summary['totalAlokasi'];
        $totalRealisasi = $summary['totalRealisasi'];
        $totalRealisasiPersen = $summary['totalRealisasiPersen'];
        $sisaBudget = $summary['sisaBudget'];

        // Program Cards calculation
        $cardsDef = [
            [
                'key' => 'culture',
                'title' => 'Culture Program',
                'col' => 'culture_program_budget',
                'icon_html' => '<i class="bi bi-heart-pulse-fill"></i>',
                'bg_icon' => 'bg-red-500',
            ],
        ];

        $programCardsList = $this->calculatorService->calculateProgramCards(
            CultureProgramAllocation::class,
            CultureProgramExpense::class,
            $cardsDef,
            $selectedCluster,
            $selectedSort
        );

        // History Table Filtered by History Filter ($selectedHistoryCluster) and Program ($selectedProgram)
        $expensesQuery = CultureProgramExpense::query();
        if ($selectedHistoryCluster !== 'all') {
            $expensesQuery->where('cluster', $selectedHistoryCluster);
        }
        if ($selectedProgram !== 'all') {
            $progNameMap = [
                'culture' => 'Culture Program',
            ];
            if (isset($progNameMap[$selectedProgram])) {
                $expensesQuery->where('program_name', $progNameMap[$selectedProgram]);
            }
        }
        $expenses = $expensesQuery->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $clusterProgramBudgets = $this->calculatorService->buildClusterProgramBudgets(
            CultureProgramAllocation::class,
            CultureProgramExpense::class,
            $clusterMap,
            $cardsDef
        );

        $defaultMitraMap = [
            'BALI BARAT' => 'PT AKAR DAYA',
            'BALI TENGAH' => 'PT. CATALIST INTEGRA PRIMA SUKSES',
            'BALI TIMUR' => 'PT AKAR DAYA',
            'ENDE SIKKA' => 'CV. RAJAWALI CELLULAR',
            'FLORES TIMUR' => 'CV. RAJAWALI CELLULAR',
            'MANGGARAI' => 'CV. RAJAWALI CELLULAR',
            'KUPANG ROTE' => 'PT. NARINDO SOLUSI TELEKOMUNIKASI',
            'MALAKA TIMTIM B' => 'PT. NARINDO SOLUSI TELEKOMUNIKASI',
            'MALAKA TIMTIM BELU' => 'PT. NARINDO SOLUSI TELEKOMUNIKASI',
            'SUMBA' => 'CV. RAJAWALI CELLULAR',
            'LOMBOK' => 'PT AKAR DAYA',
            'SUMBAWA BARAT' => 'BERKAH KARUNIA KREASI',
            'SUMBAWA TIMUR' => 'PT KINARYA SELARAS SOLUSI',
        ];
        $dbMitraMap = CultureProgramAllocation::whereNotNull('mitra')
            ->where('mitra', '!=', '')
            ->where('mitra', '!=', 'All Mitra')
            ->pluck('mitra', 'cluster')
            ->toArray();
        $clusterMitraMap = array_merge($defaultMitraMap, $dbMitraMap);

        return view('budget-bk.culture-program.index', compact(
            'clusterMap',
            'selectedCluster',
            'selectedHistoryCluster',
            'selectedProgram',
            'selectedSort',
            'totalAlokasi',
            'totalRealisasi',
            'totalRealisasiPersen',
            'sisaBudget',
            'programCardsList',
            'expenses',
            'clusterProgramBudgets',
            'clusterMitraMap'
        ));
    }

    public function export(Request $request)
    {
        $selectedCluster = $request->get('cluster', 'all');
        $selectedProgram = $request->get('program', 'all');

        $query = CultureProgramExpense::query();

        if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
            $query->where('cluster', $selectedCluster);
        }

        if ($selectedProgram !== 'all' && !empty($selectedProgram)) {
            $query->where('program_name', $selectedProgram);
        }

        $records = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();
        $filename = 'export_culture_program_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID',
                'Cluster',
                'Mitra',
                'Nama Program',
                'Tanggal',
                'Waktu',
                'Deskripsi / Aktivitas',
                'Catatan / Note',
                'Nominal Pengeluaran',
                'Budget Program',
                'Sisa Budget Program',
                'Status'
            ]);

            foreach ($records as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->cluster,
                    $row->mitra,
                    $row->program_name,
                    $row->tanggal,
                    $row->waktu,
                    $row->deskripsi,
                    $row->note,
                    round((float) $row->nominal_pengeluaran),
                    round((float) $row->budget_program),
                    round((float) $row->sisa_budget_program),
                    $row->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request, CultureProgramImportService $importService)
    {
        if (Auth::user() && Auth::user()->isVisitor()) {
            return redirect()->back()->with('error', 'Akses ditolak. Akun Visitor tidak diizinkan melakukan import data.');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,zip|max:51200',
        ]);

        try {
            $importService->import($request->file('file'));
            return redirect()->back()->with('success', 'Data Culture Program berhasil di-import.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'cluster' => 'nullable|string',
            'mitra' => 'nullable|string',
            'program_name' => 'required|string',
            'tanggal' => 'nullable|date',
            'waktu' => 'nullable',
            'deskripsi' => 'required|string',
            'note' => 'nullable|string',
            'budget_program' => 'required|numeric',
            'nominal_pengeluaran' => 'required|numeric',
            'sisa_budget_program' => 'required|numeric',
            'evidence' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'evidence.required' => 'Dokumen invoice (bukti) wajib diunggah sebelum menyimpan pengajuan.',
            'evidence.mimes' => 'Format file evidence harus berupa PDF, JPG, JPEG, atau PNG.',
            'evidence.max' => 'Ukuran file evidence tidak boleh melebihi 10MB.',
        ]);

        if (empty($validated['tanggal'])) {
            $validated['tanggal'] = date('Y-m-d');
        }
        if (empty($validated['waktu'])) {
            $validated['waktu'] = date('H:i:s');
        }

        $user = Auth::user();
        if ($user && $user->isVisitor() && $user->hasClusterLock()) {
            $validated['cluster'] = $user->cluster_name;
        }

        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            $path = $file->store('evidences', 'public');
            $validated['evidence_path'] = $path;
            $validated['evidence_original_name'] = $file->getClientOriginalName();
        }

        $validated['status'] = 'Menunggu';

        CultureProgramExpense::create($validated);

        return redirect()->back()->with('success', 'Pengajuan budgeting berhasil disimpan.');
    }

    public function updateExpense(Request $request, $id)
    {
        $expense = CultureProgramExpense::findOrFail($id);

        $validated = $request->validate([
            'cluster' => 'nullable|string',
            'mitra' => 'nullable|string',
            'program_name' => 'required|string',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'deskripsi' => 'required|string',
            'note' => 'nullable|string',
            'budget_program' => 'required|numeric',
            'nominal_pengeluaran' => 'required|numeric',
            'sisa_budget_program' => 'required|numeric',
            'evidence' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'evidence.mimes' => 'Format file evidence harus berupa PDF, JPG, JPEG, atau PNG.',
            'evidence.max' => 'Ukuran file evidence tidak boleh melebihi 10MB.',
        ]);

        if ($request->hasFile('evidence')) {
            if ($expense->evidence_path) {
                Storage::disk('public')->delete($expense->evidence_path);
            }
            $file = $request->file('evidence');
            $path = $file->store('evidences', 'public');
            $validated['evidence_path'] = $path;
            $validated['evidence_original_name'] = $file->getClientOriginalName();
        }

        $expense->update($validated);

        return redirect()->back()->with('success', 'Data pengajuan budgeting berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak,Menunggu',
        ]);

        $expense = CultureProgramExpense::findOrFail($id);
        $expense->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function destroyExpense($id)
    {
        $expense = CultureProgramExpense::findOrFail($id);
        if ($expense->evidence_path) {
            Storage::disk('public')->delete($expense->evidence_path);
        }
        $expense->delete();

        return redirect()->back()->with('success', 'Pengajuan berhasil dihapus.');
    }
}
