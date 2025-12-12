<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class WwtpPerformanceRecord extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'performance_week_id',
        'jenis',
        'tss',
        'cod',
        'foto',
    ];

    /**
     * Relasi ke minggu
     */
    public function week()
    {
        return $this->belongsTo(WwtpPerformanceWeek::class, 'performance_week_id');
    }

    /**
     * Scope: filter by jenis (equal, aerob, outlet, dll)
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }
}
