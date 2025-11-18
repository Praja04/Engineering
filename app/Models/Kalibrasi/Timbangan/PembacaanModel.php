<?php

namespace App\Models\Kalibrasi\Timbangan;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembacaanModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_timbangan_pembacaan';

    protected $fillable = [
        'kalibrasi_id',
        'kemampuan',
        'titik',
        'ulangan',
        'pembacaan_z',
        'pembacaan_m',
        'selisih',
        'maks_perbedaan',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
