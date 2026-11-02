<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak History Card - {{ $area }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/kecap.png') }}">
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f6f9;
            color: #000;
        }
        .no-print-bar {
            max-width: 820px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            gap: 15px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: 1px solid transparent;
        }
        .btn-primary {
            background-color: #0d6efd;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        .page-sheet {
            max-width: 820px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border: 2px solid #000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            min-height: 1050px;
            position: relative;
        }
        .sheet-header-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-bottom: 0;
        }
        .sheet-header-table td {
            border: 1px solid #000;
            padding: 6px 10px;
            vertical-align: middle;
        }
        .sheet-main-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            border-top: none;
        }
        .sheet-main-table th, .sheet-main-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 11px;
            color: #000;
        }
        .sheet-main-table th {
            background-color: #f8f9fa;
            text-align: center;
            font-weight: bold;
        }
        .row-blank td {
            height: 26px;
        }
        .sheet-footer-code {
            position: absolute;
            bottom: 12px;
            right: 20px;
            font-size: 10px;
            font-weight: bold;
            color: #000;
            letter-spacing: 0.5px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .page-sheet {
                border: 2px solid #000;
                box-shadow: none;
                margin: 0;
                padding: 15px;
                max-width: 100%;
                width: 100%;
                min-height: auto;
            }
            .sheet-footer-code {
                position: static;
                text-align: right;
                margin-top: 15px;
            }
            @page {
                size: A4 portrait;
                margin: 8mm;
            }
        }
    </style>
</head>
<body>

    {{-- Toolbar Screen Only --}}
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <label for="selectPrintArea" style="font-weight: bold; font-size: 13px;">Pilih Area:</label>
            <select id="selectPrintArea" onchange="changePrintArea(this.value)" style="padding: 6px 12px; font-size: 13px; font-weight: 600; border-radius: 4px; border: 1px solid #0d6efd; color: #0d6efd; background: #fff;">
                @foreach($areas as $item)
                    <option value="{{ $item }}" {{ $area === $item ? 'selected' : '' }}>
                        {{ $item }}
                    </option>
                @endforeach
            </select>
            <span style="color: #666; font-size: 12px;">({{ count($records) }} Catatan)</span>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Cetak / Print PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary" style="margin-left: 8px;">
                Tutup
            </button>
        </div>
    </div>

    <div class="page-sheet">
        {{-- Header matching physical document --}}
        <table class="sheet-header-table">
            <tr>
                <td style="width: 25%; text-align: center; background-color: #fff;">
                    <img src="{{ asset('ejo-engineer-assets/Logo-BAS.png') }}"
                        alt="Logo BAS"
                        style="max-height: 46px; max-width: 100%; object-fit: contain;">
                </td>
                <td style="width: 75%; text-align: center;">
                    <div style="font-size: 13px; font-weight: bold; letter-spacing: 0.5px;">
                        PT BUMI ALAM SEGAR
                    </div>
                    <div style="font-size: 12px; font-weight: bold; border-top: 1px solid #000; margin-top: 3px; padding-top: 3px;">
                        HISTORY CARD
                    </div>
                    {{-- Row Area Name replaces the dotted line --}}
                    <div style="font-size: 12px; font-weight: bold; border-top: 1px solid #000; margin-top: 3px; padding-top: 3px; text-transform: uppercase; letter-spacing: 0.5px;">
                        {{ $area }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- Table --}}
        <table class="sheet-main-table">
            <thead>
                <tr>
                    <th style="width: 5%;" rowspan="2">No</th>
                    <th style="width: 25%;" colspan="2">Pelaksanaan</th>
                    <th style="width: 53%;" rowspan="2">Deskripsi</th>
                    <th style="width: 17%;" rowspan="2">Teknisi</th>
                </tr>
                <tr>
                    <th style="width: 13%;">Tanggal</th>
                    <th style="width: 12%;">Jam (WIB)</th>
                </tr>
            </thead>
            <tbody>
                @php $totalRows = count($records); @endphp
                @foreach($records as $idx => $r)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="text-align: center;">{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td>
                        <td style="text-align: center;">{{ substr($r->jam, 0, 5) }} WIB</td>
                        <td style="white-space: pre-wrap; word-break: break-word;">{{ $r->deskripsi }}</td>
                        <td style="text-align: center;">{{ $r->creator ? ($r->creator->fullname ?: ($r->creator->nama ?? $r->creator->username)) : ($r->teknisi ?: '-') }}</td>
                    </tr>
                @endforeach

                {{-- Fill remaining rows so the physical form has empty lines like the document --}}
                @php $minLines = 30; $blankLines = max(0, $minLines - $totalRows); @endphp
                @for($b = 0; $b < $blankLines; $b++)
                    <tr class="row-blank">
                        <td style="text-align: center; color: #888;">{{ $totalRows + $b + 1 }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        {{-- Footer document code --}}
        <div class="sheet-footer-code">
            FRM/EUT/01/009/001-00
        </div>
    </div>

    <script>
        function changePrintArea(val) {
            const url = new URL(window.location.href);
            url.searchParams.set('area', val);
            window.location.href = url.toString();
        }
    </script>
</body>
</html>
