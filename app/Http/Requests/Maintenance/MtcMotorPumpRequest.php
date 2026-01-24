<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MtcMotorPumpRequest extends FormRequest
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
            'nama_mesin'    => ['required', 'string'],
            'tanggal'     => ['required', 'date'],
            'paket'       => ['nullable', 'string', 'max:50'],

            // Motor - semua nullable boolean
            'electrical_motor'               => ['nullable', 'boolean'],
            'putaran_motor'                  => ['nullable', 'boolean'],
            'fibrasi_suara_motor'            => ['nullable', 'boolean'],
            'bearing_motor'                  => ['nullable', 'boolean'],
            'pelumasan_motor'                => ['nullable', 'boolean'],
            'kebersihan_unit_body_motor'     => ['nullable', 'boolean'],

            // Pompa
            'putaran_pompa'                  => ['nullable', 'boolean'],
            'shaft_karet_coupling_pompa'     => ['nullable', 'boolean'],
            'fan_belt_pompa'                 => ['nullable', 'boolean'],
            'pressure_pompa'                 => ['nullable', 'boolean'],
            'mechanical_seal_pompa'          => ['nullable', 'boolean'],
            'gasket_pompa'                   => ['nullable', 'boolean'],
            'impeler'                        => ['nullable', 'boolean'],
            'kebersihan_unit_body_pompa'     => ['nullable', 'boolean'],

            // Aksesoris
            'valve_aksesoris'                => ['nullable', 'boolean'],
            'cek_valve_aksesoris'            => ['nullable', 'boolean'],
            'flow_meter_aksesoris'           => ['nullable', 'boolean'],
            'strainer_aksesoris'             => ['nullable', 'boolean'],
            'alat_ukur_aksesoris'            => ['nullable', 'boolean'],
            'kelengkapan_baut_mur_aksesoris' => ['nullable', 'boolean'],

            // Gearbox
            'tambah_ganti_oli_gearbox'       => ['nullable', 'boolean'],
            'unit_area_gearbox'              => ['nullable', 'boolean'],
            'oil_seal_gearbox'               => ['nullable', 'boolean'],
            'filter_udara_gearbox'           => ['nullable', 'boolean'],
            'bearing_gearbox'                => ['nullable', 'boolean'],

            'keterangan'  => ['nullable', 'string'],
            'korektif'    => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator) {

            $checklistFields = [
                // ======================
                // Motor
                // ======================
                'electrical_motor',
                'putaran_motor',
                'fibrasi_suara_motor',
                'bearing_motor',
                'pelumasan_motor',
                'kebersihan_unit_body_motor',

                // ======================
                // Pompa
                // ======================
                'putaran_pompa',
                'shaft_karet_coupling_pompa',
                'fan_belt_pompa',
                'pressure_pompa',
                'mechanical_seal_pompa',
                'gasket_pompa',
                'impeler',
                'kebersihan_unit_body_pompa',

                // ======================
                // Aksesoris
                // ======================
                'valve_aksesoris',
                'cek_valve_aksesoris',
                'flow_meter_aksesoris',
                'strainer_aksesoris',
                'alat_ukur_aksesoris',
                'kelengkapan_baut_mur_aksesoris',

                // ======================
                // Gearbox
                // ======================
                'tambah_ganti_oli_gearbox',
                'unit_area_gearbox',
                'oil_seal_gearbox',
                'filter_udara_gearbox',
                'bearing_gearbox',
            ];

            $hasAnyChecked = false;

            foreach ($checklistFields as $field) {
                if ($this->boolean($field)) {
                    $hasAnyChecked = true;
                    break;
                }
            }

            if (! $hasAnyChecked) {
                $validator->errors()->add(
                    'checklist',
                    'Minimal pilih 1 item pengecekan Motor / Pompa.'
                );
            }
        });
    }
}
