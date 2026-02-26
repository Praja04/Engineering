<?php

namespace App\Http\Requests\Kalibrasi;

use Illuminate\Foundation\Http\FormRequest;

class PressureRequest extends FormRequest
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

            // HEADER
            'alat_id' => 'required|exists:alat_kalibrasi,id',
            'lokasi_kalibrasi' => 'required|string|max:255',
            'suhu_ruangan' => 'required|string|max:50',
            'kelembaban' => 'required|string|max:50',
            'tgl_kalibrasi' => 'required|date',

            // ARRAY PRESSURE
            'pressure' => 'required|array|min:1',

            'pressure.*.titik_kalibrasi' => 'required|numeric',

            // NAiK
            'pressure.*.naik' => 'required|array',
            'pressure.*.naik.alat' => 'required|array|size:3',
            'pressure.*.naik.standar' => 'required|array|size:3',

            'pressure.*.naik.alat.*' => 'required|numeric',
            'pressure.*.naik.standar.*' => 'required|numeric',

            // TURUN
            'pressure.*.turun' => 'required|array',
            'pressure.*.turun.alat' => 'required|array|size:3',
            'pressure.*.turun.standar' => 'required|array|size:3',

            'pressure.*.turun.alat.*' => 'required|numeric',
            'pressure.*.turun.standar.*' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return  ['alat_id.required' => 'Alat kalibrasi harus dipilih.'];
    }
}
