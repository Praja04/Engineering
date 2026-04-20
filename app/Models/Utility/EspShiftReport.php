<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class EspShiftReport extends Model
{
    protected $fillable = [
        'tanggal_laporan',
        'pemakaian_air',
        'pemakaian_steam',
        'pemakaian_batubara',
        'efisiensi_batubara',
        'running_hour_awal',
        'running_hour_akhir',
        'feed_tank_awal',
        'feed_tank_akhir',
        'pengisian_batubara',
        'chemical_scf',
        'chemical_srtf',
        'dosis',
        'operator_id',
        'foreman_id',
        'supervisor_id',
        'foreman_approved_at',      
        'supervisor_approved_at',   
        'status'
    ];

    public function operator()
    {
        return $this->belongsTo(\App\Models\User::class, 'operator_id');
    }

    public function foreman()
    {
        return $this->belongsTo(\App\Models\User::class, 'foreman_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(\App\Models\User::class, 'supervisor_id');
    }
}