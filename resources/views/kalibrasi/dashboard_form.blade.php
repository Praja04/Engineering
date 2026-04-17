@extends('layouts.app')

@section('title', 'Form Kalibrasi')

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

        hr {
            border-top: 2px solid rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-gradient">Dashboard Form Kalibrasi</h2>
                <p class="text-muted">Pilih jenis kalibrasi di bawah untuk mengisi form terkait</p>
                <hr class="w-25 mx-auto opacity-75">
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Kalibrasi Tekanan -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-success text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.form.pressure') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-gauge fs-1 text-success"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Pressure</h5>
                            <p class="small text-muted mb-0">Pressure gauge, manometer, dan alat tekanan.</p>
                        </div>
                    </div>
                </div>

                <!-- Kalibrasi Thermohygrometer -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-info text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.form.thermohygrometer') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-thermometer-lines fs-1 text-info"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Thermohygrometer</h5>
                            <p class="small text-muted mb-0"> Thermometer, Oven, sensor suhu, dll</p>
                        </div>
                    </div>
                </div>

                <!-- Kalibrasi Timbangan -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-warning text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.form.timbangan') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-scale-balance fs-1 text-warning"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Timbangan</h5>
                            <p class="small text-muted mb-0">Timbangan, neraca, dan beban standar.</p>
                        </div>
                    </div>
                </div>

                <!-- Kalibrasi Volumetrik -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-primary text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.form.volumetrik') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-flash-outline fs-1 text-primary"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Volumetrik</h5>
                            <p class="small text-muted mb-0">Multimeter, clamp meter, dan alat listrik lainnya.</p>
                        </div>
                    </div>
                </div>

                <!-- Kalibrasi Jangka Sorong -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-danger text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.form.jangka-sorong') }}'">
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
                        onclick="window.location.href='{{ route('kalibrasi.form.temperature') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-thermometer fs-1 text-secondary"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Temperature</h5>
                            <p class="small text-muted mb-0">Termometer digital, sensor suhu, dll.</p>
                        </div>
                    </div>
                </div>

                <!-- Kalibrasi Instrumen -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-info text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.form.instrumen') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-flask fs-1 text-secondary"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Instrumen</h5>
                            <p class="small text-muted mb-0">pH Meter, Viscometer, Conductivity, dll.</p>
                        </div>
                    </div>
                </div>

                <!-- Kalibrasi Dimensi -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-warning text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.form.dimensi') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-vector-square fs-1 text-warning"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Dimensi</h5>
                            <p class="small text-muted mb-0">dimensi, dll.</p>
                        </div>
                    </div>
                </div>

                <!-- Kalibrasi Flow Meter -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-animate bg-soft-danger text-dark cursor-pointer"
                        onclick="window.location.href='{{ route('kalibrasi.form.flowmeter') }}'">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto">
                                <i class="mdi mdi-water fs-1 text-danger"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Kalibrasi Flowmeter</h5>
                            <p class="small text-muted mb-0">,Flowmeter, dll.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
