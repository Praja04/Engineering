<?php

namespace App\Models\Kalibrasi\Thermohygrometer;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiThermohygrometerModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_thermohygrometer';

    protected $fillable = [
        'kalibrasi_id',
        'titik_kalibrasi',
        'posisi',
        'tipe_hitung',
        'penunjuk_standar',
        'penunjuk_alat',
        'koreksi_standar',
        'tekanan_standar',
        'koreksi_alat',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
