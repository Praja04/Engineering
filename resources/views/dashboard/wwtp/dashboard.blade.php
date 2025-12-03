@extends('layouts.app')

@section('title', 'Dashboard WWTP')

@section('styles')
<style>
    .coming-soon-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        margin: 20px;
        position: relative;
        overflow: hidden;
    }

    .coming-soon-container::before {
        content: '';
        position: absolute;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: shine 3s infinite;
    }

    @keyframes shine {
        0% {
            transform: translateX(-100%) translateY(-100%) rotate(45deg);
        }

        100% {
            transform: translateX(100%) translateY(100%) rotate(45deg);
        }
    }

    .coming-soon-content {
        text-align: center;
        color: white;
        z-index: 1;
        padding: 40px;
    }

    .coming-soon-icon {
        font-size: 100px;
        margin-bottom: 30px;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    .coming-soon-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .coming-soon-subtitle {
        font-size: 1.5rem;
        margin-bottom: 30px;
        opacity: 0.9;
    }

    .coming-soon-description {
        font-size: 1.1rem;
        margin-bottom: 40px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        opacity: 0.85;
    }

    .feature-list {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .feature-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .feature-icon {
        font-size: 40px;
        background: rgba(255, 255, 255, 0.2);
        padding: 20px;
        border-radius: 50%;
        backdrop-filter: blur(10px);
    }

    .feature-text {
        font-size: 1rem;
        font-weight: 500;
    }

    .countdown-container {
        display: flex;
        gap: 20px;
        justify-content: center;
        margin-top: 40px;
    }

    .countdown-item {
        background: rgba(255, 255, 255, 0.2);
        padding: 20px 30px;
        border-radius: 10px;
        backdrop-filter: blur(10px);
        min-width: 100px;
    }

    .countdown-number {
        font-size: 2.5rem;
        font-weight: 700;
        display: block;
    }

    .countdown-label {
        font-size: 0.9rem;
        opacity: 0.9;
        text-transform: uppercase;
    }

    @media (max-width: 768px) {
        .coming-soon-title {
            font-size: 2.5rem;
        }

        .coming-soon-subtitle {
            font-size: 1.2rem;
        }

        .feature-list {
            gap: 20px;
        }

        .countdown-container {
            flex-wrap: wrap;
            gap: 15px;
        }

        .countdown-item {
            padding: 15px 20px;
            min-width: 80px;
        }

        .countdown-number {
            font-size: 2rem;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="coming-soon-container">
            <div class="coming-soon-content">
                <div class="coming-soon-icon">
                    🚧
                </div>

                <h1 class="coming-soon-title">Coming Soon</h1>

                <p class="coming-soon-subtitle">
                    Dashboard WWTP Sedang Dalam Pengembangan
                </p>

                <p class="coming-soon-description">
                    Kami sedang membangun dashboard monitoring yang canggih untuk sistem pengolahan air limbah (WWTP).
                    Pantau terus untuk fitur-fitur menarik yang akan segera hadir!
                </p>

                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon">📊</div>
                        <div class="feature-text">Real-time Monitoring</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">📈</div>
                        <div class="feature-text">Analytics & Reports</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">🔔</div>
                        <div class="feature-text">Smart Alerts</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">⚙️</div>
                        <div class="feature-text">System Control</div>
                    </div>
                </div>

                <div class="countdown-container">
                    <div class="countdown-item">
                        <span class="countdown-number" id="days">--</span>
                        <span class="countdown-label">Hari</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" id="hours">--</span>
                        <span class="countdown-label">Jam</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" id="minutes">--</span>
                        <span class="countdown-label">Menit</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" id="seconds">--</span>
                        <span class="countdown-label">Detik</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Set tanggal target (sesuaikan dengan tanggal launch Anda)
    const targetDate = new Date('2025-12-30T23:59:59').getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance > 0) {
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('days').textContent = String(days).padStart(2, '0');
            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
        } else {
            document.getElementById('days').textContent = '00';
            document.getElementById('hours').textContent = '00';
            document.getElementById('minutes').textContent = '00';
            document.getElementById('seconds').textContent = '00';
        }
    }

    // Update countdown setiap detik
    updateCountdown();
    setInterval(updateCountdown, 1000);
</script>
@endsection