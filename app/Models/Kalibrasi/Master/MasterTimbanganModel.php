<?php

namespace App\Models\Kalibrasi\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterTimbanganModel extends Model
{
    use HasFactory;

    protected $table = 'cal_timbangan_master';

    protected $fillable = [
        'beban',
        'standar_massa',
    ];
}
