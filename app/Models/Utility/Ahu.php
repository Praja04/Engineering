<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ahu extends Model
{
    use HasFactory;

    protected $table = 'ahu';

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_foreman_at' => 'datetime',
        'approved_supervisor_at' => 'datetime',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

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

    public function details()
    {
        return $this->hasMany(AhuDetails::class);
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
}
