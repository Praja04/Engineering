<?php

namespace App\Models\Kalibrasi\Pressure;

use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\Pressure\KalibrasiPressureDetailModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KalibrasiPressureModel extends Model
{
    use HasFactory;

    protected $table = 'cal_pressure';

    protected $fillable = [
        'kalibrasi_id',
        'titik_kalibrasi',
        'avg_penunjuk_alat_naik',
        'avg_penunjuk_alat_turun',
        'avg_tekanan_standar_naik',
        'avg_tekanan_standar_turun',
        'avg_koreksi_alat_naik',
        'avg_koreksi_alat_turun',
        'std_deviasi_naik',
        'std_deviasi_turun',
        'ketidakpastian_naik',
        'ketidakpastian_turun',
        'u_naik',
        'u_turun',
        'u_naik_kuadrat',
        'u_turun_kuadrat',
        'u_gabungan'
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }

    public function details()
    {
        return $this->hasMany(KalibrasiPressureDetailModel::class, 'pressure_id');
    }
}
