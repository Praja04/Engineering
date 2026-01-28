<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MtcBatteryRequest extends FormRequest
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
            'tanggal'        => 'required|date',
            'waktu'          => 'required|date_format:H:i',
            'battery_type'   => 'nullable|string|max:100',
            'no_seri'        => 'nullable|string|max:100',
            'no_unit'        => 'nullable|string|max:100',
            'keterangan'     => 'nullable|string',

            // DETAIL (array)
            'details'                       => 'required|array|min:1',
            'details.*.voltase'             => ['nullable', 'boolean'],
            'details.*.level_air_aki'       => ['nullable', 'boolean'],
            'details.*.intercell'           => ['nullable', 'boolean'],
            'details.*.kondisi_skun'        => ['nullable', 'boolean'],
            'details.*.kondisi_unit'        => ['nullable', 'boolean'],
            'details.*.grounding'           => ['nullable', 'boolean'],
            'details.*.cell'                => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'waktu.required'   => 'Waktu wajib diisi.',
            'details.required' => 'Minimal satu detail battery harus diisi.',
            'details.*.cell.required' => 'Cell wajib diisi.',
        ];
    }
}
