<?php

namespace App\Models\Kalibrasi\Timbangan;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KemampuanUlangModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_kemampuan_ulang';

    protected $fillable = [
        'kalibrasi_id',
        'jenis',
        'ulangan',
        'massa',
        'nilai_z',
        'nilai_m',
        'selisih',
        'maks_perbedaan',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
