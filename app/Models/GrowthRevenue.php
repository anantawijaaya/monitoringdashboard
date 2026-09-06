<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrowthRevenue extends Model
{
    use HasFactory;

    protected $table = 'growth_revenues';

    protected $guarded = ['id'];

    protected $casts = [
        'revenue_last_month' => 'float',
        'revenue_current_month' => 'float',
        'growth_mom' => 'float',
        'period_year' => 'integer',
    ];

    /**
     * Auto-calculate growth_mom upon saving
     */
    protected static function booted(): void
    {
        static::saving(function ($model) {
            $last = floatval($model->revenue_last_month);
            $curr = floatval($model->revenue_current_month);

            if ($last > 0) {
                $model->growth_mom = round((($curr - $last) / $last) * 100, 2);
            } else {
                $model->growth_mom = 0.0;
            }
        });
    }

    public function getFormattedLastMonthAttribute(): string
    {
        $val = floatval($this->revenue_last_month);
        if ($val >= 1000000000) {
            return 'Rp ' . number_format($val / 1000000000, 2, ',', '.') . ' M';
        }
        return 'Rp ' . number_format($val, 0, ',', '.');
    }

    public function getFormattedCurrentMonthAttribute(): string
    {
        $val = floatval($this->revenue_current_month);
        if ($val >= 1000000000) {
            return 'Rp ' . number_format($val / 1000000000, 2, ',', '.') . ' M';
        }
        return 'Rp ' . number_format($val, 0, ',', '.');
    }

    public function getFormattedGrowthMomAttribute(): string
    {
        $val = floatval($this->growth_mom);
        return ($val >= 0 ? '+' : '') . number_format($val, 1, ',', '.') . '%';
    }
}
