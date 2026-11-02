@extends('layouts.app')

@section('title', 'Approval Analisa WWTP')

@section('styles')
    <style>
        .page-header-card {
            background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%);
            border-radius: 12px;
        }

        .data-row {
            transition: background-color .2s ease;
        }

        .data-row:hover {
            background-color: rgba(41, 156, 219, .08);
        }

        .detail-table td,
        .detail-table th {
            vertical-align: middle;
        }

        .parameter-detail-card {
            border: 1px solid #e6f2fb;
            border-radius: 8px;
            height: 100%;
        }

        .parameter-detail-card .card-header {
            border-bottom: 1px solid #e6f2fb;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="card border-0 shadow-sm mb-4 page-header-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-check-double-line text-warning me-2"></i>
                                Approval Analisa WWTP
                            </h4>
                            <p class="text-white-50 mb-0 small">
                                Periksa dan berikan persetujuan untuk laporan analisa harian WWTP
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs & Mass Action Buttons Navigation -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-8 col-12">
                    <ul class="nav nav-pills nav-customs nav-danger" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#pending-tab" role="tab"
                                id="btnTabPending">
                                <i class="ri-timer-2-line me-1 align-bottom"></i> Menunggu Approval
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#history-tab" role="tab" id="btnTabHistory">
                                <i class="ri-history-line me-1 align-bottom"></i> Riwayat Approval
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 col-12 text-md-end mt-2 mt-md-0" id="massActionContainer" style="display:none;">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAll">
                            <i class="ri-checkbox-multiple-line me-1"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-success" id="btnMassApprove" disabled>
                            <i class="ri-checkbox-circle-line me-1"></i> Approve Semua (<span
                                class="selected-count">0</span>)
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" id="btnMassReject" disabled>
                            <i class="ri-close-circle-line me-1"></i> Reject Semua (<span class="selected-count">0</span>)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Approval Table Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 50px;">
                                        <div class="form-check" id="checkAllContainer" style="display:none;">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                        <span id="noHeaderLabel">No</span>
                                    </th>
                                    <th>Tanggal</th>
                                    <th>Parameter Uji</th>
                                    <th>Pelaksana</th>
                                    <th>Status Approval</th>
                                    <th class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="approvalTableBody">
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="spinner-border text-info" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- MODAL: Detail & Action --}}
            <div class="modal fade" id="detailAnalisaModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title text-info">
                                <i class="mdi mdi-flask-outline me-2"></i>Detail & Persetujuan Analisa WWTP
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <!-- Header Info -->
                            <div id="modalHeaderContent"></div>

                            <!-- Parameters list -->
                            <div id="modalParametersContent"></div>
                        </div>
                        <div class="modal-footer bg-light" id="modalFooterActions">
                            <!-- Actions loaded via JS based on status -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const userJabatan = "{{ Auth::user()->jabatan ?? '' }}";
            const csrfToken = "{{ csrf_token() }}";
            let activeTab = 'pending';
            let cachedRecords = [];

            // Load Pending on mount
            loadApprovals();

            $('#btnTabPending').on('click', function() {
                activeTab = 'pending';
                loadApprovals();
            });

            $('#btnTabHistory').on('click', function() {
                activeTab = 'history';
                loadApprovals();
            });

            function loadApprovals() {
                showLoading(true);
                $.ajax({
                    url: '/api/wwtp-analisa/approval-list',
                    method: 'GET',
                    data: {
                        tab: activeTab
                    },
                    success: function(response) {
                        cachedRecords = response || [];
                        renderTable(cachedRecords);
                        showLoading(false);
                    },
                    error: function() {
                        showLoading(false);
                        Swal.fire('Error', 'Gagal memuat data approval.', 'error');
                    }
                });
            }

            function renderTable(data) {
                const tbody = $('#approvalTableBody');
                tbody.empty();

                const isSupervisorPending = userJabatan === 'supervisor' && activeTab === 'pending';
                if (isSupervisorPending && data && data.length > 0) {
                    $('#checkAllContainer').show();
                    $('#noHeaderLabel').hide();
                    $('#massActionContainer').show();
                    $('#checkAll').prop('checked', false);
                    updateMassActionState();
                } else {
                    $('#checkAllContainer').hide();
                    $('#noHeaderLabel').show();
                    $('#massActionContainer').hide();
                }

                if (!data || !data.length) {
                    tbody.append(`<tr><td colspan="6" class="text-center py-5 text-muted">
                        <i class="ri-checkbox-circle-line fs-3 text-success d-block mb-2"></i>
                        Tidak ada data analisa untuk ditampilkan.</td></tr>`);
                    return;
                }

                data.forEach(function(item, idx) {
                    const no = idx + 1;

                    // Checkbox column
                    let noCol = no;
                    if (isSupervisorPending) {
                        noCol = `
                            <div class="form-check">
                                <input class="form-check-input row-checkbox" type="checkbox" value="${item.id}">
                            </div>
                        `;
                    }

                    // Parameters badges
                    let paramNames = new Set();
                    if (item.details && item.details.length > 0) {
                        item.details.forEach(d => {
                            if (d.parameter) paramNames.add(d.parameter.parameter_name);
                        });
                    }
                    let paramBadges = '';
                    if (paramNames.size > 0) {
                        paramNames.forEach(name => {
                            paramBadges +=
                                `<span class="badge bg-soft-info text-info me-1">${name}</span>`;
                        });
                    } else {
                        paramBadges = `<span class="text-muted small">-</span>`;
                    }

                    // Status Badge
                    let statusBadge = '';
                    const status = item.status || 'submitted';
                    if (status === 'submitted') {
                        statusBadge =
                            '<span class="badge bg-warning text-dark"><i class="mdi mdi-clock-outline me-1"></i>Menunggu Foreman</span>';
                    } else if (status === 'approved_foreman') {
                        statusBadge =
                            '<span class="badge bg-info"><i class="mdi mdi-account-clock-outline me-1"></i>Menunggu Spv</span>';
                    } else if (status === 'approved_supervisor') {
                        statusBadge =
                            '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Selesai</span>';
                    } else if (status === 'rejected') {
                        const reason = item.reject_reason ?
                            ` title="Alasan: ${escapeHtml(item.reject_reason)}"` : '';
                        statusBadge =
                            `<span class="badge bg-danger"${reason}><i class="mdi mdi-close-circle-outline me-1"></i>Ditolak</span>`;
                    }

                    // Action buttons
                    let actions = `<button class="btn btn-sm btn-info" onclick="showApprovalDetail(${item.id})">
                        <i class="ri-eye-line align-bottom me-1"></i> Periksa</button>`;

                    tbody.append(`
                        <tr class="data-row">
                            <td class="ps-4">${noCol}</td>
                            <td><span class="badge bg-light text-dark border"><i class="mdi mdi-calendar me-1"></i>${formatDate(item.analisa_date)}</span></td>
                            <td>${paramBadges}</td>
                            <td>${item.pelaksana ? item.pelaksana.username : '-'}</td>
                            <td>${statusBadge}</td>
                            <td class="text-center pe-4">${actions}</td>
                        </tr>
                    `);
                });
            }

            window.showApprovalDetail = function(id) {
                const record = cachedRecords.find(r => r.id === id);
                if (!record) return;

                // Load Header Info
                let statusBadge = '';
                const status = record.status || 'submitted';
                if (status === 'submitted') {
                    statusBadge =
                        '<span class="badge bg-warning text-dark"><i class="mdi mdi-clock-outline me-1"></i>Menunggu Foreman</span>';
                } else if (status === 'approved_foreman') {
                    statusBadge =
                        '<span class="badge bg-info"><i class="mdi mdi-account-clock-outline me-1"></i>Menunggu Spv</span>';
                } else if (status === 'approved_supervisor') {
                    statusBadge =
                        '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Selesai</span>';
                } else if (status === 'rejected') {
                    statusBadge =
                        '<span class="badge bg-danger"><i class="mdi mdi-close-circle-outline me-1"></i>Ditolak</span>';
                }

                let rejectReasonHtml = '';
                if (status === 'rejected' && record.reject_reason) {
                    rejectReasonHtml = `
                        <div class="col-12 mt-2">
                            <div class="alert alert-danger mb-0 py-2">
                                <i class="mdi mdi-alert-circle-outline me-1"></i><strong>Alasan Penolakan:</strong> ${escapeHtml(record.reject_reason)}
                            </div>
                        </div>
                    `;
                }

                let headerHtml = `
                     <div class="row g-3 mb-4">
                         <div class="col-md-3">
                             <div class="info-box p-3 bg-light rounded border border-info h-100">
                                 <p class="text-muted small mb-1">Tanggal Analisa</p>
                                 <p class="fw-bold mb-0 fs-6">${formatDate(record.analisa_date)}</p>
                             </div>
                         </div>
                         <div class="col-md-2">
                             <div class="info-box p-3 bg-light rounded border border-info h-100">
                                 <p class="text-muted small mb-1">Pelaksana</p>
                                 <p class="fw-bold mb-0 fs-6">${record.pelaksana ? record.pelaksana.username : '-'}</p>
                             </div>
                         </div>
                         <div class="col-md-2">
                             <div class="info-box p-3 bg-light rounded border border-info h-100">
                                 <p class="text-muted small mb-1">Foreman</p>
                                 <p class="fw-bold mb-0 fs-6">${record.foreman ? record.foreman.username : '-'}</p>
                             </div>
                         </div>
                         <div class="col-md-2">
                             <div class="info-box p-3 bg-light rounded border border-info h-100">
                                 <p class="text-muted small mb-1">Supervisor</p>
                                 <p class="fw-bold mb-0 fs-6">${record.supervisor ? record.supervisor.username : '-'}</p>
                             </div>
                         </div>
                         <div class="col-md-3">
                             <div class="info-box p-3 bg-light rounded border border-info h-100">
                                 <p class="text-muted small mb-1">Status Approval</p>
                                 <p class="mb-0 fs-6">${statusBadge}</p>
                             </div>
                         </div>
                         ${rejectReasonHtml}
                     </div>
                     <h6 class="fw-bold mb-3 text-info border-bottom pb-2">Detail Parameter Pengukuran</h6>
                 `;
                $('#modalHeaderContent').html(headerHtml);

                // Load Parameters
                let parameterData = {};
                if (record.details && record.details.length > 0) {
                    record.details.forEach(d => {
                        const paramId = d.parameter_id;
                        const param = d.parameter || {};

                        if (!parameterData[paramId]) {
                            parameterData[paramId] = {
                                id: paramId,
                                name: param.parameter_name || 'Unknown Parameter',
                                unit: param.unit || '',
                                points: []
                            };
                        }

                        parameterData[paramId].points.push({
                            pointName: d.point ? d.point.point_name : 'Unknown Point',
                            value: d.hasil_analisa
                        });
                    });
                }

                let pointsHtml = '<div class="row g-3 mt-1">';
                Object.values(parameterData).forEach(param => {
                    let rowsHtml = '';
                    param.points.forEach(point => {
                        const unit = param.unit ? ` ${escapeHtml(param.unit)}` : '';
                        rowsHtml += `
                            <tr>
                                <td class="fw-semibold">${escapeHtml(point.pointName)}</td>
                                <td class="text-center">
                                    <span class="fw-semibold text-info">${num(point.value)}${unit}</span>
                                </td>
                            </tr>
                        `;
                    });

                    pointsHtml += `
                        <div class="col-lg-6">
                            <div class="parameter-detail-card">
                                <div class="card-header bg-light p-3">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <h6 class="fw-bold text-info mb-1">${escapeHtml(param.name)}</h6>
                                            <small class="text-muted">Hasil analisa parameter</small>
                                        </div>
                                        <span class="badge bg-soft-info text-info">${escapeHtml(param.unit || '-')}</span>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover detail-table mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Point Pengukuran</th>
                                                <th class="text-center">Hasil Analisa</th>
                                            </tr>
                                        </thead>
                                        <tbody>${rowsHtml}</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;
                });

                if (!Object.keys(parameterData).length) {
                    pointsHtml +=
                        '<div class="col-12 text-center py-4 text-muted">Tidak ada data hasil pengukuran</div>';
                }
                pointsHtml += '</div>';
                $('#modalParametersContent').html(pointsHtml);

                // Load Action Buttons in Footer
                let footerActions =
                    `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>`;

                const isPending = activeTab === 'pending';
                const isMyTurn = (userJabatan === 'foreman' && status === 'submitted') ||
                    (userJabatan === 'supervisor' && status === 'approved_foreman');

                if (isPending && isMyTurn) {
                    footerActions = `
                        <button type="button" class="btn btn-danger" onclick="rejectRecord(${record.id})">
                            <i class="ri-close-circle-line me-1"></i> Tolak
                        </button>
                        <button type="button" class="btn btn-success" onclick="approveRecord(${record.id})">
                            <i class="ri-checkbox-circle-line me-1"></i> Setujui
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    `;
                }
                $('#modalFooterActions').html(footerActions);

                $('#detailAnalisaModal').modal('show');
            };

            window.approveRecord = function(id) {
                Swal.fire({
                    title: 'Konfirmasi Setuju',
                    text: 'Apakah Anda yakin ingin menyetujui data analisa WWTP ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ya, Setujui',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        showLoading(true);
                        $.ajax({
                            url: `/api/wwtp-analisa/${id}/approve`,
                            method: 'POST',
                            data: {
                                _token: csrfToken
                            },
                            success: function(res) {
                                showLoading(false);
                                $('#detailAnalisaModal').modal('hide');
                                Swal.fire('Berhasil!', res.message, 'success');
                                loadApprovals();
                            },
                            error: function(xhr) {
                                showLoading(false);
                                Swal.fire('Gagal!', xhr.responseJSON?.message ||
                                    'Gagal memproses approval.', 'error');
                            }
                        });
                    }
                });
            };

            window.rejectRecord = function(id) {
                Swal.fire({
                    title: 'Tolak Laporan',
                    text: 'Masukkan alasan penolakan:',
                    input: 'textarea',
                    inputPlaceholder: 'Alasan penolakan...',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Tolak',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Alasan penolakan wajib diisi!'
                        }
                    }
                }).then(result => {
                    if (result.isConfirmed && result.value) {
                        showLoading(true);
                        $.ajax({
                            url: `/api/wwtp-analisa/${id}/reject`,
                            method: 'POST',
                            data: {
                                _token: csrfToken,
                                reason: result.value
                            },
                            success: function(res) {
                                showLoading(false);
                                $('#detailAnalisaModal').modal('hide');
                                Swal.fire('Ditolak!', res.message, 'info');
                                loadApprovals();
                            },
                            error: function(xhr) {
                                showLoading(false);
                                Swal.fire('Gagal!', xhr.responseJSON?.message ||
                                    'Gagal menolak laporan.', 'error');
                            }
                        });
                    }
                });
            };

            // Handle Check All
            // Handle Check All Checkbox
            $(document).on('change', '#checkAll', function() {
                const isChecked = $(this).is(':checked');
                $('.row-checkbox').prop('checked', isChecked);
                updateMassActionState();
            });

            // Handle Row Checkbox Change
            $(document).on('change', '.row-checkbox', function() {
                const total = $('.row-checkbox').length;
                const checked = $('.row-checkbox:checked').length;
                $('#checkAll').prop('checked', total === checked);
                updateMassActionState();
            });

            // Handle Select All Button
            $('#btnSelectAll').on('click', function() {
                const total = $('.row-checkbox').length;
                const checked = $('.row-checkbox:checked').length;

                if (checked < total) {
                    $('.row-checkbox').prop('checked', true);
                    $('#checkAll').prop('checked', true);
                } else {
                    $('.row-checkbox').prop('checked', false);
                    $('#checkAll').prop('checked', false);
                }
                updateMassActionState();
            });

            function updateMassActionState() {
                const total = $('.row-checkbox').length;
                const checkedCount = $('.row-checkbox:checked').length;
                $('.selected-count').text(checkedCount);

                if (checkedCount > 0) {
                    $('#btnMassApprove, #btnMassReject').prop('disabled', false);
                } else {
                    $('#btnMassApprove, #btnMassReject').prop('disabled', true);
                }

                // Update btnSelectAll text and icon
                if (total > 0 && checkedCount === total) {
                    $('#btnSelectAll').html('<i class="ri-checkbox-multiple-blank-line me-1"></i> Batal Pilih');
                } else {
                    $('#btnSelectAll').html('<i class="ri-checkbox-multiple-line me-1"></i> Pilih Semua');
                }
            }

            // Mass Approve Action
            $('#btnMassApprove').on('click', function() {
                const selectedIds = [];
                $('.row-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (!selectedIds.length) return;

                Swal.fire({
                    title: 'Konfirmasi Mass Approval',
                    text: `Apakah Anda yakin ingin menyetujui ${selectedIds.length} data analisa WWTP terpilih?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ya, Setujui Semua',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        showLoading(true);
                        $.ajax({
                            url: "/api/wwtp-analisa/mass-approve",
                            method: 'POST',
                            data: {
                                _token: csrfToken,
                                ids: selectedIds
                            },
                            success: function(res) {
                                showLoading(false);
                                Swal.fire('Berhasil!', res.message, 'success');
                                loadApprovals();
                            },
                            error: function(xhr) {
                                showLoading(false);
                                Swal.fire('Gagal!', xhr.responseJSON?.message ||
                                    'Gagal memproses mass approval.', 'error');
                            }
                        });
                    }
                });
            });

            // Mass Reject Action
            $('#btnMassReject').on('click', function() {
                const selectedIds = [];
                $('.row-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (!selectedIds.length) return;

                Swal.fire({
                    title: 'Mass Reject Laporan',
                    text: `Masukkan alasan penolakan untuk ${selectedIds.length} data terpilih:`,
                    input: 'textarea',
                    inputPlaceholder: 'Alasan penolakan massal...',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Tolak Semua',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Alasan penolakan wajib diisi!'
                        }
                    }
                }).then(result => {
                    if (result.isConfirmed && result.value) {
                        showLoading(true);
                        $.ajax({
                            url: "/api/wwtp-analisa/mass-reject",
                            method: 'POST',
                            data: {
                                _token: csrfToken,
                                ids: selectedIds,
                                reason: result.value
                            },
                            success: function(res) {
                                showLoading(false);
                                Swal.fire('Ditolak!', res.message, 'info');
                                loadApprovals();
                            },
                            error: function(xhr) {
                                showLoading(false);
                                Swal.fire('Gagal!', xhr.responseJSON?.message ||
                                    'Gagal memproses mass reject.', 'error');
                            }
                        });
                    }
                });
            });

            function showLoading(show) {
                if (show) $('#loadingOverlay').removeClass('d-none');
                else $('#loadingOverlay').addClass('d-none');
            }

            const num = (number) => {
                if (number == null || number === '') return '-';
                const parsed = parseFloat(number);
                return parsed % 1 === 0 ? parsed.toFixed(0) : parsed.toString();
            };

            function formatDate(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }
        });
    </script>
@endsection
