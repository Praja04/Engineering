<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <title>Laporan Analisa WWTP - {{ $analisa->analisa_date }}</title>
        <style>
            @page {
                margin: 40px 50px 50px 50px;
            }

            body {
                font-family: 'Helvetica', 'Arial', sans-serif;
                font-size: 10pt;
                line-height: 1.4;
                color: #333333;
            }

            /* Header Style */
            .header-table {
                width: 100%;
                border-collapse: collapse;
                border-bottom: 2px solid #299cdb;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }

            .header-logo {
                width: 120px;
                vertical-align: middle;
            }

            .header-logo img {
                max-height: 45px;
            }

            .header-logo-text {
                font-size: 16pt;
                font-weight: bold;
                color: #299cdb;
            }

            .header-title-container {
                text-align: right;
                vertical-align: middle;
            }

            .header-title {
                font-size: 15pt;
                font-weight: bold;
                color: #2c3e50;
                margin: 0;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .header-subtitle {
                font-size: 8.5pt;
                color: #7f8c8d;
                margin: 3px 0 0 0;
            }

            /* Metadata Block */
            .meta-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                background-color: #f8fafc;
                border: 1px solid #e2e8f0;
            }

            .meta-table td {
                padding: 8px 12px;
                font-size: 9pt;
                vertical-align: top;
            }

            .meta-label {
                font-weight: bold;
                color: #4a5568;
                width: 20%;
            }

            .meta-value {
                color: #1a202c;
                width: 30%;
            }

            /* Section Parameter Cards */
            .parameter-section {
                margin-bottom: 20px;
                page-break-inside: avoid;
            }

            .parameter-title-bar {
                background-color: #f1f5f9;
                border-left: 4px solid #299cdb;
                padding: 6px 10px;
                margin-bottom: 8px;
            }

            .parameter-title {
                font-size: 10pt;
                font-weight: bold;
                color: #2c3e50;
                margin: 0;
            }

            .parameter-unit {
                font-size: 8.5pt;
                color: #64748b;
                font-weight: normal;
            }

            /* Main Data Table */
            .data-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 5px;
            }

            .data-table th {
                background-color: #299cdb;
                color: #ffffff;
                font-size: 9pt;
                font-weight: bold;
                text-transform: uppercase;
                padding: 6px 10px;
                border: 1px solid #299cdb;
                text-align: left;
            }

            .data-table th.text-center {
                text-align: center;
            }

            .data-table td {
                padding: 6px 10px;
                font-size: 9pt;
                border-bottom: 1px solid #e2e8f0;
                border-left: 1px solid #e2e8f0;
                border-right: 1px solid #e2e8f0;
                color: #334155;
            }

            .data-table tr:nth-child(even) td {
                background-color: #f8fafc;
            }

            .data-table td.text-center {
                text-align: center;
            }

            .value-normal {
                font-weight: bold;
                color: #0f766e;
            }

            .value-exceeds {
                font-weight: bold;
                color: #b91c1c;
                background-color: #fee2e2 !important;
            }

            .badge-exceeds {
                display: inline-block;
                background-color: #ef4444;
                color: white;
                font-size: 7pt;
                padding: 1px 4px;
                border-radius: 2px;
                font-weight: bold;
                margin-left: 5px;
                vertical-align: middle;
            }

            /* Footer Style */
            .footer {
                position: fixed;
                bottom: -30px;
                left: 0;
                right: 0;
                height: 20px;
                font-size: 7.5pt;
                color: #94a3b8;
                text-align: center;
                border-top: 1px solid #e2e8f0;
                padding-top: 5px;
            }

            .page-number:after {
                content: counter(page);
            }

            /* Signatures Table */
            .signature-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 40px;
                page-break-inside: avoid;
            }

            .signature-table td {
                width: 33.33%;
                text-align: center;
                vertical-align: bottom;
                padding: 10px;
                border: none;
                font-size: 9pt;
            }

            .signature-title {
                font-weight: bold;
                margin-bottom: 55px;
                color: #2c3e50;
            }

            .signature-name {
                font-weight: bold;
                text-decoration: underline;
                color: #1a202c;
                margin-bottom: 2px;
            }

            .signature-date {
                font-size: 7.5pt;
                color: #718096;
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

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    @if ($base64)
                        <img src="{{ $base64 }}" alt="Logo">
                    @else
                        <span class="header-logo-text">PT. BAS</span>
                    @endif
                </td>
                <td class="header-title-container">
                    <h1 class="header-title">Laporan Analisa WWTP</h1>
                    <p class="header-subtitle">Wastewater Treatment Plant - Parameter Monitoring Report</p>
                </td>
            </tr>
        </table>

        <!-- Metadata -->
        <table class="meta-table">
            <tr>
                <td class="meta-label">Tanggal Analisa</td>
                <td class="meta-value">:
                    {{ \Carbon\Carbon::parse($analisa->analisa_date)->locale('id')->translatedFormat('d F Y') }}</td>
                <td class="meta-label">Tanggal Cetak</td>
                <td class="meta-value">: {{ now()->locale('id')->translatedFormat('d F Y H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="meta-label">Dibuat Oleh</td>
                <td class="meta-value">: {{ $analisa->creator->username ?? '-' }}</td>
                <td class="meta-label">Status Dokumen</td>
                <td class="meta-value">: 
                    @if ($analisa->status === 'submitted')
                        <span style="color: #d97706; font-weight: bold;">Menunggu Foreman</span>
                    @elseif ($analisa->status === 'approved_foreman')
                        <span style="color: #2563eb; font-weight: bold;">Menunggu Supervisor</span>
                    @elseif ($analisa->status === 'approved_supervisor')
                        <span style="color: #16a34a; font-weight: bold;">Terverifikasi</span>
                    @elseif ($analisa->status === 'rejected')
                        <span style="color: #dc2626; font-weight: bold;">Ditolak</span>
                    @else
                        <span style="color: #64748b; font-weight: bold;">{{ ucfirst($analisa->status ?? 'submitted') }}</span>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Parameter Tables -->
        @foreach ($parameterData as $param)
            <div class="parameter-section">
                <div class="parameter-title-bar">
                    <h3 class="parameter-title">
                        {{ $param['name'] }}
                        <span class="parameter-unit">({{ $param['unit'] ?: '-' }})</span>
                    </h3>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Point Pengukuran</th>
                            <th class="text-center" style="width: 25%;">Nilai Standar</th>
                            <th class="text-center" style="width: 25%;">Hasil Analisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($param['points'] as $point)
                            @php
                                $key = $point['point_id'] . '_' . $param['id'];
                                $stdVal = $standards[$key] ?? null;
                                $value = $point['value'];

                                $exceeds = false;
                                if ($stdVal !== null && $value !== null) {
                                    $valFloat = (float) $value;
                                    $stdFloat = (float) $stdVal;
                                    if ($valFloat > $stdFloat) {
                                        $exceeds = true;
                                    }
                                }

                                // Format standard display
                                $stdDisplay = '-';
                                if ($stdVal !== null) {
                                    $parsedStd = (float) $stdVal;
                                    $stdDisplay =
                                        $parsedStd == (int) $parsedStd
                                            ? number_format($parsedStd, 0, ',', '.')
                                            : number_format($parsedStd, 2, ',', '.');
                                }

                                // Format value display
                                $valDisplay = '-';
                                if ($value !== null) {
                                    $parsedVal = (float) $value;
                                    $valDisplay =
                                        $parsedVal == (int) $parsedVal
                                            ? number_format($parsedVal, 0, ',', '.')
                                            : number_format($parsedVal, 2, ',', '.');
                                }
                            @endphp
                            <tr>
                                <td>{{ $point['point_name'] }}</td>
                                <td class="text-center">
                                    @if ($stdVal !== null)
                                        {{ $stdDisplay }}
                                    @else
                                        <span style="color: #94a3b8; font-style: italic;">Tidak Ada</span>
                                    @endif
                                </td>
                                <td class="text-center {{ $exceeds ? 'value-exceeds' : 'value-normal' }}">
                                    {{ $valDisplay }}
                                    @if ($exceeds)
                                        <span class="badge-exceeds">OUT</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <!-- Signatures Section -->
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-title">Pelaksana / Operator</div>
                    <div style="height: 55px;">
                        @if ($analisa->status !== 'rejected')
                            <span style="color: #4a5568; font-size: 8.5pt; font-weight: bold;">PREPARED</span>
                            <br>
                            <span style="color: #718096; font-size: 7.5pt;">Digital Signed</span>
                        @endif
                    </div>
                    <div class="signature-name">{{ $analisa->pelaksana ? $analisa->pelaksana->username : ($analisa->creator->username ?? '-') }}</div>
                    <div class="signature-date">Tanggal: {{ $analisa->created_at ? $analisa->created_at->format('d-m-Y') : '-' }}</div>
                </td>
                <td>
                    <div class="signature-title">Diperiksa Oleh (Foreman)</div>
                    <div style="height: 55px;">
                        @if ($analisa->approved_foreman_at)
                            <span style="color: #2b6cb0; font-weight: bold; font-size: 9pt;">APPROVED</span>
                            <br>
                            <span style="color: #718096; font-size: 7.5pt;">{{ $analisa->approved_foreman_at->format('d-m-Y H:i') }}</span>
                        @else
                            <span style="color: #cbd5e0; font-style: italic; font-size: 8.5pt;">Belum Disetujui</span>
                        @endif
                    </div>
                    <div class="signature-name">{{ $analisa->foreman ? $analisa->foreman->username : '-' }}</div>
                    <div class="signature-date">&nbsp;</div>
                </td>
                <td>
                    <div class="signature-title">Disetujui Oleh (Supervisor)</div>
                    <div style="height: 55px;">
                        @if ($analisa->approved_supervisor_at)
                            <span style="color: #2f855a; font-weight: bold; font-size: 9pt;">APPROVED</span>
                            <br>
                            <span style="color: #718096; font-size: 7.5pt;">{{ $analisa->approved_supervisor_at->format('d-m-Y H:i') }}</span>
                        @else
                            <span style="color: #cbd5e0; font-style: italic; font-size: 8.5pt;">Belum Disetujui</span>
                        @endif
                    </div>
                    <div class="signature-name">{{ $analisa->supervisor ? $analisa->supervisor->username : '-' }}</div>
                    <div class="signature-date">&nbsp;</div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            PT. BAS &copy; {{ date('Y') }} | Halaman <span class="page-number"></span> dari Laporan Analisa WWTP
            Tanggal {{ \Carbon\Carbon::parse($analisa->analisa_date)->format('d-m-Y') }}
        </div>
    </body>

</html>
