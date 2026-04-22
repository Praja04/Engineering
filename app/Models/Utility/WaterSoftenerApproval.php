<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaterSoftenerApproval extends Model
{
    use HasFactory;

    protected $table = 'water_softener_approvals';

    protected $fillable = [
        'bulan',
        'tahun',
        'operator_id',
        'foreman_id',
        'supervisor_id',
        'status',
        'submitted_at',
        'foreman_approved_at',
        'supervisor_approved_at',
    ];

    protected $casts = [
        'bulan'                  => 'integer',
        'tahun'                  => 'integer',
        'submitted_at'           => 'datetime',
        'foreman_approved_at'    => 'datetime',
        'supervisor_approved_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────

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

    /**
     * Ambil semua data harian WaterSoftener milik bulan ini
     */
    public function dataHarian()
    {
        return WaterSoftener::whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->orderBy('tanggal')
            ->get();
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeWaitingForeman($query)
    {
        return $query->where('status', 'waiting_foreman');
    }

    public function scopeWaitingSupervisor($query)
    {
        return $query->where('status', 'approved_foreman');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'approved_supervisor');
    }
}
