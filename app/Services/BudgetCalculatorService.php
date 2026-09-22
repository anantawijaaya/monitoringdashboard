<?php

namespace App\Services;

use App\Models\CultureProgramAllocation;
use App\Models\CultureProgramExpense;
use App\Models\DirectSalesAllocation;
use App\Models\DirectSalesExpense;
use App\Models\IndirectChannelAllocation;
use App\Models\IndirectChannelExpense;

/**
 * Class BudgetCalculatorService
 * 
 * Centralized service for calculating budget allocations, approved expense realisasi,
 * remaining budget balance, and generating formatted data for cards and modal forms
 * across Indirect Channel, Direct Sales, and Culture Program modules.
 */
class BudgetCalculatorService
{
    /**
     * Calculate summary metrics (Total Alokasi, Total Realisasi, % Realisasi, Sisa Budget)
     * for a given module filtered by cluster.
     *
     * @param string $allocationModel Fully qualified class name of Allocation Eloquent model
     * @param string $expenseModel Fully qualified class name of Expense Eloquent model
     * @param string $selectedCluster Selected cluster name or 'all'
     * @return array Matrix of summary stats
     */
    public function calculateSummary(string $allocationModel, string $expenseModel, string $selectedCluster = 'all'): array
    {
        // 1. Calculate Total Allocation
        $allocationsQuery = $allocationModel::query();
        if ($selectedCluster !== 'all') {
            $allocationsQuery->where('cluster', $selectedCluster);
        }
        $totalAlokasi = (float) $allocationsQuery->sum('total_budget');

        // 2. Calculate Total Realisasi (Approved Expenses Only)
        $approvedExpensesQuery = $expenseModel::where('status', 'Disetujui');
        if ($selectedCluster !== 'all') {
            $approvedExpensesQuery->where('cluster', $selectedCluster);
        }
        $totalRealisasi = (float) $approvedExpensesQuery->sum('nominal_pengeluaran');

        // 3. Calculate Percentage & Remaining Budget
        $totalRealisasiPersen = $totalAlokasi > 0 ? round(($totalRealisasi / $totalAlokasi) * 100, 1) : 0;
        $sisaBudget = $totalAlokasi - $totalRealisasi;

        return [
            'totalAlokasi' => $totalAlokasi,
            'totalRealisasi' => $totalRealisasi,
            'totalRealisasiPersen' => $totalRealisasiPersen,
            'sisaBudget' => $sisaBudget,
        ];
    }


    /**
     * Calculate statistics for program cards, including budget, realisasi, sisa, and percentage,
     * with optional sorting by realisasi (tertinggi / terendah).
     *
     * @param string $allocationModel Fully qualified class name of Allocation model
     * @param string $expenseModel Fully qualified class name of Expense model
     * @param array $cardsDef Definitions of program cards (key, title, col, icon_html, bg_icon)
     * @param string $selectedCluster Selected cluster filter
     * @param string $selectedSort Sort order ('default', 'tertinggi', 'terendah')
     * @return array Formatted program cards with statistics
     */
    public function calculateProgramCards(
        string $allocationModel,
        string $expenseModel,
        array $cardsDef,
        string $selectedCluster = 'all',
        string $selectedSort = 'default'
    ): array {
        $programCardsList = [];

        foreach ($cardsDef as $cDef) {
            // Program Budget Allocation
            $bQuery = $allocationModel::query();
            if ($selectedCluster !== 'all') {
                $bQuery->where('cluster', $selectedCluster);
            }
            $pBudget = (float) $bQuery->sum($cDef['col']);

            // Program Approved Realisasi
            $rQuery = $expenseModel::where('status', 'Disetujui');
            // Filter program_name if not default single-program model
            if (isset($cDef['title']) && $cDef['title'] !== 'Culture Program') {
                $rQuery->where('program_name', $cDef['title']);
            }
            if ($selectedCluster !== 'all') {
                $rQuery->where('cluster', $selectedCluster);
            }
            $pRealisasi = (float) $rQuery->sum('nominal_pengeluaran');

            $pSisa = $pBudget - $pRealisasi;
            $pPersen = $pBudget > 0 ? round(($pRealisasi / $pBudget) * 100, 1) : 0;

            $programCardsList[] = [
                'key' => $cDef['key'],
                'title' => $cDef['title'],
                'icon_html' => $cDef['icon_html'],
                'bg_icon' => $cDef['bg_icon'],
                'stat' => [
                    'budget' => $pBudget,
                    'realisasi' => $pRealisasi,
                    'sisa' => $pSisa,
                    'persen' => $pPersen,
                ],
            ];
        }

        // Apply Realisasi Sorting
        if ($selectedSort === 'tertinggi') {
            usort($programCardsList, fn($a, $b) => $b['stat']['realisasi'] <=> $a['stat']['realisasi']);
        } elseif ($selectedSort === 'terendah') {
            usort($programCardsList, fn($a, $b) => $a['stat']['realisasi'] <=> $b['stat']['realisasi']);
        }

        return $programCardsList;
    }

