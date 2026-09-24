<?php

namespace App\Http\Controllers;

use App\Models\ClusterRevenue;
use App\Services\RevenueImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Exception;

class RevenueDataController extends Controller
{
    /**
     * Display the dedicated "Kelola Revenue" management page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Filters
        $search = $request->query('search');
        $selectedCluster = $request->query('cluster', 'all');
        $selectedKabupaten = $request->query('kabupaten', 'all');
        $selectedPeriod = $request->query('period', 'all');
        $selectedYear = $request->query('year', 'all');
        $selectedStatus = $request->query('status', 'all');
        $sortBy = $request->query('sort', 'default');

        // Available Filter Dropdowns
        $availableClusters = ClusterRevenue::distinct()->pluck('cluster_name')->filter()->sort()->values();
        $availableKabupatens = ClusterRevenue::distinct()->pluck('kabupaten')->filter()->sort()->values();
        $availablePeriods = \App\Http\Controllers\KelasKpiController::sortPeriodesChronologically(
            ClusterRevenue::distinct()->pluck('period_month')->filter()
        );
        $availableYears = ClusterRevenue::distinct()->pluck('period_year')->filter()->values();

        // Query Builder
        $query = ClusterRevenue::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('cluster_name', 'LIKE', "%{$search}%")
                  ->orWhere('kabupaten', 'LIKE', "%{$search}%")
                  ->orWhere('period_month', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }

        if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
            $query->where('cluster_name', $selectedCluster);
        }

        if ($selectedKabupaten !== 'all' && !empty($selectedKabupaten)) {
            $query->where('kabupaten', $selectedKabupaten);
        }

        if ($selectedPeriod !== 'all' && !empty($selectedPeriod)) {
            $query->where('period_month', $selectedPeriod);
        }

        if ($selectedYear !== 'all' && !empty($selectedYear)) {
            $query->where('period_year', intval($selectedYear));
        }

        if ($selectedStatus !== 'all' && !empty($selectedStatus)) {
            if ($selectedStatus === 'melampaui_target' || $selectedStatus === 'Melampaui Target' || $selectedStatus === 'above_100') {
                $query->where(function ($q) {
                    $q->where('growth_mom', '>=', 1.0)
                      ->orWhere('notes', 'LIKE', '%Melampaui%')
                      ->orWhere('status', 'LIKE', '%Melampaui%');
                });
            } elseif ($selectedStatus === 'mencapai_target' || $selectedStatus === 'Mencapai Target' || $selectedStatus === 'between_98_100') {
                $query->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('growth_mom', '>=', 0)
                            ->where('growth_mom', '<', 1.0);
                    })->orWhere(function ($sub) {
                        $sub->where('notes', 'LIKE', '%Mencapai Target%')
                            ->where('notes', 'NOT LIKE', '%Tidak%')
                            ->where('notes', 'NOT LIKE', '%Melampaui%');
                    });
                });
            } elseif ($selectedStatus === 'tidak_mencapai_target' || $selectedStatus === 'Tidak Mencapai Target' || $selectedStatus === 'below_98') {
                $query->where(function ($q) {
                    $q->where('growth_mom', '<', 0)
                      ->orWhere('notes', 'LIKE', '%Tidak Mencapai%')
                      ->orWhere('status', 'LIKE', '%Tidak Mencapai%');
                });
            }
        }

        // Sorting
        switch ($sortBy) {
            case 'ach_desc':
                $query->orderByDesc('ach_revenue_all');
                break;
            case 'ach_asc':
                $query->orderBy('ach_revenue_all', 'asc');
                break;
            case 'growth_desc':
                $query->orderByDesc('growth_mom');
                break;
            case 'growth_asc':
                $query->orderBy('growth_mom', 'asc');
                break;
            case 'mtd_desc':
                $query->orderByDesc('mtd_revenue_all');
                break;
            case 'name_asc':
                $query->orderBy('cluster_name', 'asc')->orderBy('kabupaten', 'asc');
                break;
            case 'kabupaten_asc':
                $query->orderBy('kabupaten', 'asc');
                break;
            default:
                $query->orderBy('cluster_name', 'asc')->orderBy('kabupaten', 'asc');
                break;
        }

        // Summary KPI Metrics (Calculated dynamically based on active filter selections)
        $filteredClusters = (clone $query)->get();
        $totalRecords = $filteredClusters->count();
        $distinctClustersCount = $filteredClusters->pluck('cluster_name')->unique()->count();
        $totalTargetAll = $filteredClusters->sum('target_revenue_all') ?: $filteredClusters->sum('target_revenue');
        $totalMtdAll = $filteredClusters->sum('mtd_revenue_all') ?: $filteredClusters->sum('revenue_all');
        $avgAchAll = ($totalTargetAll > 0) ? round(($totalMtdAll / $totalTargetAll) * 100, 1) : 0;
        $avgGrowthMoM = $totalRecords > 0 ? round($filteredClusters->avg('growth_mom'), 1) : 0;

        // Breakdown MTD Revenue (Broadband, Redeem PV, RGB)
        $totalMtdBroadband = $filteredClusters->sum('mtd_broadband') ?: $filteredClusters->sum('revenue_broadband');
        $totalMtdRedeem = $filteredClusters->sum('mtd_redeem') ?: $filteredClusters->sum('revenue_redeem_pv');
        $totalMtdRgb = $filteredClusters->sum('mtd_rgb') ?: $filteredClusters->sum('revenue_rgb');

        $portionMtdBb = ($totalMtdAll > 0) ? round(($totalMtdBroadband / $totalMtdAll) * 100, 1) : 0;
        $portionMtdRedeem = ($totalMtdAll > 0) ? round(($totalMtdRedeem / $totalMtdAll) * 100, 1) : 0;
        $portionMtdRgb = ($totalMtdAll > 0) ? round(($totalMtdRgb / $totalMtdAll) * 100, 1) : 0;

        $clusterRevenues = $query->paginate(20)->withQueryString();

        return view('monitoring-kpi.revenue.index', compact(
            'user',
            'clusterRevenues',
            'totalRecords',
            'distinctClustersCount',
            'totalTargetAll',
            'totalMtdAll',
            'avgAchAll',
            'avgGrowthMoM',
            'totalMtdBroadband',
            'totalMtdRedeem',
            'totalMtdRgb',
            'portionMtdBb',
            'portionMtdRedeem',
            'portionMtdRgb',
            'availableClusters',
            'availableKabupatens',
            'availablePeriods',
            'availableYears',
            'search',
            'selectedCluster',
            'selectedKabupaten',
            'selectedPeriod',
            'selectedYear',
            'selectedStatus',
            'sortBy'
        ));
    }

    /**
     * Store manual revenue data input per Kabupaten matching specifications:
     * 1. Cluster & Kabupaten (Separate)
     * 2. Revenue All (Target, MTD)
     * 3. Revenue Broadband (Target, MTD)
     * 4. Revenue Redeem (Target, MTD)
     * 5. Growth Revenue (Data Bln Sebelumnya, Data Bulan Sekarang -> Auto Calculate MoM %)
     */
    public function storeManual(Request $request)
    {
        try {
            $request->validate([
                'cluster_name' => 'nullable|string|max:255',
                'kabupaten' => 'required|string|max:255',
                'period_month' => 'required|string|max:50',
                'period_year' => 'required|numeric|min:2000|max:2100',
                'target_revenue_all' => 'nullable|numeric|min:0',
                'mtd_revenue_all' => 'nullable|numeric|min:0',
                'target_broadband' => 'nullable|numeric|min:0',
                'mtd_broadband' => 'nullable|numeric|min:0',
                'target_redeem' => 'nullable|numeric|min:0',
                'mtd_redeem' => 'nullable|numeric|min:0',
                'target_rgb' => 'nullable|numeric|min:0',
                'mtd_rgb' => 'nullable|numeric|min:0',
                'revenue_last_month' => 'nullable|numeric|min:0',
                'revenue_current_month' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:500',
            ]);

            $kabupaten = \App\Services\RevenueImportService::normalizeKabupatenName(trim($request->input('kabupaten')));
            $clusterName = trim($request->input('cluster_name', ''));

            // Auto-resolve cluster if not provided or vice versa
            if (empty($clusterName)) {
                $clusterName = self::resolveClusterFromKabupaten($kabupaten);
            }
            if (empty($clusterName)) {
                $clusterName = 'BALI BARAT'; // Default fallback
            }

            $targetAll = floatval($request->input('target_revenue_all', 0));
            $mtdAll = floatval($request->input('mtd_revenue_all', 0));

            $targetBb = floatval($request->input('target_broadband', 0));
            $mtdBb = floatval($request->input('mtd_broadband', 0));

            $targetRedeem = floatval($request->input('target_redeem', 0));
            $mtdRedeem = floatval($request->input('mtd_redeem', 0));

            $targetRgb = floatval($request->input('target_rgb', 0));
            $mtdRgb = floatval($request->input('mtd_rgb', 0));

            // Auto sum if Revenue All is not typed but Broadband & Redeem are entered
            if ($targetAll <= 0 && ($targetBb > 0 || $targetRedeem > 0)) {
                $targetAll = $targetBb + $targetRedeem;
            }
            if ($mtdAll <= 0 && ($mtdBb > 0 || $mtdRedeem > 0)) {
                $mtdAll = $mtdBb + $mtdRedeem;
            }

            $lastMonth = floatval($request->input('revenue_last_month', 0));
            $currentMonth = floatval($request->input('revenue_current_month', $mtdAll));

            $payload = [
                'target_revenue_all' => $targetAll,
                'mtd_revenue_all' => $mtdAll,
                'target_broadband' => $targetBb,
                'mtd_broadband' => $mtdBb,
                'target_redeem' => $targetRedeem,
                'mtd_redeem' => $mtdRedeem,
                'target_rgb' => $targetRgb,
                'mtd_rgb' => $mtdRgb,
                'revenue_last_month' => $lastMonth,
                'revenue_current_month' => $currentMonth,
                'notes' => $request->input('notes'),
            ];

            $match = [
                'cluster_name' => $clusterName,
                'kabupaten' => $kabupaten,
                'period_month' => $request->input('period_month'),
                'period_year' => intval($request->input('period_year')),
            ];

            $cluster = \App\Models\RevenueData::updateOrCreate($match, $payload);

            $msg = "Data revenue Kabupaten '{$cluster->kabupaten}' (Cluster {$cluster->cluster_name}) berhasil disimpan ke database!";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'redirect' => $request->input('redirect_to', route('dashboard'))
                ]);
            }

            return redirect()->back()->with('success', $msg);
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal menyimpan data: " . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->with('error', "Gagal menyimpan data: " . $e->getMessage());
        }
    }

    /**
     * Update existing cluster revenue record
     */
    public function update(Request $request, $id)
    {
        try {
            $cluster = ClusterRevenue::findOrFail($id);

            $request->validate([
                'cluster_name' => 'nullable|string|max:255',
                'kabupaten' => 'required|string|max:255',
                'period_month' => 'required|string|max:50',
                'period_year' => 'required|numeric|min:2000|max:2100',
                'target_revenue_all' => 'nullable|numeric|min:0',
                'mtd_revenue_all' => 'nullable|numeric|min:0',
                'target_broadband' => 'nullable|numeric|min:0',
                'mtd_broadband' => 'nullable|numeric|min:0',
                'target_redeem' => 'nullable|numeric|min:0',
                'mtd_redeem' => 'nullable|numeric|min:0',
                'target_rgb' => 'nullable|numeric|min:0',
                'mtd_rgb' => 'nullable|numeric|min:0',
                'revenue_last_month' => 'nullable|numeric|min:0',
                'revenue_current_month' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:500',
            ]);

            $kabupaten = \App\Services\RevenueImportService::normalizeKabupatenName(trim($request->input('kabupaten')));
            $clusterName = trim($request->input('cluster_name', ''));

            if (empty($clusterName)) {
                $clusterName = self::resolveClusterFromKabupaten($kabupaten);
            }
            if (empty($clusterName)) {
                $clusterName = $cluster->cluster_name ?: 'BALI BARAT';
            }

            $targetAll = floatval($request->input('target_revenue_all', 0));
            $mtdAll = floatval($request->input('mtd_revenue_all', 0));
            $targetBb = floatval($request->input('target_broadband', 0));
            $mtdBb = floatval($request->input('mtd_broadband', 0));
            $targetRedeem = floatval($request->input('target_redeem', 0));
            $mtdRedeem = floatval($request->input('mtd_redeem', 0));
            $targetRgb = floatval($request->input('target_rgb', 0));
            $mtdRgb = floatval($request->input('mtd_rgb', 0));

            if ($targetAll <= 0 && ($targetBb > 0 || $targetRedeem > 0)) {
                $targetAll = $targetBb + $targetRedeem;
            }
            if ($mtdAll <= 0 && ($mtdBb > 0 || $mtdRedeem > 0)) {
                $mtdAll = $mtdBb + $mtdRedeem;
            }

            $lastMonth = floatval($request->input('revenue_last_month', 0));
            $currentMonth = floatval($request->input('revenue_current_month', $mtdAll));

            $cluster->update([
                'cluster_name' => $clusterName,
                'kabupaten' => $kabupaten,
                'period_month' => $request->input('period_month'),
                'period_year' => intval($request->input('period_year')),
                'target_revenue_all' => $targetAll,
                'mtd_revenue_all' => $mtdAll,
                'target_broadband' => $targetBb,
                'mtd_broadband' => $mtdBb,
                'target_redeem' => $targetRedeem,
                'mtd_redeem' => $mtdRedeem,
                'target_rgb' => $targetRgb,
                'mtd_rgb' => $mtdRgb,
                'revenue_last_month' => $lastMonth,
                'revenue_current_month' => $currentMonth,
                'notes' => $request->input('notes'),
            ]);

            $msg = "Data revenue Kabupaten '{$cluster->kabupaten}' ({$cluster->cluster_name}) berhasil diperbarui!";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                ]);
            }

            return redirect()->back()->with('success', $msg);
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal memperbarui data: " . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->with('error', "Gagal memperbarui data: " . $e->getMessage());
        }
    }

    /**
     * Delete revenue record
     */
    public function destroy($id, Request $request)
    {
        try {
            $cluster = ClusterRevenue::findOrFail($id);
            $clusterName = $cluster->cluster_name;
            $cluster->delete();

            $msg = "Data cluster '{$clusterName}' berhasil dihapus.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                ]);
            }

            return redirect()->back()->with('success', $msg);
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal menghapus data: " . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->with('error', "Gagal menghapus data: " . $e->getMessage());
        }
    }

    /**
     * Import CSV / Excel File
     */
    public function import(Request $request, RevenueImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'import_mode' => 'nullable|in:replace,append,update',
            'mode' => 'nullable|in:replace,append,update',
        ]);

        try {
            $file = $request->file('file');
            $mode = $request->input('import_mode', $request->input('mode', 'replace'));
            if ($mode === 'update') {
                $mode = 'append';
            }
            $result = $importService->importFile($file, $mode);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                ]);
            }

            return redirect()->back()->with('success', $result['message']);
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal mengimpor file: " . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->with('error', "Gagal mengimpor file: " . $e->getMessage());
        }
    }

    /**
     * Download CSV Template
     */
    public function downloadTemplate(RevenueImportService $importService)
    {
        $csvContent = $importService->generateTemplateCsv();
        $filename = "template_input_revenue_regional_" . date('Ymd_His') . ".csv";

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export current filtered data to CSV
     */
    public function exportData(Request $request)
    {
        $search = $request->query('search');
        $selectedCluster = $request->query('cluster', 'all');
        $selectedKabupaten = $request->query('kabupaten', 'all');
        $selectedPeriod = $request->query('period', 'all');
        $selectedStatus = $request->query('status', 'all');

        $query = ClusterRevenue::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('cluster_name', 'LIKE', "%{$search}%")
                  ->orWhere('kabupaten', 'LIKE', "%{$search}%")
                  ->orWhere('period_month', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }
        if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
            $query->where('cluster_name', $selectedCluster);
        }
        if ($selectedKabupaten !== 'all' && !empty($selectedKabupaten)) {
            $query->where('kabupaten', $selectedKabupaten);
        }
        if ($selectedPeriod !== 'all' && !empty($selectedPeriod)) {
            $query->where('period_month', $selectedPeriod);
        }
        if ($selectedStatus !== 'all' && !empty($selectedStatus)) {
            if ($selectedStatus === 'melampaui_target' || $selectedStatus === 'Melampaui Target' || $selectedStatus === 'above_100') {
                $query->where(function ($q) {
                    $q->where('growth_mom', '>=', 1.0)
                      ->orWhere('notes', 'LIKE', '%Melampaui%')
                      ->orWhere('status', 'LIKE', '%Melampaui%');
                });
            } elseif ($selectedStatus === 'mencapai_target' || $selectedStatus === 'Mencapai Target' || $selectedStatus === 'between_98_100') {
                $query->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('growth_mom', '>=', 0)
                            ->where('growth_mom', '<', 1.0);
                    })->orWhere(function ($sub) {
                        $sub->where('notes', 'LIKE', '%Mencapai Target%')
                            ->where('notes', 'NOT LIKE', '%Tidak%')
                            ->where('notes', 'NOT LIKE', '%Melampaui%');
                    });
                });
            } elseif ($selectedStatus === 'tidak_mencapai_target' || $selectedStatus === 'Tidak Mencapai Target' || $selectedStatus === 'below_98') {
                $query->where(function ($q) {
                    $q->where('growth_mom', '<', 0)
                      ->orWhere('notes', 'LIKE', '%Tidak Mencapai%')
                      ->orWhere('status', 'LIKE', '%Tidak Mencapai%');
                });
            }
        }

        $records = $query->orderBy('cluster_name')->orderBy('kabupaten')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="data_cluster_revenue_' . date('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            // Header columns
            fputcsv($handle, [
                'Cluster',
                'Kabupaten / Kota',
                'Periode Bulan',
                'Tahun',
                'Target Revenue All',
                'MTD Revenue All',
                'Ach Revenue All (%)',
                'Target Broadband',
                'MTD Broadband',
                'Ach Broadband (%)',
                'Target Redeem PV',
                'MTD Redeem PV',
                'Ach Redeem PV (%)',
                'Data Bln Sebelumnya',
                'Data Bulan Sekarang',
                'Growth MoM (%)',
                'Catatan'
            ]);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->cluster_name,
                    $r->kabupaten,
                    $r->period_month,
                    $r->period_year,
                    $r->target_revenue_all ?: $r->target_revenue,
                    $r->mtd_revenue_all ?: $r->revenue_all,
                    $r->ach_revenue_all ?: $r->achievement_rate,
                    $r->target_broadband,
                    $r->mtd_broadband ?: $r->revenue_broadband,
                    $r->ach_broadband,
                    $r->target_redeem,
                    $r->mtd_redeem ?: $r->revenue_redeem_pv,
                    $r->ach_redeem,
                    $r->revenue_last_month,
                    $r->revenue_current_month ?: ($r->mtd_revenue_all ?: $r->revenue_all),
                    $r->growth_mom,
                    $r->notes ?: $r->status
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Resolve Cluster Name from single Kabupaten / Kota name across 12 clusters
     */
    public static function resolveClusterFromKabupaten(string $kabupaten): string
    {
        $normalized = \App\Services\RevenueImportService::normalizeKabupatenName($kabupaten);
        $k = strtoupper(trim(preg_replace('/^(KAB\.|KOTA|KABUPATEN)\s+/i', '', $normalized)));

        // 1. BALI BARAT
        if (in_array($k, ['BULELENG', 'JEMBRANA', 'TABANAN', 'SINGARAJA', 'NEGARA'])) {
            return 'BALI BARAT';
        }
        // 2. BALI TENGAH
        if (in_array($k, ['BADUNG', 'DENPASAR', 'KOTA DENPASAR'])) {
            return 'BALI TENGAH';
        }
        // 3. BALI TIMUR
        if (in_array($k, ['BANGLI', 'GIANYAR', 'KARANG ASEM', 'KARANGASEM', 'KLUNGKUNG', 'NUSA PENIDA', 'UBUD'])) {
            return 'BALI TIMUR';
        }
        // 4. ENDE SIKKA
        if (in_array($k, ['ENDE', 'SIKKA', 'MAUMERE'])) {
            return 'ENDE SIKKA';
        }
        // 5. FLORES TIMUR
        if (in_array($k, ['ALOR', 'FLORES TIMUR', 'LEMBATA', 'LARANTUKA', 'KALABAHI', 'LEWOLEBA'])) {
            return 'FLORES TIMUR';
        }
        // 6. MANGGARAI
        if (in_array($k, ['MANGGARAI', 'MANGGARAI BARAT', 'MANGGARAI TIMUR', 'NAGEKEO', 'NGADA', 'LABUAN BAJO', 'RUTENG', 'BORONG', 'MBAY', 'BAJAWA'])) {
            return 'MANGGARAI';
        }
        // 7. KUPANG ROTE
        if (in_array($k, ['KUPANG', 'KOTA KUPANG', 'ROTE NDAO', 'ROTE', 'BAA', 'OELAMASI'])) {
            return 'KUPANG ROTE';
        }
        // 8. MALAKA TIMTIM BELU
        if (in_array($k, ['BELU', 'MALAKA', 'TIMOR TENGAH SELATAN', 'TTS', 'TIMOR TENGAH UTARA', 'TTU', 'ATAMBUA', 'BETUN', 'SOE', 'KEFAMENANU'])) {
            return 'MALAKA TIMTIM BELU';
        }
        // 9. SUMBA
        if (in_array($k, ['SABU RAIJUA', 'SUMBA BARAT', 'SUMBA BARAT DAYA', 'SUMBA TENGAH', 'SUMBA TIMUR', 'WAIKABUBAK', 'TAMBOLAKA', 'WAIBAKUL', 'WAINGAPU', 'MENIA'])) {
            return 'SUMBA';
        }
        // 10. LOMBOK
        if (in_array($k, ['MATARAM', 'KOTA MATARAM', 'LOMBOK BARAT', 'LOMBOK TENGAH', 'LOMBOK TIMUR', 'LOMBOK UTARA', 'GERUNG', 'PRAYA', 'SELONG', 'TANJUNG'])) {
            return 'LOMBOK';
        }
        // 11. SUMBAWA
        if (in_array($k, ['SUMBAWA', 'SUMBAWA BARAT', 'SUMBAWA BESAR', 'TALIWANG'])) {
            return 'SUMBAWA';
        }
        // 12. SUMBAWA TIMUR
        if (in_array($k, ['BIMA', 'KOTA BIMA', 'DOMPU', 'WOHA', 'RABA'])) {
            return 'SUMBAWA TIMUR';
        }

        // Fuzzy search fallback
        if (str_contains($k, 'BULELENG') || str_contains($k, 'JEMBRANA') || str_contains($k, 'TABANAN')) return 'BALI BARAT';
        if (str_contains($k, 'DENPASAR') || str_contains($k, 'BADUNG')) return 'BALI TENGAH';
        if (str_contains($k, 'GIANYAR') || str_contains($k, 'KLUNGKUNG') || str_contains($k, 'KARANG') || str_contains($k, 'BANGLI')) return 'BALI TIMUR';
        if (str_contains($k, 'ENDE') || str_contains($k, 'SIKKA')) return 'ENDE SIKKA';
        if (str_contains($k, 'ALOR') || str_contains($k, 'LEMBATA') || str_contains($k, 'FLORES')) return 'FLORES TIMUR';
        if (str_contains($k, 'MANGGARAI') || str_contains($k, 'NGADA') || str_contains($k, 'NAGEKEO')) return 'MANGGARAI';
        if (str_contains($k, 'KUPANG') || str_contains($k, 'ROTE')) return 'KUPANG ROTE';
        if (str_contains($k, 'BELU') || str_contains($k, 'MALAKA') || str_contains($k, 'TIMOR') || str_contains($k, 'TTU') || str_contains($k, 'TTS')) return 'MALAKA TIMTIM BELU';
        if (str_contains($k, 'SUMBA') || str_contains($k, 'SABU')) return 'SUMBA';
        if (str_contains($k, 'LOMBOK') || str_contains($k, 'MATARAM')) return 'LOMBOK';
        if (str_contains($k, 'BIMA') || str_contains($k, 'DOMPU')) return 'SUMBAWA TIMUR';
        if (str_contains($k, 'SUMBAWA')) return 'SUMBAWA';

        return 'BALI BARAT';
    }

    /**
     * Resolve default Kabupaten list or primary kabupaten for a cluster
     */
    public static function resolveDefaultKabupaten(string $clusterName): string
    {
        $c = strtoupper(trim($clusterName));

        if (str_contains($c, 'BALI BARAT')) return 'BULELENG';
        if (str_contains($c, 'BALI TENGAH')) return 'BADUNG';
        if (str_contains($c, 'BALI TIMUR')) return 'GIANYAR';
        if (str_contains($c, 'ENDE SIKKA')) return 'ENDE';
        if (str_contains($c, 'FLORES TIMUR')) return 'FLORES TIMUR';
        if (str_contains($c, 'MANGGARAI')) return 'MANGGARAI';
        if (str_contains($c, 'KUPANG ROTE')) return 'KOTA KUPANG';
        if (str_contains($c, 'MALAKA TIMTIM BELU')) return 'BELU';
        if (str_contains($c, 'SUMBA')) return 'SUMBA TIMUR';
        if (str_contains($c, 'LOMBOK')) return 'KOTA MATARAM';
        if (str_contains($c, 'SUMBAWA TIMUR')) return 'BIMA';
        if (str_contains($c, 'SUMBAWA')) return 'SUMBAWA';

        return 'BULELENG';
    }
}
