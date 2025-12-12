@extends('layouts.app')

@section('title', 'Approval Kalibrasi')

@section('styles')
    <style>

    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow-sm rounded-3 mb-4" data-aos="fade-up">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <!-- Filter Tanggal -->
                        <div class="col-md-4">
                            <label for="filterTanggal" class="form-label fw-semibold">Tanggal Kalibrasi</label>
                            <input type="date" id="filterTanggal" name="tanggal" class="form-control">
                        </div>

                        <!-- Filter Jenis -->
                        <div class="col-md-4">
                            <label for="filterJenis" class="form-label fw-semibold">Jenis Kalibrasi</label>
                            <select id="filterJenis" name="jenis" class="form-select">
                                <option value="">Semua Jenis</option>
                            </select>
                        </div>

                        <!-- Tombol Reset -->
                        <div class="col-md-4 d-flex align-items-end">
                            <button id="resetFilter" class="btn btn-outline-primary w-100">
                                <i class="mdi mdi-refresh me-1"></i> Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm rounded-3 mb-4" data-aos="fade-up">
                <div class="card-body">
                    <div class="row" id="sertifikat-list-container">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Detail Kalibrasi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" id="modal-dynamic-content">
                    <!-- konten akan diganti JS -->
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/template" id="tpl-pressure">
        <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#info-pane">Informasi</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab"
                    data-bs-target="#measurement-pane">Pengukuran</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#result-pane">Hitung U
                    Gabungan</button>
            </li>
        </ul>

        <div class="tab-content p-3">
            <!-- Tab 1: Informasi -->
            <div class="tab-pane fade show active" id="info-pane">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Kode Alat
                                </small>
                                <strong id="detail_kode_alat"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Nama Alat
                                </small>
                                <strong id="detail_nama_alat"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Tanggal Kalibrasi
                                </small>
                                <strong id="detail_tgl_kalibrasi"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-success border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-check"></i> Tgl Kalibrasi Ulang
                                </small>
                                <strong id="detail_tgl_ulang"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-info border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-geo-alt"></i> Lokasi
                                </small>
                                <strong id="detail_lokasi"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-warning border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-thermometer-half"></i> Suhu Ruangan
                                </small>
                                <strong id="detail_suhu"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-info border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-droplet"></i> Kelembaban
                                </small>
                                <strong id="detail_kelembaban"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-gear"></i> Jenis Kalibrasi
                                </small>
                                <strong id="detail_jenis"></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-primary">
                    <strong><i class="bi bi-book me-2"></i>Metode Kalibrasi</strong>
                    <p class="mb-0 mt-2 small" id="detail_metode"></p>
                </div>
            </div>

            <!-- Tab 2: Pengukuran -->
            <div class="tab-pane fade" id="measurement-pane">
                <!-- Tekanan Naik -->
                <div class="card mb-3 border-success">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-arrow-up-circle-fill me-2"></i>
                        <strong>Tekanan Naik</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm text-center mb-0">
                                <thead class="table-light align-middle">
                                    <tr>
                                        <th>Titik</th>
                                        <th>Penunjuk Standar</th>
                                        <th>Penunjuk Alat</th>
                                        <th>Koreksi Standar</th>
                                        <th>Tekanan Standar</th>
                                        <th>Koreksi Alat</th>
                                        <th>Avg. Penunjuk Alat</th>
                                        <th>Avg. Tekanan Standar</th>
                                        <th>Avg. Kor Alat</th>
                                        <th>Standar Deviasi</th>
                                        <th>Ketidakpastian</th>
                                    </tr>
                                </thead>
                                <tbody id="pressure_naik"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tekanan Turun -->
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <i class="bi bi-arrow-down-circle-fill me-2"></i>
                        <strong>Tekanan Turun</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm text-center mb-0">
                                <thead class="table-light align-middle">
                                    <tr>
                                        <th>Titik</th>
                                        <th>Penunjuk Standar</th>
                                        <th>Penunjuk Alat</th>
                                        <th>Koreksi Standar</th>
                                        <th>Tekanan Standar</th>
                                        <th>Koreksi Alat</th>
                                        <th>Avg. Penunjuk Alat</th>
                                        <th>Avg. Tekanan Standar</th>
                                        <th>Avg. Kor Alat</th>
                                        <th>Standar Deviasi</th>
                                        <th>Ketidakpastian</th>
                                    </tr>
                                </thead>
                                <tbody id="pressure_turun"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Hasil -->
            <div class="tab-pane fade" id="result-pane">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-calculator-fill me-2"></i>
                        <strong>Hasil Perhitungan Ketidakpastian</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Titik</th>
                                        <th>U Naik</th>
                                        <th>U Turun</th>
                                        <th>U Naik²</th>
                                        <th>U Turun²</th>
                                        <th class="bg-success-subtle">U Gabungan</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_gabungan"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/template" id="tpl-temperature">
        <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#info-pane">Informasi</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab"
                    data-bs-target="#measurement-pane">Pengukuran</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#result-pane">Hitung
                    Gabungan</button>
            </li>
        </ul>

        <div class="tab-content p-3">
            <!-- Tab 1: Informasi -->
            <div class="tab-pane fade show active" id="info-pane">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Kode Alat
                                </small>
                                <strong id="detail_kode_alat"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Nama Alat
                                </small>
                                <strong id="detail_nama_alat"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Tanggal Kalibrasi
                                </small>
                                <strong id="detail_tgl_kalibrasi"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-success border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-check"></i> Tgl Kalibrasi Ulang
                                </small>
                                <strong id="detail_tgl_ulang"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-info border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-geo-alt"></i> Lokasi
                                </small>
                                <strong id="detail_lokasi"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-warning border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-thermometer-half"></i> Suhu Ruangan
                                </small>
                                <strong id="detail_suhu"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-info border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-droplet"></i> Kelembaban
                                </small>
                                <strong id="detail_kelembaban"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-gear"></i> Jenis Kalibrasi
                                </small>
                                <strong id="detail_jenis"></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-primary">
                    <strong><i class="bi bi-book me-2"></i>Metode Kalibrasi</strong>
                    <p class="mb-0 mt-2 small" id="detail_metode"></p>
                </div>
            </div>

            <!-- Tab 2: Pengukuran -->
            <div class="tab-pane fade" id="measurement-pane">
                <!-- Tekanan Naik -->
                <div class="card mb-3 border-success">
                    <div class="card-header bg-soft-primary text-primary">
                        <i class="bi bi-arrow-up-circle-fill me-2"></i>
                        <strong>Pengukuran</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm text-center mb-0">
                                <thead class="table-light align-middle">
                                    <tr>
                                        <th>Titik Kalibrasi (°C)</th>
                                        <th>Penunjuk Standar</th>
                                        <th>Penunjuk Alat</th>
                                        <th>Koreksi Standar</th>
                                        <th>Suhu Standar</th>
                                        <th>Koreksi Alat</th>
                                    </tr>
                                </thead>
                                <tbody id="hitung_data"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Hasil -->
            <div class="tab-pane fade" id="result-pane">
                <div class="card border-success">
                    <div class="card-header bg-soft-primary text-primary">
                        <i class="bi bi-calculator-fill me-2"></i>
                        <strong>Hasil Perhitungan Summary</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Titik Kalibrasi (°C)</th>
                                        <th>Avg Penunjuk Alat</th>
                                        <th>Avg Suhu Standar</th>
                                        <th>Avg Kor Alat</th>
                                        <th>Std Deviasi</th>
                                        <th>Ketidakpastian</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_gabungan"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/template" id="tpl-volumetrik">
        <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#info-pane">Informasi</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab"
                    data-bs-target="#measurement-pane">Pengukuran</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#result-pane">Hitung
                    Gabungan</button>
            </li>
        </ul>

        <div class="tab-content p-3">
            <!-- Tab 1: Informasi -->
            <div class="tab-pane fade show active" id="info-pane">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Kode Alat
                                </small>
                                <strong id="detail_kode_alat"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Nama Alat
                                </small>
                                <strong id="detail_nama_alat"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Tanggal Kalibrasi
                                </small>
                                <strong id="detail_tgl_kalibrasi"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-success border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-check"></i> Tgl Kalibrasi Ulang
                                </small>
                                <strong id="detail_tgl_ulang"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-info border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-geo-alt"></i> Lokasi
                                </small>
                                <strong id="detail_lokasi"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-warning border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-thermometer-half"></i> Suhu Ruangan
                                </small>
                                <strong id="detail_suhu"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-info border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-droplet"></i> Kelembaban
                                </small>
                                <strong id="detail_kelembaban"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-gear"></i> Jenis Kalibrasi
                                </small>
                                <strong id="detail_jenis"></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-primary">
                    <strong><i class="bi bi-book me-2"></i>Metode Kalibrasi</strong>
                    <p class="mb-0 mt-2 small" id="detail_metode"></p>
                </div>
            </div>

            <!-- Tab 2: Pengukuran -->
            <div class="tab-pane fade" id="measurement-pane">
                <div class="card mb-3 border-success">
                    <div class="card-header bg-soft-primary text-primary">
                        <i class="bi bi-arrow-up-circle-fill me-2"></i>
                        <strong>Pengukuran</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm text-center mb-0">
                                <thead class="table-light align-middle">
                                    <tr>
                                        <th>Titik Kalibrasi</th>
                                        <th>Penunjuk Standar</th>
                                        <th>Penunjuk Alat</th>
                                        <th>Koreksi</th>
                                    </tr>
                                </thead>
                                <tbody id="hitung_data"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Hasil -->
            <div class="tab-pane fade" id="result-pane">
                <div class="card border-success">
                    <div class="card-header bg-soft-primary text-primary">
                        <i class="bi bi-calculator-fill me-2"></i>
                        <strong>Hasil Perhitungan Summary</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Avg Penunjuk Standar</th>
                                        <th>Avg Koreksi</th>
                                        <th>Std Dev Penunjuk Standar</th>
                                        <th>Akar 10</th>
                                        <th>U Timbangan</th>
                                        <th class="bg-success-subtle">U Total</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_gabungan"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/template" id="tpl-thermohygrometer">
        <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#info-pane">Informasi</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab"
                    data-bs-target="#measurement-pane">Pengukuran</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#result-pane">Perhitungan
                    Gabungan</button>
            </li>
        </ul>

        <div class="tab-content p-3">
            <!-- Tab 1: Informasi -->
            <div class="tab-pane fade show active" id="info-pane">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="card border-start border-primary border-3 card-animate">
                            <div class="card-body">
                                <small class="text-muted d-block"><i class="bi bi-upc"></i> Kode Alat</small>
                                <strong id="detail_kode_alat"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-primary border-3 card-animate">
                            <div class="card-body">
                                <small class="text-muted d-block"><i class="bi bi-box"></i> Nama Alat</small>
                                <strong id="detail_nama_alat"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-primary border-3 card-animate">
                            <div class="card-body">
                                <small class="text-muted d-block"><i class="bi bi-calendar-event"></i> Tanggal
                                    Kalibrasi</small>
                                <strong id="detail_tgl_kalibrasi"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-success border-3 card-animate">
                            <div class="card-body">
                                <small class="text-muted d-block"><i class="bi bi-calendar-check"></i> Kalibrasi
                                    Ulang</small>
                                <strong id="detail_tgl_ulang"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-info border-3 card-animate">
                            <div class="card-body">
                                <small class="text-muted d-block"><i class="bi bi-geo-alt"></i> Lokasi</small>
                                <strong id="detail_lokasi"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-warning border-3 card-animate">
                            <div class="card-body">
                                <small class="text-muted d-block"><i class="bi bi-thermometer-half"></i> Suhu
                                    Ruangan</small>
                                <strong id="detail_suhu"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-info border-3 card-animate">
                            <div class="card-body">
                                <small class="text-muted d-block"><i class="bi bi-droplet-half"></i>
                                    Kelembaban</small>
                                <strong id="detail_kelembaban"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-primary border-3 card-animate">
                            <div class="card-body">
                                <small class="text-muted d-block"><i class="bi bi-gear"></i> Jenis
                                    Kalibrasi</small>
                                <strong id="detail_jenis"></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-primary">
                    <strong><i class="bi bi-book me-2"></i>Metode Kalibrasi</strong>
                    <p class="mb-0 mt-2 small" id="detail_metode"></p>
                </div>
            </div>

            <!-- Tab 2: Pengukuran -->
            <div class="tab-pane fade" id="measurement-pane">
                <!-- Suhu -->
                <div class="card mb-3 border-danger">
                    <div class="card-header bg-danger text-white">
                        <i class="bi bi-thermometer-half me-2"></i><strong>Pengukuran Suhu (°C)</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm text-center mb-0">
                                <thead class="table-light align-middle">
                                    <tr>
                                        <th>Posisi Bagian</th>
                                        <th>Penunjuk Standar</th>
                                        <th>Penunjuk Alat</th>
                                        <th>Koreksi Standar</th>
                                        <th>Nilai Standar</th>
                                        <th>Koreksi Alat</th>
                                        <th>Ketidakpastian</th>
                                    </tr>
                                </thead>
                                <tbody id="table_suhu"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- RH -->
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <i class="bi bi-droplet-half me-2"></i><strong>Pengukuran Kelembaban (RH %)</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm text-center mb-0">
                                <thead class="table-light align-middle">
                                    <tr>
                                        <th>Posisi Bagian</th>
                                        <th>Penunjuk Standar</th>
                                        <th>Penunjuk Alat</th>
                                        <th>Koreksi Standar</th>
                                        <th>Nilai Standar</th>
                                        <th>Koreksi Alat</th>
                                        <th>Ketidakpastian</th>
                                    </tr>
                                </thead>
                                <tbody id="table_rh"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Hasil Gabungan -->
            <div class="tab-pane fade" id="result-pane">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-bar-chart-fill me-2"></i><strong>Hasil Perhitungan Gabungan</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Posisi Bagian</th>
                                        <th>Avg. Penunjuk Alat (°C)</th>
                                        <th>Avg. Penunjuk Alat (RH %)</th>
                                        <th>Standar Deviasi (°C)</th>
                                        <th>Standar Deviasi (RH %)</th>
                                        <th>Ketidakpastian</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_gabungan"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/template" id="tpl-jangka_sorong">
        <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#info-pane">Informasi</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#measurement-pane">Data
                    Pengukuran</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#summary-pane">Summary</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#final-pane">Final
                    Summary</button>
            </li>
        </ul>

        <div class="tab-content p-3">
            <!-- Tab 1: Informasi -->
            <div class="tab-pane fade show active" id="info-pane">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Kode Alat
                                </small>
                                <strong id="detail_kode_alat"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Nama Alat
                                </small>
                                <strong id="detail_nama_alat"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Tanggal Kalibrasi
                                </small>
                                <strong id="detail_tgl_kalibrasi"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-success border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-check"></i> Tgl Kalibrasi Ulang
                                </small>
                                <strong id="detail_tgl_ulang"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-info border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-geo-alt"></i> Lokasi
                                </small>
                                <strong id="detail_lokasi"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-warning border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-thermometer-half"></i> Suhu Ruangan
                                </small>
                                <strong id="detail_suhu"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-info border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-droplet"></i> Kelembaban
                                </small>
                                <strong id="detail_kelembaban"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-gear"></i> Jenis Kalibrasi
                                </small>
                                <strong id="detail_jenis"></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-primary">
                    <strong><i class="bi bi-book me-2"></i>Metode Kalibrasi</strong>
                    <p class="mb-0 mt-2 small" id="detail_metode"></p>
                </div>
            </div>

            <!-- Tab 2: Data Pengukuran -->
            <div class="tab-pane fade" id="measurement-pane">
                <div class="card border-success mb-3">
                    <div class="card-header bg-soft-primary text-primary">
                        <i class="bi bi-rulers me-2"></i> Data Pengukuran
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm text-center mb-0">
                                <thead class="table-light align-middle">
                                    <tr>
                                        <th>N</th>
                                        <th>Nilai Master (mm)</th>
                                        <th>Nilai Pembacaan (mm)</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_pengukuran"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Summary -->
            <div class="tab-pane fade" id="summary-pane">
                <div class="card border-success mb-3">
                    <div class="card-header bg-soft-primary text-primary">
                        <i class="bi bi-bar-chart-line me-2"></i> Hasil Perhitungan Summary
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No Mastes</th>
                                        <th>Nilai Master (mm)</th>
                                        <th>Avg Pembacaan (mm)</th>
                                        <th>Std Deviasi</th>
                                        <th>Koreksi</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_summary"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Final Summary -->
            <div class="tab-pane fade" id="final-pane">
                <div class="card border-success">
                    <div class="card-header bg-soft-primary text-primary">
                        <i class="bi bi-clipboard-check me-2"></i> Summary 2
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Std Dev Total</th>
                                        <th>Ketidakpastian</th>
                                        <th>K (K=2)</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_final_summary"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/template" id="tpl-timbangan">
        <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#info-pane">Informasi</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab"
                    data-bs-target="#pembacaan-pane">Pembacaan</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#keseragaman-pane">Keseragaman
                    Skala</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pinggan-pane">Pinggan</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tare-pane">Tare</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab"
                    data-bs-target="#histerisis-pane">Histerisis</button>
            </li>
        </ul>

        <div class="tab-content p-3">
            <!-- Tab 1: Informasi -->
            <div class="tab-pane fade show active" id="info-pane">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Kode Alat
                                </small>
                                <strong id="detail_kode_alat"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Nama Alat
                                </small>
                                <strong id="detail_nama_alat"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-event"></i> Tanggal Kalibrasi
                                </small>
                                <strong id="detail_tgl_kalibrasi"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-success border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar-check"></i> Tgl Kalibrasi Ulang
                                </small>
                                <strong id="detail_tgl_ulang"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-info border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-geo-alt"></i> Lokasi
                                </small>
                                <strong id="detail_lokasi"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-warning border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-thermometer-half"></i> Suhu Ruangan
                                </small>
                                <strong id="detail_suhu"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-info border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-droplet"></i> Kelembaban
                                </small>
                                <strong id="detail_kelembaban"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-animate border-start border-primary border-3">
                            <div class="card-body">
                                <small class="text-muted d-block">
                                    <i class="bi bi-gear"></i> Jenis Kalibrasi
                                </small>
                                <strong id="detail_jenis"></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-primary">
                    <strong><i class="bi bi-book me-2"></i>Metode Kalibrasi</strong>
                    <p class="mb-0 mt-2 small" id="detail_metode"></p>
                </div>
            </div>

            <!-- Tab Pembacaan -->
            <div class="tab-pane fade" id="pembacaan-pane">
                <div id="detail_pembacaan" class="table-responsive"></div>
                <hr>
                <div id="detail_pembacaan_summary"></div>
            </div>

            <!-- Tab Keseragaman Skala -->
            <div class="tab-pane fade" id="keseragaman-pane">
                <div id="detail_keseragaman" class="table-responsive"></div>
                <hr>
                <div id="detail_keseragaman_summary"></div>
            </div>

            <!-- Tab Pinggan -->
            <div class="tab-pane fade" id="pinggan-pane">
                <div id="detail_pinggan" class="table-responsive"></div>
                <hr>
                <div id="detail_pinggan_summary"></div>
            </div>
            <div class="tab-pane fade" id="tare-pane">
                <div id="detail_tare" class="table-responsive"></div>
                <hr>
                <div id="detail_tare_summary"></div>
            </div>

            <!-- Tab Histerisis -->
            <div class="tab-pane fade" id="histerisis-pane">
                <div id="detail_histerisis" class="table-responsive"></div>
                <hr>
                <div id="detail_histerisis_summary"></div>
            </div>
        </div>
    </script>
