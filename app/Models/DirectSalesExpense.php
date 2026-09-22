<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectSalesExpense extends Model
{
    use HasFactory;

    protected $table = 'direct_sales_expenses';

    protected $fillable = [
        'cluster',
        'mitra',
        'program_name',
        'tanggal',
        'waktu',
        'deskripsi',
        'note',
        'budget_program',
        'nominal_pengeluaran',
        'sisa_budget_program',
        'evidence_path',
        'evidence_original_name',
        'status',
    ];
}
