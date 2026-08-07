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
        'kondensat',
        'operator_id',
        'foreman_id',
        'supervisor_id',
        'foreman_approved_at',      
        'supervisor_approved_at',   
        'status'
    ];

    protected static function booted()
    {
        static::saving(function ($report) {
            if (!is_null($report->feed_tank_akhir) && 
                !is_null($report->feed_tank_awal) && 
                !is_null($report->pemakaian_air) && 
                $report->pemakaian_air != 0) {
                
                $report->kondensat = abs((($report->feed_tank_akhir - $report->feed_tank_awal) / $report->pemakaian_air) * 100 - 100);
            } else {
                $report->kondensat = null;
            }
        });
    }

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