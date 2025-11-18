<?php

namespace App\Models\Kalibrasi\Thermohygrometer;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiThermohygrometerGabModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_thermohygrometer_gab';

    protected $fillable = [
        'kalibrasi_id',
        'titik_kalibrasi',
        'posisi',

        'avg_penunjuk_alat_suhu',
        'avg_tekanan_standar_suhu',
        'avg_kor_alat_suhu',
        'std_deviasi_suhu',
        'ketidak_pastian_suhu',

        'avg_penunjuk_alat_rh',
        'avg_tekanan_standar_rh',
        'avg_kor_alat_rh',
        'std_deviasi_rh',
        'ketidak_pastian_rh',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
