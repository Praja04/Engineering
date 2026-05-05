<?php

namespace App\Models\Kalibrasi;

use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\Dimensi\CalDimensiModel;
use App\Models\Kalibrasi\Flowmeter\CalFlowmeterModel;
use App\Models\Kalibrasi\Instrumen\CalInstrumenKeypadModel;
use App\Models\Kalibrasi\Instrumen\CalInstrumenModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongSummaryModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use App\Models\Kalibrasi\Pressure\KalibrasiPressureModel;
use App\Models\Kalibrasi\Temperature\KalibrasiTemperatureModel;
use App\Models\Kalibrasi\Thermohygrometer\KalibrasiThermohygrometerModel;
use App\Models\Kalibrasi\Timbangan\HisterisisModel;
use App\Models\Kalibrasi\Timbangan\HisterisisSummariesModel;
use App\Models\Kalibrasi\Timbangan\KemampuanUlangModel;
use App\Models\Kalibrasi\Timbangan\KemampuanUlangSummariesModel;
use App\Models\Kalibrasi\Timbangan\KeseragamanSkalaModel;
use App\Models\Kalibrasi\Timbangan\KeseragamanSkalaSummariesModel;
use App\Models\Kalibrasi\Timbangan\KetidakpastianSummariesModel;
use App\Models\Kalibrasi\Timbangan\PingganModel;
use App\Models\Kalibrasi\Timbangan\PingganSummariesModel;
use App\Models\Kalibrasi\Timbangan\TareModel;
use App\Models\Kalibrasi\Timbangan\TareSummariesModel;
use App\Models\Kalibrasi\Volumetrik\KalibrasiVolumetrikModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KalibrasiModel extends Model
{
    use HasFactory;

    protected $table = 'cal_main';

    protected $fillable = [
        'alat_id',
        'user_id',
        'lokasi_kalibrasi',
        'suhu_ruangan',
        'kelembaban',
        'tgl_kalibrasi',
        'tgl_kalibrasi_ulang',
        'jenis_kalibrasi',
        'catatan',
    ];

    public function alat()
    {
        return $this->belongsTo(AlatKalibrasiModel::class, 'alat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pressure()
    {
        return $this->hasMany(KalibrasiPressureModel::class, 'kalibrasi_id');
    }

    public function certificate()
    {
        return $this->hasOne(KalibrasiSertifikatModel::class, 'kalibrasi_id');
    }

    public function volumetrik()
    {
        return $this->hasMany(KalibrasiVolumetrikModel::class, 'kalibrasi_id');
    }

    public function temperature()
    {
        return $this->hasMany(KalibrasiTemperatureModel::class, 'kalibrasi_id');
    }

    public function thermohygrometer()
    {
        return $this->hasMany(KalibrasiThermohygrometerModel::class, 'kalibrasi_id');
    }

    public function jangkaSorong()
    {
        return $this->hasMany(KalibrasiJangkaSorongModel::class, 'kalibrasi_id');
    }

    public function jangkaSorongSummary()
    {
        return $this->hasMany(KalibrasiJangkaSorongSummaryModel::class, 'kalibrasi_id');
    }

    public function getRelasiByJenis(): array
    {
        return match (strtolower($this->jenis_kalibrasi)) {
            'pressure' => ['pressure'],
            'temperature' => ['temperature'],
            'volumetrik' => ['volumetrik'],
            'thermohygrometer' => ['thermohygrometer'],
            'jangka_sorong' => ['jangkaSorong', 'jangkaSorongSummary'],
            'timbangan' => [
                'kemampuanUlang',
                'keseragamanSkala',
                'pinggan',
                'tare',
                'histerisis',
                'kemampuanUlangSummary',
                'keseragamanSkalaSummary',
                'pingganSummary',
                'tareSummary',
                'histerisisSummary',
                'ketidakpastianSummary'
            ],
            'instrumen' => ['instrumen', 'keypad'],
            'dimensi' => ['dimensi'],
            'flowmeter' => ['flowmeter'],
            default => [],
        };
    }

    public function getMaxKoreksi()
    {
        $maxKoreksi = 0;

        switch (strtolower($this->jenis_kalibrasi)) {
            case 'pressure':
                if ($this->pressure->isNotEmpty()) {
                    $naik = $this->pressure->max('avg_koreksi_alat_naik') ?? 0;
                    $turun = $this->pressure->max('avg_koreksi_alat_turun') ?? 0;
                    $maxKoreksi = max(abs($naik), abs($turun));
                }
                break;
            case 'temperature':
                if ($this->temperature->isNotEmpty()) {
                    $maxKoreksi = $this->temperature->map(fn($i) => abs($i->avg_kor_alat))->max() ?? 0;
                }
                break;
            case 'volumetrik':
                if ($this->volumetrik->isNotEmpty()) {
                    $maxKoreksi = $this->volumetrik->map(fn($i) => abs($i->avg_koreksi))->max() ?? 0;
                }
                break;
            case 'thermohygrometer':
                if ($this->thermohygrometer->isNotEmpty()) {
                    $suhu = $this->thermohygrometer->map(fn($i) => abs($i->avg_kor_alat_suhu))->max() ?? 0;
                    $rh = $this->thermohygrometer->map(fn($i) => abs($i->avg_kor_alat_rh))->max() ?? 0;
                    $maxKoreksi = max($suhu, $rh);
                }
                break;
            case 'jangka_sorong':
                if ($this->jangkaSorong->isNotEmpty()) {
                    $maxKoreksi = $this->jangkaSorong->map(fn($i) => abs($i->koreksi))->max() ?? 0;
                }
                break;
            case 'instrumen':
                if ($this->instrumen->isNotEmpty()) {
                    $maxKoreksi = $this->instrumen->map(fn($i) => abs($i->koreksi))->max() ?? 0;
                }
                break;
            case 'dimensi':
                if ($this->dimensi->isNotEmpty()) {
                    $maxKoreksi = $this->dimensi->map(fn($i) => abs($i->koreksi))->max() ?? 0;
                }
                break;
            case 'timbangan':
                if ($this->keseragamanSkala->isNotEmpty()) {
                    $maxKoreksi = $this->keseragamanSkala->map(function ($item) {
                        return abs(($item->pembacaan ?? 0) - ($item->beban ?? 0));
                    })->max() ?? 0;
                }
                break;
        }

        return $maxKoreksi;
    }

    // Timbangan
    public function kemampuanUlang()
    {
        return $this->hasMany(KemampuanUlangModel::class, 'kalibrasi_id');
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

    public function kemampuanUlangSummary()
    {
        return $this->hasMany(KemampuanUlangSummariesModel::class, 'kalibrasi_id');
    }

    public function keseragamanSkalaSummary()
    {
        return $this->hasMany(KeseragamanSkalaSummariesModel::class, 'kalibrasi_id');
    }

    public function pingganSummary()
    {
        return $this->hasOne(PingganSummariesModel::class, 'kalibrasi_id');
    }

    public function tareSummary()
    {
        return $this->hasMany(TareSummariesModel::class, 'kalibrasi_id');
    }

    public function histerisisSummary()
    {
        return $this->hasOne(HisterisisSummariesModel::class, 'kalibrasi_id');
    }

    public function ketidakpastianSummary()
    {
        return $this->hasOne(KetidakpastianSummariesModel::class, 'kalibrasi_id');
    }

    public function instrumen()
    {
        return $this->hasMany(CalInstrumenModel::class, 'kalibrasi_id');
    }

    public function keypad()
    {
        return $this->hasMany(CalInstrumenKeypadModel::class, 'kalibrasi_id');
    }

    public function dimensi()
    {
        return $this->hasMany(CalDimensiModel::class, 'kalibrasi_id');
    }

    public function flowmeter()
    {
        return $this->hasMany(CalFlowmeterModel::class, 'kalibrasi_id');
    }
}
