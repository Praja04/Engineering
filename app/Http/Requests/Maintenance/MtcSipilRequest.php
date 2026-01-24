<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MtcSipilRequest extends FormRequest
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
            // HEADER
            'tanggal'     => ['required', 'date'],
            'waktu'       => ['nullable', 'date_format:H:i'], // kalau kamu isi dari server, boleh nullable
            'area'        => ['required', 'string', 'max:255'],
            'rekomendasi' => ['nullable', 'string', 'max:255'], // ubah ke max lebih besar kalau perlu
            'korektif' => ['nullable', 'string', 'max:255'], // ubah ke max lebih besar kalau perlu

            // DETAILS
            'details' => ['required', 'array', 'min:1'],

            'details.*.item_id' => ['required', 'integer', 'exists:mtc_sipil_items,id'],
            'details.*.kondisi' => ['nullable', 'boolean'],
            'details.*.keterangan' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {
            $details = $this->input('details', []);

            // 1) item_id tidak boleh dobel dalam 1 request
            $itemIds = array_map(fn($d) => $d['item_id'] ?? null, $details);
            $itemIds = array_filter($itemIds, fn($id) => !is_null($id));
            if (count($itemIds) !== count(array_unique($itemIds))) {
                $validator->errors()->add('details', 'Item checklist tidak boleh duplikat.');
            }

            // 2) minimal 1 item kondisi terisi (Ya/Tidak)
            // penting: false itu valid (Tidak), jadi cek "ada key kondisi" & tidak null
            $hasAnyFilled = false;
            foreach ($details as $d) {
                if (array_key_exists('kondisi', $d) && $d['kondisi'] !== null && $d['kondisi'] !== '') {
                    $hasAnyFilled = true;
                    break;
                }
            }

            if (! $hasAnyFilled) {
                $validator->errors()->add('checklist', 'Minimal isi kondisi (Ya/Tidak) untuk minimal 1 item.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'details.required' => 'Detail checklist wajib dikirim.',
            'details.array' => 'Format detail checklist tidak valid.',
            'details.*.item_id.required' => 'Item checklist wajib dipilih.',
            'details.*.item_id.exists' => 'Item checklist tidak ditemukan.',
            'details.*.kondisi.boolean' => 'Kondisi harus bernilai Ya/Tidak.',
        ];
    }
}
