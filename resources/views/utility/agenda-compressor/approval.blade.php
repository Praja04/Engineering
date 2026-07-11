@extends('layouts.app')

@section('title', 'Approval Agenda Compressor')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-radius: 12px;">
                        <div class="card-body">
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-user-shared-line text-warning me-2"></i>
                                Approval Agenda Compressor
                            </h4>
                            <p class="text-white-50 mb-0">
                                Verifikasi dan Persetujuan Laporan Bulanan Agenda Compressor
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

                    <!-- Details Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle text-nowrap">
                            <thead class="table-light text-center small align-middle">
                                <tr>
                                    <th>Tanggal</th>
                                    @for ($i = 1; $i <= 25; $i++)
                                        <th>F{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody id="tbodyReviewDetails" class="small">
                                <!-- Rows injected via JS -->
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3 py-2 small">
                        <strong>Keterangan Kolom:</strong><br>
                        <div class="row">
                            <div class="col-md-4">
                                F1-F5: AQ55VSD (Press, RH, Elem Out, Kelistrikan, RPM)<br>
                                F6-F9: GA37 (Press, RH, Kelistrikan, Elem Out)<br>
                                F10-F13: IR55 (Press, RH, Kelistrikan, Temp)
                            </div>
                            <div class="col-md-4">
                                F14: Cleaning Strainer AQ55VSD<br>
                                F15: Cleaning Blowoff GA37<br>
                                F16: Replace Filter IR55<br>
                                F17-F19: Motor Inspeksi AQ55VSD, GA37, IR55 (HLT)
                            </div>
                            <div class="col-md-4">
                                F20-F22: Dryer Inspeksi 120, TR15, IR (HLT)<br>
                                F23: Press In/Out Cooling Tower<br>
                                F24: Press Receiver Tank<br>
                                F25: Press Dryer In/Out
                            </div>
                        </div>
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
            'pressure_aq55vsd',
            'running_hour_aq55vsd',
            'element_outlet_aq55vsd',
            'kelistrikan_aq55vsd',
            'rpm_aq55vsd',
            'pressure_ga37',
            'running_hour_ga37',
            'kelistrikan_ga37',
            'element_outlet_ga37',
            'pressure_ir55',
            'running_hour_ir55',
            'kelistrikan_ir55',
            'temperature_ir55',
            'cleaning_strainer_aq55vsd',
            'cleaning_valve_ga37',
            'replace_filter_ir55',
            'inspeksi_motor_aq55vsd',
            'inspeksi_motor_ga37',
            'inspeksi_motor_ir55',
            'inspeksi_dryer_120',
            'inspeksi_dryer_tr15',
            'inspeksi_dryer_ir',
            'pressure_in_out_ct',
            'pressure_bejana_receiver',
            'pressure_in_out_dryer',
        ];

        function loadApprovalData() {
            $.ajax({
                url: "{{ route('agenda-compressor.get-approval-data') }}",
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

                        if (JABATAN === 'foreman' && (item.status === 'draft' || item.status ===
                                'rejected')) {
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

        function reviewDetails(id) {
            $.ajax({
                url: `{{ url('utility/agenda-compressor/show-monthly') }}/${id}`,
                type: "GET",
                success: function(res) {
                    let main = res.header;
                    let monthName = moment().month(main.bulan - 1).format('MMMM');
                    $('#hdr_periode').text(`${monthName} ${main.tahun}`);
                    $('#hdr_operator').text(main.operator ? main.operator.username : '-');
                    $('#hdr_supervisor').text(main.supervisor ? main.supervisor.username : '-');

                    let detailsHtml = '';
                    res.details.forEach(d => {
                        let rowCells = '';
                        FIELDS.forEach(f => {
                            let cellVal = d[f] || '';
                            let badge = '-';
                            if (cellVal === 'OK') badge =
                                '<span class="text-success fw-bold">✓</span>';
                            else if (cellVal === 'NOK') badge =
                                '<span class="text-danger fw-bold">✗</span>';

                            rowCells += `<td class="text-center">${badge}</td>`;
                        });

                        detailsHtml += `
                        <tr>
                            <td class="text-center fw-medium bg-light">${d.tanggal}</td>
                            ${rowCells}
                        </tr>
                        `;
                    });

                    $('#tbodyReviewDetails').html(detailsHtml);
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
                        url: `{{ url('utility/agenda-compressor/approve-supervisor') }}/${id}`,
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
                url: `{{ url('utility/agenda-compressor/reject') }}/${id}`,
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
