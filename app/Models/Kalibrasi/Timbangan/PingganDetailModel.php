<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PingganDetailModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_pinggan_detail';

    protected $fillable = [
        'pinggan_id',
        'percobaan',
        'posisi',
        'nilai'
    ];

    public function pinggan()
    {
        return $this->belongsTo(PingganModel::class, 'pinggan_id');
    }
}
