<?php

namespace App\Models\Ejo;

use Illuminate\Database\Eloquent\Model;
use App\Models\Team;
use App\Models\User;

class EjoTeamAssign extends Model
{
    protected $table = 'ejo_team_assign';

    protected $fillable = [
        'ejo_id',
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
