<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectSalesAllocation extends Model
{
    use HasFactory;

    protected $table = 'direct_sales_allocations';

    protected $fillable = [
        'cluster',
        'mitra',
        'total_budget',
        'direct_selling_budget',
        'grebek_poi_budget',
        'dls_skulid_budget',
    ];
}
