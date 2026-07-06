<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapacitorBankCapHistory extends Model
{
    use HasFactory;

    protected $table = 'capacitor_bank_cap_histories';

    /**
     * Insert-only — tidak ada update.
     * Setiap payload dari mesin yang mengandung data cap
     * langsung di-insert sebagai baris baru.
     */
    protected $fillable = [
        'tanggal',
        'cap1', 'cap2', 'cap3', 'cap4',
        'cap5', 'cap6', 'cap7', 'cap8',
        'cap9', 'cap10', 'cap11', 'cap12',
        'recorded_at',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'recorded_at' => 'datetime',
        'cap1'        => 'integer',
        'cap2'        => 'integer',
        'cap3'        => 'integer',
        'cap4'        => 'integer',
        'cap5'        => 'integer',
        'cap6'        => 'integer',
        'cap7'        => 'integer',
        'cap8'        => 'integer',
        'cap9'        => 'integer',
        'cap10'       => 'integer',
        'cap11'       => 'integer',
        'cap12'       => 'integer',
    ];

    // ── Scopes ──────────────────────────────────────────────

    /** Filter by date (Y-m-d). */
    public function scopeByDate($query, string $date)
    {
        return $query->where('tanggal', $date);
    }

    /** Filter by tanggal range. */
    public function scopeByDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('tanggal', [$from, $to]);
    }
}
