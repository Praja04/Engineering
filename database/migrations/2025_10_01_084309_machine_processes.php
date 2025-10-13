<?php

namespace App\Models\ScoringMesin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class MachineScoring extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'machine_id',
        'machine_process_id',
        'user_id',
        'scoring_date',
        'notes',
        'status', // draft, completed
    ];

    protected $casts = [
        'scoring_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    // Relationship with Machine
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    // Relationship with MachineProcess
    public function machineProcess()
    {
        return $this->belongsTo(MachineProcess::class);
    }

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // Relationship with ScoringDetails
    public function scoringDetails()
    {
        return $this->hasMany(ScoringDetail::class);
    }

    // Scope for completed scoring
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope for draft scoring
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Check if all parts are scored
    public function isFullyScored()
    {
        $processParameter = $this->machineProcess->processParameter;
        $totalParts = $processParameter->sections()
            ->withCount('parts')
            ->get()
            ->sum('parts_count');

        $scoredParts = $this->scoringDetails()->count();

        return $totalParts === $scoredParts;
    }

    // Calculate OK percentage
    public function getOkPercentageAttribute()
    {
        $total = $this->scoringDetails()->count();
        if ($total === 0) return 0;

        $okCount = $this->scoringDetails()->where('result', 'OK')->count();
        return round(($okCount / $total) * 100, 2);
    }

    // Calculate NOT OK percentage
    public function getNotOkPercentageAttribute()
    {
        return 100 - $this->ok_percentage;
    }

    // Get summary
    public function getSummary()
    {
        $total = $this->scoringDetails()->count();
        $ok = $this->scoringDetails()->where('result', 'OK')->count();
        $notOk = $this->scoringDetails()->where('result', 'NOT OK')->count();

        return [
            'total' => $total,
            'ok' => $ok,
            'not_ok' => $notOk,
            'ok_percentage' => $this->ok_percentage,
            'not_ok_percentage' => $this->not_ok_percentage,
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->user_id && Auth::check()) {
                $model->user_id = Auth::id();
            }
            if (!$model->scoring_date) {
                $model->scoring_date = now();
            }
        });
    }
}
