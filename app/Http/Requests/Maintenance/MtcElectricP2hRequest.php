<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MtcElectricP2hRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'no_unit' => ['required', 'string'],
            'departemen' => ['nullable', 'string'],
            'shift' => ['nullable', 'integer', 'in:1,2,3'],

            'items' => ['required', 'array'],
            'items.*.item_id' => ['required', 'exists:mtc_electric_p2h_items,id'],
            'items.*.kondisi' => ['nullable', 'in:0,1'],
            'items.*.keterangan' => ['nullable', 'string'],
        ];
    }


    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'no_unit.required' => 'Nomor unit wajib diisi.',
            'items.required'   => 'Item pengecekan tidak boleh kosong.',
            'items.*.kondisi.boolean' => 'Nilai kondisi tidak valid.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->items ?? [] as $i => $item) {
                if (
                    isset($item['kondisi']) &&
                    $item['kondisi'] == 0 &&
                    empty($item['keterangan'])
                ) {
                    $validator->errors()->add(
                        "items.$i.keterangan",
                        'Keterangan wajib diisi jika kondisi NG.'
                    );
                }
            }
        });
    }
}
