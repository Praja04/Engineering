<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class EspOperationalReport extends Model
{
    //
    protected $table = 'esp_operational_reports';

    protected $fillable = [
        'tanggal_laporan',
        'jam_laporan',
        'grup',
        'arus_primer',
        'arus_sekunder',
        'tegangan_primer',
        'tegangan_sekunder',
        'suhu_thermal',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
        // 'jam_laporan' => 'datetime:H:i',
    ];
}
