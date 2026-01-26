@extends('layouts.app')

@section('title', 'Tambah Data Performance WWTP')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">Tambah Data Performance WWTP</h4>
                        <p class="text-muted mb-0">Input data kualitas air WWTP</p>
                    </div>
                    <div>
                        <a href="{{ url('/wwtp/data_performance') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Selection Cards -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="row g-3">
                    <!-- Weekly Data Card -->
                    <div class="col-md-6">
                        <div class="card form-selector-card h-100 active" data-bs-toggle="tab" data-bs-target="#weeklyForm" role="tab">
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <!-- Icon/Image -->
                                    <div class="mb-3">
                                        <div class="avatar-xl mx-auto">
                                            <div class="avatar-title bg-success-subtle rounded-circle">
                                                <i class="mdi mdi-calendar-week text-success" style="font-size: 3rem;"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <h4 class="mb-2 fw-semibold text-success">Data Mingguan</h4>

                                    <!-- Description -->
                                    <p class="text-muted mb-3">
                                        Input data TSS dan COD mingguan per tahapan
                                    </p>

                                    <!-- Select Button -->
                                    <div class="mt-4">
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            <i class="mdi mdi-cursor-pointer me-1"></i> Klik untuk Memilih
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Active Indicator -->
                            <div class="card-active-indicator"></div>
                        </div>
                    </div>

                    <!-- Daily Data Card -->
                    <div class="col-md-6">
                        <div class="card form-selector-card h-100" data-bs-toggle="tab" data-bs-target="#dailyForm" role="tab">
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <!-- Icon/Image -->
                                    <div class="mb-3">
                                        <div class="avatar-xl mx-auto">
                                            <div class="avatar-title bg-primary-subtle rounded-circle">
                                                <i class="mdi mdi-calendar-week text-success" style="font-size: 3rem;"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <h4 class="mb-2 fw-semibold text-primary">Data Harian</h4>

                                    <!-- Description -->
                                    <p class="text-muted mb-3">
                                        Input data pH harian per shift kerja
                                    </p>

                                    <!-- Select Button -->
                                    <div class="mt-4">
                                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                            <i class="mdi mdi-cursor-pointer me-1"></i> Klik untuk Memilih
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Active Indicator -->
                            <div class="card-active-indicator"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="row">
            <div class="col-12">
                <div class="tab-content">

                    <!-- Weekly Form -->
                    <div class="tab-pane active" id="weeklyForm" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-light border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs">
                                            <div class="avatar-title bg-success rounded-circle">
                                                <i class="mdi mdi-calendar-week"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="mb-0">Data Performance WWTP Mingguan</h5>
                                        <p class="text-muted mb-0">Input data TSS dan COD per tahapan</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-success">Mingguan</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="weeklyPerformanceForm" enctype="multipart/form-data">
                                    <!-- Basic Information -->
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label for="weekly_tanggal" class="form-label fw-semibold">
                                                Tanggal <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control" id="weekly_tanggal" name="tanggal" required>
                                            <div class="form-text">Tanggal pencatatan data (minggu akan otomatis terdeteksi)</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="weekly_jenis" class="form-label fw-semibold">
                                                Jenis Tahapan <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="weekly_jenis" name="jenis" required>
                                                <option value="">-- Pilih Jenis Tahapan --</option>
                                                <option value="equal">Equal (Equalisasi)</option>
                                                <option value="outlet_anaerob">Outlet Anaerob</option>
                                                <option value="aerob">Aerob</option>
                                                <option value="daf">DAF</option>
                                                <option value="outlet">Outlet</option>
                                            </select>
                                            <div class="form-text">Pilih tahapan proses WWTP</div>
                                        </div>
                                    </div>

                                    <!-- TSS and COD Data -->
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1 text-success">
                                                    <i class="mdi mdi-test-tube me-2"></i>Data Kualitas Air
                                                </h5>
                                                <p class="text-muted mb-0">Input nilai TSS dan COD</p>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="card border border-success">
                                                    <div class="card-body">
                                                        <label for="weekly_tss" class="form-label fw-semibold">
                                                            <i class="mdi mdi-beaker text-success me-1"></i>TSS (Total Suspended Solids) <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" id="weekly_tss" name="tss" min="0" placeholder="0.00" required>
                                                            <span class="input-group-text bg-light">mg/L</span>
                                                        </div>
                                                        <small class="form-text text-muted">Total padatan tersuspensi</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card border border-success">
                                                    <div class="card-body">
                                                        <label for="weekly_cod" class="form-label fw-semibold">
                                                            <i class="mdi mdi-flask text-success me-1"></i>COD (Chemical Oxygen Demand) <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" id="weekly_cod" name="cod" min="0" placeholder="0.00" required>
                                                            <span class="input-group-text bg-light">mg/L</span>
                                                        </div>
                                                        <small class="form-text text-muted">Kebutuhan oksigen kimiawi</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Photo Upload -->
                                    <div class="mb-4">
                                        <div class="card border">
                                            <div class="card-body">
                                                <label for="weekly_foto" class="form-label fw-semibold">
                                                    <i class="mdi mdi-camera me-1"></i>Foto Dokumentasi
                                                </label>
                                                <input type="file" class="form-control" id="weekly_foto" name="foto" accept="image/*">
                                                <div class="form-text">Upload foto hasil pengukuran (Opsional, max 2MB)</div>
                                                <div id="weekly_foto_preview" class="mt-3" style="display: none;">
                                                    <img src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info border-0" role="alert">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="mdi mdi-information fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <strong>Catatan:</strong> Setiap jenis tahapan hanya dapat diinput <strong>1x per minggu</strong>.
                                                Minggu akan otomatis terdeteksi dari tanggal yang dipilih.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="reset" class="btn btn-light">
                                                    <i class="mdi mdi-refresh me-1"></i> Reset Form
                                                </button>
                                                <button type="submit" class="btn btn-success" id="submitWeeklyForm">
                                                    <i class="mdi mdi-content-save me-1"></i> Simpan Data Mingguan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Form -->
                    <div class="tab-pane" id="dailyForm" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-light border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs">
                                            <div class="avatar-title bg-primary rounded-circle">
                                                <i class="mdi mdi-calendar-today"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="mb-0">Data pH WWTP Harian</h5>
                                        <p class="text-muted mb-0">Input data pH per shift kerja</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-primary">Harian</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="dailyPHForm">
                                    <!-- Basic Information -->
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label for="daily_tanggal" class="form-label fw-semibold">
                                                Tanggal <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control" id="daily_tanggal" name="tanggal" required>
                                            <div class="form-text">Tanggal pencatatan data</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="daily_shift" class="form-label fw-semibold">
                                                Shift <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="daily_shift" name="shift" required>
                                                <option value="">-- Pilih Shift --</option>
                                                <option value="shift1">Shift 1 (06:00 - 14:00)</option>
                                                <option value="shift2">Shift 2 (14:00 - 22:00)</option>
                                                <option value="shift3">Shift 3 (22:00 - 06:00)</option>
                                            </select>
                                            <div class="form-text">Shift kerja yang bertugas</div>
                                        </div>
                                    </div>

                                    <!-- pH Data -->
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1 text-primary">
                                                    <i class="mdi mdi-ph me-2"></i>Data pH Per Tahapan
                                                </h5>
                                                <p class="text-muted mb-0">Input nilai pH pada berbagai tahapan proses</p>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <!-- Row 1 -->
                                            <div class="col-md-4">
                                                <div class="card border border-primary">
                                                    <div class="card-body">
                                                        <label for="daily_equalisasi_1" class="form-label fw-semibold">
                                                            <i class="mdi mdi-water text-primary me-1"></i>Equalisasi 1
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" id="daily_equalisasi_1" name="equalisasi_1" min="0" max="14" placeholder="0.00">
                                                            <span class="input-group-text bg-light">pH</span>
                                                        </div>
                                                        <small class="form-text text-muted">pH Equalisasi 1</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="card border border-primary">
                                                    <div class="card-body">
                                                        <label for="daily_equalisasi_2" class="form-label fw-semibold">
                                                            <i class="mdi mdi-water text-primary me-1"></i>Equalisasi 2
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" id="daily_equalisasi_2" name="equalisasi_2" min="0" max="14" placeholder="0.00">
                                                            <span class="input-group-text bg-light">pH</span>
                                                        </div>
                                                        <small class="form-text text-muted">pH Equalisasi 2</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="card border border-primary">
                                                    <div class="card-body">
                                                        <label for="daily_netralisasi" class="form-label fw-semibold">
                                                            <i class="mdi mdi-water text-primary me-1"></i>Netralisasi
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" id="daily_netralisasi" name="netralisasi" min="0" max="14" placeholder="0.00">
                                                            <span class="input-group-text bg-light">pH</span>
                                                        </div>
                                                        <small class="form-text text-muted">pH Netralisasi</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 2 -->
                                            <div class="col-md-4">
                                                <div class="card border">
                                                    <div class="card-body">
                                                        <label for="daily_sedimentasi_1" class="form-label">
                                                            <i class="mdi mdi-filter me-1"></i>Sedimentasi 1
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" id="daily_sedimentasi_1" name="sedimentasi_1" min="0" max="14" placeholder="0.00">
                                                            <span class="input-group-text bg-light">pH</span>
                                                        </div>
                                                        <small class="form-text text-muted">pH Sedimentasi 1</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="card border">
                                                    <div class="card-body">
                                                        <label for="daily_sedimentasi_2" class="form-label">
                                                            <i class="mdi mdi-filter me-1"></i>Sedimentasi 2
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" id="daily_sedimentasi_2" name="sedimentasi_2" min="0" max="14" placeholder="0.00">
                                                            <span class="input-group-text bg-light">pH</span>
                                                        </div>
                                                        <small class="form-text text-muted">pH Sedimentasi 2</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="card border">
                                                    <div class="card-body">
                                                        <label for="daily_outlet_anaerob" class="form-label">
                                                            <i class="mdi mdi-water-pump me-1"></i>Outlet Anaerob
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" id="daily_outlet_anaerob" name="outlet_anaerob" min="0" max="14" placeholder="0.00">
                                                            <span class="input-group-text bg-light">pH</span>
                                                        </div>
                                                        <small class="form-text text-muted">pH Outlet Anaerob</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 3 -->
                                            <div class="col-md-3">
                                                <div class="card border">
                                                    <div class="card-body">
                                                        <label for="daily_aerob" class="form-label">
                                                            <i class="mdi mdi-air-filter me-1"></i>Aerob
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" id="daily_aerob" name="aerob" min="0" max="14" placeholder="0.00">
                                                            <span class="input-group-text bg-light">pH</span>
                                                        </div>
                                                        <small class="form-text text-muted">pH Aerob</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="card border">
                                                    <div class="card-body">
                                                        <label for="daily_lumpur_aktif" class="form-label">
                                                            <i class="mdi mdi-bacteria me-1"></i>Lumpur Aktif
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" id="daily_lumpur_aktif" name="lumpur_aktif" min="0" max="14" placeholder="0.00">
                                                            <span class="input-group-text bg-light">pH</span>
                                                        </div>
                                                        <small class="form-text text-muted">pH Lumpur Aktif</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="card border">
                                                    <div class="card-body">
                                                        <label for="daily_clarifier_2" class="form-label">
                                                            <i class="mdi mdi-beaker-outline me-1"></i>Clarifier 2
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" id="daily_clarifier_2" name="clarifier_2" min="0" max="14" placeholder="0.00">
                                                            <span class="input-group-text bg-light">pH</span>
                                                        </div>
                                                        <small class="form-text text-muted">pH Clarifier 2</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="card border">
                                                    <div class="card-body">
                                                        <label for="daily_outlet" class="form-label">
                                                            <i class="mdi mdi-pipe me-1"></i>Outlet
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" id="daily_outlet" name="outlet" min="0" max="14" placeholder="0.00">
                                                            <span class="input-group-text bg-light">pH</span>
                                                        </div>
                                                        <small class="form-text text-muted">pH Outlet</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-warning border-0" role="alert">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="mdi mdi-alert fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <strong>Penting:</strong> Setiap tanggal maksimal memiliki <strong>3 shift</strong> (shift1, shift2, shift3).
                                                Nilai pH normal berkisar antara 0-14.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="reset" class="btn btn-light">
                                                    <i class="mdi mdi-refresh me-1"></i> Reset Form
                                                </button>
                                                <button type="submit" class="btn btn-primary" id="submitDailyForm">
                                                    <i class="mdi mdi-content-save me-1"></i> Simpan Data Harian
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="avatar-lg mx-auto mb-4">
                    <div class="avatar-title bg-soft-success text-success rounded-circle">
                        <i class="mdi mdi-check-circle fs-1"></i>
                    </div>
                </div>
                <h4 class="mb-3">Data Berhasil Disimpan!</h4>
                <p class="text-muted mb-4" id="successMessage">Data Performance WWTP telah berhasil disimpan ke sistem.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Tambah Data Lagi
                    </button>
                    <a href="{{ url('/wwtp/data_performance') }}" class="btn btn-primary">
                        Lihat Semua Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Original Styles */
    .card.border-primary {
        border-color: #405189 !important;
    }

    .card.border-success {
        border-color: #0ab39c !important;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 0.15rem rgba(64, 81, 137, 0.25);
        border-color: #405189;
    }

    .input-group-text {
        background-color: #f8f9fa;
        color: #495057;
    }

    .btn-outline-primary {
        color: #405189;
        border-color: #405189;
    }

    .btn-outline-primary:hover {
        background-color: #405189;
        color: white;
    }

    .btn-outline-success {
        color: #0ab39c;
        border-color: #0ab39c;
    }

    .btn-outline-success:hover {
        background-color: #0ab39c;
        color: white;
    }

    .form-text {
        font-size: 0.8125rem;
    }
