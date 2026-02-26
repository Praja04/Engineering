@extends('layouts.app')

@section('title', 'Form Thermometer')

@section('styles')
    <style>
        #tableThermo {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        #tableThermo th,
        #tableThermo td {
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        #tableThermo input {
            width: 100%;
            border: none;
            outline: none;
            text-align: center;
            font-size: 13px;
            padding: 4px;
            background-color: #fcf0cc
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
                                    Form Kalibrasi Thermometer
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="formThermometer" method="POST">
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
                                    </div>

                                    <!-- KELEMBABAN -->
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="kelembaban" class="form-label">Kelembaban (%)</label>
                                        <input type="number" class="form-control" id="kelembaban" name="kelembaban"
                                            placeholder="47">
                                    </div>

                                    <!-- TANGGAL -->
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="tgl_kalibrasi" class="form-label">Tanggal Kalibrasi</label>
                                        <input type="date" class="form-control" id="tgl_kalibrasi" name="tgl_kalibrasi">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- === Data Pengukuran Pressure === --}}
                        <div class="card border border-primary border-opacity-50">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-gauge me-2"></i>Data Pengukuran Pressure</strong>
                            </div>
                            <div class="card-body">
                                <div class="col-md-6 mb-3">
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
                                    <table class="table table-bordered" id="tableThermo">
                                        <thead class="text-center align-middle text-nowrap table-light">
                                            <tr>
                                                <th rowspan="2">Titik Kalibrasi</th>
                                                <th rowspan="2">Posisi Bagian</th>

                                                <th colspan="2">Penunjukan Standar</th>
                                                <th colspan="2">Penunjukan Alat</th>
                                                <th rowspan="2" style="width:80px;" class="text-center">Aksi</th>
                                            </tr>

                                            <tr>
                                                <th>Suhu (°C)</th>
                                                <th>RH (%)</th>
                                                <th>Suhu (°C)</th>
                                                <th>RH (%)</th>
                                            </tr>

                                        </thead>

                                        <tbody>
                                            <tr class="text-center text-muted" id="emptyState">
                                                <td colspan="4">Silakan tentukan jumlah titik kalibrasi di atas</td>
                                            </tr>
                                            <!-- generate row di sini -->
                                        </tbody>
                                    </table>

                                </div>

                                <!-- Tombol Aksi -->
                                <div class="text-start mt-4">
                                    <div class="d-flex flex-wrap gap-2 justify-content-start">
                                        <button type="button" class="btn btn-outline-danger" id="btnResetKalibrasi">
                                            <i class="mdi mdi-close-circle-outline me-1"></i>Reset Draft
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
            const STORAGE_KEY = 'formThermohygrometer';

            $('#alat_id').change(function() {
                var id = $(this).val();

                if (!id) {
                    $('#btnDetail').prop('disabled', true);
                    return;
                }

                $.get('/api/kalibrasi/pressure/data/alat/' + id, function(res) {
                    let data = res.data;

                    $('#btnDetail').prop('disabled', false);
                    $('#departemen_pemilik').text(data.departemen_pemilik || '-');
                    $('#lokasi_alat').text(data.lokasi_alat || '-');
                    $('#no_kalibrasi').text(data.no_kalibrasi || '-');
                    $('#nama_alat').text(data.nama_alat || '-');
                    $('#merk').text(data.merk || '-');
                    $('#tipe').text(data.tipe || '-');
                    $('#kapasitas').text(data.kapasitas || '-');
                    $('#resolusi').text(data.resolusi || '-');
                    $('#range_penggunaan_alat').text(data.range_penggunaan_alat || '-');
                    $('#limits_of_permissible_error').text(data.limits_of_permissible_error || '-');
                    $('#metode_kalibrasi').text(data.metode_kalibrasi || '-');
                });
            });

            $(document).on('input change',
                '#formThermometer input, #formThermometer select, #formThermometer textarea, #tableThermo input',
                function() {
                    saveForm();
                }
            );

            function saveForm() {

                const data = {
                    header: {},
                    rows: []
                };

                // 🔹 HEADER (yang bukan data[...])
                $('#formThermometer').find('input, select, textarea').each(function() {

                    if (this.name && !this.name.startsWith('data[')) {
                        data.header[this.name] = $(this).val();
                    }
                });

                // 🔹 ROWS (berdasarkan rowIndex)
                for (let i = 0; i < rowIndex; i++) {

                    let rowData = {
                        titik_kalibrasi: $(`[name="data[${i}][titik_kalibrasi]"]`).val() || '',
                        posisi: $(`[name="data[${i}][posisi]"]`).val() || '',
                        standar: [],
                        alat: []
                    };

                    for (let j = 0; j < 3; j++) {

                        rowData.standar.push({
                            suhu: $(`[name="data[${i}][standar][${j}][suhu]"]`).val() || '',
                            rh: $(`[name="data[${i}][standar][${j}][rh]"]`).val() || ''
                        });

                        rowData.alat.push({
                            suhu: $(`[name="data[${i}][alat][${j}][suhu]"]`).val() || '',
                            rh: $(`[name="data[${i}][alat][${j}][rh]"]`).val() || ''
                        });
                    }

                    data.rows.push(rowData);
                }

                localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            }

            function loadForm() {

                const saved = localStorage.getItem(STORAGE_KEY);
                if (!saved) return;

                const data = JSON.parse(saved);

                // 🔹 HEADER
                Object.keys(data.header).forEach(name => {
                    const $el = $(`[name="${name}"]`);
                    if ($el.length) {
                        $el.val(data.header[name]);
                    }
                });

                // 🔹 CLEAR TABLE
                $('#tableThermo tbody').empty();
                rowIndex = 0;

                if (!data.rows || data.rows.length === 0) {
                    return;
                }

                // 🔹 GENERATE ROWS + ISI NILAI
                data.rows.forEach(row => {

                    const currentIndex = rowIndex;
                    addThermoPoint(); // generate kosong dulu

                    // isi titik & posisi
                    $(`[name="data[${currentIndex}][titik_kalibrasi]"]`)
                        .val(row.titik_kalibrasi);

                    $(`[name="data[${currentIndex}][posisi]"]`)
                        .val(row.posisi);

                    for (let j = 0; j < 3; j++) {

                        $(`[name="data[${currentIndex}][standar][${j}][suhu]"]`)
                            .val(row.standar[j]?.suhu || '');

                        $(`[name="data[${currentIndex}][standar][${j}][rh]"]`)
                            .val(row.standar[j]?.rh || '');

                        $(`[name="data[${currentIndex}][alat][${j}][suhu]"]`)
                            .val(row.alat[j]?.suhu || '');

                        $(`[name="data[${currentIndex}][alat][${j}][rh]"]`)
                            .val(row.alat[j]?.rh || '');
                    }
                });
            }

            let rowIndex = 0;

            // 🔹 GENERATE TITIK
            $('#generateRows').on('click', function() {

                const jumlah = parseInt($('#jumlahTitik').val());
                const tbody = $('#tableThermo tbody');
                tbody.empty();
                rowIndex = 0;

                if (!jumlah || jumlah <= 0) return;

                for (let i = 0; i < jumlah; i++) {
                    addThermoPoint();
                }

                saveForm();
            });

            function addThermoPoint() {

                const tbody = $('#tableThermo tbody');
                $('#emptyState').remove(); // hapus empty state kalau ada

                let rows = '';
                const currentIndex = rowIndex;

                for (let i = 0; i < 3; i++) {

                    let parentCell = '';
                    let actionCell = '';

                    if (i === 0) {
                        parentCell = `
                            <td rowspan="3">
                                <input type="number"
                                    name="data[${currentIndex}][titik_kalibrasi]">
                            </td>
                            <td rowspan="3">
                                <input type="text"
                                    name="data[${currentIndex}][posisi]">
                            </td>
                        `;

                        actionCell = `
                            <td rowspan="3" class="text-center align-middle">
                                <button type="button"
                                    class="btn btn-sm btn-danger btn-delete-point"
                                    data-index="${currentIndex}">
                                    ✕
                                </button>
                            </td>
                        `;
                    }

                    rows += `
                        <tr data-group="${currentIndex}">
                            ${parentCell}

                            <td>
                                <input type="number" step="0.01"
                                    name="data[${currentIndex}][standar][${i}][suhu]">
                            </td>

                            <td>
                                <input type="number" step="0.01"
                                    name="data[${currentIndex}][standar][${i}][rh]">
                            </td>

                            <td>
                                <input type="number" step="0.01"
                                    name="data[${currentIndex}][alat][${i}][suhu]">
                            </td>

                            <td>
                                <input type="number" step="0.01"
                                    name="data[${currentIndex}][alat][${i}][rh]">
                            </td>

                            ${actionCell}
                        </tr>
                    `;
                }

                tbody.append(rows);
                rowIndex++;
                saveForm();
            }

            $('#addRows').on('click', function() {
                addThermoPoint();
            });

            $(document).on('click', '.btn-delete-point', function() {

                const index = $(this).data('index');

                $(`#tableThermo tbody tr[data-group="${index}"]`).remove();

                reindexRows();
                saveForm();
            });

            function reindexRows() {

                let newIndex = 0;
                const groupMap = {};

                $('#tableThermo tbody tr').each(function() {

                    const oldIndex = $(this).data('group');

                    // Kalau group lama belum dipetakan
                    if (groupMap[oldIndex] === undefined) {
                        groupMap[oldIndex] = newIndex++;
                    }

                    const updatedIndex = groupMap[oldIndex];

                    // Update data-group
                    $(this).attr('data-group', updatedIndex);

                    // Update semua input name
                    $(this).find('input').each(function() {

                        const name = $(this).attr('name');
                        if (!name) return;

                        const newName = name.replace(/data\[\d+\]/, `data[${updatedIndex}]`);
                        $(this).attr('name', newName);
                    });

                    // Update tombol delete
                    $(this).find('.btn-delete-point')
                        .attr('data-index', updatedIndex);
                });

                rowIndex = newIndex;
            }

            loadForm();

            // simpan button
            $(document).on('click', '.btnSaveKalibrasi', function(e) {
                e.preventDefault();

                $.ajax({
                    url: `{{ route('kalibrasi.thermohygrometer.store') }}`,
                    method: 'POST',
                    data: $('#formThermometer').serialize(),

                    success: function(res) {

                        Swal.fire('Success', res.message, 'success');

                        localStorage.removeItem(STORAGE_KEY);

                        $('#formThermometer')[0].reset();
                        $('#tableThermo tbody').empty();
                        rowIndex = 0;
                    },

                    error: function(xhr) {

                        if (xhr.status === 422) {

                            let errors = xhr.responseJSON.errors;
                            let msg = '';

                            Object.keys(errors).forEach(function(key) {
                                msg += errors[key].join("<br>") + "<br>";
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                html: msg
                            });

                        } else {
                            Swal.fire('Error', 'Terjadi kesalahan pada server!', 'error');
                        }
                    }
                });
            });


            $(document).on('click', '#btnResetKalibrasi', function() {

                Swal.fire({
                    title: 'Reset Data?',
                    text: 'Semua data kalibrasi akan dihapus!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {

                    if (result.isConfirmed) {

                        // 🔹 Hapus local storage
                        localStorage.removeItem(STORAGE_KEY);

                        // 🔹 Reset form header
                        if ($('#formThermometer')[0]) {
                            $('#formThermometer')[0].reset();
                        }

                        // 🔹 Reset jumlah titik
                        $('#jumlahTitik').val('');

                        // 🔹 Kosongkan tabel
                        $('#tableThermo tbody').html(`
                            <tr class="text-center text-muted" id="emptyState">
                                <td colspan="7">
                                    Silakan tentukan jumlah titik kalibrasi di atas
                                </td>
                            </tr>
                        `);

                        // 🔹 Reset index
                        rowIndex = 0;

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data kalibrasi berhasil direset.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });
        })
    </script>
@endsection
