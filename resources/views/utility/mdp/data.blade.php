@extends('layouts.app')

@section('title', 'Rekap Pemantauan MDP')

@section('styles')
<style>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%);
        color: white;
        padding: 1.25rem;
        border-radius: 12px 12px 0 0;
    }

    .table thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #6c757d;
        border-top: none;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 text-white fw-bold">
                        <i class="ri-history-line me-2 text-warning"></i>
                        Rekap Pemantauan MDP
                    </h4>
                    <p class="mb-0 text-white-50 small">Engineering Utility · Riwayat Pemantauan Harian</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('mdp-monitoring.index') }}" class="btn btn-warning btn-sm rounded-pill px-3">
                        <i class="ri-add-line me-1"></i> Input Baru
                    </a>
                    <button class="btn btn-outline-light btn-sm rounded-pill px-3" id="btnExport">
                        <i class="ri-download-2-line me-1"></i> Export
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                {{-- Filter --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Bulan & Tahun</label>
                        <input type="month" id="filterBulan" class="form-control" value="{{ date('Y-m') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Status Approval</label>
                        <select id="filterStatus" class="form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="submitted">Submitted</option>
                            <option value="approved_foreman">Approved Foreman</option>
                            <option value="approved_supervisor">Approved Supervisor</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary w-100" id="btnFilter">
                            <i class="ri-filter-3-line me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Operator</th>
                                <th>E-Del (kWh)</th>
                                <th>Arus (A)</th>
                                <th>Tegangan (V)</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            {{-- Data via AJAX --}}
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div id="paginationInfo" class="small text-muted"></div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom p-3">
                <h5 class="modal-title fw-bold">Detail Pemantauan MDP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailContent">
                {{-- Content via AJAX --}}
            </div>
            <div class="modal-footer border-top p-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        loadData(1);

        $('#btnFilter').click(function() {
            loadData(1);
        });

        function loadData(page) {
            $('#loadingOverlay').removeClass('d-none');
            const params = {
                bulan: $('#filterBulan').val(),
                status: $('#filterStatus').val(),
                page: page
            };

            $.get("{{ route('mdp-monitoring.json') }}", params, function(res) {
                renderTable(res.data, res.pagination);
                renderPagination(res.pagination);
                $('#loadingOverlay').addClass('d-none');
            });
        }

        function renderTable(data, pagination) {
            let html = '';
            if (data.length === 0) {
                html = '<tr><td colspan="9" class="text-center py-5 text-muted">Tidak ada data ditemukan</td></tr>';
            } else {
                data.forEach((item, index) => {
                    const no = (pagination.current_page - 1) * pagination.per_page + index + 1;
                    html += `
                        <tr>
                            <td>${no}</td>
                            <td>${formatDate(item.tanggal_laporan)}</td>
                            <td>${item.jam_pencatatan}</td>
                            <td>${item.operator?.username || '-'}</td>
                            <td>${item.e_del || '-'}</td>
                            <td>${item.arus_rata_rata || '-'}</td>
                            <td>${item.tegangan_rata_rata || '-'}</td>
                            <td>${getStatusBadge(item.status)}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info rounded-pill px-3 btn-detail" data-id="${item.id}">
                                    <i class="ri-eye-line me-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#tableBody').html(html);
        }

        function renderPagination(pagination) {
            let html = '';
            if (pagination.last_page > 1) {
                for (let i = 1; i <= pagination.last_page; i++) {
                    html += `
                        <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                }
            }
            $('#paginationLinks').html(html);
            $('#paginationInfo').text(`Showing data ${dataRange(pagination)}`);
        }

        function dataRange(p) {
            if (p.total === 0) return '0';
            const start = (p.current_page - 1) * p.per_page + 1;
            const end = Math.min(p.current_page * p.per_page, p.total);
            return `${start} - ${end} of ${p.total}`;
        }

        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            loadData($(this).data('page'));
        });

        $(document).on('click', '.btn-detail', function() {
            const id = $(this).data('id');
            showDetail(id);
        });

        function showDetail(id) {
            $('#loadingOverlay').removeClass('d-none');
            $.get("{{ url('utility/mdp-monitoring/json') }}/" + id, function(res) {
                const d = res.data;
                const html = `
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold border-bottom pb-2 mb-3">Informasi Umum</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td class="text-muted" width="40%">Tanggal</td><td>: ${formatDate(d.tanggal_laporan)}</td></tr>
                                <tr><td class="text-muted">Jam</td><td>: ${d.jam_pencatatan}</td></tr>
                                <tr><td class="text-muted">Operator</td><td>: ${d.operator?.username || '-'}</td></tr>
                                <tr><td class="text-muted">Status</td><td>: ${getStatusBadge(d.status)}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold border-bottom pb-2 mb-3">Approval</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td class="text-muted" width="40%">Foreman</td><td>: ${d.foreman?.username || '-'}</td></tr>
                                <tr><td class="text-muted">Supervisor</td><td>: ${d.supervisor?.username || '-'}</td></tr>
                                ${d.reject_reason ? `<tr class="text-danger"><td class="text-muted">Alasan Reject</td><td>: ${d.reject_reason}</td></tr>` : ''}
                            </table>
                        </div>
                        <div class="col-12">
                            <h6 class="fw-bold border-bottom pb-2 mb-3">Data Teknis</h6>
                            <div class="row g-3">
                                ${renderTechnicalItem('E-Del', d.e_del, 'kWh')}
                                ${renderTechnicalItem('Arus Avg', d.arus_rata_rata, 'A')}
                                ${renderTechnicalItem('Arus I1', d.arus_i1, 'A')}
                                ${renderTechnicalItem('Arus I2', d.arus_i2, 'A')}
                                ${renderTechnicalItem('Arus I3', d.arus_i3, 'A')}
                                ${renderTechnicalItem('Volt Avg', d.tegangan_rata_rata, 'V')}
                                ${renderTechnicalItem('Volt V1', d.tegangan_v1, 'V')}
                                ${renderTechnicalItem('Volt V2', d.tegangan_v2, 'V')}
                                ${renderTechnicalItem('Volt V3', d.tegangan_v3, 'V')}
                                ${renderTechnicalItem('Daya Total', d.daya_total, 'kW')}
                                ${renderTechnicalItem('Daya P1', d.daya_p1, 'kW')}
                                ${renderTechnicalItem('Daya P2', d.daya_p2, 'kW')}
                                ${renderTechnicalItem('Daya P3', d.daya_p3, 'kW')}
                                ${renderTechnicalItem('Temp Trafo', d.temperatur_transformator, '°C')}
                                ${renderTechnicalItem('Level Oil', d.level_oil?.toUpperCase(), '')}
                            </div>
                        </div>
                    </div>
                `;
                $('#detailContent').html(html);
                $('#modalDetail').modal('show');
                $('#loadingOverlay').addClass('d-none');
            });
        }

        function renderTechnicalItem(label, value, unit) {
            return `
                <div class="col-md-3 col-6">
                    <div class="p-2 border rounded bg-light">
                        <small class="text-muted d-block">${label}</small>
                        <span class="fw-bold">${value || '-'}</span> <small>${unit}</small>
                    </div>
                </div>
            `;
        }

        function getStatusBadge(status) {
            const badges = {
                'submitted': '<span class="badge bg-info">Submitted</span>',
                'approved_foreman': '<span class="badge bg-warning text-dark">Appr Foreman</span>',
                'approved_supervisor': '<span class="badge bg-success">Approved</span>',
                'rejected': '<span class="badge bg-danger">Rejected</span>'
            };
            return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
        }

        function formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        // ── Export ──
        $('#btnExport').on('click', function() {
            const params = new URLSearchParams({
                bulan: $('#filterBulan').val(),
            });

            $.ajax({
                url: "{{ route('mdp-monitoring.export') }}" + '?' + params.toString(),
                method: 'GET',
                success: function(res) {
                    if (res.data.length === 0) {
                        Swal.fire('Tidak ada data', 'Tidak ada data untuk diekspor',
                            'info');
                        return;
                    }

                    // Buat CSV
                    let csv =
                        'Tanggal,Jam Pencatatan,Operator,Engine Speed,Engine Temp,Oil Pressure,Battery Voltage,Charge Alt Voltage,Running Hour,Frequency,Status Oil 1,Status Oil 2,Status\n';

                    res.data.forEach(item => {
                        csv +=
                            `"${item.tanggal_laporan}","${item.jam_pencatatan}","${item.operator?.username || '-'}","${item.engine_speed || '-'}","${item.engine_temperature || '-'}","${item.engine_oil_pressure || '-'}","${item.battery_voltage || '-'}","${item.charge_alt_voltage || '-'}","${item.running_hour || '-'}","${item.frequency || '-'}","${item.status_oil_1 || '-'}","${item.status_oil_2 || '-'}","${item.status}"\n`;
                    });

                    // Download CSV
                    const element = document.createElement('a');
                    element.setAttribute('href', 'data:text/csv;charset=utf-8,' +
                        encodeURIComponent(csv));
                    element.setAttribute('download',
                        `warming-up-genset-${new Date().toISOString().split('T')[0]}.csv`
                    );
                    element.style.display = 'none';
                    document.body.appendChild(element);
                    element.click();
                    document.body.removeChild(element);

                    Swal.fire('Berhasil', 'Data berhasil diekspor', 'success');
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Gagal mengekspor data', 'error');
                }
            });
        });
    });
</script>
@endsection