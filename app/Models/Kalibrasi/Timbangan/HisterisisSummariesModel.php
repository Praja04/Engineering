<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HisterisisSummariesModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_histerisis_summaries';

    protected $fillable = [
        'kalibrasi_id',
        'pembacaan_terkecil',
        'setengah_kapasitas',
        'avg_m1m2',
        'avg_z1z2',
        'nilai_mz',
        'histerisis',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
