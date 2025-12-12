<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmryPingganModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_tmb_pinggan_smry';

    protected $fillable = [
        'kalibrasi_id',
        'percobaan',
        'smry_tengah',
        'smry_depan',
        'smry_belakang',
        'smry_kiri',
        'smry_kanan',
        'minimum',
        'maximum',
        'selisih_maks',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
