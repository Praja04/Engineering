<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class WwtpPerformancePHharian extends Model
{
    //
    //
    use HasFactory;

    protected $table = 'wwtp_performance_ph_harian';

    protected $fillable = [
        'tanggal',
        'shift',
        'equalisasi_1',
        'equalisasi_2',
        'netralisasi',
        'sedimentasi_1',
        'sedimentasi_2',
        'outlet_anaerob',
        'aerob',
        'lumpur_aktif',
        'clarifier_2',
        'outlet',

    ];
}
