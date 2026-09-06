<?php

namespace App\Http\Controllers;

use App\Models\HierarchyOutlet;
use App\Services\HierarchyImportService;
use Database\Seeders\HierarchyOutletSeeder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HierarchyController extends Controller
{
    protected HierarchyImportService $importService;

    public function __construct(HierarchyImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Display Data Cluster, Mitra, Branch & Jumlah Outlet page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Auto-seed if database table is empty
        if (HierarchyOutlet::count() === 0) {
            (new HierarchyOutletSeeder())->run();
        }

        // Filters
        $search = $request->query('search');
        $selectedBranch = $request->query('branch', 'all');
        $selectedCluster = $request->query('cluster', 'all');
        $selectedMitra = $request->query('mitra', 'all');
        $perPage = intval($request->query('per_page', 10));
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        // Extract dropdown options for filter menus
        $availableBranches = HierarchyOutlet::distinct()->pluck('branch')->filter()->values();
        $availableClusters = HierarchyOutlet::distinct()->pluck('cluster')->filter()->values();
        $availableMitras = HierarchyOutlet::distinct()->pluck('mitra')->filter()->values();

        // Build Query
        $query = HierarchyOutlet::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kabupaten', 'LIKE', "%{$search}%")
                  ->orWhere('cluster', 'LIKE', "%{$search}%")
                  ->orWhere('mitra', 'LIKE', "%{$search}%")
                  ->orWhere('branch', 'LIKE', "%{$search}%")
                  ->orWhere('manager_branch', 'LIKE', "%{$search}%");
            });
        }

        if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
            $query->where('branch', $selectedBranch);
        }

        if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
            $query->where('cluster', $selectedCluster);
        }

        if ($selectedMitra !== 'all' && !empty($selectedMitra)) {
            $query->where('mitra', $selectedMitra);
        }

        $totalFilteredCount = $query->count();
        $paginatedData = $query->paginate($perPage)->withQueryString();

        // Top 4 Summary Cards Metrics
        $totalClusterCount = HierarchyOutlet::distinct()->count('cluster') ?: 12;
        $totalMitraCount = HierarchyOutlet::distinct()->count('mitra') ?: 6;
        $totalBranchCount = HierarchyOutlet::distinct()->count('branch') ?: 4;
        $totalKabupatenCount = HierarchyOutlet::count() ?: 41;
        
        $totalOutletSum = HierarchyOutlet::select('jumlah_outlet')->get()->sum(function ($item) {
            return (int) preg_replace('/[^0-9]/', '', (string) $item->jumlah_outlet);
        });
        $totalOutletDisplay = number_format($totalOutletSum ?: 16799, 0, ',', '.');

        return view('monitoring-kpi.hierarchy.index', compact(
            'user',
            'paginatedData',
            'totalFilteredCount',
            'availableBranches',
            'availableClusters',
            'availableMitras',
            'selectedBranch',
            'selectedCluster',
            'selectedMitra',
            'search',
            'perPage',
            'totalClusterCount',
            'totalMitraCount',
            'totalOutletDisplay',
            'totalBranchCount',
            'totalKabupatenCount'
        ));
    }

    /**
     * Update an existing Hierarchy / Outlet record
     */
    public function update(Request $request, $id)
    {
        try {
            $outlet = HierarchyOutlet::findOrFail($id);

            $request->validate([
                'kabupaten' => 'required|string|max:255',
                'cluster' => 'required|string|max:255',
                'mitra' => 'required|string|max:255',
                'branch' => 'required|string|max:255',
                'jumlah_outlet' => 'required|string|max:50',
                'manager_branch' => 'required|string|max:255',
            ]);

            $groupData = $this->importService->getGroupClass($request->input('cluster'));

            $outlet->update([
                'kabupaten' => strtoupper(trim($request->input('kabupaten'))),
                'cluster' => strtoupper(trim($request->input('cluster'))),
                'mitra' => strtoupper(trim($request->input('mitra'))),
                'branch' => strtoupper(trim($request->input('branch'))),
                'jumlah_outlet' => trim($request->input('jumlah_outlet')),
                'manager_branch' => strtoupper(trim($request->input('manager_branch'))),
                'group_class' => $groupData['group_class'],
                'badge_color' => $groupData['badge_color'],
            ]);

            $msg = "Data '{$outlet->kabupaten}' ({$outlet->cluster}) berhasil diperbarui!";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                ]);
            }

            return redirect()->back()->with('success', $msg);
        } catch (Exception $e) {
            $cleanMsg = mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8');
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal memperbarui data: " . $cleanMsg
                ], 422);
            }

            return redirect()->back()->with('error', "Gagal memperbarui data: " . $cleanMsg);
        }
    }

    /**
     * Import CSV / Excel file (.xlsx, .csv, .xls) directly into the hierarchy_outlets database table
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB max
            'mode' => 'nullable|string|in:replace,append',
        ]);

        $file = $request->file('file');
        $importMode = $request->input('mode', 'replace');

        try {
            $result = $this->importService->importFile($file, $importMode);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'imported_count' => $result['count'],
                ]);
            }

            return redirect()->route('hierarchy.index')->with('success', $result['message']);
        } catch (Exception $e) {
            $cleanMsg = mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal mengimpor berkas: " . $cleanMsg
                ], 422);
            }

            return redirect()->route('hierarchy.index')->with('error', "Gagal mengimpor berkas: " . $cleanMsg);
        }
    }

    /**
     * Download CSV template for Cluster, Mitra, Branch & Outlet import
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="template_import_cluster_outlet_balinusra.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            // Header Row
            fputcsv($handle, [
                'KABUPATEN / KOTA',
                'CLUSTER',
                'MITRA',
                'BRANCH',
                'JUMLAH OUTLET',
                'MANAGER BRANCH'
            ], ';');

            // Example Template Rows
            fputcsv($handle, ['JEMBRANA', 'BALI BARAT', 'PT AKAR DAYA', 'DENPASAR', '1.139', 'Herbianto'], ';');
            fputcsv($handle, ['BADUNG', 'BALI TENGAH', 'PT. CATALIST INTEGRA PRIMA SUKSES', 'DENPASAR', '1.690', 'Herbianto'], ';');
            fputcsv($handle, ['GIANYAR', 'BALI TIMUR', 'PT AKAR DAYA', 'DENPASAR', '1.322', 'Herbianto'], ';');
            fputcsv($handle, ['SIKKA', 'ENDE SIKKA', 'CV. RAJAWALI CELLULAR', 'FLORES', '954', 'Waskitho Anjar Prabowo'], ';');
            fputcsv($handle, ['NGADA', 'MANGGARAI', 'CV. RAJAWALI CELLULAR', 'FLORES', '2.024', 'Waskitho Anjar Prabowo'], ';');
            fputcsv($handle, ['KOTA KUPANG', 'KUPANG ROTE', 'PT. NARINDO SOLUSI TELEKOMUNIKASI', 'KUPANG', '1.050', 'Adhy Yanwar'], ';');
            fputcsv($handle, ['BELU', 'MALAKA TIMTIM B', 'PT. NARINDO SOLUSI TELEKOMUNIKASI', 'KUPANG', '1.476', 'Adhy Yanwar'], ';');
            fputcsv($handle, ['SUMBA BARAT', 'SUMBA', 'CV. RAJAWALI CELLULAR', 'KUPANG', '1.319', 'Adhy Yanwar'], ';');
            fputcsv($handle, ['KOTA MATARAM', 'LOMBOK', 'PT AKAR DAYA', 'MATARAM', '2.524', 'Kurnia Budi Setiawan'], ';');
            fputcsv($handle, ['SUMBAWA', 'SUMBAWA BARAT', 'PT BERKAH KARUNIA KREASI', 'MATARAM', '986', 'Kurnia Budi Setiawan'], ';');
            fputcsv($handle, ['BIMA', 'SUMBAWA TIMUR', 'PT KINARYA SELARAS SOLUSI', 'MATARAM', '1.262', 'Kurnia Budi Setiawan'], ';');

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export hierarchy / outlet distribution data to CSV / Excel
     */
    public function exportExcel(Request $request)
    {
        $search = $request->query('search');
        $selectedBranch = $request->query('branch', 'all');
        $selectedCluster = $request->query('cluster', 'all');
        $selectedMitra = $request->query('mitra', 'all');

        $query = HierarchyOutlet::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kabupaten', 'LIKE', "%{$search}%")
                  ->orWhere('cluster', 'LIKE', "%{$search}%")
                  ->orWhere('mitra', 'LIKE', "%{$search}%")
                  ->orWhere('branch', 'LIKE', "%{$search}%")
                  ->orWhere('manager_branch', 'LIKE', "%{$search}%");
            });
        }

        if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
            $query->where('branch', $selectedBranch);
        }

        if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
            $query->where('cluster', $selectedCluster);
        }

        if ($selectedMitra !== 'all' && !empty($selectedMitra)) {
            $query->where('mitra', $selectedMitra);
        }

        $records = $query->orderBy('branch')->orderBy('cluster')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="data_cluster_mitra_branch_outlet_' . date('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            // Header Row
            fputcsv($handle, [
                'NO',
                'KABUPATEN / KOTA',
                'CLUSTER',
                'MITRA',
                'BRANCH',
                'JUMLAH OUTLET',
                'MANAGER BRANCH'
            ], ';');

            $num = 1;
            foreach ($records as $row) {
                fputcsv($handle, [
                    $num++,
                    $row->kabupaten,
                    $row->cluster,
                    $row->mitra,
                    $row->branch,
                    $row->jumlah_outlet,
                    $row->manager_branch
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
