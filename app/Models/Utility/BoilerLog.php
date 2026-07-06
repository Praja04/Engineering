<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoilerLog extends Model
{
    use HasFactory;

    protected $table = 'boiler_logs';

    protected $fillable = [
        'waktu',

        // Steam & Pressure
        'PVSteam',
        'FeedPressure',
        'Press_Pasteur',

        // Water
        'LevelFeedWater',
        'InletWaterFlow',
        'OutletSteamFlow',
        'SuhuFeedTank',

        // Fan
        'IDFan',
        'LHFDFan',
        'RHFDFan',

        // Stoker
        'LHStoker',
        'RHStoker',

        // Temperature
        'LHTemp',
        'RHTemp',

        // Gas
        'O2',
        'CO2',

        // Guillotine
        'LHGuiloutine',
        'RHGuiloutine',

        // Additional columns
        'WaterPump1',
        'WaterPump2',
        'Batubara_FK',
        'Steam_FK',
    ];

    protected $casts = [
        'waktu' => 'datetime',

        'PVSteam' => 'decimal:2',
        'FeedPressure' => 'decimal:2',
        'Press_Pasteur' => 'decimal:2',

        'LevelFeedWater' => 'decimal:2',
        'InletWaterFlow' => 'decimal:2',
        'OutletSteamFlow' => 'decimal:2',
        'SuhuFeedTank' => 'decimal:2',

        'IDFan' => 'decimal:2',
        'LHFDFan' => 'decimal:2',
        'RHFDFan' => 'decimal:2',

        'LHStoker' => 'decimal:2',
        'RHStoker' => 'decimal:2',

        'LHTemp' => 'decimal:2',
        'RHTemp' => 'decimal:2',

        'O2' => 'decimal:2',
        'CO2' => 'decimal:2',

        'LHGuiloutine' => 'decimal:2',
        'RHGuiloutine' => 'decimal:2',

        'WaterPump1' => 'decimal:2',
        'WaterPump2' => 'decimal:2',
        'Batubara_FK' => 'decimal:3',
        'Steam_FK' => 'decimal:3',
    ];

    // ── SCOPES ─────────────────────────────────────────────

    /**
     * Filter berdasarkan tanggal (1 hari penuh)
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('waktu', $date);
    }

    /**
     * Filter berdasarkan range waktu
     */
    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('waktu', [$start, $end]);
    }

    /**
     * Filter berdasarkan bulan & tahun
     */
    public function scopeByBulan($query, int $bulan, int $tahun)
    {
        return $query->whereMonth('waktu', $bulan)
            ->whereYear('waktu', $tahun);
    }

    // ── ACCESSOR (OPTIONAL, tapi berguna) ──────────────────

    /**
     * Deteksi anomali sederhana (contoh)
     */
    public function getIsAnomalyAttribute(): bool
    {
        return ($this->PVSteam !== null && $this->PVSteam <= 0)
            || ($this->O2 !== null && $this->O2 > 15);
    }
}
