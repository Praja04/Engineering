<?php

namespace App\Models\Kalibrasi\Instrumen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalInstrumenDetailModel extends Model
{
    use HasFactory;

    protected $table = 'cal_instrumen_detail';

    protected $fillable = [
        'instrumen_id',
        'no_ulang',
        'alat',
        'standar',
        'pembacaan_alat',
        'pembacaan_standar',
    ];

    public function instrumen()
    {
        return $this->belongsTo(CalInstrumenModel::class, 'instrumen_id');
    }
}
