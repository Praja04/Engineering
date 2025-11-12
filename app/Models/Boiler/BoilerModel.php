<?php

namespace App\Models\Boiler;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoilerModel extends Model
{
    use HasFactory;

    protected $table = 'boilers';

    protected $fillable = [
        'jenis_input',
        'tanggal',
        'batu_bara',
        'steam',
    ];
}
