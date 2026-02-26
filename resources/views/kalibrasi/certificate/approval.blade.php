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
                <div class="card-header">
                    <h4 class="fw-bold">Approval Kalibrasi</h4>
                    <p class="card-subtitle">Approval untuk approver terkait data kalibrasi</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-nowrap">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
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
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $approval->sertifikat->kalibrasi->alat->kode_alat ?? '-' }}
                                        <td>{{ Str::title(str_replace('_', ' ', $approval->sertifikat->kalibrasi->jenis_kalibrasi)) }}
                                        </td>
                                        <td>{{ $approval->sertifikat->kalibrasi->tgl_kalibrasi ?? '-' }}
                                        </td>
                                        <td>{{ $approval->sertifikat->kalibrasi->user->username ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-warning">Pending</span>
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
                                        <td colspan="7" class="text-center py-4 text-muted">
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
                    <h5 class="modal-title">Tanda Tangan Approver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <canvas id="signature-pad" style="border:1px solid #ccc; width:60%;"></canvas>

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

                // const originalCanvas = document.getElementById('signature-pad');
                // const croppedCanvas = cropSignatureCanvas(originalCanvas);

                // const ttdBase64 = croppedCanvas.toDataURL('image/png');

                $('#modalTtd').modal('hide');

                // 🔥 submit approve + TTD
                submitApprove(ttdBase64);
            });

            // function cropSignatureCanvas(canvas) {
            //     const ctx = canvas.getContext('2d');
            //     const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            //     const data = imageData.data;

            //     let top = null,
            //         left = null,
            //         right = null,
            //         bottom = null;

            //     for (let y = 0; y < canvas.height; y++) {
            //         for (let x = 0; x < canvas.width; x++) {

            //             const index = (y * canvas.width + x) * 4;
            //             const alpha = data[index + 3];

            //             if (alpha > 0) { // ada tinta
            //                 if (top === null) top = y;
            //                 if (left === null || x < left) left = x;
            //                 if (right === null || x > right) right = x;
            //                 bottom = y;
            //             }
            //         }
            //     }

            //     if (top === null) return canvas; // kosong

            //     const croppedWidth = right - left;
            //     const croppedHeight = bottom - top;

            //     const croppedCanvas = document.createElement('canvas');
            //     croppedCanvas.width = croppedWidth;
            //     croppedCanvas.height = croppedHeight;

            //     const croppedCtx = croppedCanvas.getContext('2d');
            //     croppedCtx.putImageData(
            //         ctx.getImageData(left, top, croppedWidth, croppedHeight),
            //         0,
            //         0
            //     );

            //     return croppedCanvas;
            // }

            function submitApprove(ttdBase64) {
                const $btn = $('#btn-submit');
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ url('kalibrasi/approval/approve') }}/" + approveId,
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
                    error: function(xhr, status, error) {
                        let errorMsg = 'Gagal approve data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            }
        });
    </script>
@endsection
