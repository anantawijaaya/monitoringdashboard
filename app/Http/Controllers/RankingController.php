<?php

namespace App\Http\Controllers;

use App\Models\PeringkatData;
use App\Models\HierarchyOutlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RankingController extends Controller
{
    /**
     * Display the Revenue Rankings page ordered from highest to lowest
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Filters & Parameters
        $search = $request->query('search');
        $selectedCluster = $request->query('cluster', 'all');
        $selectedPeriod = $request->query('period', 'all');
        $selectedYear = $request->query('year', 'all');
        $selectedStatus = $request->query('status', 'all');
        $rankBy = $request->query('rank_by', 'total_desc');
        $viewMode = $request->query('view_mode', 'ranking');
        if (!in_array($viewMode, ['ranking', 'ringkasan', 'lengkap'])) {
            if ($viewMode === 'peringkat' || $viewMode === 'kabupaten') {
                $viewMode = 'ringkasan';
            } else {
                $viewMode = 'ranking';
            }
        }

        // Dropdown Options
        $availableClusters = PeringkatData::distinct()->pluck('cluster')->filter()->sort()->values();
        $availablePeriods = PeringkatData::distinct()->pluck('period_month')->filter()->values();
        $availableYears = PeringkatData::distinct()->pluck('period_year')->filter()->values();

        // Base Query
        $query = PeringkatData::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('cluster', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('period_month', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }

        if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
            $query->where('cluster', $selectedCluster);
        }

        if ($selectedPeriod !== 'all' && !empty($selectedPeriod)) {
            $query->where('period_month', $selectedPeriod);
        }

        if ($selectedYear !== 'all' && !empty($selectedYear)) {
            $query->where('period_year', intval($selectedYear));
        }

        if ($selectedStatus !== 'all' && !empty($selectedStatus)) {
            if ($selectedStatus === 'melampaui_target' || $selectedStatus === 'Melampaui Target') {
                $query->where(function ($q) {
                    $q->where('growth_mom', '>=', 1.0)
                      ->orWhere('notes', 'LIKE', '%Melampaui%')
                      ->orWhere('status', 'LIKE', '%Melampaui%');
                });
            } elseif ($selectedStatus === 'mencapai_target' || $selectedStatus === 'Mencapai Target') {
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
            } elseif ($selectedStatus === 'tidak_mencapai_target' || $selectedStatus === 'Tidak Mencapai Target') {
                $query->where(function ($q) {
                    $q->where('growth_mom', '<', 0)
                      ->orWhere('notes', 'LIKE', '%Tidak Mencapai%')
                      ->orWhere('status', 'LIKE', '%Tidak Mencapai%');
                });
            }
        }

        $allData = $query->get();

        if ($viewMode === 'cluster') {
            // Group by Cluster for Cluster-level Rankings
            $rankingsCollection = $allData->groupBy('cluster')->map(function ($rows, $clusterName) {
                $type = PeringkatData::resolveClusterType($clusterName);
                $cities = $rows->pluck('city')->filter()->unique()->implode(', ');

                $targetAll = $rows->sum('target_rev_all');
                $actualAll = $rows->sum('actual_rev_all');
                $targetBb = $rows->sum('target_rev_bb');
                $actualBb = $rows->sum('actual_rev_bb');
                $targetPv = $rows->sum('target_rev_pv');
                $actualPv = $rows->sum('actual_rev_pv');
                $targetRgb = $rows->sum('target_rgb');
                $actualRgb = $rows->sum('actual_rgb');
                $omzetM1 = $rows->sum('omzet_rev_m1');
                $mtdM1 = $rows->sum('mtd_m1');
                $mtd = $rows->sum('mtd') ?: $actualAll;
                $pjp = $rows->sum('outlet_pjp');
                $pjpGrowth = $rows->avg('outlet_pjp_growth') ?: 0;

                $model = new PeringkatData([
                    'cluster' => $clusterName,
                    'city' => $cities,
                    'type' => $type,
                    'period_month' => $rows->first()->period_month ?? 'Agustus 2026',
                    'period_year' => $rows->first()->period_year ?? 2026,
                    'target_rev_all' => $targetAll,
                    'actual_rev_all' => $actualAll,
                    'target_rev_bb' => $targetBb,
                    'actual_rev_bb' => $actualBb,
                    'target_rev_pv' => $targetPv,
                    'actual_rev_pv' => $actualPv,
                    'target_rgb' => $targetRgb,
                    'actual_rgb' => $actualRgb,
                    'omzet_rev_m1' => $omzetM1,
                    'mtd_m1' => $mtdM1,
                    'mtd' => $mtd,
                    'outlet_pjp' => $pjp,
                    'outlet_pjp_growth' => $pjpGrowth,
                ]);
                $model->computeFormulas();
                return $model;
            })->values();
        } else {
            // Kabupaten level rankings
            $rankingsCollection = $allData->map(function ($item) {
                $item->computeFormulas();
                return $item;
            });
        }

        // Apply Sorting from Highest to Lowest based on Total Final Score
        $sortedRankings = $rankingsCollection->sort(function ($a, $b) use ($rankBy) {
            if ($rankBy === 'mtd_desc') {
                return floatval($b->actual_rev_all) <=> floatval($a->actual_rev_all);
            } elseif ($rankBy === 'mom_desc') {
                return floatval($b->growth) <=> floatval($a->growth);
            } elseif ($rankBy === 'broadband_desc') {
                return floatval($b->ach_rev_bb) <=> floatval($a->ach_rev_bb);
            } elseif ($rankBy === 'redeem_desc') {
                return floatval($b->ach_rev_pv) <=> floatval($a->ach_rev_pv);
            } elseif ($rankBy === 'ach_desc') {
                return floatval($b->ach_rev_all) <=> floatval($a->ach_rev_all);
            } else {
                // Primary: Total Final Score DESC, Secondary: Actual Revenue All DESC
                $scoreDiff = (floatval($b->total_score ?: $b->final_score) <=> floatval($a->total_score ?: $a->final_score));
                if ($scoreDiff !== 0) return $scoreDiff;
                return floatval($b->actual_rev_all) <=> floatval($a->actual_rev_all);
            }
        })->values();

        // Assign Sequential Rank Numbers (1, 2, 3, ...)
        $rankedData = $sortedRankings->map(function ($item, $index) {
            $item->rank = $index + 1;
            return $item;
        });

        // Top 3 Podium Winners
        $top1 = $rankedData->get(0);
        $top2 = $rankedData->get(1);
        $top3 = $rankedData->get(2);

        // Overall KPI Metrics for Summary Cards
        $totalEntities = $rankedData->count();
        $topPerformer = $top1;
        $totalRegionalMtd = $rankedData->sum('actual_rev_all');
        $totalRegionalTarget = $rankedData->sum('target_rev_all');
        $avgRegionalAch = $totalRegionalTarget > 0 ? round(($totalRegionalMtd / $totalRegionalTarget) * 100, 1) : 0;
        $melampauiCount = $rankedData->filter(fn($i) => floatval($i->growth) >= 1.0 || str_contains(strtolower($i->status ?? ''), 'melampaui'))->count();
        // Fetch branch manager names dynamically from database table hierarchy_outlets
        $dbManagers = HierarchyOutlet::whereNotNull('manager_branch')
            ->where('manager_branch', '!=', '')
            ->get()
            ->groupBy(fn($item) => strtoupper(trim($item->branch)))
            ->map(fn($group) => $group->first()->manager_branch);

        // Prepare Branch-level Top 3 & Bottom 3 Ranking Data for 4 Branches
        $definedBranches = [
            'DENPASAR' => ['name' => 'BRANCH DENPASAR', 'manager' => $dbManagers->get('DENPASAR') ?: 'Herbianto'],
            'MATARAM'  => ['name' => 'BRANCH MATARAM', 'manager' => $dbManagers->get('MATARAM') ?: 'Kurnia Budi Setiawan'],
            'KUPANG'   => ['name' => 'BRANCH KUPANG', 'manager' => $dbManagers->get('KUPANG') ?: 'Adhy Yanwar'],
            'FLORES'   => ['name' => 'BRANCH FLORES', 'manager' => $dbManagers->get('FLORES') ?: 'Waskitho Anjar Prabowo'],
        ];

        $branchRankings = [];

        foreach ($definedBranches as $bCode => $bMeta) {
            $branchItems = $rankedData->filter(function ($item) use ($bCode) {
                $resolvedB = PeringkatData::resolveBranch($item->cluster ?? $item->cluster_name, $item->city ?? $item->kabupaten);
                return strtoupper($resolvedB) === strtoupper($bCode);
            })->values();

            // Sort branch items by Total Final Score DESC
            $sortedBranch = $branchItems->sort(function ($a, $b) {
                return (floatval($b->total_score ?: $b->final_score) <=> floatval($a->total_score ?: $a->final_score));
            })->values();

            $top3Branch = $sortedBranch->take(3)->values();
            $bottom3Branch = $sortedBranch->count() > 3 
                ? $sortedBranch->slice(-3)->values() 
                : collect();

            $branchRankings[$bCode] = [
                'code' => $bCode,
                'name' => $bMeta['name'],
                'manager' => $bMeta['manager'],
                'total_cities' => $sortedBranch->count(),
                'top3' => $top3Branch,
                'bottom3' => $bottom3Branch,
            ];
        }

        return view('monitoring-kpi.ranking.index', compact(
            'user',
            'rankedData',
            'branchRankings',
            'top1',
            'top2',
            'top3',
            'topPerformer',
            'totalEntities',
            'totalRegionalMtd',
            'totalRegionalTarget',
            'avgRegionalAch',
            'melampauiCount',
            'availableClusters',
            'availablePeriods',
            'availableYears',
            'selectedCluster',
            'selectedPeriod',
            'selectedYear',
            'selectedStatus',
            'search',
            'rankBy',
            'viewMode'
        ));
    }

    /**
     * Export Ranked Data to CSV / Excel
     */
    public function export(Request $request)
    {
        $search = $request->query('search');
        $selectedCluster = $request->query('cluster', 'all');
        $selectedPeriod = $request->query('period', 'all');
        $selectedYear = $request->query('year', 'all');
        $selectedStatus = $request->query('status', 'all');
        $rankBy = $request->query('rank_by', 'total_desc');
        $viewMode = $request->query('view_mode', 'kabupaten');

        $query = PeringkatData::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('cluster', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('period_month', 'LIKE', "%{$search}%");
            });
        }
        if ($selectedCluster !== 'all' && !empty($selectedCluster)) {
            $query->where('cluster', $selectedCluster);
        }
        if ($selectedPeriod !== 'all' && !empty($selectedPeriod)) {
            $query->where('period_month', $selectedPeriod);
        }
        if ($selectedYear !== 'all' && !empty($selectedYear)) {
            $query->where('period_year', intval($selectedYear));
        }

        $allData = $query->get();

        if ($viewMode === 'cluster') {
            $rankings = $allData->groupBy('cluster')->map(function ($rows, $clusterName) {
                $type = PeringkatData::resolveClusterType($clusterName);
                $cities = $rows->pluck('city')->filter()->unique()->implode(', ');
                $model = new PeringkatData([
                    'cluster' => $clusterName,
                    'city' => $cities,
                    'type' => $type,
                    'period_month' => $rows->first()->period_month ?? 'Agustus 2026',
                    'period_year' => $rows->first()->period_year ?? 2026,
                    'target_rev_all' => $rows->sum('target_rev_all'),
                    'actual_rev_all' => $rows->sum('actual_rev_all'),
                    'target_rev_bb' => $rows->sum('target_rev_bb'),
                    'actual_rev_bb' => $rows->sum('actual_rev_bb'),
                    'target_rev_pv' => $rows->sum('target_rev_pv'),
                    'actual_rev_pv' => $rows->sum('actual_rev_pv'),
                    'target_rgb' => $rows->sum('target_rgb'),
                    'actual_rgb' => $rows->sum('actual_rgb'),
                    'omzet_rev_m1' => $rows->sum('omzet_rev_m1'),
                    'mtd_m1' => $rows->sum('mtd_m1'),
                    'mtd' => $rows->sum('mtd') ?: $rows->sum('actual_rev_all'),
                    'outlet_pjp' => $rows->sum('outlet_pjp'),
                    'outlet_pjp_growth' => $rows->avg('outlet_pjp_growth') ?: 0,
                ]);
                $model->computeFormulas();
                return $model;
            })->values();
        } else {
            $rankings = $allData->map(function ($item) {
                $item->computeFormulas();
                return $item;
            });
        }

        $sorted = $rankings->sort(function ($a, $b) use ($rankBy) {
            if ($rankBy === 'mtd_desc') {
                return floatval($b->actual_rev_all) <=> floatval($a->actual_rev_all);
            } elseif ($rankBy === 'mom_desc') {
                return floatval($b->growth) <=> floatval($a->growth);
            } elseif ($rankBy === 'ach_desc') {
                return floatval($b->ach_rev_all) <=> floatval($a->ach_rev_all);
            } else {
                $scoreDiff = (floatval($b->total_score ?: $b->final_score) <=> floatval($a->total_score ?: $a->final_score));
                if ($scoreDiff !== 0) return $scoreDiff;
                return floatval($b->actual_rev_all) <=> floatval($a->actual_rev_all);
            }
        })->values();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="ranking_revenue_balinusra_' . date('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($sorted) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, [
                'CITY / KABUPATEN',
                'CLUSTER',
                'PERIODE',
                'TIPE CLUSTER',
                // REV ALL
                'TARGET REV ALL',
                'ACTUAL REV ALL',
                'ACH REV ALL (%)',
                'SCORE REV ALL',
                'WEIGHT REV ALL (%)',
                'FINAL SCORE REV ALL',
                // REV BB
                'TARGET REV BROADBAND',
                'ACTUAL REV BROADBAND',
                'ACH REV BROADBAND (%)',
                'SCORE REV BROADBAND',
                'WEIGHT REV BROADBAND (%)',
                'FINAL SCORE REV BROADBAND',
                // REV PV
                'TARGET REV REDEEM PV',
                'ACTUAL REV REDEEM PV',
                'ACH REV REDEEM PV (%)',
                'SCORE REV REDEEM PV',
                'WEIGHT REV REDEEM PV (%)',
                'FINAL SCORE REV REDEEM PV',
                // REV RGB
                'TARGET REV RGB',
                'ACTUAL REV RGB',
                'ACH REV RGB (%)',
                'SCORE REV RGB',
                'WEIGHT REV RGB (%)',
                'FINAL SCORE REV RGB',
                // OMZET OUTLET & GROWTH
                'OMZET REV M1',
                'MTD M1',
                'TGT 3%',
                'MTD',
                'GROWTH (%)',
                'GROWTH TGT (%)',
                'REV GROWTH ACH (%)',
                'OUTLET PJP',
                'OUTLET PJP GROWTH',
                'RATIO OUTLET PJP (%)',
                'OMZET OUTLET ACH (%)',
                'SCORE OMZET OUTLET',
                'WEIGHT OMZET OUTLET (%)',
                'FINAL SCORE OMZET OUTLET',
                // TOTAL & PERINGKAT
                'TOTAL FINAL SCORE',
                'PERINGKAT'
            ], ';');

            $rankNum = 1;
            foreach ($sorted as $row) {
                $row->computeFormulas();
                fputcsv($handle, [
                    $row->city ?? $row->kabupaten ?? '-',
                    $row->cluster ?? $row->cluster_name ?? '-',
                    ($row->period_month ?? '') . ' ' . ($row->period_year ?? ''),
                    $row->type ?? 'HIGH',
                    // REV ALL
                    number_format($row->target_rev_all, 0, ',', '.'),
                    number_format($row->actual_rev_all, 0, ',', '.'),
                    number_format($row->ach_rev_all ?: $row->ach_revenue_all, 1, ',', '.') . '%',
                    number_format($row->score_rev_all ?: 2, 0),
                    '15%',
                    number_format($row->final_score_rev_all ?: 0.3, 2, ',', '.'),
                    // REV BB
                    number_format($row->target_rev_bb, 0, ',', '.'),
                    number_format($row->actual_rev_bb, 0, ',', '.'),
                    number_format($row->ach_rev_bb ?: $row->ach_broadband, 1, ',', '.') . '%',
                    number_format($row->score_rev_bb ?: 2, 0),
                    number_format($row->weight_rev_bb ?: 15, 0) . '%',
                    number_format($row->final_score_rev_bb ?: 0.3, 2, ',', '.'),
                    // REV PV
                    number_format($row->target_rev_pv, 0, ',', '.'),
                    number_format($row->actual_rev_pv, 0, ',', '.'),
                    number_format($row->ach_rev_pv ?: $row->ach_redeem, 1, ',', '.') . '%',
                    number_format($row->score_rev_pv ?: 2, 0),
                    number_format($row->weight_rev_pv ?: 25, 0) . '%',
                    number_format($row->final_score_rev_pv ?: 0.5, 2, ',', '.'),
                    // REV RGB
                    number_format($row->target_rgb, 0, ',', '.'),
                    number_format($row->actual_rgb, 0, ',', '.'),
                    number_format($row->ach_rgb, 1, ',', '.') . '%',
                    number_format($row->score_rgb ?: 2, 0),
                    '20%',
                    number_format($row->final_score_rgb ?: 0.4, 2, ',', '.'),
                    // OMZET OUTLET & GROWTH
                    number_format($row->omzet_rev_m1, 0, ',', '.'),
                    number_format($row->mtd_m1, 0, ',', '.'),
                    number_format($row->tgt_3_percent, 0, ',', '.'),
                    number_format($row->mtd ?: $row->actual_rev_all, 0, ',', '.'),
                    number_format($row->growth ?: $row->growth_mom, 1, ',', '.') . '%',
                    '3,0%',
                    number_format($row->rev_growth_ach_percent, 1, ',', '.') . '%',
                    number_format($row->outlet_pjp, 0, ',', '.'),
                    number_format($row->outlet_pjp_growth, 0, ',', '.'),
                    number_format($row->ratio_outlet_pjp, 1, ',', '.') . '%',
                    number_format($row->omzet_outlet_ach, 1, ',', '.') . '%',
                    number_format($row->score_omzet ?: 2, 0),
                    '25%',
                    number_format($row->final_score_omzet ?: 0.5, 2, ',', '.'),
                    // TOTAL & PERINGKAT
                    number_format($row->total_score ?: $row->final_score, 2, ',', '.'),
                    $rankNum++
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import Ranking Revenue Data from CSV/Excel matching user template columns
     */
    public function import(Request $request, \App\Services\RankingImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xls,xlsx|max:10240',
            'mode' => 'nullable|string|in:append,replace',
        ], [
            'file.required' => 'Silakan pilih berkas data Peringkat (CSV/Excel) terlebih dahulu.',
            'file.mimes' => 'Format berkas harus berupa .csv, .xls, atau .xlsx',
            'file.max' => 'Ukuran berkas tidak boleh lebih dari 10 MB.'
        ]);

        try {
            $mode = $request->input('mode', 'append');
            $result = $importService->importFile($request->file('file'), $mode);

            $msg = "Impor data peringkat berhasil! {$result['total_processed']} baris diproses ({$result['inserted']} ditambahkan, {$result['updated']} diperbarui).";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'redirect' => route('ranking.index')
                ]);
            }

            return redirect()->route('ranking.index')->with('success', $msg);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal mengimpor data peringkat: " . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->with('error', "Gagal mengimpor data peringkat: " . $e->getMessage());
        }
    }

    /**
     * Download CSV template matching the exact column layout from user image:
     * city, cluster, target rev all, actual rev all, target rev bb, actual rev bb,
     * target rev pv, actual rev pv, target rgb, actual rgb, omzet rev m1, mtd m1, mtd, outlet pjp, outlet pjp growth, periode
     */
    public function downloadTemplate()
    {
        $filename = 'template_impor_peringkat_revenue.csv';

        $headers = [
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, "\xEF\xBB\xBF");

            // Header matching user screenshot columns
            fputcsv($file, [
                'city',
                'cluster',
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
                'outlet pjp growth',
                'periode'
            ], ';');

            // Sample Rows
            fputcsv($file, [
                'BULELENG',
                'BALI BARAT',
                '4250000000',
                '4180000000',
                '3870000000',
                '3810000000',
                '245000000',
                '228000000',
                '135000000',
                '142000000',
                '4100000000',
                '4100000000',
                '4180000000',
                '1250',
                '4.2',
                'Agustus 2026'
            ], ';');

            fputcsv($file, [
                'KOTA DENPASAR',
                'BALI TENGAH',
                '22395936544',
                '22168450756',
                '19961726301',
                '19883265180',
                '1267080024',
                '1196144504',
                '1167080024',
                '1089035072',
                '21500000000',
                '21500000000',
                '22168450756',
                '3450',
                '5.8',
                'Agustus 2026'
            ], ';');

            fclose($file);
        };

        return \Illuminate\Support\Facades\Response::stream($callback, 200, $headers);
    }
}
