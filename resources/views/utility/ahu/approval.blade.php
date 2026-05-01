@extends('layouts.app')

@section('title', 'Approval AHU')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #4b39b5 0%, #2d3561 100%); border-radius: 12px;">
            <div class="card-body">
                <h4 class="text-white fw-bold mb-1">
                    <i class="ri-checkbox-circle-line text-warning me-2"></i> AHU - Monthly Approval
                </h4>
                <p class="text-white-50 mb-0">Persetujuan laporan bulanan AHU</p>
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th>Operator</th>
                                <th>Foreman</th>
                                <th>Supervisor</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyApproval"></tbody>
                    </table>
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
                <h5 class="modal-title text-white">Review Detail AHU</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="reviewHeader" class="mb-3 p-3 bg-light rounded"></div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2">Tgl</th>
                                <th rowspan="2">Jam</th>
                                <th colspan="2" class="text-center">AHU 1</th>
                                <th colspan="2" class="text-center">AHU 2</th>
                                <th colspan="2" class="text-center">AHU 3</th>
                                <th colspan="2" class="text-center">AHU 4</th>
                            </tr>
                            <tr>
                                <th>Amp</th>
                                <th>Temp</th>
                                <th>Amp</th>
                                <th>Temp</th>
                                <th>Amp</th>
                                <th>Temp</th>
                                <th>Amp</th>
                                <th>Temp</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyReview"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reject_id">
                <div class="mb-3">
                    <label class="form-label">Alasan Penolakan</label>
                    <textarea id="reject_reason" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger w-100" onclick="submitReject()">Reject</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function loadApproval() {
        $.get("{{ route('ahu.get-approval-data') }}", {
            mode: 'approval'
        }, function(res) {
            let html = '';
            res.data.forEach(item => {
                let badge = '';
                if (item.status == 'submitted') badge = '<span class="badge bg-info">Menunggu FM</span>';
                else if (item.status == 'approved_foreman') badge = '<span class="badge bg-primary">Menunggu SPV</span>';
                else if (item.status == 'approved_supervisor') badge = '<span class="badge bg-success">Approved</span>';
                else if (item.status == 'rejected') badge = '<span class="badge bg-danger">Rejected</span>';

                let actions = '';
                if (item.status == 'submitted' && "{{ auth()->id() }}" == item.foreman_id) {
                    actions = `<button class="btn btn-sm btn-success" onclick="approveForeman(${item.id})">
                                    <i class="ri-check-line me-2"></i>Approve
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="openReject(${item.id})">
                                    <i class="ri-close-line me-2"></i>Reject
                                </button>`;
                } else if (item.status == 'approved_foreman' && "{{ auth()->id() }}" == item.supervisor_id) {
                    actions = `<button class="btn btn-sm btn-primary" onclick="approveSupervisor(${item.id})">
                                    <i class="ri-check-line me-2"></i>Approve
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="openReject(${item.id})">
                                    <i class="ri-close-line me-2"></i>Reject
                                </button>`;
                }

                html += `
                    <tr>
                        <td>${moment().month(item.bulan-1).format('MMMM')}</td>
                        <td>${item.tahun}</td>
                        <td>${item.operator?.username || '-'}</td>
                        <td>${item.foreman?.username || '-'}</td>
                        <td>${item.supervisor?.username || '-'}</td>
                        <td>${badge}</td>
                        <td class="text-center d-flex gap-1 justify-content-center">
                            <button class="btn btn-sm btn-info" onclick="reviewDetail(${item.id})">
                            <i class="ri-eye-line me-2"></i>Review</button>
                            ${actions}
                        </td>
                    </tr>
                `;
            });
            $('#tbodyApproval').html(html || '<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>');
        });
    }

    function reviewDetail(id) {
        $.get("{{ url('utility/ahu/show-monthly') }}/" + id, function(res) {
            const h = res.header;
            $('#reviewHeader').html(`<strong>Bulan:</strong> ${moment().month(h.bulan-1).format('MMMM')} ${h.tahun} | <strong>Operator:</strong> ${h.operator?.username || '-'}`);
            let html = '';
            const fN = (v) => v ? Number(v) : '-';
            res.details.forEach(d => {
                html += `
                    <tr>
                        <td>${moment(d.tanggal).format('DD/MM')}</td>
                        <td>${d.jam.substring(0,5)}</td>
                        <td>${fN(d.ampere_1)}</td><td>${fN(d.temp_out_1)}</td>
                        <td>${fN(d.ampere_2)}</td><td>${fN(d.temp_out_2)}</td>
                        <td>${fN(d.ampere_3)}</td><td>${fN(d.temp_out_3)}</td>
                        <td>${fN(d.ampere_4)}</td><td>${fN(d.temp_out_4)}</td>
                    </tr>
                `;
            });
            $('#tbodyReview').html(html);
            $('#modalReview').modal('show');
        });
    }

    function approveForeman(id) {
        Swal.fire({
            title: 'Approve Foreman?',
            text: 'Laporan akan diteruskan ke Supervisor',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Approve'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ url('utility/ahu/approve-foreman') }}/" + id, {
                    _token: "{{ csrf_token() }}"
                }, function(res) {
                    Swal.fire('Berhasil', 'Laporan disetujui Foreman', 'success');
                    loadApproval();
                });
            }
        });
    }

    function approveSupervisor(id) {
        Swal.fire({
            title: 'Approve Supervisor?',
            text: 'Laporan akan selesai disetujui',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#00ab55',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Approve'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ url('utility/ahu/approve-supervisor') }}/" + id, {
                    _token: "{{ csrf_token() }}"
                }, function(res) {
                    Swal.fire('Berhasil', 'Laporan disetujui Supervisor', 'success');
                    loadApproval();
                });
            }
        });
    }

    function openReject(id) {
        $('#reject_id').val(id);
        $('#modalReject').modal('show');
    }

    function submitReject() {
        let id = $('#reject_id').val();
        let reason = $('#reject_reason').val();
        if (!reason) return Swal.fire('Gagal', 'Alasan penolakan wajib diisi', 'error');

        $.post("{{ url('utility/ahu/reject') }}/" + id, {
            _token: "{{ csrf_token() }}",
            reason: reason
        }, function(res) {
            $('#modalReject').modal('hide');
            Swal.fire('Berhasil', 'Laporan telah ditolak', 'info');
            loadApproval();
        });
    }

    $(document).ready(() => loadApproval());
</script>
@endsection