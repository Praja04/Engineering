<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KpiModel extends Model
{
    use HasFactory;

    protected $table = 'kpi';

    protected $fillable = [
        'periode_tipe',
        'week',
        'start_date',
        'end_date',
        'month',
        'finish_goods',
        'kecap_matang',
    ];
}
