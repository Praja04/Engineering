<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_moc',
        'ejo',
        'deskripsi',
        'user_id',
        'keterangan',
        'fase_aktif',
    ];

    protected $casts = [
        'fase_aktif' => 'string',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function faseSatu(): HasOne
    {
        return $this->hasOne(ProjectFaseSatu::class, 'project_id');
    }

    public function faseDua(): HasOne
    {
        return $this->hasOne(ProjectFaseDua::class, 'project_id');
    }

    public function faseTiga(): HasOne
    {
        return $this->hasOne(ProjectFaseTiga::class, 'project_id');
    }

    public function dokumentasi(): HasMany
    {
        return $this->hasMany(ProjectDokumentasi::class, 'project_id');
    }

    public function dokumentasiFase1(): HasMany
    {
        return $this->hasMany(ProjectDokumentasi::class, 'project_id')
                    ->where('fase', 'fase_1');
    }

    public function dokumentasiFase3(): HasMany
    {
        return $this->hasMany(ProjectDokumentasi::class, 'project_id')
                    ->where('fase', 'fase_3');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Cek apakah project bisa dilanjutkan ke fase berikutnya.
     */
    public function bisaLanjutKeFase(string $fase): bool
    {
        return match ($fase) {
            'fase_2' => $this->fase_aktif === 'fase_1' && $this->faseSatu()->exists(),
            'fase_3' => $this->fase_aktif === 'fase_2' && $this->faseDua()->exists(),
            default  => false,
        };
    }

    /**
     * Label fase untuk tampilan UI.
     */
    public function getLabelFaseAttribute(): string
    {
        return match ($this->fase_aktif) {
            'fase_1' => 'Fase 1 — Inisiasi',
            'fase_2' => 'Fase 2 — Pengadaan',
            'fase_3' => 'Fase 3 — Pekerjaan',
            default  => '-',
        };
    }

    /**
     * Hitung persentase overall progress project (33% per fase selesai).
     */
    public function getProgressAttribute(): int
    {
        return match ($this->fase_aktif) {
            'fase_1' => 33,
            'fase_2' => 66,
            'fase_3' => 100,
            default  => 0,
        };
    }
}
