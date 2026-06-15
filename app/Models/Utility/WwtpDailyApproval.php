<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class WwtpDailyApproval extends Model
{
    use HasFactory;

    protected $table = 'wwtp_daily_approvals';

    protected $fillable = [
        'tanggal',
        'operator_id',
        'foreman_id',
        'supervisor_id',
        'status',
        'reject_reason',
        'submitted_at',
        'foreman_approved_at',
        'supervisor_approved_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'submitted_at' => 'datetime',
        'foreman_approved_at' => 'datetime',
        'supervisor_approved_at' => 'datetime',
    ];

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

    // Has many processes (Influent Harian) grouped by tanggal
    public function influentHarian()
    {
        return $this->hasMany(WwtpInfluentHarian::class, 'tanggal', 'tanggal');
    }

    // Has many performance (pH Harian) grouped by tanggal
    public function phHarian()
    {
        return $this->hasMany(WwtpPerformancePHharian::class, 'tanggal', 'tanggal');
    }

    // Has many sludge harian grouped by tanggal
    public function sludgeHarian()
    {
        return $this->hasMany(WwtpSludge::class, 'tanggal', 'tanggal');
    }
}
