<?php

namespace App\Models\Kalibrasi\Timbangan;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KemampuanUlangSummariesModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_kemampuan_ulang_summaries';

    protected $fillable = [
        'kalibrasi_id',
        'massa',
        'jenis',
        'std_dev',
        'maks_perbedaan_akhir',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
