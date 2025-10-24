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
        'jenis_kalibrasi',
        'status_save'
    ];

    public function alat()
    {
        return $this->belongsTo(AlatKalibrasiModel::class, 'alat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getRelasiByJenis(): array
    {
        return match (strtolower($this->jenis_kalibrasi)) {
            'pressure' => ['pressure', 'pressureGabungan'],
            'temperature' => ['temperature', 'temperatureGabungan'],
            'volumetric' => ['volumetric', 'volumetricGabungan'],
            // tambahkan jenis lain di sini
            'mass' => ['mass', 'massGabungan'],
            'electrical' => ['electrical', 'electricalGabungan'],
            default => [], // fallback kalau belum diatur
        };
    }

    public function pressure()
    {
        return $this->hasMany(KalibrasiPressureModel::class, 'kalibrasi_id');
    }

    public function pressureGabungan()
    {
        return $this->hasMany(KalibrasiPressureGabunganModel::class, 'kalibrasi_id');
    }

    public function certificate()
    {
        return $this->hasOne(KalibrasiSertifikatModel::class, 'kalibrasi_id');
    }
}
