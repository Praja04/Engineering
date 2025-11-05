<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KpiModel extends Model
{
    use HasFactory;

    protected $table = 'kpi';

    protected $fillable = [
        'jenis_input',
        'tanggal',
        'fg',
        'kecap_matang',
    ];
}
