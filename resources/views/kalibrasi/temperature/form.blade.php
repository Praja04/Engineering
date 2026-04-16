@extends('layouts.app')

@section('title', 'Form Kalibrasi Temperature')

@section('styles')
    <style>
        .mini-container {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .mini-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }

        .input-mini {
            width: 100%;
            padding: 4px;
            font-size: 12px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fcf0cc;
        }

        .input-mini:focus {
            outline: none;
            border-color: #0d6efd;
        }
    </style>
@endsection

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
                    <form id="formTemperature" method="POST">
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
                                                    <option value="{{ $a->id }}">
                                                        {{ $a->kode_alat . ' - ' . $a->nama_alat }}</option>
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
                                        <label for="suhu_ruangan" class="form-label">Suhu Ruangan (°C)</label>
                                        <input type="number" class="form-control" id="suhu_ruangan" name="suhu_ruangan"
                                            placeholder="25">
                                        {{-- <input type="hidden" name="suhu_ruangan_final" id="suhu_ruangan_final">
                                        <label for="suhu_ruangan" class="form-label">Suhu Ruangan</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="suhu_ruangan" name="suhu_ruangan"
                                                placeholder="25">
                                            <span class="input-group-text">±</span>
                                            <input type="number" class="form-control" id="toleransi_suhu"
                                                name="toleransi_suhu" placeholder="1">
                                            <span class="input-group-text">°C</span>
                                        </div> --}}
                                    </div>

                                    <!-- KELEMBABAN -->
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="kelembaban" class="form-label">Kelembaban (%)</label>
                                        <input type="number" class="form-control" id="kelembaban" name="kelembaban"
                                            placeholder="47">
                                        {{-- <input type="hidden" name="kelembaban_final" id="kelembaban_final">
                                        <label for="kelembaban" class="form-label">Kelembaban</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="kelembaban" name="kelembaban"
                                                placeholder="47">
                                            <span class="input-group-text">±</span>
                                            <input type="number" class="form-control" id="toleransi_kelembaban"
                                                name="toleransi_kelembaban" placeholder="3">
                                            <span class="input-group-text">%</span>
                                        </div> --}}
                                    </div>

                                    <!-- TANGGAL -->
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="tgl_kalibrasi" class="form-label">Tanggal Kalibrasi</label>
                                        <input type="date" class="form-control" id="tgl_kalibrasi" name="tgl_kalibrasi">
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
                                <div class="col-md-6 mb-3 gy-2 align-items-center">
                                    <label for="titik_naik" class="form-label">Jumlah Titik Kalibrasi</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="jumlahTitik" name="titik_naik"
                                            min="1" max="10" placeholder="0">
                                        <button class="btn btn-outline-primary" type="button"
                                            id="generateRows">Generate</button>
                                        <button class="btn btn-outline-info" type="button" id="addRows">+ Tambah
                                            Titik</button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="tableTemperature">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width:120px;">Titik Kalibrasi</th>
                                                <th class="text-center">Penunjuk Standar</th>
                                                <th class="text-center">Penunjuk Alat</th>
                                                <th style="width:80px;" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center text-muted" id="emptyState">
                                                <td colspan="4">Silakan tentukan jumlah titik kalibrasi di atas</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="text-start mt-4">
                                    <div class="d-flex flex-wrap gap-2 justify-content-start">
                                        <button type="button" class="btn btn-outline-danger" id="btnReset">
                                            <i class="mdi mdi-close-circle-outline me-1"></i> Reset Draft
                                        </button>

                                        <button type="submit" id="btnSimpanKalibrasi"
                                            class="btn btn-success btnSaveKalibrasi">
                                            <i class="mdi mdi-content-save me-1"></i> Submit
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
            $('#alat_id').select2({
                theme: 'bootstrap-5'
            });

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

            const STORAGE_KEY = 'draft_temperature';

            function saveDraft() {

                const formData = {
                    header: {
                        alat_id: $('#alat_id').val(),
                        lokasi_kalibrasi: $('#lokasi_kalibrasi').val(),
                        suhu_ruangan: $('#suhu_ruangan').val(),
                        toleransi_suhu: $('#toleransi_suhu').val(),
                        kelembaban: $('#kelembaban').val(),
                        toleransi_kelembaban: $('#toleransi_kelembaban').val(),
                        tgl_kalibrasi: $('#tgl_kalibrasi').val(),
                    },
                    jumlahBaris: $('#tableTemperature tbody tr').length,
                    data: []
                };

                $('#tableTemperature tbody tr').each(function() {

                    const titik = $(this).find('input[name*="titik_kalibrasi"]').val();

                    let standar = [];
                    let alat = [];

                    $(this).find('input[name*="penunjuk_standar"]').each(function() {
                        standar.push($(this).val());
                    });

                    $(this).find('input[name*="penunjuk_alat"]').each(function() {
                        alat.push($(this).val());
                    });

                    // kalau semua kosong skip
                    const isEmpty = !titik &&
                        standar.every(v => !v) &&
                        alat.every(v => !v);

                    if (!isEmpty) {
                        formData.data.push({
                            titik_kalibrasi: titik,
                            penunjuk_standar: standar,
                            penunjuk_alat: alat
                        });
                    }
                });

                localStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
            }

            $(document).on('input change', '#formTemperature input, #formTemperature select', function() {
                saveDraft();
            });

            function loadDraft() {

                const draft = localStorage.getItem(STORAGE_KEY);
                if (!draft) return;

                const data = JSON.parse(draft);

                // restore header
                $('#alat_id').val(data.header.alat_id).trigger('change');
                $('#lokasi_kalibrasi').val(data.header.lokasi_kalibrasi);
                $('#suhu_ruangan').val(data.header.suhu_ruangan);
                $('#toleransi_suhu').val(data.header.toleransi_suhu);
                $('#kelembaban').val(data.header.kelembaban);
                $('#toleransi_kelembaban').val(data.header.toleransi_kelembaban);
                $('#tgl_kalibrasi').val(data.header.tgl_kalibrasi);

                const tbody = $('#tableTemperature tbody');

                if (!data.data.length) return;

                data.data.forEach((row, i) => {

                    let standarInputs = '';
                    let alatInputs = '';

                    row.penunjuk_standar.forEach((val, j) => {
                        standarInputs += `
                            <input type="number" step="0.01"
                                name="data[${i}][penunjuk_standar][]"
                                value="${val}"
                                class="input-mini">
                        `;
                    });

                    row.penunjuk_alat.forEach((val, j) => {
                        alatInputs += `
                            <input type="number" step="0.01"
                                name="data[${i}][penunjuk_alat][]"
                                value="${val}"
                                class="input-mini">
                        `;
                    });

                    tbody.append(`
                        <tr>
                            <td class="text-center">
                                <input type="number"
                                    name="data[${i}][titik_kalibrasi]"
                                    value="${row.titik_kalibrasi}"
                                    class="input-mini text-center">
                            </td>

                            <td><div class="mini-container">${standarInputs}</div></td>
                            <td><div class="mini-container">${alatInputs}</div></td>

                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger btn-delete-row">✕</button>
                            </td>
                        </tr>
                    `);

                    if (i === data.data.length - 1) {
                        $('#emptyState').remove();
                    }
                });
            }

            loadDraft();

            // Generate baris sesuai input jumlah
            $('#generateRows').on('click', function() {

                const jumlah = parseInt($('#jumlahTitik').val());
                const tbody = $('#tableTemperature tbody');
                tbody.empty();

                if (!jumlah || jumlah <= 0) {
                    tbody.html(`
                        <tr class="text-center text-muted">
                            <td colspan="4">Masukkan jumlah titik kalibrasi yang valid.</td>
                        </tr>
                    `);
                    return;
                }

                for (let i = 0; i < jumlah; i++) {

                    let standarInputs = '';
                    let alatInputs = '';

                    for (let j = 0; j < 3; j++) {
                        standarInputs += `
                            <input type="number" step="0.01"
                                name="data[${i}][penunjuk_standar][]"
                                class="input-mini">
                        `;

                        alatInputs += `
                            <input type="number" step="0.01"
                                name="data[${i}][penunjuk_alat][]"
                                class="input-mini">
                        `;
                    }

                    tbody.append(`
                        <tr>
                            <td class="text-center">
                                <input type="number"
                                    name="data[${i}][titik_kalibrasi]"
                                    value="${i + 1}"
                                    class="input-mini text-center">
                            </td>

                            <td>
                                <div class="mini-container">
                                    ${standarInputs}
                                </div>
                            </td>

                            <td>
                                <div class="mini-container">
                                    ${alatInputs}
                                </div>
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger btn-delete-row">✕</button>
                            </td>
                        </tr>
                    `);
                }

                saveDraft();
            });

            $(document).on('click', '.btn-delete-row', function() {

                $(this).closest('tr').remove();

                // renumber ulang
                $('#tableTemperature tbody tr').each(function(index) {

                    $(this).find('input[name^="data"]').each(function() {

                        const name = $(this).attr('name');
                        const newName = name.replace(/data\[\d+\]/, `data[${index}]`);
                        $(this).attr('name', newName);
                    });

                    $(this).find('input[name*="titik_kalibrasi"]').val(index + 1);
                });

                if ($('#tableTemperature tbody tr').length === 0) {
                    $('#tableTemperature tbody').html(`
                        <tr class="text-center text-muted">
                            <td colspan="4">Silakan tentukan jumlah titik kalibrasi di atas</td>
                        </tr>
                    `);
                }

                saveDraft();
            });

            $('#addRows').on('click', function() {

                const tbody = $('#tableTemperature tbody');

                // kalau masih empty state, kosongkan dulu
                if (tbody.find('tr').length === 1 && tbody.find('td').length === 1) {
                    tbody.empty();
                }

                const rowIndex = tbody.find('tr').length; // index berikutnya

                let standarInputs = '';
                let alatInputs = '';

                for (let j = 0; j < 3; j++) {
                    standarInputs += `
                        <input type="number" step="0.01"
                            name="data[${rowIndex}][penunjuk_standar][]"
                            
                            class="input-mini">
                    `;

                    alatInputs += `
                        <input type="number" step="0.01"
                            name="data[${rowIndex}][penunjuk_alat][]"
                            
                            class="input-mini">
                    `;
                }

                tbody.append(`
                    <tr>
                        <td class="text-center">
                            <input type="number"
                                name="data[${rowIndex}][titik_kalibrasi]"
                                value="${rowIndex + 1}"
                                class="input-mini text-center">
                        </td>

                        <td>
                            <div class="mini-container">
                                ${standarInputs}
                            </div>
                        </td>

                        <td>
                            <div class="mini-container">
                                ${alatInputs}
                            </div>
                        </td>

                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger btn-delete-row">✕</button>
                        </td>
                    </tr>
                `);

                saveDraft();
            });

            // ---- EVENT: submit ----
            $('#formTemperature').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();

                Swal.fire({
                    title: 'Menyimpan data...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "{{ route('kalibrasi.temperature.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(res) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message ||
                                'Data kalibrasi temperature berhasil disimpan.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // reset form
                        $('#formTemperature')[0].reset();

                        $('#tableTemperature tbody').html(`
                            <tr class="text-center text-muted">
                                <td colspan="4">
                                    Silakan tentukan jumlah titik kalibrasi di atas
                                </td>
                            </tr>
                        `);

                        // hapus draft localStorage
                        localStorage.removeItem(STORAGE_KEY);
                    },
                    error: function(xhr) {

                        Swal.close();

                        if (xhr.status === 422) {

                            let errors = xhr.responseJSON.errors;
                            let errorMessages = '';

                            $.each(errors, function(key, value) {
                                errorMessages += `• ${value[0]}<br>`;
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                html: errorMessages
                            });

                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menyimpan data.'
                            });
                        }
                    }
                });
            });

            // === Tombol Reset Draft LocalStorage ===
            $('#btnReset').on('click', function() {

                Swal.fire({
                    title: 'Hapus Draft?',
                    text: "Data draft lokal akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {

                    if (result.isConfirmed) {

                        localStorage.removeItem(STORAGE_KEY);

                        $('#formTemperature')[0].reset();

                        $('#tableTemperature tbody').html(`
                            <tr class="text-center text-muted">
                                <td colspan="4">
                                    Silakan tentukan jumlah titik kalibrasi di atas
                                </td>
                            </tr>
                        `);

                        Swal.fire({
                            icon: 'success',
                            title: 'Draft Dihapus',
                            text: 'Draft lokal berhasil dibersihkan.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });
        });
    </script>
@endsection
