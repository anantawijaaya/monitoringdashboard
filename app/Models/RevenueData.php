<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model RevenueData representing the 'revenue_data' database table for Revenue menu.
 */
class RevenueData extends Model
{
    use HasFactory;

    protected $table = 'revenue_data';

    protected $fillable = [
        'cluster_name',
        'kabupaten',
        'period_month',
        'period_year',
        'target_revenue_all',
        'mtd_revenue_all',
        'ach_revenue_all',
        'target_broadband',
        'mtd_broadband',
        'ach_broadband',
        'target_redeem',
        'mtd_redeem',
        'ach_redeem',
        'target_rgb',
        'mtd_rgb',
        'ach_rgb',
        'revenue_last_month',
        'revenue_current_month',
        'growth_mom',
        'status',
        'notes',
    ];

    protected $casts = [
        'period_year' => 'integer',
        'target_revenue_all' => 'float',
        'mtd_revenue_all' => 'float',
        'ach_revenue_all' => 'float',
        'target_broadband' => 'float',
        'mtd_broadband' => 'float',
        'ach_broadband' => 'float',
        'target_redeem' => 'float',
        'mtd_redeem' => 'float',
        'ach_redeem' => 'float',
        'target_rgb' => 'float',
        'mtd_rgb' => 'float',
        'ach_rgb' => 'float',
        'revenue_last_month' => 'float',
        'revenue_current_month' => 'float',
        'growth_mom' => 'float',
    ];

    /**
     * Determine status label from Growth MoM %
     */
    public static function determineStatusFromMom($mom)
    {
        $val = floatval($mom);
        if ($val >= 1.0) {
            return 'Melampaui Target';
        } elseif ($val >= 0.0) {
            return 'Mencapai Target';
        } else {
            return 'Tidak Mencapai Target';
        }
    }

    /**
     * Boot model to automatically compute all percentage achievements and MoM growth on save.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->target_revenue_all = floatval($model->target_revenue_all);
            $model->mtd_revenue_all = floatval($model->mtd_revenue_all);
            $model->target_broadband = floatval($model->target_broadband);
            $model->mtd_broadband = floatval($model->mtd_broadband);
            $model->target_redeem = floatval($model->target_redeem);
            $model->mtd_redeem = floatval($model->mtd_redeem);
            $model->target_rgb = floatval($model->target_rgb);
            $model->mtd_rgb = floatval($model->mtd_rgb);

            // 1. Sum Broadband & Redeem if Revenue All is 0
            if (floatval($model->mtd_revenue_all) <= 0 && (floatval($model->mtd_broadband) > 0 || floatval($model->mtd_redeem) > 0)) {
                $model->mtd_revenue_all = floatval($model->mtd_broadband) + floatval($model->mtd_redeem);
            }
            if (floatval($model->target_revenue_all) <= 0 && (floatval($model->target_broadband) > 0 || floatval($model->target_redeem) > 0)) {
                $model->target_revenue_all = floatval($model->target_broadband) + floatval($model->target_redeem);
            }

            // 2. Compute Achievements %
            if (floatval($model->target_revenue_all) > 0) {
                $model->ach_revenue_all = round((floatval($model->mtd_revenue_all) / floatval($model->target_revenue_all)) * 100, 2);
            } else {
                $model->ach_revenue_all = 0;
            }

            if (floatval($model->target_broadband) > 0) {
                $model->ach_broadband = round((floatval($model->mtd_broadband) / floatval($model->target_broadband)) * 100, 2);
            } else {
                $model->ach_broadband = 0;
            }

            if (floatval($model->target_redeem) > 0) {
                $model->ach_redeem = round((floatval($model->mtd_redeem) / floatval($model->target_redeem)) * 100, 2);
            } else {
                $model->ach_redeem = 0;
            }

            if (floatval($model->target_rgb) > 0) {
                $model->ach_rgb = round((floatval($model->mtd_rgb) / floatval($model->target_rgb)) * 100, 2);
            } else {
                $model->ach_rgb = 0;
            }

            // 3. Compute Growth MoM %
            if (floatval($model->revenue_current_month) <= 0 && floatval($model->mtd_revenue_all) > 0) {
                $model->revenue_current_month = floatval($model->mtd_revenue_all);
            }

            if (floatval($model->revenue_last_month) > 0) {
                $last = floatval($model->revenue_last_month);
                $curr = floatval($model->revenue_current_month);
                $model->growth_mom = round((($curr - $last) / $last) * 100, 2);
            } else {
                $model->growth_mom = 0;
            }

            // 4. Compute Status
            $momCatatan = self::determineStatusFromMom($model->growth_mom);
            $model->status = $momCatatan;

            if (empty($model->notes) || in_array($model->notes, [
                'Melampaui Target', 'Mencapai Target', 'Tidak Mencapai Target', 
                'Optimal', 'Perlu Perhatian', 'Perlu Peningkatan', 'Belum Tercapai', 'Tercapai', '-'
            ])) {
                $model->notes = $momCatatan;
            }
        });
    }
}
