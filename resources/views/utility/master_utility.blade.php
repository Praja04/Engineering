@extends('layouts.app')

@section('title', 'Master Data Utility')

@section('content')
    @php
        $canManageMaster = (Auth::user()->jabatan ?? '') !== 'operator';
    @endphp

    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title & Header -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1 fw-bold text-primary">Master Data Management Utility</h4>
                            <p class="text-muted mb-0">Kelola master Panel Listrik, Jenis Pemakaian Air, serta Area & Jenis
                                Pemakaian Chemical.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ url('utility/form') }}" class="btn btn-outline-primary">
                                <i class="mdi mdi-file-document-edit-outline me-1"></i> Form Utility
                            </a>
                            <a href="{{ url('utility/data') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-database-eye-outline me-1"></i> Data Utility
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metric Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Panel Listrik</p>
                                    <h3 class="mb-0 fw-bold" id="metricListrik">{{ $totalPanels ?? 0 }}</h3>
                                    <small class="text-muted">Master panel aktif</small>
                                </div>
                                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                                    <i class="mdi mdi-flash fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Jenis Pemakaian Air</p>
                                    <h3 class="mb-0 fw-bold" id="metricAir">{{ $totalAirAreas ?? 0 }}</h3>
                                    <small class="text-muted">Area / titik air</small>
                                </div>
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                    <i class="mdi mdi-water fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Area Chemical</p>
                                    <h3 class="mb-0 fw-bold" id="metricChemArea">{{ $totalChemAreas ?? 0 }}</h3>
                                    <small class="text-muted">Boiler, WWTP, dll</small>
                                </div>
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                                    <i class="mdi mdi-flask-round-bottom-outline fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Jenis Chemical</p>
                                    <h3 class="mb-0 fw-bold" id="metricChemType">{{ $totalChemTypes ?? 0 }}</h3>
                                    <small class="text-muted">SRTF, SCF, PAC, dll</small>
                                </div>
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                                    <i class="mdi mdi-flask-outline fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Master Nav Tabs -->
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom p-3">
                    <ul class="nav nav-pills nav-justified" id="masterUtilityTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-semibold py-2" id="tab-listrik-btn" data-bs-toggle="pill"
                                data-bs-target="#pane-listrik" type="button" role="tab">
                                <i class="mdi mdi-flash me-1"></i> 1. Panel Listrik
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold py-2" id="tab-air-btn" data-bs-toggle="pill"
                                data-bs-target="#pane-air" type="button" role="tab">
                                <i class="mdi mdi-water me-1"></i> 2. Pemakaian Air (Jenis Pemakaian)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold py-2" id="tab-chemical-btn" data-bs-toggle="pill"
                                data-bs-target="#pane-chemical" type="button" role="tab">
                                <i class="mdi mdi-flask-outline me-1"></i> 3. Chemical (Area & Jenis Pemakaian)
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="masterUtilityTabContent">

                        <!-- ============================================== -->
                        <!-- TAB 1: MASTER LISTRIK PANEL                    -->
                        <!-- ============================================== -->
                        <div class="tab-pane fade show active" id="pane-listrik" role="tabpanel">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                <div>
                                    <h5 class="fw-bold mb-1">Daftar Panel Listrik</h5>
                                    <p class="text-muted small mb-0">Kelola panel type yang muncul pada dropdown Form
                                        Pemakaian Listrik (MDP, SDP1, dsb).</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRefreshListrik">
                                        <i class="mdi mdi-refresh me-1"></i> Refresh
                                    </button>
                                    @if ($canManageMaster)
                                        <button type="button" class="btn btn-warning btn-sm" id="btnAddListrik">
                                            <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Panel Listrik
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-6 col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-magnify"></i></span>
                                        <input type="text" id="searchListrik" class="form-control"
                                            placeholder="Cari nama panel...">
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%;" class="text-center">No</th>
                                            <th style="width: 15%;">Nama Panel</th>
                                            <th style="width: 20%;">Deskripsi</th>
                                            <th style="width: 10%;" class="text-center">Urutan</th>
                                            <th style="width: 12%;" class="text-center">Status</th>
                                            <th style="width: 15%;" class="text-center">Riwayat Transaksi</th>
                                            <th style="width: 15%;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listrikTableBody">
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status"></div>
                                                <div class="text-muted mt-2">Memuat data panel listrik...</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- TAB 2: MASTER PEMAKAIAN AIR                   -->
                        <!-- ============================================== -->
                        <div class="tab-pane fade" id="pane-air" role="tabpanel">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                <div>
                                    <h5 class="fw-bold mb-1">Daftar Jenis Pemakaian Air</h5>
                                    <p class="text-muted small mb-0">Kelola jenis pemakaian air (area/titik) yang muncul
                                        saat pengisian pemakaian air harian.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRefreshAir">
                                        <i class="mdi mdi-refresh me-1"></i> Refresh
                                    </button>
                                    @if ($canManageMaster)
                                        <button type="button" class="btn btn-primary btn-sm" id="btnAddAir">
                                            <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Jenis Pemakaian Air
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-6 col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-magnify"></i></span>
                                        <input type="text" id="searchAir" class="form-control"
                                            placeholder="Cari jenis pemakaian air...">
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%;" class="text-center">No</th>
                                            <th style="width: 25%;">Jenis Pemakaian / Area</th>
                                            <th style="width: 25%;">Deskripsi / Catatan</th>
                                            <th style="width: 12%;" class="text-center">Status</th>
                                            <th style="width: 15%;" class="text-center">Riwayat Transaksi</th>
                                            <th style="width: 18%;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="airTableBody">
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status"></div>
                                                <div class="text-muted mt-2">Memuat data pemakaian air...</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- TAB 3: MASTER CHEMICAL (AREA & JENIS)          -->
                        <!-- ============================================== -->
                        <div class="tab-pane fade" id="pane-chemical" role="tabpanel">
                            <div class="row g-4">
                                <!-- Bagian 1: Master Area Chemical -->
                                <div class="col-lg-5 col-md-12">
                                    <div class="card border border-light-subtle shadow-none h-100">
                                        <div
                                            class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                                            <div>
                                                <h6 class="fw-bold mb-0 text-success"><i
                                                        class="mdi mdi-map-marker-outline me-1"></i> Area Chemical
                                                </h6>
                                                <small class="text-muted">Boiler, WWTP, Utility, dll</small>
                                            </div>
                                            @if ($canManageMaster)
                                                <button type="button" class="btn btn-success btn-sm"
                                                    id="btnAddChemArea">
                                                    <i class="mdi mdi-plus"></i> Tambah
                                                </button>
                                            @endif
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="mb-3">
                                                <input type="text" id="searchChemArea"
                                                    class="form-control form-control-sm" placeholder="Cari nama area...">
                                            </div>
                                            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                                                <table class="table table-sm table-hover align-middle mb-0">
                                                    <thead class="table-light sticky-top">
                                                        <tr>
                                                            <th>Nama Area</th>
                                                            <th class="text-center">Chemical</th>
                                                            <th class="text-center">Status</th>
                                                            <th class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="chemAreaTableBody">
                                                        <tr>
                                                            <td colspan="4" class="text-center py-3 text-muted">Memuat
                                                                area...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bagian 2: Master Jenis Pemakaian Chemical -->
                                <div class="col-lg-7 col-md-12">
                                    <div class="card border border-light-subtle shadow-none h-100">
                                        <div
                                            class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-3 gap-2">
                                            <div>
                                                <h6 class="fw-bold mb-0 text-info"><i
                                                        class="mdi mdi-flask-outline me-1"></i> Jenis Pemakaian
                                                    Chemical</h6>
                                                <small class="text-muted">Contoh: Boiler ada SRTF & SCF; WWTP ada PAC
                                                    powder, dll</small>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                                    id="btnRefreshChemTypes">
                                                    <i class="mdi mdi-refresh"></i>
                                                </button>
                                                @if ($canManageMaster)
                                                    <button type="button" class="btn btn-info text-white btn-sm"
                                                        id="btnAddChemType">
                                                        <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Chemical
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-5">
                                                    <select id="filterChemAreaSelect" class="form-select form-select-sm">
                                                        <option value="">-- Semua Area Chemical --</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-7">
                                                    <input type="text" id="searchChemType"
                                                        class="form-control form-control-sm"
                                                        placeholder="Cari nama chemical / satuan...">
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 5%;" class="text-center">No</th>
                                                            <th style="width: 20%;">Nama Chemical</th>
                                                            <th style="width: 15%;">Area</th>
                                                            <th style="width: 10%;" class="text-center">Satuan</th>
                                                            <th style="width: 22%;">Perhitungan / Rumus</th>
                                                            <th style="width: 8%;" class="text-center">Status</th>
                                                            <th style="width: 8%;" class="text-center">Transaksi</th>
                                                            <th style="width: 12%;" class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="chemTypeTableBody">
                                                        <tr>
                                                            <td colspan="8" class="text-center py-4">
                                                                <div class="spinner-border text-info" role="status">
                                                                </div>
                                                                <div class="text-muted mt-2">Memuat jenis chemical...</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL 1: FORM LISTRIK PANEL                    -->
    <!-- ============================================== -->
    <div class="modal fade" id="modalListrik" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formListrikPanel">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalListrikTitle">Tambah Panel Listrik</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="listrikId">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Panel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="listrikNama"
                                placeholder="Contoh: MDP, SDP15, Panel Genset" required>
                            <small class="text-muted">Nama panel unik yang akan muncul di pilihan dropdown form.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <input type="text" class="form-control" id="listrikDeskripsi"
                                placeholder="Keterangan lokasi atau peruntukan (opsional)">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor Urutan</label>
                            <input type="number" class="form-control" id="listrikUrutan"
                                placeholder="Contoh: 1, 2, 3 (opsional)">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning" id="btnSubmitListrik">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL 2: FORM JENIS PEMAKAIAN AIR              -->
    <!-- ============================================== -->
    <div class="modal fade" id="modalAir" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formAirArea">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalAirTitle">Tambah Jenis Pemakaian Air</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="airId">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Pemakaian / Area <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="airNama"
                                placeholder="Contoh: Sumur 6, Storage Tank 3" required>
                            <small class="text-muted">Setiap jenis pemakaian akan dibuatkan card input awal & akhir
                                m³.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi / Catatan</label>
                            <input type="text" class="form-control" id="airDeskripsi"
                                placeholder="Keterangan area (opsional)">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitAir">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL 3: FORM AREA CHEMICAL                    -->
    <!-- ============================================== -->
    <div class="modal fade" id="modalChemArea" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formChemArea">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalChemAreaTitle">Tambah Area Chemical</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="chemAreaId">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Area Chemical <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="chemAreaNama"
                                placeholder="Contoh: Boiler, WWTP, Utility, Cooling Tower" required>
                            <small class="text-muted">Area ini akan menjadi pilihan pertama saat operator mengisi
                                chemical.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="btnSubmitChemArea">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL 4: FORM JENIS PEMAKAIAN CHEMICAL         -->
    <!-- ============================================== -->
    <div class="modal fade" id="modalChemType" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formChemType">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalChemTypeTitle">Tambah Jenis Chemical</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="chemTypeId">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pilih Area Chemical <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="chemTypeAreaId" required>
                                    <option value="">-- Pilih Area --</option>
                                </select>
                                <small class="text-muted">Chemical ini akan otomatis muncul saat Area tersebut dipilih di
                                    form.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="chemTypeSatuan"
                                    placeholder="Contoh: Liter, Kg, Drum" value="Kg" required>
                                <small class="text-muted">Satuan ukur hasil pemakaian (default: Kg).</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Chemical (Jenis Pemakaian) <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="chemTypeNama"
                                placeholder="Contoh: SRTF, SCF, PAC powder 1, BE-100" required>
                        </div>

                        <!-- Opsi Tipe Perhitungan: Langsung vs Rumus -->
                        <div class="card border border-light-subtle bg-light mb-3">
                            <div class="card-body p-3">
                                <label class="form-label fw-semibold mb-2">Metode Perhitungan Pemakaian <span
                                        class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="chemTipePerhitungan"
                                            id="tipePerhitunganLangsung" value="langsung" checked>
                                        <label class="form-check-label fw-medium" for="tipePerhitunganLangsung">
                                            <i class="mdi mdi-numeric me-1 text-primary"></i> Input Langsung (Nilai Murni)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="chemTipePerhitungan"
                                            id="tipePerhitunganRumus" value="rumus">
                                        <label class="form-check-label fw-medium" for="tipePerhitunganRumus">
                                            <i class="mdi mdi-function me-1 text-info"></i> Gunakan Rumus / Formula
                                            Matematika
                                        </label>
                                    </div>
                                </div>
                                <small class="text-muted d-block">
                                    Pilih <strong>Input Langsung</strong> untuk area biasa (misal Boiler: SRTF, SCF). Pilih
                                    <strong>Rumus</strong> jika pemakaian dihitung otomatis dari dosis dan jam kerja
                                    (seperti WWTP: PAC powder, NaOH, dll).
                                </small>
                            </div>
                        </div>

                        <!-- Container Pengaturan Rumus & Simulator -->
                        <div id="containerRumusFormula" class="card border border-info-subtle shadow-none mb-3"
                            style="display: none;">
                            <div class="card-header bg-info-subtle py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-info"><i class="mdi mdi-calculator-variant me-1"></i>
                                    Konfigurasi Rumus Chemical</span>
                                <span class="badge bg-info text-white">Dinamis</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Rumus Formula <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text font-monospace text-muted">Hasil =</span>
                                        <input type="text" class="form-control font-monospace" id="chemTypeRumus"
                                            placeholder="Contoh: ({nilai} * 10 * {rh}) / 1000">
                                    </div>
                                    <div class="form-text mt-2">
                                        <strong>Token Variabel:</strong>
                                        <span class="badge border text-dark font-monospace me-1">{nilai}</span> atau <span
                                            class="badge border text-dark font-monospace me-2">nilai</span> (Dosis pompa /
                                        input operator) &bull;
                                        <span class="badge border text-dark font-monospace me-1">{rh}</span> atau <span
                                            class="badge border text-dark font-monospace me-2">rh</span> (Running hours
                                        pompa / hari).<br>
                                        <span class="text-muted">Operator yang didukung: <code>+</code>, <code>-</code>,
                                            <code>*</code>, <code>/</code>, <code>( )</code>. Contoh: <code>({nilai} * 10 *
                                                {rh}) / 1000</code></span>
                                    </div>
                                </div>

                                <!-- Simulator Uji Coba -->
                                <div class=border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold small text-secondary"><i
                                                class="mdi mdi-play-box-outline me-1"></i> Uji Coba / Simulator
                                            Rumus</span>
                                        <small class="text-muted">Cek apakah rumus valid sebelum disimpan</small>
                                    </div>
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text text-muted">{nilai}</span>
                                                <input type="number" step="any" class="form-control form-control-sm"
                                                    id="testNilai" value="15" placeholder="Nilai">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text text-muted">{rh}</span>
                                                <input type="number" step="any" class="form-control form-control-sm"
                                                    id="testRh" value="24" placeholder="RH (Jam)">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-outline-info btn-sm w-100"
                                                id="btnTestRumus">
                                                <i class="mdi mdi-play-circle-outline me-1"></i> Uji Coba Rumus
                                            </button>
                                        </div>
                                    </div>
                                    <div id="testFormulaResult" class="mt-2" style="display: none;"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white" id="btnSubmitChemType">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript Master Management Logic -->
    <script>
        const canManageMaster = @json($canManageMaster);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Global states
        let globalListrik = [];
        let globalAir = [];
        let globalChemAreas = [];
        let globalChemTypes = [];

        // ========================================================
        // 1. LISTRIK LOGIC
        // ========================================================
        function loadListrik() {
            $.get("{{ url('utility/master/listrik') }}", function(res) {
                globalListrik = res.data || [];
                $('#metricListrik').text(globalListrik.length);
                renderListrikTable();
            }).fail(function() {
                $('#listrikTableBody').html(
                    '<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data panel listrik.</td></tr>'
                );
            });
        }

        function renderListrikTable() {
            const search = ($('#searchListrik').val() || '').toLowerCase();
            const filtered = globalListrik.filter(p => p.nama_panel.toLowerCase().includes(search) || (p.deskripsi && p
                .deskripsi.toLowerCase().includes(search)));
            const $tbody = $('#listrikTableBody');
            $tbody.empty();

            if (filtered.length === 0) {
                $tbody.html(
                    '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data panel listrik yang cocok.</td></tr>'
                );
                return;
            }

            filtered.forEach((p, idx) => {
                const statusBadge = p.is_active ?
                    '<span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>' :
                    '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nonaktif</span>';

                let actionHtml = '-';
                if (canManageMaster) {
                    const toggleTitle = p.is_active ? 'Nonaktifkan' : 'Aktifkan';
                    const toggleIcon = p.is_active ? 'mdi-eye-off-outline text-warning' :
                        'mdi-eye-outline text-success';
                    actionHtml = `
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" onclick="editListrik(${p.id})" title="Edit">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleListrik(${p.id})" title="${toggleTitle}">
                                <i class="mdi ${toggleIcon}"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="deleteListrik(${p.id}, '${escapeHtml(p.nama_panel)}', ${p.usage_count})" title="Hapus">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </div>
                    `;
                }

                $tbody.append(`
                    <tr>
                        <td class="text-center">${idx + 1}</td>
                        <td class="fw-bold text-dark">${escapeHtml(p.nama_panel)}</td>
                        <td>${escapeHtml(p.deskripsi)}</td>
                        <td class="text-center">${p.urutan}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-center"><span class="badge bg-light text-secondary border">${p.usage_count} record</span></td>
                        <td class="text-center">${actionHtml}</td>
                    </tr>
                `);
            });
        }

        $('#searchListrik').on('keyup', renderListrikTable);
        $('#btnRefreshListrik').on('click', loadListrik);

        $('#btnAddListrik').on('click', function() {
            $('#formListrikPanel')[0].reset();
            $('#listrikId').val('');
            $('#modalListrikTitle').text('Tambah Panel Listrik');
            $('#modalListrik').modal('show');
        });

        window.editListrik = function(id) {
            const p = globalListrik.find(item => item.id === id);
            if (!p) return;
            $('#listrikId').val(p.id);
            $('#listrikNama').val(p.nama_panel);
            $('#listrikDeskripsi').val(p.deskripsi === '-' ? '' : p.deskripsi);
            $('#listrikUrutan').val(p.urutan);
            $('#modalListrikTitle').text('Edit Panel Listrik: ' + p.nama_panel);
            $('#modalListrik').modal('show');
        };

        $('#formListrikPanel').on('submit', function(e) {
            e.preventDefault();
            const id = $('#listrikId').val();
            const url = id ? `{{ url('utility/master/listrik') }}/${id}/update` :
                `{{ url('utility/master/listrik') }}`;
            const btn = $('#btnSubmitListrik');
            btn.prop('disabled', true).text('Menyimpan...');

            $.post(url, {
                nama_panel: $('#listrikNama').val(),
                deskripsi: $('#listrikDeskripsi').val(),
                urutan: $('#listrikUrutan').val()
            }, function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                $('#modalListrik').modal('hide');
                loadListrik();
            }).fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.'
                });
            }).always(function() {
                btn.prop('disabled', false).text('Simpan');
            });
        });

        window.toggleListrik = function(id) {
            $.post(`{{ url('utility/master/listrik') }}/${id}/toggle`, function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Diperbarui',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                });
                loadListrik();
            }).fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Gagal mengubah status.'
                });
            });
        };

        window.deleteListrik = function(id, nama, usageCount) {
            let warnText = `Yakin ingin menghapus panel "${nama}"?`;
            if (usageCount > 0) {
                warnText =
                    `Panel "${nama}" sudah memiliki ${usageCount} data transaksi di sistem. Data yang memiliki riwayat transaksi tidak dapat dihapus, disarankan untuk dinonaktifkan saja.`;
            }

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: warnText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('utility/master/listrik') }}/${id}`,
                        type: 'DELETE',
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadListrik();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Tidak Dapat Dihapus',
                                text: xhr.responseJSON?.message || 'Gagal menghapus data.'
                            });
                        }
                    });
                }
            });
        };

        // ========================================================
        // 2. AIR LOGIC
        // ========================================================
        function loadAir() {
            $.get("{{ url('utility/master/air') }}", function(res) {
                globalAir = res.data || [];
                $('#metricAir').text(globalAir.length);
                renderAirTable();
            }).fail(function() {
                $('#airTableBody').html(
                    '<tr><td colspan="6" class="text-center text-danger py-4">Gagal memuat data pemakaian air.</td></tr>'
                );
            });
        }

        function renderAirTable() {
            const search = ($('#searchAir').val() || '').toLowerCase();
            const filtered = globalAir.filter(a => a.nama_area.toLowerCase().includes(search) || (a.deskripsi && a.deskripsi
                .toLowerCase().includes(search)));
            const $tbody = $('#airTableBody');
            $tbody.empty();

            if (filtered.length === 0) {
                $tbody.html(
                    '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data jenis pemakaian air yang cocok.</td></tr>'
                );
                return;
            }

            filtered.forEach((a, idx) => {
                const statusBadge = a.is_active ?
                    '<span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>' :
                    '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nonaktif</span>';

                let actionHtml = '-';
                if (canManageMaster) {
                    const toggleTitle = a.is_active ? 'Nonaktifkan' : 'Aktifkan';
                    const toggleIcon = a.is_active ? 'mdi-eye-off-outline text-warning' :
                        'mdi-eye-outline text-success';
                    actionHtml = `
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" onclick="editAir(${a.id})" title="Edit">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleAir(${a.id})" title="${toggleTitle}">
                                <i class="mdi ${toggleIcon}"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="deleteAir(${a.id}, '${escapeHtml(a.nama_area)}', ${a.usage_count})" title="Hapus">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </div>
                    `;
                }

                $tbody.append(`
                    <tr>
                        <td class="text-center">${idx + 1}</td>
                        <td class="fw-bold text-dark">${escapeHtml(a.nama_area)}</td>
                        <td>${escapeHtml(a.deskripsi)}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-center"><span class="badge bg-light text-secondary border">${a.usage_count} record</span></td>
                        <td class="text-center">${actionHtml}</td>
                    </tr>
                `);
            });
        }

        $('#searchAir').on('keyup', renderAirTable);
        $('#btnRefreshAir').on('click', loadAir);

        $('#btnAddAir').on('click', function() {
            $('#formAirArea')[0].reset();
            $('#airId').val('');
            $('#modalAirTitle').text('Tambah Jenis Pemakaian Air');
            $('#modalAir').modal('show');
        });

        window.editAir = function(id) {
            const a = globalAir.find(item => item.id === id);
            if (!a) return;
            $('#airId').val(a.id);
            $('#airNama').val(a.nama_area);
            $('#airDeskripsi').val(a.deskripsi === '-' ? '' : a.deskripsi);
            $('#modalAirTitle').text('Edit Jenis Pemakaian Air: ' + a.nama_area);
            $('#modalAir').modal('show');
        };

        $('#formAirArea').on('submit', function(e) {
            e.preventDefault();
            const id = $('#airId').val();
            const url = id ? `{{ url('utility/master/air') }}/${id}/update` : `{{ url('utility/master/air') }}`;
            const btn = $('#btnSubmitAir');
            btn.prop('disabled', true).text('Menyimpan...');

            $.post(url, {
                nama_area: $('#airNama').val(),
                deskripsi: $('#airDeskripsi').val()
            }, function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                $('#modalAir').modal('hide');
                loadAir();
            }).fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.'
                });
            }).always(function() {
                btn.prop('disabled', false).text('Simpan');
            });
        });

        window.toggleAir = function(id) {
            $.post(`{{ url('utility/master/air') }}/${id}/toggle`, function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Diperbarui',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                });
                loadAir();
            }).fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Gagal mengubah status.'
                });
            });
        };

        window.deleteAir = function(id, nama, usageCount) {
            let warnText = `Yakin ingin menghapus jenis pemakaian air "${nama}"?`;
            if (usageCount > 0) {
                warnText =
                    `Jenis pemakaian air "${nama}" sudah memiliki ${usageCount} data riwayat di sistem. Sebaiknya dinonaktifkan saja agar riwayat tetap aman.`;
            }

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: warnText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('utility/master/air') }}/${id}`,
                        type: 'DELETE',
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadAir();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Tidak Dapat Dihapus',
                                text: xhr.responseJSON?.message || 'Gagal menghapus data.'
                            });
                        }
                    });
                }
            });
        };

        // ========================================================
        // 3. CHEMICAL LOGIC (AREA & TYPES)
        // ========================================================
        function loadChemAreas(callback) {
            $.get("{{ url('utility/master/chemical-areas') }}", function(res) {
                globalChemAreas = res.data || [];
                $('#metricChemArea').text(globalChemAreas.length);
                renderChemAreaTable();
                updateChemAreaDropdowns();
                if (callback) callback();
            }).fail(function() {
                $('#chemAreaTableBody').html(
                    '<tr><td colspan="4" class="text-center text-danger py-3">Gagal memuat area chemical.</td></tr>'
                );
            });
        }

        function updateChemAreaDropdowns() {
            // Dropdown filter
            const currentFilterVal = $('#filterChemAreaSelect').val();
            let filterOpts = '<option value="">-- Semua Area Chemical --</option>';
            // Dropdown modal
            let modalOpts = '<option value="">-- Pilih Area --</option>';

            globalChemAreas.forEach(a => {
                filterOpts += `<option value="${a.id}">${escapeHtml(a.nama_area)} (${a.types_count} chem)</option>`;
                modalOpts += `<option value="${a.id}">${escapeHtml(a.nama_area)}</option>`;
            });

            $('#filterChemAreaSelect').html(filterOpts).val(currentFilterVal);
            $('#chemTypeAreaId').html(modalOpts);
        }

        function renderChemAreaTable() {
            const search = ($('#searchChemArea').val() || '').toLowerCase();
            const filtered = globalChemAreas.filter(a => a.nama_area.toLowerCase().includes(search));
            const $tbody = $('#chemAreaTableBody');
            $tbody.empty();

            if (filtered.length === 0) {
                $tbody.html('<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada area chemical.</td></tr>');
                return;
            }

            filtered.forEach(a => {
                const statusBadge = a.is_active ?
                    '<span class="badge bg-success-subtle text-success">Aktif</span>' :
                    '<span class="badge bg-danger-subtle text-danger">Off</span>';

                let actionHtml = '-';
                if (canManageMaster) {
                    actionHtml = `
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editChemArea(${a.id})" title="Edit">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleChemArea(${a.id})" title="Toggle Status">
                                <i class="mdi ${a.is_active ? 'mdi-eye-off-outline text-warning' : 'mdi-eye-outline text-success'}"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteChemArea(${a.id}, '${escapeHtml(a.nama_area)}', ${a.types_count})" title="Hapus">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </div>
                    `;
                }

                $tbody.append(`
                    <tr>
                        <td class="fw-bold">${escapeHtml(a.nama_area)}</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">${a.types_count}</span></td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-center">${actionHtml}</td>
                    </tr>
                `);
            });
        }

        $('#searchChemArea').on('keyup', renderChemAreaTable);

        $('#btnAddChemArea').on('click', function() {
            $('#formChemArea')[0].reset();
            $('#chemAreaId').val('');
            $('#modalChemAreaTitle').text('Tambah Area Chemical');
            $('#modalChemArea').modal('show');
        });

        window.editChemArea = function(id) {
            const a = globalChemAreas.find(item => item.id === id);
            if (!a) return;
            $('#chemAreaId').val(a.id);
            $('#chemAreaNama').val(a.nama_area);
            $('#modalChemAreaTitle').text('Edit Area Chemical: ' + a.nama_area);
            $('#modalChemArea').modal('show');
        };

        $('#formChemArea').on('submit', function(e) {
            e.preventDefault();
            const id = $('#chemAreaId').val();
            const url = id ? `{{ url('utility/master/chemical-areas') }}/${id}/update` :
                `{{ url('utility/master/chemical-areas') }}`;
            const btn = $('#btnSubmitChemArea');
            btn.prop('disabled', true).text('Menyimpan...');

            $.post(url, {
                nama_area: $('#chemAreaNama').val()
            }, function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                $('#modalChemArea').modal('hide');
                loadChemAreas(() => loadChemTypes());
            }).fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.'
                });
            }).always(function() {
                btn.prop('disabled', false).text('Simpan');
            });
        });

        window.toggleChemArea = function(id) {
            $.post(`{{ url('utility/master/chemical-areas') }}/${id}/toggle`, function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Diperbarui',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                });
                loadChemAreas();
            }).fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Gagal mengubah status.'
                });
            });
        };

        window.deleteChemArea = function(id, nama, typesCount) {
            if (typesCount > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Dapat Dihapus',
                    text: `Area "${nama}" masih memiliki ${typesCount} jenis chemical di dalamnya. Silakan hapus atau pindahkan jenis chemical terlebih dahulu.`
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus area chemical "${nama}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('utility/master/chemical-areas') }}/${id}`,
                        type: 'DELETE',
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadChemAreas(() => loadChemTypes());
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message || 'Gagal menghapus area.'
                            });
                        }
                    });
                }
            });
        };

        // --- Chemical Types Logic ---
        function loadChemTypes() {
            const areaId = $('#filterChemAreaSelect').val();
            const params = {};
            if (areaId) params.area_id = areaId;

            $.get("{{ url('utility/master/chemical-types') }}", params, function(res) {
                globalChemTypes = res.data || [];
                $('#metricChemType').text(globalChemTypes.length);
                renderChemTypeTable();
            }).fail(function() {
                $('#chemTypeTableBody').html(
                    '<tr><td colspan="8" class="text-center text-danger py-4">Gagal memuat jenis chemical.</td></tr>'
                );
            });
        }

        function renderChemTypeTable() {
            const search = ($('#searchChemType').val() || '').toLowerCase();
            const filtered = globalChemTypes.filter(t => t.nama_chemical.toLowerCase().includes(search) || t.nama_area
                .toLowerCase().includes(search) || t.satuan.toLowerCase().includes(search) || (t.rumus_formula && t
                    .rumus_formula.toLowerCase().includes(search)));
            const $tbody = $('#chemTypeTableBody');
            $tbody.empty();

            if (filtered.length === 0) {
                $tbody.html(
                    '<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data jenis chemical yang cocok.</td></tr>'
                );
                return;
            }

            filtered.forEach((t, idx) => {
                const statusBadge = t.is_active ?
                    '<span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>' :
                    '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nonaktif</span>';

                let rumusHtml = '<span class="badge bg-secondary-subtle text-muted border">Langsung</span>';
                if (t.tipe_perhitungan === 'rumus' && t.rumus_formula) {
                    rumusHtml = `
                        <div class="d-flex align-items-center" title="${escapeHtml(t.rumus_formula)}">
                            <span class="badge bg-info-subtle text-dark border font-monospace text-truncate" style="max-width: 200px;">
                                <i class="mdi mdi-function text-info me-1"></i>${escapeHtml(t.rumus_formula)}
                            </span>
                        </div>
                    `;
                }

                let actionHtml = '-';
                if (canManageMaster) {
                    actionHtml = `
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" onclick="editChemType(${t.id})" title="Edit">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleChemType(${t.id})" title="Toggle Status">
                                <i class="mdi ${t.is_active ? 'mdi-eye-off-outline text-warning' : 'mdi-eye-outline text-success'}"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="deleteChemType(${t.id}, '${escapeHtml(t.nama_chemical)}', ${t.usage_count})" title="Hapus">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </div>
                    `;
                }

                $tbody.append(`
                    <tr>
                        <td class="text-center">${idx + 1}</td>
                        <td class="fw-bold text-dark">${escapeHtml(t.nama_chemical)}</td>
                        <td><span class="badge bg-info-subtle text-info border border-info-subtle">${escapeHtml(t.nama_area)}</span></td>
                        <td class="text-center"><span class="badge bg-light text-dark border">${escapeHtml(t.satuan)}</span></td>
                        <td>${rumusHtml}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-center"><span class="badge bg-light text-secondary border">${t.usage_count} record</span></td>
                        <td class="text-center">${actionHtml}</td>
                    </tr>
                `);
            });
        }

        $('#filterChemAreaSelect').on('change', loadChemTypes);
        $('#searchChemType').on('keyup', renderChemTypeTable);
        $('#btnRefreshChemTypes').on('click', loadChemTypes);

        // Toggle container rumus berdasarkan radio button
        $('input[name="chemTipePerhitungan"]').on('change', function() {
            if ($(this).val() === 'rumus') {
                $('#containerRumusFormula').slideDown(200);
            } else {
                $('#containerRumusFormula').slideUp(200);
            }
        });

        // Uji coba simulator rumus
        $('#btnTestRumus').on('click', function() {
            const formula = $('#chemTypeRumus').val();
            const nilai = $('#testNilai').val();
            const rh = $('#testRh').val();

            if (!formula || !formula.trim()) {
                $('#testFormulaResult').removeClass('alert alert-success alert-danger').addClass(
                    'alert alert-warning py-1 px-2 mb-0').html(
                    '<i class="mdi mdi-alert-circle me-1"></i>Masukkan rumus formula terlebih dahulu.'
                ).show();
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Menguji...');

            $.post("{{ url('utility/master/chemical-types/test-formula') }}", {
                formula: formula,
                nilai: nilai,
                rh: rh
            }, function(res) {
                $('#testFormulaResult').removeClass('alert alert-danger alert-warning').addClass(
                    'alert alert-success py-1 px-2 mb-0').html(
                    `<strong><i class="mdi mdi-check-circle me-1"></i>${res.preview}</strong>`
                ).show();
            }).fail(function(xhr) {
                $('#testFormulaResult').removeClass('alert alert-success alert-warning').addClass(
                    'alert alert-danger py-1 px-2 mb-0').html(
                    `<i class="mdi mdi-close-circle me-1"></i>${xhr.responseJSON?.message || 'Rumus tidak valid.'}`
                ).show();
            }).always(function() {
                btn.prop('disabled', false).html(
                    '<i class="mdi mdi-play-circle-outline me-1"></i> Uji Coba Rumus');
            });
        });

        $('#btnAddChemType').on('click', function() {
            $('#formChemType')[0].reset();
            $('#chemTypeId').val('');
            $('input[name="chemTipePerhitungan"][value="langsung"]').prop('checked', true);
            $('#containerRumusFormula').hide();
            $('#chemTypeRumus').val('');
            $('#testFormulaResult').hide().empty();

            const currentFilter = $('#filterChemAreaSelect').val();
            if (currentFilter) {
                $('#chemTypeAreaId').val(currentFilter);
            }
            $('#modalChemTypeTitle').text('Tambah Jenis Chemical');
            $('#modalChemType').modal('show');
        });

        window.editChemType = function(id) {
            const t = globalChemTypes.find(item => item.id === id);
            if (!t) return;
            $('#chemTypeId').val(t.id);
            $('#chemTypeAreaId').val(t.chemical_area_id);
            $('#chemTypeNama').val(t.nama_chemical);
            $('#chemTypeSatuan').val(t.satuan);

            const tipe = t.tipe_perhitungan || 'langsung';
            $(`input[name="chemTipePerhitungan"][value="${tipe}"]`).prop('checked', true);
            $('#chemTypeRumus').val(t.rumus_formula || '');
            $('#testFormulaResult').hide().empty();

            if (tipe === 'rumus') {
                $('#containerRumusFormula').show();
            } else {
                $('#containerRumusFormula').hide();
            }

            $('#modalChemTypeTitle').text('Edit Chemical: ' + t.nama_chemical);
            $('#modalChemType').modal('show');
        };

        $('#formChemType').on('submit', function(e) {
            e.preventDefault();
            const id = $('#chemTypeId').val();
            const url = id ? `{{ url('utility/master/chemical-types') }}/${id}/update` :
                `{{ url('utility/master/chemical-types') }}`;
            const btn = $('#btnSubmitChemType');

            const tipePerhitungan = $('input[name="chemTipePerhitungan"]:checked').val() || 'langsung';
            const rumusFormula = tipePerhitungan === 'rumus' ? $('#chemTypeRumus').val() : '';

            if (tipePerhitungan === 'rumus' && !rumusFormula.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Rumus Kosong',
                    text: 'Silakan isi rumus formula atau pilih metode Input Langsung.'
                });
                return;
            }

            btn.prop('disabled', true).text('Menyimpan...');

            $.post(url, {
                chemical_area_id: $('#chemTypeAreaId').val(),
                nama_chemical: $('#chemTypeNama').val(),
                satuan: $('#chemTypeSatuan').val(),
                tipe_perhitungan: tipePerhitungan,
                rumus_formula: rumusFormula
            }, function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                $('#modalChemType').modal('hide');
                loadChemTypes();
                loadChemAreas(); // update types_count on area table
            }).fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.'
                });
            }).always(function() {
                btn.prop('disabled', false).text('Simpan');
            });
        });

        window.toggleChemType = function(id) {
            $.post(`{{ url('utility/master/chemical-types') }}/${id}/toggle`, function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Diperbarui',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                });
                loadChemTypes();
            }).fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Gagal mengubah status.'
                });
            });
        };

        window.deleteChemType = function(id, nama, usageCount) {
            let warnText = `Yakin ingin menghapus jenis chemical "${nama}"?`;
            if (usageCount > 0) {
                warnText =
                    `Jenis chemical "${nama}" sudah memiliki ${usageCount} data riwayat pemakaian. Sebaiknya dinonaktifkan saja agar data riwayat tidak terganggu.`;
            }

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: warnText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('utility/master/chemical-types') }}/${id}`,
                        type: 'DELETE',
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadChemTypes();
                            loadChemAreas();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Tidak Dapat Dihapus',
                                text: xhr.responseJSON?.message || 'Gagal menghapus data.'
                            });
                        }
                    });
                }
            });
        };

        // Utility: Escape HTML helper
        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Initialize on page load
        $(document).ready(function() {
            loadListrik();
            loadAir();
            loadChemAreas(() => loadChemTypes());
        });
    </script>
@endsection
