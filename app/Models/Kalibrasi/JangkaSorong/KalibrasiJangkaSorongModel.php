<?php

namespace App\Models\Kalibrasi\JangkaSorong;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Kalibrasi\Master\MasterJangkaSorongModel;

class KalibrasiJangkaSorongModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_jangka_sorong';

    protected $fillable = [
        'kalibrasi_id',
        'master_id',
        'no',
        'nilai_master',
        'nilai_pembacaan',
    ];

    public function master()
    {
        return $this->belongsTo(MasterJangkaSorongModel::class, 'master_id');
    }

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
