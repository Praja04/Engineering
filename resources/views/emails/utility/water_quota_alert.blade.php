<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Peringatan Kuota Air Mentah</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f1f5f9;
                margin: 0;
                padding: 24px;
                color: #334155;
            }

            .container {
                max-width: 650px;
                margin: 0 auto;
                background-color: #ffffff;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
                border: 1px solid #e2e8f0;
            }

            .header {
                background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
                color: #ffffff;
                padding: 28px 24px;
                text-align: center;
            }

            .header h1 {
                margin: 0;
                font-size: 20px;
                font-weight: 700;
                letter-spacing: -0.025em;
            }

            .header p {
                margin: 8px 0 0 0;
                font-size: 13px;
                color: #e0f2fe;
            }

            .badge-warning {
                display: inline-block;
                background-color: #fef08a;
                color: #854d0e;
                font-weight: 700;
                font-size: 11px;
                padding: 4px 12px;
                border-radius: 9999px;
                margin-top: 10px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .content {
                padding: 24px;
            }

            .intro {
                font-size: 14px;
                line-height: 1.6;
                margin-bottom: 20px;
                color: #475569;
            }

            .table-container {
                width: 100%;
                overflow-x: auto;
                margin-bottom: 24px;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
                text-align: left;
            }

            thead {
                background-color: #f8fafc;
                color: #475569;
                font-weight: 600;
            }

            th,
            td {
                padding: 12px 14px;
                border-bottom: 1px solid #e2e8f0;
            }

            tr:last-child td {
                border-bottom: none;
            }

            .text-right {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }

            .tag-danger {
                color: #dc2626;
                font-weight: bold;
            }

            .tag-warning {
                color: #d97706;
                font-weight: bold;
            }

            .notes-box {
                background-color: #fffbeb;
                border-left: 4px solid #f59e0b;
                padding: 14px 16px;
                border-radius: 6px;
                font-size: 13px;
                color: #92400e;
                line-height: 1.5;
                margin-bottom: 24px;
            }

            .footer {
                background-color: #f8fafc;
                padding: 16px 24px;
                text-align: center;
                font-size: 12px;
                color: #94a3b8;
                border-top: 1px solid #e2e8f0;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <!-- Header -->
            <div class="header">
                <h1>⚠️ Peringatan Kuota Air Mentah (≥ 80%)</h1>
                <p>PT Bumi Alam Segar — Engineering Utility</p>
                <div class="badge-warning">Periode {{ $monthName }} {{ $year }} ({{ $daysInMonth }} Hari)
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                <p class="intro">
                    Halo <strong>{{ $recipientName }}</strong>,<br><br>
                    Sistem otomatis memantau pemakaian air mentah harian. Diberitahukan bahwa terdapat sumur/sumber air
                    mentah yang pemakaiannya pada bulan berjalan telah <strong>mencapai atau melampaui batas
                        80%</strong> dari total kuota 1 bulan penuh:
                </p>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Sumber Air</th>
                                <th class="text-right">Kuota Harian</th>
                                <th class="text-right">Kuota 1 Bulan</th>
                                <th class="text-right">Aktual s.d Hari Ini</th>
                                <th class="text-right">% Pemakaian</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alertItems as $item)
                                <tr>
                                    <td><strong>{{ $item['source'] }}</strong></td>
                                    <td class="text-right">{{ number_format($item['daily_quota'], 1, ',', '.') }}
                                        m³/hari</td>
                                    <td class="text-right">{{ number_format($item['monthly_quota'], 1, ',', '.') }} m³
                                    </td>
                                    <td class="text-right" style="font-weight: 600;">
                                        {{ number_format($item['actual'], 1, ',', '.') }} m³</td>
                                    <td
                                        class="text-right {{ $item['percentage'] >= 100 ? 'tag-danger' : 'tag-warning' }}">
                                        {{ number_format($item['percentage'], 1, ',', '.') }}%
                                    </td>
                                    <td class="text-center">
                                        @if ($item['percentage'] >= 100)
                                            <span class="tag-danger">Over Kuota</span>
                                        @else
                                            <span class="tag-warning">Waspada (≥80%)</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="notes-box">
                    <strong>Catatan Pengingat:</strong>
                    <ul style="margin: 6px 0 0 0; padding-left: 20px;">
                        <li>Perhitungan 80% dihitung dari total kuota 1 bulan penuh ({{ $daysInMonth }} hari x kuota
                            harian).</li>
                        <li>Mohon dilakukan evaluasi pemakaian debit sumur dan koordinasikan dengan tim operasional
                            terkait untuk mencegah over kuota berlebih.</li>
                    </ul>
                </div>

                <p style="font-size: 12px; color: #64748b; margin: 0;">
                    Tanggal Pengecekan: <strong>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y H:i') }}
                        WIB</strong>
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                Pesan otomatis ini dibuat oleh Sistem Engineering Utility PT BAS.<br>
                Tidak perlu membalas email ini secara langsung.
            </div>
        </div>
    </body>

</html>
