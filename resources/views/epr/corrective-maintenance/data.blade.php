@extends('layouts.app')

@section('title', 'Corrective Maintenance History')

@section('styles')
<style>
    .badge-status {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        letter-spacing: .4px;
        text-transform: uppercase;
    }
    .badge-em-elec { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .badge-em-mech { background: rgba(139, 92, 246, 0.12); color: #8b5cf6; }
    .badge-ampm { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    
    .card-stat {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s;
    }
    .card-stat:hover {
        transform: translateY(-2px);
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-database-2-line me-2 text-primary"></i>
                    EPR — Riwayat Laporan Corrective Maintenance
                </h4>
                <p class="text-muted mb-0 fs-13">Kelola, tinjau, dan perbarui log corrective maintenance & downtime mesin</p>
            </div>
            <div class="d-flex gap-2">
                <button onclick="triggerImport()" class="btn btn-info d-flex align-items-center gap-1 shadow-sm">
                    <i class="ri-upload-2-line"></i> Import Excel
                </button>
                <button onclick="exportToExcel()" class="btn btn-success d-flex align-items-center gap-1 shadow-sm">
                    <i class="ri-file-excel-2-line"></i> Export Excel
                </button>
                @if(in_array(Auth::user()->jabatan, ['operator', 'foreman', 'supervisor', 'admin']))
                <a href="{{ route('epr.cm.form') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                    <i class="ri-add-line"></i> Buat Laporan Baru
                </a>
                @endif
            </div>
        </div>
        <input type="file" id="importExcelInput" accept=".xlsx, .xls" style="display:none;" onchange="handleExcelUpload(event)">

        {{-- Stats Widget --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card card-stat bg-white text-dark p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fs-12 uppercase mb-2 fw-bold">TOTAL LAPORAN</h6>
                            <h3 class="fw-bold mb-0" id="stat-total">0</h3>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-primary rounded-circle fs-3 text-primary p-2" style="background: rgba(59,130,246,.12);">
                                <i class="ri-file-list-3-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-stat bg-white text-dark p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fs-12 uppercase mb-2 fw-bold">TOTAL DOWNTIME (MENIT)</h6>
                            <h3 class="fw-bold mb-0 text-danger" id="stat-downtime">0 m</h3>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-danger rounded-circle fs-3 text-danger p-2" style="background: rgba(239,68,68,.12);">
                                <i class="ri-time-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-stat bg-white text-dark p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fs-12 uppercase mb-2 fw-bold">ELECTRICAL DT</h6>
                            <h3 class="fw-bold mb-0 text-info" id="stat-elec">0</h3>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-info rounded-circle fs-3 text-info p-2" style="background: rgba(6,182,212,.12);">
                                <i class="ri-flashlight-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-stat bg-white text-dark p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fs-12 uppercase mb-2 fw-bold">MECHANICAL DT</h6>
                            <h3 class="fw-bold mb-0 text-violet" id="stat-mech">0</h3>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-violet rounded-circle fs-3 text-violet p-2" style="background: rgba(139,92,246,.12);">
                                <i class="ri-settings-4-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center">
                    <div class="col-xl-2 col-md-4">
                        <label class="form-label small fw-semibold mb-1 text-muted">Cari</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" class="form-control" id="filter-search" placeholder="Cari mesin, jenis DT...">
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4">
                        <label class="form-label small fw-semibold mb-1 text-muted">Tanggal Harian</label>
                        <input type="date" class="form-control form-control-sm" id="filter-date">
                    </div>
                    <div class="col-xl-2 col-md-4">
                        <label class="form-label small fw-semibold mb-1 text-muted">Bulan</label>
                        <input type="month" class="form-control form-control-sm" id="filter-month" value="{{ date('Y-m') }}">
                    </div>
                    <div class="col-xl-1 col-md-2">
                        <label class="form-label small fw-semibold mb-1 text-muted">Week</label>
                        <select class="form-select form-select-sm" id="filter-week">
                            <option value="">Semua</option>
                            <option value="1">W1</option>
                            <option value="2">W2</option>
                            <option value="3">W3</option>
                            <option value="4">W4</option>
                            <option value="5">W5</option>
                        </select>
                    </div>
                    <div class="col-xl-1 col-md-2">
                        <label class="form-label small fw-semibold mb-1 text-muted">Shift</label>
                        <select class="form-select form-select-sm" id="filter-shift">
                            <option value="">Semua</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="col-xl-1 col-md-2">
                        <label class="form-label small fw-semibold mb-1 text-muted">Kategori</label>
                        <select class="form-select form-select-sm" id="filter-pouchSachet">
                            <option value="">Semua</option>
                            <option value="Pouch">Pouch</option>
                            <option value="Sachet">Sachet</option>
                        </select>
                    </div>
                    <div class="col-xl-1 col-md-2">
                        <label class="form-label small fw-semibold mb-1 text-muted">E / M</label>
                        <select class="form-select form-select-sm" id="filter-em">
                            <option value="">Semua</option>
                            <option value="Electrical">Elec</option>
                            <option value="Mechanical">Mech</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-4 d-flex align-items-end gap-1">
                        <button class="btn btn-primary btn-sm px-2 w-100 d-flex align-items-center justify-content-center gap-1" onclick="applyFilters()" title="Filter">
                            <i class="ri-filter-3-line"></i> <span>Filter</span>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm px-2 w-100 d-flex align-items-center justify-content-center gap-1" onclick="resetFilters()" title="Reset">
                            <i class="ri-refresh-line"></i> <span>Reset</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Tabs --}}
        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#tab-detail-log" role="tab">
                    <i class="ri-file-list-3-line me-1"></i> Data Log Corrective Maintenance
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#tab-summary-dt" role="tab">
                    <i class="ri-bar-chart-grouped-line me-1"></i> Frekuensi DT by Jenis DT (Per Week)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#tab-machine-group" role="tab">
                    <i class="ri-pie-chart-line me-1"></i> Jumlah DT Mesin / Grup (MTBF & Availability)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#tab-loading-time" role="tab">
                    <i class="ri-table-line me-1"></i> Downtime & Loading Time (Pouch & Sachet)
                </a>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Tab 1: Detail Log --}}
            <div class="tab-pane active" id="tab-detail-log" role="tabpanel">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-4 bg-white">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0" id="tableData">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60" class="text-center">NO</th>
                                        <th width="120">TANGGAL</th>
                                        <th width="100">SHIFT/GRUP</th>
                                        <th width="90">MESIN</th>
                                        <th width="110">POUCH/SACHET</th>
                                        <th>KETERANGAN DOWNTIME</th>
                                        <th width="180">JENIS DT</th>
                                        <th width="100" class="text-center">DOWNTIME</th>
                                        <th width="150" class="text-center">KLASIFIKASI</th>
                                        <th width="140" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    {{-- Dynamically filled --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top d-none" id="pagination-container">
                            <div class="text-muted small">
                                Showing <span id="page-start" class="fw-semibold">0</span> to <span id="page-end" class="fw-semibold">0</span> of <span id="page-total" class="fw-semibold">0</span> entries
                            </div>
                            <nav>
                                <ul class="pagination pagination-rounded pagination-sm justify-content-end mb-0" id="pagination-links">
                                    <!-- Dynamic pagination links -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 2: Frekuensi DT by Jenis DT --}}
            <div class="tab-pane" id="tab-summary-dt" role="tabpanel">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-uppercase text-dark mb-0">
                                <i class="ri-bar-chart-2-line text-success me-2"></i>FREKUENSI DT BY JENIS DT ALL MESIN
                            </h5>
                            <span class="badge bg-soft-success text-success px-3 py-2 fs-12 fw-semibold" id="summary-period-badge">
                                Periode: -
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tableSummary">
                                <thead class="table-success text-dark align-middle">
                                    <tr>
                                        <th width="110" class="text-center">TANGGAL</th>
                                        <th>JENIS DT</th>
                                        <th width="150" class="text-center">Count of JENIS</th>
                                        <th width="170" class="text-center">Sum of TOTAL MENIT</th>
                                        <th width="120" class="text-center">MTTR</th>
                                        <th width="120" class="text-center">Persen</th>
                                    </tr>
                                </thead>
                                <tbody id="tableSummaryBody">
                                    {{-- Dynamically filled --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 3: Jumlah DT Mesin / Grup --}}
            <div class="tab-pane" id="tab-machine-group" role="tabpanel">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-uppercase text-dark mb-0">
                                <i class="ri-dashboard-3-line text-warning me-2"></i>JUMLAH DT MESIN / GROUP (MTBF & AVAILABILITY)
                            </h5>
                            <span class="badge bg-soft-warning text-warning px-3 py-2 fs-12 fw-semibold" id="machine-period-badge">
                                Periode: -
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tableMachineGroup">
                                <thead class="table-light text-dark align-middle text-center">
                                    <tr>
                                        <th width="120" style="background-color: #fde047; color: #713f12;">UNIT MESIN</th>
                                        <th width="100" style="background-color: #38bdf8; color: #0c4a6e;">A</th>
                                        <th width="100" style="background-color: #fde047; color: #713f12;">B</th>
                                        <th width="100" style="background-color: #4ade80; color: #14532d;">C</th>
                                        <th width="150" style="background-color: #fef08a; color: #713f12;">JUMLAH / MTTR</th>
                                        <th width="120" style="background-color: #e9d5ff; color: #581c87;">MTBF (x)</th>
                                        <th width="150" style="background-color: #f472b6; color: #831843;">MTBF (MENIT)</th>
                                        <th width="130" style="background-color: #c084fc; color: #581c87;">Availability</th>
                                    </tr>
                                </thead>
                                <tbody id="tableMachineGroupBody">
                                    {{-- Dynamically filled --}}
                                </tbody>
                                <tfoot class="fw-bold" style="background-color: #fffbeb;">
                                    <tr>
                                        <td class="text-center" style="background-color: #fde047;">JUMLAH</td>
                                        <td class="text-center font-monospace" style="background-color: #38bdf8;" id="total-group-a">0</td>
                                        <td class="text-center font-monospace" style="background-color: #fde047;" id="total-group-b">0</td>
                                        <td class="text-center font-monospace" style="background-color: #4ade80;" id="total-group-c">0</td>
                                        <td class="text-center font-monospace" style="background-color: #fef08a;" id="total-group-all">0</td>
                                        <td class="text-center font-monospace" id="total-mtbf-x">0.00</td>
                                        <td class="text-center font-monospace text-muted" id="total-mtbf-menit">#DIV/0!</td>
                                        <td class="text-center font-monospace text-muted" id="total-availability">-</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 4: Downtime & Loading Time Matrix --}}
            <div class="tab-pane" id="tab-loading-time" role="tabpanel">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="fw-bold text-uppercase text-dark mb-1">
                                    <i class="ri-dashboard-fill text-primary me-2"></i>REKAPAN DOWNTIME & LOADING TIME (POUCH & SACHET)
                                </h5>
                                <p class="text-muted fs-13 mb-0">Analisis Loading Time kerja mesin, Realisasi Downtime (menit), dan Persentase Downtime (%) per Grup A, B, C & ALL.</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <label class="small fw-semibold text-muted mb-0">Tanggal Harian:</label>
                                <input type="date" class="form-control form-control-sm" id="filter-lt-date" style="width: 155px;">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearLtDate()" title="Tampilkan Semua Tanggal">
                                    Semua
                                </button>
                                <span class="badge bg-soft-primary text-primary px-3 py-2 fs-12 fw-semibold" id="lt-period-badge">
                                    Periode: -
                                </span>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0 text-center" id="tableLoadingTime">
                                <thead>
                                    <tr>
                                        <th colspan="2" style="background-color: #bbf7d0; color: #166534; font-weight: bold; font-size: 14px;">DOWNTIME</th>
                                        <th colspan="3" style="background-color: #bfdbfe; color: #1e40af; font-weight: bold; font-size: 14px;">A</th>
                                        <th colspan="3" style="background-color: #bbf7d0; color: #166534; font-weight: bold; font-size: 14px;">B</th>
                                        <th colspan="3" style="background-color: #fecaca; color: #991b1b; font-weight: bold; font-size: 14px;">C</th>
                                        <th colspan="3" style="background-color: #f3f4f6; color: #1f2937; font-weight: bold; font-size: 14px;">ALL</th>
                                    </tr>
                                    <tr style="font-size: 12px;">
                                        <th width="90" style="background-color: #f8fafc;">LINE</th>
                                        <th width="80" style="background-color: #f8fafc;">MESIN</th>
                                        {{-- Group A --}}
                                        <th width="95" style="background-color: #dcfce7; color: #14532d;">Loading Time</th>
                                        <th width="75">DT</th>
                                        <th width="75">%</th>
                                        {{-- Group B --}}
                                        <th width="95" style="background-color: #dcfce7; color: #14532d;">Loading Time</th>
                                        <th width="75">DT</th>
                                        <th width="75">%</th>
                                        {{-- Group C --}}
                                        <th width="95" style="background-color: #dcfce7; color: #14532d;">Loading Time</th>
                                        <th width="75">DT</th>
                                        <th width="75">%</th>
                                        {{-- ALL --}}
                                        <th width="105" style="background-color: #dcfce7; color: #14532d;">Loading Time</th>
                                        <th width="80">DT</th>
                                        <th width="85">%</th>
                                    </tr>
                                </thead>
                                <tbody id="tableLoadingTimeBody">
                                    {{-- Dynamically rendered by renderLoadingTimeTable() --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom p-3">
                <h5 class="modal-title fw-bold" id="detail-title">Detail Corrective Maintenance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detail-body">
                {{-- Dynamically filled --}}
            </div>
            <div class="modal-footer border-top p-3" id="detail-footer">
                {{-- Dynamically filled --}}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let reports = [];
    let loadingTimes = [];
    let filteredReports = [];
    let currentPage = 1;
    let isFirstLoad = true;
    const pageSize = 15;

    function loadReports() {
        const month = $('#filter-month').val() || '';
        $.ajax({
            url: "{{ route('epr.cm.get-reports') }}",
            method: 'GET',
            data: { month: month },
            success: function(res) {
                reports = res;
                loadLoadingTimes();
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat riwayat data laporan', 'error');
            }
        });
    }

    function loadLoadingTimes() {
        const month = $('#filter-month').val() || '';
        $.ajax({
            url: "{{ route('epr.cm.loading-times') }}",
            method: 'GET',
            data: { month: month },
            success: function(res) {
                loadingTimes = res;
                // On initial load, set default date to latest available date
                if (isFirstLoad) {
                    isFirstLoad = false;
                    if (!$('#filter-date').val() && !$('#filter-lt-date').val()) {
                        const availDates = [...new Set(loadingTimes.map(lt => lt.tanggal).concat(reports.map(r => r.tanggal)).filter(Boolean))].sort();
                        if (availDates.length) {
                            const latestDate = availDates[availDates.length - 1];
                            $('#filter-date').val(latestDate);
                            $('#filter-lt-date').val(latestDate);
                        }
                    }
                }
                applyFiltersListOnly();
            },
            error: function() {
                loadingTimes = [];
                applyFiltersListOnly();
            }
        });
    }

    window.clearLtDate = function() {
        $('#filter-date').val('');
        $('#filter-lt-date').val('');
        applyFiltersListOnly();
    };

    function getWeekOfMonth(dateStr) {
        if (!dateStr) return 1;
        const dt = new Date(dateStr + 'T00:00:00');
        const day = dt.getDate();
        const firstDayOfMonth = new Date(dt.getFullYear(), dt.getMonth(), 1);
        let firstDayOfWeek = firstDayOfMonth.getDay(); // 0 = Sun, 1 = Mon ... 6 = Sat
        let offset = (firstDayOfWeek === 0 ? 7 : firstDayOfWeek) - 1;
        return Math.ceil((day + offset) / 7);
    }

    function applyFiltersListOnly() {
        const search = ($('#filter-search').val() || '').trim().toLowerCase();
        const date = $('#filter-date').val() || '';
        const shift = $('#filter-shift').val() || '';
        const pouchSachet = $('#filter-pouchSachet').val() || '';
        const em = $('#filter-em').val() || '';
        const week = $('#filter-week').val() || '';

        let filtered = reports;

        if (date) {
            filtered = filtered.filter(r => r.tanggal === date);
        }
        if (search) {
            filtered = filtered.filter(r => 
                (r.mesin || '').toLowerCase().includes(search) ||
                (r.downtime || '').toLowerCase().includes(search) ||
                (r.keterangan || '').toLowerCase().includes(search) ||
                (r.jenis_dt_name || '').toLowerCase().includes(search)
            );
        }
        if (shift) filtered = filtered.filter(r => String(r.shift) === String(shift));
        if (pouchSachet) filtered = filtered.filter(r => (r.pouch_sachet || '').toLowerCase() === pouchSachet.toLowerCase());
        if (em) filtered = filtered.filter(r => (r.electrical_mechanical || '').toLowerCase() === em.toLowerCase());
        if (week) filtered = filtered.filter(r => getWeekOfMonth(r.tanggal) == week);

        // Stats calculation
        $('#stat-total').text(filtered.length);
        
        const totalDt = filtered.reduce((acc, curr) => acc + (curr.total_menit || 0), 0);
        $('#stat-downtime').text(`${totalDt} m`);

        const elecCount = filtered.filter(r => r.electrical_mechanical === 'Electrical').length;
        $('#stat-elec').text(elecCount);

        const mechCount = filtered.filter(r => r.electrical_mechanical === 'Mechanical').length;
        $('#stat-mech').text(mechCount);

        filteredReports = filtered;
        currentPage = 1;
        renderTable();
        renderSummaryTable();
        renderMachineGroupTable();
        renderLoadingTimeTable();
    }

    window.applyFilters = function() {
        const d = $('#filter-date').val();
        const m = $('#filter-month').val();
        if (d && (!m || d.substring(0, 7) !== m)) {
            $('#filter-month').val(d.substring(0, 7));
            isFirstLoad = false;
            loadReports();
        } else {
            applyFiltersListOnly();
        }
    };

    window.resetFilters = function() {
        $('#filter-search').val('');
        $('#filter-date').val('');
        $('#filter-lt-date').val('');
        $('#filter-shift').val('');
        $('#filter-pouchSachet').val('');
        $('#filter-em').val('');
        $('#filter-week').val('');
        $('#filter-month').val("{{ date('Y-m') }}");
        isFirstLoad = false;
        loadReports();
    };

    window.changePage = function(page) {
        currentPage = page;
        renderTable();
    };

    function renderTable() {
        const tbody = $('#tableBody');
        tbody.empty();

        const totalItems = filteredReports.length;
        const totalPages = Math.ceil(totalItems / pageSize) || 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        const startIdx = (currentPage - 1) * pageSize;
        const endIdx = Math.min(startIdx + pageSize, totalItems);
        const pageList = filteredReports.slice(startIdx, endIdx);

        if (!totalItems) {
            tbody.append('<tr><td colspan="10" class="text-center text-muted py-4"><i class="ri-file-list-3-line fs-2 mb-2 d-block text-muted"></i>Tidak ada data laporan</td></tr>');
            $('#pagination-container').addClass('d-none');
            return;
        }

        $('#pagination-container').removeClass('d-none');

        pageList.forEach((r, i) => {
            const dateStr = formatDate(r.tanggal);
            const globalIndex = startIdx + i + 1;
            const emBadge = r.electrical_mechanical === 'Electrical' 
                ? '<span class="badge-status badge-em-elec">Electrical</span>' 
                : '<span class="badge-status badge-em-mech">Mechanical</span>';

            const amPmBadge = `<span class="badge-status badge-ampm">${r.am_pm}</span>`;

            let actionHtml = `
                <div class="d-flex gap-1 justify-content-center">
                    <button class="btn btn-sm btn-outline-info btn-detail" onclick="openDetail(${r.id})" title="Detail">
                        <i class="ri-eye-line"></i>
                    </button>
                    @if(in_array(Auth::user()->jabatan, ['operator', 'foreman', 'supervisor', 'admin']))
                    <button class="btn btn-sm btn-outline-warning" onclick="editReport(${r.id})" title="Edit">
                        <i class="ri-edit-line"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteReport(${r.id})" title="Hapus">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                    @endif
                </div>
            `;

            tbody.append(`
                <tr data-id="${r.id}">
                    <td class="text-center font-monospace">${globalIndex}</td>
                    <td class="fw-semibold">${dateStr}</td>
                    <td class="text-center">${r.shift} / ${r.grup}</td>
                    <td class="text-center"><span class="badge bg-light text-dark fw-bold px-2">${r.mesin}</span></td>
                    <td>${r.pouch_sachet}</td>
                    <td style="max-width:240px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${esc(r.downtime || '')}">
                        ${esc(r.downtime || '—')}
                    </td>
                    <td><span class="fw-semibold text-primary">${esc(r.jenis_dt_name)}</span></td>
                    <td class="text-center fw-bold text-danger font-monospace">${r.total_menit} m</td>
                    <td class="text-center">${emBadge}<br><div class="mt-1">${amPmBadge}</div></td>
                    <td>${actionHtml}</td>
                </tr>
            `);
        });

        // Update pagination info
        $('#page-start').text(totalItems ? startIdx + 1 : 0);
        $('#page-end').text(endIdx);
        $('#page-total').text(totalItems);

        // Render pagination links
        const paginationLinks = $('#pagination-links');
        paginationLinks.empty();

        // Prev page button
        paginationLinks.append(`
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage - 1})">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            </li>
        `);

        // Page number buttons
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        for (let p = startPage; p <= endPage; p++) {
            paginationLinks.append(`
                <li class="page-item ${currentPage === p ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="changePage(${p})">${p}</a>
                </li>
            `);
        }

        // Next page button
        paginationLinks.append(`
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage + 1})">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            </li>
        `);
    }

    window.openDetail = function(id) {
        const r = reports.find(x => x.id == id);
        if (!r) return;

        $('#detail-title').html(`<i class="ri-tools-line text-primary me-2"></i>Laporan CM Mesin: ${r.mesin} (${formatDate(r.tanggal)})`);

        const emBadge = r.electrical_mechanical === 'Electrical' 
            ? '<span class="badge-status badge-em-elec">Electrical</span>' 
            : '<span class="badge-status badge-em-mech">Mechanical</span>';

        const amPmBadge = `<span class="badge-status badge-ampm">${r.am_pm}</span>`;

        let html = `
            <div class="p-3 bg-light rounded border-start border-primary border-3 mb-4">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted" width="30%">Tanggal</td><td>: <strong>${formatDate(r.tanggal)}</strong></td></tr>
                    <tr><td class="text-muted">Shift / Grup</td><td>: ${r.shift} / ${r.grup}</td></tr>
                    <tr><td class="text-muted">Mesin / Line</td><td>: <span class="badge bg-light text-dark fw-bold px-2">${r.mesin}</span></td></tr>
                    <tr><td class="text-muted">Pouch / Sachet</td><td>: ${r.pouch_sachet}</td></tr>
                    <tr><td class="text-muted">Jam Kerja</td><td>: ${r.jam_mulai} – ${r.jam_selesai}</td></tr>
                    <tr><td class="text-muted">Downtime</td><td>: <strong class="text-danger">${r.total_menit} menit</strong></td></tr>
                    <tr><td class="text-muted">Jenis DT</td><td>: <strong>${esc(r.jenis_dt_name)}</strong></td></tr>
                    <tr><td class="text-muted">Klasifikasi</td><td>: ${emBadge} · ${amPmBadge}</td></tr>
                    <tr><td class="text-muted">Dilaporkan oleh</td><td>: ${esc(r.created_by)} · ${formatDate(r.createdAt.substring(0, 10))}</td></tr>
                </table>
            </div>
            <div class="mb-4">
                <h6 class="fw-bold text-dark mb-2"><i class="ri-error-warning-line text-warning me-1"></i> Keterangan Downtime / Kerusakan:</h6>
                <div class="bg-light p-3 rounded text-dark" style="white-space: pre-wrap;">${esc(r.downtime || 'Tidak ada catatan kerusakan')}</div>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-2"><i class="ri-chat-check-line text-success me-1"></i> Tindakan Perbaikan / Catatan Tambahan:</h6>
                <div class="bg-light p-3 rounded text-dark" style="white-space: pre-wrap;">${esc(r.keterangan || 'Tidak ada catatan perbaikan')}</div>
            </div>
        `;

        $('#detail-body').html(html);

        let footerHtml = '<button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>';
        @if(in_array(Auth::user()->jabatan, ['operator', 'foreman', 'supervisor', 'admin']))
            footerHtml = `<button class="btn btn-warning" onclick="editReport(${r.id})"><i class="ri-edit-line me-1"></i> Edit</button>` + footerHtml;
        @endif
        $('#detail-footer').html(footerHtml);

        $('#modalDetail').modal('show');
    };

    window.editReport = function(id) {
        $('#modalDetail').modal('hide');
        window.location.href = `{{ route('epr.cm.form') }}?edit=${id}`;
    };

    window.exportToExcel = function() {
        const search = $('#filter-search').val() || '';
        const shift = $('#filter-shift').val() || '';
        const pouchSachet = $('#filter-pouchSachet').val() || '';
        const em = $('#filter-em').val() || '';
        const month = $('#filter-month').val() || '';

        let url = "{{ route('epr.cm.export') }}?";
        if (search) url += `search=${encodeURIComponent(search)}&`;
        if (shift) url += `shift=${encodeURIComponent(shift)}&`;
        if (pouchSachet) url += `pouch_sachet=${encodeURIComponent(pouchSachet)}&`;
        if (em) url += `electrical_mechanical=${encodeURIComponent(em)}&`;
        if (month) url += `month=${encodeURIComponent(month)}&`;

        window.open(url, '_blank');
    };

    window.triggerImport = function() {
        $('#importExcelInput').click();
    };

    window.handleExcelUpload = function(event) {
        const file = event.target.files[0];
        if (!file) return;

        const input = event.target;

        Swal.fire({
            title: 'Import Data Corrective Maintenance',
            html: `Apakah Anda yakin ingin mengimpor data dari file <b>"${file.name}"</b>?<br><span class="text-muted fs-12 mt-1 d-block">Mendukung format template <b>TAMPLATE INPUT DT</b> (sheet HARIAN).</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-upload-cloud-2-line me-1"></i> Ya, Import',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const formData = new FormData();
                formData.append('file', file);

                return $.ajax({
                    url: "{{ route('epr.cm.import') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        return res;
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Gagal mengimpor data';
                        Swal.showValidationMessage(msg);
                    }
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            input.value = '';
            if (result.isConfirmed && result.value && result.value.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.value.message,
                });
                loadReports();
            }
        });
    };

    window.deleteReport = function(id) {
        $('#modalDetail').modal('hide');
        Swal.fire({
            title: 'Hapus Laporan CM?',
            text: 'Data laporan yang dihapus tidak bisa dikembalikan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/epr/corrective-maintenance/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'Dihapus!', text: 'Laporan berhasil dihapus', timer: 1500, showConfirmButton: false });
                            loadReports();
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus laporan', 'error');
                    }
                });
            }
        });
    };

    function formatDate(d) {
        if (!d) return '—';
        try { return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }); }
        catch(e) { return d; }
    }

    function esc(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function renderSummaryTable() {
        const tbody = $('#tableSummaryBody');
        tbody.empty();

        const selectedDate = $('#filter-date').val();
        const selectedWeek = $('#filter-week').val();
        const monthVal = $('#filter-month').val();

        $('#summary-period-badge').text(selectedDate ? `Tanggal: ${formatDate(selectedDate)}` : `Periode: ${monthVal || 'Semua'}${selectedWeek ? ' (Week ' + selectedWeek + ')' : ''}`);

        let items = filteredReports;

        if (!items.length) {
            tbody.append('<tr><td colspan="6" class="text-center text-muted py-4"><i class="ri-file-list-3-line fs-2 mb-2 d-block text-muted"></i>Tidak ada data ringkasan</td></tr>');
            return;
        }

        // Group items by week number
        const weeksMap = {};
        items.forEach(r => {
            const wNum = getWeekOfMonth(r.tanggal);
            if (!weeksMap[wNum]) weeksMap[wNum] = [];
            weeksMap[wNum].push(r);
        });

        const weekNumbers = Object.keys(weeksMap).sort((a, b) => Number(a) - Number(b));

        weekNumbers.forEach(wNum => {
            const weekItems = weeksMap[wNum];
            const totalMenitInWeek = weekItems.reduce((acc, curr) => acc + (curr.total_menit || 0), 0);

            // Group by jenis_dt_name
            const jenisMap = {};
            weekItems.forEach(r => {
                const jName = r.jenis_dt_name || 'Lainnya';
                if (!jenisMap[jName]) {
                    jenisMap[jName] = { name: jName, count: 0, sumMenit: 0 };
                }
                jenisMap[jName].count += 1;
                jenisMap[jName].sumMenit += (r.total_menit || 0);
            });

            // Sort by sumMenit descending
            const jenisList = Object.values(jenisMap).sort((a, b) => b.sumMenit - a.sumMenit);
            const rowCount = jenisList.length;

            jenisList.forEach((jItem, index) => {
                const mttr = jItem.count > 0 ? Math.round(jItem.sumMenit / jItem.count) : 0;
                const persenVal = totalMenitInWeek > 0 ? ((jItem.sumMenit / totalMenitInWeek) * 100) : 0;
                const persenStr = persenVal.toFixed(2) + '%';

                let weekCellHtml = '';
                if (index === 0) {
                    weekCellHtml = `<td rowspan="${rowCount}" class="text-center align-middle fw-bold" style="background-color: #fef3c7; color: #92400e; font-size: 13px; border-right: 2px solid #f59e0b;">WEEK ${wNum}</td>`;
                }

                tbody.append(`
                    <tr>
                        ${weekCellHtml}
                        <td class="fw-semibold text-dark">${esc(jItem.name)}</td>
                        <td class="text-center font-monospace">${jItem.count}</td>
                        <td class="text-center font-monospace fw-semibold text-danger">${jItem.sumMenit}</td>
                        <td class="text-center font-monospace fw-bold">${mttr}</td>
                        <td class="text-center font-monospace text-primary fw-semibold">${persenStr}</td>
                    </tr>
                `);
            });
        });
    }

    function renderMachineGroupTable() {
        const tbody = $('#tableMachineGroupBody');
        tbody.empty();

        const selectedDate = $('#filter-date').val();
        const monthVal = $('#filter-month').val();
        const selectedWeek = $('#filter-week').val();
        $('#machine-period-badge').text(selectedDate ? `Tanggal: ${formatDate(selectedDate)}` : `Periode: ${monthVal || 'Semua'}${selectedWeek ? ' (Week ' + selectedWeek + ')' : ''}`);

        let items = filteredReports;

        if (!items.length) {
            tbody.append('<tr><td colspan="8" class="text-center text-muted py-4"><i class="ri-file-list-3-line fs-2 mb-2 d-block text-muted"></i>Tidak ada data ringkasan mesin</td></tr>');
            $('#total-group-a').text('0');
            $('#total-group-b').text('0');
            $('#total-group-c').text('0');
            $('#total-group-all').text('0');
            $('#total-mtbf-x').text('0.00');
            $('#total-mtbf-menit').text('#DIV/0!');
            $('#total-availability').text('-');
            return;
        }

        const daysInPeriod = selectedWeek ? 7 : 30;
        const plannedOperatingMinutes = daysInPeriod * 24 * 60;

        const machineNames = [...new Set(items.map(r => r.mesin).filter(Boolean))].sort();

        let grandA = 0, grandB = 0, grandC = 0, grandTotal = 0;

        machineNames.forEach(mName => {
            const mItems = items.filter(r => r.mesin === mName);

            const groupA = mItems.filter(r => (r.grup || '').toUpperCase() === 'A');
            const groupB = mItems.filter(r => (r.grup || '').toUpperCase() === 'B');
            const groupC = mItems.filter(r => (r.grup || '').toUpperCase() === 'C');

            const countA = groupA.length;
            const countB = groupB.length;
            const countC = groupC.length;

            const sumMenitA = groupA.reduce((acc, c) => acc + (c.total_menit || 0), 0);
            const sumMenitB = groupB.reduce((acc, c) => acc + (c.total_menit || 0), 0);
            const sumMenitC = groupC.reduce((acc, c) => acc + (c.total_menit || 0), 0);

            const totalCount = countA + countB + countC;
            const totalDowntimeMenit = sumMenitA + sumMenitB + sumMenitC;

            grandA += countA;
            grandB += countB;
            grandC += countC;
            grandTotal += totalCount;

            const mtbfX = (totalCount / 3).toFixed(2);

            let mtbfMenitStr = '#DIV/0!';
            let availabilityStr = '-';

            if (totalCount > 0) {
                const operatingMinutes = Math.max(0, plannedOperatingMinutes - totalDowntimeMenit);
                const mtbfMenit = (operatingMinutes / totalCount).toFixed(2);
                mtbfMenitStr = mtbfMenit;

                const avail = (operatingMinutes / plannedOperatingMinutes).toFixed(2);
                availabilityStr = avail;
            }

            const rowBg = totalCount === 0 ? 'style="background-color: #f9fafb;"' : '';

            tbody.append(`
                <tr ${rowBg}>
                    <td class="text-center fw-bold text-dark" style="background-color: #fef08a;">${esc(mName)}</td>
                    <td class="text-center font-monospace" style="background-color: #e0f2fe;">${countA}</td>
                    <td class="text-center font-monospace" style="background-color: #fef9c3;">${countB}</td>
                    <td class="text-center font-monospace" style="background-color: #dcfce7;">${countC}</td>
                    <td class="text-center font-monospace fw-bold text-dark" style="background-color: #fef08a;">${totalCount}</td>
                    <td class="text-center font-monospace">${mtbfX}</td>
                    <td class="text-center font-monospace ${mtbfMenitStr === '#DIV/0!' ? 'text-muted' : 'fw-semibold text-success'}">${mtbfMenitStr}</td>
                    <td class="text-center font-monospace ${availabilityStr === '-' ? 'text-muted' : 'fw-bold text-primary'}">${availabilityStr}</td>
                </tr>
            `);
        });

        $('#total-group-a').text(grandA);
        $('#total-group-b').text(grandB);
        $('#total-group-c').text(grandC);
        $('#total-group-all').text(grandTotal);
        $('#total-mtbf-x').text((grandTotal / 3).toFixed(2));
        $('#total-mtbf-menit').text('#DIV/0!');
        $('#total-availability').text('-');
    }

    function renderLoadingTimeTable() {
        const tbody = $('#tableLoadingTimeBody');
        tbody.empty();

        const monthVal = $('#filter-month').val();
        const selectedDate = $('#filter-date').val() || $('#filter-lt-date').val();

        let reportsToUse = reports;
        let ltToUse = loadingTimes;

        if (selectedDate) {
            reportsToUse = reports.filter(r => r.tanggal === selectedDate);
            ltToUse = loadingTimes.filter(lt => lt.tanggal === selectedDate);
            $('#lt-period-badge').text(`Tanggal: ${formatDate(selectedDate)}`);
        } else {
            $('#lt-period-badge').text(`Bulan: ${monthVal || 'Semua'}`);
        }

        const pouchMachines = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'C', 'L', 'B', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'V', 'A', 'U'];
        const sachetMachines = ['O', 'P', 'W', 'X', 'R', 'Q', 'Y'];

        function getMachineMetrics(mName) {
            const mReports = reportsToUse.filter(r => (r.mesin || '').trim().toUpperCase() === mName.toUpperCase());

            const dtA = mReports.filter(r => (r.grup || '').toUpperCase() === 'A').reduce((acc, c) => acc + (c.total_menit || 0), 0);
            const dtB = mReports.filter(r => (r.grup || '').toUpperCase() === 'B').reduce((acc, c) => acc + (c.total_menit || 0), 0);
            const dtC = mReports.filter(r => (r.grup || '').toUpperCase() === 'C').reduce((acc, c) => acc + (c.total_menit || 0), 0);
            const dtAll = dtA + dtB + dtC;

            const ltRecords = ltToUse.filter(lt => (lt.mesin || '').trim().toUpperCase() === mName.toUpperCase());
            const ltA = ltRecords.reduce((acc, c) => acc + (c.loading_time_a || 0), 0);
            const ltB = ltRecords.reduce((acc, c) => acc + (c.loading_time_b || 0), 0);
            const ltC = ltRecords.reduce((acc, c) => acc + (c.loading_time_c || 0), 0);
            const ltAll = (ltA + ltB + ltC) > 0 ? (ltA + ltB + ltC) : ltRecords.reduce((acc, c) => acc + (c.loading_time_all || 0), 0);

            const pctA = ltA > 0 ? ((dtA / ltA) * 100) : 0;
            const pctB = ltB > 0 ? ((dtB / ltB) * 100) : 0;
            const pctC = ltC > 0 ? ((dtC / ltC) * 100) : 0;
            const pctAll = ltAll > 0 ? ((dtAll / ltAll) * 100) : 0;

            return { mName, ltA, dtA, pctA, ltB, dtB, pctB, ltC, dtC, pctC, ltAll, dtAll, pctAll };
        }

        function fmtVal(val) {
            return val > 0 ? val : '';
        }
        function fmtPct(pct, hasLoad) {
            return hasLoad > 0 ? (pct > 0 ? pct.toFixed(1) + '%' : '0.0%') : '';
        }
        function fmtPctExact(pct, hasLoad) {
            return hasLoad > 0 ? (pct > 0 ? pct.toFixed(2) + '%' : '0.00%') : '';
        }

        // --- Render POUCH ---
        let sumPouchLtA = 0, sumPouchDtA = 0;
        let sumPouchLtB = 0, sumPouchDtB = 0;
        let sumPouchLtC = 0, sumPouchDtC = 0;
        let activePouchAllCount = 0;
        let sumPouchLtAll = 0, sumPouchDtAll = 0;

        pouchMachines.forEach((mName, idx) => {
            const data = getMachineMetrics(mName);
            sumPouchLtA += data.ltA;
            sumPouchDtA += data.dtA;
            sumPouchLtB += data.ltB;
            sumPouchDtB += data.dtB;
            sumPouchLtC += data.ltC;
            sumPouchDtC += data.dtC;

            if (data.ltAll > 0) {
                activePouchAllCount++;
                sumPouchLtAll += data.ltAll;
                sumPouchDtAll += data.dtAll;
            }

            const categoryCell = idx === 0 ? `<td rowspan="${pouchMachines.length}" class="fw-bold align-middle" style="background-color: #93c5fd; color: #1e3a8a; font-size: 13px; letter-spacing: 1px;">POUCH</td>` : '';

            tbody.append(`
                <tr>
                    ${categoryCell}
                    <td class="fw-bold text-dark" style="background-color: #f8fafc;">${esc(mName)}</td>
                    <td style="background-color: #f0fdf4;" class="font-monospace">${fmtVal(data.ltA)}</td>
                    <td class="font-monospace">${data.ltA > 0 ? data.dtA : ''}</td>
                    <td class="font-monospace fw-semibold ${data.pctA > 5 ? 'text-danger' : 'text-dark'}">${fmtPct(data.pctA, data.ltA)}</td>
                    <td style="background-color: #f0fdf4;" class="font-monospace">${fmtVal(data.ltB)}</td>
                    <td class="font-monospace">${data.ltB > 0 ? data.dtB : ''}</td>
                    <td class="font-monospace fw-semibold ${data.pctB > 5 ? 'text-danger' : 'text-dark'}">${fmtPct(data.pctB, data.ltB)}</td>
                    <td style="background-color: #f0fdf4;" class="font-monospace">${fmtVal(data.ltC)}</td>
                    <td class="font-monospace">${data.ltC > 0 ? data.dtC : ''}</td>
                    <td class="font-monospace fw-semibold ${data.pctC > 5 ? 'text-danger' : 'text-dark'}">${fmtPct(data.pctC, data.ltC)}</td>
                    <td style="background-color: #f0fdf4;" class="font-monospace fw-bold">${fmtVal(data.ltAll)}</td>
                    <td class="font-monospace fw-bold">${data.ltAll > 0 ? data.dtAll : ''}</td>
                    <td class="font-monospace fw-bold ${data.pctAll > 5 ? 'text-danger' : 'text-dark'}">${fmtPctExact(data.pctAll, data.ltAll)}</td>
                </tr>
            `);
        });

        // Row Rata-rata Pouch (Row 25 in Excel: Sum for A, B, C; Average for ALL)
        const avgPouchLtA = sumPouchLtA;
        const avgPouchDtA = Math.round(sumPouchDtA);
        const avgPouchPctA = avgPouchLtA > 0 ? ((avgPouchDtA / avgPouchLtA) * 100) : 0;

        const avgPouchLtB = sumPouchLtB;
        const avgPouchDtB = Math.round(sumPouchDtB);
        const avgPouchPctB = avgPouchLtB > 0 ? ((avgPouchDtB / avgPouchLtB) * 100) : 0;

        const avgPouchLtC = sumPouchLtC;
        const avgPouchDtC = Math.round(sumPouchDtC);
        const avgPouchPctC = avgPouchLtC > 0 ? ((avgPouchDtC / avgPouchLtC) * 100) : 0;

        const avgPouchLtAllRaw = activePouchAllCount > 0 ? (sumPouchLtAll / activePouchAllCount) : 0;
        const avgPouchDtAllRaw = activePouchAllCount > 0 ? (sumPouchDtAll / activePouchAllCount) : 0;
        const avgPouchPctAll = avgPouchLtAllRaw > 0 ? ((avgPouchDtAllRaw / avgPouchLtAllRaw) * 100) : 0;

        tbody.append(`
            <tr class="fw-bold" style="background-color: #fef08a; border-top: 2px solid #ca8a04;">
                <td colspan="2" class="text-start ps-3" style="color: #713f12;">Rata-rata pouch</td>
                <td class="font-monospace text-dark">${fmtVal(avgPouchLtA)}</td>
                <td class="font-monospace text-dark">${avgPouchDtA || 0}</td>
                <td class="font-monospace text-dark">${avgPouchPctA.toFixed(1)}%</td>
                <td class="font-monospace text-dark">${fmtVal(avgPouchLtB)}</td>
                <td class="font-monospace text-dark">${avgPouchDtB || 0}</td>
                <td class="font-monospace text-dark">${avgPouchPctB.toFixed(1)}%</td>
                <td class="font-monospace text-dark">${fmtVal(avgPouchLtC)}</td>
                <td class="font-monospace text-dark">${avgPouchDtC || 0}</td>
                <td class="font-monospace text-dark">${avgPouchPctC.toFixed(1)}%</td>
                <td class="font-monospace text-dark">${Math.round(avgPouchLtAllRaw)}</td>
                <td class="font-monospace text-dark">${Math.round(avgPouchDtAllRaw)}</td>
                <td class="font-monospace text-dark">${avgPouchPctAll.toFixed(2)}%</td>
            </tr>
        `);

        // --- Render SACHET ---
        let sumSachetLtA = 0, sumSachetDtA = 0;
        let sumSachetLtB = 0, sumSachetDtB = 0;
        let sumSachetLtC = 0, sumSachetDtC = 0;
        let activeSachetAllCount = 0;
        let sumSachetLtAll = 0, sumSachetDtAll = 0;

        sachetMachines.forEach((mName, idx) => {
            const data = getMachineMetrics(mName);
            sumSachetLtA += data.ltA;
            sumSachetDtA += data.dtA;
            sumSachetLtB += data.ltB;
            sumSachetDtB += data.dtB;
            sumSachetLtC += data.ltC;
            sumSachetDtC += data.dtC;

            if (data.ltAll > 0) {
                activeSachetAllCount++;
                sumSachetLtAll += data.ltAll;
                sumSachetDtAll += data.dtAll;
            }

            const categoryCell = idx === 0 ? `<td rowspan="${sachetMachines.length}" class="fw-bold align-middle" style="background-color: #fed7aa; color: #9a3412; font-size: 13px; letter-spacing: 1px;">SACHET</td>` : '';

            tbody.append(`
                <tr>
                    ${categoryCell}
                    <td class="fw-bold text-dark" style="background-color: #f8fafc;">${esc(mName)}</td>
                    <td style="background-color: #f0fdf4;" class="font-monospace">${fmtVal(data.ltA)}</td>
                    <td class="font-monospace">${data.ltA > 0 ? data.dtA : ''}</td>
                    <td class="font-monospace fw-semibold ${data.pctA > 5 ? 'text-danger' : 'text-dark'}">${fmtPct(data.pctA, data.ltA)}</td>
                    <td style="background-color: #f0fdf4;" class="font-monospace">${fmtVal(data.ltB)}</td>
                    <td class="font-monospace">${data.ltB > 0 ? data.dtB : ''}</td>
                    <td class="font-monospace fw-semibold ${data.pctB > 5 ? 'text-danger' : 'text-dark'}">${fmtPct(data.pctB, data.ltB)}</td>
                    <td style="background-color: #f0fdf4;" class="font-monospace">${fmtVal(data.ltC)}</td>
                    <td class="font-monospace">${data.ltC > 0 ? data.dtC : ''}</td>
                    <td class="font-monospace fw-semibold ${data.pctC > 5 ? 'text-danger' : 'text-dark'}">${fmtPct(data.pctC, data.ltC)}</td>
                    <td style="background-color: #f0fdf4;" class="font-monospace fw-bold">${fmtVal(data.ltAll)}</td>
                    <td class="font-monospace fw-bold">${data.ltAll > 0 ? data.dtAll : ''}</td>
                    <td class="font-monospace fw-bold ${data.pctAll > 5 ? 'text-danger' : 'text-dark'}">${fmtPctExact(data.pctAll, data.ltAll)}</td>
                </tr>
            `);
        });

        // Row Rata-rata Sachet (Row 33 in Excel: Sum for A, B, C; Average for ALL)
        const avgSachetLtA = sumSachetLtA;
        const avgSachetDtA = Math.round(sumSachetDtA);
        const avgSachetPctA = avgSachetLtA > 0 ? ((avgSachetDtA / avgSachetLtA) * 100) : 0;

        const avgSachetLtB = sumSachetLtB;
        const avgSachetDtB = Math.round(sumSachetDtB);
        const avgSachetPctB = avgSachetLtB > 0 ? ((avgSachetDtB / avgSachetLtB) * 100) : 0;

        const avgSachetLtC = sumSachetLtC;
        const avgSachetDtC = Math.round(sumSachetDtC);
        const avgSachetPctC = avgSachetLtC > 0 ? ((avgSachetDtC / avgSachetLtC) * 100) : 0;

        const avgSachetLtAllRaw = activeSachetAllCount > 0 ? (sumSachetLtAll / activeSachetAllCount) : 0;
        const avgSachetDtAllRaw = activeSachetAllCount > 0 ? (sumSachetDtAll / activeSachetAllCount) : 0;
        const avgSachetPctAll = avgSachetLtAllRaw > 0 ? ((avgSachetDtAllRaw / avgSachetLtAllRaw) * 100) : 0;

        tbody.append(`
            <tr class="fw-bold" style="background-color: #fef08a; border-top: 2px solid #ca8a04;">
                <td colspan="2" class="text-start ps-3" style="color: #713f12;">Rata-rata Sachet</td>
                <td class="font-monospace text-dark">${fmtVal(avgSachetLtA)}</td>
                <td class="font-monospace text-dark">${avgSachetDtA || 0}</td>
                <td class="font-monospace text-dark">${avgSachetPctA.toFixed(2)}%</td>
                <td class="font-monospace text-dark">${fmtVal(avgSachetLtB)}</td>
                <td class="font-monospace text-dark">${avgSachetDtB || 0}</td>
                <td class="font-monospace text-dark">${avgSachetPctB.toFixed(1)}%</td>
                <td class="font-monospace text-dark">${fmtVal(avgSachetLtC)}</td>
                <td class="font-monospace text-dark">${avgSachetDtC || 0}</td>
                <td class="font-monospace text-dark">${avgSachetPctC.toFixed(2)}%</td>
                <td class="font-monospace text-dark">${Math.round(avgSachetLtAllRaw)}</td>
                <td class="font-monospace text-dark">${Math.round(avgSachetDtAllRaw)}</td>
                <td class="font-monospace text-dark">${avgSachetPctAll.toFixed(2)}%</td>
            </tr>
        `);

        // Row Total Pouch & Sachet (Row 34 in Excel: AVERAGE(Pouch, Sachet))
        const totalLtA = Math.round((avgPouchLtA + avgSachetLtA) / 2);
        const totalDtA = Number(((avgPouchDtA + avgSachetDtA) / 2).toFixed(1));
        const totalPctA = totalLtA > 0 ? ((totalDtA / totalLtA) * 100) : 0;

        const totalLtB = Math.round((avgPouchLtB + avgSachetLtB) / 2);
        const totalDtB = Math.round((avgPouchDtB + avgSachetDtB) / 2);
        const totalPctB = totalLtB > 0 ? ((totalDtB / totalLtB) * 100) : 0;

        const totalLtC = Math.round((avgPouchLtC + avgSachetLtC) / 2);
        const totalDtC = Math.round((avgPouchDtC + avgSachetDtC) / 2);
        const totalPctC = totalLtC > 0 ? ((totalDtC / totalLtC) * 100) : 0;

        const totalLtAll = Math.round((avgPouchLtAllRaw + avgSachetLtAllRaw) / 2);
        const totalDtAll = Math.round((avgPouchDtAllRaw + avgSachetDtAllRaw) / 2);
        const totalPctAll = totalLtAll > 0 ? (((avgPouchDtAllRaw + avgSachetDtAllRaw) / 2) / totalLtAll * 100) : 0;

        tbody.append(`
            <tr class="fw-bold" style="background-color: #86efac; border-top: 3px double #15803d; font-size: 13px;">
                <td colspan="2" class="text-start ps-3" style="color: #14532d;">Total Pouch & Sachet</td>
                <td class="font-monospace text-dark">${fmtVal(totalLtA)}</td>
                <td class="font-monospace text-dark">${totalDtA}</td>
                <td class="font-monospace text-dark">${totalPctA.toFixed(2)}%</td>
                <td class="font-monospace text-dark">${fmtVal(totalLtB)}</td>
                <td class="font-monospace text-dark">${totalDtB}</td>
                <td class="font-monospace text-dark">${totalPctB.toFixed(2)}%</td>
                <td class="font-monospace text-dark">${fmtVal(totalLtC)}</td>
                <td class="font-monospace text-dark">${totalDtC}</td>
                <td class="font-monospace text-dark">${Math.round(totalPctC)}%</td>
                <td class="font-monospace text-dark">${totalLtAll}</td>
                <td class="font-monospace text-dark">${totalDtAll}</td>
                <td class="font-monospace text-dark">${totalPctAll.toFixed(2)}%</td>
            </tr>
        `);
    }

    $('#filter-search').on('input keypress', function(e) {
        if (e.type === 'input' || e.which === 13) {
            applyFiltersListOnly();
        }
    });

    $('#filter-date').on('change input', function() {
        const d = $(this).val();
        $('#filter-lt-date').val(d);
        const m = $('#filter-month').val();
        if (d && (!m || d.substring(0, 7) !== m)) {
            $('#filter-month').val(d.substring(0, 7));
            isFirstLoad = false;
            loadReports();
        } else {
            applyFiltersListOnly();
        }
    });

    $('#filter-lt-date').on('change input', function() {
        const d = $(this).val();
        $('#filter-date').val(d);
        const m = $('#filter-month').val();
        if (d && (!m || d.substring(0, 7) !== m)) {
            $('#filter-month').val(d.substring(0, 7));
            isFirstLoad = false;
            loadReports();
        } else {
            applyFiltersListOnly();
        }
    });

    $('#filter-shift, #filter-pouchSachet, #filter-em, #filter-week').on('change', applyFiltersListOnly);

    $('#filter-month').on('change', function() {
        $('#filter-date').val('');
        $('#filter-lt-date').val('');
        isFirstLoad = true;
        loadReports();
    });

    loadReports();
});
</script>
@endsection
