@extends('layouts.app')

@section('title', 'Form History Card - Operasional')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Header Banner --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm"
                    style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 12px;">
                    <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-file-history-line text-warning me-2"></i>
                                Operasional - History Card
                            </h4>
                            <p class="text-white-50 mb-0">
                                Form pencatatan riwayat pelaksanaan operasional & utility (FRM/EUT/01/009/001-00)
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('history-card.data') }}" class="btn btn-light btn-sm fw-semibold shadow-sm">
                                <i class="ri-database-2-line me-1"></i> Data History Card
                            </a>
                            <button type="button" class="btn btn-outline-light btn-sm fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSelectAreaPrint">
                                <i class="ri-printer-line me-1"></i> Cetak Format Card
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Input (Centered Clean Layout) --}}
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs me-2">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                    <i class="ri-edit-2-line fs-14"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="card-title mb-0 fw-bold text-dark">Input Riwayat Pelaksanaan</h6>
                                <small class="text-muted">Teknisi otomatis dicatat sesuai akun login Anda</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form id="formHistoryCard">
                            @csrf

                            {{-- Tanggal & Jam --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-6 col-12">
                                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" id="inputTanggal" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-semibold mb-0">Jam (WIB) <span class="text-danger">*</span></label>
                                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-primary fw-semibold" id="btnNowJam" title="Gunakan waktu sekarang">
                                            <i class="ri-time-line me-1"></i>Jam Sekarang
                                        </button>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" name="jam" id="inputJam" class="form-control"
                                            value="{{ now()->timezone('Asia/Jakarta')->format('H:i') }}" placeholder="HH:MM (Custom WIB)" required>
                                        <span class="input-group-text bg-light fw-bold text-primary">WIB</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">Format 24 Jam (WIB). Bisa diketik manual (custom) atau pilih waktu.</small>
                                </div>
                            </div>

                            {{-- Select Area --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pilih Area <span class="text-danger">*</span></label>
                                <select name="area" id="selectArea" class="form-select border-primary-subtle fw-medium" required>
                                    @foreach($areas as $area)
                                        <option value="{{ $area }}" {{ $defaultArea === $area ? 'selected' : '' }}>
                                            {{ $area }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih area mesin / sistem yang dikerjakan</small>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Deskripsi Pelaksanaan / Riwayat <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" id="inputDeskripsi" rows="5" class="form-control"
                                    placeholder="Tuliskan deskripsi pekerjaan, pengecekan, pergantian sparepart, temuan, atau aktivitas operasional..." required></textarea>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <button type="button" id="btnResetForm" class="btn btn-outline-secondary">
                                    <i class="ri-refresh-line me-1"></i> Reset
                                </button>
                                <button type="submit" id="btnSubmitForm" class="btn btn-primary px-4 fw-semibold shadow-sm">
                                    <i class="ri-save-line me-1"></i> Simpan History Card
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Pilih Area Cetak --}}
<div class="modal fade" id="modalSelectAreaPrint" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h6 class="modal-title fw-bold text-white mb-0">
                    <i class="ri-printer-line me-1"></i> Pilih Area Cetak History Card
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Area yang Mau Dicetak <span class="text-danger">*</span></label>
                    <select id="modalPrintAreaSelect" class="form-select fw-medium">
                        @foreach($areas as $area)
                            <option value="{{ $area }}">{{ $area }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnConfirmPrint" class="btn btn-primary btn-sm px-3 fw-semibold">
                    <i class="ri-printer-line me-1"></i> Buka Cetak Sheet
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Inisialisasi Flatpickr 24-jam format WIB (custom input allowed)
    const jamFp = flatpickr("#inputJam", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minuteIncrement: 1,
        allowInput: true,
        defaultDate: "{{ now()->timezone('Asia/Jakarta')->format('H:i') }}"
    });

    $('#btnNowJam').on('click', function() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const timeStr = `${hours}:${minutes}`;
        $('#inputJam').val(timeStr);
        jamFp.setDate(timeStr);
    });

    // Submit Form via AJAX
    $('#formHistoryCard').on('submit', function(e) {
        e.preventDefault();

        const $btn = $('#btnSubmitForm');
        const originalBtnHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...');

        const formData = $(this).serialize();

        $.ajax({
            url: "{{ route('history-card.store') }}",
            type: "POST",
            data: formData,
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan!',
                    text: res.message || 'Data History Card berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Clear description
                $('#inputDeskripsi').val('');
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan saat menyimpan data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    html: msg
                });
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });

    // Reset Form button
    $('#btnResetForm').on('click', function() {
        $('#inputDeskripsi').val('');
        $('#inputTanggal').val("{{ date('Y-m-d') }}");
        jamFp.setDate("{{ now()->timezone('Asia/Jakarta')->format('H:i') }}");
    });

    // Handle Confirm Print
    $('#btnConfirmPrint').on('click', function() {
        const area = $('#modalPrintAreaSelect').val();
        const url = "{{ route('history-card.print') }}?area=" + encodeURIComponent(area);
        window.open(url, '_blank');
        $('#modalSelectAreaPrint').modal('hide');
    });
});
</script>
@endsection
