<?php

namespace App\Models\kalibrasi\Temperature;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiTemperatureGabModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_temperature_gab';

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
}
