<?php

namespace App\Models\Epr;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WoAssignee extends Model
{
    protected $table = 'epr_wo_assignees';

    protected $fillable = [
        'work_order_id',
        'user_id',
        'duration_minutes',
        'note',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
