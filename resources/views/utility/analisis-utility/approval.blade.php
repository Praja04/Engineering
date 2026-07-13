@extends('layouts.app')

@section('title', 'Approval Analisis Utility')

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
                        style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); border-radius: 12px;">
                        <div class="card-body">
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-user-shared-line text-warning me-2"></i>
                                Approval Analisis Utility
                            </h4>
                            <p class="text-white-50 mb-0">
                                Verifikasi dan Persetujuan Laporan Bulanan Analisis Utility
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
            'ph_fw_storage', 'ph_ws_storage', 'ph_ro_storage', 'ph_in_mmf',
            'ph_buffer_tank_ws', 'ph_outlet_ws', 'ph_menara_ws', 'ph_depo_lt1',
            'ph_depo_lt2', 'ph_cooling_tower', 'ph_boiler', 'ph_outlet_ws_2',
            'tds_fw_storage', 'tds_ws_storage', 'tds_ro_storage', 'tds_in_mmf',
            'tds_out_ro', 'tds_menara_ws', 'tds_daily_tank_dissolver', 'tds_depo_lt1',
            'tds_depo_lt2', 'tds_cooling_tower', 'tds_boiler',
            'turbidity_in_mmf', 'turbidity_out_mmf', 'turbidity_cooling_tower',
            'chlorine_mmf', 'chlorine_menara', 'chlorine_depo_lt1', 'chlorine_depo_lt2',
            'chlorine_daily_tank_dissolver',
            'hardness_inlet_ws', 'hardness_outlet_ws', 'hardness_ws_storage',
            'hardness_ct', 'hardness_ro', 'hardness_boiler',
        ];

        function loadApprovalData() {
            $.ajax({
                url: "{{ route('analisis-utility.get-approval-data') }}",
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

        const FIELDS_PH = [
            { field: 'ph_fw_storage', label: 'pH FW Storage' },
            { field: 'ph_ws_storage', label: 'pH WS Storage' },
            { field: 'ph_ro_storage', label: 'pH RO Storage' },
            { field: 'ph_in_mmf', label: 'pH In MMF' },
            { field: 'ph_buffer_tank_ws', label: 'pH Buffer Tank WS' },
            { field: 'ph_outlet_ws', label: 'pH Outlet WS' },
            { field: 'ph_menara_ws', label: 'pH Menara WS' },
            { field: 'ph_depo_lt1', label: 'pH Depo Lt.1' },
            { field: 'ph_depo_lt2', label: 'pH Depo Lt.2' },
            { field: 'ph_cooling_tower', label: 'pH Cooling Tower' },
            { field: 'ph_boiler', label: 'pH Boiler' },
            { field: 'ph_outlet_ws_2', label: 'pH Outlet WS 2' }
        ];

        const FIELDS_TDS = [
            { field: 'tds_fw_storage', label: 'TDS FW Storage' },
            { field: 'tds_ws_storage', label: 'TDS WS Storage' },
            { field: 'tds_ro_storage', label: 'TDS RO Storage' },
            { field: 'tds_in_mmf', label: 'TDS In MMF' },
            { field: 'tds_out_ro', label: 'TDS Out RO' },
            { field: 'tds_menara_ws', label: 'TDS Menara WS' },
            { field: 'tds_daily_tank_dissolver', label: 'TDS Daily Tank Dissolver' },
            { field: 'tds_depo_lt1', label: 'TDS Depo Lt.1' },
            { field: 'tds_depo_lt2', label: 'TDS Depo Lt.2' },
            { field: 'tds_cooling_tower', label: 'TDS Cooling Tower' },
            { field: 'tds_boiler', label: 'TDS Boiler' }
        ];

        const FIELDS_OTHER = [
            { field: 'turbidity_in_mmf', label: 'Turbidity In MMF', unit: 'NTU' },
            { field: 'turbidity_out_mmf', label: 'Turbidity Out MMF', unit: 'NTU' },
            { field: 'turbidity_cooling_tower', label: 'Turbidity Cooling Tower', unit: 'NTU' },
            { field: 'chlorine_mmf', label: 'Chlorine MMF', unit: 'ppm' },
            { field: 'chlorine_menara', label: 'Chlorine Menara', unit: 'ppm' },
            { field: 'chlorine_depo_lt1', label: 'Chlorine Depo Lt.1', unit: 'ppm' },
            { field: 'chlorine_depo_lt2', label: 'Chlorine Depo Lt.2', unit: 'ppm' },
            { field: 'chlorine_daily_tank_dissolver', label: 'Chlorine Daily Tank Dissolver', unit: 'ppm' },
            { field: 'hardness_inlet_ws', label: 'Hardness Inlet WS', unit: 'ppm' },
            { field: 'hardness_outlet_ws', label: 'Hardness Outlet WS', unit: 'ppm' },
            { field: 'hardness_ws_storage', label: 'Hardness WS Storage', unit: 'ppm' },
            { field: 'hardness_ct', label: 'Hardness CT', unit: 'ppm' },
            { field: 'hardness_ro', label: 'Hardness RO', unit: 'ppm' },
            { field: 'hardness_boiler', label: 'Hardness Boiler', unit: 'ppm' }
        ];

        function buildAnalisisStatusHtml(item, fieldList, defaultUnit) {
            let listHtml = '<ul class="list-group list-group-flush">';
            fieldList.forEach(f => {
                let val = item[f.field];
                let badge = '<span class="badge bg-secondary">-</span>';
                let unit = f.unit || defaultUnit || '';
                if (val !== undefined && val !== null && val !== '') {
                    badge = `<span class="badge bg-primary px-2 py-1">${Number(val)}${unit ? ' ' + unit : ''}</span>`;
                }

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
                url: `{{ url('utility/analisis-utility/show-monthly') }}/${id}`,
                type: "GET",
                success: function(res) {
                    let main = res.header;
                    let monthName = moment().month(main.bulan - 1).format('MMMM');
                    $('#hdr_periode').text(`${monthName} ${main.tahun}`);
                    $('#hdr_operator').text(main.operator ? main.operator.username : '-');
                    $('#hdr_supervisor').text(main.supervisor ? main.supervisor.username : '-');

                    let detailsHtml = '';
                    res.details.forEach(d => {
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
                                    <span class="badge bg-soft-info text-info">Analisis Parameter</span>
                                    <i class="ri-arrow-down-s-line fs-5 transition-icon"></i>
                                </div>
                            </div>
                            <div id="collapse-${d.id}" class="collapse">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="fw-bold text-primary mb-2 small border-bottom pb-1">pH Parameter</div>
                                            ${buildAnalisisStatusHtml(d, FIELDS_PH, '')}
                                        </div>
                                        <div class="col-md-4">
                                            <div class="fw-bold text-warning-emphasis mb-2 small border-bottom pb-1">TDS Parameter</div>
                                            ${buildAnalisisStatusHtml(d, FIELDS_TDS, 'ppm')}
                                        </div>
                                        <div class="col-md-4">
                                            <div class="fw-bold text-danger mb-2 small border-bottom pb-1">Turbidity, Chlorine & Hardness</div>
                                            ${buildAnalisisStatusHtml(d, FIELDS_OTHER, '')}
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
                        url: `{{ url('utility/analisis-utility/approve-supervisor') }}/${id}`,
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
                url: `{{ url('utility/analisis-utility/reject') }}/${id}`,
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
