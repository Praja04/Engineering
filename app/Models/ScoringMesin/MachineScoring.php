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

    /**
     * Calculate deduction points based on critical parts
     * Critical Y + NOT OK = -4
     * Critical N + NOT OK = -1
     * OK = 0
     */
    public function getDeductionPoints()
    {
        $deduction = 0;

        $details = $this->scoringDetails()->with('part')->get();

        foreach ($details as $detail) {
            if ($detail->result === 'NOT OK') {
                // Check if part is critical
                if ($detail->part->critical === 'Y') {
                    $deduction += 4;
                } else {
                    $deduction += 1;
                }
            }
        }

        return $deduction;
    }

    /**
     * Calculate OK percentage with new formula
     * Formula: 101 - (total deduction points)
     */
    public function getOkPercentageAttribute()
    {
        $deduction = $this->getDeductionPoints();
        $percentage = ((101 - $deduction) / 101) * 100;

        // Ensure percentage is between 0 and 100
        return max(0, min(100, $percentage));
    }

    /**
     * Calculate NOT OK percentage
     */
    public function getNotOkPercentageAttribute()
    {
        return 100 - $this->ok_percentage;
    }

    /**
     * Get summary
     */
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
            'deduction_points' => $this->getDeductionPoints(),
        ];
    }

    /**
     * Calculate weekly machine score
     * This aggregates all process scorings for a machine in a week
     */
    public static function getWeeklyMachineScore($machineId, $weekStart, $weekEnd)
    {
        $scorings = self::with('scoringDetails.part')
            ->whereHas('machineProcess', function ($query) use ($machineId) {
                $query->where('machine_id', $machineId);
            })
            ->where('status', 'completed')
            ->whereBetween('scoring_date', [$weekStart, $weekEnd])
            ->get();

        if ($scorings->isEmpty()) {
            return 0;
        }

        $totalDeduction = 0;

        foreach ($scorings as $scoring) {
            $totalDeduction += $scoring->getDeductionPoints();
        }

        $percentage = ((101 - $totalDeduction) / 101) * 100;

        return round(max(0, min(100, $percentage)), 2);
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
