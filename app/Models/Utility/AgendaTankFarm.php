<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaTankFarm extends Model
{
    use HasFactory;

    protected $table = 'agenda_tank_farm';

    protected $fillable = [
        'bulan',
        'tahun',
        'status',
        'operator_id',
        'foreman_id',
        'supervisor_id',
        'submitted_at',
        'approved_foreman_at',
        'approved_supervisor_at',
        'reject_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_foreman_at' => 'datetime',
        'approved_supervisor_at' => 'datetime',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function foreman()
    {
        return $this->belongsTo(User::class, 'foreman_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function details()
    {
        return $this->hasMany(AgendaTankFarmDetails::class, 'agenda_tank_farm_id');
    }
}
