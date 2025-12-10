<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwtpEffluent extends Model
{

    use HasFactory;

    protected $table = 'wwtp_effluent';

    protected $fillable = [
        'wwtp_record_id',
        'full_proses',
        'daf_pre',
    ];

    // Relasi ke Record
    public function record()
    {
        return $this->belongsTo(WwtpRecord::class, 'wwtp_record_id');
    }
}
