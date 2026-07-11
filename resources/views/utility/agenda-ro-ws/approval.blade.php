@extends('layouts.app')

@section('title', 'Approval Bulanan Agenda RO-WS')

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
                                Agenda RO-WS - Approval
                            </h4>
                            <p class="text-white-50 mb-0">
                                Persetujuan laporan bulanan checklist Agenda RO-WS
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <select id="filter_mode" class="form-select form-select-sm" style="width: 150px;"
                                onchange="loadApproval()">
                                <option value="approval">Perlu Review</option>
                                <option value="history">Semua Riwayat</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button id="btnBulkApprove" class="btn btn-success btn-sm rounded-pill px-3"
                                onclick="bulkApprove()" style="display:none;">
                                <i class="ri-check-double-line me-1"></i> Approve Terpilih
                            </button>
                            <button id="btnBulkReject" class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                onclick="openBulkReject()" style="display:none;">
                                <i class="ri-close-circle-line me-1"></i> Reject Terpilih
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th width="40" class="text-center">
                                        <input type="checkbox" id="checkAll" onchange="toggleCheckAll()">
                                    </th>
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

    <!-- Modal Bulk Reject -->
    <div class="modal fade" id="modalBulkReject" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white">Tolak Laporan Terpilih</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formBulkReject">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan untuk Semua Laporan Terpilih</label>
                            <textarea name="reason" id="bulk_reject_reason" class="form-control" rows="3" required
                                placeholder="Tulis alasan penolakan untuk semua data..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="submitBulkReject()">Tolak Semua</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const API_URL = "{{ route('agenda-ro-ws.get-approval-data') }}";
        const CURRENT_USER_ID = {{ Auth::id() }};
        const JABATAN = "{{ Auth::user()->jabatan }}";
        let currentPage = 1;

        function loadApproval(page = 1) {
            currentPage = page;
            let mode = $('#filter_mode').val();

            $.ajax({
                url: API_URL,
                type: "GET",
                data: {
                    mode: mode,
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

                        let checkboxCol = canApprove ?
                            `<input type="checkbox" class="row-checkbox" value="${item.id}" onchange="toggleBulkButtons()">` :
                            `-`;

                        html += `
                        <tr>
                            <td class="text-center">${checkboxCol}</td>
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
                            '<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada pengajuan untuk disetujui</td></tr>';
                    }

                    $('#tbodyApproval').html(html);
                    renderPagination(res.pagination);
                    toggleBulkButtons();
                }
            });
        }

        function toggleCheckAll() {
            let isChecked = $('#checkAll').prop('checked');
            $('.row-checkbox').prop('checked', isChecked);
            toggleBulkButtons();
        }

        function toggleBulkButtons() {
            let selectedCount = $('.row-checkbox:checked').length;
            if (selectedCount > 0) {
                $('#btnBulkApprove').show();
                $('#btnBulkReject').show();
            } else {
                $('#btnBulkApprove').hide();
                $('#btnBulkReject').hide();
            }
        }

        const ALL_FIELDS = [
            { field: 'inspeksi_hpt_pump', label: 'Inspeksi (HLT) High Pressure Pump' },
            { field: 'inspeksi_cip_pump', label: 'Inspeksi (HLT) CIP Pump' },
            { field: 'inspeksi_blower_ro', label: 'Inspeksi (HLT) Blower RO' },
            { field: 'cek_chemical', label: 'Cek Chemical' },
            { field: 'pencatatan_flow_meter_produksi', label: 'Pencatatan Flow Meter Produksi RO Produk' },
            { field: 'cek_nilai_conductivity', label: 'Cek Nilai Conductivity' },
            { field: 'cek_dp_1st_2st', label: 'Cek ΔP 1st & 2st' },
            { field: 'cek_dp_mmf_1_2', label: 'Cek ΔP MMF #1 & MMF #2' },
            { field: 'pencatatan_flow_meter_konsumsi', label: 'Pencatatan Flow Meter Konsumsi RO Produk' },
            { field: 'backwash_mmf_1', label: 'Backwash MMF #1' },
            { field: 'backwash_mmf_2', label: 'Backwash MMF #2' },
            { field: 'cek_kondisi_rotameter_mmf_1', label: 'Cek Kondisi Rota Meter MMF 1' },
            { field: 'cek_kondisi_rotameter_mmf_2', label: 'Cek Kondisi Rota Meter MMF 2' },
            { field: 'cek_kondisi_rotameter_ro_product', label: 'Cek Kondisi Rotameter RO Product' },
            { field: 'cek_kondisi_rotameter_ro_reject', label: 'Cek Kondisi Rotameter RO Reject' },
            { field: 'kalibrasi_dosis_kimia', label: 'Kalibrasi Dosis Penggunaan Kimia' },
            { field: 'cleaning_unit_ro', label: 'Cleaning Unit RO' },
            { field: 'cleaning_unit_mmf_1', label: 'Cleaning Unit MMF 1' },
            { field: 'cleaning_unit_mmf_2', label: 'Cleaning Unit MMF 2' },
            { field: 'cek_output_hardness', label: 'Cek Output Hardness' },
            { field: 'cek_flow_produk', label: 'Cek Flow Produk' },
            { field: 'regenerasi_mesin_ws', label: 'Regenerasi Mesin Water Softener' },
            { field: 'cek_pompa_transfer', label: 'Cek Kondisi Pompa Transfer (H,L,T)' },
            { field: 'cek_pompa_suplai', label: 'Cek Kondisi Pompa Suplai (H,L,T)' },
            { field: 'cleaning_tanki_buffer_ws', label: 'Cleaning Tanki Buffer WS' }
        ];

        function showDetails(id) {
            $.get("{{ url('utility/agenda-ro-ws/show-monthly') }}/" + id, function(res) {
                let main = res.header;
                let details = res.details;
                let monthName = moment().month(main.bulan - 1).format('MMMM');

                $('#modalMonthlyTitle').text(`Detail Log Bulanan - Periode: ${monthName} ${main.tahun}`);
                $('#hdr_operator').text(main.operator ? main.operator.username : '-');
                $('#hdr_foreman').text(main.foreman ? main.foreman.username : '-');
                $('#hdr_supervisor').text(main.supervisor ? main.supervisor.username : '-');

                let contentHtml = '';
                details.forEach(item => {
                    let countOk = 0;
                    let countNok = 0;
                    ALL_FIELDS.forEach(f => {
                        if (item[f.field] === 'OK') countOk++;
                        else if (item[f.field] === 'NOK') countNok++;
                    });

                    contentHtml += `
                    <div class="card border mb-2">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold">Tanggal: ${item.tanggal}</span>
                            <span>
                                <span class="badge bg-success me-1">${countOk} OK</span>
                                <span class="badge bg-danger">${countNok} NOK</span>
                            </span>
                        </div>
                    </div>
                    `;
                });

                if (details.length === 0) {
                    contentHtml = '<div class="alert alert-warning text-center">Tidak ada data checklist harian</div>';
                }

                $('#modalMonthlyContent').html(contentHtml);
                $('#modalMonthlyDetail').modal('show');
            });
        }

        function approveSingle(id, status) {
            let url = '';
            if (JABATAN === 'foreman' && status === 'submitted') {
                url = "{{ url('utility/agenda-ro-ws/approve-foreman') }}/" + id;
            } else if (JABATAN === 'supervisor' && status === 'approved_foreman') {
                url = "{{ url('utility/agenda-ro-ws/approve-supervisor') }}/" + id;
            }

            if (!url) return;

            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                text: 'Apakah Anda yakin ingin menyetujui laporan bulanan checklist ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(url, {
                        _token: "{{ csrf_token() }}"
                    }, function(res) {
                        Swal.fire('Berhasil!', res.message, 'success');
                        loadApproval(currentPage);
                    }).fail(function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        Swal.fire('Gagal!', msg, 'error');
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
            let data = $('#formReject').serialize();

            if (!$('#reject_reason').val().trim()) {
                Swal.fire('Peringatan', 'Harap isi alasan penolakan', 'warning');
                return;
            }

            $.post("{{ url('utility/agenda-ro-ws/reject') }}/" + id, data, function(res) {
                Swal.fire('Berhasil!', res.message, 'success');
                $('#modalReject').modal('hide');
                loadApproval(currentPage);
            }).fail(function(xhr) {
                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                Swal.fire('Gagal!', msg, 'error');
            });
        }

        function bulkApprove() {
            let selectedIds = [];
            $('.row-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) return;

            Swal.fire({
                title: 'Konfirmasi Bulk Approval',
                text: `Apakah Anda yakin ingin menyetujui ${selectedIds.length} laporan bulanan secara massal?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route('agenda-ro-ws.bulk-approve') }}", {
                        _token: "{{ csrf_token() }}",
                        ids: selectedIds
                    }, function(res) {
                        Swal.fire('Berhasil!', res.message, 'success');
                        loadApproval(currentPage);
                    }).fail(function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        Swal.fire('Gagal!', msg, 'error');
                    });
                }
            });
        }

        function openBulkReject() {
            $('#bulk_reject_reason').val('');
            $('#modalBulkReject').modal('show');
        }

        function submitBulkReject() {
            let selectedIds = [];
            $('.row-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) return;

            if (!$('#bulk_reject_reason').val().trim()) {
                Swal.fire('Peringatan', 'Harap isi alasan penolakan', 'warning');
                return;
            }

            $.post("{{ route('agenda-ro-ws.bulk-reject') }}", {
                _token: "{{ csrf_token() }}",
                ids: selectedIds,
                reason: $('#bulk_reject_reason').val()
            }, function(res) {
                Swal.fire('Berhasil!', res.message, 'success');
                $('#modalBulkReject').modal('hide');
                loadApproval(currentPage);
            }).fail(function(xhr) {
                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                Swal.fire('Gagal!', msg, 'error');
            });
        }

        function renderPagination(pagination) {
            let html = '';
            if (pagination && pagination.last_page > 1) {
                html += `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="loadApproval(${pagination.current_page - 1})">Prev</a>
            </li>`;

                for (let i = 1; i <= pagination.last_page; i++) {
                    if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
                        html += `<li class="page-item ${pagination.current_page === i ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="loadApproval(${i})">${i}</a>
                    </li>`;
                    } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }

                html += `<li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="loadApproval(${pagination.current_page + 1})">Next</a>
            </li>`;
            }
            $('#paginationLinks').html(html);
            if (pagination) {
                $('#paginationInfo').html(
                    `Showing <b>${pagination.total > 0 ? (pagination.current_page - 1) * pagination.per_page + 1 : 0}</b> to <b>${Math.min(pagination.current_page * pagination.per_page, pagination.total)}</b> of <b>${pagination.total}</b> entries`
                );
            }
        }

        $(document).ready(function() {
            loadApproval();
        });
    </script>
@endsection
