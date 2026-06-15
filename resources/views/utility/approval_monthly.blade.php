@extends('layouts.app')

@section('title', 'Approval Utility')

@section('styles')
    <style>
        .nav-tabs-custom {
            border-bottom: 2px solid #e9ecef;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            color: #495057;
            font-weight: 600;
            padding: 0.75rem 1.25rem;
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-tabs-custom .nav-link.active {
            color: #4b39b5;
            background: transparent;
        }

        .nav-tabs-custom .nav-link.active::after {
            content: "";
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #4b39b5;
        }

        .table-responsive {
            max-height: 450px;
            overflow-y: auto;
        }

        [data-layout-mode="dark"] .bg-light {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="card border-0 shadow-sm mb-3"
                style="background: linear-gradient(135deg, #4b39b5 0%, #2d3561 100%); border-radius: 12px;">
                <div class="card-body">
                    <h4 class="text-white fw-bold mb-1">
                        <i class="ri-checkbox-circle-line text-warning me-2"></i> Utility - Monthly Approval
                    </h4>
                    <p class="text-white-50 mb-0">Persetujuan laporan bulanan Pemakaian Listrik, Air, dan Chemical</p>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#pending-approvals" role="tab"
                        id="tab-pending">
                        <i class="ri-time-line me-1"></i> Menunggu Approval
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#history-approvals" role="tab" id="tab-history">
                        <i class="ri-history-line me-1"></i> Riwayat Approval
                    </a>
                </li>
            </ul>

            <!-- Tab Contents -->
            <div class="tab-content text-muted">
                <div class="tab-pane active" id="pending-approvals" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle text-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Tipe</th>
                                            <th>Operator Pengirim</th>
                                            <th>Foreman</th>
                                            <th>Supervisor</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyPending">
                                        <tr>
                                            <td colspan="6" class="text-center py-3">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                </div> Loading...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="history-approvals" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle text-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Tipe</th>
                                            <th>Operator Pengirim</th>
                                            <th>Foreman</th>
                                            <th>Supervisor</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyHistory">
                                        <tr>
                                            <td colspan="6" class="text-center py-3">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                </div> Loading...
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

    <!-- Modal Review Detail -->
    <div class="modal fade" id="modalReview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white"><i class="ri-file-list-3-line me-2"></i> Review Detail Utility</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="reviewHeader" class="mb-3 p-3 bg-light rounded shadow-sm"></div>

                    <!-- Inner Tabs for Utility Types -->
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-listrik-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-listrik" type="button" role="tab"><i
                                    class="ri-flashlight-line me-1"></i> Pemakaian Listrik</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-air-tab" data-bs-toggle="pill" data-bs-target="#pills-air"
                                type="button" role="tab"><i class="ri-drop-line me-1"></i> Pemakaian Air</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-chemical-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-chemical" type="button" role="tab"><i
                                    class="ri-flask-line me-1"></i> Pemakaian Chemical</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <!-- Listrik Tab -->
                        <div class="tab-pane fade show active" id="pills-listrik" role="tabpanel">
                            <div class="table-responsive border rounded">
                                <table class="table table-sm table-striped table-bordered align-middle text-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Panel</th>
                                            <th>Volt</th>
                                            <th>Ampere</th>
                                            <th>KW</th>
                                            <th>MWh</th>
                                            <th>Cos φ</th>
                                            <th>Usage</th>
                                            <th>Operator</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyReviewListrik"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Air Tab -->
                        <div class="tab-pane fade" id="pills-air" role="tabpanel">
                            <div class="table-responsive border rounded">
                                <table class="table table-sm table-striped table-bordered align-middle text-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Area</th>
                                            <th>Awal (m³)</th>
                                            <th>Akhir (m³)</th>
                                            <th>Total (m³)</th>
                                            <th>Catatan</th>
                                            <th>Operator</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyReviewAir"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Chemical Tab -->
                        <div class="tab-pane fade" id="pills-chemical" role="tabpanel">
                            <div class="table-responsive border rounded">
                                <table class="table table-sm table-striped table-bordered align-middle text-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Shift</th>
                                            <th>Area</th>
                                            <th>Jenis Chemical</th>
                                            <th>Jumlah Pemakaian</th>
                                            <th>Running Hour</th>
                                            <th>Catatan</th>
                                            <th>Operator</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyReviewChemical"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="modalReviewFooter"></div>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white"><i class="ri-close-circle-line me-2"></i> Reject Laporan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="reject_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Penolakan</label>
                        <textarea id="reject_reason" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..."
                            required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="submitReject()">Kirim Rejection</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const currentUserId = "{{ auth()->id() }}";
        const userJabatan = "{{ auth()->user()->jabatan }}";

        function loadApprovals(tab = 'pending') {
            const tbody = tab === 'pending' ? $('#tbodyPending') : $('#tbodyHistory');
            tbody.html(
                '<tr><td colspan="7" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</td></tr>'
                );

            $.get("{{ url('utility/approval/list') }}", {
                tab: tab
            }, function(res) {
                let html = '';

                if (res.length === 0) {
                    tbody.html(
                        '<tr><td colspan="7" class="text-center py-3 text-muted">Tidak ada data approval ditemukan.</td></tr>'
                        );
                    return;
                }

                res.forEach(item => {
                    let badge = '';
                    if (item.status === 'submitted') {
                        badge =
                            '<span class="badge bg-warning-subtle text-warning fs-12">Menunggu Foreman</span>';
                    } else if (item.status === 'approved_foreman') {
                        badge =
                            '<span class="badge bg-info-subtle text-info fs-12">Menunggu Supervisor</span>';
                    } else if (item.status === 'approved_supervisor') {
                        badge =
                            '<span class="badge bg-success-subtle text-success fs-12">Disetujui SPV</span>';
                    } else if (item.status === 'rejected') {
                        badge = '<span class="badge bg-danger-subtle text-danger fs-12">Ditolak</span>';
                    }

                    let actions = '';
                    const canApproveFM = (item.status === 'submitted' && currentUserId == item.foreman_id &&
                        userJabatan === 'foreman');
                    const canApproveSPV = (item.status === 'approved_foreman' && currentUserId == item
                        .supervisor_id && userJabatan === 'supervisor');

                    if (canApproveFM || canApproveSPV) {
                        actions = `
                        <button class="btn btn-sm btn-success px-2 py-1" onclick="approveRecord(${item.id}, '${item.status}')">
                            <i class="ri-check-line"></i> Approve
                        </button>
                        <button class="btn btn-sm btn-danger px-2 py-1" onclick="openReject(${item.id})">
                            <i class="ri-close-line"></i> Reject
                        </button>
                    `;
                    }

                    const mStart = moment(item.bulan + '-01');
                    const bulanFormatted = mStart.format('MMMM YYYY');
                    let typeBadge = '';
                    if (item.tipe === 'listrik') {
                        typeBadge = '<span class="badge bg-danger">Listrik</span>';
                    } else if (item.tipe === 'air') {
                        typeBadge = '<span class="badge bg-primary">Air</span>';
                    } else if (item.tipe === 'chemical') {
                        typeBadge = '<span class="badge bg-secondary">Chemical</span>';
                    }

                    html += `
                    <tr>
                        <td><strong>${bulanFormatted}</strong></td>
                        <td>${typeBadge}</td>
                        <td>${item.operator?.username || '-'}</td>
                        <td>${item.foreman?.username || '-'}</td>
                        <td>${item.supervisor?.username || '-'}</td>
                        <td>${badge}</td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm btn-info px-2 py-1" onclick="reviewDetail(${item.id})">
                                    <i class="ri-eye-line"></i> Review
                                </button>
                                ${actions}
                            </div>
                        </td>
                    </tr>
                `;
                });

                tbody.html(html);
            }).fail(function() {
                tbody.html('<tr><td colspan="7" class="text-center text-danger py-3">Gagal memuat data.</td></tr>');
            });
        }

        window.reviewDetail = function(id) {
            // Clear previous values
            $('#tbodyReviewListrik').html(
                '<tr><td colspan="9" class="text-center py-2"><div class="spinner-border spinner-border-sm text-primary"></div> Loading...</td></tr>'
                );
            $('#tbodyReviewAir').html(
                '<tr><td colspan="7" class="text-center py-2"><div class="spinner-border spinner-border-sm text-primary"></div> Loading...</td></tr>'
                );
            $('#tbodyReviewChemical').html(
                '<tr><td colspan="8" class="text-center py-2"><div class="spinner-border spinner-border-sm text-primary"></div> Loading...</td></tr>'
                );
            $('#modalReviewFooter').empty();

            $.get("{{ url('utility/approval/show') }}/" + id, function(res) {
                const h = res.approval;
                const mStart = moment(h.bulan + '-01');
                const bulanFormatted = mStart.format('MMMM YYYY');

                // Hide all tabs and buttons first
                $('#pills-listrik-tab, #pills-air-tab, #pills-chemical-tab').removeClass('active').parent()
                    .hide();
                $('#pills-listrik, #pills-air, #pills-chemical').removeClass('show active');

                // Show and activate the corresponding tab
                if (h.tipe === 'listrik') {
                    $('#pills-listrik-tab').addClass('active').parent().show();
                    $('#pills-listrik').addClass('show active');
                } else if (h.tipe === 'air') {
                    $('#pills-air-tab').addClass('active').parent().show();
                    $('#pills-air').addClass('show active');
                } else if (h.tipe === 'chemical') {
                    $('#pills-chemical-tab').addClass('active').parent().show();
                    $('#pills-chemical').addClass('show active');
                }

                let tipeText = h.tipe.charAt(0).toUpperCase() + h.tipe.slice(1);
                $('#reviewHeader').html(`
                <div class="row">
                    <div class="col-md-3"><strong>Bulan:</strong> ${bulanFormatted}</div>
                    <div class="col-md-3"><strong>Tipe:</strong> <span class="badge bg-secondary">${tipeText}</span></div>
                    <div class="col-md-3"><strong>Operator Pengirim:</strong> ${h.operator?.username || '-'}</div>
                    <div class="col-md-3"><strong>Status:</strong> <span class="badge bg-primary">${h.status.toUpperCase()}</span></div>
                </div>
                ${h.reject_reason ? `<div class="row mt-2"><div class="col-12 text-danger"><strong>Alasan Penolakan:</strong> ${h.reject_reason}</div></div>` : ''}
            `);

                // 1. Render Listrik
                let listrikHtml = '';
                if (res.listrik && res.listrik.length > 0) {
                    res.listrik.forEach(l => {
                        listrikHtml += `
                        <tr>
                            <td>${moment(l.waktu).format('DD/MM/YYYY')}</td>
                            <td><span class="badge bg-secondary-subtle text-secondary">${l.panel_type}</span></td>
                            <td>${l.volt ?? '-'}</td>
                            <td>${l.a ?? '-'}</td>
                            <td>${l.kw ?? '-'}</td>
                            <td>${l.mwh ?? '-'}</td>
                            <td>${l.cos ?? '-'}</td>
                            <td><strong>${l.usage !== null ? parseFloat(l.usage).toFixed(3) : '-'}</strong></td>
                            <td>${l.operator || '-'}</td>
                        </tr>
                    `;
                    });
                } else {
                    listrikHtml =
                        '<tr><td colspan="9" class="text-center py-2 text-muted">Tidak ada data listrik.</td></tr>';
                }
                $('#tbodyReviewListrik').html(listrikHtml);

                // 2. Render Air
                let airHtml = '';
                if (res.air && res.air.length > 0) {
                    res.air.forEach(a => {
                        const total = parseFloat(a.pemakaian_akhir) - parseFloat(a.pemakaian_awal);
                        airHtml += `
                        <tr>
                            <td>${moment(a.tanggal).format('DD/MM/YYYY')}</td>
                            <td><span class="badge bg-primary-subtle text-primary">${a.jenis_pemakaian}</span></td>
                            <td>${a.pemakaian_awal}</td>
                            <td>${a.pemakaian_akhir}</td>
                            <td><span class="badge bg-success">${total.toFixed(2)}</span></td>
                            <td>${a.notes || '-'}</td>
                            <td>${a.created_by || '-'}</td>
                        </tr>
                    `;
                    });
                } else {
                    airHtml =
                        '<tr><td colspan="7" class="text-center py-2 text-muted">Tidak ada data air.</td></tr>';
                }
                $('#tbodyReviewAir').html(airHtml);

                // 3. Render Chemical
                let chemicalHtml = '';
                if (res.chemical && res.chemical.length > 0) {
                    res.chemical.forEach(c => {
                        chemicalHtml += `
                        <tr>
                            <td>${moment(c.tanggal).format('DD/MM/YYYY')}</td>
                            <td><span class="badge bg-warning-subtle text-warning">${c.shift}</span></td>
                            <td>${c.chemical_area}</td>
                            <td>${c.jenis_pemakaian}</td>
                            <td>${c.nilai_pemakaian}</td>
                            <td>${c.running_hour ?? '-'}</td>
                            <td>${c.notes || '-'}</td>
                            <td>${c.operator || '-'}</td>
                        </tr>
                    `;
                    });
                } else {
                    chemicalHtml =
                        '<tr><td colspan="8" class="text-center py-2 text-muted">Tidak ada data chemical.</td></tr>';
                }
                $('#tbodyReviewChemical').html(chemicalHtml);

                // Modal footer action buttons
                const canApproveFM = (h.status === 'submitted' && currentUserId == h.foreman_id &&
                    userJabatan === 'foreman');
                const canApproveSPV = (h.status === 'approved_foreman' && currentUserId == h.supervisor_id &&
                    userJabatan === 'supervisor');

                let footerHtml =
                    '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>';
                if (canApproveFM || canApproveSPV) {
                    footerHtml += `
                    <button class="btn btn-danger" onclick="triggerRejectFromReview(${h.id})"><i class="ri-close-line"></i> Tolak</button>
                    <button class="btn btn-success" onclick="triggerApproveFromReview(${h.id}, '${h.status}')"><i class="ri-check-line"></i> Setujui</button>
                `;
                }
                $('#modalReviewFooter').html(footerHtml);

                $('#modalReview').modal('show');
            });
        }

        window.approveRecord = function(id, currentStatus) {
            let stepText = currentStatus === 'submitted' ?
                'Laporan akan disetujui sebagai Foreman dan diteruskan ke Supervisor.' :
                'Laporan akan selesai disetujui sepenuhnya oleh Supervisor.';

            Swal.fire({
                title: 'Setujui Laporan Bulanan?',
                text: stepText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        didOpen: () => {
                            Swal.showLoading()
                        },
                        allowOutsideClick: false
                    });

                    $.post(`{{ url('utility/approval') }}/${id}/approve`, {
                        _token: "{{ csrf_token() }}"
                    }, function(res) {
                        Swal.fire('Berhasil!', res.message, 'success');
                        loadApprovals('pending');
                        loadApprovals('history');
                    }).fail(function(xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    });
                }
            });
        }

        window.triggerApproveFromReview = function(id, currentStatus) {
            $('#modalReview').modal('hide');
            setTimeout(() => {
                approveRecord(id, currentStatus);
            }, 300);
        }

        window.openReject = function(id) {
            $('#reject_id').val(id);
            $('#reject_reason').val('');
            $('#modalReject').modal('show');
        }

        window.triggerRejectFromReview = function(id) {
            $('#modalReview').modal('hide');
            setTimeout(() => {
                openReject(id);
            }, 300);
        }

        window.submitReject = function() {
            const id = $('#reject_id').val();
            const reason = $('#reject_reason').val();
            if (!reason) {
                Swal.fire('Info', 'Alasan penolakan wajib diisi.', 'info');
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                didOpen: () => {
                    Swal.showLoading()
                },
                allowOutsideClick: false
            });

            $.post(`{{ url('utility/approval') }}/${id}/reject`, {
                _token: "{{ csrf_token() }}",
                reason: reason
            }, function(res) {
                $('#modalReject').modal('hide');
                Swal.fire('Berhasil ditolak!', res.message, 'info');
                loadApprovals('pending');
                loadApprovals('history');
            }).fail(function(xhr) {
                Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
            });
        }

        $(document).ready(function() {
            // Load initial approvals
            loadApprovals('pending');
            loadApprovals('history');

            // Tab changes
            $('#tab-pending').on('click', function() {
                loadApprovals('pending');
            });
            $('#tab-history').on('click', function() {
                loadApprovals('history');
            });
        });
    </script>
@endsection
