<?php

namespace App\Models\Utility\wwtp_analisa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwtpStandard extends Model
{
    use HasFactory;

    protected $table = 'wwtp_standards';

    protected $fillable = [
        'point_id',
        'parameter_id',
        'standard_value'
    ];

    public function point()
    {
        return $this->belongsTo(WwtpPoint::class, 'point_id');
    }

    public function parameter()
    {
        return $this->belongsTo(WwtpParameter::class, 'parameter_id');
    }
}
