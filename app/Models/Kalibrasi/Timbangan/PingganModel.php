<?php

namespace App\Models\Kalibrasi\Timbangan;

use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\Timbangan\PingganDetailModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PingganModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_pinggan';

    protected $fillable = [
        'kalibrasi_id',
        'diameter',
        'massa',
    ];

    public function details()
    {
        return $this->hasMany(PingganDetailModel::class, 'pinggan_id');
    }

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
