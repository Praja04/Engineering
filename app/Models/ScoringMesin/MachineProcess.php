<?php

namespace App\Models\ScoringMesin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id',
        'process_parameter_id',
        'catatan'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with Machine
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    // Relationship with ProcessParameter
    public function processParameter()
    {
        return $this->belongsTo(ProcessParameter::class);
    }

    // Accessor untuk mendapatkan status dari machine
    public function getStatusAttribute()
    {
        return $this->machine->status ?? null;
    }

    // Accessor untuk mendapatkan status badge dari machine
    public function getStatusBadgeClassAttribute()
    {
        return $this->machine->status_badge_class ?? 'bg-secondary';
    }

    // Accessor untuk mendapatkan status text dari machine
    public function getStatusTextAttribute()
    {
        return $this->machine->status_text ?? 'Unknown';
    }
    public function machineScorings()
    {
        return $this->hasMany(MachineScoring::class, 'machine_process_id');
    }

}
