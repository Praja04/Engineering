<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class EspCoalHandover extends Model
{
    protected $table = 'esp_coal_handovers';

    protected $fillable = [
        'tanggal_laporan',
        'penyuplai_qty',
        'penyuplai_nik_nama',
        'penerima_qty',
        'penerima_nik_nama',
        'operator_id',
    ];

    public function operator()
    {
        return $this->belongsTo(\App\Models\User::class, 'operator_id');
    }
}
