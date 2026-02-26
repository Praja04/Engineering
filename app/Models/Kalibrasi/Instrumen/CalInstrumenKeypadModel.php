<?php

namespace App\Models\Kalibrasi\Instrumen;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalInstrumenKeypadModel extends Model
{
    use HasFactory;

    protected $table = 'cal_instrumen_keypad';

    protected $fillable = [
        'kalibrasi_id',
        'tested',
        'measured',
        'criterion',
        'passed',
    ];

    protected $casts = [
        'tested' => 'boolean',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
