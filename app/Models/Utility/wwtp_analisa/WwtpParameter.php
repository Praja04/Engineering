<?php

namespace App\Models\Utility\wwtp_analisa;

use Illuminate\Database\Eloquent\Model;

class WwtpParameter extends Model
{
    protected $table = 'wwtp_parameters';

    protected $fillable = [
        'parameter_name',
        'unit'
    ];
}
