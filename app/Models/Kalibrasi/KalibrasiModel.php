<?php

namespace App\Models\Kalibrasi;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\Timbangan\TareModel;
use App\Models\Kalibrasi\Timbangan\PingganModel;
use App\Models\Kalibrasi\Timbangan\SmryTareModel;
use App\Models\Kalibrasi\Timbangan\PembacaanModel;
use App\Models\Kalibrasi\Timbangan\HisterisisModel;
use App\Models\Kalibrasi\Timbangan\SmryPingganModel;
use App\Models\Kalibrasi\Timbangan\SmryPembacaanModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Kalibrasi\Timbangan\SmryHisterisisModel;
use App\Models\Kalibrasi\Pressure\KalibrasiPressureModel;
use App\Models\Kalibrasi\Timbangan\KeseragamanSkalaModel;
use App\Models\Kalibrasi\Timbangan\SmryKetidakpastianModel;
use App\Models\Kalibrasi\Timbangan\SmryKeseragamanSkalaModel;
use App\Models\Kalibrasi\Volumetrik\KalibrasiVolumetrikModel;
use App\Models\kalibrasi\Temperature\KalibrasiTemperatureModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongModel;
use App\Models\Kalibrasi\Pressure\KalibrasiPressureGabunganModel;
use App\Models\kalibrasi\Temperature\KalibrasiTemperatureGabModel;
use App\Models\Kalibrasi\Volumetrik\KalibrasiVolumetrikGabunganModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongSummaryModel;
use App\Models\Kalibrasi\Thermohygrometer\KalibrasiThermohygrometerModel;
use App\Models\Kalibrasi\Thermohygrometer\KalibrasiThermohygrometerGabModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongFinalSummaryModel;

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

    public function volumetrik()
    {
        return $this->hasMany(KalibrasiVolumetrikModel::class, 'kalibrasi_id');
    }

    public function volumetrikGabungan()
    {
        return $this->hasOne(KalibrasiVolumetrikGabunganModel::class, 'kalibrasi_id');
    }

    public function temperature()
    {
        return $this->hasMany(KalibrasiTemperatureModel::class, 'kalibrasi_id');
    }

    public function temperatureGabungan()
    {
        return $this->hasMany(KalibrasiTemperatureGabModel::class, 'kalibrasi_id');
    }

    public function thermohygrometer()
    {
        return $this->hasMany(KalibrasiThermohygrometerModel::class, 'kalibrasi_id');
    }

    public function thermohygrometerGabungan()
    {
        return $this->hasMany(KalibrasiThermohygrometerGabModel::class, 'kalibrasi_id');
    }

    public function jangkaSorong()
    {
        return $this->hasMany(KalibrasiJangkaSorongModel::class, 'kalibrasi_id');
    }

    public function jangkaSorongSummary()
    {
        return $this->hasMany(KalibrasiJangkaSorongSummaryModel::class, 'kalibrasi_id');
    }

    public function jangkaSorongFinalSummary()
    {
        return $this->hasMany(KalibrasiJangkaSorongFinalSummaryModel::class, 'kalibrasi_id');
    }

    // Timbangan
    public function pembacaan()
    {
        return $this->hasMany(PembacaanModel::class, 'kalibrasi_id');
    }

    public function keseragamanSkala()
    {
        return $this->hasMany(KeseragamanSkalaModel::class, 'kalibrasi_id');
    }

    public function pinggan()
    {
        return $this->hasMany(PingganModel::class, 'kalibrasi_id');
    }

    public function tare()
    {
        return $this->hasMany(TareModel::class, 'kalibrasi_id');
    }

    public function histerisis()
    {
        return $this->hasMany(HisterisisModel::class, 'kalibrasi_id');
    }

    // Timbangan Summary
    public function pembacaanSummary()
    {
        return $this->hasMany(SmryPembacaanModel::class, 'kalibrasi_id');
    }

    public function keseragamanSummary()
    {
        return $this->hasMany(SmryKeseragamanSkalaModel::class, 'kalibrasi_id');
    }

    public function pingganSummary()
    {
        return $this->hasMany(SmryPingganModel::class, 'kalibrasi_id');
    }

    public function tareSummary()
    {
        return $this->hasOne(SmryTareModel::class, 'kalibrasi_id');
    }

    public function histerisisSummary()
    {
        return $this->hasOne(SmryHisterisisModel::class, 'kalibrasi_id');
    }

    // public function ketidakpastian()
    // {
    //     return $this->hasOne(SmryKetidakpastianModel::class, 'kalibrasi_id');
    // }
}
