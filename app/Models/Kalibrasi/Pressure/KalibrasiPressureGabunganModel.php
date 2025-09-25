<?php

namespace App\Models\Kalibrasi\Pressure;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiPressureGabunganModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_pressure_gabungan';

    protected $fillable = [
        'kalibrasi_id',
        'titik_kalibrasi',

        'avg_penunjuk_alat_naik',
        'avg_penunjuk_alat_turun',

        'avg_tekanan_standar_naik',
        'avg_tekanan_standar_turun',

        'avg_kor_alat_naik',
        'avg_kor_alat_turun',

        'std_deviasi_naik',
        'std_deviasi_turun',

        'ketidak_pastian_naik',
        'ketidak_pastian_turun',

        'u_naik',
        'u_turun',
        'u_naik_kuadrat',
        'u_turun_kuadrat',
        'u_gabungan',
    ];


    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
