<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MtcRefrigerasiRequest extends FormRequest
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

            // ======================
            // Unit Indoor
            // ======================
            'check_filter_udara' => 'nullable|boolean',
            'check_cover_filter_udara' => 'nullable|boolean',
            'check_electrical_indoor' => 'nullable|boolean',
            'check_suhu_evaporator' => 'nullable|boolean',
            'check_indikator_display' => 'nullable|boolean',
            'check_motor_blower' => 'nullable|boolean',
            'check_fan_belt_blower' => 'nullable|boolean',
            'check_pelumasan_blower' => 'nullable|boolean',
            'check_pergerakan_motor_swing' => 'nullable|boolean',
            'check_kontroler_indoor' => 'nullable|boolean',
            'check_saluran_drain_kondensasi' => 'nullable|boolean',
            'sirkulasi_evaporator' => 'nullable|boolean',

            // ======================
            // Unit Outdoor
            // ======================
            'check_kondisi_kondensor' => 'nullable|boolean',
            'check_electrical_outdoor' => 'nullable|boolean',
            'check_motor_fan' => 'nullable|boolean',
            'check_tekanan_freon' => 'nullable|boolean',
            'pelumasan_motor_fan' => 'nullable|boolean',
            'kebersihan_unit_body_outdoor' => 'nullable|boolean',

            // ======================
            // Jalur Distribusi
            // ======================
            'check_jalur_freon' => 'nullable|boolean',
            'check_jalur_distribusi_udara' => 'nullable|boolean',
            'check_jalur_return_udara' => 'nullable|boolean',
            'check_suhu_supply' => 'nullable|boolean',
            'check_suhu_return' => 'nullable|boolean',
            'check_flow_supply' => 'nullable|boolean',
            'check_flow_return' => 'nullable|boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator) {

            $checklistFields = [
                // Indoor
                'check_filter_udara',
                'check_cover_filter_udara',
                'check_electrical_indoor',
                'check_suhu_evaporator',
                'check_indikator_display',
                'check_motor_blower',
                'check_fan_belt_blower',
                'check_pelumasan_blower',
                'check_pergerakan_motor_swing',
                'check_kontroler_indoor',
                'check_saluran_drain_kondensasi',
                'sirkulasi_evaporator',

                // Outdoor
                'check_kondisi_kondensor',
                'check_electrical_outdoor',
                'check_motor_fan',
                'check_tekanan_freon',
                'pelumasan_motor_fan',
                'kebersihan_unit_body_outdoor',

                // Jalur distribusi
                'check_jalur_freon',
                'check_jalur_distribusi_udara',
                'check_jalur_return_udara',
                'check_suhu_supply',
                'check_suhu_return',
                'check_flow_supply',
                'check_flow_return',
            ];

            // minimal ada 1 yang bernilai true / 1 / "on"
            $hasAnyChecked = false;
            foreach ($checklistFields as $field) {
                if ($this->boolean($field)) { // true kalau field ada & truthy
                    $hasAnyChecked = true;
                    break;
                }
            }

            if (! $hasAnyChecked) {
                $validator->errors()->add('checklist', 'Minimal pilih 1 item untuk pengecekan.');
            }
        });
    }
}
