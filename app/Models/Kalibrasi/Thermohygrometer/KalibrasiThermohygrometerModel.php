<?php

namespace App\Models\Kalibrasi\Thermohygrometer;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiThermohygrometerModel extends Model
{
    use HasFactory;

    protected $table = 'cal_thermohygrometer';

    protected $fillable = [
        'kalibrasi_id',
        'titik_kalibrasi',
        'posisi',

        'avg_penunjuk_alat_suhu',
        'avg_tekanan_standar_suhu',
        'avg_koreksi_suhu',
        'std_deviasi_suhu',
        'ketidak_pastian_suhu',

        'avg_penunjuk_alat_rh',
        'avg_tekanan_standar_rh',
        'avg_koreksi_rh',
        'std_deviasi_rh',
        'ketidak_pastian_rh',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }

    public function details()
    {
        return $this->hasMany(KalibrasiThermohygrometerDetailModel::class, 'thermohygro_id');
    }
}
