<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeringkatData extends Model
{
    use HasFactory;

    protected $table = 'peringkat_data';

    protected $fillable = [
        'cluster',
        'city',
        'period_month',
        'period_year',
        'type',

        // 23 Raw & Metric Columns
        'target_rev_all',
        'actual_rev_all',
        'target_rev_bb',
        'actual_rev_bb',
        'target_rev_pv',
        'actual_rev_pv',
        'target_rgb',
        'actual_rgb',
        'omzet_rev_m1',
        'mtd_m1',
        'tgt_3_percent',
        'mtd',
        'growth',
        'growth_tgt',
        'rev_growth_ach_percent',
        'outlet_pjp',
        'outlet_pjp_growth',
        'ratio_outlet_pjp',
        'omzet_outlet_ach',

        // Scores & Weights
        'ach_rev_all',
        'ach_rev_bb',
        'ach_rev_pv',
        'ach_rgb',
        'ach_revenue_all',
        'ach_broadband',
        'ach_redeem',
        'growth_mom',

        'score_rev_all',
        'score_rev_bb',
        'score_rev_pv',
        'score_rgb',
        'score_omzet',

        'weight_rev_all',
        'weight_rev_bb',
        'weight_rev_pv',
        'weight_rgb',
        'weight_omzet',

        'final_score_rev_all',
        'final_score_rev_bb',
        'final_score_rev_pv',
        'final_score_rgb',
        'final_score_omzet',
        'final_score',
        'total_score',
        'class',
        'status',
        'notes',
    ];

    protected $casts = [
        'period_year' => 'integer',
        'target_rev_all' => 'float',
        'actual_rev_all' => 'float',
        'target_rev_bb' => 'float',
        'actual_rev_bb' => 'float',
        'target_rev_pv' => 'float',
        'actual_rev_pv' => 'float',
        'target_rgb' => 'float',
        'actual_rgb' => 'float',
        'omzet_rev_m1' => 'float',
        'mtd_m1' => 'float',
        'tgt_3_percent' => 'float',
        'mtd' => 'float',
        'growth' => 'float',
        'growth_tgt' => 'float',
        'rev_growth_ach_percent' => 'float',
        'outlet_pjp' => 'float',
        'outlet_pjp_growth' => 'float',
        'ratio_outlet_pjp' => 'float',
        'omzet_outlet_ach' => 'float',
        'ach_rev_all' => 'float',
        'ach_rev_bb' => 'float',
        'ach_rev_pv' => 'float',
        'ach_rgb' => 'float',
        'ach_revenue_all' => 'float',
        'ach_broadband' => 'float',
        'ach_redeem' => 'float',
        'growth_mom' => 'float',
        'score_rev_all' => 'float',
        'score_rev_bb' => 'float',
        'score_rev_pv' => 'float',
        'score_rgb' => 'float',
        'score_omzet' => 'float',
        'weight_rev_all' => 'float',
        'weight_rev_bb' => 'float',
        'weight_rev_pv' => 'float',
        'weight_rgb' => 'float',
        'weight_omzet' => 'float',
        'final_score_rev_all' => 'float',
        'final_score_rev_bb' => 'float',
        'final_score_rev_pv' => 'float',
        'final_score_rgb' => 'float',
        'final_score_omzet' => 'float',
        'final_score' => 'float',
        'total_score' => 'float',
    ];

    /**
     * Resolve Cluster Category Type (VERY HIGH, HIGH, LOW) based on Cluster / City Name
     * Patokan:
     * - VERY HIGH: BALI BARAT, BALI TENGAH, BALI TIMUR, LOMBOK
     * - HIGH: KUPANG ROTE, SUMBAWA BARAT, SUMBAWA TIMUR, SUMBAWA
     * - LOW: ENDE SIKKA, FLORES TIMUR, MALAKA TIMTIM BELU, MANGGARAI, SUMBA
     */
    public static function resolveClusterType(?string $clusterName, ?string $cityName = null): string
    {
        $c = strtoupper(trim($clusterName ?? ''));
        $city = strtoupper(trim($cityName ?? ''));
        $searchStr = $c . ' ' . $city;

        if (str_contains($searchStr, 'ENDE') || str_contains($searchStr, 'FLORES') || str_contains($searchStr, 'MALAKA') || str_contains($searchStr, 'MANGGARAI') || (str_contains($searchStr, 'SUMBA') && !str_contains($searchStr, 'SUMBAWA'))) {
            return 'LOW';
        }

        if (str_contains($searchStr, 'KUPANG') || str_contains($searchStr, 'SUMBAWA')) {
            return 'HIGH';
        }

        if (str_contains($searchStr, 'BALI') || str_contains($searchStr, 'LOMBOK')) {
            return 'VERY HIGH';
        }

        return 'HIGH';
    }

    /**
     * Resolve Branch (DENPASAR, MATARAM, KUPANG, FLORES) based on Cluster / City Name
     */
    public static function resolveBranch(?string $clusterName, ?string $cityName = null): string
    {
        $c = strtoupper(trim($clusterName ?? ''));
        $city = strtoupper(trim($cityName ?? ''));
        $searchStr = $c . ' ' . $city;

        if (str_contains($searchStr, 'BALI')) {
            return 'DENPASAR';
        }

        if (str_contains($searchStr, 'LOMBOK') || str_contains($searchStr, 'SUMBAWA') || str_contains($searchStr, 'MATARAM')) {
            return 'MATARAM';
        }

        if (str_contains($searchStr, 'KUPANG') || str_contains($searchStr, 'MALAKA') || str_contains($searchStr, 'SUMBA') || str_contains($searchStr, 'BELU') || str_contains($searchStr, 'ROTE') || str_contains($searchStr, 'TIMOR')) {
            return 'KUPANG';
        }

        if (str_contains($searchStr, 'ENDE') || str_contains($searchStr, 'FLORES') || str_contains($searchStr, 'MANGGARAI') || str_contains($searchStr, 'SIKKA') || str_contains($searchStr, 'ALOR') || str_contains($searchStr, 'LEMBATA') || str_contains($searchStr, 'NGADA') || str_contains($searchStr, 'NAGEKEO')) {
            return 'FLORES';
        }

        return 'DENPASAR';
    }

    /**
     * Automatic Calculation Engine for 11 Formulas
     */
    public function computeFormulas(): void
    {
        // 1. Sync mtd & actual_rev_all
        if (floatval($this->actual_rev_all) <= 0 && floatval($this->mtd) > 0) {
            $this->actual_rev_all = floatval($this->mtd);
        }
        if (floatval($this->mtd) <= 0 && floatval($this->actual_rev_all) > 0) {
            $this->mtd = floatval($this->actual_rev_all);
        }

        if (floatval($this->actual_rev_all) <= 0 && (floatval($this->actual_rev_bb) > 0 || floatval($this->actual_rev_pv) > 0)) {
            $this->actual_rev_all = floatval($this->actual_rev_bb) + floatval($this->actual_rev_pv);
            $this->mtd = $this->actual_rev_all;
        }
        if (floatval($this->target_rev_all) <= 0 && (floatval($this->target_rev_bb) > 0 || floatval($this->target_rev_pv) > 0)) {
            $this->target_rev_all = floatval($this->target_rev_bb) + floatval($this->target_rev_pv);
        }

        // 2. Cluster Type & Weight Determination
        $typeUpper = self::resolveClusterType($this->cluster, $this->city);
        $this->type = $typeUpper;

        // Weights:
        // Rev ALL: 15%, Rev RGB: 20%, Omzet Outlet: 25% for ALL cluster types
        // Rev Broadband: VERY HIGH & HIGH = 15%, LOW = 20%
        // Rev Redeem PV: VERY HIGH & HIGH = 25%, LOW = 20%
        $this->weight_rev_all = 15.0;
        $this->weight_rgb = 20.0;
        $this->weight_omzet = 25.0;

        if ($typeUpper === 'LOW') {
            $this->weight_rev_bb = 20.0;
            $this->weight_rev_pv = 20.0;
        } else {
            $this->weight_rev_bb = 15.0;
            $this->weight_rev_pv = 25.0;
        }

        // 3. Formula 1: ach = actual / target * 100
        $this->ach_rev_all = $this->target_rev_all > 0 ? round(($this->actual_rev_all / $this->target_rev_all) * 100, 2) : 0;
        $this->ach_rev_bb = $this->target_rev_bb > 0 ? round(($this->actual_rev_bb / $this->target_rev_bb) * 100, 2) : 0;
        $this->ach_rev_pv = $this->target_rev_pv > 0 ? round(($this->actual_rev_pv / $this->target_rev_pv) * 100, 2) : 0;
        $this->ach_rgb = $this->target_rgb > 0 ? round(($this->actual_rgb / $this->target_rgb) * 100, 2) : 0;

        $this->ach_revenue_all = $this->ach_rev_all;
        $this->ach_broadband = $this->ach_rev_bb;
        $this->ach_redeem = $this->ach_rev_pv;

        // 4. Formula 2: score = IF(ach<=85%; 1; IF(ach<=95%; 2; 3))
        $calculateScore = function ($ach) {
            if ($ach <= 85.0) return 1.0;
            if ($ach <= 95.0) return 2.0;
            return 3.0;
        };

        $this->score_rev_all = $calculateScore($this->ach_rev_all);
        $this->score_rev_bb = $calculateScore($this->ach_rev_bb);
        $this->score_rev_pv = $calculateScore($this->ach_rev_pv);
        $this->score_rgb = $calculateScore($this->ach_rgb);

        // 5. Formula 3: final score = score * weight
        $this->final_score_rev_all = round($this->score_rev_all * ($this->weight_rev_all / 100), 2);
        $this->final_score_rev_bb = round($this->score_rev_bb * ($this->weight_rev_bb / 100), 2);
        $this->final_score_rev_pv = round($this->score_rev_pv * ($this->weight_rev_pv / 100), 2);
        $this->final_score_rgb = round($this->score_rgb * ($this->weight_rgb / 100), 2);

        // 6. Formula 4: tgt 3 percent = omzet rev m1 * 103%
        $lastMonth = floatval($this->omzet_rev_m1) ?: floatval($this->mtd_m1);
        if ($lastMonth > 0) {
            $this->tgt_3_percent = round($lastMonth * 1.03, 2);
        }

        // 7. Formula 5: growth % = (Omzet MTD / Omzet MTD M1) - 1
        if ($lastMonth > 0 && $this->mtd > 0) {
            $this->growth = round((($this->mtd / $lastMonth) - 1) * 100, 2);
            $this->growth_mom = $this->growth;
        }

        // 8. Formula 6: Growth TGT % = 3%
        $this->growth_tgt = 3.0;

        // 9. Formula 7: Rev Growth Ach% = IF((growth%/growth TGT%)<=0%; 0%; IF((growth%/growth TGT%)>100%; 100%; (growth%/growth TGT%)))
        $growthRatio = ($this->growth_tgt > 0) ? ($this->growth / $this->growth_tgt) * 100 : 0;
        if ($growthRatio <= 0) {
            $this->rev_growth_ach_percent = 0.0;
        } elseif ($growthRatio >= 100) {
            $this->rev_growth_ach_percent = 100.0;
        } else {
            $this->rev_growth_ach_percent = round($growthRatio, 2);
        }

        // 10. Formula 8: Ratio Outlet PJP = Outlet PJP Growth / Outlet PJP
        if ($this->outlet_pjp > 0) {
            if ($this->outlet_pjp_growth > 0 && $this->outlet_pjp_growth <= 100) {
                $this->ratio_outlet_pjp = round($this->outlet_pjp_growth, 2);
            } else {
                $this->ratio_outlet_pjp = round(($this->outlet_pjp_growth / $this->outlet_pjp) * 100, 2);
            }
        }

        // 11. Formula 9: Omzet outlet ach% = (Ratio Outlet PJP + Rev Growth Ach%) / 2
        $this->omzet_outlet_ach = round(($this->ratio_outlet_pjp + $this->rev_growth_ach_percent) / 2, 2);

        // 12. Formula 10: Omzet outlet ach (Score) = IF(Omzet outlet ach%<=85%; 1; IF(Omzet outlet ach%<=95%; 2; 3))
        $this->score_omzet = $calculateScore($this->omzet_outlet_ach);

        // 13. Formula 11: final score omzet = omzet outlet ach * weight (25%)
        $this->final_score_omzet = round($this->score_omzet * ($this->weight_omzet / 100), 2);

        // 14. Total Final Score = sum of all 5 final scores
        $calculatedTotalScore = round(
            $this->final_score_rev_all +
            $this->final_score_rev_bb +
            $this->final_score_rev_pv +
            $this->final_score_rgb +
            $this->final_score_omzet,
            2
        );

        $this->final_score = $calculatedTotalScore;
        $this->total_score = $calculatedTotalScore;

        // 15. Class Assignment
        if ($calculatedTotalScore > 2.80) {
            $this->class = 'PLATINUM';
        } elseif ($calculatedTotalScore > 2.40) {
            $this->class = 'GOLD';
        } elseif ($calculatedTotalScore > 2.00) {
            $this->class = 'SILVER';
        } elseif ($calculatedTotalScore >= 1.60) {
            $this->class = 'BRONZE';
        } else {
            $this->class = 'BLACK';
        }

        // Status & Notes
        $status = self::determineStatusFromMom($this->growth_mom);
        $this->status = $status;
        if (empty($this->notes)) {
            $this->notes = $status;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->computeFormulas();
        });
    }

    /**
     * Alias Accessors & Mutators for backward compatibility
     */
    public function getKabupatenAttribute(): string
    {
        return $this->attributes['city'] ?? '';
    }

    public function setKabupatenAttribute($value)
    {
        $this->attributes['city'] = $value;
    }

    public function getClusterNameAttribute(): string
    {
        return $this->attributes['cluster'] ?? '';
    }

    public function setClusterNameAttribute($value)
    {
        $this->attributes['cluster'] = $value;
    }

    public function getMtdRevenueAllAttribute()
    {
        return $this->attributes['actual_rev_all'] ?? 0;
    }

    public function setMtdRevenueAllAttribute($value)
    {
        $this->attributes['actual_rev_all'] = $value;
    }

    public function getTargetRevenueAllAttribute()
    {
        return $this->attributes['target_rev_all'] ?? 0;
    }

    public function setTargetRevenueAllAttribute($value)
    {
        $this->attributes['target_rev_all'] = $value;
    }

    public function getMtdBroadbandAttribute()
    {
        return $this->attributes['actual_rev_bb'] ?? 0;
    }

    public function setMtdBroadbandAttribute($value)
    {
        $this->attributes['actual_rev_bb'] = $value;
    }

    public function getTargetBroadbandAttribute()
    {
        return $this->attributes['target_rev_bb'] ?? 0;
    }

    public function setTargetBroadbandAttribute($value)
    {
        $this->attributes['target_rev_bb'] = $value;
    }

    public function getMtdRedeemAttribute()
    {
        return $this->attributes['actual_rev_pv'] ?? 0;
    }

    public function setMtdRedeemAttribute($value)
    {
        $this->attributes['actual_rev_pv'] = $value;
    }

    public function getTargetRedeemAttribute()
    {
        return $this->attributes['target_rev_pv'] ?? 0;
    }

    public function setTargetRedeemAttribute($value)
    {
        $this->attributes['target_rev_pv'] = $value;
    }

    public function getMtdRgbAttribute()
    {
        return $this->attributes['actual_rgb'] ?? 0;
    }

    public function setMtdRgbAttribute($value)
    {
        $this->attributes['actual_rgb'] = $value;
    }

    public function getRevenueLastMonthAttribute()
    {
        return $this->attributes['omzet_rev_m1'] ?? $this->attributes['mtd_m1'] ?? 0;
    }

    public function getRevenueCurrentMonthAttribute()
    {
        return $this->attributes['mtd'] ?? $this->attributes['actual_rev_all'] ?? 0;
    }

    public function getCatatanAttribute(): string
    {
        return self::determineStatusFromMom($this->growth_mom ?? 0);
    }

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
}
