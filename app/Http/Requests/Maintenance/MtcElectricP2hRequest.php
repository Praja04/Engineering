<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MtcElectricP2hRequest extends FormRequest
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
            'no_unit' => ['required', 'string'],
            'shift' => ['nullable', 'integer', 'in:1,2,3'],
            'catatan' => ['nullable', 'string'],

            'level_minyak_rem'       => ['nullable', 'boolean'],
            'level_oli_hydraulic'    => ['nullable', 'boolean'],
            'isi_air_aki'            => ['nullable', 'boolean'],
            'baterai'                => ['nullable', 'boolean'],
            'hydraulic_system'       => ['nullable', 'boolean'],
            'selang_hydraulic'       => ['nullable', 'boolean'],
            'lift_chains'            => ['nullable', 'boolean'],
            'fork'                   => ['nullable', 'boolean'],
            'body_unit'              => ['nullable', 'boolean'],
            'lampu_kombinasi_kiri'   => ['nullable', 'boolean'],
            'lampu_kombinasi_kanan'  => ['nullable', 'boolean'],
            'lampu_sorot'            => ['nullable', 'boolean'],
            'lampu_sign_depan_kanan' => ['nullable', 'boolean'],
            'lampu_sign_depan_kiri'  => ['nullable', 'boolean'],
            'klakson'                => ['nullable', 'boolean'],
            'buzzer_back'            => ['nullable', 'boolean'],
            'kaca_spion'             => ['nullable', 'boolean'],
            'baut_roda'              => ['nullable', 'boolean'],
            'ban'                    => ['nullable', 'boolean'],
            'kebersihan_unit'        => ['nullable', 'boolean'],
            'panel_display'          => ['nullable', 'boolean'],
            'sistem_kemudi'          => ['nullable', 'boolean'],

            'hours_meter' => ['nullable', 'string'],
        ];
    }
}
