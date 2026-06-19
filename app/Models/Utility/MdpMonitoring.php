<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdpMonitoring extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'foreman_id',
        'supervisor_id',
        'tanggal_laporan',
        'jam_pencatatan',
        'e_del',
        'arus_rata_rata',
        'arus_i1',
        'arus_i2',
        'arus_i3',
        'tegangan_rata_rata',
        'tegangan_v1',
        'tegangan_v2',
        'tegangan_v3',
        'daya_total',
        'daya_p1',
        'daya_p2',
        'daya_p3',
        'temperatur_transformator',
        'level_oil',
        'status',
        'approved_foreman_by',
        'approved_foreman_at',
        'approved_supervisor_by',
        'approved_supervisor_at',
        'reject_reason',
        'updated_by',
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

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
