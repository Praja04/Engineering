<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MtcBatteryRequest extends FormRequest
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
            'details'              => 'required|array|min:12',
            'details.*.cell'       => 'required|integer|min:1|distinct',  // distinct biar cell tidak duplikat
            'details.*.voltase'    => 'nullable|numeric|min:0',
            'details.*.level_air_aki'   => 'nullable|boolean',
        ];
    }
}
