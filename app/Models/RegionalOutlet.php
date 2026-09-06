<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionalOutlet extends Model
{
    use HasFactory;

    protected $table = 'regional_outlets';

    protected $fillable = [
        'id_outlet',
        'longitude',
        'latitude',
        'kabupaten',
        'cluster',
        'branch',
        'total_omzet',
        'flag_omzet',
    ];

    protected $casts = [
        'longitude' => 'float',
        'latitude' => 'float',
        'total_omzet' => 'float',
        'flag_omzet' => 'float',
    ];

    /**
     * Get color category for the outlet based on flag_omzet
     * Rules:
     * - < 0%  : Black (Hitam)
     * - == 0% : Red (Merah)
     * - <= 3% : Orange (Orange)
     * - > 3%  : Green (Hijau)
     */
    public function getFlagColorAttribute(): string
    {
        $val = (float) $this->flag_omzet;
        if ($val < 0) {
            return 'black';
        } elseif ($val == 0.0) {
            return 'red';
        } elseif ($val <= 3.0) {
            return 'orange';
        } else {
            return 'green';
        }
    }

    /**
     * Get Hex color code for SVG and canvas rendering
     */
    public function getFlagColorHexAttribute(): string
    {
        return match ($this->flag_color) {
            'black' => '#18181B',
            'red' => '#ED1C24',
            'orange' => '#F97316',
            'green' => '#10B981',
            default => '#6B7280',
        };
    }

    /**
     * Get Human-readable status label
     */
    public function getFlagStatusLabelAttribute(): string
    {
        return match ($this->flag_color) {
            'black' => 'Flag < 0%',
            'red' => 'Flag = 0%',
            'orange' => 'Flag <= 3%',
            'green' => 'Flag > 3%',
            default => 'Unknown',
        };
    }

    /**
     * Get Tailwind CSS classes for badge styling
     */
    public function getFlagBadgeClassAttribute(): string
    {
        return match ($this->flag_color) {
            'black' => 'bg-gray-900 text-white border border-gray-700 shadow-sm',
            'red' => 'bg-red-50 text-red-700 border border-red-200',
            'orange' => 'bg-orange-50 text-orange-800 border border-orange-300',
            'green' => 'bg-emerald-50 text-emerald-700 border border-emerald-300',
            default => 'bg-gray-100 text-gray-700 border border-gray-200',
        };
    }

    /**
     * Formatted Indonesian Rupiah Omzet
     */
    public function getFormattedOmzetAttribute(): string
    {
        return 'Rp ' . number_format($this->total_omzet, 0, ',', '.');
    }

    /**
     * Formatted Flag Omzet with sign and percentage
     */
    public function getFormattedFlagAttribute(): string
    {
        $prefix = $this->flag_omzet > 0 ? '+' : '';
        return $prefix . number_format($this->flag_omzet, 2, ',', '.') . '%';
    }
}
