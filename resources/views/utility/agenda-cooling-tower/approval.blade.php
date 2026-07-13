@extends('layouts.app')

@section('title', 'Approval Agenda Cooling Tower')

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
                        style="background: linear-gradient(135deg, #064e3b 0%, #10b981 100%); border-radius: 12px;">
                        <div class="card-body">
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-user-shared-line text-warning me-2"></i>
                                Approval Agenda Cooling Tower
                            </h4>
                            <p class="text-white-50 mb-0">
                                Verifikasi dan Persetujuan Bulanan Laporan Agenda Cooling Tower
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Table -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-nowrap" id="tableApproval">
                            <thead class="table-light align-middle text-center">
                                <tr>
                                    <th>Periode</th>
                                    <th>Operator Submitter</th>
                                    <th>Foreman</th>
                                    <th>Supervisor</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyApproval">
                                <!-- Loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Review Details -->
    <div class="modal fade" id="modalReview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Review Laporan Bulanan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Meta info -->
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Periode:</strong> <span id="hdr_periode">-</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Operator:</strong> <span id="hdr_operator">-</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Supervisor:</strong> <span id="hdr_supervisor">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Collapsible List -->
                    <div id="modalReviewContent" class="px-2">
                        <!-- Injected via JS -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reject Reason -->
    <div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title">Tolak Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formReject">
                        @csrf
                        <input type="hidden" id="reject_id">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan Penolakan</label>
                            <textarea id="reject_reason" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="submitReject()">Tolak Sekarang</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const JABATAN = "{{ auth()->user()->jabatan }}";
        const CURRENT_USER_ID = "{{ auth()->id() }}";

        const FIELDS = [
            'kelistrikan_pompa_10000p2',
            'kelistrikan_pompa_10000p2a',
            'kelistrikan_pompa_10000p2b',
            'kelistrikan_fan_1',
            'kelistrikan_fan_2',
            'kelistrikan_fan_3',
            'kelistrikan_fan_4',
            'suhu_out_ct',
            'suhu_in_ct',
            'pressure_out_ct',
            'pressure_in_ct',
            'ph_air_ct',
            'stok_chemical',
            'cleaning_saringan_bak',
            'cleaning_strainer_10000p2',
            'cleaning_strainer_10000p2a',
            'cleaning_strainer_10000p2b',
            'greasing_pompa_10000p2',
            'greasing_pompa_10000p2a',
            'greasing_pompa_10000p2b',
            'rubber_coupling_10000p2',
            'rubber_coupling_10000p2a',
            'rubber_coupling_10000p2b',
            'cleaning_valve_10000p2',
            'cleaning_valve_10000p2a',
            'cleaning_valve_10000p2b',
            'kalibrasi_dosis_chemical',
            'greasing_cleaning_fan_1',
            'greasing_cleaning_fan_2',
            'greasing_cleaning_fan_3',
            'greasing_cleaning_fan_4',
            'sling_fan_ct_1',
            'sling_fan_ct_2',
            'sling_fan_ct_3',
            'sling_fan_ct_4',
            'inspeksi_baut_mur',
        ];

        function loadApprovalData() {
            $.ajax({
                url: "{{ route('agenda-cooling-tower.get-approval-data') }}",
                type: "GET",
                data: {
                    mode: 'approval'
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

                        let btnAction = '';

                        if (JABATAN === 'foreman' && (item.status === 'draft' || item.status === 'rejected')) {
                            // Foreman can submit
                        } else if (JABATAN === 'supervisor' && item.status === 'approved_foreman' &&
                            item.supervisor_id == CURRENT_USER_ID) {
                            btnAction = `
                            <button class="btn btn-sm btn-success px-3 me-1" onclick="approveSupervisor(${item.id})">
                                <i class="ri-checkbox-circle-line me-1"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-danger px-3" onclick="openRejectModal(${item.id})">
                                <i class="ri-close-circle-line me-1"></i> Tolak
                            </button>
                            `;
                        }

                        let monthName = moment().month(item.bulan - 1).format('MMMM');

                        html += `
                        <tr>
                            <td class="text-center fw-medium">${monthName} ${item.tahun}</td>
                            <td class="text-center">${item.operator ? item.operator.username : '-'}</td>
                            <td class="text-center">${item.foreman ? item.foreman.username : '-'}</td>
                            <td class="text-center">${item.supervisor ? item.supervisor.username : '-'}</td>
                            <td class="text-center">${statusBadge}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info me-1" onclick="reviewDetails(${item.id})">
                                    <i class="ri-search-line me-1"></i> Review
                                </button>
                                ${btnAction}
                            </td>
                        </tr>
                        `;
                    });

                    if (res.data.length === 0) {
                        html =
                            '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada ajuan approval bulanan</td></tr>';
                    }
                    $('#tbodyApproval').html(html);
                }
            });
        }

        const FIELDS_KEL_SUHU = [
            { field: 'kelistrikan_pompa_10000p2', label: 'Kelistrikan Pompa 10000P2' },
            { field: 'kelistrikan_pompa_10000p2a', label: 'Kelistrikan Pompa 10000P2a' },
            { field: 'kelistrikan_pompa_10000p2b', label: 'Kelistrikan Pompa 10000P2b' },
            { field: 'kelistrikan_fan_1', label: 'Kelistrikan Fan 1' },
            { field: 'kelistrikan_fan_2', label: 'Kelistrikan Fan 2' },
            { field: 'kelistrikan_fan_3', label: 'Kelistrikan Fan 3' },
            { field: 'kelistrikan_fan_4', label: 'Kelistrikan Fan 4' },
            { field: 'suhu_out_ct', label: 'Suhu Out CT' },
            { field: 'suhu_in_ct', label: 'Suhu In CT' },
            { field: 'pressure_out_ct', label: 'Pressure Out CT' },
            { field: 'pressure_in_ct', label: 'Pressure In CT' },
            { field: 'ph_air_ct', label: 'pH Air CT' },
            { field: 'stok_chemical', label: 'Stok Chemical' }
        ];

        const FIELDS_CLEAN_GREASE = [
            { field: 'cleaning_saringan_bak', label: 'Cleaning Saringan Bak CT' },
            { field: 'cleaning_strainer_10000p2', label: 'Cleaning Strainer Pompa 10000P2' },
            { field: 'cleaning_strainer_10000p2a', label: 'Cleaning Strainer Pompa 10000P2a' },
            { field: 'cleaning_strainer_10000p2b', label: 'Cleaning Strainer Pompa 10000P2b' },
            { field: 'greasing_pompa_10000p2', label: 'Greasing Pompa 10000P2' },
            { field: 'greasing_pompa_10000p2a', label: 'Greasing Pompa 10000P2a' },
            { field: 'greasing_pompa_10000p2b', label: 'Greasing Pompa 10000P2b' },
            { field: 'rubber_coupling_10000p2', label: 'Rubber Coupling Pompa 10000P2' },
            { field: 'rubber_coupling_10000p2a', label: 'Rubber Coupling Pompa 10000P2a' },
            { field: 'rubber_coupling_10000p2b', label: 'Rubber Coupling Pompa 10000P2b' },
            { field: 'cleaning_valve_10000p2', label: 'Cleaning Check Valve Pompa 10000P2' },
            { field: 'cleaning_valve_10000p2a', label: 'Cleaning Check Valve Pompa 10000P2a' },
            { field: 'cleaning_valve_10000p2b', label: 'Cleaning Check Valve Pompa 10000P2b' }
        ];

        const FIELDS_FAN_INSP = [
            { field: 'kalibrasi_dosis_chemical', label: 'Kalibrasi Dosis Chemical' },
            { field: 'greasing_cleaning_fan_1', label: 'Greasing & Cleaning Fan 1' },
            { field: 'greasing_cleaning_fan_2', label: 'Greasing & Cleaning Fan 2' },
            { field: 'greasing_cleaning_fan_3', label: 'Greasing & Cleaning Fan 3' },
            { field: 'greasing_cleaning_fan_4', label: 'Greasing & Cleaning Fan 4' },
            { field: 'sling_fan_ct_1', label: 'Pengecekan Sling Fan CT 1' },
            { field: 'sling_fan_ct_2', label: 'Pengecekan Sling Fan CT 2' },
            { field: 'sling_fan_ct_3', label: 'Pengecekan Sling Fan CT 3' },
            { field: 'sling_fan_ct_4', label: 'Pengecekan Sling Fan CT 4' },
            { field: 'inspeksi_baut_mur', label: 'Inspeksi Baut & Mur (All)' }
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
                <li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2 border-bottom-0">
                    <span class="small fw-medium text-muted" style="font-size: 0.8rem; text-align: left;">${f.label}</span>
                    ${badge}
                </li>
                `;
            });
            listHtml += '</ul>';
            return listHtml;
        }

        function reviewDetails(id) {
            $.ajax({
                url: `{{ url('utility/agenda-cooling-tower/show-monthly') }}/${id}`,
                type: "GET",
                success: function(res) {
                    let main = res.header;
                    let monthName = moment().month(main.bulan - 1).format('MMMM');
                    $('#hdr_periode').text(`${monthName} ${main.tahun}`);
                    $('#hdr_operator').text(main.operator ? main.operator.username : '-');
                    $('#hdr_supervisor').text(main.supervisor ? main.supervisor.username : '-');

                    let detailsHtml = '';
                    res.details.forEach(d => {
                        let countOk = 0;
                        let countNok = 0;
                        FIELDS.forEach(f => {
                            if (d[f] === 'OK') countOk++;
                            else if (d[f] === 'NOK') countNok++;
                        });

                        detailsHtml += `
                        <div class="card border mb-2 shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 collapse-trigger" 
                                 role="button" 
                                 data-bs-toggle="collapse" 
                                 data-bs-target="#collapse-${d.id}" 
                                 aria-expanded="false"
                                 style="cursor: pointer;">
                                <span class="fw-bold text-dark"><i class="ri-calendar-event-line me-2 text-primary"></i>Tanggal: ${d.tanggal}</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success">${countOk} OK</span>
                                    <span class="badge bg-danger">${countNok} NOK</span>
                                    <i class="ri-arrow-down-s-line fs-5 transition-icon"></i>
                                </div>
                            </div>
                            <div id="collapse-${d.id}" class="collapse">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="fw-bold text-primary mb-2 small border-bottom pb-1">Kelistrikan & Parameter</div>
                                            ${buildChecklistStatusHtml(d, FIELDS_KEL_SUHU)}
                                        </div>
                                        <div class="col-md-4">
                                            <div class="fw-bold text-warning-emphasis mb-2 small border-bottom pb-1">Cleaning & Maintenance</div>
                                            ${buildChecklistStatusHtml(d, FIELDS_CLEAN_GREASE)}
                                        </div>
                                        <div class="col-md-4">
                                            <div class="fw-bold text-danger mb-2 small border-bottom pb-1">Fan & Inspeksi</div>
                                            ${buildChecklistStatusHtml(d, FIELDS_FAN_INSP)}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
                    });

                    if (res.details.length === 0) {
                        detailsHtml = '<div class="alert alert-warning text-center">Tidak ada data checklist harian</div>';
                    }

                    $('#modalReviewContent').html(detailsHtml);
                    $('#modalReview').modal('show');
                }
            });
        }

        function approveSupervisor(id) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Anda menyetujui laporan checklist bulanan ini!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, setujui!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('utility/agenda-cooling-tower/approve-supervisor') }}/${id}`,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            Swal.fire('Selesai!', res.message, 'success');
                            loadApprovalData();
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan',
                                'error');
                        }
                    });
                }
            });
        }

        function openRejectModal(id) {
            $('#reject_id').val(id);
            $('#reject_reason').val('');
            $('#modalReject').modal('show');
        }

        function submitReject() {
            let id = $('#reject_id').val();
            let reason = $('#reject_reason').val();

            if (!reason) {
                Swal.fire('Peringatan', 'Harap isi alasan penolakan', 'warning');
                return;
            }

            $.ajax({
                url: `{{ url('utility/agenda-cooling-tower/reject') }}/${id}`,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    reason: reason
                },
                success: function(res) {
                    Swal.fire('Selesai!', res.message, 'success');
                    $('#modalReject').modal('hide');
                    loadApprovalData();
                },
                error: function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            });
        }

        $(document).ready(function() {
            loadApprovalData();
        });
    </script>
@endsection
