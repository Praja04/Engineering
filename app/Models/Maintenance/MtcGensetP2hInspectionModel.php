<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcGensetP2hInspectionModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_genset_p2h_inspections';

    protected $fillable = [
        'mesin_id',
        'mtc_main_id',
        'no_unit',
        'shift',
        'catatan',
        'hours_meter',

        'level_oli_mesin',
        'kebocoran_oli_mesin',
        'level_coolant_radiator',
        'kebocoran_coolant',
        'level_bahan_bakar',
        'kebocoran_bahan_bakar',
        'kondisi_aki_baterai',
        'tegangan_baterai',
        'filter_udara',
        'kondisi_panel_genset',
        'emergency_stop',
        'suara_mesin_running',
        'kebersihan_area_genset',
        'kondisi_knalpot_exhaust',
    ];

    /* ================= RELATION ================= */

    public function main()
    {
        return $this->belongsTo(MtcMainModel::class, 'mtc_main_id');
    }

    public function mesin()
    {
        return $this->belongsTo(MtcMasterMesinModel::class, 'mesin_id');
    }
}
