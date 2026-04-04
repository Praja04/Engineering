<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MtcMasterMesinUploadRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.file'     => 'Upload harus berupa file.',
            'file_excel.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'      => 'Ukuran file tidak boleh lebih dari 5 MB.',
        ];
    }
}
