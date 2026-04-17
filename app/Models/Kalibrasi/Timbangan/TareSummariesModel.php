<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TareSummariesModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_tare_summaries';

    protected $fillable = [
        'kalibrasi_id',
        'kondisi',
        'massa',
        'selisih_mz',
        'pengaruh',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
