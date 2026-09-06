<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasKpi extends Model
{
    use HasFactory;

    protected $table = 'kelas_kpis';

    protected $fillable = [
        'region',
        'cluster',
        'new_cluster',
        'periode',
        'mitra',
        'type',

        // 23 Image KPI Columns
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
        'weight',
        'omzet_outlet_ach_score',
        'final_score',

        // Breakdown Metrics & Scores
        'ach_rev_all',
        'ach_rev_bb',
        'ach_rev_pv',
        'ach_rgb',
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

        // Legacy / Backward Compatibility
        'revenue_all',
        'achieved',
        'ach_percent',
        'score',
        'total_score',
        'class',
    ];

    /**
     * Accessor for cluster name (supports both cluster and new_cluster attribute).
     */
    public function getClusterNameAttribute(): string
    {
        return $this->cluster ?? $this->new_cluster ?? '';
    }

    /**
     * Get official Mitra SBP distributor name mapped from cluster.
     */
    public function getMitraAttribute(): string
    {
        if (!empty($this->attributes['mitra'])) {
            return $this->attributes['mitra'];
        }

        $c = strtoupper($this->cluster ?? $this->new_cluster ?? '');
        if (str_contains($c, 'BALI BARAT')) return 'PT AKAR DAYA';
        if (str_contains($c, 'BALI TENGAH')) return 'PT. CATALIST INTEGRA PRIMA SUKSES';
        if (str_contains($c, 'BALI TIMUR')) return 'PT AKAR DAYA';
        if (str_contains($c, 'ENDE')) return 'CV. RAJAWALI CELLULAR';
        if (str_contains($c, 'FLORES')) return 'CV. RAJAWALI CELLULAR';
        if (str_contains($c, 'KUPANG')) return 'PT. NARINDO SOLUSI TELEKOMUNIKASI';
        if (str_contains($c, 'MALAKA')) return 'PT. NARINDO SOLUSI TELEKOMUNIKASI';
        if (str_contains($c, 'MANGGARAI')) return 'CV. RAJAWALI CELLULAR';
        if (str_contains($c, 'SUMBAWA BARAT')) return 'PT BERKAH KARUNIA KREASI';
        if (str_contains($c, 'SUMBAWA TIMUR')) return 'PT KINARYA SELARAS SOLUSI';
        if (str_contains($c, 'SUMBAWA')) return 'PT BERKAH KARUNIA KREASI';
        if (str_contains($c, 'SUMBA')) return 'CV. RAJAWALI CELLULAR';
        if (str_contains($c, 'LOMBOK')) return 'PT AKAR DAYA';

        return 'TELKOMSEL REGIONAL BALI NUSRA';
    }

    /**
     * Automated Calculation Engine for 11 KPI Formulas
     */
    public function computeKpiFormulas(): void
    {
        // 1. Sync cluster & new_cluster & auto-detect cluster type if empty
        if (empty($this->cluster) && !empty($this->new_cluster)) {
            $this->cluster = $this->new_cluster;
        } elseif (empty($this->new_cluster) && !empty($this->cluster)) {
            $this->new_cluster = $this->cluster;
        }

        if (empty($this->periode)) {
            $this->periode = '2026-08';
        }

        $cName = strtoupper(trim($this->cluster ?? $this->new_cluster ?? ''));

        // Always resolve official cluster type mapping based on cluster name
        if (str_contains($cName, 'ENDE') || str_contains($cName, 'FLORES') || str_contains($cName, 'MALAKA') || str_contains($cName, 'MANGGARAI') || (str_contains($cName, 'SUMBA') && !str_contains($cName, 'SUMBAWA'))) {
            $typeUpper = 'LOW';
        } elseif (str_contains($cName, 'KUPANG') || str_contains($cName, 'SUMBAWA')) {
            $typeUpper = 'HIGH';
        } elseif (str_contains($cName, 'BALI') || str_contains($cName, 'LOMBOK')) {
            $typeUpper = 'VERY HIGH';
        } else {
            $typeUpper = strtoupper(trim($this->type ?? ''));
            if (!in_array($typeUpper, ['VERY HIGH', 'HIGH', 'LOW'])) {
                $typeUpper = 'HIGH';
            }
        }
        $this->type = $typeUpper;

        // 2. Weights Definition based on Cluster Type (Rule update)
        // ALL: 15%, RGB: 20%, Omzet: 25% for all cluster types
        // Broadband: VERY HIGH & HIGH = 15%, LOW = 20%
        // Redeem PV: VERY HIGH & HIGH = 25%, LOW = 20%
        $this->weight_rev_all = 15.0;
        $this->weight_rgb = 20.0;
        $this->weight_omzet = 25.0;
        $this->weight = 15.0;

        if ($typeUpper === 'LOW') {
            $this->weight_rev_bb = 20.0;
            $this->weight_rev_pv = 20.0;
        } else {
            // VERY HIGH & HIGH
            $this->weight_rev_bb = 15.0;
            $this->weight_rev_pv = 25.0;
        }

        // 3. Formula 1: Achievement % (ach%) for each revenue category
        $this->ach_rev_all = $this->target_rev_all > 0 ? round(($this->actual_rev_all / $this->target_rev_all) * 100, 2) : 0;
        $this->ach_rev_bb = $this->target_rev_bb > 0 ? round(($this->actual_rev_bb / $this->target_rev_bb) * 100, 2) : 0;
        $this->ach_rev_pv = $this->target_rev_pv > 0 ? round(($this->actual_rev_pv / $this->target_rev_pv) * 100, 2) : 0;
        $this->ach_rgb = $this->target_rgb > 0 ? round(($this->actual_rgb / $this->target_rgb) * 100, 2) : 0;

        // 4. Formula 2: Score rev all, rev BB, rev redeem PV, rev RGB
        $calculateScore = function ($ach) {
            if ($ach <= 85.0) return 1.0;
            if ($ach <= 95.0) return 2.0;
            return 3.0;
        };

        $this->score_rev_all = $calculateScore($this->ach_rev_all);
        $this->score_rev_bb = $calculateScore($this->ach_rev_bb);
        $this->score_rev_pv = $calculateScore($this->ach_rev_pv);
        $this->score_rgb = $calculateScore($this->ach_rgb);

        // 5. Formula 3: Final score per category = score * weight
        $this->final_score_rev_all = round($this->score_rev_all * ($this->weight_rev_all / 100), 2);
        $this->final_score_rev_bb = round($this->score_rev_bb * ($this->weight_rev_bb / 100), 2);
        $this->final_score_rev_pv = round($this->score_rev_pv * ($this->weight_rev_pv / 100), 2);
        $this->final_score_rgb = round($this->score_rgb * ($this->weight_rgb / 100), 2);

        // 6. Formula 4: Target 3% (tgt_3_percent = omzet_rev_m1 * 103%)
        if ($this->omzet_rev_m1 > 0) {
            $this->tgt_3_percent = round($this->omzet_rev_m1 * 1.03, 2);
        }

        // 7. Formula 5: Growth % = (Omzet MTD / Omzet MTD M-1) - 1
        if ($this->mtd_m1 > 0 && $this->mtd > 0) {
            $this->growth = round((($this->mtd / $this->mtd_m1) - 1) * 100, 2);
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

        // 12. Formula 10: Omzet outlet ach (Score) = IF(Omzet outlet ach% <= 85%; 1; IF(Omzet outlet ach% <= 95%; 2; 3))
        $this->score_omzet = $calculateScore($this->omzet_outlet_ach);
        $this->omzet_outlet_ach_score = $this->score_omzet;

        // 13. Formula 11: Final Score Omzet = omzet outlet ach score * weight (25%)
        $this->final_score_omzet = round($this->score_omzet * ($this->weight_omzet / 100), 2);

        // 14. Total Final Score = sum of all 5 final scores
        $totalCalculatedFinalScore = round(
            $this->final_score_rev_all +
            $this->final_score_rev_bb +
            $this->final_score_rev_pv +
            $this->final_score_rgb +
            $this->final_score_omzet,
            2
        );

        $this->final_score = $totalCalculatedFinalScore;
        $this->total_score = $totalCalculatedFinalScore;

        // 15. Class assignment based on Total Final Score (5 Official Levels)
        // < 1.6: Black | 1.6 - 2.0: Bronze | > 2.0 - 2.4: Silver | > 2.4 - 2.8: Gold | > 2.8: Platinum
        if ($totalCalculatedFinalScore > 2.80) {
            $this->class = 'PLATINUM';
        } elseif ($totalCalculatedFinalScore > 2.40) {
            $this->class = 'GOLD';
        } elseif ($totalCalculatedFinalScore > 2.00) {
            $this->class = 'SILVER';
        } elseif ($totalCalculatedFinalScore >= 1.60) {
            $this->class = 'BRONZE';
        } else {
            $this->class = 'BLACK';
        }

        // Legacy fields sync
        $this->revenue_all = $this->target_rev_all > 0 ? $this->target_rev_all : $this->revenue_all;
        $this->achieved = $this->actual_rev_all > 0 ? $this->actual_rev_all : $this->achieved;
        $this->ach_percent = $this->ach_rev_all > 0 ? $this->ach_rev_all : $this->ach_percent;
        $this->score = $this->score_rev_all;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->computeKpiFormulas();
        });
    }
}