</style>

<script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        $('#daily_tanggal').val(today);
        $('#weekly_tanggal').val(today);

        // Handle form selector card click
        $('.form-selector-card').on('click', function() {
            // Remove active class from all cards
            $('.form-selector-card').removeClass('active');

            // Add active class to clicked card
            $(this).addClass('active');

            // Get target tab
            const target = $(this).data('bs-target');

            // Show the corresponding tab
            $('.tab-pane').removeClass('show active');
            $(target).addClass('show active');
        });

        // Preview foto for weekly form
        $('#weekly_foto').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#weekly_foto_preview img').attr('src', e.target.result);
                    $('#weekly_foto_preview').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#weekly_foto_preview').hide();
            }
        });

        // Submit weekly performance form
        $('#weeklyPerformanceForm').on('submit', function(e) {
            e.preventDefault();

            const btnSubmit = $('#submitWeeklyForm');
            const originalText = btnSubmit.html();
            btnSubmit.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

            const formData = new FormData(this);

            $.ajax({
                url: "{{ url('api/wwtp-performance') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Show success modal
                    $('#successMessage').html(response.message || 'Data performance mingguan berhasil disimpan!');
                    $('#successModal').modal('show');

                    // Reset form
                    $('#weeklyPerformanceForm')[0].reset();
                    $('#weekly_tanggal').val(today);
                    $('#weekly_foto_preview').hide();
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    let message = 'Terjadi kesalahan saat menyimpan data!';

                    if (error && error.message) {
                        message = error.message;
                    } else if (error && error.errors) {
                        message = Object.values(error.errors).flat().join('<br>');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: message,
                        confirmButtonColor: '#3085d6'
                    });
                },
                complete: function() {
                    btnSubmit.prop('disabled', false).html(originalText);
                }
            });
        });

        // Submit daily pH form
        $('#dailyPHForm').on('submit', function(e) {
            e.preventDefault();

            const btnSubmit = $('#submitDailyForm');
            const originalText = btnSubmit.html();
            btnSubmit.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

            const formData = {
                tanggal: $('#daily_tanggal').val(),
                shift: $('#daily_shift').val(),
                equalisasi_1: $('#daily_equalisasi_1').val() || null,
                equalisasi_2: $('#daily_equalisasi_2').val() || null,
                netralisasi: $('#daily_netralisasi').val() || null,
                sedimentasi_1: $('#daily_sedimentasi_1').val() || null,
                sedimentasi_2: $('#daily_sedimentasi_2').val() || null,
                outlet_anaerob: $('#daily_outlet_anaerob').val() || null,
                aerob: $('#daily_aerob').val() || null,
                lumpur_aktif: $('#daily_lumpur_aktif').val() || null,
                clarifier_2: $('#daily_clarifier_2').val() || null,
                outlet: $('#daily_outlet').val() || null
            };

            $.ajax({
                url: "{{ url('api/wwtp-performance/ph-harian') }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    // Show success modal
                    $('#successMessage').html(response.message || 'Data pH harian berhasil disimpan!');
                    $('#successModal').modal('show');

                    // Reset form
                    $('#dailyPHForm')[0].reset();
                    $('#daily_tanggal').val(today);
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    let message = 'Terjadi kesalahan saat menyimpan data!';

                    if (error && error.message) {
                        message = error.message;
                    } else if (error && error.errors) {
                        message = Object.values(error.errors).flat().join('<br>');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: message,
                        confirmButtonColor: '#3085d6'
                    });
                },
                complete: function() {
                    btnSubmit.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endsection