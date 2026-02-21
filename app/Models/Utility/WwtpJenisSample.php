<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class WwtpJenisSample extends Model
{
    //
    protected $table = 'wwtp_jenis_sampel';

    protected $fillable = [
        'nama_sampel',
    ];

    public function performanceSamples()
    {
        return $this->hasMany(WwtpPerformanceSample::class, 'id_sampel');
    }
}
