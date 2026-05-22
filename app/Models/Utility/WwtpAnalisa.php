<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwtpAnalisa extends Model
{
    use HasFactory;

    protected $table = 'wwtp_analisa';

    protected $fillable = [
        'tanggal',
        'cod',
        'tss',
        'ph',
        'ec',
        'do'
    ];
}
