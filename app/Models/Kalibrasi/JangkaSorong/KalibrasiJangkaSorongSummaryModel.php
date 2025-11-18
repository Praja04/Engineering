<?php

namespace App\Models\Kalibrasi\JangkaSorong;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Kalibrasi\Master\MasterJangkaSorongModel;

class KalibrasiJangkaSorongSummaryModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_jangka_sorong_summary';

    protected $fillable = [
        'kalibrasi_id',
        'master_id',
        'avg_pembacaan',
        'std_dev',
        'koreksi'
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
