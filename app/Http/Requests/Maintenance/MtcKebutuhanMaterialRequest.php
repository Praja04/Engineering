<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MtcKebutuhanMaterialRequest extends FormRequest
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
            'materials'               => ['nullable', 'array'],
            'materials.*.mid'         => ['nullable', 'string'],
            'materials.*.deskripsi'   => ['nullable', 'string'],
            'materials.*.qty'         => ['nullable', 'integer', 'min:1'],
        ];
    }
}
