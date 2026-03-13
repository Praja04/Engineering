<?php

namespace App\Models\Ejo;

use Illuminate\Database\Eloquent\Model;

class EjoType extends Model
{
    protected $table = 'ejo_types';

    protected $fillable = [
        'name'
    ];

    public function classifications()
    {
        return $this->hasMany(EjoClassification::class, 'type_id');
    }
}
