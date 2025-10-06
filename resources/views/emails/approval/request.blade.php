<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Request Approval Sertifikat</title>
    </head>

    <body
        style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px;">

        <!-- Main Container -->
        <div
            style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">

            <!-- Header -->
            <div
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                <div
                    style="background: rgba(255,255,255,0.2); width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                            stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; letter-spacing: -0.5px;">
                    Request Approval</h1>
                <p style="color: rgba(255,255,255,0.9); margin: 8px 0 0 0; font-size: 14px;">Sertifikat Kalibrasi</p>
            </div>

            <!-- Content -->
            <div style="padding: 40px 30px;">
                <p style="color: #334155; font-size: 16px; line-height: 1.6; margin: 0 0 10px 0;">
                    Halo <strong style="color: #667eea;">{{ $approverName }}</strong>,
                </p>

                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 30px 0;">
                    Anda memiliki permintaan approval untuk sertifikat kalibrasi berikut:
                </p>

                <!-- Info Card -->
                <div
                    style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 12px; padding: 24px; margin-bottom: 30px; border-left: 4px solid #667eea;">
                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
                        <tr>
                            <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                <div
                                    style="color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                                    Lokasi Kalibrasi</div>
                                <div style="color: #1e293b; font-weight: 600; font-size: 15px;">
                                    {{ $sertifikat->kalibrasi->lokasi_kalibrasi ?? '-' }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                <div
                                    style="color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                                    Tanggal Kalibrasi</div>
                                <div style="color: #1e293b; font-weight: 600; font-size: 15px;">
                                    {{ $sertifikat->kalibrasi->tgl_kalibrasi ?? '-' }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 0;">
                                <div
                                    style="color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                                    Dibuat Oleh</div>
                                <div style="color: #1e293b; font-weight: 600; font-size: 15px;">
                                    {{ $sertifikat->user->username ?? '-' }}</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- CTA Button -->
                <div style="text-align: center; margin: 35px 0;">
                    <a href="{{ route('kalibrasi.certificate.approval.detail', $sertifikat->id) }}"
                        style="display: inline-block; 
                          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                          color: #ffffff; 
                          text-decoration: none; 
                          padding: 16px 48px; 
                          border-radius: 50px; 
                          font-weight: 600; 
                          font-size: 15px; 
                          box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
                          transition: all 0.3s ease;
                          letter-spacing: 0.5px;">
                        ✓ Review & Approve
                    </a>
                </div>

                <p
                    style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin: 30px 0 0 0; text-align: center; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                    Jika Anda memiliki pertanyaan, silakan hubungi tim kami.
                </p>
                <p
                    style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin: 30px 0 0 0; text-align: center; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                    Mohon jangan balas email ini.
                </p>
            </div>

            <!-- Footer -->
            <div style="background: #f8fafc; padding: 24px 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                <p style="color: #64748b; font-size: 13px; margin: 0 0 8px 0;">
                    Terima kasih atas perhatian Anda 🙏
                </p>
                <p style="color: #94a3b8; font-size: 12px; margin: 0; font-weight: 600;">
                    {{ config('app.name') }}
                </p>
            </div>

        </div>

        <!-- Spacer -->
        <div style="height: 40px;"></div>

    </body>

</html>
