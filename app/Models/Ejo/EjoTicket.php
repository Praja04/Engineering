<?php

namespace App\Models\Ejo;

use Illuminate\Database\Eloquent\Model;

class EjoTicket extends Model
{
    protected $table = 'ejo_tickets';

    protected $fillable = [
        'ticket_id',
        'os_in',
        'department',
        'request_date',
        'category',
        'module',
        'subject',
        'description',
        'requestor',
        'status',
        'type',
        'schedule',
        'est_time',
        'date_done',
        'classification_id'
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'schedule' => 'date',
        'date_done' => 'date'
    ];

    public function classification()
    {
        return $this->belongsTo(EjoClassification::class, 'classification_id');
    }

    public function progress()
    {
        return $this->hasMany(EjoProgress::class, 'ejo_id');
    }

    public function notes()
    {
        return $this->hasMany(EjoNote::class, 'ejo_id');
    }

    public function attachments()
    {
        return $this->hasMany(EjoAttachment::class, 'ejo_id');
    }

    public function teams()
    {
        return $this->hasMany(EjoTeamAssign::class, 'ejo_id');
    }
}
