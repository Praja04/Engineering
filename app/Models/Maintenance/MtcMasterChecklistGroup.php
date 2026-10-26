<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MtcMasterChecklistGroup extends Model
{
    use HasFactory;

    protected $table = 'mtc_master_checklist_groups';

    protected $fillable = [
        'jenis_mtc',
        'nama_group',
        'urutan',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan'    => 'integer',
    ];

    public function checklists()
    {
        return $this->hasMany(MtcMasterChecklist::class, 'group_id')
                    ->orderBy('urutan', 'asc');
    }

    public function activeChecklists()
    {
        return $this->hasMany(MtcMasterChecklist::class, 'group_id')
                    ->where('is_active', true)
                    ->orderBy('urutan', 'asc');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
