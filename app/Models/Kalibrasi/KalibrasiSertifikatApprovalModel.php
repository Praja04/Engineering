<?php

namespace App\Models\Kalibrasi;

use App\Models\User;
use App\Helpers\NotificationHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiSertifikatApprovalModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_sertifikat_approvals';

    protected $fillable = [
        'sertifikat_id',
        'approver_id',
        'approver_email',
        'status',
        'comment',
        'approved_at',
    ];

    // relasi ke sertifikat
    public function sertifikat()
    {
        return $this->belongsTo(KalibrasiSertifikatModel::class, 'sertifikat_id');
    }

    // relasi ke user approver (opsional)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    protected static function booted()
    {
        static::created(function ($approval) {
            if ($approval->status === 'pending') {
                NotificationHelper::pushToPortalUser($approval);
            }
        });

        static::updated(function ($approval) {
            if ($approval->isDirty('status') && $approval->status === 'pending') {
                NotificationHelper::pushToPortalUser($approval);
            }
        });
    }
}
