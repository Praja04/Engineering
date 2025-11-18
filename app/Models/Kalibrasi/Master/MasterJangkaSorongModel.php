<?php

namespace App\Models\Kalibrasi\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongSummaryModel;

class MasterJangkaSorongModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_jangka_sorong_master';

    protected $fillable = [
        'no',
        'nilai_master',
    ];

    public function jangkaSorong()
    {
        return $this->hasMany(KalibrasiJangkaSorongModel::class, 'master_id');
    }

    public function jangkaSorongSummary()
    {
        return $this->hasMany(KalibrasiJangkaSorongSummaryModel::class, 'master_id');
    }
}
