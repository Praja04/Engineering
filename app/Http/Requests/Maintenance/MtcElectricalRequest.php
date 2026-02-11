<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MtcElectricalRequest extends FormRequest
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

            // Panel / Unit Electrical
            'check_kunci'             => ['nullable', 'boolean'],
            'check_koneksi_kabel'     => ['nullable', 'boolean'],
            'check_wiring_panel'      => ['nullable', 'boolean'],
            'check_lampu_indikator'   => ['nullable', 'boolean'],
            'check_name_plate'        => ['nullable', 'boolean'],
            'check_unit_electrical'   => ['nullable', 'boolean'],
            'check_grounding'         => ['nullable', 'boolean'],
            'check_kebersihan'        => ['nullable', 'boolean'],
            'check_bus_bar'           => ['nullable', 'boolean'],
            'check_nilai_grounding'   => ['nullable', 'boolean'],

            // Penerangan
            'check_kondisi_lampu'        => ['nullable', 'boolean'],
            'check_cover_lampu'          => ['nullable', 'boolean'],
            'check_wiring_penerangan'    => ['nullable', 'boolean'],
            'check_saklar'               => ['nullable', 'boolean'],
            'check_penyangga_penerangan' => ['nullable', 'boolean'],

            // Sistem Distribusi
            'check_stecker'                      => ['nullable', 'boolean'],
            'check_stop_kontak'                  => ['nullable', 'boolean'],
            'check_terminal_listrik'             => ['nullable', 'boolean'],
            'check_pengabelan_distribusi'         => ['nullable', 'boolean'],
            'check_support_pelindung_distribusi'  => ['nullable', 'boolean'],

            // Capacitor Bank
            'check_kondisi_fisik_capacitor' => ['nullable', 'boolean'],
            'check_nilai_farad'             => ['nullable', 'boolean'],
            'check_nilai_ampere'            => ['nullable', 'boolean'],
            'check_kebersihan_capacitor'    => ['nullable', 'boolean'],

            // Trafo / Oli
            'check_kebocoran_oli_sisi_bawah' => ['nullable', 'boolean'],
            'check_kebocoran_oli_sisi_atas'  => ['nullable', 'boolean'],
            'check_level_oli'                => ['nullable', 'boolean'],

            // Catatan
            // 'keterangan' => ['nullable', 'string'],
            // 'korektif'   => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator) {

            $checklistFields = [
                // Panel / Unit Electrical
                'check_kunci',
                'check_koneksi_kabel',
                'check_wiring_panel',
                'check_lampu_indikator',
                'check_name_plate',
                'check_unit_electrical',
                'check_grounding',
                'check_kebersihan',
                'check_bus_bar',
                'check_nilai_grounding',

                // Penerangan
                'check_kondisi_lampu',
                'check_cover_lampu',
                'check_wiring_penerangan',
                'check_saklar',
                'check_penyangga_penerangan',

                // Sistem Distribusi
                'check_stecker',
                'check_stop_kontak',
                'check_terminal_listrik',
                'check_pengabelan_distribusi',
                'check_support_pelindung_distribusi',

                // Capacitor Bank
                'check_kondisi_fisik_capacitor',
                'check_nilai_farad',
                'check_nilai_ampere',
                'check_kebersihan_capacitor',

                // Trafo / Oli
                'check_kebocoran_oli_sisi_bawah',
                'check_kebocoran_oli_sisi_atas',
                'check_level_oli',
            ];

            $hasAnyChecked = false;
            foreach ($checklistFields as $field) {
                if ($this->boolean($field)) {
                    $hasAnyChecked = true;
                    break;
                }
            }

            if (! $hasAnyChecked) {
                $validator->errors()->add('checklist', 'Minimal pilih 1 item pengecekan.');
            }
        });
    }
}
