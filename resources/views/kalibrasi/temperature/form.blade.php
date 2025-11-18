@extends('layouts.app')

@section('title', 'Form Kalibrasi Temperature')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title d-sm-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <!-- Tombol Back -->
                            <a href="{{ route('kalibrasi.form.dashboard') }}"
                                class="btn btn-outline-primary rounded-pill px-4 d-flex align-items-center">
                                <i class="mdi mdi-arrow-left me-1"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header bg-soft-primary py-3">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-between g-2">
                            <div class="col-12 col-sm-auto d-flex align-items-center flex-wrap gap-2">
                                <h5 class="mb-0 d-flex align-items-center">
                                    Form Kalibrasi Temperature
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="formKalibrasi" method="POST">
                        @csrf
                        <div class="card border border-primary border-opacity-50 mb-4">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-clipboard-list-outline me-2"></i>Data Utama Kalibrasi</strong>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- PILIH KODE ALAT -->
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <label for="alat_id" class="form-label">Pilih Kode Alat</label>
                                        <div class="input-group">
                                            <select class="form-select" id="alat_id" name="alat_id">
                                                <option value="">-- Pilih Kode Alat --</option>
                                                @foreach ($alat as $a)
                                                    <option value="{{ $a->id }}">{{ $a->kode_alat }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" id="btnDetail" class="btn btn-outline-primary"
                                                data-bs-toggle="collapse" data-bs-target="#collapseAlatDetail" disabled>
                                                <i class="mdi mdi-information-outline me-2"></i>Detail
                                            </button>
                                        </div>
                                    </div>

                                    <!-- DATA KALIBRASI -->
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <label for="lokasi_kalibrasi" class="form-label">Lokasi Kalibrasi</label>
                                        <input type="text" class="form-control" id="lokasi_kalibrasi"
                                            name="lokasi_kalibrasi" placeholder="Laboratorium Kalibrasi">
                                    </div>

                                    <!-- DETAIL ALAT -->
                                    <div class="col-12">
                                        <div class="collapse mt-3" id="collapseAlatDetail">
                                            <div class="card border-info shadow-sm">
                                                <div class="card-header bg-soft-info">
                                                    <strong>Detail Alat</strong>
                                                </div>
                                                <div class="card-body small">
                                                    <div class="row g-2 gy-3">
                                                        <div class="col-sm-6 col-md-4"><strong>Departemen Pemilik:</strong>
                                                            <span id="departemen_pemilik">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Lokasi Alat:</strong>
                                                            <span id="lokasi_alat">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>No. Kalibrasi:</strong>
                                                            <span id="no_kalibrasi">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Nama Alat:</strong>
                                                            <span id="nama_alat">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Merk:</strong>
                                                            <span id="merk">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Tipe:</strong>
                                                            <span id="tipe">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Kapasitas:</strong>
                                                            <span id="kapasitas">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Resolusi:</strong>
                                                            <span id="resolusi">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Range Penggunaan:</strong>
                                                            <span id="range_penggunaan_alat">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Limits of Permissible
                                                                Error:</strong>
                                                            <span id="limits_of_permissible_error">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Metode Kalibrasi:</strong>
                                                            <span id="metode_kalibrasi">-</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SUHU -->
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <input type="hidden" name="suhu_ruangan_final" id="suhu_ruangan_final">
                                        <label for="suhu_ruangan" class="form-label">Suhu Ruangan</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="suhu_ruangan" name="suhu_ruangan"
                                                placeholder="25">
                                            <span class="input-group-text">±</span>
                                            <input type="number" class="form-control" id="toleransi_suhu"
                                                name="toleransi_suhu" placeholder="1">
                                            <span class="input-group-text">°C</span>
                                        </div>
                                    </div>

                                    <!-- KELEMBABAN -->
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <input type="hidden" name="kelembaban_final" id="kelembaban_final">
                                        <label for="kelembaban" class="form-label">Kelembaban</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="kelembaban" name="kelembaban"
                                                placeholder="47">
                                            <span class="input-group-text">±</span>
                                            <input type="number" class="form-control" id="toleransi_kelembaban"
                                                name="toleransi_kelembaban" placeholder="3">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>

                                    <!-- TANGGAL -->
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="tgl_kalibrasi" class="form-label">Tanggal Kalibrasi</label>
                                        <input type="date" class="form-control" id="tgl_kalibrasi"
                                            name="tgl_kalibrasi">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- === Data Pengukuran Temperature === --}}
                        <div class="card border border-primary border-opacity-50">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-thermometer me-2"></i>Data Pengukuran Temperature</strong>
                            </div>

                            <div class="card-body">

                                {{-- Input jumlah titik kalibrasi --}}
                                <div class="col-xxl-3 col-md-3 mb-3">
                                    <label for="titik_kalibrasi" class="form-label">Jumlah Titik Kalibrasi</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="titik_kalibrasi"
                                            name="titik_kalibrasi" min="1" max="10" placeholder="0">
                                        <button type="button" class="btn btn-outline-primary btn-generate"
                                            id="generateTitik">
                                            <i class="mdi mdi-plus me-1"></i>Buat / Tambah Titik
                                        </button>
                                    </div>
                                </div>

                                {{-- Container untuk field titik kalibrasi --}}
                                <div id="containerTitik"></div>

                                {{-- Tombol Aksi --}}
                                <div class="text-start mt-4">
                                    <div class="d-flex flex-wrap gap-2 justify-content-start">
                                        <button type="button" class="btn btn-outline-danger rounded-pill px-4"
                                            id="btnResetKalibrasi">
                                            <i class="mdi mdi-close-circle-outline me-1"></i> Reset Draft
                                        </button>

                                        <button type="submit" id="btnSimpanKalibrasi"
                                            class="btn btn-success btnSaveKalibrasi rounded-pill px-4">
                                            <i class="mdi mdi-send-check-outline me-1"></i> Selesai & Submit
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // ---- GENERATE TITIK ----
            function generateTitikFields(jumlahTitik) {
                const container = $('#containerTitik').empty();

                for (let i = 1; i <= jumlahTitik; i++) {
                    const html = `
                        <div class="card border shadow-sm mb-3">
                            <div class="card-body">
                                <h5 class="mb-3 fw-bold text-primary">Titik Kalibrasi ${i}</h5>

                                <div class="mb-3">
                                    <label class="form-label">Nilai Titik Kalibrasi (misal: 50, 100) °C</label>
                                    <input type="number" class="form-control bg-light"
                                        name="temperature[${i}][titik_kalibrasi]" step="0.1">
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold d-block">Penunjuk Standar</label>
                                        <input type="number" class="form-control mb-2 bg-light" name="temperature[${i}][standar_1]" step="0.1">
                                        <input type="number" class="form-control mb-2 bg-light" name="temperature[${i}][standar_2]" step="0.1">
                                        <input type="number" class="form-control bg-light" name="temperature[${i}][standar_3]" step="0.1">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold d-block">Penunjuk Alat</label>
                                        <input type="number" class="form-control mb-2 bg-light" name="temperature[${i}][alat_1]" step="0.1">
                                        <input type="number" class="form-control mb-2 bg-light" name="temperature[${i}][alat_2]" step="0.1">
                                        <input type="number" class="form-control bg-light" name="temperature[${i}][alat_3]" step="0.1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(html);
                }
            }

            // ---- EVENT: pilih alat ----
            $('#alat_id').on('change', function() {
                const id = $(this).val();
                console.log('[EVENT] alat_id berubah:', $(this).val());
                if (!id) {
                    $('#btnDetail').prop('disabled', true);
                    return;
                }

                // load detail alat dari API
                $.get(`/api/kalibrasi/pressure/data/alat/${id}`, function(res) {
                    const d = res.data || {};
                    $('#btnDetail').prop('disabled', false);
                    $('#departemen_pemilik').text(d.departemen_pemilik || '-');
                    $('#lokasi_alat').text(d.lokasi_alat || '-');
                    $('#no_kalibrasi').text(d.no_kalibrasi || '-');
                    $('#nama_alat').text(d.nama_alat || '-');
                    $('#merk').text(d.merk || '-');
                    $('#tipe').text(d.tipe || '-');
                    $('#kapasitas').text(d.kapasitas || '-');
                    $('#resolusi').text(d.resolusi || '-');
                    $('#range_penggunaan_alat').text(d.range_penggunaan_alat || '-');
                    $('#limits_of_permissible_error').text(d.limits_of_permissible_error || '-');
                    $('#metode_kalibrasi').text(d.metode_kalibrasi || '-');
                });
            });

            // ---- EVENT: generate titik ----
            $('#generateTitik').on('click', function() {
                const jumlahTitik = parseInt($('#titik_kalibrasi').val(), 10);
                if (isNaN(jumlahTitik) || jumlahTitik < 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Masukkan jumlah titik kalibrasi yang valid!'
                    });
                    return;
                }
                generateTitikFields(jumlahTitik);
            });

            // ---- EVENT: submit ----
            $('#formKalibrasi').on('submit', function(e) {
                e.preventDefault();

                const alatId = $('#alat_id').val();
                if (!alatId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih alat dulu',
                        text: 'Silakan pilih kode alat sebelum submit.'
                    });
                    return;
                }

                // format suhu & kelembaban final
                const suhu = $('#suhu_ruangan').val();
                const tolSuhu = $('#toleransi_suhu').val();
                const kelembaban = $('#kelembaban').val();
                const tolKelembaban = $('#toleransi_kelembaban').val();

                const suhuFormatted = suhu && tolSuhu ? `${suhu}°C ± ${tolSuhu}°C` : (suhu ? `${suhu}°C` :
                    '');
                const kelembabanFormatted = kelembaban && tolKelembaban ?
                    `${kelembaban}% ± ${tolKelembaban}%` :
                    (kelembaban ? `${kelembaban}%` : '');

                $('#suhu_ruangan_final').val(suhuFormatted);
                $('#kelembaban_final').val(kelembabanFormatted);

                const formData = new FormData(this);

                $.ajax({
                    url: "{{ route('kalibrasi.temperature.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#btnSimpanKalibrasi').prop('disabled', true).text('Menyimpan...');
                    },
                    success: function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data kalibrasi berhasil disimpan.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        $('#formKalibrasi')[0].reset();
                        $('#containerTitik').empty();
                        $('#btnSimpanKalibrasi').prop('disabled', false).text(
                            'Selesai & Submit');
                        $('#alat_id').val('').trigger('change');
                    },
                    error: function(xhr) {
                        $('#btnSimpanKalibrasi').prop('disabled', false).text(
                            'Selesai & Submit');
                        const msg = xhr.status === 422 && xhr.responseJSON?.errors ?
                            Object.values(xhr.responseJSON.errors).map(e => e.join(', ')).join(
                                '<br>') :
                            'Silakan coba lagi nanti.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: msg
                        });
                    }
                });
            });

            // === Tombol Reset Draft LocalStorage ===
            $('#btnResetKalibrasi').on('click', function(e) {
                e.preventDefault();
                const alatId = $('#alat_id').val();

                if (!alatId) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak ada alat dipilih',
                        text: 'Silakan pilih alat dulu sebelum mereset draft.'
                    });
                    return;
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Reset Draft?',
                    text: 'Draft kalibrasi untuk alat ini akan dihapus dari penyimpanan lokal.',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const key = storageKeyForAlat(alatId);
                        if (key && localStorage.getItem(key)) {
                            localStorage.removeItem(key);
                            console.log(`[LocalStorage] Draft dihapus untuk key: ${key}`);
                        }

                        // Bersihkan form & trigger ulang change agar state alat kosong diakui
                        $('#formKalibrasi')[0].reset();
                        $('#containerTitik').empty();
                        $('#alat_id').val('').trigger('change');


                        Swal.fire({
                            icon: 'success',
                            title: 'Draft Dihapus',
                            text: 'Draft untuk alat ini telah dihapus dari localStorage.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });
        });
    </script>
@endsection
