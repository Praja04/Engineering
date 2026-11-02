<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MtcSipilRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'checklist_map' => ['nullable', 'array'],
            'plumbing' => ['nullable', 'boolean'],
            'plafon' => ['nullable', 'boolean'],
            'lantai' => ['nullable', 'boolean'],
            'dinding' => ['nullable', 'boolean'],
            'jendela' => ['nullable', 'boolean'],
            'pintu' => ['nullable', 'boolean'],
            'rooling_fast_door' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator) {

            $checklistFields = [
                'plumbing',
                'plafon',
                'lantai',
                'dinding',
                'jendela',
                'pintu',
                'rooling_fast_door',
            ];

            $hasAnyChecked = false;

            if ($this->has('checklist_map') && is_array($this->input('checklist_map'))) {
                foreach ($this->input('checklist_map') as $field => $id) {
                    if ($this->has($field) && $this->input($field) !== null && $this->input($field) !== '') {
                        $hasAnyChecked = true;
                        break;
                    }
                }
            }

            if (! $hasAnyChecked) {
                foreach ($checklistFields as $field) {
                    if ($this->has($field) && $this->input($field) !== null && $this->input($field) !== '') {
                        $hasAnyChecked = true;
                        break;
                    }
                }
            }

            if (! $hasAnyChecked) {
                $validator->errors()->add('checklist', 'Minimal pilih 1 item pengecekan Sipil.');
            }
        });
    }
}
