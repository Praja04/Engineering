<?php

namespace App\Models\Ejo;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EjoNote extends Model
{
    protected $table = 'ejo_notes';

    protected $fillable = [
        'ejo_id',
        'note',
        'user_id'
    ];

    public function ticket()
    {
        return $this->belongsTo(EjoTicket::class, 'ejo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
