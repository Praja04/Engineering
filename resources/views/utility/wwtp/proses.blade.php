@extends('layouts.app')

@section('title', 'WWTP Management')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">WWTP Management</h4>
                        <p class="text-muted mb-0">Wastewater Treatment Plant Data Monitoring</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 1: Statistics & Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <!-- Statistics Row -->
                        <div class="row g-0 border-bottom">
                            <div class="col-xl-3 col-md-6">
                                <div class="p-4 border-end">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-2">Total Records</p>
                                            <h4 class="mb-0" id="totalRecords">0</h4>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm">
                                                <div class="avatar-title bg-soft-primary text-primary rounded fs-3">
                                                    <i class="mdi mdi-database"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="p-4 border-end">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-2">Influent Records</p>
                                            <h4 class="mb-0" id="totalInfluent">0</h4>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm">
                                                <div class="avatar-title bg-soft-info text-info rounded fs-3">
                                                    <i class="mdi mdi-water-pump"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="p-4 border-end">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-2">Effluent Records</p>
                                            <h4 class="mb-0" id="totalEffluent">0</h4>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm">
                                                <div class="avatar-title bg-soft-success text-success rounded fs-3">
                                                    <i class="mdi mdi-water-check"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-2">This Week</p>
                                            <h4 class="mb-0" id="weekRecords">0</h4>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm">
                                                <div class="avatar-title bg-soft-warning text-warning rounded fs-3">
                                                    <i class="mdi mdi-calendar-week"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions Row -->
                        <div class="p-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1">Quick Actions</h6>
                                    <p class="text-muted mb-0 small">Tambahkan data WWTP sesuai kebutuhan</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFormHarian">
                                            <i class="mdi mdi-calendar-today me-1"></i> Data Harian
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalForm">
                                            <i class="mdi mdi-calendar-week me-1"></i> Data Mingguan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Main Content (Filter & Table) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Filter Section -->
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Data WWTP</h5>
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-soft-info btn-sm" id="btnExport">
                                    <i class="mdi mdi-file-export me-1"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Filter Row -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Filter Kategori</label>
                                        <select id="filterKategori" class="form-select">
                                            <option value="">Semua Kategori</option>
                                            <option value="influent">Influent</option>
                                            <option value="effluent">Effluent</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Filter Bulan</label>
                                        <input type="month" id="filterMonth" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Cari Data</label>
                                        <input type="text" id="searchInput" class="form-control" placeholder="Cari...">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="button" id="btnReset" class="btn btn-outline-secondary w-100">
                                            <i class="mdi mdi-refresh me-1"></i> Reset Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Section -->
                        <div class="table-responsive">
                            <table id="wwtpTable" class="table table-hover table-striped align-middle table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">No</th>
                                        <th>Tanggal</th>
                                        <th class="text-center" style="width: 120px;">Kategori</th>
                                        <th>Detail Data</th>
                                        <th class="text-center" style="width: 120px;">Total Volume</th>
                                        <th class="text-center" style="width: 150px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalFormLabel">
                    <i class="mdi mdi-file-document-edit me-2"></i>Tambah Data WWTP Mingguan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="wwtpForm">
                <div class="modal-body">
                    <input type="hidden" id="recordId" name="recordId">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label fw-semibold">
                                Tanggal <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            <div class="form-text">Pilih tanggal pencatatan data</div>
                        </div>
                        <div class="col-md-6">
                            <label for="kategori" class="form-label fw-semibold">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="kategori" name="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="influent">Influent (Air Masuk)</option>
                                <option value="effluent">Effluent (Air Keluar)</option>
                            </select>
                            <div class="form-text">Jenis kategori pengolahan</div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Form Influent -->
                    <div id="formInfluent" style="display: none;">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-xs">
                                        <div class="avatar-title bg-primary rounded-circle">
                                            <i class="mdi mdi-water-pump"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Data Influent</h6>
                                    <p class="text-muted mb-0 small">Input volume air masuk dari berbagai sumber</p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="pit_sparta" class="form-label">
                                    <i class="mdi mdi-water text-primary"></i> Pit Sparta
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_sparta" name="pit_sparta" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_garam" class="form-label">
                                    <i class="mdi mdi-water text-primary"></i> Pit Garam
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_garam" name="pit_garam" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_domestik" class="form-label">
                                    <i class="mdi mdi-water text-primary"></i> Pit Domestik
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_domestik" name="pit_domestik" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_produksi_step3" class="form-label">
                                    <i class="mdi mdi-water text-primary"></i> Pit Produksi Step 3 ke Equal 1
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_produksi_step3" name="pit_produksi_step3" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_storage" class="form-label">
                                    <i class="mdi mdi-water text-primary"></i> Pit Storage
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_storage" name="pit_storage" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_proses_wwtp2" class="form-label">
                                    <i class="mdi mdi-water text-primary"></i> Pit Proses WWTP 2
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_proses_wwtp2" name="pit_proses_wwtp2" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_outlet" class="form-label">
                                    <i class="mdi mdi-water text-primary"></i> Pit Outlet
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_outlet" name="pit_outlet" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_boiler" class="form-label">
                                    <i class="mdi mdi-water text-primary"></i> Pit Boiler
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_boiler" name="pit_boiler" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 mt-3" role="alert">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="mdi mdi-information fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <strong>Catatan:</strong> Data influent hanya dapat diinput <strong>1x per minggu</strong>.
                                    Pastikan data yang diinput sudah benar.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Effluent -->
                    <div id="formEffluent" style="display: none;">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-xs">
                                        <div class="avatar-title bg-success rounded-circle">
                                            <i class="mdi mdi-water-check"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Data Effluent</h6>
                                    <p class="text-muted mb-0 small">Input volume air keluar setelah proses</p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="full_proses" class="form-label">
                                    <i class="mdi mdi-water-check text-success"></i> Full Proses
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="full_proses" name="full_proses" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                                <div class="form-text">Volume air hasil proses lengkap</div>
                            </div>
                            <div class="col-md-6">
                                <label for="daf_pre" class="form-label">
                                    <i class="mdi mdi-water-check text-success"></i> DAF Pre
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="daf_pre" name="daf_pre" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                                <div class="form-text">Volume air hasil DAF preliminary</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="mdi mdi-content-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="mdi mdi-eye me-2"></i>Detail Data WWTP
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Harian -->
<div class="modal fade" id="modalFormHarian" tabindex="-1" aria-labelledby="modalFormHarianLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalFormHarianLabel">
                    <i class="mdi mdi-calendar-today me-2"></i>Tambah Data WWTP Harian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="wwtpFormHarian">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggalHarian" class="form-label fw-semibold">
                                Tanggal <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="tanggalHarian" name="tanggalHarian" required>
                            <div class="form-text">Pilih tanggal pencatatan data harian</div>
                        </div>
                        <div class="col-md-6">
                            <label for="shiftHarian" class="form-label fw-semibold">
                                Shift <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="shiftHarian" name="shiftHarian" required>
                                <option value="">-- Pilih Shift --</option>
                                <option value="shift1">Shift 1</option>
                                <option value="shift2">Shift 2</option>
                                <option value="shift3">Shift 3</option>
                            </select>
                            <div class="form-text">Pilih shift kerja</div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Form Influent Harian -->
                    <div id="formInfluentHarian">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-xs">
                                        <div class="avatar-title bg-success rounded-circle">
                                            <i class="mdi mdi-water-pump"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Data Influent Harian</h6>
                                    <p class="text-muted mb-0 small">Input volume air masuk harian dari berbagai sumber</p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="pit_sparta_harian" class="form-label">
                                    <i class="mdi mdi-water text-success"></i> Pit Sparta <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_sparta_harian" name="pit_sparta_harian" min="0" placeholder="0.00" required>
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_garam_harian" class="form-label">
                                    <i class="mdi mdi-water text-success"></i> Pit Garam <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_garam_harian" name="pit_garam_harian" min="0" placeholder="0.00" required>
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_domestik_harian" class="form-label">
                                    <i class="mdi mdi-water text-success"></i> Pit Domestik <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_domestik_harian" name="pit_domestik_harian" min="0" placeholder="0.00" required>
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_produksi_step3_harian" class="form-label">
                                    <i class="mdi mdi-water text-success"></i> Pit Produksi Step 3 ke Equal 1
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_produksi_step3_harian" name="pit_produksi_step3_harian" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_storage_harian" class="form-label">
                                    <i class="mdi mdi-water text-success"></i> Pit Storage
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_storage_harian" name="pit_storage_harian" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_proses_wwtp2_harian" class="form-label">
                                    <i class="mdi mdi-water text-success"></i> Pit Proses WWTP 2
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_proses_wwtp2_harian" name="pit_proses_wwtp2_harian" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_outlet_harian" class="form-label">
                                    <i class="mdi mdi-water text-success"></i> Pit Outlet
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_outlet_harian" name="pit_outlet_harian" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pit_boiler_harian" class="form-label">
                                    <i class="mdi mdi-water text-success"></i> Pit Boiler
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="pit_boiler_harian" name="pit_boiler_harian" min="0" placeholder="0.00">
                                    <span class="input-group-text">m³</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success border-0 mt-3" role="alert">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="mdi mdi-information fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <strong>Info:</strong> Data influent harian dapat diinput <strong>setiap hari per shift</strong> untuk monitoring berkelanjutan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success" id="btnSubmitHarian">
                        <i class="mdi mdi-content-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<link href="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    .card-animate {
        transition: all 0.3s ease;
    }

    .card-animate:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
    }

    .bg-soft-primary {
        background-color: rgba(64, 81, 137, 0.1);
    }

    .bg-soft-info {
        background-color: rgba(41, 156, 219, 0.1);
    }

    .bg-soft-success {
        background-color: rgba(10, 179, 156, 0.1);
    }

    .bg-soft-warning {
        background-color: rgba(243, 156, 18, 0.1);
    }

    .bg-soft-secondary {
        background-color: rgba(108, 117, 125, 0.1);
    }

    .table> :not(caption)>*>* {
        padding: 0.75rem 0.75rem;
    }

    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.75rem;
    }

    /* Custom border for the first card */
    .card:first-child {
        border: 1px solid #e9ecef;
        border-radius: 0.75rem;
        overflow: hidden;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .border-end {
            border-right: none !important;
            border-bottom: 1px solid #e9ecef;
        }

        .p-4 {
            padding: 1.5rem !important;
        }
    }
