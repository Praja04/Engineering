<?php

namespace App\Http\Requests\Kalibrasi;

use Illuminate\Foundation\Http\FormRequest;

class VolumetrikRequest extends FormRequest
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

            'data' => 'required|array|min:1',

            'data.*.titik_kalibrasi' => 'required|numeric',

            'data.*.penunjuk_standar' => 'required|array|min:1',
            'data.*.penunjuk_standar.*' => 'required|numeric',

            'data.*.penunjuk_alat' => 'required|array|min:1',
            'data.*.penunjuk_alat.*' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'alat_id.required' => 'Alat kalibrasi harus dipilih.',
            'data.required' => 'Data titik kalibrasi harus diisi.',
            'data.*.titik_kalibrasi.required' => 'Titik kalibrasi wajib diisi.',
            'data.*.penunjuk_standar.*.required' => 'Penunjuk standar tidak boleh kosong.',
            'data.*.penunjuk_alat.*.required' => 'Penunjuk alat tidak boleh kosong.',
        ];
    }
}
