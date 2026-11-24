<?php

namespace App\Http\Requests\Kalibrasi;

use Illuminate\Foundation\Http\FormRequest;

class VolumetrikRequest extends FormRequest
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
            'alat_id' => 'required|exists:alat_kalibrasi,id',
            'lokasi_kalibrasi' => 'required|string|max:255',
            'suhu_ruangan' => 'required|string|max:50',
            'kelembaban' => 'required|string|max:50',
            'tgl_kalibrasi' => 'required|date',

            'data' => 'required|array|min:1',

            'data.*.titik_kalibrasi' => 'required|numeric',

            'data.*.penunjuk_standar' => 'required|array|min:1',
            'data.*.penunjuk_standar.*' => 'nullable|numeric',

            'data.*.penunjuk_alat' => 'required|array|min:1',
            'data.*.penunjuk_alat.*' => 'nullable|numeric',
        ];
    }

    /**
     * Custom validation:
     * 1. Jika salah satu antara penunjuk standar dan penunjuk alat terisi, maka pasangannya wajib diisi.
     * 2. Setiap titik kalibrasi minimal 3 baris pengisian harus diisi lengkap (standar & alat).
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->input('data');
            if (!is_array($data)) return;

            foreach ($data as $i => $titik) {
                $titikVal = $titik['titik_kalibrasi'] ?? ($i + 1);
                $standar = $titik['penunjuk_standar'] ?? [];
                $alat = $titik['penunjuk_alat'] ?? [];

                $filledCount = 0;
                $maxLen = max(count($standar), count($alat));

                for ($j = 0; $j < $maxLen; $j++) {
                    $stdVal = isset($standar[$j]) && $standar[$j] !== '' && $standar[$j] !== null ? $standar[$j] : null;
                    $alatVal = isset($alat[$j]) && $alat[$j] !== '' && $alat[$j] !== null ? $alat[$j] : null;

                    // Jika salah satunya terisi, maka harus terisi semua dalam satu barisnya
                    if ($stdVal !== null && $alatVal === null) {
                        $validator->errors()->add(
                            "data.{$i}.penunjuk_alat.{$j}",
                            "Titik {$titikVal} baris ke-" . ($j + 1) . ": Penunjuk Alat wajib diisi karena Penunjuk Standar telah terisi."
                        );
                    } elseif ($stdVal === null && $alatVal !== null) {
                        $validator->errors()->add(
                            "data.{$i}.penunjuk_standar.{$j}",
                            "Titik {$titikVal} baris ke-" . ($j + 1) . ": Penunjuk Standar wajib diisi karena Penunjuk Alat telah terisi."
                        );
                    } elseif ($stdVal !== null && $alatVal !== null) {
                        $filledCount++;
                    }
                }

                // Minimal 3 pengisian lengkap
                if ($filledCount < 3) {
                    $validator->errors()->add(
                        "data.{$i}",
                        "Titik {$titikVal}: Minimal 3 baris pengisian (Standar & Alat) harus diisi lengkap. Saat ini baru terisi {$filledCount} baris."
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'alat_id.required'                  => 'Alat kalibrasi harus dipilih.',
            'alat_id.exists'                    => 'Alat kalibrasi tidak valid.',
            'lokasi_kalibrasi.required'         => 'Lokasi kalibrasi wajib diisi.',
            'suhu_ruangan.required'             => 'Suhu ruangan wajib diisi.',
            'kelembaban.required'               => 'Kelembaban wajib diisi.',
            'tgl_kalibrasi.required'            => 'Tanggal kalibrasi wajib diisi.',
            'data.required'                     => 'Data titik kalibrasi harus diisi.',
            'data.*.titik_kalibrasi.required'   => 'Titik kalibrasi wajib diisi.',
            'data.*.titik_kalibrasi.numeric'    => 'Titik kalibrasi harus berupa angka.',
            'data.*.penunjuk_standar.*.numeric' => 'Nilai penunjuk standar harus berupa angka valid.',
            'data.*.penunjuk_alat.*.numeric'    => 'Nilai penunjuk alat harus berupa angka valid.',
        ];
    }
}
