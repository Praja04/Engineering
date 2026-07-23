<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MtcPenggantianMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'replacements'               => ['nullable', 'array'],
            'replacements.*.id'          => ['nullable', 'integer'],
            'replacements.*.mid'         => ['required', 'string'],
            'replacements.*.desc'        => ['nullable', 'string'],
            'replacements.*.deskripsi'   => ['nullable', 'string'],
            'replacements.*.qty'         => ['required', 'integer', 'min:1'],
            'replacements.*.uom'         => ['nullable', 'string'],
        ];
    }
}
