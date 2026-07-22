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
                    <div class="col-md-2">
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
                    <div class="col-md-2">
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
                    <div class="col-md-1 d-flex align-items-end gap-1">
                        <button class="btn btn-primary btn-sm w-100" onclick="applyFilters()" title="Filter">
                            <i class="ri-filter-3-line"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="resetFilters()" title="Reset">
                            <i class="ri-refresh-line"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Table --}}
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
                <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top" id="pagination-container" style="display: none;">
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

    function applyFiltersListOnly() {
        const search = $('#filter-search').val().trim().toLowerCase();
        const shift = $('#filter-shift').val();
        const pouchSachet = $('#filter-pouchSachet').val();
        const em = $('#filter-em').val();

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
    }

    window.applyFilters = function() {
        loadReports();
    };

    window.resetFilters = function() {
        $('#filter-search').val('');
        $('#filter-shift').val('');
        $('#filter-pouchSachet').val('');
        $('#filter-em').val('');
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
            $('#pagination-container').hide();
            return;
        }

        $('#pagination-container').show();

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

    loadReports();
});
</script>
@endsection
