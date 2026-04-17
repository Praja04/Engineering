<?php

namespace App\Http\Requests\Kalibrasi;

use Illuminate\Foundation\Http\FormRequest;

class JangkaSorongRequest extends FormRequest
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

            'master_id_titik' => 'required|array|min:1',
            'master_id_titik.*' => 'required|exists:cal_jangka_sorong_master,id',

            // Nilai master snapshot
            'nilai_master' => 'required|array',
            'nilai_master.*' => 'required|array|min:1',
            'nilai_master.*.*' => 'required|numeric',

            // Nilai pembacaan 10x
            'nilai_pembacaan' => 'required|array',
            'nilai_pembacaan.*' => 'required|array|min:10|max:10',
            'nilai_pembacaan.*.*' => 'required|numeric',

            // Nomor pengulangan
            'no' => 'required|array',
            'no.*' => 'required|array|min:10|max:10',
            'no.*.*' => 'required|integer|min:1|max:10',
        ];
    }

    public function messages()
    {
        return  ['alat_id.required' => 'Alat kalibrasi harus dipilih.'];
    }
}
