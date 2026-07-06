<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoilerApproval extends Model
{
    use HasFactory;

    protected $table = 'boiler_approvals';

    protected $fillable = [
        'tanggal',
        'foreman_id',
        'supervisor_id',
        'status',           // draft | waiting_supervisor | approved_supervisor
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'submitted_at' => 'datetime',
        'approved_at'  => 'datetime',
    ];

    // ── RELATIONSHIPS ─────────────────────────────────────

    public function foreman()
    {
        return $this->belongsTo(\App\Models\User::class, 'foreman_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(\App\Models\User::class, 'supervisor_id');
    }

    /**
     * Ambil semua log jam-an untuk tanggal ini
     */
    public function logs()
    {
        return BoilerLog::whereDate('waktu', $this->tanggal)
            ->orderBy('waktu')
            ->get();
    }

    // ── SCOPES ────────────────────────────────────────────

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeWaitingSupervisor($query)
    {
        return $query->where('status', 'waiting_supervisor');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved_supervisor');
    }
}
