<?php

namespace App\Models\Kalibrasi\Timbangan;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KeseragamanSkalaModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_keseragaman_skala';

    protected $fillable = [
        'kalibrasi_id',
        'massa_ke',
        'jenis',
        'beban',
        'pembacaan'
    ];


    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
