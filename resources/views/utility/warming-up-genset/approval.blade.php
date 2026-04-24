@extends('layouts.app')

@section('title', 'Warming Up Genset — Approval')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="card border-0 shadow-sm mb-3"
            style="background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%); border-radius: 12px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h4 class="text-white fw-bold mb-1">
                            <i class="ri-check-double-line text-warning me-2"></i>
                            Approval Warming Up Genset
                        </h4>
                        <p class="text-white-50 mb-0 small">
                            Silakan periksa dan setujui laporan yang masuk
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📊 TABLE --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-primary">
                    <i class="ri-timer-2-line me-1"></i> Menunggu Persetujuan Anda
                </h6>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="tblData" class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Tanggal</th>
                                <th>Operator</th>
                                <th>Engine Speed</th>
                                <th>Temp</th>
                                <th>Oil</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="tableBody">
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                                    Memuat data...
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        {{-- 🔢 PAGINATION --}}
        <div class="d-flex justify-content-end mt-3">
            <nav id="paginationNav"></nav>
        </div>

    </div>
</div>

{{-- 📄 MODAL DETAIL --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold">Detail Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailContent">
                <!-- Data akan dimuat di sini -->
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- 🔄 LOADING --}}
<div class="loading-overlay d-none" id="loadingOverlay">
    <div class="spinner-border text-warning" role="status"></div>
</div>
@endsection

