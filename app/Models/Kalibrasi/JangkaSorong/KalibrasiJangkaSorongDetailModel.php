<?php

namespace App\Models\Kalibrasi\JangkaSorong;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiJangkaSorongDetailModel extends Model
{
    use HasFactory;

    protected $table = 'cal_jangka_sorong_detail';

    protected $fillable = [
        'jangka_sorong_id',
        'no_pengulangan',
        'nilai_master',
        'nilai_pembacaan',
    ];

    public function jangkaSorong()
    {
        return $this->belongsTo(KalibrasiJangkaSorongModel::class, 'jangka_sorong_id');
    }
}
