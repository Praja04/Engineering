<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TareModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_tare';

    protected $fillable = [
        'kalibrasi_id',
        'kondisi',
        'label',
        'nilai',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
