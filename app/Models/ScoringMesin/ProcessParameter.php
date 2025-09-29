<?php

namespace App\Models\ScoringMesin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessParameter extends Model
{
    /** @use HasFactory<\Database\Factories\ScoringMesin\ProcessParameterFactory> */
    use HasFactory;
    protected $fillable = ['machine_id', 'name'];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

}
