@extends('layouts.app')

@section('title', 'Approval Kalibrasi')

@section('styles')
    <style>
        #btnSelectAll {
            min-width: 180px;
            transition: all 0.3s ease;
        }

        .btn-mass-action {
            transition: opacity 0.3s ease, max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s ease, margin 0.3s ease, border-color 0.3s ease;
            opacity: 0;
            max-width: 0;
            padding-left: 0 !important;
            padding-right: 0 !important;
            border-left-width: 0 !important;
            border-right-width: 0 !important;
            overflow: hidden;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-mass-action.show {
            opacity: 1;
            max-width: 180px;
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            border-left-width: 1px !important;
            border-right-width: 1px !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="row ">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Approval Kalibrasi</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#">Kalibrasi</a></li>
                                <li class="breadcrumb-item active">Approval Kalibrasi</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm rounded-3 mb-4" data-aos="fade-up">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold mb-2">List Sertifikat Kalibrasi</h4>
                        <p class="card-subtitle mb-0">Approval untuk approver terkait data sertifikat kalibrasi</p>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($approvals->isNotEmpty())
                            <button id="btnSelectAll" class="btn btn-outline-primary shadow-sm" type="button">
                                <i class="mdi mdi-checkbox-multiple-marked-outline me-1"></i> Pilih Semua
                            </button>
                            <button id="btnMassApprove" class="btn btn-success shadow-sm btn-mass-action" type="button">
                                <i class="mdi mdi-check-all me-1"></i> Approve Terpilih
                            </button>
                            <button id="btnMassReject" class="btn btn-danger shadow-sm btn-mass-action" type="button">
                                <i class="mdi mdi-close-box-multiple me-1"></i> Reject Terpilih
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" style="width: 40px;">
                                        <input type="checkbox" id="checkAll">
                                    </th>
                                    <th class="text-center" style="width: 60px;">No</th>
                                    <th>Kode Alat</th>
                                    <th>Jenis Kalibrasi</th>
                                    <th>Tanggal Kalibrasi</th>
                                    <th>Dibuat Oleh</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($approvals as $i => $approval)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="checkItem" value="{{ $approval->id }}">
                                        </td>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $approval->sertifikat->kalibrasi->alat->kode_alat ?? '-' }}
                                        <td>{{ Str::title(str_replace('_', ' ', $approval->sertifikat->kalibrasi->jenis_kalibrasi)) }}
                                        </td>
                                        <td>{{ $approval->sertifikat->kalibrasi->tgl_kalibrasi ?? '-' }}
                                        </td>
                                        <td>{{ $approval->sertifikat->kalibrasi->user->username ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-soft-warning text-uppercase">Pending</span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info btn-detail"
                                                data-main-id="{{ $approval->sertifikat->kalibrasi->id }}"
                                                data-id="{{ $approval->sertifikat->id }}">
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
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-check-circle-outline fs-3 d-block mb-2"></i>
                                            <strong>Tidak ada approval pending</strong>
                                            <div class="small">Semua data kalibrasi sudah Anda tindak lanjuti</div>
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
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Detail Kalibrasi</h5>
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
                        if (!catatan) {
                            Swal.showValidationMessage('Catatan wajib diisi');
                        }
                        return catatan;
                    }
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ url('kalibrasi/approval/reject') }}/" + id,
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
                        error: function(xhr, status, error) {
                            let errorMsg = 'Gagal approve data';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                });
            });

            $(document).on('click', '.btn-detail', function() {

                const mainId = $(this).data('main-id');

                $('#detailContent').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border"></div>
                    </div>
                `);

                $('#modalDetail').modal('show');

                $.get(`/kalibrasi/approval/detail/${mainId}`, function(res) {
                    $('#detailContent').html(res);
                }).fail(function() {
                    $('#detailContent').html(
                        '<div class="text-danger text-center">Gagal memuat detail</div>'
                    );
                });
            });

            // Approve & TTD
            let approveId = null;

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
                submitApprove();
            });

            function submitApprove() {
                $.ajax({
                    url: "{{ url('kalibrasi/approval/approve') }}/" + approveId,
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
                    error: function(xhr, status, error) {
                        let errorMsg = 'Gagal approve data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            }

            // Toggle Select All
            let allSelected = false;
            $('#btnSelectAll').on('click', function() {
                allSelected = !allSelected;
                $('.checkItem').prop('checked', allSelected);
                $('#checkAll').prop('checked', allSelected);

                if (allSelected) {
                    $(this).html(
                        '<i class="mdi mdi-checkbox-multiple-blank-outline me-1"></i> Batal Pilih Semua'
                    );
                } else {
                    $(this).html(
                        '<i class="mdi mdi-checkbox-multiple-marked-outline me-1"></i> Pilih Semua');
                }
                toggleMassActions();
            });

            // Checkbox logic
            $('#checkAll').on('change', function() {
                $('.checkItem').prop('checked', this.checked);
                allSelected = this.checked;
                if (allSelected) {
                    $('#btnSelectAll').html(
                        '<i class="mdi mdi-checkbox-multiple-blank-outline me-1"></i> Batal Pilih Semua'
                    );
                } else {
                    $('#btnSelectAll').html(
                        '<i class="mdi mdi-checkbox-multiple-marked-outline me-1"></i> Pilih Semua');
                }
                toggleMassActions();
            });

            $(document).on('change', '.checkItem', function() {
                const checkedCount = $('.checkItem:checked').length;
                const totalCount = $('.checkItem').length;

                if (checkedCount === totalCount) {
                    $('#checkAll').prop('checked', true);
                    allSelected = true;
                    $('#btnSelectAll').html(
                        '<i class="mdi mdi-checkbox-multiple-blank-outline me-1"></i> Batal Pilih Semua'
                    );
                } else {
                    $('#checkAll').prop('checked', false);
                    allSelected = false;
                    $('#btnSelectAll').html(
                        '<i class="mdi mdi-checkbox-multiple-marked-outline me-1"></i> Pilih Semua');
                }
                toggleMassActions();
            });

            function toggleMassActions() {
                const checkedCount = $('.checkItem:checked').length;
                if (checkedCount > 0) {
                    $('#btnMassApprove, #btnMassReject').addClass('show');
                } else {
                    $('#btnMassApprove, #btnMassReject').removeClass('show');
                }
            }

            $('#btnMassApprove').on('click', function() {
                const ids = $('.checkItem:checked').map(function() {
                    return $(this).val();
                }).get();

                Swal.fire({
                    title: 'Approve semua data terpilih?',
                    text: `Anda akan menyetujui ${ids.length} data kalibrasi sekaligus.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Approve',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('approval.mass-approve') }}",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            ids: ids
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Selesai!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        },
                        error: function(xhr) {
                            let errorMsg = 'Gagal memproses mass approval';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                });
            });

            $('#btnMassReject').on('click', function() {
                const ids = $('.checkItem:checked').map(function() {
                    return $(this).val();
                }).get();

                Swal.fire({
                    title: 'Reject semua data terpilih?',
                    text: `Anda akan menolak ${ids.length} data kalibrasi sekaligus.`,
                    input: 'textarea',
                    inputLabel: 'Catatan penolakan massal',
                    inputPlaceholder: 'Masukkan alasan reject terpilih...',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Reject',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    preConfirm: (catatan) => {
                        if (!catatan) {
                            Swal.showValidationMessage('Catatan wajib diisi');
                        }
                        return catatan;
                    }
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('approval.mass-reject') }}",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            ids: ids,
                            catatan: result.value
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Selesai!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        },
                        error: function(xhr) {
                            let errorMsg = 'Gagal memproses mass reject';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                });
            });
        });
    </script>
@endsection
