<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcDieselP2hInspectionModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_diesel_p2h_inspections';

    protected $fillable = [
        'mesin_id',
        'mtc_main_id',
        'no_unit',
        'shift',
        'catatan',

        'klakson',
        'buzzer_back',
        'oli_mesin',
        'radiator_hose',
        'water_pump',
        'injection_system',
        'fan_vbelt',
        'turbocharger_manifold',
        'tensioner_belt',
        'starting_motor',
        'alternator',
        'control_display',
        'oli_transmisi',
        'aki',
        'engine_mounting',
        'filter_oli_transmisi',
        'fungsi_rem',
        'fungsi_kopling',
        'oli_hydraulic',
        'hydraulic_system',
        'steering_system',
        'body_back_rest',
        'kaca_spion',
        'bucket_pin',
        'dump_pin_bushing',
        'seal_hydraulic',
        'roda_ban_baut',
        'lampu_unit',
        'baut_bearing_molen',
        'baut_hanger_as',
        'baut_grease',
        'katup_pembuangan_angin',
        'hours_meter',
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
