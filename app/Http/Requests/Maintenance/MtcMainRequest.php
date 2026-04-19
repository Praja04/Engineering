<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MtcMainRequest extends FormRequest
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
            'tanggal'   => ['required', 'date'],
            'waktu_mulai'     => ['required', 'date_format:H:i'],
            'waktu_selesai' => ['nullable', 'date_format:H:i'],
            'paket'     => ['nullable', 'string', 'max:50'],
            'tanggal_selesai' => 'nullable|date|required_if:paket,Korektif',
            'keterangan'  => ['nullable', 'string'],
            'korektif'    => ['nullable', 'string'],
            'area'        => ['nullable', 'string'],
            'departemen'  => ['nullable', 'string'],
            'lokasi'  => ['nullable', 'string'],
            'rekomendasi' => ['nullable', 'string'],
            'running_hour' => ['nullable', 'string'],

            'staff_id' => ['required', 'string'],
            'user_id' => ['required', 'string']
        ];
    }
}
