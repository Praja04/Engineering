@extends('layouts.app')

@section('title', 'Approval Cooling Tower')

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
                                Cooling Tower - Monthly Approval
                            </h4>
                            <p class="text-white-50 mb-0">
                                Persetujuan laporan bulanan cooling tower
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <div id="bulkActionContainer" class="d-none">
                        <div class="d-flex align-items-center bg-light p-3 rounded-3 border">
                            <span class="me-auto fw-medium"><span id="selectedCount">0</span> item terpilih</span>
                            <button class="btn btn-success btn-sm me-2 px-3" id="btnBulkApprove">
                                <i class="ri-checkbox-circle-line me-1"></i> Approve Terpilih
                            </button>
                            <button class="btn btn-danger btn-sm px-3" id="btnBulkReject">
                                <i class="ri-close-circle-line me-1"></i> Reject Terpilih
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
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
                    <h5 class="modal-title text-white">Review Detail Laporan Bulanan</h5>
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
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th class="text-center">Pressure IN (Bar)</th>
                                    <th class="text-center">Pressure OUT (Bar)</th>
                                    <th class="text-center">Temperatur IN (°C)</th>
                                    <th class="text-center">Temperatur OUT (°C)</th>
                                    <th class="text-center">Flowrate RO Awal</th>
                                    <th class="text-center">Flowrate RO Akhir</th>
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
                url: "{{ route('cooling-tower.get-approval-data') }}",
                type: "GET",
                data: {
                    mode: 'approval'
                },
                success: function(res) {
                    $('#selectAll').prop('checked', false);
                    $('#bulkActionContainer').addClass('d-none');

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

                        let monthName = moment().month(item.bulan - 1).format('MMMM');

                        html += `
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input item-checkbox" type="checkbox" value="${item.id}" onchange="updateBulkUI()">
                                    </div>
                                </td>
                                <td>${monthName}</td>
                                <td>${item.tahun}</td>
                                <td>${item.operator?.username || '-'}</td>
                                <td>${item.foreman?.username || '-'}</td>
                                <td>${item.supervisor?.username || '-'}</td>
                                <td>${item.submitted_at ? moment(item.submitted_at).format('DD/MM/YYYY HH:mm') : '-'}</td>
                                <td>${statusBadge}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" onclick="reviewDetail(${item.id})">
                                        <i class="ri-eye-line"></i> Review
                                    </button>
                                    ${actions}
                                </td>
                            </tr>
                        `;
                    });

                    if (res.data.length == 0) {
                        html =
                            '<tr><td colspan="9" class="text-center py-4">Tidak ada antrian persetujuan</td></tr>';
                    }
                    $('#tbodyApproval').html(html);
                }
            });
        }

        function reviewDetail(id) {
            $.get("{{ url('utility/cooling-tower/show-monthly') }}/" + id, function(res) {
                let h = res.header;
                let monthName = moment().month(h.bulan - 1).format('MMMM');
                let headerHtml = `
                    <div class="row">
                        <div class="col-md-3"><strong>Bulan:</strong> ${monthName} ${h.tahun}</div>
                        <div class="col-md-3"><strong>Operator:</strong> ${h.operator?.username || '-'}</div>
                        <div class="col-md-3"><strong>Foreman:</strong> ${h.foreman?.username || '-'}</div>
                        <div class="col-md-3"><strong>Supervisor:</strong> ${h.supervisor?.username || '-'}</div>
                    </div>
                `;
                $('#reviewHeader').html(headerHtml);

                let rowsHtml = '';
                const formatNum = (v) => v ? Number(v) : '-';
                res.details.forEach(item => {
                    rowsHtml += `
                        <tr>
                            <td>${item.tanggal}</td>
                            <td>${item.jam}</td>
                            <td class="text-center">${formatNum(item.pressure_ct_in)}</td>
                            <td class="text-center">${formatNum(item.pressure_ct_out)}</td>
                            <td class="text-center">${formatNum(item.temp_ct_in)}</td>
                            <td class="text-center">${formatNum(item.temp_ct_out)}</td>
                            <td class="text-center">${formatNum(item.flowrate_ro_awal)}</td>
                            <td class="text-center">${formatNum(item.flowrate_ro_akhir)}</td>
                        </tr>
                    `;
                });
                $('#tbodyReview').html(rowsHtml);
                $('#modalReview').modal('show');
            });
        }

        function approveForeman(id) {
            Swal.fire({
                title: 'Setujui laporan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Setujui'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ url('utility/cooling-tower/approve-foreman') }}/" + id, {
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
                title: 'Setujui laporan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Setujui'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ url('utility/cooling-tower/approve-supervisor') }}/" + id, {
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
                Swal.fire('Error', 'Alasan harus diisi', 'error');
                return;
            }

            $.post("{{ url('utility/cooling-tower/reject') }}/" + id, {
                _token: "{{ csrf_token() }}",
                reason: reason
            }, function(res) {
                Swal.fire('Berhasil!', res.message, 'success');
                $('#modalReject').modal('hide');
                loadApproval();
            });
        }

        // Bulk operations
        $('#selectAll').on('change', function() {
            $('.item-checkbox').prop('checked', this.checked);
            updateBulkUI();
        });

        function updateBulkUI() {
            let checked = $('.item-checkbox:checked');
            $('#selectedCount').text(checked.length);
            if (checked.length > 0) {
                $('#bulkActionContainer').removeClass('d-none');
            } else {
                $('#bulkActionContainer').addClass('d-none');
            }
        }

        $('#btnBulkApprove').on('click', function() {
            let ids = [];
            $('.item-checkbox:checked').each(function() {
                ids.push($(this).val());
            });

            Swal.fire({
                title: 'Setujui semua data terpilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Setujui'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route('cooling-tower.bulk-approve') }}", {
                        _token: "{{ csrf_token() }}",
                        ids: ids
                    }, function(res) {
                        Swal.fire('Berhasil!', res.message, 'success');
                        loadApproval();
                    });
                }
            });
        });

        $('#btnBulkReject').on('click', function() {
            let ids = [];
            $('.item-checkbox:checked').each(function() {
                ids.push($(this).val());
            });

            Swal.fire({
                title: 'Reject data terpilih?',
                text: 'Masukkan alasan penolakan untuk semua data:',
                input: 'textarea',
                inputPlaceholder: 'Tulis alasan di sini...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Reject'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $.post("{{ route('cooling-tower.bulk-reject') }}", {
                        _token: "{{ csrf_token() }}",
                        ids: ids,
                        reason: result.value
                    }, function(res) {
                        Swal.fire('Berhasil!', res.message, 'success');
                        loadApproval();
                    });
                } else if (result.isConfirmed && !result.value) {
                    Swal.fire('Error', 'Alasan harus diisi untuk menolak!', 'error');
                }
            });
        });

        $(document).ready(function() {
            loadApproval();
        });
    </script>
@endsection
