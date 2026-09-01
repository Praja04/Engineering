<?php

namespace App\Models\EjoEngineer;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'ejo_engineer_notifications';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'target_username',
        'ejo_id',
        'message',
        'timestamp',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
