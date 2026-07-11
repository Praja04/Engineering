@extends('layouts.app')

@section('title', 'Approval Agenda Cooling Tower')

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

                    <!-- Details Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle text-nowrap">
                            <thead class="table-light text-center small align-middle">
                                <tr>
                                    <th>Tanggal</th>
                                    @for ($i = 1; $i <= 36; $i++)
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
                                F1-F3: Kelistrikan Pompa 10000P2, P2a, P2b<br>
                                F4-F7: Kelistrikan Fan 1, 2, 3, 4<br>
                                F8-F9: Suhu Out & In Cooling Tower<br>
                                F10-F11: Pressure Out & In CT<br>
                                F12-F13: pH Air CT & Stok Chemical
                            </div>
                            <div class="col-md-4">
                                F14: Cleaning Saringan Bak CT<br>
                                F15-F17: Cleaning Strainer Pompa 10000P2, P2a, P2b<br>
                                F18-F20: Greasing Pompa 10000P2, P2a, P2b<br>
                                F21-F23: Pengecekan Rubber Coupling Pompa 10000P2, P2a, P2b<br>
                                F24-F26: Cleaning Check Valve Pompa 10000P2, P2a, P2b
                            </div>
                            <div class="col-md-4">
                                F27: Kalibrasi Dosis Chemical<br>
                                F28-F31: Greasing & Cleaning Fan 1, 2, 3, 4<br>
                                F32-F35: Pengecekan Sling Fan CT 1, 2, 3, 4<br>
                                F36: Inspeksi Baut & Mur (All)
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
                        let rowCells = '';
                        FIELDS.forEach(f => {
                            let cellVal = d[f] || '';
                            let badge = '-';
                            if (cellVal === 'OK') badge = '<span class="text-success fw-bold">✓</span>';
                            else if (cellVal === 'NOK') badge = '<span class="text-danger fw-bold">✗</span>';

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
