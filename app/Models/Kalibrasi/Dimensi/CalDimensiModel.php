<?php

namespace App\Models\Kalibrasi\Dimensi;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalDimensiModel extends Model
{
    use HasFactory;

    protected $table = 'cal_dimensi';

    protected $fillable = [
        'kalibrasi_id',
        'titik_kalibrasi',
        'nilai_master',
        'avg_pembacaan',
        'koreksi',
        'std_dev',
        'ketidakpastian',
    ];

    /**
     * Relasi ke tabel kalibrasi
     */
    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }

    public function details()
    {
        return $this->hasMany(CalDimensiDetailModel::class, 'dimensi_id');
    }
}
