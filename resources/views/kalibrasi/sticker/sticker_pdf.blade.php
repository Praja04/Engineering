<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 6px;
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .wrapper {
            border: 2px solid #000;
            padding: 6px;
        }

        .logo {
            text-align: center;
            margin-bottom: 4px;
        }

        .logo img {
            height: 20px;
        }

        .row {
            margin-bottom: 3px;
        }

        .label {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <div class="logo">
            <img src="{{ public_path('assets/images/logo/bas.png') }}">
        </div>

        <div class="row">
            <span class="label">Kode:</span>
            {{ $kalibrasi->alat->kode_alat ?? '-' }}
        </div>

        <div class="row">
            <span class="label">Dibuat:</span>
            {{ $kalibrasi->user->username ?? '-' }}
        </div>

        <div class="row">
            <span class="label">Kalibrasi:</span>
            {{ \Carbon\Carbon::parse($kalibrasi->tgl_kalibrasi)->format('d-m-Y') }}
        </div>

        <div class="row">
            <span class="label">Ulang:</span>
            {{ $kalibrasi->tgl_kalibrasi_ulang ? \Carbon\Carbon::parse($kalibrasi->tgl_kalibrasi_ulang)->format('d-m-Y') : \Carbon\Carbon::parse($kalibrasi->tgl_kalibrasi)->addYear()->format('d-m-Y') }}
        </div>

        <div class="row">
            <span class="label">Koreksi:</span>
            {{ number_format($kalibrasi->getMaxKoreksi(), 3) }}
        </div>

    </div>

</body>

</html>