<?php

namespace App\Models\Ejo;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EjoProgress extends Model
{
    protected $table = 'ejo_progress';

    protected $fillable = [
        'ejo_id',
        'progress_percent',
        'progress_note',
        'updated_by'
    ];

    public function ticket()
    {
        return $this->belongsTo(EjoTicket::class, 'ejo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
