<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MtcDieselP2hRequest extends FormRequest
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
            'mesin_id' => ['required', 'exists:mtc_master_mesin,id'],
            'no_unit' => ['required', 'string'],
            'shift' => ['nullable', 'integer', 'in:1,2,3'],
            'catatan' => ['nullable', 'string'],

            'klakson'                => ['nullable', 'boolean'],
            'buzzer_back'            => ['nullable', 'boolean'],
            'oli_mesin'              => ['nullable', 'boolean'],
            'radiator_hose'          => ['nullable', 'boolean'],
            'water_pump'             => ['nullable', 'boolean'],
            'injection_system'       => ['nullable', 'boolean'],
            'fan_vbelt'              => ['nullable', 'boolean'],
            'turbocharger_manifold'  => ['nullable', 'boolean'],
            'tensioner_belt'         => ['nullable', 'boolean'],
            'starting_motor'         => ['nullable', 'boolean'],
            'alternator'             => ['nullable', 'boolean'],
            'control_display'        => ['nullable', 'boolean'],
            'oli_transmisi'          => ['nullable', 'boolean'],
            'aki'                    => ['nullable', 'boolean'],
            'engine_mounting'        => ['nullable', 'boolean'],
            'filter_oli_transmisi'   => ['nullable', 'boolean'],
            'fungsi_rem'             => ['nullable', 'boolean'],
            'fungsi_kopling'         => ['nullable', 'boolean'],
            'oli_hydraulic'          => ['nullable', 'boolean'],
            'hydraulic_system'       => ['nullable', 'boolean'],
            'steering_system'        => ['nullable', 'boolean'],
            'body_back_rest'         => ['nullable', 'boolean'],
            'kaca_spion'             => ['nullable', 'boolean'],
            'bucket_pin'             => ['nullable', 'boolean'],
            'dump_pin_bushing'       => ['nullable', 'boolean'],
            'seal_hydraulic'         => ['nullable', 'boolean'],
            'roda_ban_baut'          => ['nullable', 'boolean'],
            'lampu_unit'             => ['nullable', 'boolean'],
            'baut_bearing_molen'     => ['nullable', 'boolean'],
            'baut_hanger_as'         => ['nullable', 'boolean'],
            'baut_grease'            => ['nullable', 'boolean'],
            'katup_pembuangan_angin' => ['nullable', 'boolean'],

            'hours_meter' => ['nullable', 'string'],
        ];
    }
}
