<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Terjadi Kesalahan') | Engineering PT BAS</title>
        <link rel="shortcut icon" href="{{ asset('assets/images/logo/kecap.png') }}">

        <!-- Bootstrap & Icons Css -->
        <link href="{{ asset('material/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('material/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('material/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

        <style>
            :root {
                --error-accent: #405189;
            }

            body {
                background: linear-gradient(135deg, #f3f6f9 0%, #e2e8f0 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                margin: 0;
                padding: 1.5rem;
                color: #334155;
            }

            .error-card {
                background: #ffffff;
                border-radius: 1.25rem;
                box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.12), 0 8px 16px -6px rgba(15, 23, 42, 0.06);
                border: 1px solid rgba(226, 232, 240, 0.8);
                max-width: 620px;
                width: 100%;
                overflow: hidden;
                position: relative;
                animation: fadeInCard 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            }

            @keyframes fadeInCard {
                from {
                    opacity: 0;
                    transform: translateY(18px) scale(0.98);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .error-top-bar {
                height: 6px;
                background: linear-gradient(90deg, #405189, #0ab39c);
            }

            .error-top-bar.danger {
                background: linear-gradient(90deg, #f06548, #e63946);
            }

            .error-top-bar.warning {
                background: linear-gradient(90deg, #f7b84b, #f19c38);
            }

            .error-top-bar.info {
                background: linear-gradient(90deg, #299cdb, #405189);
            }

            .error-img-box {
                max-height: 200px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0.5rem auto 1rem;
            }

            .error-img-box img {
                max-height: 180px;
                max-width: 100%;
                object-fit: contain;
                filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.08));
            }

            .error-code-badge {
                font-size: 0.9rem;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                padding: 0.4rem 1rem;
                border-radius: 9999px;
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
            }

            .btn-action {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                padding: 0.65rem 1.4rem;
                border-radius: 9999px;
                transition: all 0.2s ease-in-out;
                text-decoration: none;
                font-size: 0.925rem;
            }

            .btn-action:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
            }

            .brand-logo-header {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding-top: 1.5rem;
            }

            .brand-logo-header img {
                height: 32px;
                object-fit: contain;
            }

            .brand-logo-header .divider {
                width: 1px;
                height: 24px;
                background: #cbd5e1;
            }

            .brand-logo-header .portal-name {
                font-size: 0.95rem;
                font-weight: 700;
                color: #1e293b;
                letter-spacing: 0.02em;
            }
        </style>
    </head>

    <body>
        @php
            $code = trim($__env->yieldContent('code', 'Error'));
            $barTheme = match ($code) {
                '403', '500' => 'danger',
                '401', '419', '429' => 'warning',
                '503' => 'info',
                default => 'primary',
            };

            $badgeTheme = match ($code) {
                '403', '500' => 'bg-danger-subtle text-danger border border-danger-subtle',
                '401', '419', '429' => 'bg-warning-subtle text-warning border border-warning-subtle',
                '503' => 'bg-info-subtle text-info border border-info-subtle',
                default => 'bg-primary-subtle text-primary border border-primary-subtle',
            };

            $defaultHeading = match ($code) {
                '401' => 'Akses Tidak Diizinkan',
                '402' => 'Pembayaran Diperlukan',
                '403' => 'Akses Ditolak (Forbidden)',
                '404' => 'Halaman Tidak Ditemukan',
                '419' => 'Sesi Halaman Kedaluwarsa',
                '429' => 'Terlalu Banyak Permintaan',
                '500' => 'Terjadi Kesalahan Server',
                '503' => 'Layanan Sedang Pemeliharaan',
                default => 'Terjadi Kendala Sistem',
            };

            $defaultDesc = match ($code) {
                '401'
                    => 'Sesi login Anda belum aktif atau telah berakhir. Silakan masuk kembali ke akun Anda untuk melanjutkan.',
                '402' => 'Halaman ini memerlukan lisensi atau langganan aktif untuk dapat dibuka.',
                '403'
                    => 'Akun Anda tidak memiliki hak akses atau permission yang diperlukan untuk membuka halaman ini.',
                '404'
                    => 'Halaman atau URL yang Anda tuju tidak ditemukan, sudah dihapus, atau mengalami perubahan alamat.',
                '419'
                    => 'Token keamanan sesi halaman Anda telah habis karena tidak ada aktivitas. Silakan muat ulang halaman.',
                '429'
                    => 'Server menerima terlalu banyak permintaan dalam waktu singkat. Mohon tunggu beberapa detik sebelum mencoba lagi.',
                '500'
                    => 'Terjadi kendala internal pada server saat memproses permintaan Anda. Silakan coba beberapa saat lagi.',
                '503'
                    => 'Sistem sedang dalam proses pemeliharaan atau optimasi berkala. Mohon kembali beberapa saat lagi.',
                default => 'Permintaan Anda tidak dapat diproses saat ini.',
            };

            $defaultImg = match ($code) {
                '404' => asset('material/assets/images/404-error.png'),
                '500' => asset('material/assets/images/server-error.png'),
                '503' => asset('material/assets/images/error500.png'),
                '403', '401' => asset('material/assets/images/error400-cover.png'),
                default => asset('material/assets/images/error.svg'),
            };
        @endphp

        <div class="error-card">
            <div class="error-top-bar {{ $barTheme }}"></div>

            <div class="brand-logo-header">
                @if (file_exists(public_path('assets/images/logo/wings.png')))
                    <img src="{{ asset('assets/images/logo/wings.png') }}" alt="Wings Logo">
                @endif
                @if (file_exists(public_path('assets/images/logo/bas.png')))
                    <div class="divider"></div>
                    <img src="{{ asset('assets/images/logo/bas.png') }}" alt="BAS Logo">
                @endif
                <div class="divider"></div>
                <span class="portal-name">ENGINEERING PORTAL</span>
            </div>

            <div class="card-body p-4 p-md-5 text-center">
                {{-- Illustration --}}
                <div class="error-img-box">
                    @hasSection('illustration')
                        @yield('illustration')
                    @else
                        <img src="{{ $defaultImg }}" alt="Error {{ $code }}">
                    @endif
                </div>

                {{-- Error Code Badge --}}
                <div class="mb-3">
                    <span class="error-code-badge {{ $badgeTheme }}">
                        <i class="ri-alert-line"></i> KODE ERROR: {{ $code }}
                    </span>
                </div>

                {{-- Heading Title --}}
                <h2 class="fw-bold text-dark mb-2 fs-22">
                    @hasSection('heading')
                        @yield('heading')
                    @else
                        {{ $defaultHeading }}
                    @endif
                </h2>

                {{-- Description / Message --}}
                <p class="text-muted fs-14 mb-4 mx-auto" style="max-width: 480px; line-height: 1.6;">
                    @hasSection('description')
                        @yield('description')
                    @else
                        @hasSection('message')
                            @php
                                $rawMsg = trim($__env->yieldContent('message'));
                            @endphp
                            @if (
                                $rawMsg &&
                                    !in_array(strtolower($rawMsg), [
                                        'not found',
                                        'forbidden',
                                        'unauthorized',
                                        'server error',
                                        'service unavailable',
                                        'page expired',
                                        'too many requests',
                                    ]))
                                {{ $rawMsg }}
                            @else
                                {{ $defaultDesc }}
                            @endif
                        @else
                            {{ $defaultDesc }}
                        @endif
                    @endif
                </p>

                {{-- Action Buttons: Back, Home & Extras --}}
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 pt-2">
                    {{-- Tombol Back --}}
                    <a href="javascript:void(0)"
                        onclick="if(window.history.length > 1){ window.history.back(); }else{ window.location.href='{{ url('/') }}'; }"
                        class="btn btn-outline-secondary btn-action shadow-sm">
                        <i class="ri-arrow-left-line me-1"></i> Kembali
                    </a>

                    {{-- Tombol Home --}}
                    <a href="{{ url('/home') }}" class="btn btn-primary btn-action shadow-sm">
                        <i class="ri-home-4-line me-1"></i> Ke Halaman Utama
                    </a>

                    {{-- Extra actions if any --}}
                    @yield('extra_actions')
                </div>

                {{-- Footer Note --}}
                <div class="mt-4 pt-3 border-top text-muted fs-12">
                    <span>&copy; {{ date('Y') }} PT Bumi Alam Segar &bull; Wings Group. All rights reserved.</span>
                </div>
            </div>
        </div>
    </body>

</html>
