<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('saveBase64Signature')) {

    function saveBase64Signature(
        string $base64,
        string $folder = 'mtc',
        string $username = 'user',
        string $dept = 'dept'
    ): string {

        // VALIDASI base64 image
        if (!preg_match('#^data:image/(png|jpg|jpeg);base64,#i', $base64)) {
            throw new Exception('TTD tidak valid');
        }

        // hapus prefix
        $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $base64);
        $image  = base64_decode($base64);

        if ($image === false) {
            throw new Exception('Decode TTD gagal');
        }

        // folder per tahun/bulan
        $path = $folder . '/' . date('Y/m');

        // sanitasi nama
        $username = Str::slug($username, '_');
        $dept     = Str::slug($dept, '_');

        // filename sesuai request kamu
        $fileName = "{$username}_{$dept}.png";

        $fullPath = $path . '/' . $fileName;

        Storage::disk('public')->put('ttd/' . $fullPath, $image);

        return $fullPath;
    }
}
