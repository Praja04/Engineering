@extends('layouts.app')

@section('title', 'Approval Bulanan Agenda AHU')

@section('styles')
    <style>
        .collapse-trigger[aria-expanded="true"] .transition-icon {
            transform: rotate(180deg);
        }

        .transition-icon {
            transition: transform 0.2s ease;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #0f3057 0%, #00587a 100%); border-radius: 12px;">
                        <div class="card-body">
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-checkbox-multiple-line text-warning me-2"></i>
                                Agenda AHU - Approval
                            </h4>
                            <p class="text-white-50 mb-0">
                                Persetujuan laporan bulanan checklist Agenda AHU
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Periode</th>
                                    <th>Operator</th>
                                    <th>Foreman</th>
                                    <th>Supervisor</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyApproval">
                                <!-- Data AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div id="paginationInfo"></div>
                        <nav>
                            <ul class="pagination mb-0" id="paginationLinks"></ul>
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Detail Laporan Bulanan -->
    <div class="modal fade" id="modalMonthlyDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalMonthlyTitle">Detail Data Bulanan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Informational header -->
                    <div class="row g-3 mb-4 p-3 bg-light rounded shadow-sm border mx-0">
                        <div class="col-md-4">
                            <strong>Operator:</strong> <span id="hdr_operator">-</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Foreman:</strong> <span id="hdr_foreman">-</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Supervisor:</strong> <span id="hdr_supervisor">-</span>
                        </div>
                    </div>

                    <!-- Scrollable checklist listings -->
                    <div id="modalMonthlyContent" class="px-2">
                        <!-- Dynamic items -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white">Tolak Laporan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formReject">
                        @csrf
                        <input type="hidden" name="reject_id" id="reject_id">
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan</label>
                            <textarea name="reason" id="reject_reason" class="form-control" rows="3" required
                                placeholder="Tulis alasan penolakan..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="submitReject()">Tolak Laporan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const API_URL = "{{ route('agenda-ahu.get-approval-data') }}";
        const CURRENT_USER_ID = {{ Auth::id() }};
        const JABATAN = "{{ Auth::user()->jabatan }}";
        let currentPage = 1;

        function loadApproval(page = 1) {
            currentPage = page;

            $.ajax({
                url: API_URL,
                type: "GET",
                data: {
                    mode: 'approval',
                    page: page
                },
                success: function(res) {
                    let html = '';
                    res.data.forEach(item => {
                        let statusBadge = '';
                        if (item.status === 'draft') statusBadge =
                            '<span class="badge bg-warning">Draft</span>';
                        else if (item.status === 'submitted') statusBadge =
                            '<span class="badge bg-info">Submitted</span>';
                        else if (item.status === 'approved_foreman') statusBadge =
                            '<span class="badge bg-primary">Approve Foreman</span>';
                        else if (item.status === 'approved_supervisor') statusBadge =
                            '<span class="badge bg-success">Approved</span>';
                        else if (item.status === 'rejected') statusBadge =
                            '<span class="badge bg-danger">Rejected</span>';

                        let monthName = moment().month(item.bulan - 1).format('MMMM');

                        let actionButtons = '';
                        let canApprove = false;

                        if (JABATAN === 'foreman' && item.status === 'submitted' && item
                            .foreman_id == CURRENT_USER_ID) {
                            canApprove = true;
                        } else if (JABATAN === 'supervisor' && item.status === 'approved_foreman' &&
                            item.supervisor_id == CURRENT_USER_ID) {
                            canApprove = true;
                        }

                        if (canApprove) {
                            actionButtons = `
                            <button class="btn btn-sm btn-success px-2 py-1 rounded-pill" onclick="approveSingle(${item.id}, '${item.status}')">
                                <i class="ri-checkbox-circle-line"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-outline-danger px-2 py-1 rounded-pill" onclick="openReject(${item.id})">
                                <i class="ri-close-circle-line"></i> Reject
                            </button>
                        `;
                        } else {
                            actionButtons = `<span class="text-muted small">No Action Required</span>`;
                        }

                        html += `
                        <tr>
                            <td class="fw-bold">${monthName} ${item.tahun}</td>
                            <td>${item.operator ? item.operator.username : '-'}</td>
                            <td>${item.foreman ? item.foreman.username : '-'}</td>
                            <td>${item.supervisor ? item.supervisor.username : '-'}</td>
                            <td>${statusBadge}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <button class="btn btn-sm btn-info rounded-pill px-3" onclick="showDetails(${item.id})">
                                        <i class="ri-eye-line me-1"></i> Review Data
                                    </button>
                                    ${actionButtons}
                                </div>
                            </td>
                        </tr>
                    `;
                    });

                    if (res.data.length === 0) {
                        html =
                            '<tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada pengajuan untuk disetujui</td></tr>';
                    }

                    $('#tbodyApproval').html(html);
                    renderPagination(res.pagination);
                }
            });
        }

        const FIELDS_KELISTRIKAN_PRESSURE = [{
                field: 'kelistrikan_ahu_1',
                label: 'Cek kelistrikan (A,V) AHU 1'
            },
            {
                field: 'kelistrikan_ahu_2',
                label: 'Cek kelistrikan (A,V) AHU 2'
            },
            {
                field: 'kelistrikan_ahu_3',
                label: 'Cek kelistrikan (A,V) AHU 3'
            },
            {
                field: 'kelistrikan_ahu_4',
                label: 'Cek kelistrikan (A,V) AHU 4'
            },
            {
                field: 'pressur_gauge_in_ahu_1',
                label: 'Cek pressur gauge in AHU 1'
            },
            {
                field: 'pressur_gauge_in_ahu_2',
                label: 'Cek pressur gauge in AHU 2'
            },
            {
                field: 'pressur_gauge_in_ahu_3',
                label: 'Cek pressur gauge in AHU 3'
            },
            {
                field: 'pressur_gauge_in_ahu_4',
                label: 'Cek pressur gauge in AHU 4'
            },
            {
                field: 'pressur_gauge_out_ahu_1',
                label: 'Cek pressur gauge out AHU 1'
            },
            {
                field: 'pressur_gauge_out_ahu_2',
                label: 'Cek pressur gauge out AHU 2'
            },
            {
                field: 'pressur_gauge_out_ahu_3',
                label: 'Cek pressur gauge out AHU 3'
            },
            {
                field: 'pressur_gauge_out_ahu_4',
                label: 'Cek pressur gauge out AHU 4'
            }
        ];

        const FIELDS_TEMPERATURE = [{
                field: 'temp_gauge_in_ahu_1',
                label: 'Cek temperature gauge in AHU 1'
            },
            {
                field: 'temp_gauge_in_ahu_2',
                label: 'Cek temperature gauge in AHU 2'
            },
            {
                field: 'temp_gauge_in_ahu_3',
                label: 'Cek temperature gauge in AHU 3'
            },
            {
                field: 'temp_gauge_in_ahu_4',
                label: 'Cek temperature gauge in AHU 4'
            },
            {
                field: 'temp_gauge_out_ahu_1',
                label: 'Cek temperature gauge out AHU 1'
            },
            {
                field: 'temp_gauge_out_ahu_2',
                label: 'Cek temperature gauge out AHU 2'
            },
            {
                field: 'temp_gauge_out_ahu_3',
                label: 'Cek temperature gauge out AHU 3'
            },
            {
                field: 'temp_gauge_out_ahu_4',
                label: 'Cek temperature gauge out AHU 4'
            }
        ];

        const FIELDS_CLEAN_INSPEKSI = [{
                field: 'clean_filter_strainer_1',
                label: 'Cleaning filter udara & strainer 1'
            },
            {
                field: 'clean_filter_strainer_2',
                label: 'Cleaning filter udara & strainer 2'
            },
            {
                field: 'clean_filter_strainer_3',
                label: 'Cleaning filter udara & strainer 3'
            },
            {
                field: 'clean_filter_strainer_4',
                label: 'Cleaning filter udara & strainer 4'
            },
            {
                field: 'clean_filter_bebas_ahu',
                label: 'Cleaning filter udara bebas ke AHU'
            },
            {
                field: 'inspeksi_h_ahu_1_4',
                label: 'Inspeksi (H) AHU 1 s/d 4'
            }
        ];

        function buildChecklistStatusHtml(item, fieldList) {
            let listHtml = '<ul class="list-group list-group-flush">';
            fieldList.forEach(f => {
                let badge = '<span class="badge bg-secondary">-</span>';
                if (item[f.field] === 'OK') badge =
                    '<span class="badge bg-success"><i class="ri-check-line"></i> OK</span>';
                else if (item[f.field] === 'NOK') badge =
                    '<span class="badge bg-danger"><i class="ri-close-line"></i> NOK</span>';

                listHtml += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="small fw-medium">${f.label}</span>
                    ${badge}
                </li>
                `;
            });
            listHtml += '</ul>';
            return listHtml;
        }

        function showDetails(id) {
            $.ajax({
                url: `{{ url('utility/agenda-ahu/show-monthly') }}/${id}`,
                type: "GET",
                success: function(res) {
                    if (res.status === 200) {
                        let header = res.header;
                        $('#modalMonthlyTitle').text(
                            `Detail Laporan Bulanan - ${moment().month(header.bulan - 1).format('MMMM')} ${header.tahun}`
                        );
                        $('#hdr_operator').text(header.operator ? header.operator.username : '-');
                        $('#hdr_foreman').text(header.foreman ? header.foreman.username : '-');
                        $('#hdr_supervisor').text(header.supervisor ? header.supervisor.username : '-');

                        let html = '';
                        res.details.forEach(d => {
                            html += `
                                <div class="card mb-3 border shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 collapse-trigger" 
                                         role="button" 
                                         data-bs-toggle="collapse" 
                                         data-bs-target="#collapse-${d.id}" 
                                         aria-expanded="false" 
                                         style="cursor: pointer;">
                                        <h6 class="mb-0 fw-bold text-dark"><i class="ri-calendar-event-line me-2 text-primary"></i>Tanggal: ${d.tanggal}</h6>
                                        <i class="ri-arrow-down-s-line fs-5 transition-icon"></i>
                                    </div>
                                    <div id="collapse-${d.id}" class="collapse">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="fw-bold text-primary mb-2">Cek Kelistrikan & Pressure</div>
                                                    ${buildChecklistStatusHtml(d, FIELDS_KELISTRIKAN_PRESSURE)}
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="fw-bold text-warning-emphasis mb-2">Cek Temperature Gauge</div>
                                                    ${buildChecklistStatusHtml(d, FIELDS_TEMPERATURE)}
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="fw-bold text-danger mb-2">Cleaning & Inspeksi</div>
                                                    ${buildChecklistStatusHtml(d, FIELDS_CLEAN_INSPEKSI)}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        if (res.details.length === 0) {
                            html =
                                '<p class="text-center py-4 text-muted">Belum ada data checklist harian untuk bulan ini.</p>';
                        }

                        $('#modalMonthlyContent').html(html);
                        $('#modalMonthlyDetail').modal('show');
                    }
                }
            });
        }

        function approveSingle(id, currentStatus) {
            let actionText = JABATAN === 'foreman' ? 'Approve sebagai Foreman' : 'Approve sebagai Supervisor';
            let url = JABATAN === 'foreman' ? `{{ url('utility/agenda-ahu/approve-foreman') }}/${id}` :
                `{{ url('utility/agenda-ahu/approve-supervisor') }}/${id}`;

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: `Laporan bulanan ini akan disetujui sebagai ${JABATAN}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadApproval();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                            });
                        }
                    });
                }
            });
        }

        function openReject(id) {
            $('#reject_id').val(id);
            $('#reject_reason').val('');
            $('#modalReject').modal('show');
        }

        function submitReject() {
            let id = $('#reject_id').val();
            let reason = $('#reject_reason').val();

            if (!reason) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Alasan penolakan wajib diisi.'
                });
                return;
            }

            $.ajax({
                url: `{{ url('utility/agenda-ahu/reject') }}/${id}`,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    reason: reason
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#modalReject').modal('hide');
                    loadApproval();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                    });
                }
            });
        }

        function renderPagination(p) {
            let links = '';
            let info = `Showing page <b>${p.current_page}</b> of <b>${p.last_page}</b> (Total: <b>${p.total}</b> items)`;
            $('#paginationInfo').html(info);

            for (let i = 1; i <= p.last_page; i++) {
                links += `
                    <li class="page-item ${p.current_page === i ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="loadApproval(${i})">${i}</a>
                    </li>
                `;
            }
            $('#paginationLinks').html(links);
        }

        $(document).ready(function() {
            loadApproval();
        });
    </script>
@endsection
