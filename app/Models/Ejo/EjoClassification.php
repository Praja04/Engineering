<?php

namespace App\Models\Ejo;

use Illuminate\Database\Eloquent\Model;

class EjoClassification extends Model
{
    protected $table = 'ejo_classifications';

    protected $fillable = [
        'type_id',
        'name'
    ];

    public function type()
    {
        return $this->belongsTo(EjoType::class, 'type_id');
    }

    public function tickets()
    {
        return $this->hasMany(EjoTicket::class, 'classification_id');
    }
}
