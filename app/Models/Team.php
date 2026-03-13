<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Ejo\EjoTeamAssign;
use App\Models\Ejo\EjoTicket;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';

    protected $fillable = [
        'team_name',
        'department',
        'ejo_id'
    ];

    /**
     * EJO ticket yang dikerjakan tim ini
     */
    public function ticket()
    {
        return $this->belongsTo(EjoTicket::class, 'ejo_id');
    }
}