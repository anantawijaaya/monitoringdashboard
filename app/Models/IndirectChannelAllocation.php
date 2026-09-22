<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndirectChannelAllocation extends Model
{
    use HasFactory;

    protected $table = 'indirect_channel_allocations';

    protected $fillable = [
        'cluster',
        'mitra',
        'total_budget',
        'digital_marketing_budget',
        'cvm_program_budget',
        'engagement_outlet_budget',
        'branding_outlet_budget',
        'program_sales_outlet_budget',
        'voucher_games_budget',
    ];
}
