<?php

namespace App\Http\Requests\Kalibrasi;

use Illuminate\Foundation\Http\FormRequest;

class InstrumenRequest extends FormRequest
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

            'jenis_alat_ukur'  => 'required|string|max:255',
            'jenis_standar'    => 'required|string|max:255',
            'catatan'    => 'nullable|string|max:255',

            'data'                     => 'required|array|min:1',
            'data.*.titik_kalibrasi'   => 'required|string|max:100',
            'data.*.indikator'             => 'nullable|string|max:100',

            'data.*.alat'              => 'required|array|min:1',
            'data.*.standar'           => 'required|array|min:1',
            'data.*.pembacaan_alat'    => 'required|array|min:1',
            'data.*.pembacaan_standar' => 'required|array|min:1',

            'tested'    => 'required|boolean',
            'measured'  => 'required|string|max:50',
            'criterion' => 'required|string|max:50',
            'passed'    => 'nullable|string|max:10',
        ];
    }
}
