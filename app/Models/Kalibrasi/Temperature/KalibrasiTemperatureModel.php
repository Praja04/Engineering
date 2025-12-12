<?php

namespace App\Models\kalibrasi\Temperature;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiTemperatureModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_temperature';

    protected $fillable = [
        'kalibrasi_id',
        'titik_kalibrasi',
        'penunjuk_standar',
        'penunjuk_alat',
        'koreksi_standar',
        'suhu_standar',
        'koreksi_alat',
    ];

    /**
     * Relasi ke tabel kalibrasi
     */
    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