</style>
<script src="{{ asset('material/assets/libs/datatables.net/datatables.min.js') }}"></script>
<script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<!-- JavaScript tetap sama seperti sebelumnya -->
<script>
    $(document).ready(function() {
        let table;
        let isEdit = false;
        let allData = [];

        // Initialize DataTable
        function initDataTable() {
            table = $('#wwtpTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/api/wwtp',
                    dataSrc: function(json) {
                        allData = json;
                        updateStatistics(json);
                        return json;
                    }
                },
                columns: [{
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return `<span class="badge badge-soft-secondary">${meta.row + 1}</span>`;
                        }
                    },
                    {
                        data: 'tanggal',
                        render: function(data) {
                            const date = new Date(data);
                            const options = {
                                weekday: 'short',
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            };
                            return `
                        <div class="d-flex flex-column">
                            <span class="fw-semibold">${date.toLocaleDateString('id-ID', options)}</span>
                            <small class="text-muted">${getWeekNumber(date)} Minggu ke-${getWeekOfMonth(date)}</small>
                        </div>
                    `;
                        }
                    },
                    {
                        data: 'kategori',
                        className: 'text-center',
                        render: function(data) {
                            if (data === 'influent') {
                                return `<span class="badge bg-primary"><i class="mdi mdi-water-pump me-1"></i>INFLUENT</span>`;
                            } else {
                                return `<span class="badge bg-success"><i class="mdi mdi-water-check me-1"></i>EFFLUENT</span>`;
                            }
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            if (data.kategori === 'influent' && data.influent) {
                                return `
                            <div class="d-flex flex-column gap-1">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Pit Sparta:</span>
                                    <span class="fw-semibold">${data.influent.pit_sparta != null ? parseFloat(data.influent.pit_sparta).toFixed(2) + ' m³' : '-'}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Pit Garam:</span>
                                    <span class="fw-semibold">${data.influent.pit_garam != null ? parseFloat(data.influent.pit_garam).toFixed(2) + ' m³' : '-'}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Pit Domestik:</span>
                                    <span class="fw-semibold">${data.influent.pit_domestik != null ? parseFloat(data.influent.pit_domestik).toFixed(2) + ' m³' : '-'}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Pit Produksi Step 3 ke Equal 1:</span>
                                    <span class="fw-semibold">${data.influent.pit_produksi_step3 != null ? parseFloat(data.influent.pit_produksi_step3).toFixed(2) + ' m³' : '-'}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Pit Storage:</span>
                                    <span class="fw-semibold">${data.influent.pit_storage != null ? parseFloat(data.influent.pit_storage).toFixed(2) + ' m³' : '-'}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Pit Proses WWTP 2:</span>
                                    <span class="fw-semibold">${data.influent.pit_proses_wwtp2 != null ? parseFloat(data.influent.pit_proses_wwtp2).toFixed(2) + ' m³' : '-'}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Pit Outlet:</span>
                                    <span class="fw-semibold">${data.influent.pit_outlet != null ? parseFloat(data.influent.pit_outlet).toFixed(2) + ' m³' : '-'}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Pit Boiler:</span>
                                    <span class="fw-semibold">${data.influent.pit_boiler != null ? parseFloat(data.influent.pit_boiler).toFixed(2) + ' m³' : '-'}</span>
                                </div>
                            </div>
                        `;
                            } else if (data.kategori === 'effluent' && data.effluent) {
                                return `
                            <div class="d-flex flex-column gap-1">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Full Proses:</span>
                                    <span class="fw-semibold">${data.effluent.full_proses != null ? parseFloat(data.effluent.full_proses).toFixed(2) + ' m³' : '-'}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">DAF Pre:</span>
                                    <span class="fw-semibold">${data.effluent.daf_pre != null ? parseFloat(data.effluent.daf_pre).toFixed(2) + ' m³' : '-'}</span>
                                </div>
                            </div>
                        `;
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data) {
                            let total = 0;
                            if (data.kategori === 'influent' && data.influent) {
                                total = (parseFloat(data.influent.pit_sparta) || 0) +
                                    (parseFloat(data.influent.pit_garam) || 0) +
                                    (parseFloat(data.influent.pit_domestik) || 0);
                            } else if (data.kategori === 'effluent' && data.effluent) {
                                total = (parseFloat(data.effluent.full_proses) || 0) +
                                    (parseFloat(data.effluent.daf_pre) || 0);
                            }
                            return `
                        <div class="text-center">
                            <h5 class="mb-0 text-primary">${total.toFixed(2)}</h5>
                            <small class="text-muted">m³</small>
                        </div>
                    `;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data) {
                            return `
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-sm btn-soft-info btn-detail" data-id="${data.id}" title="Detail">
                                <i class="mdi mdi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-soft-warning btn-edit" data-id="${data.id}" title="Edit">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-soft-danger btn-delete" data-id="${data.id}" title="Hapus">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    `;
                        }
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari data...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang cocok",
                    emptyTable: "Tidak ada data tersedia",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                responsive: true
            });
        }

        // Helper functions
        function getWeekNumber(date) {
            const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
            const pastDaysOfYear = (date - firstDayOfYear) / 86400000;
            return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
        }

        function getWeekOfMonth(date) {
            const firstDayOfMonth = new Date(date.getFullYear(), date.getMonth(), 1);
            const firstDayWeekday = firstDayOfMonth.getDay();
            const offsetDate = date.getDate() + firstDayWeekday - 1;
            return Math.floor(offsetDate / 7) + 1;
        }

        // Update statistics
        function updateStatistics(data) {
            const total = data.length;
            const influent = data.filter(d => d.kategori === 'influent').length;
            const effluent = data.filter(d => d.kategori === 'effluent').length;

            // Get current week data
            const now = new Date();
            const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
            const endOfWeek = new Date(now.setDate(now.getDate() - now.getDay() + 6));

            const weekData = data.filter(d => {
                const date = new Date(d.tanggal);
                return date >= startOfWeek && date <= endOfWeek;
            }).length;

            $('#totalRecords').text(total);
            $('#totalInfluent').text(influent);
            $('#totalEffluent').text(effluent);
            $('#weekRecords').text(weekData);
        }

        initDataTable();

        // Filter by kategori
        $('#filterKategori').on('change', function() {
            const value = $(this).val();
            table.column(2).search(value ? value.toUpperCase() : '').draw();
        });

        // Filter by month
        $('#filterMonth').on('change', function() {
            const value = $(this).val();

            // Remove existing month filter if any
            $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
                return fn.name !== 'monthFilter';
            });

            if (value) {
                const monthFilter = function(settings, data, dataIndex) {
                    const rowData = allData[dataIndex];
                    const date = new Date(rowData.tanggal + 'T00:00:00');
                    const [selectedYear, selectedMonth] = value.split('-');

                    return date.getMonth() === (parseInt(selectedMonth) - 1) &&
                        date.getFullYear() === parseInt(selectedYear);
                };
                monthFilter.name = 'monthFilter';
                $.fn.dataTable.ext.search.push(monthFilter);
            }

            table.draw();
        });

        // Custom search
        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Reset filters
        $('#btnReset').on('click', function() {
            $('#filterKategori').val('');
            $('#filterMonth').val('');
            $('#searchInput').val('');
            $.fn.dataTable.ext.search = [];
            table.search('').columns().search('').draw();
        });

        // Show/hide form based on kategori
        $('#kategori').on('change', function() {
            const kategori = $(this).val();

            if (kategori === 'influent') {
                $('#formInfluent').slideDown();
                $('#formEffluent').slideUp();

                $('#pit_sparta, #pit_garam, #pit_domestik').prop('required', true);
                $('#full_proses, #daf_pre').prop('required', false);
            } else if (kategori === 'effluent') {
                $('#formInfluent').slideUp();
                $('#formEffluent').slideDown();

                $('#pit_sparta, #pit_garam, #pit_domestik').prop('required', false);
                $('#full_proses, #daf_pre').prop('required', true);
            } else {
                $('#formInfluent, #formEffluent').slideUp();
            }
        });

        // Reset modal when closed
        $('#modalForm').on('hidden.bs.modal', function() {
            resetForm();
        });

        $('#modalFormHarian').on('hidden.bs.modal', function() {
            resetFormHarian();
        });

        // Submit form
        $('#wwtpForm').on('submit', function(e) {
            e.preventDefault();

            const btnSubmit = $('#btnSubmit');
            const originalText = btnSubmit.html();
            btnSubmit.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

            const formData = {
                tanggal: $('#tanggal').val(),
                kategori: $('#kategori').val()
            };

            if (formData.kategori === 'influent') {
                formData.pit_sparta = $('#pit_sparta').val();
                formData.pit_garam = $('#pit_garam').val();
                formData.pit_domestik = $('#pit_domestik').val();
                formData.pit_produksi_step3 = $('#pit_produksi_step3').val();
                formData.pit_storage = $('#pit_storage').val();
                formData.pit_proses_wwtp2 = $('#pit_proses_wwtp2').val();
                formData.pit_outlet = $('#pit_outlet').val();
                formData.pit_boiler = $('#pit_boiler').val();
            } else if (formData.kategori === 'effluent') {
                formData.full_proses = $('#full_proses').val();
                formData.daf_pre = $('#daf_pre').val();
            }

            const url = isEdit ? `/api/wwtp/${$('#recordId').val()}` : '/api/wwtp';
            const method = isEdit ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    $('#modalForm').modal('hide');
                    table.ajax.reload(null, false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    let message = 'Terjadi kesalahan!';

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

        $('#wwtpFormHarian').on('submit', function(e) {
            e.preventDefault();

            const btnSubmit = $('#btnSubmitHarian');
            const originalText = btnSubmit.html();
            btnSubmit.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

            const formData = {
                tanggal: $('#tanggalHarian').val(),
                shift: $('#shiftHarian').val(),
                pit_sparta: $('#pit_sparta_harian').val(),
                pit_garam: $('#pit_garam_harian').val(),
                pit_domestik: $('#pit_domestik_harian').val(),
                pit_produksi_step3: $('#pit_produksi_step3_harian').val() || null,
                pit_storage: $('#pit_storage_harian').val() || null,
                pit_proses_wwtp2: $('#pit_proses_wwtp2_harian').val() || null,
                pit_outlet: $('#pit_outlet_harian').val() || null,
                pit_boiler: $('#pit_boiler_harian').val() || null
            };

            $.ajax({
                url: "{{url('api/wwtp/influent-harian')}}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#modalFormHarian').modal('hide');

                    if (typeof table !== 'undefined') {
                        table.ajax.reload(null, false);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    let message = 'Terjadi kesalahan!';

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

        // Detail button
        $(document).on('click', '.btn-detail', function() {
            const id = $(this).data('id');

            $.ajax({
                url: `/api/wwtp/${id}`,
                method: 'GET',
                beforeSend: function() {
                    $('#detailContent').html(`
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `);
                    $('#modalDetail').modal('show');
                },
                success: function(data) {
                    let content = `
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-soft-primary text-primary rounded">
                                                        <i class="mdi mdi-calendar fs-4"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="text-muted mb-1">Tanggal</p>
                                                <h6 class="mb-0">${new Date(data.tanggal).toLocaleDateString('id-ID', {
                                                    weekday: 'long',
                                                    day: '2-digit',
                                                    month: 'long',
                                                    year: 'numeric'
                                                })}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-soft-${data.kategori === 'influent' ? 'primary' : 'success'} text-${data.kategori === 'influent' ? 'primary' : 'success'} rounded">
                                                        <i class="mdi mdi-${data.kategori === 'influent' ? 'water-pump' : 'water-check'} fs-4"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="text-muted mb-1">Kategori</p>
                                                <h6 class="mb-0">
                                                    <span class="badge bg-${data.kategori === 'influent' ? 'primary' : 'success'}">
                                                        ${data.kategori.toUpperCase()}
                                                    </span>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">
                    `;

                    if (data.kategori === 'influent' && data.influent) {
                        const total = parseFloat(data.influent.pit_sparta) +
                            parseFloat(data.influent.pit_garam) +
                            parseFloat(data.influent.pit_domestik);

                        content += `
                            <h6 class="mb-3 text-primary">
                                <i class="mdi mdi-water-pump me-2"></i>Detail Influent
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted" width="200">
                                                <i class="mdi mdi-water text-primary me-2"></i>Pit Sparta
                                            </td>
                                            <td class="text-end fw-semibold">${parseFloat(data.influent.pit_sparta).toFixed(2)} m³</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="mdi mdi-water text-primary me-2"></i>Pit Garam
                                            </td>
                                            <td class="text-end fw-semibold">${parseFloat(data.influent.pit_garam).toFixed(2)} m³</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="mdi mdi-water text-primary me-2"></i>Pit Domestik
                                            </td>
                                            <td class="text-end fw-semibold">${parseFloat(data.influent.pit_domestik).toFixed(2)} m³</td>
                                        </tr>
                                        <tr class="table-light">
                                            <td class="fw-bold text-primary">
                                                <i class="mdi mdi-calculator me-2"></i>Total Volume
                                            </td>
                                            <td class="text-end">
                                                <h5 class="mb-0 text-primary">${total.toFixed(2)} m³</h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        `;
                    } else if (data.kategori === 'effluent' && data.effluent) {
                        const total = parseFloat(data.effluent.full_proses) +
                            parseFloat(data.effluent.daf_pre);

                        content += `
                            <h6 class="mb-3 text-success">
                                <i class="mdi mdi-water-check me-2"></i>Detail Effluent
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted" width="200">
                                                <i class="mdi mdi-water-check text-success me-2"></i>Full Proses
                                            </td>
                                            <td class="text-end fw-semibold">${parseFloat(data.effluent.full_proses).toFixed(2)} m³</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="mdi mdi-water-check text-success me-2"></i>DAF Pre
                                            </td>
                                            <td class="text-end fw-semibold">${parseFloat(data.effluent.daf_pre).toFixed(2)} m³</td>
                                        </tr>
                                        <tr class="table-light">
                                            <td class="fw-bold text-success">
                                                <i class="mdi mdi-calculator me-2"></i>Total Volume
                                            </td>
                                            <td class="text-end">
                                                <h5 class="mb-0 text-success">${total.toFixed(2)} m³</h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }

                    content += `
                            </div>
                        </div>
                    `;

                    $('#detailContent').html(content);
                },
                error: function() {
                    $('#detailContent').html(`
                        <div class="alert alert-danger" role="alert">
                            <i class="mdi mdi-alert-circle me-2"></i>
                            Gagal memuat data. Silakan coba lagi.
                        </div>
                    `);
                }
            });
        });

        // Edit button
        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            isEdit = true;

            $.ajax({
                url: `/api/wwtp/${id}`,
                method: 'GET',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memuat data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(data) {
                    Swal.close();

                    $('#recordId').val(data.id);
                    $('#tanggal').val(data.tanggal);
                    $('#kategori').val(data.kategori).trigger('change');

                    if (data.kategori === 'influent' && data.influent) {
                        $('#pit_sparta').val(data.influent.pit_sparta);
                        $('#pit_garam').val(data.influent.pit_garam);
                        $('#pit_domestik').val(data.influent.pit_domestik);
                    } else if (data.kategori === 'effluent' && data.effluent) {
                        $('#full_proses').val(data.effluent.full_proses);
                        $('#daf_pre').val(data.effluent.daf_pre);
                    }

                    $('#modalFormLabel').html('<i class="mdi mdi-pencil me-2"></i>Edit Data WWTP');
                    $('#kategori').prop('disabled', true);
                    $('#modalForm').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal memuat data untuk diedit'
                    });
                }
            });
        });

        // Delete button
        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: '<p class="mb-0">Apakah Anda yakin ingin menghapus data ini?</p><small class="text-muted">Data yang dihapus tidak dapat dikembalikan</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="mdi mdi-delete me-1"></i> Ya, Hapus!',
                cancelButtonText: '<i class="mdi mdi-close me-1"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/wwtp/${id}`,
                        method: 'DELETE',
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Menghapus...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            table.ajax.reload(null, false);

                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal menghapus data'
                            });
                        }
                    });
                }
            });
        });

        // Export button
        $('#btnExport').on('click', function() {
            Swal.fire({
                title: 'Export Data',
                text: 'Fitur export akan segera tersedia',
                icon: 'info'
            });
        });

        function resetForm() {
            isEdit = false;
            $('#wwtpForm')[0].reset();
            $('#recordId').val('');
            $('#formInfluent, #formEffluent').hide();
            $('#kategori').prop('disabled', false);
            $('#modalFormLabel').html('<i class="mdi mdi-file-document-edit me-2"></i>Tambah Data WWTP');

            // Reset validation
            $('#pit_sparta, #pit_garam, #pit_domestik, #full_proses, #daf_pre').prop('required', false);
        }

        function resetFormHarian() {
            $('#wwtpFormHarian')[0].reset();
            $('#modalFormHarianLabel').html('<i class="mdi mdi-calendar-today me-2"></i>Tambah Data WWTP Harian');
        }
    });
</script>

@endsection