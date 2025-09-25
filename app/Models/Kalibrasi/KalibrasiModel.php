<?php

namespace App\Models\Kalibrasi;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Kalibrasi\Pressure\KalibrasiPressureModel;
use App\Models\Kalibrasi\Pressure\KalibrasiPressureGabunganModel;

class KalibrasiModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi';

    protected $fillable = [
        'alat_id',
        'user_id',
        'lokasi_kalibrasi',
        'suhu_ruangan',
        'kelembaban',
        'tgl_kalibrasi',
        'tgl_kalibrasi_ulang',
        'metode_kalibrasi',
        'jenis_kalibrasi',
    ];

    public function alat()
    {
        return $this->belongsTo(AlatKalibrasiModel::class, 'alat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pressureDetails()
    {
        return $this->hasMany(KalibrasiPressureModel::class, 'kalibrasi_id');
    }

    public function pressureSummary()
    {
        return $this->hasOne(KalibrasiPressureGabunganModel::class, 'kalibrasi_id');
    }
}
