<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcElectricEngineModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_electric_engine_inspections';

    protected $fillable = [
        'mesin_id',
        'mtc_main_id',

        // Forklift Electrical - General
        'check_buzzer_back',
        'check_klakson',
        'check_pilot_lamp',
        'check_lampu_sorot',
        'check_lampu_kombinasi_kanan_belakang',
        'check_lampu_kombinasi_kiri_belakang',
        'check_kaca_sepion',

        // Battery, Charger & Electrical System
        'check_battery',
        'check_skun_battery',
        'check_terminal_charger_battery',
        'check_kunci_kontak',
        'check_main_contactor',
        'check_microswitch',
        'check_eps_controller',
        'check_steering_motor',
        'check_fan',
        'check_fuse',
        'check_display_control',
        'check_wiring_terminal',
        'check_carbon_brush',

        // Drive, Steering, Mast, Hydraulic & Braking System
        'check_steering_wheel',
        'check_baut_roda',
        'check_drive_caster_load_wheel',
        'check_lift_chain',
        'check_lift_bracket',
        'check_hydraulic_hose',
        'check_motor_hydraulic_pump',
        'check_fork',
        'check_lift_rollers',
        'check_mast_rollers',
        'check_lift_cylinders',
        'check_tilt_cylinders',
        'check_control_valve',
        'check_hydraulic_tank',
        'check_overhead_guard',
        'check_all_bolt_nut',
        'check_power_steering',
        'check_brake_cam_adjust_bolt',
        'check_axle',
        'check_greasing_point',
        'check_air_spring',
        'check_boot_steering',
        'check_wheel_chain',

        // Oil
        'ganti_gear_oil',
        'ganti_hydraulic_oil',
        'ganti_return_filter',
        'ganti_brake_oil',

        // Catatan
        // 'keterangan',
        // 'korektif',
    ];

    protected $casts = [
        // Forklift Electrical
        'check_buzzer_back' => 'boolean',
        'check_klakson' => 'boolean',
        'check_pilot_lamp' => 'boolean',
        'check_lampu_sorot' => 'boolean',
        'check_lampu_kombinasi_kanan_belakang' => 'boolean',
        'check_lampu_kombinasi_kiri_belakang' => 'boolean',
        'check_kaca_sepion' => 'boolean',

        // Battery & Electrical
        'check_battery' => 'boolean',
        'check_skun_battery' => 'boolean',
        'check_terminal_charger_battery' => 'boolean',
        'check_kunci_kontak' => 'boolean',
        'check_main_contactor' => 'boolean',
        'check_microswitch' => 'boolean',
        'check_eps_controller' => 'boolean',
        'check_steering_motor' => 'boolean',
        'check_fan' => 'boolean',
        'check_fuse' => 'boolean',
        'check_display_control' => 'boolean',
        'check_wiring_terminal' => 'boolean',
        'check_carbon_brush' => 'boolean',

        // Mechanical
        'check_steering_wheel' => 'boolean',
        'check_baut_roda' => 'boolean',
        'check_drive_caster_load_wheel' => 'boolean',
        'check_lift_chain' => 'boolean',
        'check_lift_bracket' => 'boolean',
        'check_hydraulic_hose' => 'boolean',
        'check_motor_hydraulic_pump' => 'boolean',
        'check_fork' => 'boolean',
        'check_lift_rollers' => 'boolean',
        'check_mast_rollers' => 'boolean',
        'check_lift_cylinders' => 'boolean',
        'check_tilt_cylinders' => 'boolean',
        'check_control_valve' => 'boolean',
        'check_hydraulic_tank' => 'boolean',
        'check_overhead_guard' => 'boolean',
        'check_all_bolt_nut' => 'boolean',
        'check_power_steering' => 'boolean',
        'check_brake_cam_adjust_bolt' => 'boolean',
        'check_axle' => 'boolean',
        'check_greasing_point' => 'boolean',
        'check_air_spring' => 'boolean',
        'check_boot_steering' => 'boolean',
        'check_wheel_chain' => 'boolean',

        // Oil
        'ganti_gear_oil' => 'boolean',
        'ganti_hydraulic_oil' => 'boolean',
        'ganti_return_filter' => 'boolean',
        'ganti_brake_oil' => 'boolean',
    ];

    public function main()
    {
        return $this->belongsTo(MtcMainModel::class, 'mtc_main_id');
    }

    public function mesin()
    {
        return $this->belongsTo(MtcMasterMesinModel::class, 'mesin_id');
    }
}
