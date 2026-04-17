<?php

namespace App\Models\Kalibrasi\Instrumen;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalInstrumenModel extends Model
{
    use HasFactory;

    protected $table = 'cal_instrumen';

    protected $fillable = [
        'kalibrasi_id',
        'titik_kalibrasi',
        'indikator',
        'jenis_alat_ukur',
        'jenis_standar',
        'nilai_master',
        'avg_pembacaan',
        'std_dev',
        'koreksi',
    ];


    // Ke header kalibrasi
    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }

    // Ke detail (5 pembacaan)
    public function details()
    {
        return $this->hasMany(CalInstrumenDetailModel::class, 'instrumen_id');
    }

    // Ke keypad / evaluasi
    public function keypad()
    {
        return $this->hasOne(CalInstrumenKeypadModel::class, 'instrumen_id');
    }
}
