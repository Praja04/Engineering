@extends('layouts.app')

@section('content')
    <div class="page-content d-flex align-items-center justify-content-center min-vh-100 bg-light">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4 p-4">
                        <div class="card-body">
                            <div class="mb-4">
                                <img src="https://cdn-icons-png.flaticon.com/512/11412/11412112.png" alt="Under Development"
                                    class="img-fluid" style="max-width: 160px;">
                            </div>

                            <h3 class="fw-bold text-primary mb-2">Fitur Dalam Pengembangan 🚧</h3>
                            <p class="text-muted mb-4">
                                Kami sedang bekerja keras untuk menghadirkan fitur ini agar bisa segera digunakan.
                                Terima kasih atas kesabaran dan dukungan Anda.
                            </p>

                            <a href="{{ url()->previous() }}" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="mdi mdi-arrow-left me-1"></i> Kembali
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill px-4">
                                <i class="mdi mdi-view-dashboard-outline me-1"></i> Ke Dashboard
                            </a>

                            <div class="mt-4 small text-muted">
                                <i class="mdi mdi-clock-outline"></i> Terakhir diperbarui: {{ now()->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
