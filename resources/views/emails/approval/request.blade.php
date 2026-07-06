<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Request Approval Sertifikat</title>
    </head>

    <body
        style="margin: 0; padding: 0; font-family: 'Segoe UI', Helvetica, Arial, sans-serif; background-color: #f4f6f9; -webkit-font-smoothing: antialiased;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%"
            style="table-layout: fixed; background-color: #f4f6f9;">
            <tr>
                <td align="center" style="padding: 40px 10px;">
                    <!-- Card Container -->
                    <table border="0" cellpadding="0" cellspacing="0" width="100%"
                        style="max-width: 550px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #eef2f6;">

                        <!-- Decorative Top Banner -->
                        <tr>
                            <td align="center"
                                style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding: 35px 20px;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td align="center"
                                            style="color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; line-height: 30px;">
                                            Request Approval
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center"
                                            style="color: #bfdbfe; font-size: 14px; font-weight: 500; padding-top: 5px; text-transform: uppercase; letter-spacing: 1px;">
                                            Sertifikat Kalibrasi
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Body Content -->
                        <tr>
                            <td style="padding: 40px 30px;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td
                                            style="color: #1e293b; font-size: 16px; font-weight: 600; padding-bottom: 12px;">
                                            Halo, {{ $approverName }}!
                                        </td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="color: #475569; font-size: 14px; line-height: 1.6; padding-bottom: 24px;">
                                            Anda memiliki permintaan persetujuan baru untuk sertifikat kalibrasi yang
                                            baru saja diajukan. Berikut rincian datanya:
                                        </td>
                                    </tr>

                                    <!-- Info Table -->
                                    <tr>
                                        <td style="padding-bottom: 30px;">
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                                style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #f1f5f9; padding: 20px;">
                                                <tr>
                                                    <td width="35%"
                                                        style="color: #64748b; font-size: 13px; font-weight: 600; padding: 6px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Kode Alat</td>
                                                    <td
                                                        style="color: #334155; font-size: 14px; font-weight: 600; padding: 6px 0;">
                                                        {{ $sertifikat->kalibrasi->alat->kode_alat ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="color: #64748b; font-size: 13px; font-weight: 600; padding: 6px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Jenis Kalibrasi</td>
                                                    <td
                                                        style="color: #334155; font-size: 14px; padding: 6px 0; font-weight: 500;">
                                                        {{ Str::title(str_replace('_', ' ', $sertifikat->kalibrasi->jenis_kalibrasi ?? '-')) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="color: #64748b; font-size: 13px; font-weight: 600; padding: 6px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Lokasi</td>
                                                    <td
                                                        style="color: #334155; font-size: 14px; padding: 6px 0; font-weight: 500;">
                                                        {{ $sertifikat->kalibrasi->lokasi_kalibrasi ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="color: #64748b; font-size: 13px; font-weight: 600; padding: 6px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Tgl Kalibrasi</td>
                                                    <td
                                                        style="color: #334155; font-size: 14px; padding: 6px 0; font-weight: 500;">
                                                        {{ $sertifikat->kalibrasi->tgl_kalibrasi ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="color: #64748b; font-size: 13px; font-weight: 600; padding: 6px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Dibuat Oleh</td>
                                                    <td
                                                        style="color: #334155; font-size: 14px; padding: 6px 0; font-weight: 500;">
                                                        {{ $sertifikat->user->username ?? '-' }}</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <!-- CTA Button -->
                                    <tr>
                                        <td align="center" style="padding-bottom: 10px;">
                                            <a href="{{ route('kalibrasi.certificate.approvals') }}"
                                                style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 8px; font-weight: 600; font-size: 14px; box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2); transition: background-color 0.2s;">
                                                Tinjau & Setujui
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td
                                style="background-color: #f8fafc; border-top: 1px solid #f1f5f9; padding: 25px 30px; text-align: center;">
                                <p style="margin: 0; color: #94a3b8; font-size: 11px; line-height: 1.5;">
                                    Ini adalah email otomatis dari Aplikasi Sistem Engineering. Mohon tidak membalas
                                    email ini secara langsung.
                                </p>
                                <p style="margin: 8px 0 0 0; color: #64748b; font-size: 12px; font-weight: 500;">
                                    Terima kasih atas perhatian Anda 🙏
                                </p>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
    </body>

</html>
