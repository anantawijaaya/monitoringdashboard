<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CultureProgramAllocation extends Model
{
    use HasFactory;

    protected $table = 'culture_program_allocations';

    protected $fillable = [
        'cluster',
        'mitra',
        'total_budget',
        'culture_program_budget',
    ];
}
