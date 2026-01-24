<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MtcDieselEngineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    public function rules(): array
    {
        return [
            'nama_mesin' => ['required', 'string', 'max:255'],
            'tanggal'    => ['required', 'date'],
            'paket'      => ['nullable', 'string', 'max:255'],

            // ======================
            // ENGINE
            // ======================
            'check_kondisi_level_oli_mesin'           => ['nullable', 'boolean'],
            'check_kondisi_radiator_hose'             => ['nullable', 'boolean'],
            'check_kondisi_level_air_radiator'        => ['nullable', 'boolean'],
            'check_water_pump'                        => ['nullable', 'boolean'],
            'check_injection_pump_injector_piping'    => ['nullable', 'boolean'],
            'check_turbocharger_manifold'             => ['nullable', 'boolean'],
            'check_fan_v_belt'                        => ['nullable', 'boolean'],
            'check_automatic_tensioner_belt'          => ['nullable', 'boolean'],
            'check_engine_mounting'                   => ['nullable', 'boolean'],
            'check_air_filter_condition'              => ['nullable', 'boolean'],
            'check_clearence_valve_drain_valve'       => ['nullable', 'boolean'],
            'check_engine_oil_filter'                 => ['nullable', 'boolean'],
            'check_air_radiator'                      => ['nullable', 'boolean'],
            'check_minyak_kopling'                    => ['nullable', 'boolean'],
            'check_fuel_filter'                       => ['nullable', 'boolean'],

            // ======================
            // ELECTRIC
            // ======================
            'check_kondisi_aki_level_air_aki'         => ['nullable', 'boolean'],
            'check_fungsi_starting_motor'             => ['nullable', 'boolean'],
            'check_fungsi_alternator'                 => ['nullable', 'boolean'],
            'check_sensor_sensor_gauge'               => ['nullable', 'boolean'],
            'check_fuse_control_switch'               => ['nullable', 'boolean'],
            'check_control_display'                   => ['nullable', 'boolean'],
            'check_indicator_wiring'                  => ['nullable', 'boolean'],

            // ======================
            // TRANSMISI / BRAKE / DRIVE SHAFT
            // ======================
            'check_kondisi_level_oli_transmisi'       => ['nullable', 'boolean'],
            'check_fungsi_transmisi'                  => ['nullable', 'boolean'],
            'check_filter_oli_transmisi'              => ['nullable', 'boolean'],
            'check_fungsi_rem'                        => ['nullable', 'boolean'],
            'check_oli_tidak_ada_yang_bocor'          => ['nullable', 'boolean'],
            'check_kondisi_drive_shaft'               => ['nullable', 'boolean'],

            // ======================
            // HYDRAULIC
            // ======================
            'check_kondisi_level_hydraulic_oil'       => ['nullable', 'boolean'],
            'check_kondisi_hydraulic_oil_filter'      => ['nullable', 'boolean'],
            'check_fungsi_hydraulic_system'           => ['nullable', 'boolean'],
            'check_fungsi_steering_system'            => ['nullable', 'boolean'],
            'check_kondisi_hydraulic_cylinder'        => ['nullable', 'boolean'],
            'check_kondisi_steering_cylinder'         => ['nullable', 'boolean'],
            'check_kondisi_axle_oil'                  => ['nullable', 'boolean'],
            'check_kondisi_baut_roda_hydraulic'       => ['nullable', 'boolean'],
            'check_kondisi_bucket_pin_bucket'         => ['nullable', 'boolean'],
            'check_kondisi_dump_pin_bushing'          => ['nullable', 'boolean'],

            // ======================
            // GENERAL
            // ======================
            'check_klakson'                           => ['nullable', 'boolean'],
            'check_buzzer_back'                       => ['nullable', 'boolean'],
            'check_kondisi_basket_fresh_body'         => ['nullable', 'boolean'],
            'check_kaca_sepion'                       => ['nullable', 'boolean'],
            'check_kondisi_roda_ban'                  => ['nullable', 'boolean'],
            'check_baut_roda_general'                 => ['nullable', 'boolean'],
            'check_lampu_depan_kanan'                 => ['nullable', 'boolean'],
            'check_lampu_depan_kiri'                  => ['nullable', 'boolean'],
            'check_baut_bearing_molen'                => ['nullable', 'boolean'],
            'check_baut_hanger_as_roda'               => ['nullable', 'boolean'],

            // Catatan
            'keterangan' => ['nullable', 'string'],
            'korektif'   => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'Field :attribute wajib diisi.',
            '*.boolean'  => 'Field :attribute harus bernilai true / false.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {

            $checklistFields = [
                // Engine
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

                // Electric
                'check_kondisi_aki_level_air_aki',
                'check_fungsi_starting_motor',
                'check_fungsi_alternator',
                'check_sensor_sensor_gauge',
                'check_fuse_control_switch',
                'check_control_display',
                'check_indicator_wiring',

                // Transmisi
                'check_kondisi_level_oli_transmisi',
                'check_fungsi_transmisi',
                'check_filter_oli_transmisi',
                'check_fungsi_rem',
                'check_oli_tidak_ada_yang_bocor',
                'check_kondisi_drive_shaft',

                // Hydraulic
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

                // General
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
            ];

            $hasAnyChecked = false;
            foreach ($checklistFields as $field) {
                if ($this->boolean($field)) {
                    $hasAnyChecked = true;
                    break;
                }
            }

            if (! $hasAnyChecked) {
                $validator->errors()->add('checklist', 'Minimal pilih 1 item pengecekan Diesel Engine.');
            }
        });
    }
}
