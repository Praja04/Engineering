<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwtpPerformanceWeek extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'week_start',
        'week_end',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end'   => 'date',
    ];

    /**
     * Relasi: satu minggu memiliki banyak record jenis proses
     */
    public function records()
    {
        return $this->hasMany(WwtpPerformanceRecord::class, 'performance_week_id');
    }

    /**
     * Scope: filter by year
     */
    public function scopeYear($query, $year)
    {
        return $query->whereYear('week_start', $year);
    }

    /**
     * Scope: find a specific week by date
     */
    public function scopeByDate($query, $date)
    {
        return $query->where('week_start', '<=', $date)
            ->where('week_end', '>=', $date);
    }
}
