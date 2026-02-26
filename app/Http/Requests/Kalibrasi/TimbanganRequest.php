<?php

namespace App\Http\Requests\Kalibrasi;

use Illuminate\Foundation\Http\FormRequest;

class TimbanganRequest extends FormRequest
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
            'pembacaan_terkecil' => 'required|numeric',

            /*
            |--------------------------------------------------------------------------
            | A. Kemampuan Ulang
            |--------------------------------------------------------------------------
            */
            'data' => 'nullable|array',

            'data.mendekati_nol' => 'nullable|array',
            'data.setengah_kapasitas' => 'nullable|array',
            'data.full_kapasitas' => 'nullable|array',

            'data.*.*.z' => 'nullable|numeric',
            'data.*.*.m' => 'nullable|numeric',


            /*
            |--------------------------------------------------------------------------
            | B. Keseragaman Skala
            |--------------------------------------------------------------------------
            */
            'keseragaman' => 'nullable|array',
            'keseragaman.*.beban' => 'nullable|numeric',
            'keseragaman.*.pembacaan' => 'nullable|numeric',


            /*
            |--------------------------------------------------------------------------
            | C. Pinggan
            |--------------------------------------------------------------------------
            */

            'pinggan' => 'nullable|array',
            'pinggan.diameter' => 'nullable|numeric',
            'pinggan.massa' => 'nullable|numeric',

            'pinggan.percobaan_1' => 'nullable|array',
            'pinggan.percobaan_2' => 'nullable|array',
            'pinggan.percobaan_3' => 'nullable|array',

            'pinggan.percobaan_*.tengah' => 'nullable|numeric',
            'pinggan.percobaan_*.depan' => 'nullable|numeric',
            'pinggan.percobaan_*.belakang' => 'nullable|numeric',
            'pinggan.percobaan_*.kiri' => 'nullable|numeric',
            'pinggan.percobaan_*.kanan' => 'nullable|numeric',

            /*
            |--------------------------------------------------------------------------
            | D. Tare
            |--------------------------------------------------------------------------
            */
            'tare' => 'nullable|array',
            'tare.massa' => 'nullable|numeric',

            'tare.tanpa' => 'nullable|array',
            'tare.dengan' => 'nullable|array',

            'tare.tanpa.*' => 'nullable|numeric',
            'tare.dengan.*' => 'nullable|numeric',


            /*
            |--------------------------------------------------------------------------
            | E. Histerisis
            |--------------------------------------------------------------------------
            */
            'histerisis' => 'nullable|array',

            'histerisis.pembacaan_terkecil' => 'nullable|numeric',
            'histerisis.m_setengah' => 'nullable|numeric',

            'histerisis.z1.*' => 'nullable|numeric',
            'histerisis.m1.*' => 'nullable|numeric',
            'histerisis.m_plus.*' => 'nullable|numeric',
            'histerisis.m2.*' => 'nullable|numeric',
            'histerisis.z2.*' => 'nullable|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'alat_id.required' => 'Alat wajib dipilih.',
            'alat_id.exists'   => 'Alat tidak valid.',
            'tgl_kalibrasi.required' => 'Tanggal kalibrasi wajib diisi.',
        ];
    }
}
