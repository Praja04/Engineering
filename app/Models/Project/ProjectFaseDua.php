<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFaseDua extends Model
{
    use HasFactory;

    protected $table = 'project_fase_dua';

    protected $fillable = [
        'project_id',
        'ejo',
        'deskripsi',
        'user_id',
        'nomor_io',
        'keterangan',
        'persen_pr',
        'persen_po',
        'persen_gr',
    ];

    protected $casts = [
        'persen_pr' => 'integer',
        'persen_po' => 'integer',
        'persen_gr' => 'integer',
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

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Rata-rata persentase pengadaan (PR + PO + GR) / 3.
     */
    public function getRataRataPersenAttribute(): int
    {
        return (int) round(($this->persen_pr + $this->persen_po + $this->persen_gr) / 3);
    }
}
