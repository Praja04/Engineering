<?php

namespace App\Models\Boiler;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoilerModel extends Model
{
    use HasFactory;

    protected $table = 'boilers';

    protected $fillable = [
        // 'periode_tipe',
        // 'week',
        // 'start_date',
        // 'end_date',
        'date',
        'batu_bara',
        'steam',
    ];
}
