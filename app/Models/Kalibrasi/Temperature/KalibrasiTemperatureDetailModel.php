<?php

namespace App\Models\Kalibrasi\Temperature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiTemperatureDetailModel extends Model
{
    use HasFactory;

    protected $table = 'cal_temperature_detail';

    protected $fillable = [
        'temperature_id',
        'penunjuk_standar',
        'penunjuk_alat',
        'koreksi_standar',
        'suhu_standar',
        'koreksi_alat',
    ];

    /**
     * Relasi ke tabel kalibrasi
     */
    public function temperature()
    {
        return $this->belongsTo(KalibrasiTemperatureModel::class, 'temperature_id');
    }
}
