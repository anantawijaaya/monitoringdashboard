<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClusterRevenue extends Model
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
        'omzet_rev_m1',
        'mtd_m1',
        'mtd',
        'outlet_pjp',
        'outlet_pjp_growth',
        'total_score',
        'revenue_all',
        'revenue_broadband',
        'revenue_redeem_pv',
        'revenue_rgb',
        'target_revenue',
        'achievement_rate',
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
        'omzet_rev_m1' => 'float',
        'mtd_m1' => 'float',
        'mtd' => 'float',
        'outlet_pjp' => 'float',
        'outlet_pjp_growth' => 'float',
        'total_score' => 'float',
        'revenue_all' => 'float',
        'revenue_broadband' => 'float',
        'revenue_redeem_pv' => 'float',
        'revenue_rgb' => 'float',
        'target_revenue' => 'float',
        'achievement_rate' => 'float',
    ];

    /**
     * Boot model to automatically compute all percentage achievements and MoM growth
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

            // 1. If MTD Revenue All is 0 but Broadband & Redeem are provided, sum them
            if (floatval($model->mtd_revenue_all) <= 0 && (floatval($model->mtd_broadband) > 0 || floatval($model->mtd_redeem) > 0)) {
                $model->mtd_revenue_all = floatval($model->mtd_broadband) + floatval($model->mtd_redeem);
            }
            if (floatval($model->target_revenue_all) <= 0 && (floatval($model->target_broadband) > 0 || floatval($model->target_redeem) > 0)) {
                $model->target_revenue_all = floatval($model->target_broadband) + floatval($model->target_redeem);
            }

            // 2. Compute Achievement % for Revenue All
            if (floatval($model->target_revenue_all) > 0) {
                $model->ach_revenue_all = round((floatval($model->mtd_revenue_all) / floatval($model->target_revenue_all)) * 100, 2);
            } else {
                $model->ach_revenue_all = 0;
            }

            // 3. Compute Achievement % for Broadband
            if (floatval($model->target_broadband) > 0) {
                $model->ach_broadband = round((floatval($model->mtd_broadband) / floatval($model->target_broadband)) * 100, 2);
            } else {
                $model->ach_broadband = 0;
            }

            // 4. Compute Achievement % for Redeem PV
            if (floatval($model->target_redeem) > 0) {
                $model->ach_redeem = round((floatval($model->mtd_redeem) / floatval($model->target_redeem)) * 100, 2);
            } else {
                $model->ach_redeem = 0;
            }

            // 5. Compute Achievement % for Revenue RGB
            if (floatval($model->target_rgb) > 0) {
                $model->ach_rgb = round((floatval($model->mtd_rgb) / floatval($model->target_rgb)) * 100, 2);
            } else {
                $model->ach_rgb = 0;
            }

            // 6. Compute Growth MoM %
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

            // Sync standard aliases
            $model->revenue_all = $model->mtd_revenue_all;
            $model->revenue_broadband = $model->mtd_broadband;
            $model->revenue_redeem_pv = $model->mtd_redeem;
            $model->revenue_rgb = $model->mtd_rgb;
            $model->target_revenue = $model->target_revenue_all;
            $model->achievement_rate = $model->ach_revenue_all;

            // 6. Compute Catatan / Status based on Growth MoM %
            $momCatatan = self::determineStatusFromMom($model->growth_mom);
            $model->status = $momCatatan;

            // Sync notes if empty or previously assigned default target labels
            if (empty($model->notes) || in_array($model->notes, [
                'Melampaui Target', 'Mencapai Target', 'Tidak Mencapai Target', 
                'Optimal', 'Perlu Perhatian', 'Perlu Peningkatan', 'Belum Tercapai', 'Tercapai', '-'
            ])) {
                $model->notes = $momCatatan;
            }
        });
    }

    /**
     * Determine Catatan / Status based on Growth MoM:
     * - MoM >= 1% -> 'Melampaui Target'
     * - 0% <= MoM < 1% (or <= 0.9%) -> 'Mencapai Target'
     * - MoM < 0% (e.g. < 0% to -10% / negative) -> 'Tidak Mencapai Target'
     */
    public static function determineStatusFromMom($mom): string
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
     * Get computed Catatan attribute
     */
    public function getCatatanAttribute(): string
    {
        if (!empty($this->notes) && !in_array($this->notes, [
            'Melampaui Target', 'Mencapai Target', 'Tidak Mencapai Target', 
            'Optimal', 'Perlu Perhatian', 'Perlu Peningkatan', '-'
        ])) {
            return $this->notes;
        }
        return self::determineStatusFromMom($this->growth_mom);
    }

    /**
     * Formatted string helpers
     */
    public function getFormattedRevenueAllAttribute(): string
    {
        return self::formatCurrencyDisplay($this->mtd_revenue_all ?: $this->revenue_all);
    }

    public function getFormattedBroadbandAttribute(): string
    {
        return self::formatCurrencyDisplay($this->mtd_broadband ?: $this->revenue_broadband);
    }

    public function getFormattedRedeemPvAttribute(): string
    {
        return self::formatCurrencyDisplay($this->mtd_redeem ?: $this->revenue_redeem_pv);
    }

    public function getFormattedTargetAttribute(): string
    {
        return self::formatCurrencyDisplay($this->target_revenue_all ?: $this->target_revenue);
    }

    public static function formatCurrencyDisplay($amount): string
    {
        $val = floatval($amount);
        if ($val >= 1000000000000) {
            return number_format($val / 1000000000000, 2, ',', '.') . ' Triliun';
        } elseif ($val >= 1000000000) {
            return number_format($val / 1000000000, 2, ',', '.') . ' Miliar';
        } elseif ($val >= 1000000) {
            return number_format($val / 1000000, 0, ',', '.') . ' Juta';
        } elseif ($val >= 1000) {
            return number_format($val / 1000, 0, ',', '.') . ' Ribu';
        }
        return number_format($val, 0, ',', '.');
    }

    /**
     * Format currency amount to M (Miliar / Billion) or JT (Juta / Million) format.
     */
    public static function formatRevenueBm($amount, bool $withPrefix = false): string
    {
        $val = floatval($amount);
        $prefix = $withPrefix ? 'Rp ' : '';
        if (abs($val) >= 1000000000) {
            return $prefix . number_format($val / 1000000000, 2, ',', '.') . ' M';
        } elseif (abs($val) >= 1000000) {
            return $prefix . number_format($val / 1000000, 2, ',', '.') . ' JT';
        }
        return $prefix . number_format($val, 0, ',', '.');
    }
}
