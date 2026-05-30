<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
            size: 283.46pt 113.38pt;
            /* Explicitly define size in CSS too */
        }

        * {
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 283.46pt;
            height: 113.38pt;
            overflow: hidden;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #fff;
        }

        .sticker-box {
            width: 283.46pt;
            height: 113.38pt;
            border: 2pt solid #2c3e50;
            position: absolute;
            top: 0;
            left: 0;
            background-color: #fff;
            overflow: hidden;
        }

        .header {
            background-color: #2c3e50;
            padding: 4pt 10pt;
            color: white;
            height: 28pt;
            width: 100%;
        }

        .logo-container {
            float: left;
            width: 80pt;
            height: 20pt;
        }

        .logo-container img {
            max-height: 20pt;
            max-width: 80pt;
        }

        .header-text {
            float: right;
            text-align: right;
            padding-right: 20pt;
            font-weight: bold;
            font-size: 11pt;
            margin-top: 2pt;
        }

        .content {
            padding: 5pt 10pt;
            clear: both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2pt;
        }

        td {
            padding: 2pt 0;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
            font-size: 10px;
        }

        .label {
            font-weight: bold;
            color: #555;
            width: 90pt;
            text-transform: uppercase;
        }

        .value {
            color: #000;
            font-weight: bold;
            font-size: 11px;
        }

        .footer {
            position: absolute;
            bottom: 3pt;
            right: 10pt;
            font-size: 7px;
            color: #7f8c8d;
            font-style: italic;
        }

        .highlight {
            color: #c0392b;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @php
    $path = public_path('assets/images/logo/bas.png');
    $base64 = null;
    if (file_exists($path)) {
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
    @endphp

    <div class="sticker-box">
        <div class="header">
            <div class="logo-container">
                @if($base64)
                <img src="{{ $base64 }}">
                @else
                <span style="font-size: 9pt; font-weight: bold;">PT. BAS</span>
                @endif
            </div>
            <div class="header-text">
                STIKER KALIBRASI
            </div>
        </div>

        <div class="content">
            <table>
                <tr>
                    <td class="label">ID Alat</td>
                    <td class="value">: {{ $kalibrasi->alat->kode_alat ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Tgl Kalibrasi</td>
                    <td class="value">: {{ \Carbon\Carbon::parse($kalibrasi->tgl_kalibrasi)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Tgl Kalib. Ulang</td>
                    <td class="value">: <span class="highlight">{{ $kalibrasi->tgl_kalibrasi_ulang ? \Carbon\Carbon::parse($kalibrasi->tgl_kalibrasi_ulang)->format('d-m-Y') : \Carbon\Carbon::parse($kalibrasi->tgl_kalibrasi)->addYear()->format('d-m-Y') }}</span></td>
                </tr>
                <tr>
                    <td class="label">Koreksi</td>
                    <td class="value">: {{ number_format($kalibrasi->getMaxKoreksi(), 3) }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Oleh: {{ $kalibrasi->user->username ?? '-' }} | Generated: {{ now()->format('d/m/Y') }}
        </div>
    </div>
</body>

</html>