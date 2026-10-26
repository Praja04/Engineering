<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MtcChecklistResult extends Model
{
    use HasFactory;

    protected $table = 'mtc_inspection_checklist_results';

    protected $fillable = [
        'mtc_main_id',
        'checklist_id',
        'status',
        'nilai',
        'keterangan',
        'created_by',
    ];

    public function main()
    {
        return $this->belongsTo(MtcMainModel::class, 'mtc_main_id');
    }

    public function checklist()
    {
        return $this->belongsTo(MtcMasterChecklist::class, 'checklist_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
