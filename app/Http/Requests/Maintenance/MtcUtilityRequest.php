<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MtcUtilityRequest extends FormRequest
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
            // 'nama_mesin' => ['required', 'string', 'max:255'],
            'mesin_id' => ['required', 'exists:mtc_master_mesin,id'],

            // Cooling Tower
            'cleaning_saringan_cooling_tower' => ['nullable', 'boolean'],
            'cleaning_unit_cooling_tower'     => ['nullable', 'boolean'],
            'cleaning_bak_cooling_tower'      => ['nullable', 'boolean'],

            // RO
            'check_sensor_tank_farm_ro_produk'        => ['nullable', 'boolean'],
            'cleaning_flow_rate_mmf_1'                => ['nullable', 'boolean'],
            'cleaning_flow_rate_mmf_2'                => ['nullable', 'boolean'],
            'cleaning_flow_rate_ro_produk'            => ['nullable', 'boolean'],
            'cleaning_flow_rate_ro_reject'            => ['nullable', 'boolean'],
            'penggantian_micron_filter_cip'           => ['nullable', 'boolean'],
            'penggantian_micron_filter_makeup_water'  => ['nullable', 'boolean'],
            'cleaning_cip_tank'                       => ['nullable', 'boolean'],
            'cip_membrane_reverse_osmosis'            => ['nullable', 'boolean'],
            'check_fungsi_valve'                      => ['nullable', 'boolean'],
            'cleaning_unit_ro_mesin'                  => ['nullable', 'boolean'],

            // Compressor
            'sirkulasi_phe_aq55vsd'                   => ['nullable', 'boolean'],
            'penggantian_air_ro_aq55vsd'              => ['nullable', 'boolean'],
            'cleaning_compressor_aq55vsd'             => ['nullable', 'boolean'],
            'cleaning_jalur_cooling_aq55vsd'          => ['nullable', 'boolean'],
            'cleaning_dryer_fd185'                    => ['nullable', 'boolean'],
            'cleaning_compressor_ga37'                => ['nullable', 'boolean'],
            'cleaning_dryer_fd120'                    => ['nullable', 'boolean'],
            'lubrikasi_motor_compressor_aq55vsd'      => ['nullable', 'boolean'],
            'cleaning_compressor_sm55'                => ['nullable', 'boolean'],

            // Tank Farm
            'cleaning_sensor_level_tank_farm'         => ['nullable', 'boolean'],
            'cleaning_sensor_level_fresh_water_menara' => ['nullable', 'boolean'],
            'cleaning_sensor_level_ro_reject_menara'  => ['nullable', 'boolean'],
            'cleaning_sensor_level_intermediate'      => ['nullable', 'boolean'],

            // Boiler
            'check_safety_valve'                      => ['nullable', 'boolean'],
            'cleaning_level_gauge'                    => ['nullable', 'boolean'],
            'cleaning_level_transmitter'              => ['nullable', 'boolean'],
            'check_pressure_transmitter'              => ['nullable', 'boolean'],
            'check_temperature_transmitter'           => ['nullable', 'boolean'],
            'cleaning_sensor_o2_co2'                  => ['nullable', 'boolean'],
            'check_chaingrate'                        => ['nullable', 'boolean'],
            'check_ruang_bakar'                       => ['nullable', 'boolean'],
            'check_back_chamber'                      => ['nullable', 'boolean'],
            'check_guillotine'                        => ['nullable', 'boolean'],
            'check_wet_ash_conveyor'                  => ['nullable', 'boolean'],
            'check_bottom_ash_conveyor'               => ['nullable', 'boolean'],
            'check_conveyor_batu_bara'                => ['nullable', 'boolean'],
            'check_feeder'                            => ['nullable', 'boolean'],
            'cleaning_bak_wet_ash_conveyor'           => ['nullable', 'boolean'],
            'check_feed_tank'                         => ['nullable', 'boolean'],

            // WWTP
            'check_line_limbah'                       => ['nullable', 'boolean'],
            'check_line_chemical'                     => ['nullable', 'boolean'],
            'check_tangki_kotak'                      => ['nullable', 'boolean'],
            'check_tangki_bulat'                      => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator) {

            $checklistFields = [
                // Cooling Tower
                'cleaning_saringan_cooling_tower',
                'cleaning_unit_cooling_tower',
                'cleaning_bak_cooling_tower',

                // RO
                'check_sensor_tank_farm_ro_produk',
                'cleaning_flow_rate_mmf_1',
                'cleaning_flow_rate_mmf_2',
                'cleaning_flow_rate_ro_produk',
                'cleaning_flow_rate_ro_reject',
                'penggantian_micron_filter_cip',
                'penggantian_micron_filter_makeup_water',
                'cleaning_cip_tank',
                'cip_membrane_reverse_osmosis',
                'check_fungsi_valve',
                'cleaning_unit_ro_mesin',

                // Compressor
                'sirkulasi_phe_aq55vsd',
                'penggantian_air_ro_aq55vsd',
                'cleaning_compressor_aq55vsd',
                'cleaning_jalur_cooling_aq55vsd',
                'cleaning_dryer_fd185',
                'cleaning_compressor_ga37',
                'cleaning_dryer_fd120',
                'lubrikasi_motor_compressor_aq55vsd',
                'cleaning_compressor_sm55',

                // Tank Farm
                'cleaning_sensor_level_tank_farm',
                'cleaning_sensor_level_fresh_water_menara',
                'cleaning_sensor_level_ro_reject_menara',
                'cleaning_sensor_level_intermediate',

                // Boiler
                'check_safety_valve',
                'cleaning_level_gauge',
                'cleaning_level_transmitter',
                'check_pressure_transmitter',
                'check_temperature_transmitter',
                'cleaning_sensor_o2_co2',
                'check_chaingrate',
                'check_ruang_bakar',
                'check_back_chamber',
                'check_guillotine',
                'check_wet_ash_conveyor',
                'check_bottom_ash_conveyor',
                'check_conveyor_batu_bara',
                'check_feeder',
                'cleaning_bak_wet_ash_conveyor',
                'check_feed_tank',

                // WWTP
                'check_line_limbah',
                'check_line_chemical',
                'check_tangki_kotak',
                'check_tangki_bulat',
            ];

            $hasAnyChecked = false;
            foreach ($checklistFields as $field) {
                if ($this->boolean($field)) {
                    $hasAnyChecked = true;
                    break;
                }
            }

            if (! $hasAnyChecked) {
                $validator->errors()->add('checklist', 'Minimal pilih 1 item pengecekan Utility.');
            }
        });
    }
}
