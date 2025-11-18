<?php

namespace App\Models\Kalibrasi\JangkaSorong;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiJangkaSorongFinalSummaryModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_jangka_sorong_final_summary';

    protected $fillable = [
        'kalibrasi_id',
        'std_dev_total',
        'ketidakpastian',
        'k_2',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
