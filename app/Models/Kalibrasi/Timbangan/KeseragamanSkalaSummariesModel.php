<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KeseragamanSkalaSummariesModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_keseragaman_skala_summaries';

    protected $fillable = [
        'kalibrasi_id',
        'massa_ke',
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
