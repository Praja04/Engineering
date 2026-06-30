<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class UtilityMonthlyApproval extends Model
{
    use HasFactory;

    protected $table = 'utility_monthly_approvals';

    protected $fillable = [
        'bulan',
        'tipe',
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

    public static function checkAndNotify($bulan, $tipe, $operatorId, $foremanId, $supervisorId)
    {
        $approval = self::where('bulan', $bulan)->where('tipe', $tipe)->first();

        $isNew = false;
        if (!$approval) {
            $approval = self::create([
                'bulan' => $bulan,
                'tipe' => $tipe,
                'status' => 'submitted',
                'operator_id' => $operatorId,
                'foreman_id' => $foremanId,
                'supervisor_id' => $supervisorId,
                'submitted_at' => now(),
            ]);
            $isNew = true;
        } elseif ($approval->status === 'rejected') {
            $approval->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'reject_reason' => null
            ]);
            $isNew = true;
        }

        if ($isNew && $approval->foreman_id) {
            $bulanFormatted = \Illuminate\Support\Carbon::parse($approval->bulan . '-01')->translatedFormat('F Y');
            $tipeFormatted = ucfirst($approval->tipe);
            \App\Models\NotificationsModel::create([
                'user_id' => $approval->foreman_id,
                'title' => "Approval Bulanan Utility ({$tipeFormatted})",
                'message' => "Laporan Pemakaian {$tipeFormatted} Bulan {$bulanFormatted} menunggu persetujuan Anda.",
                'url' => url('/utility/approval'),
                'notifiable_type' => self::class,
                'notifiable_id' => $approval->id,
                'is_read' => 0,
            ]);
        }

        return $approval;
    }
}
