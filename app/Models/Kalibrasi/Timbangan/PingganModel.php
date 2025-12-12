<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PingganModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_timbangan_pinggan';

    protected $fillable = [
        'kalibrasi_id',
        'diameter',
        'massa',
        'percobaan',
        'tengah',
        'depan',
        'belakang',
        'kiri',
        'kanan',
    ];


    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
