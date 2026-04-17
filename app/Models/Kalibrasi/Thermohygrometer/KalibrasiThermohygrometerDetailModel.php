<?php

namespace App\Models\Kalibrasi\Thermohygrometer;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiThermohygrometerDetailModel extends Model
{
    use HasFactory;

    protected $table = 'cal_thermohygrometer_detail';

    protected $fillable = [
        'thermohygro_id',
        'urutan',
        'penunjuk_standar_suhu',
        'penunjuk_alat_suhu',
        'penunjuk_standar_rh',
        'penunjuk_alat_rh',
        'koreksi_standar_suhu',
        'koreksi_standar_rh',
        'tekanan_standar_suhu',
        'tekanan_standar_rh',
        'koreksi_alat_suhu',
        'koreksi_alat_rh',
    ];

    public function thermohygrometer()
    {
        return $this->belongsTo(KalibrasiModel::class, 'thermohygro_id');
    }
}
