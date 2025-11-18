<?php

namespace App\Models\Kalibrasi\Timbangan;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KeseragamanSkalaModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_timbangan_keseragaman_skala';
    protected $fillable = [
        'kalibrasi_id',
        'massa',
        'beban',
        'beban_timbangan',
        'pembacaan_skala'
    ];


    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