@section('scripts')
<script>
    const API_URL = "{{ route('warming-up-genset.json') }}";
    const SHOW_URL = "{{ url('utility/warming-up-genset/json') }}";
    const APPROVE_FOREMAN_URL = "{{ url('utility/warming-up-genset/approve-foreman') }}";
    const APPROVE_SUPERVISOR_URL = "{{ url('utility/warming-up-genset/approve-supervisor') }}";
    const REJECT_URL = "{{ url('utility/warming-up-genset/reject') }}";
    let cachedData = [];

    $(document).ready(function() {
        loadData(1);

        function loadData(page = 1) {
            showLoading(true);
            $.ajax({
                url: API_URL,
                method: 'GET',
                data: {
                    page: page,
                    per_page: 15,
                    mode: 'approval'
                },
                success: function(res) {
                    cachedData = res.data;
                    renderTable(res.data, res.pagination);
                    renderPagination(res.pagination);
                    showLoading(false);
                },
                error: function(xhr) {
                    showLoading(false);
                    Swal.fire('Error', 'Gagal memuat data approval', 'error');
                }
            });
        }

        function renderTable(data, pagination) {
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="8" class="text-center py-5 text-muted"><i class="ri-check-line fs-3 d-block mb-2 text-success"></i>Semua laporan sudah diproses.</td></tr>';
                $('#tableBody').html(html);
                return;
            }

            data.forEach((item, idx) => {
                const statusBadge = getStatusBadge(item.status);
                const no = ((pagination.current_page - 1) * pagination.per_page) + (idx + 1);

                html += `
                    <tr>
                        <td class="text-center">${no}</td>
                        <td>${formatDate(item.tanggal_laporan)}</td>
                        <td>${item.operator?.username || '-'}</td>
                        <td>${item.engine_speed || '-'}</td>
                        <td>${item.engine_temperature || '-'}</td>
                        <td>${item.engine_oil_pressure || '-'}</td>
                        <td>${statusBadge}</td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm btn-info btn-detail" data-id="${item.id}" title="Detail">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button class="btn btn-sm btn-success btn-approve" data-id="${item.id}" data-status="${item.status}" title="Approve">
                                    <i class="ri-check-line"></i> Approve
                                </button>
                                <button class="btn btn-sm btn-danger btn-reject" data-id="${item.id}" title="Reject">
                                    <i class="ri-close-line"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            $('#tableBody').html(html);
        }

        function renderPagination(pagination) {
            if (pagination.last_page <= 1) {
                $('#paginationNav').html('');
                return;
            }

            let html = `<nav aria-label="Page navigation"><ul class="pagination pagination-sm mb-0">`;
            html += `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadData(${pagination.current_page - 1}); return false;"><i class="ri-arrow-left-s-line"></i></a></li>`;

            for (let i = 1; i <= pagination.last_page; i++) {
                html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}"><a class="page-link" href="#" onclick="loadData(${i}); return false;">${i}</a></li>`;
            }

            html += `<li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadData(${pagination.current_page + 1}); return false;"><i class="ri-arrow-right-s-line"></i></a></li>`;
            html += `</ul></nav>`;
            $('#paginationNav').html(html);
        }

        // ── Detail ──
        $(document).on('click', '.btn-detail', function() {
            const id = $(this).data('id');
            const data = cachedData.find(item => item.id == id);
            if (data) {
                renderDetail(data);
                $('#modalDetail').modal('show');
            }
        });

        // ── Approve ──
        $(document).on('click', '.btn-approve', function() {
            const id = $(this).data('id');
            const currentStatus = $(this).data('status');
            const url = currentStatus === 'submitted' ? `${APPROVE_FOREMAN_URL}/${id}` : `${APPROVE_SUPERVISOR_URL}/${id}`;

            Swal.fire({
                title: 'Konfirmasi Approval',
                text: "Apakah Anda yakin ingin menyetujui laporan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    processApproval(url);
                }
            });
        });

        function processApproval(url) {
            showLoading(true);
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    showLoading(false);
                    Swal.fire('Berhasil!', res.message, 'success');
                    loadData(1);
                },
                error: function(xhr) {
                    showLoading(false);
                    const msg = xhr.responseJSON?.message || 'Gagal memproses approval';
                    Swal.fire('Gagal', msg, 'error');
                }
            });
        }

        // ── Reject ──
        $(document).on('click', '.btn-reject', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Tolak Laporan',
                text: 'Masukkan alasan penolakan:',
                input: 'textarea',
                inputPlaceholder: 'Tulis alasan di sini...',
                inputAttributes: {
                    'aria-label': 'Tulis alasan di sini'
                },
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Tolak Laporan',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan penolakan wajib diisi!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    processReject(id, result.value);
                }
            });
        });

        function processReject(id, reason) {
            showLoading(true);
            $.ajax({
                url: `${REJECT_URL}/${id}`,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    reason: reason
                },
                success: function(res) {
                    showLoading(false);
                    Swal.fire('Ditolak!', res.message, 'info');
                    loadData(1);
                },
                error: function(xhr) {
                    showLoading(false);
                    const msg = xhr.responseJSON?.message || 'Gagal menolak laporan';
                    Swal.fire('Gagal', msg, 'error');
                }
            });
        }

        function getStatusBadge(status) {
            const badges = {
                'submitted': '<span class="badge bg-info">Menunggu Foreman</span>',
                'approved_foreman': '<span class="badge bg-warning text-dark">Menunggu Supervisor</span>',
                'approved_supervisor': '<span class="badge bg-success">Selesai</span>',
                'rejected': '<span class="badge bg-danger">Ditolak</span>'
            };
            return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
        }

        function formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function showLoading(show) {
            if (show) $('#loadingOverlay').removeClass('d-none');
            else $('#loadingOverlay').addClass('d-none');
        }

        function renderDetail(data) {
            const statusBadge = getStatusBadge(data.status);
            const html = `
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Informasi Dasar</label>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted w-40">Tanggal</td><td class="fw-semibold">: ${formatDate(data.tanggal_laporan)}</td></tr>
                            <tr><td class="text-muted">Jam</td><td>: ${data.jam_pencatatan || '-'}</td></tr>
                            <tr><td class="text-muted">Operator</td><td>: ${data.operator?.username || '-'}</td></tr>
                            <tr><td class="text-muted">Status</td><td>: ${statusBadge}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Persetujuan</label>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted w-40">Foreman</td><td>: ${data.foreman?.username || '-'}</td></tr>
                            <tr><td class="text-muted">Supervisor</td><td>: ${data.supervisor?.username || '-'}</td></tr>
                        </table>
                    </div>
                    <div class="col-12">
                        <hr class="my-2 opacity-50">
                        <label class="text-muted small mb-3">Monitoring Teknis</label>
                        <div class="row g-3">
                            ${renderDetailItem('Speed', data.engine_speed, 'RPM')}
                            ${renderDetailItem('Temp', data.engine_temperature, '°C')}
                            ${renderDetailItem('Oil Press', data.engine_oil_pressure, 'Bar')}
                            ${renderDetailItem('Battery', data.battery_voltage, 'V')}
                            ${renderDetailItem('Charge Alt', data.charge_alt_voltage, 'V')}
                            ${renderDetailItem('Freq', data.frequency, 'Hz')}
                            ${renderDetailItem('RH', data.running_hour, 'Hours')}
                        </div>
                    </div>
                </div>
            `;
            $('#detailContent').html(html);
        }

        function renderDetailItem(label, value, unit) {
            return `<div class="col-6 col-md-3"><div class="p-2 border rounded bg-light"><div class="text-muted small">${label}</div><div class="fw-bold">${value || '-'} <small class="text-muted fw-normal">${unit}</small></div></div></div>`;
        }
    });
</script>
@endsection