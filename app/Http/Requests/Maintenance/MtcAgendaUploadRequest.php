<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MtcAgendaUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file_excel' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:5120', // 5 MB
            ],
            'tahun' => [
                'required',
                'integer',
                'min:2020',
                'max:2050',
            ],
            'jenis_mtc' => [
                'required',
                'string',
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.file'     => 'Upload harus berupa file.',
            'file_excel.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'      => 'Ukuran file tidak boleh lebih dari 5 MB.',
            'tahun.required'      => 'Tahun wajib diisi.',
            'tahun.integer'       => 'Tahun harus berupa angka.',
            'tahun.min'           => 'Tahun minimal 2020.',
            'tahun.max'           => 'Tahun maksimal 2050.',
            'jenis_mtc.required'  => 'Jenis MTC wajib dipilih.',
        ];
    }
}
