<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MtcMasterChecklist extends Model
{
    use HasFactory;

    protected $table = 'mtc_master_checklists';

    protected $fillable = [
        'group_id',
        'jenis_mtc',
        'field_key',
        'label',
        'standar',
        'tipe_input',
        'urutan',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan'    => 'integer',
    ];

    public function group()
    {
        return $this->belongsTo(MtcMasterChecklistGroup::class, 'group_id');
    }

    public function results()
    {
        return $this->hasMany(MtcChecklistResult::class, 'checklist_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
