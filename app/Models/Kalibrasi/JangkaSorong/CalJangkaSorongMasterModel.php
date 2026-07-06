<?php

namespace App\Models\Kalibrasi\JangkaSorong;

use Illuminate\Database\Eloquent\Model;

class CalJangkaSorongMasterModel extends Model
{
    protected $table = 'cal_jangka_sorong_master';

    protected $fillable = [
        // 'no',
        'nilai_master'
    ];

    public function titik()
    {
        return $this->hasMany(KalibrasiJangkaSorongModel::class, 'master_id');
    }
}
