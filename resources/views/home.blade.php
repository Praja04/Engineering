@extends('layouts.app')

@section('styles')
    <style>
        .hero-card {
            height: 70vh;
            border-radius: 10px;
            background-image: url('{{ asset('assets/images/background_home.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            overflow: hidden;
        }

        .hero-overlay {
            background: linear-gradient(135deg,
                    rgba(71, 129, 167, 0.90) 0%,
                    rgba(50, 50, 50, 0.82) 50%,
                    rgba(74, 144, 226, 0.72) 100%);
            position: absolute;
            inset: 0;
            border-radius: 10px;
            z-index: 1;
        }

        /* Text Welcome di tengah kiri */
        .hero-text {
            position: absolute;
            top: 50%;
            left: 10%;
            transform: translateY(-50%);
            z-index: 3;
        }

        .hero-text h1 {
            margin: 0;
            line-height: 1.1;
        }

        .hero-text .welcome {
            font-size: 40px;
            font-weight: 300;
        }

        .hero-text .name {
            font-size: 50px;
            font-weight: 700;
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            border-right: 4px solid #fff;
            animation: blink 0.8s step-end infinite;
        }

        @keyframes blink {

            from,
            to {
                border-color: transparent;
            }

            50% {
                border-color: white;
            }
        }

        /* Logo di kanan atas */
        .bas-logo {
            position: absolute;
            top: 30px;
            right: 7%;
            width: 120px;
            z-index: 4;
            animation: bounce 2s infinite ease-in-out;
        }

        .wings-logo {
            position: absolute;
            top: 30px;
            right: 15%;
            width: 90px;
            z-index: 4;
            animation: bounce 3s infinite ease-in-out;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .bas-logo {
                right: 5%;
                width: 40px;
            }

            .wings-logo {
                right: 15%;
                width: 70px;
            }
        }

        @media (max-width: 768px) {
            .bas-logo {
                top: 20px;
                right: 5%;
                width: 35px;
            }

            .wings-logo {
                top: 20px;
                right: 18%;
                width: 60px;
            }

            .hero-text .welcome {
                font-size: 32px;
            }

            .hero-text .name {
                font-size: 36px;
            }
        }

        @media (max-width: 480px) {
            .bas-logo {
                top: 20px;
                right: 5%;
                width: 28px;
            }

            .wings-logo {
                top: 20px;
                right: 20%;
                width: 48px;
            }

            .hero-text .welcome {
                font-size: 28px;
            }

            .hero-text .name {
                font-size: 32px;
            }

            .hero-card {
                height: 60vh;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-content d-flex align-items-center min-vh-100 bg-light">
        <div class="container-fluid">
            <div class="row mb-2">
                {{-- Hero Banner --}}
                <div class="col-12">
                    <div class="hero-card shadow">
                        <div class="hero-overlay"></div>

                        <!-- Logo -->
                        <img src="{{ asset('assets/images/logo/kecap.png') }}" class="bas-logo" alt="Logo Bas">

                        <!-- Text dengan typing effect -->
                        <div class="hero-text">
                            <h1 class="welcome text-white">Welcome,</h1>
                            <h1 class="name text-white" id="typing-name"></h1>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Looping typing animation untuk nama
        document.addEventListener('DOMContentLoaded', function() {
            const nameElement = document.getElementById('typing-name');
            const fullName = "{{ Auth::user()->nama_lengkap ?? (Auth::user()->username ?? 'User') }}";
            let index = 0;
            let isDeleting = false;
            let phase = 'typing'; // typing / pausing / deleting

            function animateText() {
                if (phase === 'typing') {
                    if (index < fullName.length) {
                        nameElement.textContent += fullName.charAt(index);
                        index++;
                        setTimeout(animateText, 80); // kecepatan ketik
                    } else {
                        // Selesai ketik → pause sebentar
                        phase = 'pausing';
                        setTimeout(animateText, 2500); // pause 2.5 detik setelah selesai
                    }
                } else if (phase === 'pausing') {
                    // Mulai hapus
                    phase = 'deleting';
                    setTimeout(animateText, 50); // mulai hapus setelah pause
                } else if (phase === 'deleting') {
                    if (index > 0) {
                        nameElement.textContent = fullName.substring(0, index - 1);
                        index--;
                        setTimeout(animateText, 40); // kecepatan hapus (lebih cepat dari ketik)
                    } else {
                        // Selesai hapus → mulai ketik ulang
                        phase = 'typing';
                        setTimeout(animateText, 800); // jeda kecil sebelum mulai ketik lagi
                    }
                }
            }

            // Mulai animasi
            animateText();

            // Optional: cursor blinking tetap aktif selama animasi
            nameElement.style.borderRight = '4px solid white';
            nameElement.style.animation = 'blink 0.8s step-end infinite';
        });

        // Toastr notifications (tetap sama)
        @if (session('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "0",
                "extendedTimeOut": "0",
                "tapToDismiss": false
            };
            toastr.error("{{ session('error') }}", "Peringatan!");
        @endif

        @if (session('success'))
            toastr.success("{{ session('success') }}", "Berhasil!");
        @endif
    </script>
@endsection
