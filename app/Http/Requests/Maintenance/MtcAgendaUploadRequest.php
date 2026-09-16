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
            'kategori' => [
                'required',
                'in:mhe,non_mhe',
            ],
            'tahun' => [
                'required',
                'integer',
                'min:2020',
                'max:2050',
            ],
            'bulan' => [
                'nullable',
                'required_if:kategori,mhe',
                'integer',
                'between:1,12',
            ],
            'file_excel' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240', // 10 MB
            ],
            'jenis_mtc' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori.required'    => 'Kategori agenda wajib dipilih (MHE atau Non MHE).',
            'kategori.in'          => 'Kategori agenda tidak valid.',
            'tahun.required'       => 'Tahun wajib diisi.',
            'tahun.integer'        => 'Tahun harus berupa angka.',
            'tahun.min'            => 'Tahun minimal 2020.',
            'tahun.max'            => 'Tahun maksimal 2050.',
            'bulan.required_if'    => 'Bulan wajib dipilih untuk format MHE.',
            'bulan.between'        => 'Bulan harus antara 1 sampai 12.',
            'file_excel.required'  => 'File Excel wajib diunggah.',
            'file_excel.file'      => 'Upload harus berupa file.',
            'file_excel.mimes'     => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'       => 'Ukuran file tidak boleh lebih dari 10 MB.',
        ];
    }
}
