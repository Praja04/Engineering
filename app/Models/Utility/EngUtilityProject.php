<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EngUtilityProject extends Model
{
    use HasFactory;

    protected $table = 'eng_utility_projects';

    protected $fillable = [
        'nomor_project',
        'nama_project',
        'target_result',
        'area',
        'kategori',
        'priority',
        'fase',
        'start_date',
        'start_week',
        'finish_date',
        'finish_week',
        'progress',
        'status',
        'latest_note',
        'created_by',
        'updated_by',
    ];

    public const FASE_OPTIONS = [
        'Fase 1' => 'Fase 1: Konsep Project',
        'Fase 2' => 'Fase 2: Administrasi Project (IO/ PR/ PO)',
        'Fase 3' => 'Fase 3: Proses Pekerjaan Project',
    ];

    protected $casts = [
        'start_date' => 'date:d-m-Y',
        'finish_date' => 'date:d-m-Y',
        'progress' => 'integer',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saving(function ($project) {
            if ((int)$project->progress >= 100 || strtolower(trim((string)$project->status)) === 'selesai') {
                $project->fase = 'Fase 3';
                if ((int)$project->progress < 100 && strtolower(trim((string)$project->status)) === 'selesai') {
                    $project->progress = 100;
                }
                $project->status = 'Selesai';
            }
        });
    }

    /**
     * Accessor untuk memastikan jika progress >= 100 atau status Selesai, selalu mengembalikan Fase 3
     */
    public function getFaseAttribute($value)
    {
        if ((int)$this->progress >= 100 || strtolower(trim((string)$this->status)) === 'selesai') {
            return 'Fase 3';
        }
        return $value ?: 'Fase 1';
    }

    /**
     * Relationship to progress history logs
     */
    public function logs()
    {
        return $this->hasMany(EngUtilityProjectLog::class, 'project_id')->orderBy('created_at', 'desc');
    }

    /**
     * Relationship to the latest progress history log
     */
    public function latestLog()
    {
        return $this->hasOne(EngUtilityProjectLog::class, 'project_id')->latestOfMany();
    }

    /**
     * Relationship to user who created
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to user who updated
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Auto determine or check status
     */
    public function calculateStatus(): string
    {
        if ($this->progress >= 100) {
            $this->fase = 'Fase 3';
            return 'Selesai';
        }

        if ($this->finish_date && Carbon::parse($this->finish_date)->isPast() && $this->progress < 100) {
            return 'Terlambat';
        }

        return 'On Progress';
    }
}
