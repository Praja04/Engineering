<?php

namespace App\Models\Kalibrasi\Temperature;

use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\Temperature\KalibrasiTemperatureDetailModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KalibrasiTemperatureModel extends Model
{
    use HasFactory;

    protected $table = 'cal_temperature';

    protected $fillable = [
        'kalibrasi_id',
        'titik_kalibrasi',
        'avg_penunjuk_alat',
        'avg_suhu_standar',
        'avg_kor_alat',
        'stdev',
        'ketidakpastian',
    ];

    /**
     * Relasi ke tabel kalibrasi
     */
    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }

    public function details()
    {
        return $this->hasMany(KalibrasiTemperatureDetailModel::class, 'temperature_id');
    }
}
