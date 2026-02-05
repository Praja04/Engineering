<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MtcMasterMesinRequest extends FormRequest
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
            'jenis_mtc'  => ['required', 'string'],
            'nama_mesin' => ['required', 'string'],
            'lokasi'     => ['nullable', 'string'],
            'frekuensi'  => ['nullable', 'string'],
            // 'aktif'      => ['required', 'boolean'],
        ];
    }
}
