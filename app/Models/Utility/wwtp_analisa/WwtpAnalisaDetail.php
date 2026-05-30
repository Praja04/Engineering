<?php

namespace App\Models\Utility\wwtp_analisa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwtpAnalisaDetail extends Model
{
    use HasFactory;

    protected $table = 'wwtp_analisa_details';

    protected $fillable = [
        'analisa_id',
        'point_id',
        'parameter_id',
        'hasil_analisa',
        'keterangan'
    ];

    public function analisa()
    {
        return $this->belongsTo(WwtpAnalisa::class, 'analisa_id');
    }

    public function point()
    {
        return $this->belongsTo(WwtpPoint::class, 'point_id');
    }

    public function parameter()
    {
        return $this->belongsTo(WwtpParameter::class, 'parameter_id');
    }
}
