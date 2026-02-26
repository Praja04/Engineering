<?php

namespace App\Models\Kalibrasi\Volumetrik;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiVolumetrikModel extends Model
{
    use HasFactory;

    protected $table = 'cal_volumetrik';

    protected $fillable = [
        'kalibrasi_id',
        'titik_kalibrasi',
        'avg_penunjuk_standar',
        'avg_koreksi',
        'stdev_penunjuk_standar',
        'akar_10',
        'u_timbangan',
        'u_total',
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
        return $this->hasMany(KalibrasiVolumetrikDetailModel::class, 'volumetrik_id');
    }
}
