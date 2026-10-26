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

    protected $casts = [
        'start_date' => 'date',
        'finish_date' => 'date',
        'progress' => 'integer',
    ];

    /**
     * Relationship to progress history logs
     */
    public function logs()
    {
        return $this->hasMany(EngUtilityProjectLog::class, 'project_id')->orderBy('created_at', 'desc');
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
            return 'Selesai';
        }

        if ($this->finish_date && Carbon::parse($this->finish_date)->isPast() && $this->progress < 100) {
            return 'Terlambat';
        }

        return 'On Progress';
    }
}
