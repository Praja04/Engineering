<?php

namespace App\Models\Utility\wwtp_analisa;

use Illuminate\Database\Eloquent\Model;

class WwtpPoint extends Model
{
    protected $table = 'wwtp_point';

    protected $fillable = [
        'point_name'
    ];
}
