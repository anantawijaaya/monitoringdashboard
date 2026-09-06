<?php

namespace App\Http\Controllers;

use App\Models\RegionalOutlet;
use App\Services\RegionalOutletImportService;
use Database\Seeders\RegionalOutletSeeder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class RegionalMapController extends Controller
{
    protected RegionalOutletImportService $importService;

    public function __construct(RegionalOutletImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Display Regional Map & Heatmap Page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Auto-seed if database table is empty
        if (RegionalOutlet::count() === 0) {
            (new RegionalOutletSeeder())->run();
        }

        // Request Filter Parameters
        $search = $request->query('search');
        $selectedBranch = $request->query('branch', 'all');
        $selectedCluster = $request->query('cluster', 'all');
        $selectedKabupaten = $request->query('kabupaten', 'all');
        $selectedFlag = $request->query('flag', 'all');
        $perPage = intval($request->query('per_page', 10));
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        // Distinct filter options
        $availableBranches = RegionalOutlet::distinct()->orderBy('branch')->pluck('branch')->filter()->values();
        $availableClusters = RegionalOutlet::distinct()->orderBy('cluster')->pluck('cluster')->filter()->values();
        $availableKabupatens = RegionalOutlet::distinct()->orderBy('kabupaten')->pluck('kabupaten')->filter()->values();

        // Absolute Grand Total Outlets (always 16,799 regardless of any filter selected)
        $grandTotalOutlets = RegionalOutlet::count();

        // Base Query with search, branch, cluster, and kabupaten filters applied
        $baseQuery = RegionalOutlet::query();

        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('id_outlet', 'LIKE', "%{$search}%")
                  ->orWhere('kabupaten', 'LIKE', "%{$search}%")
                  ->orWhere('cluster', 'LIKE', "%{$search}%")
                  ->orWhere('branch', 'LIKE', "%{$search}%");
            });
        }

        if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
            $baseQuery->where('branch', $selectedBranch);
        }

        if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
            $baseQuery->where('cluster', $selectedCluster);
        }

        if ($selectedKabupaten !== 'all' && !empty($selectedKabupaten)) {
            $baseQuery->where('kabupaten', $selectedKabupaten);
        }

        // Base Statistics calculated on base filtered query (for Flag Box & Avg Omzet)
        $baseStats = (clone $baseQuery)->selectRaw('
            COUNT(*) as total_outlets,
            COALESCE(SUM(total_omzet), 0) as total_omzet,
            COALESCE(SUM(CASE WHEN flag_omzet < 0 THEN 1 ELSE 0 END), 0) as count_black,
            COALESCE(SUM(CASE WHEN flag_omzet = 0 THEN 1 ELSE 0 END), 0) as count_red,
            COALESCE(SUM(CASE WHEN flag_omzet > 0 AND flag_omzet <= 3.0 THEN 1 ELSE 0 END), 0) as count_yellow,
            COALESCE(SUM(CASE WHEN flag_omzet > 3.0 THEN 1 ELSE 0 END), 0) as count_green
        ')->first();

        $countBlack = (int) ($baseStats->count_black ?? 0);
        $countRed = (int) ($baseStats->count_red ?? 0);
        $countYellow = (int) ($baseStats->count_yellow ?? 0);
        $countGreen = (int) ($baseStats->count_green ?? 0);
        $filteredBaseCount = (int) ($baseStats->total_outlets ?? 0);

        // Apply Flag Omzet filter to final query
        $query = clone $baseQuery;

        $flagBoxTitle = 'SEMUA KATEGORI FLAG';
        $flagBoxCount = $filteredBaseCount;

        if ($selectedFlag !== 'all' && !empty($selectedFlag)) {
            if ($selectedFlag === 'black' || $selectedFlag === 'white') {
                $query->where('flag_omzet', '<', 0);
                $flagBoxTitle = 'FLAG OMZET < 0%';
                $flagBoxCount = $countBlack;
            } elseif ($selectedFlag === 'red') {
                $query->where('flag_omzet', '=', 0);
                $flagBoxTitle = 'FLAG OMZET = 0%';
                $flagBoxCount = $countRed;
            } elseif ($selectedFlag === 'yellow' || $selectedFlag === 'orange') {
                $query->where('flag_omzet', '>', 0)->where('flag_omzet', '<=', 3.0);
                $flagBoxTitle = 'FLAG OMZET <= 3%';
                $flagBoxCount = $countYellow;
            } elseif ($selectedFlag === 'green') {
                $query->where('flag_omzet', '>', 3.0);
                $flagBoxTitle = 'FLAG OMZET > 3%';
                $flagBoxCount = $countGreen;
            }
        }

        // Total Outlets for Box 1 is ALWAYS the absolute overall count (16,799)
        $totalOutlets = $grandTotalOutlets;
        $totalOmzet = (float) ($baseStats->total_omzet ?? 0);
        $avgOmzet = $filteredBaseCount > 0 ? ($totalOmzet / $filteredBaseCount) : 0;
        $formattedAvgOmzet = 'Rp ' . number_format($avgOmzet, 0, ',', '.');

        // Paginated table data
        $outlets = $query->orderBy('id', 'asc')->paginate($perPage)->withQueryString();

        return view('monitoring-kpi.peta-regional.index', compact(
            'user',
            'outlets',
            'totalOutlets',
            'totalOmzet',
            'formattedAvgOmzet',
            'flagBoxTitle',
            'flagBoxCount',
            'countBlack',
            'countRed',
            'countYellow',
            'countGreen',
            'availableBranches',
            'availableClusters',
            'availableKabupatens',
            'selectedBranch',
            'selectedCluster',
            'selectedKabupaten',
            'selectedFlag',
            'search',
            'perPage'
        ));
    }

    /**
     * AJAX endpoint to fetch markers as JSON
     */
    public function getMarkersJson(Request $request)
    {
        $search = $request->query('search');
        $selectedBranch = $request->query('branch', 'all');
        $selectedCluster = $request->query('cluster', 'all');
        $selectedKabupaten = $request->query('kabupaten', 'all');
        $selectedFlag = $request->query('flag', 'all');

        $query = RegionalOutlet::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id_outlet', 'LIKE', "%{$search}%")
                  ->orWhere('kabupaten', 'LIKE', "%{$search}%")
                  ->orWhere('cluster', 'LIKE', "%{$search}%")
                  ->orWhere('branch', 'LIKE', "%{$search}%");
            });
        }

        if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
            $query->where('branch', $selectedBranch);
        }

        if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
            $query->where('cluster', $selectedCluster);
        }

        if ($selectedKabupaten !== 'all' && !empty($selectedKabupaten)) {
            $query->where('kabupaten', $selectedKabupaten);
        }

        if ($selectedFlag !== 'all' && !empty($selectedFlag)) {
            if ($selectedFlag === 'black') {
                $query->where('flag_omzet', '<', 0);
            } elseif ($selectedFlag === 'red') {
                $query->where('flag_omzet', '=', 0);
            } elseif ($selectedFlag === 'yellow') {
                $query->where('flag_omzet', '>', 0)->where('flag_omzet', '<=', 3.0);
            } elseif ($selectedFlag === 'green') {
                $query->where('flag_omzet', '>', 3.0);
            }
        }

        $markers = $query->select([
            'id',
            'id_outlet',
            'longitude',
            'latitude',
            'kabupaten',
            'cluster',
            'branch',
            'total_omzet',
            'flag_omzet'
        ])->orderBy('id', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'count' => $markers->count(),
            'data' => $markers,
        ]);
    }

    /**
     * Store new outlet manually (Admin only)
     */
    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'id_outlet' => 'required|string|max:100|unique:regional_outlets,id_outlet',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'kabupaten' => 'required|string|max:100',
            'cluster' => 'required|string|max:100',
            'branch' => 'required|string|max:100',
            'total_omzet' => 'required|numeric|min:0',
            'flag_omzet' => 'required|numeric',
        ]);

        RegionalOutlet::create($validated);

        return redirect()->route('regional-map.index')->with('success', "Outlet {$validated['id_outlet']} berhasil ditambahkan!");
    }

    /**
     * Update outlet data (Admin only)
     */
    public function update(Request $request, $id)
    {
        $outlet = RegionalOutlet::findOrFail($id);

        $validated = $request->validate([
            'id_outlet' => 'required|string|max:100|unique:regional_outlets,id_outlet,' . $outlet->id,
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'kabupaten' => 'required|string|max:100',
            'cluster' => 'required|string|max:100',
            'branch' => 'required|string|max:100',
            'total_omzet' => 'required|numeric|min:0',
            'flag_omzet' => 'required|numeric',
        ]);

        $outlet->update($validated);

        return redirect()->route('regional-map.index')->with('success', "Data Outlet {$outlet->id_outlet} berhasil diperbarui!");
    }

    /**
     * Delete outlet (Admin only)
     */
    public function destroy($id)
    {
        $outlet = RegionalOutlet::findOrFail($id);
        $idOutlet = $outlet->id_outlet;
        $outlet->delete();

        return redirect()->route('regional-map.index')->with('success', "Outlet {$idOutlet} berhasil dihapus!");
    }

    /**
     * Import Excel or CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:51200', // 50MB max
            'import_mode' => 'required|in:replace,append',
        ]);

        try {
            $service = new RegionalOutletImportService();
            $result = $service->importFile($request->file('file'), $request->input('import_mode'));

            return redirect()->route('regional-map.index')->with('success', "Import berhasil! {$result['inserted']} ditambahkan, {$result['updated']} diperbarui dari total {$result['total_processed']} baris.");
        } catch (\Exception $e) {
            return redirect()->route('regional-map.index')->with('error', "Gagal mengimpor file: " . $e->getMessage());
        }
    }

    /**
     * Download CSV Template
     */
    public function downloadTemplate()
    {
        $filename = "Template_Import_Peta_Regional.csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'id_outlet',
            'longitude',
            'latitude',
            'kabupaten',
            'cluster',
            'branch',
            'total_omzet',
            'flag_omzet'
        ];

        $sampleData = [
            ['OUT-DPS-001', '115.2078', '-8.6782', 'Kota Denpasar', 'Cluster Denpasar Kota', 'Branch Denpasar', '245000000', '5.40'],
            ['OUT-DPS-002', '115.2625', '-8.6881', 'Kota Denpasar', 'Cluster Denpasar Selatan', 'Branch Denpasar', '180000000', '2.10'],
            ['OUT-DPS-003', '115.2250', '-8.6360', 'Kota Denpasar', 'Cluster Denpasar Utara', 'Branch Denpasar', '95000000', '-1.85'],
            ['OUT-BDG-001', '115.1785', '-8.7125', 'Badung', 'Cluster Kuta Badung', 'Branch Denpasar', '310000000', '6.75'],
            ['OUT-BDG-002', '115.1380', '-8.6510', 'Badung', 'Cluster Badung Utara', 'Branch Denpasar', '140000000', '0.00'],
            ['OUT-MTR-001', '116.1210', '-8.5830', 'Kota Mataram', 'Cluster Mataram Kota', 'Branch Mataram', '280000000', '<0%'],
            ['OUT-LBJ-001', '119.8820', '-8.4980', 'Manggarai Barat', 'Cluster Labuan Bajo', 'Branch Flores Barat', '320000000', '<=3%'],
            ['OUT-KPG-001', '123.6040', '-10.1750', 'Kota Kupang', 'Cluster Kupang Kota', 'Branch Kupang', '290000000', '>3%'],
        ];

        $callback = function () use ($columns, $sampleData) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export current filtered outlets to CSV
     */
    public function exportData(Request $request)
    {
        $search = $request->query('search');
        $selectedBranch = $request->query('branch', 'all');
        $selectedCluster = $request->query('cluster', 'all');
        $selectedKabupaten = $request->query('kabupaten', 'all');
        $selectedFlag = $request->query('flag', 'all');

        $query = RegionalOutlet::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id_outlet', 'LIKE', "%{$search}%")
                  ->orWhere('kabupaten', 'LIKE', "%{$search}%")
                  ->orWhere('cluster', 'LIKE', "%{$search}%")
                  ->orWhere('branch', 'LIKE', "%{$search}%");
            });
        }

        if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
            $query->where('branch', $selectedBranch);
        }

        if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
            $query->where('cluster', $selectedCluster);
        }

        if ($selectedKabupaten !== 'all' && !empty($selectedKabupaten)) {
            $query->where('kabupaten', $selectedKabupaten);
        }

        if ($selectedFlag !== 'all' && !empty($selectedFlag)) {
            if ($selectedFlag === 'white' || $selectedFlag === 'black') {
                $query->where('flag_omzet', '<', 0);
            } elseif ($selectedFlag === 'red') {
                $query->where('flag_omzet', '=', 0);
            } elseif ($selectedFlag === 'orange' || $selectedFlag === 'yellow') {
                $query->where('flag_omzet', '>', 0)->where('flag_omzet', '<=', 3.0);
            } elseif ($selectedFlag === 'green') {
                $query->where('flag_omzet', '>', 3.0);
            }
        }

        $outlets = $query->orderBy('id', 'asc')->get();

        $filename = "Export_Data_Outlet_Peta_Regional_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'ID Outlet',
            'Longitude',
            'Latitude',
            'Kabupaten',
            'Cluster',
            'Branch',
            'Total Omzet (Rp)',
            'Flag Omzet (%)',
            'Status Performa',
            'Kategori Warna'
        ];

        $callback = function () use ($columns, $outlets) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($outlets as $o) {
                fputcsv($file, [
                    $o->id_outlet,
                    $o->longitude,
                    $o->latitude,
                    $o->kabupaten,
                    $o->cluster,
                    $o->branch,
                    $o->total_omzet,
                    $o->flag_omzet,
                    $o->flag_status_label,
                    ucfirst($o->flag_color),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Reset outlet data to default seed (Admin only)
     */
    public function resetData()
    {
        (new RegionalOutletSeeder())->run();
        return redirect()->route('regional-map.index')->with('success', 'Data Peta Regional berhasil direset ke dataset awal!');
    }
}
