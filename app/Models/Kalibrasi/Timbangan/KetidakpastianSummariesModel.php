<?php

namespace App\Models\Kalibrasi\Timbangan;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetidakpastianSummariesModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_ketidakpastian_summaries';

    protected $fillable = [
        'kalibrasi_id',
        'kapasitas_alat',
        'pembacaan_terkecil',
        'timbangan_standar',
        'skala_terkecil',
        'max_kemampuan_ulang',
        'drift',
        'bouyancy',
        'ketidakpastian_gabungan',
        'ketidakpastian_perluas',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
