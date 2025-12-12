<?php

namespace App\Models\ScoringMesin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoringDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_scoring_id',
        'part_id',
        'result', // OK, NOT OK
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with MachineScoring
    public function machineScoring()
    {
        return $this->belongsTo(MachineScoring::class);
    }

    // Relationship with Part
    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    // Scope for OK results
    public function scopeOk($query)
    {
        return $query->where('result', 'OK');
    }

    // Scope for NOT OK results
    public function scopeNotOk($query)
    {
        return $query->where('result', 'NOT OK');
    }

    // Check if result is OK
    public function isOk()
    {
        return $this->result === 'OK';
    }

    // Check if result is NOT OK
    public function isNotOk()
    {
        return $this->result === 'NOT OK';
    }

    // Get result badge class
    public function getResultBadgeClassAttribute()
    {
        return match ($this->result) {
            'OK' => 'bg-success',
            'NOT OK' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    // Get result text
    public function getResultTextAttribute()
    {
        return $this->result;
    }
}
