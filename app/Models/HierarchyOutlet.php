<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HierarchyOutlet extends Model
{
    use HasFactory;

    protected $table = 'hierarchy_outlets';

    protected $fillable = [
        'kabupaten',
        'cluster',
        'mitra',
        'branch',
        'jumlah_outlet',
        'manager_branch',
        'group_class',
        'badge_color',
    ];
}
