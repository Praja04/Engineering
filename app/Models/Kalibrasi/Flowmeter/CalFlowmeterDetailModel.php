<?php

namespace App\Models\Kalibrasi\Flowmeter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalFlowmeterDetailModel extends Model
{
    use HasFactory;

    protected $table = 'cal_flowmeter_detail';

    protected $fillable = [
        'flowmeter_id',
        'penunjuk_standar',
        'penunjuk_alat',
        'keterangan',
    ];

    /**
     * Relasi ke tabel kalibrasi
     */
    public function flowmeter()
    {
        return $this->belongsTo(CalFlowmeterModel::class, 'flowmeter_id');
    }
}
