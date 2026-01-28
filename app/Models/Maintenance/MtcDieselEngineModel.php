<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcDieselEngineModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_diesel_engine';

    protected $fillable = [
        'nama_mesin',
        'tanggal',
        'waktu',
        'paket',

        // ======================
        // ENGINE
        // ======================
        'check_kondisi_level_oli_mesin',
        'check_kondisi_radiator_hose',
        'check_kondisi_level_air_radiator',
        'check_water_pump',
        'check_injection_pump_injector_piping',
        'check_turbocharger_manifold',
        'check_fan_v_belt',
        'check_automatic_tensioner_belt',
        'check_engine_mounting',
        'check_air_filter_condition',
        'check_clearence_valve_drain_valve',
        'check_engine_oil_filter',
        'check_air_radiator',
        'check_minyak_kopling',
        'check_fuel_filter',

        // ======================
        // ELECTRIC
        // ======================
        'check_kondisi_aki_level_air_aki',
        'check_fungsi_starting_motor',
        'check_fungsi_alternator',
        'check_sensor_sensor_gauge',
        'check_fuse_control_switch',
        'check_control_display',
        'check_indicator_wiring',

        // ======================
        // TRANSMISI / BRAKE / DRIVE SHAFT
        // ======================
        'check_kondisi_level_oli_transmisi',
        'check_fungsi_transmisi',
        'check_filter_oli_transmisi',
        'check_fungsi_rem',
        'check_oli_tidak_ada_yang_bocor',
        'check_kondisi_drive_shaft',

        // ======================
        // HYDRAULIC
        // ======================
        'check_kondisi_level_hydraulic_oil',
        'check_kondisi_hydraulic_oil_filter',
        'check_fungsi_hydraulic_system',
        'check_fungsi_steering_system',
        'check_kondisi_hydraulic_cylinder',
        'check_kondisi_steering_cylinder',
        'check_kondisi_axle_oil',
        'check_kondisi_baut_roda_hydraulic',
        'check_kondisi_bucket_pin_bucket',
        'check_kondisi_dump_pin_bushing',

        // ======================
        // GENERAL
        // ======================
        'check_klakson',
        'check_buzzer_back',
        'check_kondisi_basket_fresh_body',
        'check_kaca_sepion',
        'check_kondisi_roda_ban',
        'check_baut_roda_general',
        'check_lampu_depan_kanan',
        'check_lampu_depan_kiri',
        'check_baut_bearing_molen',
        'check_baut_hanger_as_roda',

        // Catatan
        'keterangan',
        'korektif',

        // Audit
        'created_by',
    ];

    /**
     * Casting boolean supaya konsisten true/false
     */
    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'waktu'   => 'datetime:H:i:s',

        // Engine
        'check_kondisi_level_oli_mesin' => 'boolean',
        'check_kondisi_radiator_hose' => 'boolean',
        'check_kondisi_level_air_radiator' => 'boolean',
        'check_water_pump' => 'boolean',
        'check_injection_pump_injector_piping' => 'boolean',
        'check_turbocharger_manifold' => 'boolean',
        'check_fan_v_belt' => 'boolean',
        'check_automatic_tensioner_belt' => 'boolean',
        'check_engine_mounting' => 'boolean',
        'check_air_filter_condition' => 'boolean',
        'check_clearence_valve_drain_valve' => 'boolean',
        'check_engine_oil_filter' => 'boolean',
        'check_air_radiator' => 'boolean',
        'check_minyak_kopling' => 'boolean',
        'check_fuel_filter' => 'boolean',

        // Electric
        'check_kondisi_aki_level_air_aki' => 'boolean',
        'check_fungsi_starting_motor' => 'boolean',
        'check_fungsi_alternator' => 'boolean',
        'check_sensor_sensor_gauge' => 'boolean',
        'check_fuse_control_switch' => 'boolean',
        'check_control_display' => 'boolean',
        'check_indicator_wiring' => 'boolean',

        // Transmisi
        'check_kondisi_level_oli_transmisi' => 'boolean',
        'check_fungsi_transmisi' => 'boolean',
        'check_filter_oli_transmisi' => 'boolean',
        'check_fungsi_rem' => 'boolean',
        'check_oli_tidak_ada_yang_bocor' => 'boolean',
        'check_kondisi_drive_shaft' => 'boolean',

        // Hydraulic
        'check_kondisi_level_hydraulic_oil' => 'boolean',
        'check_kondisi_hydraulic_oil_filter' => 'boolean',
        'check_fungsi_hydraulic_system' => 'boolean',
        'check_fungsi_steering_system' => 'boolean',
        'check_kondisi_hydraulic_cylinder' => 'boolean',
        'check_kondisi_steering_cylinder' => 'boolean',
        'check_kondisi_axle_oil' => 'boolean',
        'check_kondisi_baut_roda_hydraulic' => 'boolean',
        'check_kondisi_bucket_pin_bucket' => 'boolean',
        'check_kondisi_dump_pin_bushing' => 'boolean',

        // General
        'check_klakson' => 'boolean',
        'check_buzzer_back' => 'boolean',
        'check_kondisi_basket_fresh_body' => 'boolean',
        'check_kaca_sepion' => 'boolean',
        'check_kondisi_roda_ban' => 'boolean',
        'check_baut_roda_general' => 'boolean',
        'check_lampu_depan_kanan' => 'boolean',
        'check_lampu_depan_kiri' => 'boolean',
        'check_baut_bearing_molen' => 'boolean',
        'check_baut_hanger_as_roda' => 'boolean',
    ];

    /**
     * Relasi ke user pembuat
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
