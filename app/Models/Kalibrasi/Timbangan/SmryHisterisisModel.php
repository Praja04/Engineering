<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmryHisterisisModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_tmb_histerisis_smry';

    protected $fillable = [
        'kalibrasi_id',
        'pembacaan_terkecil',
        'setengah_kapasitas',
        'avg_m1m2',
        'avg_z1z2',
        'avg_mz',
        'histerisis',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
