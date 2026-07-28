@extends('layouts.app')

@section('title', ' Approval Mtc')

@section('styles')
    <style>
        .card-soft {
            border: 1px solid #eee;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="card card-soft shadow-sm">
                <div class="card-header">
                    <h4 class="fw-bold">Approval Maintenance</h4>
                    <p class="card-subtitle">Approval untuk approver terkait data checklist maintenance</p>
                </div>

                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3"
                                id="btnSelectAll">
                                <i class="mdi mdi-checkbox-multiple-marked-outline me-1"></i> Pilih Semua
                            </button>
                        </div>
                        <div class="d-flex gap-2 d-none" id="massActionArea">
                            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3" id="btnMassReject">
                                <i class="mdi mdi-close-circle-outline me-1"></i> Reject Terpilih (<span
                                    id="checkedCount">0</span>)
                            </button>
                            <button type="button" class="btn btn-success btn-sm rounded-pill px-3" id="btnMassApprove">
                                <i class="mdi mdi-check-decagram me-1"></i> Approve Terpilih (<span
                                    id="checkedCountApprove">0</span>)
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-nowrap">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                    <th>No</th>
                                    <th>Jenis Maintenance</th>
                                    <th>Tanggal & Waktu</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($approvals as $i => $approval)
                                    <tr>
                                        <td><input type="checkbox" class="form-check-input row-checkbox"
                                                value="{{ $approval->id }}"></td>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ Str::title(str_replace('_', ' ', $approval->main->jenis_mtc)) }}</td>
                                        <td>
                                            <div>{{ $approval->main->tanggal ?? '-' }}</div>
                                            <small class="text-muted">
                                                {{ $approval->main->waktu ?? '' }}
                                            </small>
                                        </td>
                                        <td>{{ $approval->main->createdBy->username ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-warning">Pending</span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info btn-detail"
                                                data-main-id="{{ $approval->mtc_main_id }}">
                                                <i class="mdi mdi-eye"></i> Reveiew
                                            </button>

                                            <button class="btn btn-sm btn-success btn-approve"
                                                data-id="{{ $approval->id }}">
                                                <i class="mdi mdi-check"></i> Approve
                                            </button>

                                            <button class="btn btn-sm btn-danger btn-reject" data-id="{{ $approval->id }}">
                                                <i class="mdi mdi-close"></i> Reject
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-check-circle-outline fs-3 d-block mb-2"></i>
                                            <strong>Tidak ada approval pending</strong>
                                            <div class="small">Semua maintenance sudah Anda tindak lanjuti</div>
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Detail Maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="detailContent">
                    <div class="text-center py-5">
                        <div class="spinner-border"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTtd" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Tanda Tangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    @if ($ttdPath)
                        <p class="text-muted mb-3">Tanda tangan Anda akan digunakan untuk approval ini:</p>
                        <img src="{{ asset('storage/' . $ttdPath) }}" alt="Tanda Tangan" class="img-fluid border rounded"
                            style="max-height: 150px;">
                    @else
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert"></i>
                            Tanda tangan Anda belum tersedia. Hubungi administrator.
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="btnSaveTtd" {{ !$ttdPath ? 'disabled' : '' }}>
                        <i class="mdi mdi-check"></i> Konfirmasi & Approve
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            // ── Reject ──────────────────────────────────────────────
            $(document).on('click', '.btn-reject', function() {

                const id = $(this).data('id');

                Swal.fire({
                    title: 'Reject data',
                    input: 'textarea',
                    inputLabel: 'Catatan penolakan',
                    inputPlaceholder: 'Masukkan alasan reject...',
                    inputAttributes: {
                        'aria-label': 'Catatan reject'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Reject',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc3545',
                    preConfirm: (catatan) => {
                        if (!catatan) Swal.showValidationMessage('Catatan wajib diisi');
                        return catatan;
                    }
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ url('mtc/approval/reject') }}/" + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            catatan: result.value
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Rejected!',
                                text: res.message,
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        },
                        error: function(xhr) {
                            let message = 'Gagal reject data';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', message, 'error');
                        }
                    });
                });
            });

            // ── Detail ──────────────────────────────────────────────
            $(document).on('click', '.btn-detail', function() {

                const mainId = $(this).data('main-id');

                $('#detailContent').html(`
                <div class="text-center py-5">
                    <div class="spinner-border"></div>
                </div>
            `);
                $('#modalDetail').modal('show');

                $.get(`/mtc/approval/detail/${mainId}`, function(res) {
                    $('#detailContent').html(res);
                }).fail(function() {
                    $('#detailContent').html(
                        '<div class="text-danger text-center">Gagal memuat detail</div>'
                    );
                });
            });

            // ── Approve ──────────────────────────────────────────────
            let approveId = null; // ← deklarasi cukup 1x

            $(document).on('click', '.btn-approve', function() {
                approveId = $(this).data('id');

                Swal.fire({
                    title: 'Approve data?',
                    text: 'Pastikan data sudah benar sebelum melanjutkan.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, lanjutkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (!result.isConfirmed) return;
                    $('#modalTtd').modal('show');
                });
            });

            $('#btnSaveTtd').on('click', function() {
                $('#modalTtd').modal('hide');

                $.ajax({
                    url: "{{ url('mtc/approval/approve') }}/" + approveId,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Approved!',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        let message = 'Gagal approve data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', message, 'error');
                    }
                });
            });

            // ── Checkbox Actions ──
            $(document).on('change', '#checkAll', function() {
                const checked = $(this).is(':checked');
                $('.row-checkbox').prop('checked', checked);
                toggleMassActionArea();
            });

            $(document).on('change', '.row-checkbox', function() {
                const totalChecked = $('.row-checkbox:checked').length;
                const totalCheckboxes = $('.row-checkbox').length;
                $('#checkAll').prop('checked', totalChecked === totalCheckboxes && totalCheckboxes > 0);
                toggleMassActionArea();
            });

            // ── Button Pilih Semua Action ──
            $(document).on('click', '#btnSelectAll', function() {
                const totalCheckboxes = $('.row-checkbox').length;
                if (totalCheckboxes === 0) {
                    Swal.fire('Info', 'Tidak ada data approval pending.', 'info');
                    return;
                }

                const totalChecked = $('.row-checkbox:checked').length;
                const checkAllState = (totalChecked < totalCheckboxes);

                $('.row-checkbox').prop('checked', checkAllState);
                $('#checkAll').prop('checked', checkAllState);
                toggleMassActionArea();
            });

            function toggleMassActionArea() {
                const checkedIds = getCheckedIds();
                const totalChecked = checkedIds.length;
                const totalCheckboxes = $('.row-checkbox').length;

                if (totalChecked > 0) {
                    $('#checkedCount').text(totalChecked);
                    $('#checkedCountApprove').text(totalChecked);
                    $('#massActionArea').removeClass('d-none');
                } else {
                    $('#massActionArea').addClass('d-none');
                }

                // Update tombol Pilih Semua text/style
                if (totalChecked === totalCheckboxes && totalCheckboxes > 0) {
                    $('#btnSelectAll').html(
                            '<i class="mdi mdi-checkbox-multiple-blank-outline me-1"></i> Batal Pilih Semua')
                        .removeClass('btn-outline-primary')
                        .addClass('btn-outline-danger');
                } else {
                    $('#btnSelectAll').html(
                            '<i class="mdi mdi-checkbox-multiple-marked-outline me-1"></i> Pilih Semua')
                        .removeClass('btn-outline-danger')
                        .addClass('btn-outline-primary');
                }
            }

            function getCheckedIds() {
                const ids = [];
                $('.row-checkbox:checked').each(function() {
                    ids.push($(this).val());
                });
                return ids;
            }

            // ── Mass Approve Action ──
            $('#btnMassApprove').click(function() {
                const ids = getCheckedIds();
                if (ids.length === 0) return;

                Swal.fire({
                    title: 'Approve Massal?',
                    text: `Anda akan menyetujui ${ids.length} data maintenance secara bersamaan.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Approve Semua',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ route('mtc.approval.mass-approve') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: ids
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        },
                        error: function(xhr) {
                            let message = 'Gagal approve massal';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', message, 'error');
                        }
                    });
                });
            });

            // ── Mass Reject Action ──
            $('#btnMassReject').click(function() {
                const ids = getCheckedIds();
                if (ids.length === 0) return;

                Swal.fire({
                    title: 'Reject Massal?',
                    input: 'textarea',
                    inputLabel: 'Catatan penolakan massal',
                    inputPlaceholder: 'Masukkan alasan reject untuk seluruh data terpilih...',
                    inputAttributes: {
                        'aria-label': 'Catatan reject massal'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Reject Semua',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc3545',
                    preConfirm: (catatan) => {
                        if (!catatan) Swal.showValidationMessage('Catatan wajib diisi');
                        return catatan;
                    }
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ route('mtc.approval.mass-reject') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: ids,
                            catatan: result.value
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Rejected!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        },
                        error: function(xhr) {
                            let message = 'Gagal reject massal';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', message, 'error');
                        }
                    });
                });
            });

        });
    </script>
@endsection
