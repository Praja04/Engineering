<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwtpSludge extends Model
{
    //
    use HasFactory;

    protected $table = 'wwtp_sludge';

    protected $fillable = [
        'tanggal',
        'shift',
        'drain_lumpur',
        'running_hour_scp',
        'hasil_lumpur',
        'sludge_content'
    ];
}
