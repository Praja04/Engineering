<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HisterisisModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_timbangan_histerisis';

    protected $fillable = [
        'kalibrasi_id',
        'pembacaan_terkecil',
        'setengah_kapasitas',
        'percobaan',
        'z1',
        'm1',
        'm_m',
        'm2',
        'z2',
        'm1_m2',
        'z1_z2',
    ];


    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
