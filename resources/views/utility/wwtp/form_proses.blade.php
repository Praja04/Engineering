@extends('layouts.app')

@section('title', 'Tambah Data WWTP')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">Tambah Data WWTP</h4>
                            <p class="text-muted mb-0">Input data Wastewater Treatment Plant</p>
                        </div>
                        <div>
                            <a href="{{ url('/wwtp/data_proses') }}" class="btn btn-outline-secondary">
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
                        <!-- Daily Data Card -->
                        <div class="col-md-6">
                            <div class="card form-selector-card h-100 active" data-bs-toggle="tab"
                                data-bs-target="#dailyForm" role="tab">
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <!-- Icon/Image -->
                                        <div class="mb-3">
                                            <div class="avatar-xl mx-auto">
                                                <div class="avatar-title bg-primary-subtle rounded-circle">
                                                    <i class="mdi mdi-calendar-today text-primary"
                                                        style="font-size: 3rem;"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Title -->
                                        <h4 class="mb-2 fw-semibold text-primary">Data Harian</h4>

                                        <!-- Description -->
                                        <p class="text-muted mb-3">
                                            Input volume air masuk per shift kerja (Shift 1, 2, dan 3)
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

                        <!-- Weekly Data Card -->
                        <div class="col-md-6">
                            <div class="card form-selector-card h-100" data-bs-toggle="tab" data-bs-target="#weeklyForm"
                                role="tab">
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <!-- Icon/Image -->
                                        <div class="mb-3">
                                            <div class="avatar-xl mx-auto">
                                                <div class="avatar-title bg-success-subtle rounded-circle">
                                                    <i class="mdi mdi-calendar-week text-success"
                                                        style="font-size: 3rem;"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Title -->
                                        <h4 class="mb-2 fw-semibold text-success">Data Mingguan</h4>

                                        <!-- Description -->
                                        <p class="text-muted mb-3">
                                            Input data konsolidasi mingguan influent dan effluent
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
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="row">
                <div class="col-12">
                    <div class="tab-content">

                        <!-- Daily Form -->
                        <div class="tab-pane active" id="dailyForm" role="tabpanel">
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
                                            <h5 class="mb-0">Data WWTP Harian</h5>
                                            <p class="text-muted mb-0">Input volume air masuk harian per shift</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-primary">Harian</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form id="dailyWwtpForm">
                                        <!-- Basic Information -->
                                        <div class="row mb-4">
                                            <div class="col-md-3 mb-3">
                                                <label for="daily_debit1" class="form-label fw-semibold">
                                                    Debit 1 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control" id="daily_debit1" name="debit1"
                                                    required>
                                                <div class="form-text">Debit air masuk (m³/h)</div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="daily_debit2" class="form-label fw-semibold">
                                                    Debit 2 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control" id="daily_debit2" name="debit2"
                                                    required>
                                                <div class="form-text">Debit air masuk (m³/h)</div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="daily_running_wwtp1" class="form-label fw-semibold">
                                                    Running WWTP 1 <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="daily_running_wwtp1" name="running_wwtp1"
                                                    required>
                                                    <option value="">-- Pilih Status --</option>
                                                    <option value="ON">ON</option>
                                                    <option value="OFF">OFF</option>
                                                </select>
                                                <div class="form-text">Status operasional WWTP 1</div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="daily_running_wwtp2" class="form-label fw-semibold">
                                                    Running WWTP 2 <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="daily_running_wwtp2" name="running_wwtp2"
                                                    required>
                                                    <option value="">-- Pilih Status --</option>
                                                    <option value="ON">ON</option>
                                                    <option value="OFF">OFF</option>
                                                </select>
                                                <div class="form-text">Status operasional WWTP 2</div>
                                            </div>
                                        </div>


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

                                        <!-- Approval Selectors (conditionally visible) -->
                                        <div class="row mb-4" id="daily_approval_row" style="display: none;">
                                            <div class="col-md-6 mb-3">
                                                <label for="daily_foreman_id" class="form-label fw-semibold">
                                                    Pilih Foreman <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="daily_foreman_id" name="foreman_id">
                                                    <option value="">-- Pilih Foreman --</option>
                                                </select>
                                                <div class="form-text">Foreman yang akan memverifikasi laporan ini</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="daily_supervisor_id" class="form-label fw-semibold">
                                                    Pilih Supervisor <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="daily_supervisor_id"
                                                    name="supervisor_id">
                                                    <option value="">-- Pilih Supervisor --</option>
                                                </select>
                                                <div class="form-text">Supervisor yang akan menyetujui laporan ini</div>
                                            </div>
                                        </div>

                                        <!-- Influent Data -->
                                        <div class="mb-4">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1 text-primary">
                                                        <i class="mdi mdi-water-pump me-2"></i>Data Influent Harian
                                                    </h5>
                                                    <p class="text-muted mb-0">Input volume air masuk dari berbagai sumber
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        id="calculateDailyTotal">
                                                        <i class="mdi mdi-calculator me-1"></i> Hitung Total
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="row g-3">
                                                <!-- Required Fields -->
                                                <div class="col-md-4">
                                                    <div class="card border border-primary shadow-sm h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="avatar-xs me-2">
                                                                    <div
                                                                        class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                                        <i class="mdi mdi-water fs-5"></i>
                                                                    </div>
                                                                </div>
                                                                <h6 class="mb-0 fw-semibold text-primary">Pit Sparta <span
                                                                        class="text-danger">*</span></h6>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small text-muted mb-1">Data
                                                                    Awal</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control bg-light"
                                                                        id="daily_pit_sparta_awal" name="pit_sparta_awal"
                                                                        readonly placeholder="0.00">
                                                                    <span
                                                                        class="input-group-text bg-light text-muted">m³</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label
                                                                    class="form-label small text-muted mb-1 text-dark">Data
                                                                    Sekarang</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control border-primary"
                                                                        id="daily_pit_sparta" name="pit_sparta"
                                                                        min="0" placeholder="0.00" required>
                                                                    <span
                                                                        class="input-group-text bg-primary text-white border-primary">m³</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">
                                                                <i class="mdi mdi-information-outline me-1"></i>Volume air
                                                                dari Sparta
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="card border border-primary shadow-sm h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="avatar-xs me-2">
                                                                    <div
                                                                        class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                                        <i class="mdi mdi-water fs-5"></i>
                                                                    </div>
                                                                </div>
                                                                <h6 class="mb-0 fw-semibold text-primary">Pit Garam <span
                                                                        class="text-danger">*</span></h6>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small text-muted mb-1">Data
                                                                    Awal</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control bg-light"
                                                                        id="daily_pit_garam_awal" name="pit_garam_awal"
                                                                        readonly placeholder="0.00">
                                                                    <span
                                                                        class="input-group-text bg-light text-muted">m³</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label
                                                                    class="form-label small text-muted mb-1 text-dark">Data
                                                                    Sekarang</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control border-primary"
                                                                        id="daily_pit_garam" name="pit_garam"
                                                                        min="0" placeholder="0.00" required>
                                                                    <span
                                                                        class="input-group-text bg-primary text-white border-primary">m³</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">
                                                                <i class="mdi mdi-information-outline me-1"></i>Volume air
                                                                dari Garam
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="card border border-primary shadow-sm h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="avatar-xs me-2">
                                                                    <div
                                                                        class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                                        <i class="mdi mdi-water fs-5"></i>
                                                                    </div>
                                                                </div>
                                                                <h6 class="mb-0 fw-semibold text-primary">Pit Domestik
                                                                    <span class="text-danger">*</span>
                                                                </h6>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small text-muted mb-1">Data
                                                                    Awal</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control bg-light"
                                                                        id="daily_pit_domestik_awal"
                                                                        name="pit_domestik_awal" readonly
                                                                        placeholder="0.00">
                                                                    <span
                                                                        class="input-group-text bg-light text-muted">m³</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label
                                                                    class="form-label small text-muted mb-1 text-dark">Data
                                                                    Sekarang</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control border-primary"
                                                                        id="daily_pit_domestik" name="pit_domestik"
                                                                        min="0" placeholder="0.00" required>
                                                                    <span
                                                                        class="input-group-text bg-primary text-white border-primary">m³</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">
                                                                <i class="mdi mdi-information-outline me-1"></i>Volume air
                                                                domestik
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Optional Fields -->
                                                <div class="col-md-4">
                                                    <div class="card border border-secondary-subtle shadow-sm h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="avatar-xs me-2">
                                                                    <div
                                                                        class="avatar-title bg-light text-secondary rounded-circle">
                                                                        <i class="mdi mdi-factory fs-5"></i>
                                                                    </div>
                                                                </div>
                                                                <h6 class="mb-0 fw-semibold text-dark">Pit Produksi Step 3
                                                                </h6>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small text-muted mb-1">Data
                                                                    Awal</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control bg-light"
                                                                        id="daily_pit_produksi_step3_awal"
                                                                        name="pit_produksi_step3_awal" readonly
                                                                        placeholder="0.00">
                                                                    <span
                                                                        class="input-group-text bg-light text-muted">m³</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label
                                                                    class="form-label small text-muted mb-1 text-dark">Data
                                                                    Sekarang</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="daily_pit_produksi_step3"
                                                                        name="pit_produksi_step3" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">
                                                                <i class="mdi mdi-information-outline me-1"></i>Step 3 ke
                                                                Equal 1
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="card border border-secondary-subtle shadow-sm h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="avatar-xs me-2">
                                                                    <div
                                                                        class="avatar-title bg-light text-secondary rounded-circle">
                                                                        <i class="mdi mdi-archive fs-5"></i>
                                                                    </div>
                                                                </div>
                                                                <h6 class="mb-0 fw-semibold text-dark">Pit Storage</h6>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small text-muted mb-1">Data
                                                                    Awal</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control bg-light"
                                                                        id="daily_pit_storage_awal"
                                                                        name="pit_storage_awal" readonly
                                                                        placeholder="0.00">
                                                                    <span
                                                                        class="input-group-text bg-light text-muted">m³</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label
                                                                    class="form-label small text-muted mb-1 text-dark">Data
                                                                    Sekarang</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="daily_pit_storage"
                                                                        name="pit_storage" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">
                                                                <i class="mdi mdi-information-outline me-1"></i>Volume
                                                                storage
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="card border border-secondary-subtle shadow-sm h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="avatar-xs me-2">
                                                                    <div
                                                                        class="avatar-title bg-light text-secondary rounded-circle">
                                                                        <i class="mdi mdi-water-treatment fs-5"></i>
                                                                    </div>
                                                                </div>
                                                                <h6 class="mb-0 fw-semibold text-dark">Pit Proses WWTP 2
                                                                </h6>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small text-muted mb-1">Data
                                                                    Awal</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control bg-light"
                                                                        id="daily_pit_proses_wwtp2_awal"
                                                                        name="pit_proses_wwtp2_awal" readonly
                                                                        placeholder="0.00">
                                                                    <span
                                                                        class="input-group-text bg-light text-muted">m³</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label
                                                                    class="form-label small text-muted mb-1 text-dark">Data
                                                                    Sekarang</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="daily_pit_proses_wwtp2"
                                                                        name="pit_proses_wwtp2" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">
                                                                <i class="mdi mdi-information-outline me-1"></i>Proses WWTP
                                                                2
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="card border border-secondary-subtle shadow-sm h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="avatar-xs me-2">
                                                                    <div
                                                                        class="avatar-title bg-light text-secondary rounded-circle">
                                                                        <i class="mdi mdi-pipe fs-5"></i>
                                                                    </div>
                                                                </div>
                                                                <h6 class="mb-0 fw-semibold text-dark">Pit Outlet</h6>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small text-muted mb-1">Data
                                                                    Awal</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control bg-light"
                                                                        id="daily_pit_outlet_awal" name="pit_outlet_awal"
                                                                        readonly placeholder="0.00">
                                                                    <span
                                                                        class="input-group-text bg-light text-muted">m³</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label
                                                                    class="form-label small text-muted mb-1 text-dark">Data
                                                                    Sekarang</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="daily_pit_outlet"
                                                                        name="pit_outlet" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">
                                                                <i class="mdi mdi-information-outline me-1"></i>Outlet
                                                                volume
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="card border border-secondary-subtle shadow-sm h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="avatar-xs me-2">
                                                                    <div
                                                                        class="avatar-title bg-light text-secondary rounded-circle">
                                                                        <i class="mdi mdi-boiler fs-5"></i>
                                                                    </div>
                                                                </div>
                                                                <h6 class="mb-0 fw-semibold text-dark">Pit Boiler</h6>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small text-muted mb-1">Data
                                                                    Awal</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control bg-light"
                                                                        id="daily_pit_boiler_awal" name="pit_boiler_awal"
                                                                        readonly placeholder="0.00">
                                                                    <span
                                                                        class="input-group-text bg-light text-muted">m³</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label
                                                                    class="form-label small text-muted mb-1 text-dark">Data
                                                                    Sekarang</label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="daily_pit_boiler"
                                                                        name="pit_boiler" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">
                                                                <i class="mdi mdi-information-outline me-1"></i>Boiler
                                                                volume
                                                            </small>
                                                        </div>
                                                    </div>
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

                        <!-- Weekly Form -->
                        <div class="tab-pane" id="weeklyForm" role="tabpanel">
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
                                            <h5 class="mb-0">Data WWTP Mingguan</h5>
                                            <p class="text-muted mb-0">Input data konsolidasi mingguan</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-success">Mingguan</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form id="weeklyWwtpForm">
                                        <!-- Basic Information -->
                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <label for="weekly_tanggal" class="form-label fw-semibold">
                                                    Tanggal <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" class="form-control" id="weekly_tanggal"
                                                    name="tanggal" required>
                                                <div class="form-text">Tanggal pencatatan data mingguan</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="weekly_kategori" class="form-label fw-semibold">
                                                    Kategori <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="weekly_kategori" name="kategori"
                                                    required>
                                                    <option value="">-- Pilih Kategori --</option>
                                                    <option value="influent">Influent (Air Masuk)</option>
                                                    <option value="effluent">Effluent (Air Keluar)</option>
                                                </select>
                                                <div class="form-text">Jenis data yang akan diinput</div>
                                            </div>
                                        </div>

                                        <!-- Influent Form -->
                                        <div id="weekly_influent_form" style="display: none;">
                                            <div class="mb-4">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1 text-primary">
                                                            <i class="mdi mdi-water-pump me-2"></i>Data Influent Mingguan
                                                        </h5>
                                                        <p class="text-muted mb-0">Input volume air masuk mingguan</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            id="calculateInfluentTotal">
                                                            <i class="mdi mdi-calculator me-1"></i> Hitung Total
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <div class="card border border-primary">
                                                            <div class="card-body">
                                                                <label for="weekly_pit_sparta"
                                                                    class="form-label fw-semibold">
                                                                    <i class="mdi mdi-water text-primary me-1"></i>Pit
                                                                    Sparta
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="weekly_pit_sparta"
                                                                        name="pit_sparta" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="card border border-primary">
                                                            <div class="card-body">
                                                                <label for="weekly_pit_garam"
                                                                    class="form-label fw-semibold">
                                                                    <i class="mdi mdi-water text-primary me-1"></i>Pit
                                                                    Garam
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="weekly_pit_garam"
                                                                        name="pit_garam" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="card border border-primary">
                                                            <div class="card-body">
                                                                <label for="weekly_pit_domestik"
                                                                    class="form-label fw-semibold">
                                                                    <i class="mdi mdi-water text-primary me-1"></i>Pit
                                                                    Domestik
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="weekly_pit_domestik"
                                                                        name="pit_domestik" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="card border">
                                                            <div class="card-body">
                                                                <label for="weekly_pit_produksi_step3" class="form-label">
                                                                    <i class="mdi mdi-factory me-1"></i>Pit Produksi Step 3
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control"
                                                                        id="weekly_pit_produksi_step3"
                                                                        name="pit_produksi_step3" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="card border">
                                                            <div class="card-body">
                                                                <label for="weekly_pit_storage" class="form-label">
                                                                    <i class="mdi mdi-archive me-1"></i>Pit Storage
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="weekly_pit_storage"
                                                                        name="pit_storage" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="card border">
                                                            <div class="card-body">
                                                                <label for="weekly_pit_proses_wwtp2" class="form-label">
                                                                    <i class="mdi mdi-water-treatment me-1"></i>Pit Proses
                                                                    WWTP 2
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="weekly_pit_proses_wwtp2"
                                                                        name="pit_proses_wwtp2" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="card border">
                                                            <div class="card-body">
                                                                <label for="weekly_pit_outlet" class="form-label">
                                                                    <i class="mdi mdi-pipe me-1"></i>Pit Outlet
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="weekly_pit_outlet"
                                                                        name="pit_outlet" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="card border">
                                                            <div class="card-body">
                                                                <label for="weekly_pit_boiler" class="form-label">
                                                                    <i class="mdi mdi-boiler me-1"></i>Pit Boiler
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="weekly_pit_boiler"
                                                                        name="pit_boiler" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="alert alert-info border-0 mt-3" role="alert">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0">
                                                        <i class="mdi mdi-information fs-4"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-2">
                                                        <strong>Catatan:</strong> Data influent hanya dapat diinput
                                                        <strong>1x per minggu</strong>.
                                                        Pastikan data yang diinput sudah benar.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Effluent Form -->
                                        <div id="weekly_effluent_form" style="display: none;">
                                            <div class="mb-4">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1 text-success">
                                                            <i class="mdi mdi-water-check me-2"></i>Data Effluent Mingguan
                                                        </h5>
                                                        <p class="text-muted mb-0">Input volume air keluar mingguan</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <button type="button" class="btn btn-sm btn-outline-success "
                                                            id="calculateEffluentTotal">
                                                            <i class="mdi mdi-calculator me-1"></i> Hitung Total
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="card border border-success">
                                                            <div class="card-body">
                                                                <label for="weekly_full_proses"
                                                                    class="form-label fw-semibold">
                                                                    <i
                                                                        class="mdi mdi-water-check text-success me-1"></i>Full
                                                                    Proses
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="weekly_full_proses"
                                                                        name="full_proses" min="0"
                                                                        placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                                <small class="form-text text-muted">Volume air hasil proses
                                                                    lengkap</small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="card border border-success">
                                                            <div class="card-body">
                                                                <label for="weekly_daf_pre"
                                                                    class="form-label fw-semibold">
                                                                    <i
                                                                        class="mdi mdi-water-check text-success me-1"></i>DAF
                                                                    Pre
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control" id="weekly_daf_pre"
                                                                        name="daf_pre" min="0" placeholder="0.00">
                                                                    <span class="input-group-text bg-light">m³</span>
                                                                </div>
                                                                <small class="form-text text-muted">Volume air hasil DAF
                                                                    preliminary</small>
                                                            </div>
                                                        </div>
                                                    </div>
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
                    <p class="text-muted mb-4" id="successMessage">Data WWTP telah berhasil disimpan ke sistem.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Tambah Data Lagi
                        </button>
                        <a href="{{ url('/wwtp/data_proses') }}" class="btn btn-primary">
                            Lihat Semua Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Form Selector Card Styles */
        .form-selector-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
            position: relative;
            overflow: hidden;
        }

        .form-selector-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-color: #405189;
        }

        .form-selector-card.active {
            border-color: #405189;
            background: linear-gradient(135deg, rgba(64, 81, 137, 0.05) 0%, rgba(64, 81, 137, 0.02) 100%);
            box-shadow: 0 8px 20px rgba(64, 81, 137, 0.15);
        }

        .form-selector-card.active .card-active-indicator {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #405189 0%, #0ab39c 100%);
        }

        .form-selector-card.active .badge {
            background-color: #405189 !important;
            color: white !important;
        }

        /* Avatar Styles */
        .avatar-xl {
            width: 6rem;
            height: 6rem;
        }

        .avatar-lg {
            width: 5rem;
            height: 5rem;
        }

        .avatar-title {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .bg-primary-subtle {
            background-color: rgba(64, 81, 137, 0.1) !important;
        }

        .bg-success-subtle {
            background-color: rgba(10, 179, 156, 0.1) !important;
        }

        .badge.bg-primary-subtle {
            background-color: rgba(64, 81, 137, 0.1) !important;
        }

        .badge.bg-success-subtle {
            background-color: rgba(10, 179, 156, 0.1) !important;
        }

        /* Animation on hover */
        .form-selector-card .avatar-title {
            transition: transform 0.3s ease;
        }

        .form-selector-card:hover .avatar-title {
            transform: scale(1.1) rotate(5deg);
        }

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

        .table-sm th,
        .table-sm td {
            padding: 0.75rem;
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

            // Show/hide weekly forms based on category
            $('#weekly_kategori').on('change', function() {
                const kategori = $(this).val();

                if (kategori === 'influent') {
                    $('#weekly_influent_form').slideDown();
                    $('#weekly_effluent_form').slideUp();

                    // Set required for main influent fields
                    $('#weekly_pit_sparta, #weekly_pit_garam, #weekly_pit_domestik').prop('required', true);
                    $('#weekly_full_proses, #weekly_daf_pre').prop('required', false);

                } else if (kategori === 'effluent') {
                    $('#weekly_influent_form').slideUp();
                    $('#weekly_effluent_form').slideDown();

                    // Set required for effluent fields
                    $('#weekly_pit_sparta, #weekly_pit_garam, #weekly_pit_domestik').prop('required',
                        false);
                    $('#weekly_full_proses, #weekly_daf_pre').prop('required', true);

                } else {
                    $('#weekly_influent_form, #weekly_effluent_form').slideUp();

                    // Reset required fields
                    $('#weekly_pit_sparta, #weekly_pit_garam, #weekly_pit_domestik, ' +
                        '#weekly_full_proses, #weekly_daf_pre').prop('required', false);
                }
            });

            // Submit daily form
            $('#dailyWwtpForm').on('submit', function(e) {
                e.preventDefault();

                const btnSubmit = $('#submitDailyForm');
                const originalText = btnSubmit.html();
                btnSubmit.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                const formData = {
                    _token: "{{ csrf_token() }}",
                    tanggal: $('#daily_tanggal').val(),
                    shift: $('#daily_shift').val(),
                    pit_sparta: $('#daily_pit_sparta').val(),
                    pit_garam: $('#daily_pit_garam').val(),
                    pit_domestik: $('#daily_pit_domestik').val(),
                    pit_produksi_step3: $('#daily_pit_produksi_step3').val() || null,
                    pit_storage: $('#daily_pit_storage').val() || null,
                    pit_proses_wwtp2: $('#daily_pit_proses_wwtp2').val() || null,
                    pit_outlet: $('#daily_pit_outlet').val() || null,
                    pit_boiler: $('#daily_pit_boiler').val() || null,
                    debit1: $('#daily_debit1').val() || null,
                    running_wwtp1: $('#daily_running_wwtp1').val() || null,
                    debit2: $('#daily_debit2').val() || null,
                    running_wwtp2: $('#daily_running_wwtp2').val() || null,
                    foreman_id: $('#daily_foreman_id').val() || null,
                    supervisor_id: $('#daily_supervisor_id').val() || null,
                };

                $.ajax({
                    url: "{{ url('wwtp/proses/influent-harian') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Show success modal
                        $('#successMessage').html(response.message);
                        $('#successModal').modal('show');

                        // Reset form
                        $('#dailyWwtpForm')[0].reset();
                        $('#daily_tanggal').val(today);
                        fetchPreviousData();

                        // Reload recent data if function exists
                        if (typeof loadRecentData === 'function') {
                            loadRecentData();
                        }
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

            // Submit weekly form
            $('#weeklyWwtpForm').on('submit', function(e) {
                e.preventDefault();

                const btnSubmit = $('#submitWeeklyForm');
                const originalText = btnSubmit.html();
                btnSubmit.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                const formData = {
                    tanggal: $('#weekly_tanggal').val(),
                    kategori: $('#weekly_kategori').val()
                };

                if (formData.kategori === 'influent') {
                    formData.pit_sparta = $('#weekly_pit_sparta').val();
                    formData.pit_garam = $('#weekly_pit_garam').val();
                    formData.pit_domestik = $('#weekly_pit_domestik').val();
                    formData.pit_produksi_step3 = $('#weekly_pit_produksi_step3').val() || null;
                    formData.pit_storage = $('#weekly_pit_storage').val() || null;
                    formData.pit_proses_wwtp2 = $('#weekly_pit_proses_wwtp2').val() || null;
                    formData.pit_outlet = $('#weekly_pit_outlet').val() || null;
                    formData.pit_boiler = $('#weekly_pit_boiler').val() || null;
                } else if (formData.kategori === 'effluent') {
                    formData.full_proses = $('#weekly_full_proses').val();
                    formData.daf_pre = $('#weekly_daf_pre').val();
                }

                $.ajax({
                    url: "{{ url('api/wwtp') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Show success modal
                        $('#successMessage').html(response.message);
                        $('#successModal').modal('show');

                        // Reset form
                        $('#weeklyWwtpForm')[0].reset();
                        $('#weekly_tanggal').val(today);
                        $('#weekly_influent_form, #weekly_effluent_form').hide();

                        // Reset calculations
                        $('#weekly_influent_total').text('0.00');

                        // Reload recent data if function exists
                        if (typeof loadRecentData === 'function') {
                            loadRecentData();
                        }
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

            function fetchPreviousData() {
                const tanggal = $('#daily_tanggal').val();
                const shift = $('#daily_shift').val();

                if (!tanggal || !shift) {
                    // Clear the _awal fields if date/shift not selected
                    $('[id$="_awal"]').val('0.00');
                    return;
                }

                $.ajax({
                    url: "{{ url('api/wwtp/influent-harian/previous-data') }}",
                    method: 'GET',
                    data: {
                        tanggal: tanggal,
                        shift: shift
                    },
                    success: function(response) {
                        $('#daily_pit_sparta_awal').val(response.pit_sparta_awal);
                        $('#daily_pit_garam_awal').val(response.pit_garam_awal);
                        $('#daily_pit_domestik_awal').val(response.daily_pit_domestik_awal || response
                            .pit_domestik_awal);
                        $('#daily_pit_produksi_step3_awal').val(response.pit_produksi_step3_awal);
                        $('#daily_pit_storage_awal').val(response.pit_storage_awal);
                        $('#daily_pit_proses_wwtp2_awal').val(response.pit_proses_wwtp2_awal);
                        $('#daily_pit_outlet_awal').val(response.pit_outlet_awal);
                        $('#daily_pit_boiler_awal').val(response.pit_boiler_awal);
                    },
                    error: function(xhr) {
                        console.error('Gagal mengambil data awal:', xhr);
                    }
                });
            }

            function checkDailyApproval() {
                const tanggal = $('#daily_tanggal').val();
                if (!tanggal) {
                    $('#daily_approval_row').hide();
                    $('#daily_foreman_id, #daily_supervisor_id').val('').prop('required', false);
                    $('#submitDailyForm').prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: "{{ url('wwtp-approval/check') }}",
                    method: 'GET',
                    data: {
                        tanggal: tanggal
                    },
                    success: function(response) {
                        if (response.approval_exists) {
                            $('#daily_approval_row').hide();
                            $('#daily_foreman_id, #daily_supervisor_id').val('').prop('required',
                                false);

                            // If status is approved_foreman or approved_supervisor, lock editing
                            // if (response.approval.status === 'approved_foreman' || response.approval
                            //     .status === 'approved_supervisor') {
                            //     Swal.fire({
                            //         icon: 'warning',
                            //         title: 'Peringatan',
                            //         text: 'Laporan harian untuk tanggal ini sudah disetujui dan terkunci.',
                            //         confirmButtonColor: '#3085d6'
                            //     });
                            //     $('#submitDailyForm').prop('disabled', true);
                            // } else {
                            //     $('#submitDailyForm').prop('disabled', false);
                            // }
                        } else {
                            $('#submitDailyForm').prop('disabled', false);
                            $('#daily_approval_row').show();
                            $('#daily_foreman_id, #daily_supervisor_id').prop('required', true);

                            // Populate foremen dropdown
                            const foremanSelect = $('#daily_foreman_id');
                            foremanSelect.html('<option value="">-- Pilih Foreman --</option>');
                            response.foremen.forEach(function(u) {
                                foremanSelect.append(
                                    `<option value="${u.id}">${u.username}</option>`);
                            });

                            // Populate supervisors dropdown
                            const supervisorSelect = $('#daily_supervisor_id');
                            supervisorSelect.html('<option value="">-- Pilih Supervisor --</option>');
                            response.supervisors.forEach(function(u) {
                                supervisorSelect.append(
                                    `<option value="${u.id}">${u.username}</option>`);
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Gagal mengecek status approval harian:', xhr);
                    }
                });
            }

            $(document).on('wheel', 'input[type=number]', function(e) {
                $(this).blur();
            });

            // Trigger on date or shift change
            $('#daily_tanggal, #daily_shift').on('change', fetchPreviousData);
            $('#daily_tanggal').on('change', checkDailyApproval);

            // Run check on load
            checkDailyApproval();

        });
    </script>
@endsection
