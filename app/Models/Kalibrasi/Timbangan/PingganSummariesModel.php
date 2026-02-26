<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PingganSummariesModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_pinggan_summaries';

    protected $fillable = [
        'kalibrasi_id',
        'percobaan',
        'summary_tengah',
        'summary_depan',
        'summary_belakang',
        'summary_kiri',
        'summary_kanan',
        'minimum',
        'maximum',
        'selisih_maks',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
