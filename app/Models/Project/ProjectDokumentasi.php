<?php

namespace App\Models\Project;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDokumentasi extends Model
{
    use HasFactory;

    protected $table = 'project_dokumentasi';

    protected $fillable = [
        'project_id',
        'fase',
        'tipe',
        'nama_file',
        'path_file',
        'mime_type',
        'ukuran_file',
        'uploaded_by',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectMaster::class, 'project_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * URL publik file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url('public/' . $this->path_file);
    }

    /**
     * Ukuran file dalam format human-readable (KB / MB).
     */
    public function getUkuranFormatAttribute(): string
    {
        if (!$this->ukuran_file) return '-';

        $kb = $this->ukuran_file / 1024;

        return $kb >= 1024
            ? number_format($kb / 1024, 1) . ' MB'
            : number_format($kb, 0) . ' KB';
    }

    /**
     * Cek apakah file ini adalah gambar.
     */
    public function getIsGambarAttribute(): bool
    {
        return $this->tipe === 'foto';
    }
}
