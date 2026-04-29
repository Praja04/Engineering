@extends('layouts.app')

@section('title', 'Approval Compressor')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #4b39b5 0%, #2d3561 100%); border-radius: 12px;">
                        <div class="card-body">
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-checkbox-circle-line text-warning me-2"></i>
                                Compressor - Weekly Approval
                            </h4>
                            <p class="text-white-50 mb-0">
                                Persetujuan laporan mingguan compressor
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Minggu Ke</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Operator</th>
                                    <th>Foreman</th>
                                    <th>Supervisor</th>
                                    <th>Submitted At</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyApproval">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
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
                    <h5 class="modal-title text-white">Review Detail Laporan Mingguan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reviewHeader" class="mb-3 p-3 bg-light rounded">
                        <!-- Header Info -->
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover align-middle text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">Tgl</th>
                                    <th rowspan="2">Jam</th>
                                    <th colspan="4" class="text-center">Pressure Outlet (Bar)</th>
                                    <th colspan="3" class="text-center">Element Outlet (°C)</th>
                                    <th rowspan="2">Load (%)</th>
                                    <th colspan="4" class="text-center">Running Hour</th>
                                    <th colspan="4" class="text-center">Loaded Hour</th>
                                    <th colspan="4" class="text-center">Motor Start</th>
                                </tr>
                                <tr>
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>4</th>
                                    <th>1</th>
                                    <th>2</th>
                                    <th>4</th>
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>4</th>
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>4</th>
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>4</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyReview">
                                <!-- Data Details -->
                            </tbody>
                        </table>
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
                <div class="modal-header">
                    <h5 class="modal-title">Reject Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="reject_id">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan</label>
                        <textarea id="reject_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="submitReject()">Reject</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function loadApproval() {
            $.ajax({
                url: "{{ route('compressor.get-approval-data') }}",
                type: "GET",
                data: {
                    mode: 'approval'
                },
                success: function(res) {
                    let html = '';
                    res.data.forEach(item => {
                        let statusBadge = '';
                        if (item.status == 'submitted') statusBadge =
                            '<span class="badge bg-info">Menunggu Foreman</span>';
                        else if (item.status == 'approved_foreman') statusBadge =
                            '<span class="badge bg-primary">Menunggu Supervisor</span>';
                        else if (item.status == 'approved_supervisor') statusBadge =
                            '<span class="badge bg-success">Approved</span>';
                        else if (item.status == 'rejected') statusBadge =
                            `<span class="badge bg-danger" title="${item.reject_reason || ''}">Rejected</span>`;

                        let actions = '';
                        if (item.status == 'submitted' && "{{ auth()->id() }}" == item.foreman_id) {
                            actions = `
                                <button class="btn btn-sm btn-success" onclick="approveForeman(${item.id})">
                                    <i class="ri-check-line"></i> Approve
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="openReject(${item.id})">
                                    <i class="ri-close-line"></i> Reject
                                </button>
                            `;
                        } else if (item.status == 'approved_foreman' && "{{ auth()->id() }}" == item
                            .supervisor_id) {
                            actions = `
                                <button class="btn btn-sm btn-primary" onclick="approveSupervisor(${item.id})">
                                    <i class="ri-check-line"></i> Approve
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="openReject(${item.id})">
                                    <i class="ri-close-line"></i> Reject
                                </button>
                            `;
                        }

                        html += `
                            <tr>
                                <td class="text-center">${item.week}</td>
                                <td>${moment().month(item.bulan - 1).format('MMMM')}</td>
                                <td>${item.tahun}</td>
                                <td>${item.operator?.username || '-'}</td>
                                <td>${item.foreman?.username || '-'}</td>
                                <td>${item.supervisor?.username || '-'}</td>
                                <td>${item.submitted_at ? moment(item.submitted_at).format('DD-MM-YYYY HH:mm') : '-'}</td>
                                <td>${statusBadge}</td>
                                <td class="text-center">
                                    <div>
                                        <button class="btn btn-sm btn-info" onclick="reviewDetail(${item.id})" title="Review Data">
                                            <i class="ri-eye-line"></i> Review
                                        </button>
                                        ${actions}
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    if (res.data.length == 0) {
                        html =
                            '<tr><td colspan="9" class="text-center">Tidak ada data menunggu approval</td></tr>';
                    }

                    $('#tbodyApproval').html(html);
                }
            });
        }

        function approveForeman(id) {
            Swal.fire({
                title: 'Konfirmasi Approval Foreman?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Approve'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ url('utility/compressor/approve-foreman') }}/" + id, {
                        _token: "{{ csrf_token() }}"
                    }, function(res) {
                        Swal.fire('Berhasil!', res.message, 'success');
                        loadApproval();
                    });
                }
            });
        }

        function approveSupervisor(id) {
            Swal.fire({
                title: 'Konfirmasi Approval Supervisor?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Approve'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ url('utility/compressor/approve-supervisor') }}/" + id, {
                        _token: "{{ csrf_token() }}"
                    }, function(res) {
                        Swal.fire('Berhasil!', res.message, 'success');
                        loadApproval();
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
                Swal.fire('Peringatan', 'Alasan harus diisi', 'warning');
                return;
            }

            $.post("{{ url('utility/compressor/reject') }}/" + id, {
                _token: "{{ csrf_token() }}",
                reason: reason
            }, function(res) {
                Swal.fire('Berhasil!', res.message, 'info');
                $('#modalReject').modal('hide');
                loadApproval();
            });
        }

        function reviewDetail(id) {
            $.get("{{ url('utility/compressor/show-weekly') }}/" + id, function(res) {
                const h = res.header;
                const formatNum = (v) => v ? Number(v) : '-';

                $('#reviewHeader').html(`
                <div class="row">
                    <div class="col-md-4"><strong>Minggu:</strong> ${h.week}</div>
                    <div class="col-md-4"><strong>Periode:</strong> ${moment(h.tgl_awal).format('DD MMM')} - ${moment(h.tgl_akhir).format('DD MMM YYYY')}</div>
                    <div class="col-md-4"><strong>Operator:</strong> ${h.operator?.username || '-'}</div>
                </div>
            `);

                let html = '';
                res.details.forEach(d => {
                    html += `
                    <tr>
                        <td>${moment(d.tanggal).format('DD/MM')}</td>
                        <td>${d.jam.substring(0, 5)}</td>
                        <td>${formatNum(d.pressure_outlet_1)}</td>
                        <td>${formatNum(d.pressure_outlet_2)}</td>
                        <td>${formatNum(d.pressure_outlet_3)}</td>
                        <td>${formatNum(d.pressure_outlet_4)}</td>
                        <td>${formatNum(d.element_outlet_1)}</td>
                        <td>${formatNum(d.element_outlet_2)}</td>
                        <td>${formatNum(d.element_outlet_4)}</td>
                        <td>${formatNum(d.load_percent)}</td>
                        <td>${formatNum(d.running_hour_1)}</td>
                        <td>${formatNum(d.running_hour_2)}</td>
                        <td>${formatNum(d.running_hour_3)}</td>
                        <td>${formatNum(d.running_hour_4)}</td>
                        <td>${formatNum(d.loaded_hour_1)}</td>
                        <td>${formatNum(d.loaded_hour_2)}</td>
                        <td>${formatNum(d.loaded_hour_3)}</td>
                        <td>${formatNum(d.loaded_hour_4)}</td>
                        <td>${formatNum(d.motor_start_1)}</td>
                        <td>${formatNum(d.motor_start_2)}</td>
                        <td>${formatNum(d.motor_start_3)}</td>
                        <td>${formatNum(d.motor_start_4)}</td>
                    </tr>
                `;
                });
                $('#tbodyReview').html(html);
                $('#modalReview').modal('show');
            });
        }

        $(document).ready(function() {
            loadApproval();
        });
    </script>
@endsection