    /**
     * Build cluster program budget mapping used for JavaScript frontend modal dynamic calculations.
     *
     * @param string $allocationModel Fully qualified class name of Allocation model
     * @param string $expenseModel Fully qualified class name of Expense model
     * @param array $clusterMap Cluster key-value pair mapping
     * @param array $cardsDef Cards definitions
     * @return array Cluster program budgets matrix
     */
    public function buildClusterProgramBudgets(
        string $allocationModel,
        string $expenseModel,
        array $clusterMap,
        array $cardsDef
    ): array {
        $clusterProgramBudgets = [];

        foreach ($clusterMap as $cKey => $cItem) {
            foreach ($cardsDef as $cDef) {
                // Budget Allocation
                $bQuery = $allocationModel::query();
                if ($cKey !== 'all') {
                    $bQuery->where('cluster', $cKey);
                }
                $bSum = (float) $bQuery->sum($cDef['col']);

                // Approved Realisasi
                $approvedQuery = $expenseModel::where('status', 'Disetujui');
                if (isset($cDef['title']) && $cDef['title'] !== 'Culture Program') {
                    $approvedQuery->where('program_name', $cDef['title']);
                }
                if ($cKey !== 'all') {
                    $approvedQuery->where('cluster', $cKey);
                }
                $rSum = (float) $approvedQuery->sum('nominal_pengeluaran');

                // Total Submitted (Disetujui + Pending / non-Ditolak)
                $submittedQuery = $expenseModel::where('status', '!=', 'Ditolak');
                if (isset($cDef['title']) && $cDef['title'] !== 'Culture Program') {
                    $submittedQuery->where('program_name', $cDef['title']);
                }
                if ($cKey !== 'all') {
                    $submittedQuery->where('cluster', $cKey);
                }
                $sSum = (float) $submittedQuery->sum('nominal_pengeluaran');

                $clusterProgramBudgets[$cKey][$cDef['title']] = [
                    'budget' => $bSum,
                    'realisasi' => $rSum,
                    'pengajuan_terpakai' => $sSum,
                    'sisa_sebelumnya' => max(0, $bSum - $sSum),
                ];
            }
        }

        return $clusterProgramBudgets;
    }

