<?php

namespace App\Models\Kalibrasi\Pressure;

use App\Models\Kalibrasi\Pressure\KalibrasiPressureModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KalibrasiPressureDetailModel extends Model
{
    use HasFactory;

    protected $table = 'cal_pressure_detail';

    protected $fillable = [
        'pressure_id',
        'arah',
        'penunjuk_standar',
        'penunjuk_alat',
        'koreksi_standar',
        'tekanan_standar',
        'koreksi_alat',
    ];

    public function pressure()
    {
        return $this->belongsTo(KalibrasiPressureModel::class, 'pressure_id');
    }
}
