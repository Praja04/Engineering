<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwtpInfluent extends Model
{
    //
    use HasFactory;

    protected $table = 'wwtp_influent';

    protected $fillable = [
        'wwtp_record_id',
        'pit_sparta',
        'pit_garam',
        'pit_domestik',
    ];

    // Relasi ke Record (N : 1)
    public function record()
    {
        return $this->belongsTo(WwtpRecord::class, 'wwtp_record_id');
    }
}
