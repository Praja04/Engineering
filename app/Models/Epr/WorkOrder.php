<?php

namespace App\Models\Epr;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    protected $table = 'epr_work_orders';

    protected $fillable = [
        'wo_number',
        'title',
        'description',
        'area',
        'machine',
        'priority',
        'status',
        'target_date',
        'created_by',
        'approved_by',
        'approved_at',
        'reject_reason',
    ];

    protected $casts = [
        'target_date'  => 'date',
        'approved_at'  => 'datetime',
    ];

    // ── Relationships ──

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function assignees(): HasMany
    {
        return $this->hasMany(WoAssignee::class, 'work_order_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(PredictiveMaintenance::class, 'work_order_id');
    }

    // ── Auto WO Number ──

    public static function generateWoNumber(): string
    {
        $prefix = 'WO-PM-' . date('ymd') . '-';
        $lastWo = static::where('wo_number', 'like', $prefix . '%')
                        ->orderBy('wo_number', 'desc')
                        ->first();

        if ($lastWo) {
            $lastSeq = (int) substr($lastWo->wo_number, -3);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
    }
}
