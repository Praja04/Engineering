<?php

namespace App\Models\ScoringMesin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessParameter extends Model
{
    /** @use HasFactory<\Database\Factories\ScoringMesin\ProcessParameterFactory> */
    use HasFactory;
    protected $fillable = ['name'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
    
    public function machineProcesses()
    {
        return $this->hasMany(MachineProcess::class);
    }

    // Relationship with Machine through MachineProcess
    public function machines()
    {
        return $this->belongsToMany(
            Machine::class,
            'machine_processes',
            'process_parameter_id',
            'machine_id'
        )->withPivot('catatan')->withTimestamps();
    }
}
