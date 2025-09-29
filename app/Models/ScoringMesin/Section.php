<?php

namespace App\Models\ScoringMesin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    /** @use HasFactory<\Database\Factories\ScoringMesin\SectionFactory> */
    use HasFactory;
    protected $fillable = ['process_parameter_id', 'name'];

    public function processParameter()
    {
        return $this->belongsTo(ProcessParameter::class);
    }

    public function parts()
    {
        return $this->hasMany(Part::class);
    }

}
