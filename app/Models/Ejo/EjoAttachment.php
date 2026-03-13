<?php

namespace App\Models\Ejo;

use Illuminate\Database\Eloquent\Model;

class EjoAttachment extends Model
{
    protected $table = 'ejo_attachments';

    protected $fillable = [
        'ejo_id',
        'file_name',
        'file_path'
    ];

    public function ticket()
    {
        return $this->belongsTo(EjoTicket::class, 'ejo_id');
    }
}
