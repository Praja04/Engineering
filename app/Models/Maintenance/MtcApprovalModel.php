<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcApprovalModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_approval';

    protected $fillable = [
        'mtc_main_id',
        'level',
        'role',
        'approver_id',
        'status',
        'action_at',
        'action_by',
        'catatan',
        'ttd',
    ];

    // ================= RELATIONS =================

    public function main()
    {
        return $this->belongsTo(MtcMainModel::class, 'mtc_main_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
