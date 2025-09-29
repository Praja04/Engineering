<?php

namespace App\Models\ScoringMesin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandardState extends Model
{
    /** @use HasFactory<\Database\Factories\ScoringMesin\StandardStateFactory> */
    use HasFactory;
    protected $fillable = ['part_id', 'value', 'unit'];

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

}
