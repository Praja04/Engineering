@extends('layouts.app')

@section('title', 'Corrective Maintenance Form')

@section('styles')
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
        padding: 1.25rem;
        border-radius: 12px 12px 0 0;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Page Header (Reference Style Banner) --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-0"
                    style="background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%); border-radius: 12px;">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-tools-line text-info me-2"></i>
                                EPR — Corrective Maintenance Form
                            </h4>
                            <p class="text-white-50 mb-0">
                                Masukkan data perbaikan mesin dan breakdown downtime
                            </p>
                        </div>
                        <a href="{{ route('epr.cm.data') }}" class="btn btn-outline-light rounded-pill btn-sm px-3">
                            <i class="ri-database-2-line me-1"></i> Lihat Riwayat Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-body p-4 bg-white">
                        <div class="alert alert-warning d-none mb-4" id="editBanner">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <strong><i class="ri-edit-line me-1"></i> Mode Edit Laporan</strong>
                                    <div class="small" id="editBannerSub"></div>
                                </div>
                                <button class="btn btn-sm btn-outline-warning" onclick="cancelEdit()">Batal</button>
                            </div>
                        </div>

                        <form id="formReport">
                            @csrf
                            <input type="hidden" id="f-id" name="id">

                            {{-- Section 1: Informasi Dasar --}}
                            <div class="mb-3">
                                <h6 class="fw-bold text-primary mb-2">
                                    <i class="ri-file-list-3-line me-1"></i> Informasi Dasar
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row mb-4 g-3">
                                {{-- Tanggal --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="f-tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                                </div>

                                {{-- Shift --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Shift <span class="text-danger">*</span></label>
                                    <select class="form-select" id="f-shift" name="shift" required>
                                        <option value="">Pilih...</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>

                                {{-- Grup --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Grup <span class="text-danger">*</span></label>
                                    <select class="form-select" id="f-grup" name="grup" required>
                                        <option value="">Pilih...</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                </div>

                                {{-- Mesin --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Mesin <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="f-mesin" name="mesin" placeholder="Ketik kode mesin (cth: E, J, AI...)" list="machine-list" required>
                                    <datalist id="machine-list">
                                        <option value="A"><option value="B"><option value="C"><option value="D">
                                        <option value="E"><option value="F"><option value="G"><option value="H">
                                        <option value="I"><option value="J"><option value="K"><option value="L">
                                        <option value="M"><option value="N"><option value="O"><option value="P">
                                        <option value="Q"><option value="R"><option value="S"><option value="T">
                                        <option value="U"><option value="V"><option value="W"><option value="X">
                                        <option value="Y"><option value="Z"><option value="AH"><option value="AI">
                                        <option value="AJ"><option value="AK"><option value="AL"><option value="AM">
                                    </datalist>
                                </div>

                                {{-- Pouch/Sachet --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Pouch / Sachet <span class="text-danger">*</span></label>
                                    <select class="form-select" id="f-pouchSachet" name="pouch_sachet" required>
                                        <option value="">Pilih...</option>
                                        <option value="Pouch">Pouch</option>
                                        <option value="Sachet">Sachet</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Section 2: Data Downtime & Perbaikan --}}
                            <div class="mb-3">
                                <h6 class="fw-bold text-success mb-2">
                                    <i class="ri-dashboard-3-line me-1"></i> Data Downtime & Perbaikan
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row g-3 mb-4">
                                {{-- Jam Mulai --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Jam Mulai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control time-calc" id="f-jamMulai" name="jam_mulai" required>
                                </div>

                                {{-- Jam Selesai --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Jam Selesai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control time-calc" id="f-jamSelesai" name="jam_selesai" required>
                                </div>

                                {{-- Total Menit --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Total Menit <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="f-totalMenit" name="total_menit" min="0" required>
                                    <small class="text-muted" style="font-size: 10px;">Dihitung otomatis (bisa diedit manual)</small>
                                </div>

                                {{-- Jenis DT (Master Data) --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Jenis DT</label>
                                    <select class="form-select" id="f-jenisDtId" name="jenis_dt_id">
                                        <option value="">Pilih Jenis DT...</option>
                                        @foreach($jenisDts as $dt)
                                            <option value="{{ $dt->id }}">{{ $dt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- AM / PM --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">AM / PM <span class="text-danger">*</span></label>
                                    <select class="form-select" id="f-amPm" name="am_pm" required>
                                        <option value="">Pilih...</option>
                                        <option value="PM">PM</option>
                                        <option value="AM">AM</option>
                                        <option value="AM/PM">AM/PM</option>
                                    </select>
                                </div>

                                {{-- Electrical / Mechanical --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Electrical / Mechanical <span class="text-danger">*</span></label>
                                    <select class="form-select" id="f-electricalMechanical" name="electrical_mechanical" required>
                                        <option value="">Pilih...</option>
                                        <option value="Electrical">Electrical</option>
                                        <option value="Mechanical">Mechanical</option>
                                    </select>
                                </div>

                                {{-- Keterangan Downtime --}}
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Keterangan Downtime</label>
                                    <textarea class="form-control" id="f-downtime" name="downtime" rows="2" placeholder="Detail apa yang rusak / terhambat..."></textarea>
                                </div>

                                {{-- Keterangan Tambahan --}}
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Keterangan / Catatan Tambahan</label>
                                    <textarea class="form-control" id="f-keterangan" name="keterangan" rows="2" placeholder="Catatan perbaikan, suku cadang yang digunakan, dll..."></textarea>
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Submit --}}
                            <button type="submit" class="btn btn-primary w-100 py-2 fs-15 shadow-sm" id="btnSubmit">
                                <i class="ri-send-plane-line me-1"></i>
                                <span id="submitLabel">Kirim Laporan CM</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const paramEditId = urlParams.get('edit');

    if (paramEditId) {
        initFormState(paramEditId);
    }

    // Auto-calculate duration in minutes
    $('.time-calc').on('change input', function() {
        const start = $('#f-jamMulai').val();
        const end = $('#f-jamSelesai').val();
        if (start && end) {
            const [sh, sm] = start.split(':').map(Number);
            const [eh, em] = end.split(':').map(Number);
            let diff = (eh * 60 + em) - (sh * 60 + sm);
            if (diff < 0) {
                // Handle overnight shift, e.g. 23:00 to 01:00 (next day)
                diff += 24 * 60;
            }
            $('#f-totalMenit').val(diff);
        }
    });

    function initFormState(editIdVal) {
        $.get("{{ route('epr.cm.get-reports') }}", function(data) {
            const r = data.find(x => x.id == editIdVal);
            if (!r) return;

            $('#f-id').val(r.id);
            $('#f-tanggal').val(r.tanggal);
            $('#f-shift').val(r.shift);
            $('#f-grup').val(r.grup);
            $('#f-mesin').val(r.mesin);
            $('#f-pouchSachet').val(r.pouch_sachet);
            $('#f-jamMulai').val(r.jam_mulai);
            $('#f-jamSelesai').val(r.jam_selesai);
            $('#f-totalMenit').val(r.total_menit);
            $('#f-downtime').val(r.downtime || '');
            $('#f-keterangan').val(r.keterangan || '');
            $('#f-jenisDtId').val(r.jenis_dt_id || '');
            $('#f-amPm').val(r.am_pm);
            $('#f-electricalMechanical').val(r.electrical_mechanical);

            // Banner
            $('#editBanner').removeClass('d-none');
            $('#editBannerSub').text(`Menyunting laporan CM tanggal ${r.tanggal} pada mesin ${r.mesin}`);
            $('#submitLabel').text('Simpan Perubahan Laporan');
            $('#btnSubmit').removeClass('btn-primary').addClass('btn-warning');
        });
    }

    window.cancelEdit = function() {
        window.location.href = "{{ route('epr.cm.data') }}";
    };

    $('#formReport').submit(function(e) {
        e.preventDefault();

        const btn = $('#btnSubmit');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm me-2"></div> Memproses...');

        const formData = {
            id: $('#f-id').val() || null,
            tanggal: $('#f-tanggal').val(),
            shift: $('#f-shift').val(),
            grup: $('#f-grup').val(),
            mesin: $('#f-mesin').val().trim(),
            pouch_sachet: $('#f-pouchSachet').val(),
            jam_mulai: $('#f-jamMulai').val(),
            jam_selesai: $('#f-jamSelesai').val(),
            total_menit: parseInt($('#f-totalMenit').val()) || 0,
            downtime: $('#f-downtime').val(),
            keterangan: $('#f-keterangan').val(),
            jenis_dt_id: $('#f-jenisDtId').val() || null,
            am_pm: $('#f-amPm').val(),
            electrical_mechanical: $('#f-electricalMechanical').val()
        };

        $.ajax({
            url: "{{ route('epr.cm.store') }}",
            method: 'POST',
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: JSON.stringify(formData),
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Laporan Corrective Maintenance berhasil disimpan',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('epr.cm.data') }}";
                    });
                } else {
                    Swal.fire('Error', res.message || 'Gagal menyimpan laporan', 'error');
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Terjadi kesalahan pada server';
                Swal.fire('Error', msg, 'error');
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endsection
