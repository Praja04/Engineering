<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Model;

class MtcMesinFrekuensiModel extends Model
{
    //
    protected $table = 'mtc_mesin_frekuensi';

    protected $fillable = [
        'mesin_id',
        'interval',
        'satuan',
    ];

    protected $casts = [
        'interval' => 'integer',
    ];

    // ── Accessor: label ringkas, misal "2 Bulan", "1 Minggu" ────────────────
    public function getLabelAttribute(): string
    {
        return $this->interval . ' ' . ucfirst($this->satuan);
    }

    // ── Relation ─────────────────────────────────────────────────────────────
    public function mesin()
    {
        return $this->belongsTo(MtcMasterMesinModel::class, 'mesin_id');
    }
}
