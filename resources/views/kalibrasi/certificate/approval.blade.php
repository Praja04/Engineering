@extends('layouts.app')

@section('title', 'Approval Kalibrasi')

@section('styles')
    <style>

    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow-sm rounded-3 mb-4" data-aos="fade-up">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-0">Approval Kalibrasi</h4>
                        <p class="card-subtitle mb-0">Approval untuk approver terkait data kalibrasi</p>
                    </div>
                    <button id="btnMassApprove" class="btn btn-success shadow-sm" style="display: none;">
                        <i class="mdi mdi-check-all"></i> Approve Terpilih
                    </button>
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

            // Checkbox logic
            $('#checkAll').on('change', function() {
                $('.checkItem').prop('checked', this.checked);
                toggleMassApproveButton();
            });

            $(document).on('change', '.checkItem', function() {
                if ($('.checkItem:checked').length === $('.checkItem').length) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
                }
                toggleMassApproveButton();
            });

            function toggleMassApproveButton() {
                const checkedCount = $('.checkItem:checked').length;
                if (checkedCount > 0) {
                    $('#btnMassApprove').fadeIn();
                } else {
                    $('#btnMassApprove').fadeOut();
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
        });
    </script>
@endsection
