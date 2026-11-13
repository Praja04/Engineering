<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class WwtpPengangkutanSludge extends Model
{
    //
    protected $table = 'pengangkutan_sludge';

    protected $fillable = [
        'week_start',
        'week_end',
        'jumlah_pengangkutan',
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

}
