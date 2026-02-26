<?php

namespace App\Http\Requests\Kalibrasi;

use Illuminate\Foundation\Http\FormRequest;

class ThermohygrometerRequest extends FormRequest
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
            'alat_id' => 'required|exists:alat_kalibrasi,id',
            'lokasi_kalibrasi' => 'required|string|max:255',
            'suhu_ruangan' => 'required|string|max:50',
            'kelembaban' => 'required|string|max:50',
            'tgl_kalibrasi' => 'required|date',

            'data' => ['nullable', 'array', 'min:1'],

            // tiap titik
            'data.*.titik_kalibrasi' => ['nullable', 'numeric'],
            'data.*.posisi' => ['nullable', 'string', 'max:255'],
            'data.*' => [
                function ($attribute, $value, $fail) {
                    if (empty($value['titik_kalibrasi']) && empty($value['posisi'])) {
                        $fail('Minimal titik kalibrasi atau posisi harus diisi.');
                    }
                }
            ],

            // standar harus 3 kali
            'data.*.standar' => ['nullable', 'array', 'size:3'],
            'data.*.standar.*.suhu' => ['nullable', 'numeric'],
            'data.*.standar.*.rh'   => ['nullable', 'numeric'],

            // alat harus 3 kali
            'data.*.alat' => ['nullable', 'array', 'size:3'],
            'data.*.alat.*.suhu' => ['nullable', 'numeric'],
            'data.*.alat.*.rh'   => ['nullable', 'numeric'],
        ];
    }

    public function messages()
    {
        return  ['alat_id.required' => 'Alat kalibrasi harus dipilih.'];
    }
}
