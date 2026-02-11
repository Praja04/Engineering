<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MtcElectricEngineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mesin_id' => ['required', 'exists:mtc_master_mesin,id'],

            // Forklift Electrical - General
            'check_buzzer_back' => ['nullable', 'boolean'],
            'check_klakson' => ['nullable', 'boolean'],
            'check_pilot_lamp' => ['nullable', 'boolean'],
            'check_lampu_sorot' => ['nullable', 'boolean'],
            'check_lampu_kombinasi_kanan_belakang' => ['nullable', 'boolean'],
            'check_lampu_kombinasi_kiri_belakang' => ['nullable', 'boolean'],
            'check_kaca_sepion' => ['nullable', 'boolean'],

            // Battery, Charger & Electrical System
            'check_battery' => ['nullable', 'boolean'],
            'check_skun_battery' => ['nullable', 'boolean'],
            'check_terminal_charger_battery' => ['nullable', 'boolean'],
            'check_kunci_kontak' => ['nullable', 'boolean'],
            'check_main_contactor' => ['nullable', 'boolean'],
            'check_microswitch' => ['nullable', 'boolean'],
            'check_eps_controller' => ['nullable', 'boolean'],
            'check_steering_motor' => ['nullable', 'boolean'],
            'check_fan' => ['nullable', 'boolean'],
            'check_fuse' => ['nullable', 'boolean'],
            'check_display_control' => ['nullable', 'boolean'],
            'check_wiring_terminal' => ['nullable', 'boolean'],
            'check_carbon_brush' => ['nullable', 'boolean'],

            // Drive, Steering, Mast, Hydraulic & Braking System
            'check_steering_wheel' => ['nullable', 'boolean'],
            'check_baut_roda' => ['nullable', 'boolean'],
            'check_drive_caster_load_wheel' => ['nullable', 'boolean'],
            'check_lift_chain' => ['nullable', 'boolean'],
            'check_lift_bracket' => ['nullable', 'boolean'],
            'check_hydraulic_hose' => ['nullable', 'boolean'],
            'check_motor_hydraulic_pump' => ['nullable', 'boolean'],
            'check_fork' => ['nullable', 'boolean'],
            'check_lift_rollers' => ['nullable', 'boolean'],
            'check_mast_rollers' => ['nullable', 'boolean'],
            'check_lift_cylinders' => ['nullable', 'boolean'],
            'check_tilt_cylinders' => ['nullable', 'boolean'],
            'check_control_valve' => ['nullable', 'boolean'],
            'check_hydraulic_tank' => ['nullable', 'boolean'],
            'check_overhead_guard' => ['nullable', 'boolean'],
            'check_all_bolt_nut' => ['nullable', 'boolean'],
            'check_power_steering' => ['nullable', 'boolean'],
            'check_brake_cam_adjust_bolt' => ['nullable', 'boolean'],
            'check_axle' => ['nullable', 'boolean'],
            'check_greasing_point' => ['nullable', 'boolean'],
            'check_air_spring' => ['nullable', 'boolean'],

            // Oil
            'ganti_gear_oil' => ['nullable', 'boolean'],
            'ganti_hydraulic_oil' => ['nullable', 'boolean'],
            'ganti_return_filter' => ['nullable', 'boolean'],
            'ganti_brake_oil' => ['nullable', 'boolean'],

            // // Catatan
            // 'keterangan' => ['nullable', 'string'],
            // 'korektif'   => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {

            $checklistFields = [
                // General
                'check_buzzer_back',
                'check_klakson',
                'check_pilot_lamp',
                'check_lampu_sorot',
                'check_lampu_kombinasi_kanan_belakang',
                'check_lampu_kombinasi_kiri_belakang',
                'check_kaca_sepion',

                // Battery & Electrical
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

                // Mechanical
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

                // Oil
                'ganti_gear_oil',
                'ganti_hydraulic_oil',
                'ganti_return_filter',
                'ganti_brake_oil',
            ];

            $hasAnyChecked = false;
            foreach ($checklistFields as $field) {
                if ($this->boolean($field)) {
                    $hasAnyChecked = true;
                    break;
                }
            }

            if (! $hasAnyChecked) {
                $validator->errors()->add('checklist', 'Minimal pilih 1 item pengecekan Electric Engine.');
            }
        });
    }
}
