<?php

namespace App\Http\Controllers;

use App\Models\KelasKpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class KelasKpiController extends Controller
{
    /**
     * Display the Kelas KPI dashboard page matching user design screenshot.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Filters
        $search = $request->query('search');
        $selectedCluster = $request->query('cluster', 'all');
        $selectedPeriode = $request->query('periode', 'all');
        $viewMode = $request->query('view_mode', 'ringkasan');

        // Query Builder
        $query = KelasKpi::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('cluster', 'LIKE', "%{$search}%")
                  ->orWhere('new_cluster', 'LIKE', "%{$search}%")
                  ->orWhere('region', 'LIKE', "%{$search}%")
                  ->orWhere('type', 'LIKE', "%{$search}%")
                  ->orWhere('periode', 'LIKE', "%{$search}%")
                  ->orWhere('class', 'LIKE', "%{$search}%");
            });
        }

        if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
            $query->where(function ($q) use ($selectedCluster) {
                $q->where('cluster', $selectedCluster)
                  ->orWhere('new_cluster', $selectedCluster);
            });
        }

        if ($selectedPeriode !== 'all' && !empty($selectedPeriode)) {
            $query->where('periode', $selectedPeriode);
        }

        // Fetch all matching records
        $allRecords = (clone $query)->orderBy('id', 'asc')->get();

        // 4 Top Cards Metrics Calculations
        $totalRevAllSum = $allRecords->sum(fn($r) => $r->target_rev_all > 0 ? $r->target_rev_all : $r->revenue_all);
        $totalAchievedSum = $allRecords->sum(fn($r) => $r->actual_rev_all > 0 ? $r->actual_rev_all : $r->achieved);

        $avgAchPercent = $allRecords->count() > 0 ? round($allRecords->avg('rev_growth_ach_percent') ?: $allRecords->avg('ach_percent'), 1) : 0;
        
        $highestClusterObj = $allRecords->sortByDesc(fn($r) => $r->rev_growth_ach_percent ?: $r->ach_percent)->first();
        $highestClusterName = $highestClusterObj ? ($highestClusterObj->cluster ?: $highestClusterObj->new_cluster) : '-';
        $highestClusterVal = $highestClusterObj ? number_format($highestClusterObj->rev_growth_ach_percent ?: $highestClusterObj->ach_percent, 1, ',', '.') : '0';

        $avgScore = $allRecords->count() > 0 ? round($allRecords->avg('final_score') ?: $allRecords->avg('score'), 2) : 0;
        $avgFinalScoreRevAll = $allRecords->count() > 0 ? round($allRecords->avg('omzet_outlet_ach_score') ?: $allRecords->avg('final_score_rev_all'), 2) : 0;

        $totalClustersCount = $allRecords->count();
        $topScoreCategory = 'GOLD';

        // Filter dropdown list
        $availableClusters = KelasKpi::select('cluster', 'new_cluster')->get()
            ->map(fn($r) => $r->cluster ?: $r->new_cluster)
            ->filter()->unique()->sort()->values();

        $availablePeriodes = self::sortPeriodesChronologically(
            KelasKpi::select('periode')
                ->whereNotNull('periode')
                ->where('periode', '!=', '')
                ->distinct()
                ->pluck('periode')
        );

        // Fetch all records for Analisis Data view (multi-line chart across available Periodes)
        $analisisRawData = KelasKpi::select('cluster', 'new_cluster', 'periode', 'final_score', 'total_score', 'class')
            ->whereNotNull('periode')
            ->where('periode', '!=', '')
            ->get();

        // 1. Available Periodes (sorted chronologically from January to December)
        $analisisPeriodes = self::sortPeriodesChronologically(
            $analisisRawData->pluck('periode')->filter()->unique()
        )->toArray();

        // 2. Month Labels for X-Axis (Indikator Bulan di sumbu X mengikuti periode data yang tersedia)
        $analisisMonths = array_map(function($p) {
            if (preg_match('/^(\d{4})[-_]?(\d{1,2})$/', trim($p), $m)) {
                $year = $m[1];
                $monthNum = (int)$m[2];
                $months = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                if (isset($months[$monthNum])) {
                    return $months[$monthNum] . ' ' . $year;
                }
            }
            return $p;
        }, $analisisPeriodes);

        if (empty($analisisMonths)) {
            $analisisMonths = ['Tidak Ada Data'];
        }

        // 3. Available Clusters
        $analisisClusters = $analisisRawData->map(fn($r) => $r->cluster ?: $r->new_cluster)
            ->filter()->unique()->sort()->values()->toArray();

        // Palette of vibrant, distinct colors for line charts
        $colorPalette = [
            '#ED1C24', // Telkomsel Red (Rata-rata Regional)
            '#2563EB', // Royal Blue
            '#10B981', // Emerald Green
            '#F59E0B', // Amber / Gold
            '#8B5CF6', // Purple
            '#06B6D4', // Cyan
            '#EC4899', // Pink
            '#F97316', // Orange
            '#64748B', // Slate
            '#84CC16', // Lime
        ];

        // 4. Compute Scores across Periodes for Regional Average & Per Cluster
        $avgScorePerPeriode = [];
        foreach ($analisisPeriodes as $periodeVal) {
            $scoresInPeriode = [];
            foreach ($analisisClusters as $clusterName) {
                $rec = $analisisRawData->first(function ($r) use ($clusterName, $periodeVal) {
                    $c = $r->cluster ?: $r->new_cluster;
                    return $c === $clusterName && $r->periode === $periodeVal;
                });
                if ($rec) {
                    $scoreVal = (float) ($rec->final_score ?: $rec->total_score ?: 0);
                    $scoresInPeriode[] = $scoreVal;
                }
            }
            $avgVal = count($scoresInPeriode) > 0 ? round(array_sum($scoresInPeriode) / count($scoresInPeriode), 2) : 0;
            $avgScorePerPeriode[$periodeVal] = $avgVal;
        }

        $analisisDataMap = [];
        
        // Regional Average Line Data across periodes
        $analisisDataMap['all'] = [];
        foreach ($analisisPeriodes as $periodeVal) {
            $analisisDataMap['all'][] = $avgScorePerPeriode[$periodeVal] ?? 0;
        }

        // Individual Cluster Line Data across periodes
        foreach ($analisisClusters as $clusterName) {
            $analisisDataMap[$clusterName] = [];
            foreach ($analisisPeriodes as $periodeVal) {
                $rec = $analisisRawData->first(function ($r) use ($clusterName, $periodeVal) {
                    $c = $r->cluster ?: $r->new_cluster;
                    return $c === $clusterName && $r->periode === $periodeVal;
                });
                $score = $rec ? (float) ($rec->final_score ?: $rec->total_score ?: 0) : 0;
                $analisisDataMap[$clusterName][] = round($score, 2);
            }
        }

        // 5. Prepare Initial Chart Datasets
        $analisisChartDatasets = [];
        
        // Datasets per Cluster
        foreach ($analisisClusters as $cIdx => $clusterName) {
            $color = $colorPalette[$cIdx % count($colorPalette)];
            $analisisChartDatasets[] = [
                'label' => $clusterName,
                'cluster' => $clusterName,
                'data' => $analisisDataMap[$clusterName] ?? [],
                'borderColor' => $color,
                'backgroundColor' => 'transparent',
                'borderWidth' => 2.5,
                'tension' => 0.4,
                'pointRadius' => 4,
                'pointHoverRadius' => 7,
                'fill' => false,
            ];
        }

        $highestPeriode = !empty($avgScorePerPeriode) ? array_search(max($avgScorePerPeriode), $avgScorePerPeriode) : '-';
        $highestPeriodeAvg = !empty($avgScorePerPeriode) ? max($avgScorePerPeriode) : 0;

        // Paginated items for table
        $kelasKpis = $query->orderBy('id', 'asc')->paginate(12)->withQueryString();

        return view('monitoring-kpi.kelas-kpi.index', compact(
            'user',
            'kelasKpis',
            'totalRevAllSum',
            'totalAchievedSum',
            'avgAchPercent',
            'highestClusterName',
            'highestClusterVal',
            'avgScore',
            'avgFinalScoreRevAll',
            'totalClustersCount',
            'topScoreCategory',
            'availableClusters',
            'availablePeriodes',
            'search',
            'selectedCluster',
            'selectedPeriode',
            'viewMode',
            'analisisClusters',
            'analisisPeriodes',
            'analisisMonths',
            'analisisDataMap',
            'analisisChartDatasets',
            'highestPeriode',
            'highestPeriodeAvg'
        ));
    }

    /**
     * Export Kelas KPI data to CSV matching the exact 23 image columns.
     */
    public function export(Request $request)
    {
        $query = KelasKpi::query();

        if ($request->has('cluster') && $request->cluster !== 'all') {
            $query->where(function ($q) use ($request) {
                $q->where('cluster', $request->cluster)
                  ->orWhere('new_cluster', $request->cluster);
            });
        }

        if ($request->has('periode') && $request->periode !== 'all') {
            $query->where('periode', $request->periode);
        }

        $records = $query->orderBy('id', 'asc')->get();
        $filename = 'kelas_kpis_export_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fprintf($file, "\xEF\xBB\xBF");
            
            // Header with All 42 Calculation Matrix Breakdown Columns (Matching View Perhitungan Lengkap KPI)
            fputcsv($file, [
                'Cluster',
                'Periode',

                // Rev ALL (6)
                'Target Rev All',
                'Actual Rev All',
                'Ach Rev All %',
                'Score Rev All',
                'Weight Rev All %',
                'Final Score Rev All',

                // Rev Broadband (6)
                'Target Rev Broadband',
                'Actual Rev Broadband',
                'Ach Rev Broadband %',
                'Score Rev Broadband',
                'Weight Rev Broadband %',
                'Final Score Rev Broadband',

                // Rev Redeem PV (6)
                'Target Rev Redeem PV',
                'Actual Rev Redeem PV',
                'Ach Rev Redeem PV %',
                'Score Rev Redeem PV',
                'Weight Rev Redeem PV %',
                'Final Score Rev Redeem PV',

                // Rev RGB (6)
                'Target Rev RGB',
                'Actual Rev RGB',
                'Ach Rev RGB %',
                'Score Rev RGB',
                'Weight Rev RGB %',
                'Final Score Rev RGB',

                // Growth Revenue (7)
                'Omzet Rev M1',
                'MTD M1',
                'Tgt 3%',
                'MTD',
                'Growth %',
                'Growth Tgt %',
                'Rev Growth Ach %',

                // Outlet PJP & Omzet Outlet (7)
                'Outlet PJP',
                'Outlet PJP Growth',
                'Ratio Outlet PJP %',
                'Omzet Outlet Ach %',
                'Score Omzet',
                'Weight Omzet %',
                'Final Score Omzet',

                // Hasil Akhir KPI (2)
                'Total Final Score',
                'Kelas KPI'
            ]);

            foreach ($records as $row) {
                $typeUpper = strtoupper($row->type ?? '');
                $weightBb = ($typeUpper === 'LOW') ? '20%' : '15%';
                $weightPv = ($typeUpper === 'LOW') ? '20%' : '25%';

                fputcsv($file, [
                    $row->cluster ?: $row->new_cluster,
                    $row->periode ?: '2026-08',

                    // Rev ALL
                    round($row->target_rev_all),
                    round($row->actual_rev_all),
                    number_format($row->ach_rev_all ?: $row->ach_percent, 1, ',', '.') . '%',
                    number_format($row->score_rev_all ?: $row->score, 0),
                    '15%',
                    number_format($row->final_score_rev_all, 2, ',', '.'),

                    // Rev Broadband
                    round($row->target_rev_bb),
                    round($row->actual_rev_bb),
                    number_format($row->ach_rev_bb, 1, ',', '.') . '%',
                    number_format($row->score_rev_bb, 0),
                    $weightBb,
                    number_format($row->final_score_rev_bb, 2, ',', '.'),

                    // Rev Redeem PV
                    round($row->target_rev_pv),
                    round($row->actual_rev_pv),
                    number_format($row->ach_rev_pv, 1, ',', '.') . '%',
                    number_format($row->score_rev_pv, 0),
                    $weightPv,
                    number_format($row->final_score_rev_pv, 2, ',', '.'),

                    // Rev RGB
                    round($row->target_rgb),
                    round($row->actual_rgb),
                    number_format($row->ach_rgb, 1, ',', '.') . '%',
                    number_format($row->score_rgb, 0),
                    '20%',
                    number_format($row->final_score_rgb, 2, ',', '.'),

                    // Growth Revenue
                    round($row->omzet_rev_m1),
                    round($row->mtd_m1),
                    round($row->tgt_3_percent),
                    round($row->mtd),
                    number_format($row->growth, 1, ',', '.') . '%',
                    number_format($row->growth_tgt, 1, ',', '.') . '%',
                    number_format($row->rev_growth_ach_percent, 1, ',', '.') . '%',

                    // Outlet PJP & Omzet Outlet
                    $row->outlet_pjp,
                    $row->outlet_pjp_growth,
                    number_format($row->ratio_outlet_pjp, 1, ',', '.') . '%',
                    number_format($row->omzet_outlet_ach, 1, ',', '.') . '%',
                    number_format($row->score_omzet ?: $row->omzet_outlet_ach_score, 0),
                    '25%',
                    number_format($row->final_score_omzet, 2, ',', '.'),

                    // Hasil Akhir KPI
                    number_format($row->final_score ?: $row->total_score, 2, ',', '.'),
                    strtoupper($row->class ?: 'BRONZE')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Handle CSV/Excel file import for Kelas KPI data.
     */
    public function import(Request $request, \App\Services\KelasKpiImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xls,xlsx|max:10240',
            'mode' => 'nullable|string|in:append,replace',
        ], [
            'file.required' => 'Silakan pilih berkas data KPI (CSV/Excel) terlebih dahulu.',
            'file.mimes' => 'Format berkas harus berupa .csv, .xls, atau .xlsx',
            'file.max' => 'Ukuran berkas tidak boleh lebih dari 10 MB.'
        ]);

        try {
            $mode = $request->input('mode', 'append');
            $result = $importService->importFile($request->file('file'), $mode);

            $msg = "Impor data KPI berhasil! {$result['total_processed']} baris diproses ({$result['inserted']} ditambahkan, {$result['updated']} diperbarui).";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'redirect' => route('kelas-kpi.index', ['view_mode' => 'lengkap'])
                ]);
            }

            return redirect()->route('kelas-kpi.index', ['view_mode' => 'lengkap'])->with('success', $msg);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal mengimpor data KPI: " . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->with('error', "Gagal mengimpor data KPI: " . $e->getMessage());
        }
    }

    /**
     * Download CSV template for Kelas KPI import (14 Raw Input Columns).
     * System automatically computes all 10+ derived metrics, scores, weights, and class statuses.
     */
    public function downloadTemplate()
    {
        $filename = 'template_impor_kelas_kpi.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            
            // 15 Raw Input Header Columns (including periode)
            fputcsv($file, [
                'cluster',
                'periode',
                'target rev all',
                'actual rev all',
                'target rev bb',
                'actual rev bb',
                'target rev pv',
                'actual rev pv',
                'target rgb',
                'actual rgb',
                'omzet rev m1',
                'mtd m1',
                'mtd',
                'outlet pjp',
                'outlet pjp growth'
            ]);

            // Sample Raw Input Row
            fputcsv($file, [
                'BALI BARAT',
                '2026-08',
                '24133301567',
                '22145064457',
                '14500000000',
                '13800000000',
                '5000000000',
                '4800000000',
                '4633301567',
                '3545064457',
                '21800000000',
                '21500000000',
                '22145064457',
                '1250',
                '4.2'
            ]);

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Helper method to sort month/period strings (e.g., "Januari 2026", "Februari 2026", "2026-01")
     * chronologically from January to December.
     */
    public static function sortPeriodesChronologically($periodesCollection)
    {
        $monthsMap = [
            'januari' => 1, 'jan' => 1,
            'februari' => 2, 'feb' => 2,
            'maret' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'mei' => 5, 'may' => 5,
            'juni' => 6, 'jun' => 6,
            'juli' => 7, 'jul' => 7,
            'agustus' => 8, 'aug' => 8, 'agust' => 8,
            'september' => 9, 'sep' => 9, 'sept' => 9,
            'oktober' => 10, 'okt' => 10, 'oct' => 10,
            'november' => 11, 'nov' => 11,
            'desember' => 12, 'des' => 12, 'dec' => 12,
        ];

        return collect($periodesCollection)->sortBy(function ($p) use ($monthsMap) {
            $str = strtolower(trim((string) $p));

            // Format YYYY-MM or YYYY_MM
            if (preg_match('/^(\d{4})[-_]?(\d{1,2})$/', $str, $m)) {
                return (int)$m[1] * 100 + (int)$m[2];
            }

            // Format "Month YYYY" (e.g. "Januari 2026")
            $monthNum = 0;
            foreach ($monthsMap as $name => $num) {
                if (str_contains($str, $name)) {
                    $monthNum = $num;
                    break;
                }
            }

            $year = 0;
            if (preg_match('/(\d{4})/', $str, $yMatch)) {
                $year = (int)$yMatch[1];
            }

            return $year * 100 + $monthNum;
        })->values();
    }
}
