@extends('layouts.app')

@section('content')
    <div class="page-content">
        <div class="container-fluid pb-5">

            {{-- HEADER --}}
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
                <div>
                    <span
                        style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:4px 12px;border-radius:20px;background:rgba(37,99,235,.08);color:#2563eb;border:1px solid rgba(37,99,235,.15);display:inline-block;">
                        Machine Ledger &amp; Maintenance
                    </span>
                    <h3 class="fw-bold fs-3 mb-1 mt-2" style="letter-spacing:-0.5px;">Dashboard Material &amp; Machine Ledger</h3>
                    <p class="text-secondary small mb-0 fw-medium">
                        Ledger pengerjaan maintenance serta analisis kebutuhan dan penggantian material per mesin / unit &mdash;
                        <strong>{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</strong>
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <ol class="breadcrumb m-0 bg-transparent">
                        <li class="breadcrumb-item"><a href="javascript: void(0);" class="text-secondary">Dashboards</a></li>
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
                                            <span class="input-group-text border-0 bg-transparent"><i
                                                    class="ri-calendar-2-line"></i></span>
                                            <input type="text" id="startDate" name="start_date"
                                                class="form-control border-0 flatpickr-input"
                                                placeholder="Pilih tanggal mulai">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-muted">TANGGAL SELESAI</label>
                                        <div class="input-group border rounded">
                                            <span class="input-group-text border-0 bg-transparent"><i
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
                                            <option value="Maintenance">Maintenance</option>
                                            <option value="Korektif">Korektif</option>
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

            <!-- Stats Cards Row (4 KPI Cards) -->
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
                                    <h6 class="mb-1 text-muted fw-semibold text-uppercase tracking-wide small">Total Kebutuhan (Qty)</h6>
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
                                    <h6 class="mb-1 text-muted fw-semibold text-uppercase tracking-wide small">Total Penggantian (Qty)</h6>
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
                                    <h6 class="mb-1 text-muted fw-semibold text-uppercase tracking-wide small">Item Material Unik</h6>
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
                                    <h6 class="mb-1 text-muted fw-semibold text-uppercase tracking-wide small">Total Pekerjaan MTC</h6>
                                    <h4 class="mb-0 fw-bold" id="valTotalJobs">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row: 2 Top 10 Charts (Kebutuhan & Penggantian) -->
            <div class="row g-4 mb-4">
                <!-- Top 10 Kebutuhan Material Bar Chart -->
                <div class="col-lg-6 col-12">
                    <div class="card chart-card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-bottom-0 pb-0">
                            <h5 class="card-title mb-1 fw-semibold text-primary">
                                <i class="ri-bar-chart-fill me-2"></i>Top 10 Kebutuhan Material
                            </h5>
                            <p class="text-muted small mb-0">Material yang paling banyak diajukan kebutuhannya</p>
                        </div>
                        <div class="card-body">
                            <div id="chartTopKebutuhan" style="min-height: 350px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Top 10 Penggantian Material Bar Chart -->
                <div class="col-lg-6 col-12">
                    <div class="card chart-card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-bottom-0 pb-0">
                            <h5 class="card-title mb-1 fw-semibold text-success">
                                <i class="ri-bar-chart-2-fill me-2"></i>Top 10 Penggantian Material
                            </h5>
                            <p class="text-muted small mb-0">Material yang paling banyak terealisasi diganti</p>
                        </div>
                        <div class="card-body">
                            <div id="chartTopPenggantian" style="min-height: 350px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Machine Ledger Summary Table -->
            <div class="row">
                <div class="col-md-12 col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0 p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">
                                    <i class="ri-database-2-line text-primary me-2"></i>Ledger Material per Mesin / Unit / Area
                                </h5>
                                <p class="text-muted mb-0 small">Ringkasan akumulasi kebutuhan & penggantian material per unit mesin</p>
                            </div>
                            <div>
                                <input type="text" id="tableLedgerSearch" class="form-control form-control-sm border"
                                    placeholder="Cari mesin / unit / jenis..." style="width: 260px;">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="tableLedger" class="table table-hover align-middle mb-0 w-100" style="font-size: 13px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4" style="width: 60px;">NO</th>
                                            <th style="width: 140px;">KODE MESIN</th>
                                            <th>MESIN / UNIT / AREA</th>
                                            <th>JENIS MAINTENANCE</th>
                                            <th>LOKASI</th>
                                            <th class="text-end">TOTAL KEBUTUHAN</th>
                                            <th class="text-end">TOTAL PENGGANTIAN</th>
                                            <th class="text-center">PEKERJAAN</th>
                                            <th class="text-center pe-4" style="width: 110px;">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableLedgerBody">
                                        <!-- Loaded dynamically via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                            <!-- Ledger Table Pagination Controls -->
                            <div class="d-flex justify-content-between align-items-center p-3 border-top flex-wrap gap-2" id="ledgerPaginationWrap">
                                <div class="text-muted small" id="ledgerTableInfo">Menampilkan 0 data</div>
                                <ul class="pagination pagination-sm mb-0" id="ledgerPagination">
                                    <!-- Pagination items generated dynamically -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ════════════════════ MODAL DETAIL PENGERJAAN MATERIAL ════════════════════ -->
    <div class="modal fade" id="modalDetailMaterial" tabindex="-1" aria-labelledby="modalDetailMaterialLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-1" id="modalDetailMaterialLabel">
                            <i class="ri-file-list-3-line text-primary me-2"></i>Detail Transaksi Material: <span id="modalMachineName" class="text-primary"></span>
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
                            <button type="button" class="btn btn-outline-primary" data-cat="Kebutuhan">Kebutuhan</button>
                            <button type="button" class="btn btn-outline-success" data-cat="Penggantian">Penggantian</button>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" id="modalSearch" class="form-control form-control-sm border"
                                placeholder="Cari material, MID, teknisi..." style="width: 250px;">
                        </div>
                    </div>

                    <!-- Clean Vanilla HTML Table (No DataTables) -->
                    <div class="table-responsive border rounded mb-3" style="max-height: 460px;">
                        <table class="table table-hover table-striped align-middle mb-0" style="font-size: 12.5px;">
                            <thead class="table-light sticky-top" style="z-index: 2;">
                                <tr>
                                    <th class="ps-3" style="width: 50px;">NO</th>
                                    <th style="width: 110px;">TANGGAL</th>
                                    <th>JENIS</th>
                                    <th style="width: 100px;">PAKET</th>
                                    <th style="width: 110px;">KATEGORI</th>
                                    <th style="width: 90px;">MID</th>
                                    <th>DESKRIPSI MATERIAL</th>
                                    <th class="text-end" style="width: 80px;">QTY</th>
                                    <th style="width: 70px;">SATUAN</th>
                                    <th class="text-center" style="width: 70px;">HARGA</th>
                                    <th class="pe-3" style="width: 110px;">TEKNISI</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableBody">
                                <!-- Loaded dynamically via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal Custom Pagination Controls -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2" id="modalPaginationWrap">
                        <div class="text-muted small" id="modalTableInfo">Menampilkan 0 data</div>
                        <ul class="pagination pagination-sm mb-0" id="modalPagination">
                            <!-- Page numbers -->
                        </ul>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4 border-top">
                    <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
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
                defaultDate: new Date(new Date().setDate(new Date().getDate() - 30))
            });
            flatpickr("#endDate", {
                dateFormat: "Y-m-d",
                defaultDate: new Date()
            });

            let chartKebutuhan = null;
            let chartPenggantian = null;

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
                            $('#valTotalQtyKeb').text((res.summary.total_qty_kebutuhan || 0).toLocaleString('id-ID'));
                            $('#valTotalQtyPeng').text((res.summary.total_qty_penggantian || 0).toLocaleString('id-ID'));
                            $('#valUniqueItems').text((res.summary.unique_items || 0).toLocaleString('id-ID'));
                            $('#valTotalJobs').text((res.summary.total_jobs || 0).toLocaleString('id-ID'));

                            renderTopKebutuhanChart(res.charts.top_kebutuhan || []);
                            renderTopPenggantianChart(res.charts.top_penggantian || []);
                        }
                    },
                    error: function(err) {
                        console.error("Dashboard charts error:", err);
                    }
                });
            }

            // Top 10 Kebutuhan Chart (Horizontal Bar)
            function renderTopKebutuhanChart(data) {
                const container = document.querySelector("#chartTopKebutuhan");
                if (!data || data.length === 0) {
                    if (chartKebutuhan) { chartKebutuhan.destroy(); chartKebutuhan = null; }
                    container.innerHTML = '<div class="text-center p-5 text-muted small">Tidak ada data kebutuhan material pada periode ini</div>';
                    return;
                }
                const categories = data.map(item => item.label || 'Tanpa Deskripsi');
                const seriesData = data.map(item => item.qty);

                const options = {
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: false }
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
                        formatter: function(val) {
                            return val.toLocaleString('id-ID');
                        },
                        style: {
                            colors: ['#fff'],
                            fontSize: '11px',
                            fontWeight: 700
                        }
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            formatter: function(val) {
                                return Math.floor(val);
                            }
                        }
                    },
                    grid: { borderColor: '#f1f5f9' },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val.toLocaleString('id-ID') + ' Qty';
                            }
                        }
                    }
                };

                if (chartKebutuhan) chartKebutuhan.destroy();
                chartKebutuhan = new ApexCharts(container, options);
                chartKebutuhan.render();
            }

            // Top 10 Penggantian Chart (Horizontal Bar)
            function renderTopPenggantianChart(data) {
                const container = document.querySelector("#chartTopPenggantian");
                if (!data || data.length === 0) {
                    if (chartPenggantian) { chartPenggantian.destroy(); chartPenggantian = null; }
                    container.innerHTML = '<div class="text-center p-5 text-muted small">Tidak ada data penggantian material pada periode ini</div>';
                    return;
                }
                const categories = data.map(item => item.label || 'Tanpa Deskripsi');
                const seriesData = data.map(item => item.qty);

                const options = {
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: false }
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
                        formatter: function(val) {
                            return val.toLocaleString('id-ID');
                        },
                        style: {
                            colors: ['#fff'],
                            fontSize: '11px',
                            fontWeight: 700
                        }
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            formatter: function(val) {
                                return Math.floor(val);
                            }
                        }
                    },
                    grid: { borderColor: '#f1f5f9' },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val.toLocaleString('id-ID') + ' Qty';
                            }
                        }
                    }
                };

                if (chartPenggantian) chartPenggantian.destroy();
                chartPenggantian = new ApexCharts(container, options);
                chartPenggantian.render();
            }

            // ─────────────────────────────────────────────────────────────
            // 2. LOAD MACHINE LEDGER TABLE DATA
            // ─────────────────────────────────────────────────────────────
            function loadMachineLedgerData() {
                const formData = $('#filterForm').serialize();
                $('#tableLedgerBody').html('<tr><td colspan="9" class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat data ledger mesin...</td></tr>');

                $.ajax({
                    url: "{{ route('mtc.dashboard.material.ledger') }}",
                    type: "GET",
                    data: formData,
                    success: function(res) {
                        if (res.status === 200) {
                            rawLedgerData = res.data || [];
                            filterLedgerTable();
                        } else {
                            $('#tableLedgerBody').html('<tr><td colspan="9" class="text-center py-5 text-danger">Gagal memuat data.</td></tr>');
                        }
                    },
                    error: function(err) {
                        $('#tableLedgerBody').html('<tr><td colspan="9" class="text-center py-5 text-danger">Terjadi kesalahan saat memuat data ledger.</td></tr>');
                    }
                });
            }

            // Filter & Search Ledger Table locally
            function filterLedgerTable() {
                const q = $('#tableLedgerSearch').val().trim().toLowerCase();
                if (!q) {
                    filteredLedgerData = rawLedgerData.slice();
                } else {
                    filteredLedgerData = rawLedgerData.filter(item => {
                        const searchStr = `${item.kode_mesin || ''} ${item.nama_mesin || ''} ${item.jenis_mtc || ''} ${item.lokasi || ''}`.toLowerCase();
                        return searchStr.includes(q);
                    });
                }
                ledgerCurrentPage = 1;
                renderLedgerTablePage();
            }

            // Render paginated slice of Ledger Table
            function renderLedgerTablePage() {
                const total = filteredLedgerData.length;
                const totalPages = Math.ceil(total / ledgerPageSize) || 1;
                if (ledgerCurrentPage > totalPages) ledgerCurrentPage = totalPages;
                if (ledgerCurrentPage < 1) ledgerCurrentPage = 1;

                const startIdx = (ledgerCurrentPage - 1) * ledgerPageSize;
                const endIdx = Math.min(startIdx + ledgerPageSize, total);
                const pageItems = filteredLedgerData.slice(startIdx, endIdx);

                if (total === 0) {
                    $('#tableLedgerBody').html('<tr><td colspan="9" class="text-center py-5 text-muted">Tidak ada data ledger mesin yang sesuai.</td></tr>');
                    $('#ledgerTableInfo').text('Menampilkan 0 data');
                    $('#ledgerPagination').empty();
                    return;
                }

                let rowsHtml = '';
                pageItems.forEach((item, idx) => {
                    const rowNo = startIdx + idx + 1;
                    const mainIdsStr = (item.main_ids || []).join(',');
                    const kebBadge = item.total_kebutuhan_qty > 0 
                        ? `<span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">${item.total_kebutuhan_qty.toLocaleString('id-ID')}</span>`
                        : `<span class="text-muted">—</span>`;
                    const pengBadge = item.total_penggantian_qty > 0 
                        ? `<span class="badge bg-success-subtle text-success fw-bold px-2 py-1">${item.total_penggantian_qty.toLocaleString('id-ID')}</span>`
                        : `<span class="text-muted">—</span>`;

                    rowsHtml += `
                        <tr>
                            <td class="ps-4 text-secondary fw-semibold">${rowNo}</td>
                            <td>
                                <span class="badge bg-light text-dark font-monospace border">${item.kode_mesin || '—'}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">${item.nama_mesin}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">${item.jenis_mtc || '-'}</span>
                            </td>
                            <td class="text-muted small">${item.lokasi || '-'}</td>
                            <td class="text-end">${kebBadge}</td>
                            <td class="text-end">${pengBadge}</td>
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

                // Build Pagination Buttons
                let pagHtml = '';
                pagHtml += `<li class="page-item ${ledgerCurrentPage === 1 ? 'disabled' : ''}">
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

            $('#tableLedgerSearch').on('input', function() {
                filterLedgerTable();
            });

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

                $('#modalTableBody').html('<tr><td colspan="11" class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat rincian transaksi material...</td></tr>');
                $('#modalDetailMaterial').modal('show');

                $.ajax({
                    url: "{{ route('mtc.dashboard.material.machine-details') }}",
                    type: "GET",
                    data: { main_ids: mainIds },
                    success: function(res) {
                        if (res.status === 200) {
                            rawModalDetails = res.data || [];
                            filterModalDetails();
                        } else {
                            $('#modalTableBody').html('<tr><td colspan="11" class="text-center py-5 text-danger">Gagal mengambil data transaksi.</td></tr>');
                        }
                    },
                    error: function(err) {
                        $('#modalTableBody').html('<tr><td colspan="11" class="text-center py-5 text-danger">Terjadi kesalahan saat memuat rincian.</td></tr>');
                    }
                });
            });

            // Filter modal category (All / Kebutuhan / Penggantian)
            $('#modalKategoriFilter').on('click', 'button', function() {
                $('#modalKategoriFilter button').removeClass('active');
                $(this).addClass('active');
                modalActiveCategory = $(this).data('cat') || 'all';
                filterModalDetails();
            });

            $('#modalSearch').on('input', function() {
                filterModalDetails();
            });

            function filterModalDetails() {
                const q = $('#modalSearch').val().trim().toLowerCase();
                filteredModalDetails = rawModalDetails.filter(item => {
                    // Category filter
                    if (modalActiveCategory !== 'all' && item.kategori !== modalActiveCategory) {
                        return false;
                    }
                    // Search filter
                    if (q) {
                        const searchStr = `${item.tanggal || ''} ${item.mid || ''} ${item.deskripsi || ''} ${item.paket || ''} ${item.teknisi || ''}`.toLowerCase();
                        if (!searchStr.includes(q)) return false;
                    }
                    return true;
                });

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
                    $('#modalTableBody').html('<tr><td colspan="11" class="text-center py-5 text-muted">Tidak ada transaksi material yang sesuai.</td></tr>');
                    $('#modalTableInfo').text('Menampilkan 0 data');
                    $('#modalPagination').empty();
                    return;
                }

                let rowsHtml = '';
                pageItems.forEach((item, idx) => {
                    const rowNo = startIdx + idx + 1;
                    const katBadge = item.kategori === 'Kebutuhan' 
                        ? `<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Kebutuhan</span>`
                        : `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Penggantian</span>`;
                    const pktBadge = item.paket && item.paket.toLowerCase().includes('korektif')
                        ? `<span class="badge bg-danger-subtle text-danger">Korektif</span>`
                        : `<span class="badge bg-info-subtle text-info">Maintenance</span>`;

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
                            <td class="text-center text-muted">${item.harga}</td>
                            <td class="pe-3"><span class="small fw-medium text-dark">${item.teknisi}</span></td>
                        </tr>
                    `;
                });

                $('#modalTableBody').html(rowsHtml);
                $('#modalTableInfo').text(`Menampilkan ${startIdx + 1} - ${endIdx} dari ${total} transaksi`);

                // Pagination links in Modal
                let pagHtml = '';
                pagHtml += `<li class="page-item ${modalCurrentPage === 1 ? 'disabled' : ''}">
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

            // ─────────────────────────────────────────────────────────────
            // 4. FILTER ACTIONS (Filter & Reset Buttons)
            // ─────────────────────────────────────────────────────────────
            $('#btnFilter').click(function() {
                loadDashboardCharts();
                loadMachineLedgerData();
            });

            $('#btnReset').click(function() {
                $('#filterForm')[0].reset();
                document.getElementById('startDate')._flatpickr.setDate(new Date(new Date().setDate(new Date().getDate() - 30)));
                document.getElementById('endDate')._flatpickr.setDate(new Date());
                $('#tableLedgerSearch').val('');

                loadDashboardCharts();
                loadMachineLedgerData();
            });

            // Initial Load
            loadDashboardCharts();
            loadMachineLedgerData();
        });
    </script>
@endsection
