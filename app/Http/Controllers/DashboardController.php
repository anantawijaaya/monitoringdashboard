<?php

namespace App\Http\Controllers;

use App\Models\ClusterRevenue;
use App\Models\GrowthRevenue;
use App\Services\GrowthRevenueImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Exception;

class DashboardController extends Controller
{
    /**
     * Display dynamic dashboard with real-time metrics,
     * Growth Revenue Line Chart powered directly from growth_revenues or cluster_revenues table,
     * TOP 3 Ranking Atas, and TOP 3 Ranking Bawah.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Active Filters
        $selectedCluster = $request->query('cluster', 'all');
        $selectedKabupaten = $request->query('kabupaten', 'all');
        $selectedPeriod = $request->query('period', 'all');
        $selectedYear = intval($request->query('year', date('Y')));

        // Check if dedicated growth revenue data exists
        $hasDedicatedGrowthData = \Illuminate\Support\Facades\Schema::hasTable('growth_revenues') && GrowthRevenue::exists();

        // Fetch distinct filter options from cluster_revenues table (or combined with growth_revenues)
        if ($hasDedicatedGrowthData) {
            $availableClusters = GrowthRevenue::distinct()->pluck('cluster_name')->filter()->values();
            $availableKabupatens = GrowthRevenue::query()
                ->when($selectedCluster !== 'all' && !empty($selectedCluster), function ($q) use ($selectedCluster) {
                    return $q->where('cluster_name', $selectedCluster);
                })
                ->distinct()
                ->pluck('kabupaten')
                ->filter()
                ->values();
        } else {
            $availableClusters = ClusterRevenue::distinct()->pluck('cluster_name')->filter()->values();
            $availableKabupatens = ClusterRevenue::query()
                ->when($selectedCluster !== 'all' && !empty($selectedCluster), function ($q) use ($selectedCluster) {
                    return $q->where('cluster_name', $selectedCluster);
                })
                ->distinct()
                ->pluck('kabupaten')
                ->filter()
                ->values();
        }
        $availablePeriods = ClusterRevenue::distinct()->pluck('period_month')->filter()->values();

        // Base Query for Cluster Revenues (Main KPI Summary & Leaderboard)
        $clusterQuery = ClusterRevenue::query();

        if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
            $clusterQuery->where('cluster_name', $selectedCluster);
        }

        if ($selectedKabupaten !== 'all' && !empty($selectedKabupaten)) {
            $clusterQuery->where('kabupaten', $selectedKabupaten);
        }

        if ($selectedPeriod !== 'all' && !empty($selectedPeriod)) {
            $clusterQuery->where('period_month', $selectedPeriod);
        }

        $clusters = $clusterQuery->get();
        $totalRecords = $clusters->count();
        $hasData = $totalRecords > 0;

        // Total MTD & Target Aggregations from cluster_revenues
        $totalMtdAll = $clusters->sum('mtd_revenue_all') ?: $clusters->sum('revenue_all');
        $totalTargetAll = $clusters->sum('target_revenue_all') ?: $clusters->sum('target_revenue');
        $achAll = ($totalTargetAll > 0) ? round(($totalMtdAll / $totalTargetAll) * 100, 2) : ($hasData ? 100 : 0);

        $totalMtdBroadband = $clusters->sum('mtd_broadband') ?: $clusters->sum('revenue_broadband');
        $totalTargetBroadband = $clusters->sum('target_broadband');
        $achBroadband = ($totalTargetBroadband > 0) ? round(($totalMtdBroadband / $totalTargetBroadband) * 100, 2) : 0;
        $portionBroadband = ($totalMtdAll > 0) ? round(($totalMtdBroadband / $totalMtdAll) * 100, 1) : 0;

        $totalMtdRedeem = $clusters->sum('mtd_redeem') ?: $clusters->sum('revenue_redeem_pv');
        $totalTargetRedeem = $clusters->sum('target_redeem');
        $achRedeem = ($totalTargetRedeem > 0) ? round(($totalMtdRedeem / $totalTargetRedeem) * 100, 2) : 0;
        $portionRedeem = ($totalMtdAll > 0) ? round(($totalMtdRedeem / $totalMtdAll) * 100, 1) : 0;

        $totalMtdRgb = $clusters->sum('mtd_rgb') ?: $clusters->sum('revenue_rgb');
        $totalTargetRgb = $clusters->sum('target_rgb');
        $achRgb = ($totalTargetRgb > 0) ? round(($totalMtdRgb / $totalTargetRgb) * 100, 2) : 0;
        $portionRgb = ($totalMtdAll > 0) ? round(($totalMtdRgb / $totalMtdAll) * 100, 1) : 0;

        // Growth MoM Aggregation from cluster_revenues
        $totalLastMonth = $clusters->sum('revenue_last_month');
        $totalCurrentMonth = $clusters->sum('revenue_current_month') ?: $totalMtdAll;
        if ($totalLastMonth > 0) {
            $overallGrowth = round((($totalCurrentMonth - $totalLastMonth) / $totalLastMonth) * 100, 2);
        } else {
            $overallGrowth = $hasData ? round($clusters->avg('growth_mom'), 2) : 0;
        }

        // Structured Revenue Data for KPI Boxes
        $revenueData = [
            'has_data' => $hasData,
            'total_records' => $totalRecords,
            'all' => [
                'value' => self::formatToMiliarDisplay($totalMtdAll),
                'raw_value' => $totalMtdAll,
                'target' => self::formatToMiliarDisplay($totalTargetAll),
                'raw_target' => $totalTargetAll,
                'achievement' => $achAll,
                'growth' => ($overallGrowth >= 0 ? '+' : '') . number_format($overallGrowth, 1, ',', '.') . '%',
                'raw_growth' => $overallGrowth,
            ],
            'broadband' => [
                'value' => self::formatToMiliarDisplay($totalMtdBroadband),
                'raw_value' => $totalMtdBroadband,
                'target' => self::formatToMiliarDisplay($totalTargetBroadband),
                'raw_target' => $totalTargetBroadband,
                'achievement' => $achBroadband,
                'portion' => $portionBroadband,
            ],
            'redeem_pv' => [
                'value' => self::formatToMiliarDisplay($totalMtdRedeem),
                'raw_value' => $totalMtdRedeem,
                'target' => self::formatToMiliarDisplay($totalTargetRedeem),
                'raw_target' => $totalTargetRedeem,
                'achievement' => $achRedeem,
                'portion' => $portionRedeem,
            ],
            'rgb' => [
                'value' => self::formatToMiliarDisplay($totalMtdRgb),
                'raw_value' => $totalMtdRgb,
                'target' => self::formatToMiliarDisplay($totalTargetRgb),
                'raw_target' => $totalTargetRgb,
                'achievement' => $achRgb,
                'portion' => $portionRgb,
            ],
            'growth' => [
                'rate' => ($overallGrowth >= 0 ? '+' : '') . number_format($overallGrowth, 1, ',', '.') . '%',
                'status' => $overallGrowth >= 2.0 ? 'Melampaui KPI (+2.0%)' : ($overallGrowth >= 0 ? 'Pertumbuhan Positif' : 'Perlu Evaluasi'),
                'is_positive' => $overallGrowth >= 0,
                'last_month' => self::formatToMiliarDisplay($totalLastMonth),
                'current_month' => self::formatToMiliarDisplay($totalCurrentMonth),
            ]
        ];

        // TOP 3 Ranking Atas (Highest Achievement / Revenue)
        $topRankings = ClusterRevenue::query()
            ->when($selectedCluster !== 'all' && !empty($selectedCluster), function ($q) use ($selectedCluster) {
                return $q->where('cluster_name', $selectedCluster);
            })
            ->when($selectedKabupaten !== 'all' && !empty($selectedKabupaten), function ($q) use ($selectedKabupaten) {
                return $q->where('kabupaten', $selectedKabupaten);
            })
            ->when($selectedPeriod !== 'all' && !empty($selectedPeriod), function ($q) use ($selectedPeriod) {
                return $q->where('period_month', $selectedPeriod);
            })
            ->orderByDesc('ach_revenue_all')
            ->orderByDesc('mtd_revenue_all')
            ->take(3)
            ->get();

        // TOP 3 Ranking Bawah (Lowest Achievement / Growth)
        // Ambil 3 performa terbawah, lalu diurutkan sehingga peringkat 1 adalah nilai teratas di antara 3 terbawah (misal DOMPU 90.45%), peringkat 2 (BIMA 90.34%), dan peringkat 3 adalah yang paling rendah (KOTA MATARAM 87.64%)
        $bottomRankings = ClusterRevenue::query()
            ->when($selectedCluster !== 'all' && !empty($selectedCluster), function ($q) use ($selectedCluster) {
                return $q->where('cluster_name', $selectedCluster);
            })
            ->when($selectedKabupaten !== 'all' && !empty($selectedKabupaten), function ($q) use ($selectedKabupaten) {
                return $q->where('kabupaten', $selectedKabupaten);
            })
            ->when($selectedPeriod !== 'all' && !empty($selectedPeriod), function ($q) use ($selectedPeriod) {
                return $q->where('period_month', $selectedPeriod);
            })
            ->orderBy('ach_revenue_all', 'asc')
            ->orderBy('mtd_revenue_all', 'asc')
            ->take(3)
            ->get()
            ->reverse()
            ->values();

        // ==========================================
        // Prepare Growth Revenue Line & Bar Chart Data
        // Comparing Bulan Sebelumnya vs Bulan Sekarang
        // ==========================================
        $chartLabels = [];
        $chartKabupatens = [];
        $chartClusterNames = [];
        $growthMoM = [];
        $valLastMonthList = [];
        $valCurrentMonthList = [];
        $targetKpiGrowth = [];

        if ($hasDedicatedGrowthData) {
            // Read from dedicated growth_revenues table, grouped PER CLUSTER
            $growthQuery = GrowthRevenue::query();
            if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
                $growthQuery->where('cluster_name', $selectedCluster);
            }
            if ($selectedKabupaten !== 'all' && !empty($selectedKabupaten)) {
                $growthQuery->where('kabupaten', $selectedKabupaten);
            }
            $growthItems = $growthQuery->get();

            if ($growthItems->isEmpty()) {
                $growthItems = GrowthRevenue::all();
            }

            $groupedByCluster = $growthItems->groupBy('cluster_name');

            foreach ($groupedByCluster as $clusterName => $items) {
                $sumLastMonth = $items->sum('revenue_last_month');
                $sumCurrentMonth = $items->sum('revenue_current_month');

                $mom = ($sumLastMonth > 0)
                    ? round((($sumCurrentMonth - $sumLastMonth) / $sumLastMonth) * 100, 2)
                    : round($items->avg('growth_mom'), 2);

                $chartLabels[] = $clusterName;
                $chartKabupatens[] = $items->pluck('kabupaten')->filter()->unique()->implode(', ');
                $chartClusterNames[] = $clusterName;

                $growthMoM[] = $mom;
                $valLastMonthList[] = round(floatval($sumLastMonth) / 1000000000, 2);
                $valCurrentMonthList[] = round(floatval($sumCurrentMonth) / 1000000000, 2);
                $targetKpiGrowth[] = 2.0; // 2% MoM KPI Target line
            }
        } else {
            // Fallback from cluster_revenues table, grouped PER CLUSTER
            $chartClusters = $clusters;
            if ($chartClusters->isEmpty()) {
                $chartClusters = ClusterRevenue::all();
            }

            $groupedByCluster = $chartClusters->groupBy('cluster_name');

            foreach ($groupedByCluster as $clusterName => $items) {
                $sumLastMonth = $items->sum('revenue_last_month');
                $sumCurrentMonth = $items->sum(function ($c) {
                    return floatval($c->revenue_current_month ?: ($c->mtd_revenue_all ?: $c->revenue_all));
                });

                $mom = ($sumLastMonth > 0)
                    ? round((($sumCurrentMonth - $sumLastMonth) / $sumLastMonth) * 100, 2)
                    : round($items->avg('growth_mom'), 2);

                $chartLabels[] = $clusterName;
                $chartKabupatens[] = $items->pluck('kabupaten')->filter()->unique()->implode(', ');
                $chartClusterNames[] = $clusterName;

                $growthMoM[] = $mom;
                $valLastMonthList[] = round(floatval($sumLastMonth) / 1000000000, 2);
                $valCurrentMonthList[] = round(floatval($sumCurrentMonth) / 1000000000, 2);
                $targetKpiGrowth[] = 2.0;
            }
        }

        // Exact Average Growth Omzet from Chart Data
        $chartRecordCount = count($growthMoM);
        $avgGrowthFromChart = $chartRecordCount > 0 ? round(array_sum($growthMoM) / $chartRecordCount, 2) : 0;
        $totalChartLastMonth = round(array_sum($valLastMonthList), 2);
        $totalChartCurrentMonth = round(array_sum($valCurrentMonthList), 2);

        // Update revenueData['growth'] to reflect AVG Growth Omzet from the Chart
        $revenueData['growth'] = [
            'rate' => ($avgGrowthFromChart >= 0 ? '+' : '') . number_format($avgGrowthFromChart, 2, ',', '.') . '%',
            'raw_rate' => $avgGrowthFromChart,
            'status' => $avgGrowthFromChart >= 2.0 ? 'Melampaui KPI (+2.0%)' : ($avgGrowthFromChart >= 0 ? 'Pertumbuhan Positif' : 'Perlu Evaluasi'),
            'is_positive' => $avgGrowthFromChart >= 0,
            'cluster_count' => $chartRecordCount,
            'last_month' => number_format($totalChartLastMonth, 2, ',', '.'),
            'current_month' => number_format($totalChartCurrentMonth, 2, ',', '.'),
            'raw_last_month' => $totalChartLastMonth,
            'raw_current_month' => $totalChartCurrentMonth,
        ];

        $chartData = [
            'labels' => $chartLabels,
            'kabupatens' => $chartKabupatens,
            'clusters' => $chartClusterNames,
            'growth_mom' => $growthMoM,
            'val_last_month' => $valLastMonthList,
            'val_current_month' => $valCurrentMonthList,
            'target_kpi_growth' => $targetKpiGrowth,
            'has_dedicated_data' => $hasDedicatedGrowthData,
            'total_growth_records' => count($chartLabels),
            'avg_growth' => $avgGrowthFromChart,
            'avg_growth_formatted' => ($avgGrowthFromChart >= 0 ? '+' : '') . number_format($avgGrowthFromChart, 2, ',', '.') . '%',
            'total_last_month' => $totalChartLastMonth,
            'total_current_month' => $totalChartCurrentMonth,
        ];

        // Complete list of cluster data for management table
        $allClusterData = ClusterRevenue::orderByDesc('period_year')
            ->orderBy('period_month')
            ->orderByDesc('ach_revenue_all')
            ->paginate(15);

        return view('dashboard', compact(
            'user',
            'revenueData',
            'chartData',
            'topRankings',
            'bottomRankings',
            'allClusterData',
            'availableClusters',
            'availableKabupatens',
            'availablePeriods',
            'selectedCluster',
            'selectedKabupaten',
            'selectedPeriod',
            'selectedYear',
            'hasData',
            'hasDedicatedGrowthData'
        ));
    }

    /**
     * Import Excel / CSV specifically for Growth Revenue Chart
     */
    public function importGrowthData(Request $request, GrowthRevenueImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $result = $importService->importFile($file);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'count' => $result['count'] ?? 0,
                ]);
            }

            return redirect()->route('dashboard')->with('success', $result['message']);
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal mengimpor data grafik: " . $e->getMessage()
                ], 422);
            }

            return redirect()->route('dashboard')->with('error', "Gagal mengimpor data grafik: " . $e->getMessage());
        }
    }

    /**
     * Download Template CSV specifically for Growth Revenue Chart (4 columns)
     */
    public function downloadGrowthTemplate(GrowthRevenueImportService $importService)
    {
        $csvContent = $importService->generateTemplateCsv();
        $filename = "template_data_grafik_growth_" . date('Ymd_His') . ".csv";

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Reset dedicated Growth Revenue data to sync back with main revenue table
     */
    public function resetGrowthData(Request $request)
    {
        try {
            GrowthRevenue::truncate();
            $msg = "Data khusus grafik berhasil direset. Grafik sekarang kembali sinkron dengan database revenue utama.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                ]);
            }

            return redirect()->route('dashboard')->with('success', $msg);
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal mereset data: " . $e->getMessage()
                ], 422);
            }

            return redirect()->route('dashboard')->with('error', "Gagal mereset data: " . $e->getMessage());
        }
    }

    private static function formatToMiliarDisplay($amount): string
    {
        $val = floatval($amount);
        if ($val <= 0) return '0';
        if ($val >= 1000000000) {
            return number_format($val / 1000000000, 2, ',', '.');
        } elseif ($val >= 1000000) {
            return number_format($val / 1000000, 0, ',', '.') . ' Jt';
        }
        return number_format($val, 0, ',', '.');
    }
}
