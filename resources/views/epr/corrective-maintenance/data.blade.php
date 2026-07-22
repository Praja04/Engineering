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
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1 text-muted">Cari</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" class="form-control" id="filter-search" placeholder="Cari mesin, keterangan, jenis DT...">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small fw-semibold mb-1 text-muted">Shift</label>
                        <select class="form-select form-select-sm" id="filter-shift">
                            <option value="">Semua</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold mb-1 text-muted">Pouch / Sachet</label>
                        <select class="form-select form-select-sm" id="filter-pouchSachet">
                            <option value="">Semua</option>
                            <option value="Pouch">Pouch</option>
                            <option value="Sachet">Sachet</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small fw-semibold mb-1 text-muted">E / M</label>
                        <select class="form-select form-select-sm" id="filter-em">
                            <option value="">Semua</option>
                            <option value="Electrical">Electrical</option>
                            <option value="Mechanical">Mechanical</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold mb-1 text-muted">Bulan</label>
                        <input type="month" class="form-control form-control-sm" id="filter-month" value="{{ date('Y-m') }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small fw-semibold mb-1 text-muted">Week</label>
                        <select class="form-select form-select-sm" id="filter-week">
                            <option value="">Semua</option>
                            <option value="1">Week 1</option>
                            <option value="2">Week 2</option>
                            <option value="3">Week 3</option>
                            <option value="4">Week 4</option>
                            <option value="5">Week 5</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-1">
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
    let filteredReports = [];
    let currentPage = 1;
    const pageSize = 15;

    function loadReports() {
        const month = $('#filter-month').val() || '';
        $.ajax({
            url: "{{ route('epr.cm.get-reports') }}",
            method: 'GET',
            data: { month: month },
            success: function(res) {
                reports = res;
                applyFiltersListOnly();
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat riwayat data laporan', 'error');
            }
        });
    }

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
        const search = $('#filter-search').val().trim().toLowerCase();
        const shift = $('#filter-shift').val();
        const pouchSachet = $('#filter-pouchSachet').val();
        const em = $('#filter-em').val();
        const week = $('#filter-week').val();

        let filtered = reports;

        if (search) {
            filtered = filtered.filter(r => 
                (r.mesin || '').toLowerCase().includes(search) ||
                (r.downtime || '').toLowerCase().includes(search) ||
                (r.keterangan || '').toLowerCase().includes(search) ||
                (r.jenis_dt_name || '').toLowerCase().includes(search)
            );
        }
        if (shift) filtered = filtered.filter(r => r.shift === shift);
        if (pouchSachet) filtered = filtered.filter(r => r.pouch_sachet === pouchSachet);
        if (em) filtered = filtered.filter(r => r.electrical_mechanical === em);
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
    }

    window.applyFilters = function() {
        loadReports();
    };

    window.resetFilters = function() {
        $('#filter-search').val('');
        $('#filter-shift').val('');
        $('#filter-pouchSachet').val('');
        $('#filter-em').val('');
        $('#filter-week').val('');
        $('#filter-month').val("{{ date('Y-m') }}");
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
            title: 'Import Data CM?',
            text: `Apakah Anda yakin ingin mengimpor data dari file "${file.name}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Import',
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

        const selectedWeek = $('#filter-week').val();
        const monthVal = $('#filter-month').val();

        $('#summary-period-badge').text(`Periode: ${monthVal || 'Semua'}${selectedWeek ? ' (Week ' + selectedWeek + ')' : ''}`);

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

        const monthVal = $('#filter-month').val();
        const selectedWeek = $('#filter-week').val();
        $('#machine-period-badge').text(`Periode: ${monthVal || 'Semua'}${selectedWeek ? ' (Week ' + selectedWeek + ')' : ''}`);

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

    $('#filter-search').on('input', applyFiltersListOnly);
    $('#filter-shift, #filter-pouchSachet, #filter-em, #filter-week').on('change', applyFiltersListOnly);
    $('#filter-month').on('change', loadReports);

    loadReports();
});
</script>
@endsection
