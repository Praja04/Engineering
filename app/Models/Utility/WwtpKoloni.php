<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwtpKoloni extends Model
{
    use HasFactory;

    protected $table = 'wwtp_koloni';

    protected $fillable = [
        'week_start',
        'week_end',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end'   => 'date',
    ];

    public function details()
    {
        return $this->hasMany(WwtpKoloniDetail::class, 'wwtp_koloni_id');
    }
}
