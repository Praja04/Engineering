@extends('layouts.app')

@section('content')
    <div class="page-content">
        <div class="container-fluid pb-5">

            {{-- HEADER --}}
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
                <div>
                    <span
                        style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:4px 12px;border-radius:20px;background:rgba(37,99,235,.08);color:#2563eb;border:1px solid rgba(37,99,235,.15);display:inline-block;">
                        Machine Ledger &amp; Maintenance Analytics
                    </span>
                    <h3 class="fw-bold fs-3 mb-1 mt-2" style="letter-spacing:-0.5px;">Dashboard Material &amp; Forklift
                        Analytics</h3>
                    <p class="text-secondary small mb-0 fw-medium">
                        Analisis ledger perawatan mesin, kebutuhan &amp; penggantian sparepart, dan monitoring cost
                        pengeluaran Forklift &mdash;
                        <strong>{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</strong>
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('master.mtc.material.index') }}"
                        class="btn btn-outline-primary btn-sm px-3 py-2 fw-semibold">
                        <i class="ri-price-tag-3-line me-1"></i> Master Material &amp; Harga
                    </a>
                    <ol class="breadcrumb m-0 ms-2">
                        <li class="breadcrumb-item"><a href="javascript: void(0);" class="text-secondary">Dashboards</a>
                        </li>
                        <li class="breadcrumb-item active text-primary fw-semibold">Machine Ledger</li>
                    </ol>
                </div>
            </div>

            <!-- Filters Panel -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <form id="filterForm">
                                <div class="row g-3 align-items-end">
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-muted">TANGGAL MULAI</label>
                                        <div class="input-group border rounded">
                                            <span class="input-group-text border-0"><i
                                                    class="ri-calendar-2-line"></i></span>
                                            <input type="text" id="startDate" name="start_date"
                                                class="form-control border-0 flatpickr-input"
                                                placeholder="Pilih tanggal mulai">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-muted">TANGGAL SELESAI</label>
                                        <div class="input-group border rounded">
                                            <span class="input-group-text border-0"><i
                                                    class="ri-calendar-2-line"></i></span>
                                            <input type="text" id="endDate" name="end_date"
                                                class="form-control border-0 flatpickr-input"
                                                placeholder="Pilih tanggal selesai">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-muted">JENIS MAINTENANCE</label>
                                        <select id="filterJenisMtc" name="jenis_mtc" class="form-select border">
                                            <option value="">Semua Jenis</option>
                                            @foreach ($jenisMtcList as $jenis)
                                                <option value="{{ $jenis }}">{{ $jenis }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-muted">PAKET MAINTENANCE</label>
                                        <select id="filterPaket" name="paket" class="form-select border">
                                            <option value="">Semua Paket</option>
                                            <option value="Maintenance">Maintenance (A, B, C, D, Z, Checkpoint)</option>
                                            <option value="Korektif">Korektif</option>
                                            <optgroup label="Paket Spesifik">
                                                <option value="A">Paket A</option>
                                                <option value="B">Paket B</option>
                                                <option value="C">Paket C</option>
                                                <option value="D">Paket D</option>
                                                <option value="Z">Paket Z</option>
                                                <option value="Checkpoint">Checkpoint</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-12 d-flex gap-2">
                                        <button type="button" id="btnFilter" class="btn btn-primary w-100 py-2">
                                            <i class="ri-filter-line me-1"></i> Filter
                                        </button>
                                        <button type="button" id="btnReset" class="btn btn-light border w-100 py-2">
                                            <i class="ri-refresh-line me-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards Row (General Overview) -->
            <div class="row g-3 mb-4">
                <!-- Card 1: Total Kebutuhan Material -->
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card stat-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center"
                                        style="width: 48px; height: 48px;">
                                        <i class="ri-stack-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted fw-semibold text-uppercase tracking-wide small">Total
                                        Kebutuhan (Qty)</h6>
                                    <h4 class="mb-0 fw-bold text-primary" id="valTotalQtyKeb">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 2: Total Penggantian Material -->
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card stat-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-success-subtle d-flex align-items-center justify-content-center"
                                        style="width: 48px; height: 48px;">
                                        <i class="ri-recycle-line fs-4 text-success"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted fw-semibold text-uppercase tracking-wide small">Total
                                        Penggantian (Qty)</h6>
                                    <h4 class="mb-0 fw-bold text-success" id="valTotalQtyPeng">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 3: Jenis Barang Unik -->
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card stat-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-info-subtle d-flex align-items-center justify-content-center"
                                        style="width: 48px; height: 48px;">
                                        <i class="ri-shape-line fs-4 text-info"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted fw-semibold text-uppercase tracking-wide small">Item
                                        Material Unik</h6>
                                    <h4 class="mb-0 fw-bold" id="valUniqueItems">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 4: Total Pekerjaan Maintenance -->
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card stat-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-warning-subtle d-flex align-items-center justify-content-center"
                                        style="width: 48px; height: 48px;">
                                        <i class="ri-tools-line fs-4 text-warning"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted fw-semibold text-uppercase tracking-wide small">Total
                                        Pekerjaan MTC</h6>
                                    <h4 class="mb-0 fw-bold" id="valTotalJobs">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════ SECTION: ANALISIS KHUSUS FORKLIFT ════════════════════ -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center"
                                style="width: 36px; height: 36px;">
                                <i class="ri-truck-line fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">Analisis Forklift (Part &amp; Cost Pengeluaran)</h5>
                                <p class="text-muted small mb-0">Statistik penggantian suku cadang dan beban biaya material
                                    dari pengerjaan <strong>Mtc Electric Engine</strong> (Forklift)</p>
                            </div>
                        </div>
                        <span class="badge bg-warning-subtle text-dark border border-warning px-3 py-2 fw-semibold">
                            <i class="ri-flashlight-line me-1 text-warning"></i> Forklift Dedicated KPI
                        </span>
                    </div>

                    <!-- Forklift KPI Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body py-3 px-4">
                                    <div class="text-muted small fw-semibold text-uppercase">Total Cost Material Forklift
                                    </div>
                                    <h4 class="mb-0 fw-bold text-danger mt-1" id="valForkliftTotalCost">Rp 0</h4>
                                    <div class="small text-muted mt-1" style="font-size: 11px;">Berdasarkan Master Harga
                                        MTC</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body py-3 px-4">
                                    <div class="text-muted small fw-semibold text-uppercase">Total Part Diganti (Qty)</div>
                                    <h4 class="mb-0 fw-bold text-primary mt-1" id="valForkliftPartsQty">0</h4>
                                    <div class="small text-muted mt-1" style="font-size: 11px;">Kuantitas part penggantian
                                        forklift</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body py-3 px-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-muted small fw-semibold text-uppercase">Cost Pengeluaran WRH</div>
                                        <span
                                            class="badge bg-warning-subtle text-warning fw-bold font-monospace">WRH</span>
                                    </div>
                                    <h4 class="mb-0 fw-bold text-dark mt-1" id="valForkliftCostWrh">Rp 0</h4>
                                    <div class="small text-muted mt-1" style="font-size: 11px;">Penggantian part unit
                                        Warehouse</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body py-3 px-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-muted small fw-semibold text-uppercase">Cost Pengeluaran PRD</div>
                                        <span class="badge bg-info-subtle text-info fw-bold font-monospace">PRD</span>
                                    </div>
                                    <h4 class="mb-0 fw-bold text-dark mt-1" id="valForkliftCostPrd">Rp 0</h4>
                                    <div class="small text-muted mt-1" style="font-size: 11px;">Penggantian part unit
                                        Produksi</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Forklift Charts Row (2 Big Interactive Charts) -->
                    <div class="row g-4">
                        <!-- Chart Forklift: Penggantian Part -->
                        <div class="col-lg-6 col-12">
                            <div class="card border-0 shadow-sm h-100">
                                <div
                                    class="card-header border-bottom-0 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="card-title mb-1 fw-bold text-primary">
                                            <i class="ri-recycle-line me-1"></i> Forklift: Penggantian Part
                                        </h6>
                                        <p class="text-muted small mb-0">Analisis frekuensi dan kuantitas penggantian suku
                                            cadang</p>
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group" id="btnGroupForkliftPart">
                                        <button type="button" class="btn btn-outline-primary active"
                                            data-view="top_parts">Top 10 Part</button>
                                        <button type="button" class="btn btn-outline-primary" data-view="per_unit">Per
                                            Unit Forklift</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="chartForkliftPart" style="min-height: 360px;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Chart Forklift: Cost Pengeluaran Material -->
                        <div class="col-lg-6 col-12">
                            <div class="card border-0 shadow-sm h-100">
                                <div
                                    class="card-header border-bottom-0 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="card-title mb-1 fw-bold text-danger">
                                            <i class="ri-money-dollar-circle-line me-1"></i> Forklift: Cost
                                            Pengeluaran
                                        </h6>
                                        <p class="text-muted small mb-0">Beban biaya pengeluaran sparepart material per
                                            unit Forklift</p>
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group" id="btnGroupForkliftCost">
                                        <button type="button" class="btn btn-outline-danger active"
                                            data-view="cost_per_unit">Cost per Unit</button>
                                        <button type="button" class="btn btn-outline-danger"
                                            data-view="top_cost_parts">Top Cost Part</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="chartForkliftCost" style="min-height: 360px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row: General Overview Top 10 Kebutuhan & Penggantian -->
            <div class="row g-4 mb-4">
                <!-- Top 10 Kebutuhan Material Bar Chart -->
                <div class="col-lg-6 col-12">
                    <div class="card chart-card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom-0 pb-0">
                            <h5 class="card-title mb-1 fw-semibold text-primary">
                                <i class="ri-bar-chart-fill me-2"></i>Top 10 Kebutuhan Material (Seluruh Mesin)
                            </h5>
                            <p class="text-muted small mb-0">Material yang paling banyak diajukan kebutuhannya</p>
                        </div>
                        <div class="card-body">
                            <div id="chartTopKebutuhan" style="min-height: 340px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Top 10 Penggantian Material Bar Chart -->
                <div class="col-lg-6 col-12">
                    <div class="card chart-card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom-0 pb-0">
                            <h5 class="card-title mb-1 fw-semibold text-success">
                                <i class="ri-bar-chart-2-fill me-2"></i>Top 10 Penggantian Material (Seluruh Mesin)
                            </h5>
                            <p class="text-muted small mb-0">Material yang paling banyak terealisasi diganti</p>
                        </div>
                        <div class="card-body">
                            <div id="chartTopPenggantian" style="min-height: 340px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Machine Ledger Summary Table -->
            <div class="row">
                <div class="col-md-12 col-12">
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header border-bottom-0 p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">
                                    <i class="ri-database-2-line text-primary me-2"></i>Ledger Material per Mesin / Unit /
                                    Area
                                </h5>
                                <p class="text-muted mb-0 small">Ringkasan akumulasi kebutuhan, penggantian material, dan
                                    estimasi biaya per unit mesin</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" id="tableLedgerSearch" class="form-control form-control-sm border"
                                    placeholder="Cari mesin / unit / jenis..." style="width: 260px;">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="tableLedger" class="table table-hover align-middle mb-0 w-100"
                                    style="font-size: 13px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4" style="width: 50px;">NO</th>
                                            <th style="width: 130px;">KODE MESIN</th>
                                            <th>MESIN / UNIT / AREA</th>
                                            <th>JENIS MAINTENANCE</th>
                                            <th>LOKASI</th>
                                            <th class="text-end">TOTAL KEBUTUHAN</th>
                                            <th class="text-end">TOTAL PENGGANTIAN</th>
                                            <th class="text-end" style="width: 150px;">ESTIMASI BIAYA</th>
                                            <th class="text-center">PEKERJAAN</th>
                                            <th class="text-center pe-4" style="width: 100px;">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableLedgerBody">
                                        <!-- Loaded dynamically via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                            <!-- Ledger Table Pagination Controls -->
                            <div class="d-flex justify-content-between align-items-center p-3 border-top flex-wrap gap-2"
                                id="ledgerPaginationWrap">
                                <div class="text-muted small" id="ledgerTableInfo">Menampilkan 0 data</div>
                                <ul class="pagination pagination-sm mb-0" id="ledgerPagination"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ════════════════════ MODAL DETAIL PENGERJAAN MATERIAL ════════════════════ -->
    <div class="modal fade" id="modalDetailMaterial" tabindex="-1" aria-labelledby="modalDetailMaterialLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-1" id="modalDetailMaterialLabel">
                            <i class="ri-file-list-3-line text-primary me-2"></i>Detail Transaksi Material: <span
                                id="modalMachineName" class="text-primary"></span>
                        </h5>
                        <div class="text-muted small">
                            Jenis: <span id="modalJenisMtc" class="fw-semibold text-dark">-</span> &nbsp;•&nbsp;
                            Lokasi: <span id="modalLocation" class="fw-semibold text-dark">-</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Modal Filter & Search Bar -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="btn-group btn-group-sm" role="group" id="modalKategoriFilter">
                            <button type="button" class="btn btn-outline-primary active" data-cat="all">Semua</button>
                            <button type="button" class="btn btn-outline-primary"
                                data-cat="Kebutuhan">Kebutuhan</button>
                            <button type="button" class="btn btn-outline-success"
                                data-cat="Penggantian">Penggantian</button>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" id="modalSearch" class="form-control form-control-sm border"
                                placeholder="Cari material, MID, teknisi..." style="width: 250px;">
                        </div>
                    </div>

                    <!-- Clean Vanilla HTML Table -->
                    <div class="table-responsive border rounded mb-3" style="max-height: 460px;">
                        <table class="table table-hover table-striped align-middle mb-0" style="font-size: 12.5px;">
                            <thead class="table-light sticky-top" style="z-index: 2;">
                                <tr>
                                    <th class="ps-3" style="width: 45px;">NO</th>
                                    <th style="width: 105px;">TANGGAL</th>
                                    <th>JENIS</th>
                                    <th style="width: 95px;">PAKET</th>
                                    <th style="width: 105px;">KATEGORI</th>
                                    <th style="width: 85px;">MID</th>
                                    <th>DESKRIPSI MATERIAL</th>
                                    <th class="text-end" style="width: 75px;">QTY</th>
                                    <th style="width: 65px;">SATUAN</th>
                                    <th class="text-end" style="width: 110px;">HARGA SATUAN</th>
                                    <th class="text-end" style="width: 120px;">TOTAL BIAYA</th>
                                    <th class="pe-3" style="width: 100px;">TEKNISI</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableBody">
                                <!-- Loaded dynamically via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal Custom Pagination Controls -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2"
                        id="modalPaginationWrap">
                        <div class="text-muted small" id="modalTableInfo">Menampilkan 0 data</div>
                        <ul class="pagination pagination-sm mb-0" id="modalPagination"></ul>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4 border-top d-flex justify-content-between align-items-center">
                    <div class="small fw-semibold text-muted">
                        Total Biaya Transaksi Filter: <span id="modalSummaryTotalCost"
                            class="text-danger fw-bold fs-6 ms-1">Rp 0</span>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold"
                        data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Load ApexCharts -->
    <script src="{{ asset('material/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Init Flatpickr Date Pickers
            flatpickr("#startDate", {
                dateFormat: "Y-m-d",
                defaultDate: new Date(new Date().setDate(new Date().getDate() - 30)),
                onChange: function() {
                    loadDashboardCharts();
                    loadMachineLedgerData();
                }
            });
            flatpickr("#endDate", {
                dateFormat: "Y-m-d",
                defaultDate: new Date(),
                onChange: function() {
                    loadDashboardCharts();
                    loadMachineLedgerData();
                }
            });

            // Instant filter on select change
            $('#filterJenisMtc, #filterPaket').on('change', function() {
                loadDashboardCharts();
                loadMachineLedgerData();
            });

            $('#btnFilter').on('click', function() {
                loadDashboardCharts();
                loadMachineLedgerData();
            });

            $('#btnReset').on('click', function() {
                $('#filterForm')[0].reset();
                $('#startDate').flatpickr().setDate(new Date(new Date().setDate(new Date().getDate() -
                    30)));
                $('#endDate').flatpickr().setDate(new Date());
                loadDashboardCharts();
                loadMachineLedgerData();
            });

            // Chart instances
            let chartKebutuhan = null;
            let chartPenggantian = null;
            let chartForkliftPart = null;
            let chartForkliftCost = null;

            // Cached Forklift chart data
            let cachedForkliftCharts = {};
            let activeForkliftPartView = 'top_parts';
            let activeForkliftCostView = 'cost_per_unit';

            // In-Memory Storage for Ledger and Modal Detail
            let rawLedgerData = [];
            let filteredLedgerData = [];
            let ledgerCurrentPage = 1;
            const ledgerPageSize = 10;

            let rawModalDetails = [];
            let filteredModalDetails = [];
            let modalCurrentPage = 1;
            const modalPageSize = 10;
            let modalActiveCategory = 'all';

            // Initial Load
            loadDashboardCharts();
            loadMachineLedgerData();

            // ─────────────────────────────────────────────────────────────
            // 1. LOAD CHARTS & CARDS DATA
            // ─────────────────────────────────────────────────────────────
            function loadDashboardCharts() {
                const formData = $('#filterForm').serialize();

                $.ajax({
                    url: "{{ route('mtc.dashboard.material.charts') }}",
                    type: "GET",
                    data: formData,
                    success: function(res) {
                        if (res.status === 200) {
                            // General KPIs
                            $('#valTotalQtyKeb').text((res.summary.total_qty_kebutuhan || 0)
                                .toLocaleString('id-ID'));
                            $('#valTotalQtyPeng').text((res.summary.total_qty_penggantian || 0)
                                .toLocaleString('id-ID'));
                            $('#valUniqueItems').text((res.summary.unique_items || 0).toLocaleString(
                                'id-ID'));
                            $('#valTotalJobs').text((res.summary.total_jobs || 0).toLocaleString(
                                'id-ID'));

                            // Forklift KPIs
                            if (res.forklift_summary) {
                                $('#valForkliftTotalCost').text(res.forklift_summary.total_cost_fmt ||
                                    'Rp 0');
                                $('#valForkliftPartsQty').text((res.forklift_summary.total_parts_qty ||
                                    0).toLocaleString('id-ID'));
                                $('#valForkliftCostWrh').text(res.forklift_summary.total_cost_wrh_fmt ||
                                    'Rp 0');
                                $('#valForkliftCostPrd').text(res.forklift_summary.total_cost_prd_fmt ||
                                    'Rp 0');
                            }

                            // General Top 10 Charts
                            renderTopKebutuhanChart(res.charts.top_kebutuhan || []);
                            renderTopPenggantianChart(res.charts.top_penggantian || []);

                            // Store Forklift Charts Data & Render
                            cachedForkliftCharts = res.charts;
                            renderForkliftPartChart();
                            renderForkliftCostChart();
                        }
                    },
                    error: function(err) {
                        console.error("Dashboard charts error:", err);
                    }
                });
            }

            // General: Top 10 Kebutuhan Chart
            function renderTopKebutuhanChart(data) {
                const container = document.querySelector("#chartTopKebutuhan");
                if (!data || data.length === 0) {
                    if (chartKebutuhan) {
                        chartKebutuhan.destroy();
                        chartKebutuhan = null;
                    }
                    container.innerHTML =
                        '<div class="text-center p-5 text-muted small">Tidak ada data kebutuhan material pada periode ini</div>';
                    return;
                }
                const categories = data.map(item => item.label || 'Tanpa Deskripsi');
                const seriesData = data.map(item => item.qty);

                const options = {
                    chart: {
                        type: 'bar',
                        height: 340,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Qty Kebutuhan',
                        data: seriesData
                    }],
                    colors: ['#0d6efd'],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 5,
                            barHeight: '62%'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: val => val.toLocaleString('id-ID'),
                        style: {
                            colors: ['#fff'],
                            fontSize: '11px',
                            fontWeight: 700
                        }
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            formatter: val => Math.floor(val)
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9'
                    },
                    tooltip: {
                        y: {
                            formatter: val => val.toLocaleString('id-ID') + ' Qty'
                        }
                    }
                };

                if (chartKebutuhan) chartKebutuhan.destroy();
                chartKebutuhan = new ApexCharts(container, options);
                chartKebutuhan.render();
            }

            // General: Top 10 Penggantian Chart
            function renderTopPenggantianChart(data) {
                const container = document.querySelector("#chartTopPenggantian");
                if (!data || data.length === 0) {
                    if (chartPenggantian) {
                        chartPenggantian.destroy();
                        chartPenggantian = null;
                    }
                    container.innerHTML =
                        '<div class="text-center p-5 text-muted small">Tidak ada data penggantian material pada periode ini</div>';
                    return;
                }
                const categories = data.map(item => item.label || 'Tanpa Deskripsi');
                const seriesData = data.map(item => item.qty);

                const options = {
                    chart: {
                        type: 'bar',
                        height: 340,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Qty Penggantian',
                        data: seriesData
                    }],
                    colors: ['#198754'],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 5,
                            barHeight: '62%'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: val => val.toLocaleString('id-ID'),
                        style: {
                            colors: ['#fff'],
                            fontSize: '11px',
                            fontWeight: 700
                        }
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            formatter: val => Math.floor(val)
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9'
                    },
                    tooltip: {
                        y: {
                            formatter: val => val.toLocaleString('id-ID') + ' Qty'
                        }
                    }
                };

                if (chartPenggantian) chartPenggantian.destroy();
                chartPenggantian = new ApexCharts(container, options);
                chartPenggantian.render();
            }

            // ════════════════════ FORKLIFT CHARTS ════════════════════
            $('#btnGroupForkliftPart').on('click', 'button', function() {
                $('#btnGroupForkliftPart button').removeClass('active');
                $(this).addClass('active');
                activeForkliftPartView = $(this).data('view');
                renderForkliftPartChart();
            });

            $('#btnGroupForkliftCost').on('click', 'button', function() {
                $('#btnGroupForkliftCost button').removeClass('active');
                $(this).addClass('active');
                activeForkliftCostView = $(this).data('view');
                renderForkliftCostChart();
            });

            // Render Forklift Part Replacement Chart (Top 10 Parts OR Per Unit Forklift)
            function renderForkliftPartChart() {
                const container = document.querySelector("#chartForkliftPart");
                let categories = [];
                let seriesData = [];
                let chartTitle = '';

                if (activeForkliftPartView === 'top_parts') {
                    const data = cachedForkliftCharts.forklift_top_parts || [];
                    if (data.length === 0) {
                        if (chartForkliftPart) {
                            chartForkliftPart.destroy();
                            chartForkliftPart = null;
                        }
                        container.innerHTML =
                            '<div class="text-center p-5 text-muted small">Tidak ada data penggantian part forklift pada periode ini</div>';
                        return;
                    }
                    categories = data.map(item => item.label);
                    seriesData = data.map(item => item.qty);
                    chartTitle = 'Qty Part Diganti';
                } else {
                    const data = cachedForkliftCharts.forklift_parts_per_unit || [];
                    if (data.length === 0) {
                        if (chartForkliftPart) {
                            chartForkliftPart.destroy();
                            chartForkliftPart = null;
                        }
                        container.innerHTML =
                            '<div class="text-center p-5 text-muted small">Tidak ada data unit forklift pada periode ini</div>';
                        return;
                    }
                    categories = data.map(item => item.unit);
                    seriesData = data.map(item => item.total_qty);
                    chartTitle = 'Total Part per Unit';
                }

                const options = {
                    chart: {
                        type: 'bar',
                        height: 360,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: chartTitle,
                        data: seriesData
                    }],
                    colors: ['#2563eb'],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 5,
                            barHeight: '62%'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: val => val.toLocaleString('id-ID'),
                        style: {
                            colors: ['#fff'],
                            fontSize: '11px',
                            fontWeight: 700
                        }
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            formatter: val => Math.floor(val)
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9'
                    },
                    tooltip: {
                        y: {
                            formatter: val => val.toLocaleString('id-ID') + ' Part (Qty)'
                        }
                    }
                };

                if (chartForkliftPart) chartForkliftPart.destroy();
                chartForkliftPart = new ApexCharts(container, options);
                chartForkliftPart.render();
            }

            // Render Forklift Cost Chart (Cost per Unit OR Top Cost Parts)
            function renderForkliftCostChart() {
                const container = document.querySelector("#chartForkliftCost");
                let categories = [];
                let seriesData = [];
                let chartTitle = '';

                if (activeForkliftCostView === 'cost_per_unit') {
                    const data = cachedForkliftCharts.forklift_cost_per_unit || [];
                    if (data.length === 0) {
                        if (chartForkliftCost) {
                            chartForkliftCost.destroy();
                            chartForkliftCost = null;
                        }
                        container.innerHTML =
                            '<div class="text-center p-5 text-muted small">Tidak ada transaksi biaya forklift pada periode ini</div>';
                        return;
                    }
                    categories = data.map(item => item.unit);
                    seriesData = data.map(item => item.total_cost);
                    chartTitle = 'Total Biaya (Rp)';
                } else {
                    const data = cachedForkliftCharts.forklift_top_cost_parts || [];
                    if (data.length === 0) {
                        if (chartForkliftCost) {
                            chartForkliftCost.destroy();
                            chartForkliftCost = null;
                        }
                        container.innerHTML =
                            '<div class="text-center p-5 text-muted small">Tidak ada data biaya part pada periode ini</div>';
                        return;
                    }
                    categories = data.map(item => item.label);
                    seriesData = data.map(item => item.total_cost);
                    chartTitle = 'Total Biaya Part (Rp)';
                }

                const options = {
                    chart: {
                        type: 'bar',
                        height: 360,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: chartTitle,
                        data: seriesData
                    }],
                    colors: ['#dc2626'],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 5,
                            barHeight: '62%'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: val => 'Rp ' + Math.round(val).toLocaleString('id-ID'),
                        style: {
                            colors: ['#fff'],
                            fontSize: '11px',
                            fontWeight: 700
                        }
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            formatter: val => 'Rp ' + (val >= 1000000 ? (val / 1000000).toFixed(1) + ' Jt' : val
                                .toLocaleString('id-ID'))
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9'
                    },
                    tooltip: {
                        y: {
                            formatter: val => 'Rp ' + Number(val).toLocaleString('id-ID')
                        }
                    }
                };

                if (chartForkliftCost) chartForkliftCost.destroy();
                chartForkliftCost = new ApexCharts(container, options);
                chartForkliftCost.render();
            }

            // ─────────────────────────────────────────────────────────────
            // 2. LOAD MACHINE LEDGER TABLE DATA
            // ─────────────────────────────────────────────────────────────
            function loadMachineLedgerData() {
                const formData = $('#filterForm').serialize();
                $('#tableLedgerBody').html(
                    '<tr><td colspan="10" class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat data ledger mesin...</td></tr>'
                );

                $.ajax({
                    url: "{{ route('mtc.dashboard.material.ledger') }}",
                    type: "GET",
                    data: formData,
                    success: function(res) {
                        if (res.status === 200) {
                            rawLedgerData = res.data || [];
                            filterLedgerTable();
                        } else {
                            $('#tableLedgerBody').html(
                                '<tr><td colspan="10" class="text-center py-5 text-danger">Gagal memuat data.</td></tr>'
                            );
                        }
                    },
                    error: function() {
                        $('#tableLedgerBody').html(
                            '<tr><td colspan="10" class="text-center py-5 text-danger">Terjadi kesalahan saat memuat data ledger.</td></tr>'
                        );
                    }
                });
            }

            function filterLedgerTable() {
                const q = $('#tableLedgerSearch').val().trim().toLowerCase();
                if (!q) {
                    filteredLedgerData = rawLedgerData.slice();
                } else {
                    filteredLedgerData = rawLedgerData.filter(item => {
                        const searchStr =
                            `${item.kode_mesin || ''} ${item.nama_mesin || ''} ${item.jenis_mtc || ''} ${item.lokasi || ''}`
                            .toLowerCase();
                        return searchStr.includes(q);
                    });
                }
                ledgerCurrentPage = 1;
                renderLedgerTablePage();
            }

            function renderLedgerTablePage() {
                const total = filteredLedgerData.length;
                const totalPages = Math.ceil(total / ledgerPageSize) || 1;
                if (ledgerCurrentPage > totalPages) ledgerCurrentPage = totalPages;
                if (ledgerCurrentPage < 1) ledgerCurrentPage = 1;

                const startIdx = (ledgerCurrentPage - 1) * ledgerPageSize;
                const endIdx = Math.min(startIdx + ledgerPageSize, total);
                const pageItems = filteredLedgerData.slice(startIdx, endIdx);

                if (total === 0) {
                    $('#tableLedgerBody').html(
                        '<tr><td colspan="10" class="text-center py-5 text-muted">Tidak ada data ledger mesin yang sesuai.</td></tr>'
                    );
                    $('#ledgerTableInfo').text('Menampilkan 0 data');
                    $('#ledgerPagination').empty();
                    return;
                }

                let rowsHtml = '';
                pageItems.forEach((item, idx) => {
                    const rowNo = startIdx + idx + 1;
                    const mainIdsStr = (item.main_ids || []).join(',');
                    const kebBadge = item.total_kebutuhan_qty > 0 ?
                        `<span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">${item.total_kebutuhan_qty.toLocaleString('id-ID')}</span>` :
                        `<span class="text-muted">—</span>`;
                    const pengBadge = item.total_penggantian_qty > 0 ?
                        `<span class="badge bg-success-subtle text-success fw-bold px-2 py-1">${item.total_penggantian_qty.toLocaleString('id-ID')}</span>` :
                        `<span class="text-muted">—</span>`;
                    const costBadge = item.total_cost > 0 ?
                        `<span class="fw-bold text-danger">${item.total_cost_fmt}</span>` :
                        `<span class="text-muted">—</span>`;

                    const forkliftTag = item.is_forklift ?
                        `<span class="badge bg-warning-subtle text-dark border border-warning-subtle me-1"><i class="ri-truck-line me-1"></i>Forklift</span>` :
                        '';

                    rowsHtml += `
                        <tr>
                            <td class="ps-4 text-secondary fw-semibold">${rowNo}</td>
                            <td>
                                <span class="badge bg-light text-dark font-monospace border">${item.kode_mesin || '—'}</span>
                            </td>
                            <td>
                                ${forkliftTag}<span class="fw-bold text-dark">${item.nama_mesin}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">${item.jenis_mtc || '-'}</span>
                            </td>
                            <td class="text-muted small">${item.lokasi || '-'}</td>
                            <td class="text-end">${kebBadge}</td>
                            <td class="text-end">${pengBadge}</td>
                            <td class="text-end">${costBadge}</td>
                            <td class="text-center">
                                <span class="badge bg-warning-subtle text-warning fw-bold">${item.total_pekerjaan}x</span>
                            </td>
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-detail-machine py-1 px-2"
                                    data-name="${item.nama_mesin}"
                                    data-jenis="${item.jenis_mtc}"
                                    data-location="${item.lokasi}"
                                    data-main-ids="${mainIdsStr}">
                                    <i class="ri-file-list-3-line me-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                    `;
                });

                $('#tableLedgerBody').html(rowsHtml);
                $('#ledgerTableInfo').text(`Menampilkan ${startIdx + 1} - ${endIdx} dari ${total} mesin / unit`);

                // Pagination
                let pagHtml = `<li class="page-item ${ledgerCurrentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="changeLedgerPage(${ledgerCurrentPage - 1})">‹</a>
                </li>`;

                for (let p = 1; p <= totalPages; p++) {
                    if (p === 1 || p === totalPages || (p >= ledgerCurrentPage - 2 && p <= ledgerCurrentPage + 2)) {
                        pagHtml += `<li class="page-item ${p === ledgerCurrentPage ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0)" onclick="changeLedgerPage(${p})">${p}</a>
                        </li>`;
                    } else if (p === ledgerCurrentPage - 3 || p === ledgerCurrentPage + 3) {
                        pagHtml += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
                    }
                }

                pagHtml += `<li class="page-item ${ledgerCurrentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="changeLedgerPage(${ledgerCurrentPage + 1})">›</a>
                </li>`;

                $('#ledgerPagination').html(pagHtml);
            }

            window.changeLedgerPage = function(page) {
                ledgerCurrentPage = page;
                renderLedgerTablePage();
            };

            $('#tableLedgerSearch').on('input', filterLedgerTable);

            // ─────────────────────────────────────────────────────────────
            // 3. MODAL DETAIL TRANSAKSI PENGERJAAN
            // ─────────────────────────────────────────────────────────────
            $(document).on('click', '.btn-detail-machine', function() {
                const name = $(this).data('name');
                const jenis = $(this).data('jenis');
                const loc = $(this).data('location');
                const mainIds = $(this).data('main-ids');

                $('#modalMachineName').text(name || '-');
                $('#modalJenisMtc').text(jenis || '-');
                $('#modalLocation').text(loc || '-');
                $('#modalSearch').val('');
                modalActiveCategory = 'all';
                $('#modalKategoriFilter button').removeClass('active');
                $('#modalKategoriFilter button[data-cat="all"]').addClass('active');

                $('#modalTableBody').html(
                    '<tr><td colspan="12" class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat rincian transaksi material...</td></tr>'
                );
                $('#modalDetailMaterial').modal('show');

                $.ajax({
                    url: "{{ route('mtc.dashboard.material.machine-details') }}",
                    type: "GET",
                    data: {
                        main_ids: mainIds
                    },
                    success: function(res) {
                        if (res.status === 200) {
                            rawModalDetails = res.data || [];
                            filterModalDetails();
                        } else {
                            $('#modalTableBody').html(
                                '<tr><td colspan="12" class="text-center py-5 text-danger">Gagal mengambil data transaksi.</td></tr>'
                            );
                        }
                    },
                    error: function() {
                        $('#modalTableBody').html(
                            '<tr><td colspan="12" class="text-center py-5 text-danger">Terjadi kesalahan saat memuat rincian.</td></tr>'
                        );
                    }
                });
            });

            $('#modalKategoriFilter').on('click', 'button', function() {
                $('#modalKategoriFilter button').removeClass('active');
                $(this).addClass('active');
                modalActiveCategory = $(this).data('cat') || 'all';
                filterModalDetails();
            });

            $('#modalSearch').on('input', filterModalDetails);

            function filterModalDetails() {
                const q = $('#modalSearch').val().trim().toLowerCase();
                let sumCost = 0;

                filteredModalDetails = rawModalDetails.filter(item => {
                    if (modalActiveCategory !== 'all' && item.kategori !== modalActiveCategory) {
                        return false;
                    }
                    if (q) {
                        const searchStr =
                            `${item.tanggal || ''} ${item.mid || ''} ${item.deskripsi || ''} ${item.paket || ''} ${item.teknisi || ''}`
                            .toLowerCase();
                        if (!searchStr.includes(q)) return false;
                    }
                    sumCost += (item.total_harga_val || 0);
                    return true;
                });

                $('#modalSummaryTotalCost').text('Rp ' + Math.round(sumCost).toLocaleString('id-ID'));

                modalCurrentPage = 1;
                renderModalPage();
            }

            function renderModalPage() {
                const total = filteredModalDetails.length;
                const totalPages = Math.ceil(total / modalPageSize) || 1;
                if (modalCurrentPage > totalPages) modalCurrentPage = totalPages;
                if (modalCurrentPage < 1) modalCurrentPage = 1;

                const startIdx = (modalCurrentPage - 1) * modalPageSize;
                const endIdx = Math.min(startIdx + modalPageSize, total);
                const pageItems = filteredModalDetails.slice(startIdx, endIdx);

                if (total === 0) {
                    $('#modalTableBody').html(
                        '<tr><td colspan="12" class="text-center py-5 text-muted">Tidak ada transaksi material yang sesuai.</td></tr>'
                    );
                    $('#modalTableInfo').text('Menampilkan 0 data');
                    $('#modalPagination').empty();
                    return;
                }

                let rowsHtml = '';
                pageItems.forEach((item, idx) => {
                    const rowNo = startIdx + idx + 1;
                    const katBadge = item.kategori === 'Kebutuhan' ?
                        `<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Kebutuhan</span>` :
                        `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Penggantian</span>`;

                    let pktBadge = '<span class="badge bg-secondary-subtle text-secondary">-</span>';
                    if (item.paket && item.paket !== '-') {
                        if (item.paket.toLowerCase().includes('korektif')) {
                            pktBadge = `<span class="badge bg-danger-subtle text-danger">Korektif</span>`;
                        } else {
                            const displayText = ['a', 'b', 'c', 'd', 'z'].includes(item.paket
                                    .toLowerCase()) ?
                                `Paket ${item.paket.toUpperCase()}` :
                                item.paket;
                            pktBadge = `<span class="badge bg-info-subtle text-info">${displayText}</span>`;
                        }
                    }

                    const hargaBadge = item.harga !== '-' ?
                        `<span class="text-muted small">${item.harga}</span>` :
                        '<span class="text-muted">—</span>';
                    const totalHargaBadge = item.total_harga !== '-' ?
                        `<span class="fw-bold text-danger">${item.total_harga}</span>` :
                        '<span class="text-muted">—</span>';

                    rowsHtml += `
                        <tr>
                            <td class="ps-3 text-secondary fw-semibold">${rowNo}</td>
                            <td class="fw-medium">${item.tanggal}</td>
                            <td><span class="text-muted">${item.jenis_mtc}</span></td>
                            <td>${pktBadge}</td>
                            <td>${katBadge}</td>
                            <td><span class="font-monospace text-muted">${item.mid}</span></td>
                            <td><span class="fw-semibold text-dark">${item.deskripsi}</span></td>
                            <td class="text-end fw-bold text-primary">${item.qty.toLocaleString('id-ID')}</td>
                            <td><span class="badge bg-light text-secondary border">${item.satuan}</span></td>
                            <td class="text-end">${hargaBadge}</td>
                            <td class="text-end">${totalHargaBadge}</td>
                            <td class="pe-3"><span class="small fw-medium text-dark">${item.teknisi}</span></td>
                        </tr>
                    `;
                });

                $('#modalTableBody').html(rowsHtml);
                $('#modalTableInfo').text(`Menampilkan ${startIdx + 1} - ${endIdx} dari ${total} transaksi`);

                let pagHtml = `<li class="page-item ${modalCurrentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="changeModalPage(${modalCurrentPage - 1})">‹</a>
                </li>`;

                for (let p = 1; p <= totalPages; p++) {
                    if (p === 1 || p === totalPages || (p >= modalCurrentPage - 2 && p <= modalCurrentPage + 2)) {
                        pagHtml += `<li class="page-item ${p === modalCurrentPage ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0)" onclick="changeModalPage(${p})">${p}</a>
                        </li>`;
                    } else if (p === modalCurrentPage - 3 || p === modalCurrentPage + 3) {
                        pagHtml += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
                    }
                }

                pagHtml += `<li class="page-item ${modalCurrentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="changeModalPage(${modalCurrentPage + 1})">›</a>
                </li>`;

                $('#modalPagination').html(pagHtml);
            }

            window.changeModalPage = function(page) {
                modalCurrentPage = page;
                renderModalPage();
            };

        });
    </script>
@endsection