@endsection

@section('scripts')

    <script>
        $(document).ready(function() {
            const baseUrlApi = `{{ url('kalibrasi/certificate/approval/data') }}`;

            fetchSertifikat(baseUrlApi);

            $('#filterTanggal, #filterJenis').on('change', function() {
                const tanggal = $('#filterTanggal').val();
                const jenis = $('#filterJenis').val();

                // Buat query string dinamis
                const params = new URLSearchParams();
                if (tanggal) params.append('tanggal', tanggal);
                if (jenis) params.append('jenis', jenis);

                // Gabungkan URL + query
                const url = params.toString() ? `${baseUrlApi}?${params.toString()}` : baseUrlApi;

                fetchSertifikat(url);
            });

            // Tombol reset
            $('#resetFilter').on('click', function() {
                $('#filterTanggal').val('');
                $('#filterJenis').val('');
                fetchSertifikat(baseUrlApi);
            });

            function fetchSertifikat(url) {
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(res) {
                        const container = $('#sertifikat-list-container');
                        const data = res.data;
                        container.empty();

                        if ($('#filterJenis option').length <= 1 && Array.isArray(data)) {
                            filterJenis(data);
                        }

                        if ($.isArray(data)) {
                            if (data.length > 0) {
                                displayListView(data, container);
                            } else {
                                displayEmptyState(container, res.message ||
                                    'Tidak ada data sertifikat.');
                            }
                        } else {
                            displayEmptyState(container, 'Data tidak valid.');
                        }
                    },
                    error: function() {
                        $('#sertifikat-list-container').html(
                            '<div class="col-md-12"><div class="alert alert-danger">Gagal memuat data sertifikat.</div></div>'
                        );
                    }
                });
            }

            function filterJenis(data) {
                const jenisSet = new Set();

                data.forEach(item => {
                    const jenis = item.kalibrasi?.jenis_kalibrasi;
                    if (jenis) jenisSet.add(jenis);
                });

                $('#filterJenis').find('option:not(:first)').remove();

                // Tambahkan option dengan format Capitalized
                jenisSet.forEach(jenis => {
                    const formatted = jenis
                        .toLowerCase()
                        .split(' ')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(' ');

                    $('#filterJenis').append(`<option value="${jenis}">${formatted}</option>`);
                });
            }

            function displayListView(data, container) {
                const rowsHtml = data.map((item, i) => {
                    const tgl_kalibrasi = item.kalibrasi.tgl_kalibrasi ?? '—';
                    const kodeAlat = item.kalibrasi?.alat?.kode_alat ?? '—';
                    const namaAlat = item.kalibrasi?.alat?.nama_alat ?? '—';
                    const jenisKalibrasi = item.kalibrasi?.jenis_kalibrasi ?
                        item.kalibrasi.jenis_kalibrasi
                        .toLowerCase()
                        .replace(/\b\w/g, c => c.toUpperCase()) :
                        '—';

                    const status = item.status ?? 'pending';
                    const komentar = item.comment ?? '';

                    const badgeClass =
                        status === 'approved' ? 'success' :
                        status === 'pending' || status === 'read' ? 'warning' :
                        'danger';

                    // Tombol detail selalu ada
                    let actionColumn = `
                        <button class="btn btn-outline-primary btn-sm btn-detail" data-id="${item.id}">
                            <i class="mdi mdi-eye me-2"></i>Detail
                        </button>
                    `;

                    // Tambahkan tombol approve/reject hanya jika masih pending atau read
                    if (status === 'pending' || status === 'read') {
                        actionColumn += `
                            <button class="btn btn-success btn-sm btn-approve" data-id="${item.id}">
                                <i class="mdi mdi-check me-2"></i>Approve
                            </button>
                            <button class="btn btn-danger btn-sm btn-reject" data-id="${item.id}">
                                <i class="mdi mdi-close me-2"></i>Reject
                            </button>
                        `;
                    } else {
                        // Kalau sudah final, tampilkan badge
                        actionColumn += `
                            <span class="badge badge-soft-${badgeClass} px-3 py-2 ms-2 text-uppercase">
                                <i class="mdi mdi-${status === 'approved' ? 'check' : 'close'} me-1"></i>
                                ${status === 'approved' ? 'Disetujui' : 'Ditolak'}
                            </span>
                        `;
                    }

                    return `
                    <tr data-id="${item.id}">
                        <td>${i + 1}</td>
                        <td>${kodeAlat}</td>
                        <td>${namaAlat}</td>
                        <td>${tgl_kalibrasi}</td>
                        <td>${jenisKalibrasi}</td>
                        <td>
                            <span class="badge badge-soft-${badgeClass} text-uppercase">${status}</span>
                        </td>
                        <td>
                            <textarea class="form-control form-control komentar"
                                placeholder="Tulis komentar..."
                                rows="1"
                                data-id="${item.id}"
                                style="min-width:150px;resize:none;"
                                ${status !== 'pending' && status !== 'read' ? 'disabled' : ''}>${komentar}</textarea>
                        </td>
                        <td class="text-nowrap gap-2">${actionColumn}</td>
                    </tr>
                `;
                }).join('');

                const tableHtml = `
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-nowrap">
                            <thead class="table-info">
                                <tr>
                                    <th>#</th>
                                    <th>Kode Alat</th>
                                    <th>Nama Alat</th>
                                    <th>Tgl Kalibrasi</th>
                                    <th>Jenis Kalibrasi</th>
                                    <th>Status</th>
                                    <th>Komentar</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>${rowsHtml}</tbody>
                        </table>
                    </div>
                `;

                container.html(tableHtml);

                // Event handler
                $('.btn-detail').on('click', function() {
                    const id = $(this).data('id');
                    const item = data.find(d => d.id === id);
                    showDetailModal(item);
                });

                $('.btn-approve').on('click', function() {
                    const id = $(this).data('id');
                    const komentar = $(`.komentar[data-id="${id}"]`).val();
                    handleApproval(id, 'approved', komentar);
                });

                $('.btn-reject').on('click', function() {
                    const id = $(this).data('id');
                    const komentar = $(`.komentar[data-id="${id}"]`).val();
                    handleApproval(id, 'rejected', komentar);
                });
            }

            function handleApproval(id, status, komentar) {
                if (!komentar && status === 'rejected') {
                    toastr.warning('Komentar wajib diisi jika menolak sertifikat.');
                    return;
                }

                Swal.fire({
                    title: `Yakin ingin ${status === 'approved' ? 'menyetujui' : 'menolak'} sertifikat ini?`,
                    text: komentar ? `Komentar: "${komentar}"` : 'Tanpa komentar.',
                    icon: status === 'approved' ? 'question' : 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: status === 'approved' ? '#28a745' : '#dc3545'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('kalibrasi.certificate.approval.action') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id,
                            status,
                            komentar
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Memproses...',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                        },
                        success: function(res) {
                            Swal.close();

                            if (res.status === 'success') {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: res.message,
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    didClose: () => {
                                        fetchSertifikat(
                                            `{{ url('kalibrasi/certificate/approval/data') }}`
                                        );
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: res.message || 'Terjadi kesalahan.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            toastr.error(xhr.responseJSON?.message ||
                                'Gagal memproses approval.');
                        }
                    });
                });
            }

            function showDetailModal(item) {
                if (!item) return;

                const jenis = item.kalibrasi?.jenis_kalibrasi?.toLowerCase();

                let templateId = '';

                switch (jenis) {
                    case 'pressure':
                        templateId = '#tpl-pressure';
                        break;
                    case 'timbangan':
                        templateId = '#tpl-timbangan';
                        break;
                    case 'temperature':
                        templateId = '#tpl-temperature';
                        break;
                    case 'thermohygrometer':
                        templateId = '#tpl-thermohygrometer';
                        break;
                    case 'volumetrik':
                        templateId = '#tpl-volumetrik';
                        break;
                    case 'jangka_sorong':
                        templateId = '#tpl-jangka_sorong';
                        break;
                    default:
                        templateId = '#tpl-default';
                        break;
                }

                // 1. masukkan template
                $('#modal-dynamic-content').html($(templateId).html());

                renderDetailByType(jenis, item);

                $('#detailModal').modal('show');
            }

            function renderDetailByType(jenis, item) {
                switch (jenis) {
                    case 'pressure':
                        renderPressureDetail(item);
                        break;

                    case 'timbangan':
                        renderTimbanganDetail(item);
                        break;

                    case 'temperature':
                        renderTemperatureDetail(item);
                        break;

                    case 'thermohygrometer':
                        renderThermohygrometerDetail(item);
                        break;

                    case 'volumetrik':
                        renderVolumetrikDetail(item);
                        break;

                    case 'jangka_sorong':
                        renderJangkaSorongDetail(item);
                        break;

                    default:
                        renderDefaultDetail(item);
                        break;
                }
            }

            function renderPressureDetail(item) {
                // let item = historyData.find(x => x.id === id);
                if (!item) return;

                // console.log(item);

                // Isi data umum
                $('#detail_kode_alat').text(item.kalibrasi.alat.kode_alat);
                $('#detail_nama_alat').text(item.kalibrasi.alat.nama_alat);
                $('#detail_tgl_kalibrasi').text(formatDate(item.kalibrasi.tgl_kalibrasi));
                $('#detail_tgl_ulang').text(formatDate(item.kalibrasi.tgl_kalibrasi_ulang));
                $('#detail_lokasi').text(item.kalibrasi.lokasi_kalibrasi);
                $('#detail_suhu').text(item.kalibrasi.suhu_ruangan + '°C');
                $('#detail_kelembaban').text(item.kalibrasi.kelembaban + '%');
                $('#detail_jenis').text(item.kalibrasi.jenis_kalibrasi.toUpperCase());
                $('#detail_metode').text(item.kalibrasi.alat.metode_kalibrasi);

                // Render pressure data
                let naikBody = $('#pressure_naik');
                let turunBody = $('#pressure_turun');
                naikBody.empty();
                turunBody.empty();
                let lastTitik = null;

                const pressures = (item.kalibrasi.pressure || []).map(p => ({
                    ...p,
                    tekanan: (p.tekanan || '').toString().toLowerCase().trim(),
                    titik_kalibrasi: p.titik_kalibrasi ?? '',
                    penunjuk_standar: p.penunjuk_standar ?? '',
                    penunjuk_alat: p.penunjuk_alat ?? '',
                    koreksi_standar: p.koreksi_standar ?? '',
                    tekanan_standar: p.tekanan_standar ?? '',
                    koreksi_alat: p.koreksi_alat ?? ''
                }));

                const naikArr = pressures.filter(p => p.tekanan === 'naik')
                    .sort((a, b) => a.titik_kalibrasi - b.titik_kalibrasi);
                const turunArr = pressures.filter(p => p.tekanan === 'turun')
                    .sort((a, b) => a.titik_kalibrasi - b.titik_kalibrasi);

                function formatNumber(val) {
                    const num = parseFloat(val);
                    if (isNaN(num)) return '—';
                    return num.toFixed(1); // hanya 1 angka desimal
                }

                function renderList(item, arr, $body, tipe) {
                    if (!arr.length) {
                        $body.append('<tr><td colspan="9" class="text-center text-muted">Tidak ada data</td></tr>');
                        return;
                    }

                    const grouped = arr.reduce((acc, p) => {
                        if (!acc[p.titik_kalibrasi]) acc[p.titik_kalibrasi] = [];
                        acc[p.titik_kalibrasi].push(p);
                        return acc;
                    }, {});

                    const suffix = tipe === 'naik' ? '_naik' : '_turun';

                    Object.keys(grouped).forEach(titik => {
                        const dataTitik = grouped[titik];
                        const pg = item.kalibrasi.pressure_gabungan?.find(pg => pg.titik_kalibrasi ==
                            titik);

                        dataTitik.forEach((p, i) => {
                            const showTitik = i === 0; // tampilkan hanya di baris pertama per titik
                            const showGabungan = i ===
                                0; // tampilkan nilai avg/sd/u hanya di baris pertama

                            $body.append(`
                                <tr>
                                    <td>${showTitik ? `<span class="badge bg-primary">${formatNumber(p.titik_kalibrasi)}</span>` : ''}</td>
                                    <td>${formatNumber(p.penunjuk_standar)}</td>
                                    <td>${formatNumber(p.penunjuk_alat)}</td>
                                    <td>${formatNumber(p.koreksi_standar)}</td>
                                    <td>${formatNumber(p.tekanan_standar)}</td>
                                    <td>${formatNumber(p.koreksi_alat)}</td>
                                    <td>${showGabungan && pg ? formatNumber(pg['avg_penunjuk_alat' + suffix]) : ''}</td>
                                    <td>${showGabungan && pg ? formatNumber(pg['avg_tekanan_standar' + suffix]) : ''}</td>
                                    <td>${showGabungan && pg ? formatNumber(pg['avg_kor_alat' + suffix]) : ''}</td>
                                    <td>${showGabungan && pg ? formatNumber(pg['std_deviasi' + suffix]) : ''}</td>
                                    <td>${showGabungan && pg ? formatNumber(pg['ketidak_pastian' + suffix]) : ''}</td>
                                </tr>
                            `);
                        });
                    });
                }

                renderList(item, naikArr, naikBody, 'naik');
                renderList(item, turunArr, turunBody, 'turun');

                // Render data gabungan
                if (item.kalibrasi.pressure_gabungan && item.kalibrasi.pressure_gabungan.length > 0) {
                    let tbody = $('#detail_gabungan');
                    tbody.empty();

                    $.each(item.kalibrasi.pressure_gabungan, function(i, pg) {
                        let row = `
                        <tr>
                            <td><span class="badge badge-soft-primary">${formatNumber(pg.titik_kalibrasi)}</span></td>
                            <td>${parseFloat(pg.u_naik).toFixed(9)}</td>
                            <td>${parseFloat(pg.u_turun).toFixed(9)}</td>
                            <td>${parseFloat(pg.u_naik_kuadrat).toFixed(9)}</td>
                            <td>${parseFloat(pg.u_turun_kuadrat).toFixed(9)}</td>
                            <td class="highlight-value">${parseFloat(pg.u_gabungan).toFixed(9)}</td>
                        </tr>
                    `;
                        tbody.append(row);
                    });
                }

                // Show modal
                $('#detailModal').modal('show');
            }

            function renderTemperatureDetail(item) {
                // let item = historyData.find(x => x.id === id);
                if (!item) return;

                // console.log(item);

                // Isi data umum
                $('#detail_kode_alat').text(item.kalibrasi.alat.kode_alat);
                $('#detail_nama_alat').text(item.kalibrasi.alat.nama_alat);
                $('#detail_tgl_kalibrasi').text(formatDate(item.kalibrasi.tgl_kalibrasi));
                $('#detail_tgl_ulang').text(formatDate(item.kalibrasi.tgl_kalibrasi_ulang));
                $('#detail_lokasi').text(item.kalibrasi.lokasi_kalibrasi);
                $('#detail_suhu').text(item.kalibrasi.suhu_ruangan);
                $('#detail_kelembaban').text(item.kalibrasi.kelembaban);
                $('#detail_jenis').text(item.kalibrasi.jenis_kalibrasi.toUpperCase());
                $('#detail_metode').text(item.kalibrasi.alat.metode_kalibrasi);

                // Render volumetrik data
                const temperatures = Array.isArray(item.kalibrasi.temperature) ? item.kalibrasi.temperature : [];
                const hitungBody = $('#hitung_data');
                hitungBody.empty();

                if (temperatures.length > 0) {
                    temperatures.forEach(v => {
                        hitungBody.append(`
                            <tr>
                                <td>${formatNumberDynamic(v.titik_kalibrasi, 2)}</td>
                                <td>${formatNumberDynamic(v.penunjuk_standar, 2)}</td>
                                <td>${formatNumberDynamic(v.penunjuk_alat, 2)}</td>
                                <td>${formatNumberDynamic(v.koreksi_standar, 2)}</td>
                                <td>${formatNumberDynamic(v.suhu_standar, 2)}</td>
                                <td>${formatNumberDynamic(v.koreksi_alat, 2)}</td>
                            </tr>
                        `);
                    });
                } else {
                    hitungBody.append(`
                        <tr>
                            <td colspan="6" class="text-muted fst-italic">Tidak ada data pengukuran</td>
                        </tr>
                    `);
                }

                // === Render data gabungan ===
                const gabungan = item.kalibrasi.temperature_gabungan ?? [];
                const gabunganBody = $('#detail_gabungan');
                gabunganBody.empty();

                if (Array.isArray(gabungan) && gabungan.length > 0) {
                    gabungan.forEach(row => {
                        gabunganBody.append(`
                            <tr>
                                <td>${formatNumberDynamic(row.titik_kalibrasi ?? '-')}</td>
                                <td>${formatNumberDynamic(row.avg_penunjuk_alat, 8)}</td>
                                <td>${formatNumberDynamic(row.avg_suhu_standar, 8)}</td>
                                <td>${formatNumberDynamic(row.avg_kor_alat, 8)}</td>
                                <td>${formatNumberDynamic(row.stdev, 8)}</td>
                                <td>${formatNumberDynamic(row.ketidakpastian, 8)}</td>
                            </tr>
                        `);
                    });
                } else {
                    gabunganBody.append(`
                        <tr>
                            <td colspan="6" class="text-muted fst-italic">Data gabungan belum tersedia</td>
                        </tr>
                    `);
                }


                // Show modal
                $('#detailModal').modal('show');
            }

            function renderVolumetrikDetail(item) {
                // let item = historyData.find(x => x.id === id);
                if (!item) return;

                // Isi data umum
                $('#detail_kode_alat').text(item.kalibrasi.alat.kode_alat);
                $('#detail_nama_alat').text(item.kalibrasi.alat.nama_alat);
                $('#detail_tgl_kalibrasi').text(formatDate(item.kalibrasi.tgl_kalibrasi));
                $('#detail_tgl_ulang').text(formatDate(item.kalibrasi.tgl_kalibrasi_ulang));
                $('#detail_lokasi').text(item.kalibrasi.lokasi_kalibrasi);
                $('#detail_suhu').text(item.kalibrasi.suhu_ruangan);
                $('#detail_kelembaban').text(item.kalibrasi.kelembaban);
                $('#detail_jenis').text(item.kalibrasi.jenis_kalibrasi.toUpperCase());
                $('#detail_metode').text(item.kalibrasi.alat.metode_kalibrasi);

                // Render volumetrik data
                const volumetriks = Array.isArray(item.kalibrasi.volumetrik) ? item.kalibrasi.volumetrik : [];
                const hitungBody = $('#hitung_data');
                hitungBody.empty();

                if (volumetriks.length > 0) {
                    volumetriks.forEach(v => {
                        hitungBody.append(`
                            <tr>
                                <td>${formatNumberDynamic(v.titik_kalibrasi, 2)}</td>
                                <td>${formatNumberDynamic(v.penunjuk_standar, 2)}</td>
                                <td>${formatNumberDynamic(v.penunjuk_alat, 2)}</td>
                                <td>${formatNumberDynamic(v.koreksi, 2)}</td>
                            </tr>
                        `);
                    });
                } else {
                    hitungBody.append(`
                        <tr>
                            <td colspan="6" class="text-muted fst-italic">Tidak ada data pengukuran</td>
                        </tr>
                    `);
                }

                // === Render data gabungan ===
                const gabungan = item.kalibrasi.volumetrik_gabungan ?? null;
                const gabunganBody = $('#detail_gabungan');
                gabunganBody.empty();

                if (gabungan) {
                    gabunganBody.append(`
                        <tr>
                            <td>${formatNumberDynamic(gabungan.avg_penunjuk_standar, 8)}</td>
                            <td>${formatNumberDynamic(gabungan.avg_koreksi, 8)}</td>
                            <td>${formatNumberDynamic(gabungan.stdev_penunjuk_standar, 8)}</td>
                            <td>${formatNumberDynamic(gabungan.akar_10, 8)}</td>
                            <td>${formatNumberDynamic(gabungan.u_timbangan, 8)}</td>
                            <td class="fw-bold bg-success-subtle">${formatNumberDynamic(gabungan.u_total, 8)}</td>
                        </tr>
                    `);
                } else {
                    gabunganBody.append(`
                        <tr>
                            <td colspan="6" class="text-muted fst-italic">Data gabungan belum tersedia</td>
                        </tr>
                    `);
                }

                // Show modal
                $('#detailModal').modal('show');
            }

            function renderThermohygrometerDetail(item) {
                if (!item) return;

                // === Informasi Umum ===
                $('#detail_kode_alat').text(item.kalibrasi.alat.kode_alat);
                $('#detail_nama_alat').text(item.kalibrasi.alat.nama_alat);
                $('#detail_tgl_kalibrasi').text(formatDate(item.kalibrasi.tgl_kalibrasi));
                $('#detail_tgl_ulang').text(formatDate(item.kalibrasi.tgl_kalibrasi_ulang));
                $('#detail_lokasi').text(item.kalibrasi.lokasi_kalibrasi);
                $('#detail_suhu').text(item.kalibrasi.suhu_ruangan + '°C');
                $('#detail_kelembaban').text(item.kalibrasi.kelembaban + '%');
                $('#detail_jenis').text(item.kalibrasi.jenis_kalibrasi.toUpperCase());
                $('#detail_metode').text(item.kalibrasi.alat.metode_kalibrasi);

                const tableSuhu = $('#table_suhu');
                const tableRh = $('#table_rh');
                const gabBody = $('#detail_gabungan');
                tableSuhu.empty();
                tableRh.empty();
                gabBody.empty();

                const thermo = item.kalibrasi.thermohygrometer || [];
                const gab = item.kalibrasi.thermohygrometer_gabungan || [];

                const grouped = thermo.reduce((acc, d) => {
                    const key = `${d.tipe_hitung}_${d.titik_kalibrasi}`;
                    if (!acc[key]) acc[key] = [];
                    acc[key].push(d);
                    return acc;
                }, {});

                const formatNum = (v, dec = 2) => (isNaN(parseFloat(v)) ? '—' : parseFloat(v).toFixed(dec));

                function renderTable(type, $body) {
                    const filtered = Object.keys(grouped).filter(k => k.startsWith(type + '_'));
                    if (!filtered.length) {
                        $body.append(
                            '<tr><td colspan="11" class="text-center text-muted">Tidak ada data</td></tr>');
                        return;
                    }

                    filtered.forEach(key => {
                        const [_, titik] = key.split('_');
                        const data = grouped[key];
                        const g = gab.find(x => x.titik_kalibrasi == titik);

                        data.forEach((r, i) => {
                            const show = i === 0;
                            $body.append(`
                                <tr>
                                    ${show ? `<td rowspan="${data.length}" class="align-middle text-center"><span class="badge bg-primary">${r.posisi}</span></td>` : ''}
                                    <td>${formatNum(r.penunjuk_standar)}</td>
                                    <td>${formatNum(r.penunjuk_alat)}</td>
                                    <td>${formatNum(r.koreksi_standar)}</td>
                                    <td>${formatNum(r.tekanan_standar)}</td>
                                    <td>${formatNum(r.koreksi_alat)}</td>
                                     ${show && g ? `<td rowspan="${data.length}" class="align-middle">${formatNum(g['ketidak_pastian_' + type])}</td>` : ''}
                                </tr>
                            `);
                        });
                    });
                }

                renderTable('suhu', tableSuhu);
                renderTable('rh', tableRh);

                // === Gabungan ===
                if (gab.length > 0) {
                    gab.forEach(g => {
                        gabBody.append(`
                            <tr>
                                <td><span class="badge bg-success">${g.posisi || '—'}</span></td>
                                <td>${formatNum(g.avg_penunjuk_alat_suhu)}</td>
                                <td>${formatNum(g.avg_penunjuk_alat_rh)}</td>
                                <td>${formatNum(g.std_deviasi_suhu)}</td>
                                <td>${formatNum(g.std_deviasi_rh)}</td>
                                <td class="highlight-value">${formatNum(g.ketidak_pastian_suhu)} / ${formatNum(g.ketidak_pastian_rh)}</td>
                            </tr>
                        `);
                    });
                } else {
                    gabBody.append(
                        '<tr><td colspan="6" class="text-center text-muted">Tidak ada data gabungan</td></tr>');
                }

                $('#detailModal').modal('show');
            }

            function renderJangkaSorongDetail(item) {
                if (!item) {
                    Swal.fire('Oops!', 'Data tidak ditemukan!', 'error');
                    return;
                }

                // console.log(item);

                const alat = item.kalibrasi.alat ?? {};
                const summaryList = item.kalibrasi.jangka_sorong_summary ?? [];
                const finalSummaryList = item.kalibrasi.jangka_sorong_final_summary ?? [];
                const pengukuranList = item.kalibrasi.jangka_sorong ?? [];

                // === Isi Tab Informasi ===
                $('#detail_kode_alat').text(alat.kode_alat ?? '-');
                $('#detail_nama_alat').text(alat.nama_alat ?? '-');
                $('#detail_tgl_kalibrasi').text(item.kalibrasi.tgl_kalibrasi ?? '-');
                $('#detail_tgl_ulang').text(item.kalibrasi.tgl_kalibrasi_ulang ?? '-');
                $('#detail_lokasi').text(item.kalibrasi.lokasi_kalibrasi ?? '-');
                $('#detail_suhu').text(item.kalibrasi.suhu_ruangan ?? '-');
                $('#detail_kelembaban').text(item.kalibrasi.kelembaban ?? '-');
                $('#detail_jenis').text(item.kalibrasi.jenis_kalibrasi ?? '-');
                $('#detail_metode').text(item.kalibrasi.alat.metode_kalibrasi ?? 'Tidak ada data metode.');

                // === Tab Data Pengukuran (Grouped by nilai_master) ===
                const pengukuranBody = $('#detail_pengukuran');
                pengukuranBody.empty();

                if (pengukuranList.length > 0) {
                    // Group berdasarkan nilai_master (bukan master_id lagi)
                    const grouped = {};
                    pengukuranList.forEach(p => {
                        const nilaiMaster = p.master?.nilai_master ?? `Master ID: ${p.master_id}`;
                        if (!grouped[nilaiMaster]) grouped[nilaiMaster] = [];
                        grouped[nilaiMaster].push(p);
                    });

                    Object.keys(grouped).forEach(nilaiMaster => {
                        pengukuranBody.append(`
                            <tr class="table-primary fw-bold">
                                <td colspan="3" class="text-start ps-3">
                                    Titik Master: ${formatNumber(nilaiMaster)} mm
                                </td>
                            </tr>
                        `);

                        grouped[nilaiMaster].forEach((p, i) => {
                            pengukuranBody.append(`
                                <tr>
                                    <td>${p.no ?? i + 1}</td>
                                    <td>${formatNumber(p.master?.nilai_master ?? '-')}</td>
                                    <td>${formatNumber(p.nilai_pembacaan ?? '-')}</td>
                                </tr>
                            `);
                        });
                    });
                } else {
                    pengukuranBody.append(
                        '<tr><td colspan="3" class="text-muted">Tidak ada data pengukuran.</td></tr>');
                }

                // === Isi Tab Summary ===
                const summaryBody = $('#detail_summary');
                summaryBody.empty();

                if (summaryList.length > 0) {
                    summaryList.forEach(s => {
                        summaryBody.append(`
                            <tr>
                                <td>${s.master.no ?? '-'}</td>
                                <td>${formatNumber(s.master.nilai_master) ?? '-'}</td>
                                <td>${formatNumber(s.avg_pembacaan) ?? '-'}</td>
                                <td>${s.std_dev ?? '-'}</td>
                                <td>${s.koreksi ?? '-'}</td>
                            </tr>
                        `);
                    });
                } else {
                    summaryBody.append('<tr><td colspan="4" class="text-muted">Tidak ada data summary.</td></tr>');
                }

                // === Isi Tab Final Summary ===
                const finalBody = $('#detail_final_summary');
                finalBody.empty();

                if (finalSummaryList.length > 0) {
                    const f = finalSummaryList[0];
                    finalBody.append(`
                        <tr>
                            <td>${f.std_dev_total ?? '0.00000'}</td>
                            <td>${f.ketidakpastian ?? '0.0000'}</td>
                            <td>${f.k_2 ?? '2'}</td>
                        </tr>
                    `);
                } else {
                    finalBody.append(
                        '<tr><td colspan="3" class="text-muted">Tidak ada data final summary.</td></tr>');
                }

                // === Tampilkan modal ===
                $('#detailModal').modal('show');
            }

            function renderTimbanganDetail(item) {
                if (!item) {
                    Swal.fire('Oops!', 'Data tidak ditemukan!', 'error');
                    return;
                }

                // console.log(item);

                const items = item.kalibrasi;

                const alat = items.alat ?? {};

                // === Informasi Umum ===
                $('#detail_kode_alat').text(alat.kode_alat ?? '-');
                $('#detail_nama_alat').text(alat.nama_alat ?? '-');
                $('#detail_tgl_kalibrasi').text(items.tgl_kalibrasi ?? '-');
                $('#detail_tgl_ulang').text(items.tgl_kalibrasi_ulang ?? '-');
                $('#detail_lokasi').text(items.lokasi_kalibrasi ?? '-');
                $('#detail_suhu').text(items.suhu_ruangan ?? '-');
                $('#detail_kelembaban').text(items.kelembaban ?? '-');
                $('#detail_jenis').text(items.jenis_kalibrasi ?? '-');
                $('#detail_metode').text(alat.metode_kalibrasi ?? 'Tidak ada data metode.');

                renderPembacaan(items);
                renderKeseragamanSkala(items);
                renderPinggan(items);
                renderTare(items);
                renderHisterisis(items);

                // === Tampilkan modal ===
                $('#detailModal').modal('show');
            }

            function renderPembacaan(item) {
                const pembacaan = Array.isArray(item.pembacaan) ? item.pembacaan : [];
                const pembacaanSummary = item.pembacaan_summary ?? {};

                if (pembacaan.length === 0) {
                    $('#detail_pembacaan').html('<p class="text-muted">Tidak ada data pembacaan.</p>');
                } else {
                    // Kelompokkan berdasarkan 'kemampuan'
                    const grouped = pembacaan.reduce((acc, row) => {
                        if (!acc[row.kemampuan]) acc[row.kemampuan] = [];
                        acc[row.kemampuan].push(row);
                        return acc;
                    }, {});

                    let html = '';

                    Object.entries(grouped).forEach(([kemampuan, rows]) => {
                        // Ambil semua nilai titik unik di kelompok ini
                        const titikUnik = [...new Set(rows.map(r => r.titik).filter(Boolean))].join(', ');

                        html += `
                            <h6 class="mt-3">
                                <strong>${kemampuan}</strong>
                                ${titikUnik ? `<p class="text-muted">${titikUnik} gram</p>` : ''}
                            </h6>
                            <table class="table table-bordered table-sm mb-3">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ulangan</th>
                                        <th>Pembacaan Z</th>
                                        <th>Pembacaan M</th>
                                        <th>Selisih</th>
                                        <th>Maks. Perbedaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows.map(row => `<tr><td>${row.ulangan ?? '-'}</td><td>${row.pembacaan_z ?? '-'}</td><td>${row.pembacaan_m ?? '-'}</td><td>${row.selisih ?? '-'}</td><td>${row.maks_perbedaan ?? '-'}</td></tr>`).join('')}
                                </tbody>
                            </table>
                        `;
                    });

                    $('#detail_pembacaan').html(html);
                }

                // === Bagian Summary Pembacaan (Horizontal) ===
                if (pembacaanSummary.length > 0) {
                    let summaryHtml = `
                        <h6 class="mt-4"><strong>Summary Pembacaan</strong></h6>
                        <table class="table table-bordered table-sm w-100 mb-3">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Standar Deviasi</th>
                                    <th>Maks. Perbedaan Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${pembacaanSummary.map((summary, index) => `<tr><td>${index + 1}</td><td>${summary.std_dev ?? '-'}</td><td>${summary.maks_perbedaan_akhir ?? '-'}</td></tr>`).join('')}
                            </tbody>
                        </table>
                    `;

                    $('#detail_pembacaan_summary').html(summaryHtml);
                } else {
                    $('#detail_pembacaan_summary').html('<p class="text-muted">Tidak ada data summary.</p>');
                }
            }

            function renderKeseragamanSkala(item) {
                const keseragaman = Array.isArray(item.keseragaman_skala) ? item.keseragaman_skala : [];
                const summary = item.keseragaman_summary ?? {};

                // Jika tidak ada data
                if (keseragaman.length === 0) {
                    $('#detail_keseragaman').html('<p class="text-muted">Tidak ada data keseragaman skala.</p>');
                    $('#detail_keseragaman_summary').html('');
                    return;
                }

                // === Kelompokkan berdasarkan nilai beban ===
                const grouped = keseragaman.reduce((acc, row) => {
                    const key = row.beban || 'Tidak diketahui';
                    if (!acc[key]) acc[key] = [];
                    acc[key].push(row);
                    return acc;
                }, {});

                let html = '';

                Object.entries(grouped).forEach(([beban, rows]) => {
                    html += `
                        <h6 class="mt-3"><strong>Beban ${beban}</strong></h6>
                        <table class="table table-bordered table-sm mb-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Beban Timbangan</th>
                                    <th>Pembacaan Skala</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rows.map(r => `<tr><td>${r.beban_timbangan ?? '-'}</td><td>${r.pembacaan_skala ?? '-'}</td></tr>`).join('')}
                            </tbody>
                        </table>
                    `;
                });

                // Tampilkan tabel keseragaman skala
                $('#detail_keseragaman').html(html);

                let summaryHtml = '';
                if (summary.length === 0) {
                    summaryHtml = '<p class="text-muted">Tidak ada data summary keseragaman.</p>';
                } else {
                    summaryHtml = `
                        <h6 class="mt-3"><strong>Summary Keseragaman Skala</strong></h6>
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Beban</th>
                                    <th>Avg Z</th>
                                    <th>Avg M</th>
                                    <th>Selisih Z-M</th>
                                    <th>Standar Massa</th>
                                    <th>Koreksi Skala</th>
                                    <th>Absolut Koreksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${summary.map((s, i) => `<tr><td>${i + 1}</td><td>${s.beban ?? '-'}</td><td>${s.avg_z ?? '-'}</td><td>${s.avg_m ?? '-'}</td><td>${s.selisih_zm ?? '-'}</td><td>${s.standar_massa ?? '-'}</td><td>${s.koreksi_skala ?? '-'}</td><td>${s.absolut_koreksi ?? '-'}</td></tr>`).join('')}
                            </tbody>
                        </table>
                    `;
                }

                $('#detail_keseragaman_summary').html(summaryHtml);
            }

            function renderPinggan(item) {
                // === Render Pinggan ===
                const pinggan = item.pinggan ?? [];
                const pingganSummary = item.pinggan_summary ?? {};

                let pingganHtml = '';
                if (pinggan.length === 0) {
                    pingganHtml = '<p class="text-muted">Tidak ada data pinggan.</p>';
                } else {
                    pingganHtml += `
                        <h6 class="mt-3"><strong>Data Pengujian Pinggan</strong></h6>
                        <table class="table table-bordered table-sm text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Diameter</th>
                                    <th>Massa</th>
                                    <th>Tengah</th>
                                    <th>Depan</th>
                                    <th>Belakang</th>
                                    <th>Kiri</th>
                                    <th>Kanan</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    pinggan.forEach((item, index) => {
                        pingganHtml += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.diameter ?? '-'}</td>
                                <td>${item.massa ?? '-'}</td>
                                <td>${item.tengah ?? '-'}</td>
                                <td>${item.depan ?? '-'}</td>
                                <td>${item.belakang ?? '-'}</td>
                                <td>${item.kiri ?? '-'}</td>
                                <td>${item.kanan ?? '-'}</td>
                            </tr>
                        `;
                    });

                    pingganHtml += `
                            </tbody>
                        </table>
                    `;
                }

                let summaryHtml = '';
                if (pingganSummary.length === 0) {
                    summaryHtml = '<p class="text-muted">Tidak ada data summary pinggan.</p>';
                } else {
                    summaryHtml = `
                        <h6 class="mt-3"><strong>Summary Pinggan</strong></h6>
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tengah</th>
                                    <th>Depan</th>
                                    <th>Belakang</th>
                                    <th>Kiri</th>
                                    <th>Kanan</th>
                                    <th>Minimum</th>
                                    <th>Maximum</th>
                                    <th>Selisih Maks</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    pingganSummary.forEach((smry, index) => {
                        summaryHtml += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${smry.smry_tengah ?? '-'}</td>
                                <td>${smry.smry_depan ?? '-'}</td>
                                <td>${smry.smry_belakang ?? '-'}</td>
                                <td>${smry.smry_kiri ?? '-'}</td>
                                <td>${smry.smry_kanan ?? '-'}</td>
                                <td>${smry.minimum ?? '-'}</td>
                                <td>${smry.maximum ?? '-'}</td>
                                <td>${smry.selisih_maks ?? '-'}</td>
                            </tr>
                        `;
                    });

                    summaryHtml += `
                            </tbody>
                        </table>
                    `;
                }

                $('#detail_pinggan').html(pingganHtml);
                $('#detail_pinggan_summary').html(summaryHtml);

            }

            function renderTare(item) {
                const tare = Array.isArray(item.tare) ? item.tare : [];
                const summary = item.tare_summary ?? null;

                // === Bagian Data Tare ===
                if (tare.length === 0) {
                    $('#detail_tare').html('<p class="text-muted">Tidak ada data tare.</p>');
                } else {
                    // Kelompokkan berdasarkan tipe_tare (tanpa_pengenolan / dengan_pengenolan)
                    const grouped = tare.reduce((acc, row) => {
                        const key = row.tipe_tare || 'tidak_diketahui';
                        if (!acc[key]) acc[key] = [];
                        acc[key].push(row);
                        return acc;
                    }, {});

                    let html = '';

                    Object.entries(grouped).forEach(([tipe, rows]) => {
                        const tipeLabel = tipe === 'tanpa_pengenolan' ?
                            'Tanpa Pengenolan' :
                            tipe === 'dengan_pengenolan' ?
                            'Dengan Pengenolan' :
                            'Tidak Diketahui';

                        html += `
                            <h6 class="mt-3"><strong>${tipeLabel}</strong></h6>
                            <table class="table table-bordered table-sm mb-3 text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Beban</th>
                                        <th>Massa</th>
                                        <th>Pembacaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows.map(row => `<tr><td>${row.beban ?? '-'}</td><td>${row.massa ?? '-'}</td><td>${row.pembacaan ?? '-'}</td></tr>`).join('')}
                                </tbody>
                            </table>
                        `;
                    });

                    $('#detail_tare').html(html);
                }

                // === Bagian Summary Tare (Horizontal) ===
                if (summary) {
                    const summaryHtml = `
                        <h6 class="mt-4"><strong>Summary Tare</strong></h6>
                        <table class="table table-bordered table-sm text-center w-100 mb-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Massa</th>
                                    <th>Selisih MZ Tanpa Nol</th>
                                    <th>Selisih MZ Dengan Nol</th>
                                    <th>Pengaruh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>${summary.massa ?? '-'}</td>
                                    <td>${summary.selisih_mz_tanpa_nol ?? '-'}</td>
                                    <td>${summary.selisih_mz_dengan_nol ?? '-'}</td>
                                    <td>${summary.pengaruh ?? '-'}</td>
                                </tr>
                            </tbody>
                        </table>
                    `;
                    $('#detail_tare_summary').html(summaryHtml);
                } else {
                    $('#detail_tare_summary').html('<p class="text-muted">Tidak ada data summary tare.</p>');
                }
            }

            function renderHisterisis(item) {
                const histerisis = item.histerisis ?? [];
                const histerisisSummary = item.histerisis_summary ?? null;

                const histerisisBody = $('#detail_histerisis');
                histerisisBody.empty();

                if (histerisis.length > 0) {
                    let html = `
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">Percobaan</th>
                                    <th colspan="2">M</th>
                                    <th colspan="2">Z</th>
                                    <th colspan="2">Selisih</th>
                                </tr>
                                <tr>
                                    <th>M1</th>
                                    <th>M2</th>
                                    <th>Z1</th>
                                    <th>Z2</th>
                                    <th>M1 - M2</th>
                                    <th>Z1 - Z2</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    histerisis.forEach(h => {
                        html += `
                            <tr>
                                <td>${h.percobaan ?? '-'}</td>
                                <td>${h.m1 ?? '-'}</td>
                                <td>${h.m2 ?? '-'}</td>
                                <td>${h.z1 ?? '-'}</td>
                                <td>${h.z2 ?? '-'}</td>
                                <td>${h.m1_m2 ?? '-'}</td>
                                <td>${h.z1_z2 ?? '-'}</td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table>`;
                    histerisisBody.append(html);

                    if (histerisisSummary) {
                        const s = histerisisSummary;
                        const summaryHtml = `
                            <div class="mt-3">
                                <h6 class="fw-bold mb-2">Ringkasan Histerisis</h6>
                                <table class="table table-sm table-bordered text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Pembacaan Terkecil</th>
                                            <th>½ Kapasitas</th>
                                            <th>Rata-rata M1 - M2</th>
                                            <th>Rata-rata Z1 - Z2</th>
                                            <th>Rata-rata MZ</th>
                                            <th>Histerisis</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>${s.pembacaan_terkecil ?? '-'}</td>
                                            <td>${s.setengah_kapasitas ?? '-'}</td>
                                            <td>${s.avg_m1m2 ?? '-'}</td>
                                            <td>${s.avg_z1z2 ?? '-'}</td>
                                            <td>${s.avg_mz ?? '-'}</td>
                                            <td>${s.histerisis ?? '-'}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        `;
                        histerisisBody.append(summaryHtml);
                    }

                } else {
                    histerisisBody.append('<p class="text-muted">Tidak ada data histerisis.</p>');
                }

            }

            function formatDate(dateString) {
                let date = new Date(dateString);
                let options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                return date.toLocaleDateString('id-ID', options);
            }

            function displayEmptyState(container, message = 'Tidak ada data sertifikat yang tersedia.') {
                container.append(`
                    <div class="col-md-12">
                        <div class="alert alert-info text-center" role="alert">
                            <i class="mdi mdi-information-outline me-2"></i> ${message}
                        </div>
                    </div>
                `);
            }

            function formatNumberDynamic(value, maxDecimals = 2) {
                if (value === null || value === undefined || value === '' || isNaN(value)) return '-';
                const num = parseFloat(value);
                if (Number.isInteger(num)) return num.toString(); // tanpa koma kalau bulat
                return num.toFixed(maxDecimals).replace(/\.?0+$/, ''); // hapus nol di belakang koma
            }

            function formatNumber(val) {
                if (val === null || val === undefined || val === '') return '-';
                const num = parseFloat(val);
                if (isNaN(num)) return val;
                return parseFloat(num.toFixed(2)).toString();
            }
        });
    </script>
@endsection
