<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MtcBatteryMainRequest extends FormRequest
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
            'battery_type'   => 'nullable|string|max:100',
            'no_seri'        => 'nullable|string|max:100',
            'no_unit'        => 'nullable|string|max:100',
            'kondisi_plug_battery' => 'nullable|boolean',
            'total_voltase'     => ['nullable', 'numeric', 'min:0'],
            'grounding'         => 'nullable|string|max:100',
            'catatan'        => 'nullable|string|max:100',
            'intercell'      => 'nullable|boolean',
            'kondisi_skun'   => 'nullable|boolean',
            'kondisi_unit'   => 'nullable|boolean',
        ];
    }
}
