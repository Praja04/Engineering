<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmryKeseragamanSkalaModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_tmb_keseragaman_skala_smry';

    protected $fillable = [
        'kalibrasi_id',
        'beban',
        'avg_z',
        'avg_m',
        'selisih_zm',
        'standar_massa',
        'koreksi_skala',
        'absolut_koreksi',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
