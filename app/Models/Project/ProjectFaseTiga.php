<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFaseTiga extends Model
{
    use HasFactory;

    protected $table = 'project_fase_tiga';

    protected $fillable = [
        'project_id',
        'ejo',
        'deskripsi',
        'user_id',
        'keterangan',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectMaster::class, 'project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
