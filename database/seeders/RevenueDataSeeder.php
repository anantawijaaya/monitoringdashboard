<?php

namespace Database\Seeders;

use App\Models\ClusterRevenue;
use App\Models\RevenueData;
use Illuminate\Database\Seeder;

class RevenueDataSeeder extends Seeder
{
    /**
     * Seed revenue_data table from ClusterRevenue records.
     */
    public function run(): void
    {
        $clusters = ClusterRevenue::all();

        foreach ($clusters as $item) {
            RevenueData::updateOrCreate(
                [
                    'cluster_name' => $item->cluster_name,
                    'kabupaten' => $item->kabupaten,
                    'period_month' => $item->period_month,
                    'period_year' => $item->period_year,
                ],
                [
                    'target_revenue_all' => $item->target_revenue_all,
                    'mtd_revenue_all' => $item->mtd_revenue_all,
                    'target_broadband' => $item->target_broadband,
                    'mtd_broadband' => $item->mtd_broadband,
                    'target_redeem' => $item->target_redeem,
                    'mtd_redeem' => $item->mtd_redeem,
                    'target_rgb' => $item->target_rgb,
                    'mtd_rgb' => $item->mtd_rgb,
                    'revenue_last_month' => $item->revenue_last_month,
                    'revenue_current_month' => $item->revenue_current_month,
                    'status' => $item->status,
                    'notes' => $item->notes,
                ]
            );
        }
    }
}
