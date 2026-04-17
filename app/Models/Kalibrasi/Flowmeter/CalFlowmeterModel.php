<?php

namespace App\Models\Kalibrasi\Flowmeter;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalFlowmeterModel extends Model
{
    use HasFactory;

    protected $table = 'cal_flowmeter';

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
        return $this->hasMany(CalFlowmeterDetailModel::class, 'flowmeter_id');
    }
}