    /**
     * Calculate budget realization summary for each Cluster and Mitra across
     * Indirect Channel, Direct Sales, and Culture Program.
     *
     * @return array Array of cluster realization summary items
     */
    public function calculateClusterRealizationSummary(?string $selectedCluster = 'all'): array
    {
        $defaultClusters = [
            'BALI BARAT',
            'BALI TENGAH',
            'BALI TIMUR',
            'ENDE SIKKA',
            'FLORES TIMUR',
            'MANGGARAI',
            'KUPANG ROTE',
            'MALAKA TIMTIM BELU',
            'SUMBA',
            'LOMBOK',
            'SUMBAWA BARAT',
            'SUMBAWA TIMUR',
        ];

        $clustersMap = [];

        // Indirect Channel Allocations
        $icAllocations = IndirectChannelAllocation::all();
        foreach ($icAllocations as $alloc) {
            $c = trim($alloc->cluster ?? '');
            $m = trim($alloc->mitra ?? '');
            if ($c !== '' && $c !== 'all') {
                $key = $c . '___' . ($m !== '' ? $m : '-');
                $clustersMap[$key] = ['cluster' => $c, 'mitra' => $m !== '' ? $m : '-'];
            }
        }

        // Direct Sales Allocations
        $dsAllocations = DirectSalesAllocation::all();
        foreach ($dsAllocations as $alloc) {
            $c = trim($alloc->cluster ?? '');
            $m = trim($alloc->mitra ?? '');
            if ($c !== '' && $c !== 'all' && $c !== 'General') {
                $key = $c . '___' . ($m !== '' ? $m : '-');
                if (!isset($clustersMap[$key])) {
                    $clustersMap[$key] = ['cluster' => $c, 'mitra' => $m !== '' ? $m : '-'];
                }
            }
        }

        // Culture Program Allocations
        $cpAllocations = CultureProgramAllocation::all();
        foreach ($cpAllocations as $alloc) {
            $c = trim($alloc->cluster ?? '');
            $m = trim($alloc->mitra ?? '');
            if ($c !== '' && $c !== 'all' && $c !== 'General') {
                $key = $c . '___' . ($m !== '' ? $m : '-');
                if (!isset($clustersMap[$key])) {
                    $clustersMap[$key] = ['cluster' => $c, 'mitra' => $m !== '' ? $m : '-'];
                }
            }
        }

        // Ensure all default 12 clusters are present
        foreach ($defaultClusters as $c) {
            $found = false;
            foreach ($clustersMap as $item) {
                if (strtoupper($item['cluster']) === strtoupper($c)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $key = $c . '___-';
                $clustersMap[$key] = ['cluster' => $c, 'mitra' => '-'];
            }
        }

        // Filter clustersMap if selectedCluster is specified
        if ($selectedCluster && $selectedCluster !== 'all') {
            $clustersMap = array_filter($clustersMap, function ($item) use ($selectedCluster) {
                return strtoupper(trim($item['cluster'])) === strtoupper(trim($selectedCluster));
            });
        }

        // Pre-load all approved expenses
        $icApprovedExpenses = IndirectChannelExpense::where('status', 'Disetujui')->get();
        $dsApprovedExpenses = DirectSalesExpense::where('status', 'Disetujui')->get();
        $cpApprovedExpenses = CultureProgramExpense::where('status', 'Disetujui')->get();

        $result = [];

        foreach ($clustersMap as $cm) {
            $c = $cm['cluster'];
            $m = $cm['mitra'];

            // --- Indirect Channel ---
            $icAllocQuery = IndirectChannelAllocation::where('cluster', $c);
            if ($m !== '-' && $m !== '') {
                $icAllocQuery->where('mitra', $m);
            }
            $icBudget = (float) $icAllocQuery->sum('total_budget');

            $icRealisasi = (float) $icApprovedExpenses
                ->filter(function ($e) use ($c, $m) {
                    $matchCluster = strtoupper(trim($e->cluster ?? '')) === strtoupper(trim($c));
                    $matchMitra = ($m === '-' || $m === '') || (strtoupper(trim($e->mitra ?? '')) === strtoupper(trim($m)));
                    return $matchCluster && $matchMitra;
                })
                ->sum('nominal_pengeluaran');

            $icPersen = $icBudget > 0 ? round(($icRealisasi / $icBudget) * 100, 1) : 0;

            // --- Direct Sales ---
            $dsAllocQuery = DirectSalesAllocation::where('cluster', $c);
            if ($m !== '-' && $m !== '') {
                $dsAllocQuery->where('mitra', $m);
            }
            $dsBudget = (float) $dsAllocQuery->sum('total_budget');

            $dsRealisasi = (float) $dsApprovedExpenses
                ->filter(function ($e) use ($c, $m) {
                    $matchCluster = strtoupper(trim($e->cluster ?? '')) === strtoupper(trim($c));
                    $matchMitra = ($m === '-' || $m === '') || (strtoupper(trim($e->mitra ?? '')) === strtoupper(trim($m)));
                    return $matchCluster && $matchMitra;
                })
                ->sum('nominal_pengeluaran');

            $dsPersen = $dsBudget > 0 ? round(($dsRealisasi / $dsBudget) * 100, 1) : 0;

            // --- Culture Program ---
            $cpAllocQuery = CultureProgramAllocation::where('cluster', $c);
            if ($m !== '-' && $m !== '') {
                $cpAllocQuery->where('mitra', $m);
            }
            $cpBudget = (float) $cpAllocQuery->sum('total_budget');

            $cpRealisasi = (float) $cpApprovedExpenses
                ->filter(function ($e) use ($c, $m) {
                    $matchCluster = strtoupper(trim($e->cluster ?? '')) === strtoupper(trim($c));
                    $matchMitra = ($m === '-' || $m === '') || (strtoupper(trim($e->mitra ?? '')) === strtoupper(trim($m)));
                    return $matchCluster && $matchMitra;
                })
                ->sum('nominal_pengeluaran');

            $cpPersen = $cpBudget > 0 ? round(($cpRealisasi / $cpBudget) * 100, 1) : 0;

            // Total Overall Realisasi & Budget
            $totalOverallBudget = $icBudget + $dsBudget + $cpBudget;
            $totalOverallRealisasi = $icRealisasi + $dsRealisasi + $cpRealisasi;
            $overallPersen = $totalOverallBudget > 0 ? round(($totalOverallRealisasi / $totalOverallBudget) * 100, 1) : 0;

            $result[] = [
                'cluster' => $c,
                'mitra' => $m,
                'indirect_channel' => [
                    'budget' => $icBudget,
                    'realisasi' => $icRealisasi,
                    'persen' => $icPersen,
                ],
                'direct_sales' => [
                    'budget' => $dsBudget,
                    'realisasi' => $dsRealisasi,
                    'persen' => $dsPersen,
                ],
                'culture_program' => [
                    'budget' => $cpBudget,
                    'realisasi' => $cpRealisasi,
                    'persen' => $cpPersen,
                ],
                'overall' => [
                    'budget' => $totalOverallBudget,
                    'realisasi' => $totalOverallRealisasi,
                    'persen' => $overallPersen,
                ]
            ];
        }

        return array_values($result);
    }
}

