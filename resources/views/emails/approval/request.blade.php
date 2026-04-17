<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Request Approval Sertifikat</title>
    </head>

    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background: #f5f5f5;">

        <!-- Main Container -->
        <div
            style="max-width: 600px; margin: 40px auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 30px;">

            <!-- Header -->
            <h2 style="text-align: center; margin-top: 0; color: #111827;">Request Approval Sertifikat Kalibrasi</h2>
            <p style="text-align: center; color: #6b7280; font-size: 14px;">Diperlukan tindakan Anda untuk melakukan
                approval</p>

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;">

            <!-- Content -->
            <p style="color: #111827; font-size: 15px;">
                Halo <strong>{{ $approverName }}</strong>,
            </p>

            <p style="color: #374151; font-size: 14px; line-height: 1.6;">
                Anda memiliki permintaan approval untuk sertifikat kalibrasi berikut:
            </p>

            <!-- Info List -->
            <ul style="color: #374151; font-size: 14px; line-height: 1.8; list-style-type: disc; padding-left: 20px;">
                <li><strong>Kode Alat:</strong> {{ $sertifikat->kalibrasi->alat->kode_alat ?? '-' }}</li>
                <li><strong>Jenis Kalibrasi:</strong> {{ $sertifikat->kalibrasi->jenis_kalibrasi ?? '-' }}</li>
                <li><strong>Lokasi Kalibrasi:</strong> {{ $sertifikat->kalibrasi->lokasi_kalibrasi ?? '-' }}</li>
                <li><strong>Tanggal Kalibrasi:</strong> {{ $sertifikat->kalibrasi->tgl_kalibrasi ?? '-' }}</li>
                <li><strong>Dibuat Oleh:</strong> {{ $sertifikat->user->username ?? '-' }}</li>
            </ul>

            <!-- Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('kalibrasi.certificate.approvals') }}"
                    style="display: inline-block;
                       background-color: #2563eb;
                       color: #ffffff;
                       text-decoration: none;
                       padding: 12px 32px;
                       border-radius: 6px;
                       font-weight: 600;
                       font-size: 14px;">
                    Review & Approve
                </a>
            </div>

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

            <!-- Footer -->
            <p style="color: #9ca3af; font-size: 12px; text-align: center; margin-top: 0;">
                Mohon jangan balas email ini.
            </p>

            <p style="color: #9ca3af; font-size: 12px; text-align: center; margin-top: 10px;">
                Terima kasih atas perhatian Anda 🙏<br>
            </p>

        </div>

    </body>

</html>
