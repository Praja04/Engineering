<?php

namespace App\Models\Kalibrasi\JangkaSorong;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiJangkaSorongModel extends Model
{
    use HasFactory;

    protected $table = 'cal_jangka_sorong';

    protected $fillable = [
        'kalibrasi_id',
        'master_id',
        'avg_pembacaan',
        'std_dev',
        'koreksi'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }

    public function master()
    {
        return $this->belongsTo(CalJangkaSorongMasterModel::class, 'master_id');
    }

    public function details()
    {
        return $this->hasMany(KalibrasiJangkaSorongDetailModel::class, 'jangka_sorong_id');
    }
}
