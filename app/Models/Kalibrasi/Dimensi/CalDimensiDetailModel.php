<?php

namespace App\Models\Kalibrasi\Dimensi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalDimensiDetailModel extends Model
{
    use HasFactory;

    protected $table = 'cal_dimensi_detail';

    protected $fillable = [
        'dimensi_id',
        'penunjuk_standar',
        'penunjuk_alat',
    ];

    /**
     * Relasi ke tabel kalibrasi
     */
    public function dimensi()
    {
        return $this->belongsTo(CalDimensiModel::class, 'dimensi_id');
    }
}
