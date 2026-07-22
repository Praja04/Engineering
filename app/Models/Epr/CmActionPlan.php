<?php

namespace App\Models\Epr;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmActionPlan extends Model
{
    use HasFactory;

    protected $table = 'epr_cm_action_plans';

    protected $fillable = [
        'month',
        'mesin',
        'isu_utama',
        'akar_masalah',
        'saran_perbaikan',
        'pic',
        'target_date',
        'w1_status',
        'w2_status',
        'w3_status',
        'w4_status',
        'status',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
