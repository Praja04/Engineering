<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcElectricP2hInspectionModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_electric_p2h_inspections';

    protected $fillable = [
        'mtc_main_id',
        'no_unit',
        'shift',
        'catatan',

        'level_minyak_rem',
        'level_oli_hydraulic',
        'isi_air_aki',
        'baterai',
        'hydraulic_system',
        'selang_hydraulic',
        'lift_chains',
        'fork',
        'body_unit',
        'lampu_kombinasi_kiri',
        'lampu_kombinasi_kanan',
        'lampu_sorot',
        'lampu_sign_depan_kanan',
        'lampu_sign_depan_kiri',
        'klakson',
        'buzzer_back',
        'kaca_spion',
        'baut_roda',
        'ban',
        'kebersihan_unit',
        'panel_display',
        'sistem_kemudi',
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
