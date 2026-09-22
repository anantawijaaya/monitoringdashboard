<?php

namespace Database\Seeders;

use App\Models\ClusterRevenue;
use App\Models\PeringkatData;
use Illuminate\Database\Seeder;

class PeringkatDataSeeder extends Seeder
{
    /**
     * Seed peringkat_data table from ClusterRevenue records.
     */
    public function run(): void
    {
        if (PeringkatData::count() > 0) {
            return;
        }
        $clusters = ClusterRevenue::all();

        foreach ($clusters as $item) {
            $type = PeringkatData::resolveClusterType($item->cluster_name);
            PeringkatData::updateOrCreate(
                [
                    'cluster' => $item->cluster_name,
                    'city' => $item->kabupaten,
                    'period_month' => $item->period_month ?? 'Agustus 2026',
                    'period_year' => $item->period_year ?? 2026,
                ],
                [
                    'type' => $type,
                    'target_rev_all' => $item->target_revenue_all ?? 0,
                    'actual_rev_all' => $item->mtd_revenue_all ?? 0,
                    'target_rev_bb' => $item->target_broadband ?? 0,
                    'actual_rev_bb' => $item->mtd_broadband ?? 0,
                    'target_rev_pv' => $item->target_redeem ?? 0,
                    'actual_rev_pv' => $item->mtd_redeem ?? 0,
                    'target_rgb' => $item->target_rgb ?? 0,
                    'actual_rgb' => $item->mtd_rgb ?? 0,
                    'omzet_rev_m1' => $item->revenue_last_month ?? 0,
                    'mtd_m1' => $item->revenue_last_month ?? 0,
                    'mtd' => $item->mtd_revenue_all ?? 0,
                    'growth_mom' => floatval($item->growth_mom ?? 0),
                    'total_score' => floatval($item->mtd_revenue_all ?? 0),
                    'status' => $item->status ?? 'Optimal',
                    'notes' => $item->notes ?? null,
                ]
            );
        }
    }
}
