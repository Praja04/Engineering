<?php

namespace App\Models\ScoringMesin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'status',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    // Scope for active machines
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for maintenance machines
    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    // Scope for broken machines
    public function scopeBroken($query)
    {
        return $query->where('status', 'broken');
    }

    // Relationship with ProcessParameter
    public function processParameters()
    {
        return $this->hasMany(ProcessParameter::class);
    }

    // Accessor for status badge class
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'active' => 'bg-success',
            'maintenance' => 'bg-warning',
            'broken' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    // Accessor for status text
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'maintenance' => 'Maintenance',
            'broken' => 'Rusak',
            default => 'Unknown'
        };
    }
}
