<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CapacitorBank extends Model
{
    use HasFactory;

    protected $table = 'capacitor_banks';

    protected $fillable = [
        'tanggal',
        'jam',
        'arus_total',

        // Capasitor A
        'cap_a_nomor',
        'cap_a_i1',
        'cap_a_i2',
        'cap_a_i3',

        // Capasitor B
        'cap_b_nomor',
        'cap_b_i1',
        'cap_b_i2',
        'cap_b_i3',

        // Capasitor C
        'cap_c_nomor',
        'cap_c_i1',
        'cap_c_i2',
        'cap_c_i3',

        'suhu_ruang',
    ];

    protected $casts = [
        'arus_total' => 'decimal:2',

        'cap_a_i1' => 'decimal:2',
        'cap_a_i2' => 'decimal:2',
        'cap_a_i3' => 'decimal:2',

        'cap_b_i1' => 'decimal:2',
        'cap_b_i2' => 'decimal:2',
        'cap_b_i3' => 'decimal:2',

        'cap_c_i1' => 'decimal:2',
        'cap_c_i2' => 'decimal:2',
        'cap_c_i3' => 'decimal:2',

        'suhu_ruang' => 'decimal:2',
    ];

    // ── ACCESSOR ─────────────────────────────

    public function getCapAStatusAttribute()
    {
        return $this->checkBalance(
            $this->cap_a_i1,
            $this->cap_a_i2,
            $this->cap_a_i3
        );
    }

    public function getCapBStatusAttribute()
    {
        return $this->checkBalance(
            $this->cap_b_i1,
            $this->cap_b_i2,
            $this->cap_b_i3
        );
    }

    public function getCapCStatusAttribute()
    {
        return $this->checkBalance(
            $this->cap_c_i1,
            $this->cap_c_i2,
            $this->cap_c_i3
        );
    }

    private function checkBalance($i1, $i2, $i3)
    {
        if (is_null($i1) || is_null($i2) || is_null($i3)) {
            return 'N/A';
        }

        $max = max($i1, $i2, $i3);
        $min = min($i1, $i2, $i3);

        return (($max - $min) <= ($max * 0.1))
            ? 'balance'
            : 'unbalance';
    }

    // ── SCOPES ───────────────────────────────

    public function scopeByBulan($query, $bulan, $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);
    }

    public function scopeUnbalance($query)
    {
        return $query->get()->filter(function ($item) {
            return $item->cap_a_status === 'unbalance'
                || $item->cap_b_status === 'unbalance'
                || $item->cap_c_status === 'unbalance';
        });
    }
}
