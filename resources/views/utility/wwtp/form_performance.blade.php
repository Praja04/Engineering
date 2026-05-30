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
                        <div class="col-md-4">
                            <div class="card form-selector-card h-100 active" data-bs-toggle="tab"
                                data-bs-target="#weeklyForm" role="tab">
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <div class="mb-3">
                                            <div class="avatar-xl mx-auto">
                                                <div class="avatar-title bg-success-subtle rounded-circle">
                                                    <i class="mdi mdi-calendar-week text-success"
                                                        style="font-size: 3rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="mb-2 fw-semibold text-success">Data Mingguan</h4>
                                        <p class="text-muted mb-3">
                                            Input data TSS dan COD mingguan per tahapan
                                        </p>
                                        <div class="mt-4">
                                            <span class="badge bg-success-subtle text-success px-3 py-2">
                                                <i class="mdi mdi-cursor-pointer me-1"></i> Klik untuk Memilih
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-active-indicator"></div>
                            </div>
                        </div>

                        <!-- Daily Data Card -->
                        <div class="col-md-4">
                            <div class="card form-selector-card h-100" data-bs-toggle="tab" data-bs-target="#dailyForm"
                                role="tab">
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <div class="mb-3">
                                            <div class="avatar-xl mx-auto">
                                                <div class="avatar-title bg-primary-subtle rounded-circle">
                                                    <i class="mdi mdi-calendar-today text-primary"
                                                        style="font-size: 3rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="mb-2 fw-semibold text-primary">Data Harian</h4>
                                        <p class="text-muted mb-3">
                                            Input data pH harian per shift kerja
                                        </p>
                                        <div class="mt-4">
                                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                                <i class="mdi mdi-cursor-pointer me-1"></i> Klik untuk Memilih
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-active-indicator"></div>
                            </div>
                        </div>

                        <!-- Sample Data Card -->
                        <div class="col-md-4">
                            <div class="card form-selector-card h-100" data-bs-toggle="tab" data-bs-target="#sampleForm"
                                role="tab">
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <div class="mb-3">
                                            <div class="avatar-xl mx-auto">
                                                <div class="avatar-title bg-warning-subtle rounded-circle">
                                                    <i class="mdi mdi-test-tube text-warning" style="font-size: 3rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="mb-2 fw-semibold text-warning">Data Sample</h4>
                                        <p class="text-muted mb-3">
                                            Input data sampel berdasarkan jenis sample WWTP
                                        </p>
                                        <div class="mt-4">
                                            <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                                <i class="mdi mdi-cursor-pointer me-1"></i> Klik untuk Memilih
                                            </span>
                                        </div>
                                    </div>
                                </div>
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
                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <label for="weekly_tanggal" class="form-label fw-semibold">
                                                    Tanggal <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" class="form-control" id="weekly_tanggal"
                                                    name="tanggal" required>
                                                <div class="form-text">Tanggal pencatatan data (minggu akan otomatis
                                                    terdeteksi)</div>
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
                                                                <i class="mdi mdi-beaker text-success me-1"></i>TSS (Total
                                                                Suspended Solids) <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="weekly_tss" name="tss" min="0"
                                                                    placeholder="0.00" required>
                                                                <span class="input-group-text bg-light">mg/L</span>
                                                            </div>
                                                            <small class="form-text text-muted">Total padatan
                                                                tersuspensi</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card border border-success">
                                                        <div class="card-body">
                                                            <label for="weekly_cod" class="form-label fw-semibold">
                                                                <i class="mdi mdi-flask text-success me-1"></i>COD
                                                                (Chemical Oxygen Demand) <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="weekly_cod" name="cod" min="0"
                                                                    placeholder="0.00" required>
                                                                <span class="input-group-text bg-light">mg/L</span>
                                                            </div>
                                                            <small class="form-text text-muted">Kebutuhan oksigen
                                                                kimiawi</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <label for="weekly_foto" class="form-label fw-semibold">
                                                        <i class="mdi mdi-camera me-1"></i>Foto Dokumentasi
                                                    </label>
                                                    <input type="file" class="form-control" id="weekly_foto"
                                                        name="foto" accept="image/*">
                                                    <div class="mt-3">
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            id="openCameraBtn">
                                                            <i class="mdi mdi-camera"></i> Ambil dari Kamera
                                                        </button>
                                                    </div>

                                                    <!-- Camera Container -->
                                                    <div id="cameraContainer" class="mt-3 d-none">
                                                        <video id="cameraPreview" autoplay playsinline
                                                            class="w-100 rounded" style="max-height:300px;"></video>

                                                        <div class="mt-2 d-flex gap-2">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                id="captureBtn">
                                                                <i class="mdi mdi-camera"></i> Ambil Foto
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                id="closeCameraBtn">
                                                                Tutup Kamera
                                                            </button>
                                                        </div>

                                                        <canvas id="snapshotCanvas" class="d-none"></canvas>
                                                    </div>
                                                    <div class="form-text">Upload foto hasil pengukuran (Opsional, max 2MB)
                                                    </div>
                                                    <div id="weekly_foto_preview" class="mt-3" style="display: none;">
                                                        <img src="" alt="Preview" class="img-thumbnail"
                                                            style="max-width: 200px;">
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
                                                    <strong>Catatan:</strong> Setiap jenis tahapan hanya dapat diinput
                                                    <strong>1x per minggu</strong>.
                                                    Minggu akan otomatis terdeteksi dari tanggal yang dipilih.
                                                </div>
                                            </div>
                                        </div>

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
                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <label for="daily_tanggal" class="form-label fw-semibold">
                                                    Tanggal <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" class="form-control" id="daily_tanggal"
                                                    name="tanggal" required>
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

                                        <div class="mb-4">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1 text-primary">
                                                        <i class="mdi mdi-ph me-2"></i>Data pH Per Tahapan
                                                    </h5>
                                                    <p class="text-muted mb-0">Input nilai pH pada berbagai tahapan proses
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <div class="card border border-primary">
                                                        <div class="card-body">
                                                            <label for="daily_equalisasi_1"
                                                                class="form-label fw-semibold">
                                                                <i class="mdi mdi-water text-primary me-1"></i>Equalisasi 1
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="daily_equalisasi_1" name="equalisasi_1"
                                                                    min="0" max="14" placeholder="0.00">
                                                                <span class="input-group-text bg-light">pH</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card border border-primary">
                                                        <div class="card-body">
                                                            <label for="daily_equalisasi_2"
                                                                class="form-label fw-semibold">
                                                                <i class="mdi mdi-water text-primary me-1"></i>Equalisasi 2
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="daily_equalisasi_2" name="equalisasi_2"
                                                                    min="0" max="14" placeholder="0.00">
                                                                <span class="input-group-text bg-light">pH</span>
                                                            </div>
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
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="daily_netralisasi" name="netralisasi"
                                                                    min="0" max="14" placeholder="0.00">
                                                                <span class="input-group-text bg-light">pH</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card border">
                                                        <div class="card-body">
                                                            <label for="daily_sedimentasi_1" class="form-label">
                                                                <i class="mdi mdi-filter me-1"></i>Sedimentasi 1
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="daily_sedimentasi_1" name="sedimentasi_1"
                                                                    min="0" max="14" placeholder="0.00">
                                                                <span class="input-group-text bg-light">pH</span>
                                                            </div>
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
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="daily_sedimentasi_2" name="sedimentasi_2"
                                                                    min="0" max="14" placeholder="0.00">
                                                                <span class="input-group-text bg-light">pH</span>
                                                            </div>
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
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="daily_outlet_anaerob" name="outlet_anaerob"
                                                                    min="0" max="14" placeholder="0.00">
                                                                <span class="input-group-text bg-light">pH</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card border">
                                                        <div class="card-body">
                                                            <label for="daily_aerob" class="form-label">
                                                                <i class="mdi mdi-air-filter me-1"></i>Aerob
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="daily_aerob" name="aerob" min="0"
                                                                    max="14" placeholder="0.00">
                                                                <span class="input-group-text bg-light">pH</span>
                                                            </div>
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
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="daily_lumpur_aktif" name="lumpur_aktif"
                                                                    min="0" max="14" placeholder="0.00">
                                                                <span class="input-group-text bg-light">pH</span>
                                                            </div>
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
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="daily_clarifier_2" name="clarifier_2"
                                                                    min="0" max="14" placeholder="0.00">
                                                                <span class="input-group-text bg-light">pH</span>
                                                            </div>
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
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="daily_outlet" name="outlet" min="0"
                                                                    max="14" placeholder="0.00">
                                                                <span class="input-group-text bg-light">pH</span>
                                                            </div>
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
                                                    <strong>Penting:</strong> Setiap tanggal maksimal memiliki <strong>3
                                                        shift</strong> (shift1, shift2, shift3).
                                                    Nilai pH normal berkisar antara 0-14.
                                                </div>
                                            </div>
                                        </div>

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

                        <!-- ===================== SAMPLE FORM ===================== -->
                        <div class="tab-pane" id="sampleForm" role="tabpanel">
                            <div class="card">
                                <div class="card-header bg-light border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-xs">
                                                <div class="avatar-title bg-warning rounded-circle">
                                                    <i class="mdi mdi-test-tube"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="mb-0">Data Performance Sampel WWTP</h5>
                                            <p class="text-muted mb-0">Input data parameter kualitas sampel</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-warning text-dark">Sample</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    {{-- Loading state jenis sampel --}}
                                    <div id="sampleLoadingState" class="text-center py-4">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-muted mt-2 mb-0">Memuat daftar jenis sampel...</p>
                                    </div>

                                    {{-- Error state jenis sampel --}}
                                    <div id="sampleErrorState" class="alert alert-danger d-none">
                                        <i class="mdi mdi-alert-circle me-2"></i>
                                        Gagal memuat daftar jenis sampel.
                                        <a href="javascript:void(0)" id="retrySampleLoad" class="alert-link">Coba
                                            lagi</a>
                                    </div>

                                    <form id="samplePerformanceForm" class="d-none">
                                        <!-- Tanggal & Jenis Sampel -->
                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <label for="sample_tanggal" class="form-label fw-semibold">
                                                    Tanggal <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" class="form-control" id="sample_tanggal"
                                                    name="tanggal" required>
                                                <div class="form-text">Tanggal pengambilan sampel</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="sample_id_sampel" class="form-label fw-semibold">
                                                    Jenis Sampel <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="sample_id_sampel" name="id_sampel"
                                                    required>
                                                    <option value="">-- Pilih Jenis Sampel --</option>
                                                    {{-- Options diisi dari JS --}}
                                                </select>
                                                <div class="form-text">Pilih jenis sampel yang diuji</div>
                                            </div>
                                        </div>

                                        <!-- Info nama sampel yang dipilih -->
                                        <div id="selectedSampleInfo"
                                            class="alert alert-warning-subtle border border-warning-subtle d-none mb-4">
                                            <div class="d-flex align-items-center">
                                                <i class="mdi mdi-test-tube text-warning fs-4 me-2"></i>
                                                <div>
                                                    <strong class="text-warning">Jenis Sampel Dipilih:</strong>
                                                    <span id="selectedSampleName" class="ms-1 fw-semibold"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Parameter Data -->
                                        <div class="mb-4">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1 text-warning">
                                                        <i class="mdi mdi-flask-outline me-2"></i>Parameter Kualitas Sampel
                                                    </h5>
                                                    <p class="text-muted mb-0">Input nilai parameter hasil pengujian sampel
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="row g-3">
                                                <!-- TSS -->
                                                <div class="col-md-12">
                                                    <div class="card border border-warning h-100">
                                                        <div class="card-body">
                                                            <label for="sample_tss" class="form-label fw-semibold">
                                                                <i class="mdi mdi-beaker text-warning me-1"></i>
                                                                TSS <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="sample_tss" name="tss" min="0"
                                                                    placeholder="0.00" required>
                                                                <span class="input-group-text bg-light">mg/L</span>
                                                            </div>
                                                            <small class="form-text text-muted">Total Suspended
                                                                Solids</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- SV30 -->
                                                <div class="col-md-12">
                                                    <div class="card border border-warning h-100">
                                                        <div class="card-body">
                                                            <label for="sample_sv30" class="form-label fw-semibold">
                                                                <i class="mdi mdi-chart-bar text-warning me-1"></i>
                                                                SV30 <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="sample_sv30" name="sv30" min="0"
                                                                    placeholder="0.00" required>
                                                                <span class="input-group-text bg-light">mL/L</span>
                                                            </div>
                                                            <small class="form-text text-muted">Sludge Volume 30
                                                                menit</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- pH -->
                                                <div class="col-md-12">
                                                    <div class="card border border-warning h-100">
                                                        <div class="card-body">
                                                            <label for="sample_ph" class="form-label fw-semibold">
                                                                <i class="mdi mdi-ph text-warning me-1"></i>
                                                                pH <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="sample_ph" name="ph" min="0"
                                                                    max="14" placeholder="0.00" required>
                                                                <span class="input-group-text bg-light">pH</span>
                                                            </div>
                                                            <small class="form-text text-muted">Derajat keasaman
                                                                (0–14)</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- MLSS -->
                                                <div class="col-md-12">
                                                    <div class="card border h-100">
                                                        <div class="card-body">
                                                            <label for="sample_mlss" class="form-label fw-semibold">
                                                                <i class="mdi mdi-bacteria me-1"></i>
                                                                MLSS
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="sample_mlss" name="mlss" min="0"
                                                                    placeholder="0.00">
                                                                <span class="input-group-text bg-light">mg/L</span>
                                                            </div>
                                                            <small class="form-text text-muted">Mixed Liquor Suspended
                                                                Solids</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- SVL -->
                                                <div class="col-md-12">
                                                    <div class="card border h-100">
                                                        <div class="card-body">
                                                            <label for="sample_svl" class="form-label fw-semibold">
                                                                <i class="mdi mdi-water-percent me-1"></i>
                                                                SVI
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="sample_svl" name="svl" min="0"
                                                                    placeholder="0.00">
                                                                <span class="input-group-text bg-light">mL/g</span>
                                                            </div>
                                                            <small class="form-text text-muted">Sludge Volume
                                                                Loading</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- DO -->
                                                <div class="col-md-12">
                                                    <div class="card border h-100">
                                                        <div class="card-body">
                                                            <label for="sample_do" class="form-label fw-semibold">
                                                                <i class="mdi mdi-air-filter me-1"></i>
                                                                DO
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="sample_do" name="do" min="0"
                                                                    placeholder="0.00">
                                                                <span class="input-group-text bg-light">mg/L</span>
                                                            </div>
                                                            <small class="form-text text-muted">Dissolved Oxygen</small>
                                                        </div>
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
                                                    <strong>Catatan:</strong> Pastikan jenis sampel dipilih sesuai dengan
                                                    lokasi pengambilan sampel.
                                                    Semua parameter bertanda <span class="text-danger fw-bold">*</span>
                                                    wajib diisi.
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="reset" class="btn btn-light" id="resetSampleForm">
                                                        <i class="mdi mdi-refresh me-1"></i> Reset Form
                                                    </button>
                                                    <button type="submit" class="btn btn-warning text-dark"
                                                        id="submitSampleForm">
                                                        <i class="mdi mdi-content-save me-1"></i> Simpan Data Sampel
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- ===================== END SAMPLE FORM ===================== --}}

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
                    <p class="text-muted mb-4" id="successMessage">Data Performance WWTP telah berhasil disimpan ke
                        sistem.</p>
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
        .card.border-primary {
            border-color: #405189 !important;
        }

        .card.border-success {
            border-color: #0ab39c !important;
        }

        .card.border-warning {
            border-color: #f7b84b !important;
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

        /* Form Selector Cards */
        .form-selector-card {
            cursor: pointer;
            border: 2px solid #e9ebec;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .form-selector-card:hover {
            border-color: #405189;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(64, 81, 137, 0.15);
        }

        .form-selector-card.active {
            border-color: #f7b84b;
            box-shadow: 0 4px 20px rgba(247, 184, 75, 0.2);
        }

        /* Color the active border per card type */
        #weeklyForm-card.form-selector-card.active,
        [data-bs-target="#weeklyForm"].form-selector-card.active {
            border-color: #0ab39c;
            box-shadow: 0 4px 20px rgba(10, 179, 156, 0.2);
        }

        [data-bs-target="#dailyForm"].form-selector-card.active {
            border-color: #405189;
            box-shadow: 0 4px 20px rgba(64, 81, 137, 0.2);
        }

        [data-bs-target="#sampleForm"].form-selector-card.active {
            border-color: #f7b84b;
            box-shadow: 0 4px 20px rgba(247, 184, 75, 0.2);
        }

        .card-active-indicator {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: transparent;
            transition: background 0.2s ease;
        }

        [data-bs-target="#weeklyForm"].form-selector-card.active .card-active-indicator {
            background: #0ab39c;
        }

        [data-bs-target="#dailyForm"].form-selector-card.active .card-active-indicator {
            background: #405189;
        }

        [data-bs-target="#sampleForm"].form-selector-card.active .card-active-indicator {
            background: #f7b84b;
        }

        .alert-warning-subtle {
            background-color: rgba(247, 184, 75, 0.08);
        }
    </style>

    <script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            let stream = null;

            $('#openCameraBtn').on('click', async function() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: "environment"
                        } // kamera belakang
                    });

                    $('#cameraContainer').removeClass('d-none');
                    $('#cameraPreview')[0].srcObject = stream;

                } catch (err) {
                    alert('Tidak bisa mengakses kamera: ' + err.message);
                }
            });

            $('#closeCameraBtn').on('click', function() {
                stopCamera();
            });

            function stopCamera() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
                $('#cameraContainer').addClass('d-none');
            }

            $('#captureBtn').on('click', function() {
                const video = document.getElementById('cameraPreview');
                const canvas = document.getElementById('snapshotCanvas');

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Convert ke blob
                canvas.toBlob(function(blob) {
                    const file = new File([blob], "camera.jpg", {
                        type: "image/jpeg"
                    });

                    // Masukkan ke input file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    document.getElementById('weekly_foto').files = dataTransfer.files;

                    // Preview
                    const url = URL.createObjectURL(blob);
                    $('#weekly_foto_preview img').attr('src', url);
                    $('#weekly_foto_preview').show();

                    stopCamera();
                }, 'image/jpeg', 0.9);
            });

            const today = new Date().toISOString().split('T')[0];
            $('#daily_tanggal').val(today);
            $('#weekly_tanggal').val(today);
            $('#sample_tanggal').val(today);

            // =========================================
            // Card selector logic
            // =========================================
            $('.form-selector-card').on('click', function() {
                $('.form-selector-card').removeClass('active');
                $(this).addClass('active');

                const target = $(this).data('bs-target');
                $('.tab-pane').removeClass('show active');
                $(target).addClass('show active');
            });

            // =========================================
            // Preview foto weekly
            // =========================================
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

            // =========================================
            // SUBMIT: Weekly
            // =========================================
            $('#weeklyPerformanceForm').on('submit', function(e) {
                e.preventDefault();
                const btn = $('#submitWeeklyForm');
                const originalText = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: "{{ url('api/wwtp-performance') }}",
                    method: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#successMessage').html(response.message ||
                            'Data performance mingguan berhasil disimpan!');
                        $('#successModal').modal('show');
                        $('#weeklyPerformanceForm')[0].reset();
                        $('#weekly_tanggal').val(today);
                        $('#weekly_foto_preview').hide();
                    },
                    error: function(xhr) {
                        showErrorSwal(xhr);
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // =========================================
            // SUBMIT: Daily PH
            // =========================================
            $('#dailyPHForm').on('submit', function(e) {
                e.preventDefault();
                const btn = $('#submitDailyForm');
                const originalText = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: "{{ url('api/wwtp-performance/ph-harian') }}",
                    method: 'POST',
                    data: {
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
                        outlet: $('#daily_outlet').val() || null,
                    },
                    success: function(response) {
                        $('#successMessage').html(response.message ||
                            'Data pH harian berhasil disimpan!');
                        $('#successModal').modal('show');
                        $('#dailyPHForm')[0].reset();
                        $('#daily_tanggal').val(today);
                    },
                    error: function(xhr) {
                        showErrorSwal(xhr);
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // =========================================
            // LOAD: Jenis Sampel
            // =========================================
            function loadJenisSampel() {
                $('#sampleLoadingState').removeClass('d-none');
                $('#sampleErrorState').addClass('d-none');
                $('#samplePerformanceForm').addClass('d-none');

                $.ajax({
                    url: "{{ url('api/wwtp-performance/jenis-sampel') }}",
                    method: 'GET',
                    success: function(response) {
                        const select = $('#sample_id_sampel');
                        select.find('option:not(:first)').remove(); // clear existing options

                        if (response.success && response.data.length > 0) {
                            response.data.forEach(function(item) {
                                select.append(
                                    $('<option>', {
                                        value: item.id,
                                        text: item.nama_sampel,
                                        'data-nama': item.nama_sampel
                                    })
                                );
                            });
                            $('#sampleLoadingState').addClass('d-none');
                            $('#samplePerformanceForm').removeClass('d-none');
                        } else {
                            // No data
                            $('#sampleLoadingState').addClass('d-none');
                            $('#sampleErrorState')
                                .removeClass('d-none')
                                .html(
                                    '<i class="mdi mdi-alert-circle me-2"></i>Belum ada jenis sampel yang tersedia. Tambahkan terlebih dahulu melalui manajemen master data.'
                                );
                        }
                    },
                    error: function() {
                        $('#sampleLoadingState').addClass('d-none');
                        $('#sampleErrorState').removeClass('d-none').html(
                            '<i class="mdi mdi-alert-circle me-2"></i>Gagal memuat daftar jenis sampel. ' +
                            '<a href="javascript:void(0)" id="retrySampleLoad" class="alert-link">Coba lagi</a>'
                        );
                        // Re-bind retry karena HTML di-replace
                        $(document).on('click', '#retrySampleLoad', loadJenisSampel);
                    }
                });
            }

            // Load saat tab sample diklik
            $('[data-bs-target="#sampleForm"]').on('click', function() {
                // Hanya load jika belum ada option (belum pernah di-load)
                if ($('#sample_id_sampel option').length <= 1) {
                    loadJenisSampel();
                }
            });

            // Tampilkan info jenis sampel yang dipilih
            $('#sample_id_sampel').on('change', function() {
                const selected = $(this).find('option:selected');
                if (selected.val()) {
                    $('#selectedSampleName').text(selected.data('nama'));
                    $('#selectedSampleInfo').removeClass('d-none');
                } else {
                    $('#selectedSampleInfo').addClass('d-none');
                }
            });

            // Reset sample form - kembalikan tanggal ke hari ini & sembunyikan info
            $('#resetSampleForm').on('click', function() {
                setTimeout(function() {
                    $('#sample_tanggal').val(today);
                    $('#selectedSampleInfo').addClass('d-none');
                }, 10);
            });

            // =========================================
            // SUBMIT: Sample
            // =========================================
            $('#samplePerformanceForm').on('submit', function(e) {
                e.preventDefault();
                const btn = $('#submitSampleForm');
                const originalText = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: "{{ url('api/wwtp-performance/sample') }}",
                    method: 'POST',
                    data: {
                        tanggal: $('#sample_tanggal').val(),
                        id_sampel: $('#sample_id_sampel').val(),
                        tss: $('#sample_tss').val(),
                        sv30: $('#sample_sv30').val(),
                        ph: $('#sample_ph').val(),
                        mlss: $('#sample_mlss').val(),
                        svl: $('#sample_svl').val(),
                        do: $('#sample_do').val(),
                    },
                    success: function(response) {
                        $('#successMessage').html(response.message ||
                            'Data sampel berhasil disimpan!');
                        $('#successModal').modal('show');
                        $('#samplePerformanceForm')[0].reset();
                        $('#sample_tanggal').val(today);
                        $('#selectedSampleInfo').addClass('d-none');
                    },
                    error: function(xhr) {
                        showErrorSwal(xhr);
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // =========================================
            // Helper: Error SweetAlert
            // =========================================
            function showErrorSwal(xhr) {
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
            }
        });
    </script>
@endsection
