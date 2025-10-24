@extends('layouts.app')

@section('styles')
    <style>
        .text-gradient {
            background: linear-gradient(90deg, #4a90e2, #8e44ad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        . {
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .icon-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Custom text colors */
        .text-purple {
            color: #6f42c1;
        }

        .text-pink {
            color: #e83e8c;
        }

        hr {
            border-top: 2px solid rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-gradient">Dashboard Data Kalibrasi</h2>
                <p class="text-muted">Pilih jenis kalibrasi di bawah untuk melihat data terkait</p>
                <hr class="w-25 mx-auto opacity-75">
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Kalibrasi Tekanan -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-success border-success text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.data.pressure') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-gauge fs-1 text-success"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Pressure</h5>
                            <p class="small text-muted mb-0">Pressure gauge, manometer, dan alat tekanan.</p>
                        </div>
                    </div>
                </div>

                <!-- Kalibrasi Suhu -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-info text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.data.dev-page') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-thermometer-lines fs-1 text-info"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Suhu</h5>
                            <p class="small text-muted mb-0">Thermometer, oven, sensor suhu, dll.</p>
                        </div>
                    </div>
                </div>

                <!-- Kalibrasi Massa -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-warning text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.data.dev-page') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-scale-balance fs-1 text-warning"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Massa</h5>
                            <p class="small text-muted mb-0">Timbangan, neraca, dan beban standar.</p>
                        </div>
                    </div>
                </div>

                <!-- Kalibrasi Listrik -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-primary text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.data.dev-page') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-flash-outline fs-1 text-primary"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Volumetrik</h5>
                            <p class="small text-muted mb-0">Multimeter, clamp meter, dan alat listrik lainnya.</p>
                        </div>
                    </div>
                </div>

                <!-- Kalibrasi Panjang -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-danger text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.data.dev-page') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-ruler-square fs-1 text-danger"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Jangka Sorong</h5>
                            <p class="small text-muted mb-0">Mistar, jangka sorong, mikrometer, dll.</p>
                        </div>
                    </div>
                </div>

                <!-- Kalibrasi Temperature -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-secondary text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.data.dev-page') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-thermometer fs-1 text-secondary"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Temperature</h5>
                            <p class="small text-muted mb-0">Termometer digital, sensor suhu, dll.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
