<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarmingUpGenset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'foreman_id',
        'supervisor_id',
        'tanggal_laporan',
        'jam_pencatatan',
        'engine_speed',
        'engine_temperature',
        'engine_oil_pressure',
        'battery_voltage',
        'charge_alt_voltage',
        'running_hour',
        'frequency',
        'status_oil_1',
        'status_oil_2',
        'status',
        'approved_foreman_by',
        'approved_foreman_at',
        'approved_supervisor_by',
        'approved_supervisor_at',
        'reject_reason',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date:Y-m-d',
        'approved_foreman_at' => 'datetime',
        'approved_supervisor_at' => 'datetime',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


    public function operator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function foreman()
    {
        return $this->belongsTo(User::class, 'foreman_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function approvedForeman()
    {
        return $this->belongsTo(User::class, 'approved_foreman_by');
    }

    public function approvedSupervisor()
    {
        return $this->belongsTo(User::class, 'approved_supervisor_by');
    }
}
