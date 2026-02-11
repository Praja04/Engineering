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
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-nowrap">
                            <thead>
                                <tr>
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
                                        <td colspan="6" class="text-center py-4 text-muted">
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
                    <h5 class="modal-title">Tanda Tangan Approver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <canvas id="signature-pad" style="border:1px solid #ccc; width:100%; height:200px;"></canvas>

                    <div class="mt-2">
                        <button type="button" class="btn btn-outline-danger btn-sm" id="btnClearTtd">
                            Reset TTD
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnSaveTtd">
                        Simpan & Kirim
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
                        error: function() {
                            Swal.fire('Error', 'Gagal reject data', 'error');
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

                $.get(`/mtc/approval/detail/${mainId}`, function(res) {
                    $('#detailContent').html(res);
                }).fail(function() {
                    $('#detailContent').html(
                        '<div class="text-danger text-center">Gagal memuat detail</div>'
                    );
                });
            });

            // Approve & TTD
            let signaturePad = null;
            let pendingFormData = null;
            let approveId = null;

            $(document).on('click', '.btn-approve', function() {

                approveId = $(this).data('id');

                Swal.fire({
                    title: 'Approve data?',
                    text: 'Pastikan data sudah benar',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, approve',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    $('#modalTtd').modal('show');
                });
            });

            $('#modalTtd').on('shown.bs.modal', function() {

                if (!signaturePad) {
                    const canvas = document.getElementById('signature-pad');
                    canvas.width = canvas.offsetWidth;
                    canvas.height = 200;
                    signaturePad = new SignaturePad(canvas);
                }
            });

            $('#btnClearTtd').on('click', function() {
                signaturePad.clear();
            });

            $('#modalTtd').on('hidden.bs.modal', function() {
                if (signaturePad) signaturePad.clear();
            });

            $('#btnSaveTtd').on('click', function() {

                if (!signaturePad || signaturePad.isEmpty()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'TTD belum diisi',
                        text: 'Silakan tanda tangan terlebih dahulu'
                    });
                    return;
                }

                const ttdBase64 = signaturePad.toDataURL('image/png');

                $('#modalTtd').modal('hide');

                // 🔥 submit approve + TTD
                submitApprove(ttdBase64);
            });

            function submitApprove(ttdBase64) {
                const $btn = $('#btn-submit');
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ url('mtc/approval/approve') }}/" + approveId,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ttd_base64: ttdBase64
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
                    error: function() {
                        Swal.fire('Error', 'Gagal approve data', 'error');
                    }
                });
            }
        })
    </script>
@endsection
