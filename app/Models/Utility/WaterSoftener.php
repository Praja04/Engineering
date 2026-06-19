<?php

namespace App\Models\Utility;

use App\Models\User;
use App\Models\Utility\WaterSoftener;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterSoftener extends Model
{
    use HasFactory;

    protected $table = 'water_softeners';

    protected $fillable = [
        'tanggal',

        // WS 1
        'ws1_jam',
        'ws1_hardness_in',
        'ws1_hardness_out',
        'ws1_flow',

        // WS 2
        'ws2_jam',
        'ws2_hardness_in',
        'ws2_hardness_out',
        'ws2_flow',

        // Regen 1
        'regen1_jam',
        'regen1_air_pelarut',
        'regen1_garam',
        'regen1_nomer_ws',

        // Regen 2
        'regen2_jam',
        'regen2_air_pelarut',
        'regen2_garam',
        'regen2_nomer_ws',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        // 'tanggal'            => 'date',

        'ws1_hardness_in'    => 'decimal:2',
        'ws1_hardness_out'   => 'decimal:2',
        'ws1_flow'           => 'decimal:2',

        'ws2_hardness_in'    => 'decimal:2',
        'ws2_hardness_out'   => 'decimal:2',
        'ws2_flow'           => 'decimal:2',

        'regen1_air_pelarut' => 'decimal:2',
        'regen1_garam'       => 'decimal:2',
        'regen1_nomer_ws'    => 'integer',

        'regen2_air_pelarut' => 'decimal:2',
        'regen2_garam'       => 'decimal:2',
        'regen2_nomer_ws'    => 'integer',
    ];

    // ── Accessors ────────────────────────────────────────────────

    /**
     * Cek apakah WS 1 hardness out melebihi standar (max 10 ppm)
     */
    public function getWs1HardnessOutStatusAttribute(): string
    {
        if (is_null($this->ws1_hardness_out)) return 'N/A';
        return $this->ws1_hardness_out <= 10 ? 'normal' : 'over';
    }

    /**
     * Cek apakah WS 2 hardness out melebihi standar (max 10 ppm)
     */
    public function getWs2HardnessOutStatusAttribute(): string
    {
        if (is_null($this->ws2_hardness_out)) return 'N/A';
        return $this->ws2_hardness_out <= 10 ? 'normal' : 'over';
    }

    // ── Scopes ───────────────────────────────────────────────────

    /**
     * Filter berdasarkan bulan dan tahun
     * Contoh: WaterSoftener::byBulan(1, 2025)->get()
     */
    public function scopeByBulan($query, int $bulan, int $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);
    }

    /**
     * Filter data yang hardness out WS 1 atau WS 2 melebihi standar
     */
    public function scopeOverStandard($query)
    {
        return $query->where(function ($q) {
            $q->where('ws1_hardness_out', '>', 10)
                ->orWhere('ws2_hardness_out', '>', 10);
        });
    }

    /**
     * Filter data yang ada regenerasinya
     */
    public function scopeHasRegen($query)
    {
        return $query->whereNotNull('regen1_jam')
            ->orWhereNotNull('regen2_jam');
    }


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
