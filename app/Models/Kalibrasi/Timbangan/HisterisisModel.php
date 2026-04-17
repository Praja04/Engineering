<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HisterisisModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_histerisis';

    protected $fillable = [
        'kalibrasi_id',
        'label',
        'pengulangan',
        'nilai',
    ];


    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
