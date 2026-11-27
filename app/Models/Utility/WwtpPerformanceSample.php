<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class WwtpPerformanceSample extends Model
{
    //
    protected $table = 'wwtp_performance_sample';

    protected $fillable = [
        'tanggal',
        'jenis_sampel',
        'id_sampel',
        'tss',
        'sv30',
        'ph',
        'mlss',
        'svl',
        'do',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    protected $casts = [
        // 'tanggal' => 'date',
        'tss'     => 'decimal:2',
        'sv30'    => 'decimal:2',
        'ph'      => 'decimal:2',
        'mlss'    => 'decimal:2',
        'svl'     => 'decimal:2',
        'do'      => 'decimal:2',
    ];

    public function jenisSampel()
    {
        return $this->belongsTo(WwtpJenisSample::class, 'id_sampel');
    }
}
