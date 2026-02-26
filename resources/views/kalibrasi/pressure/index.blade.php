@extends('layouts.app')

@section('title', 'Form Pressure')

@section('styles')
    <style>
        .titik-kalibrasi-block {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }

        .titik-kalibrasi-block:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
        }

        /* animasi pulse */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(46, 142, 255, 0.6);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(46, 142, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(46, 142, 255, 0);
            }
        }

        /* Table lebih compact */
        #tabelNaik,
        #tabelTurun {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        /* Padding cell diperkecil */
        #tabelNaik th,
        #tabelNaik td,
        #tabelTurun th,
        #tabelTurun td {
            padding: 4px 5px;
            vertical-align: middle;
        }

        /* Input dibuat kecil & rapat */
        #tabelNaik input,
        #tabelTurun input {
            width: 70px;
            padding: 3px 6px;
            font-size: 12px;
            text-align: center !important;
            background-color: #fcf0cc;
            border: none;
            border: .3px solid #dedede;
        }

        #tabelNaik input:focus,
        #tabelTurun input:focus {
            border: 1px solid #bda14f;
            outline: none;
            box-shadow: none;
        }


        /* Header lebih rapi */
        #tabelNaik thead th,
        #tabelTurun thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-align: center;
        }

        /* Tombol delete kecil */
        #tabelNaik .btn-danger {
            padding: 2px 6px;
            font-size: 11px;
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
                                    Form Kalibrasi Pressure
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
                                            onclick="generateTabel()">Generate</button>
                                        <button class="btn btn-outline-info" type="button" onclick="addRow()">+ Tambah
                                            Titik</button>
                                    </div>
                                </div>

                                <div id="kalibrasiContainer"></div>

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

            const STORAGE_KEY = 'formKalibrasiPressure';

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
                '#formKalibrasi input, #formKalibrasi select, #formKalibrasi textarea, #tabelNaik input, #tabelTurun input',
                function() {
                    saveForm();
                }
            );

            function saveForm() {

                const data = {
                    header: {},
                    rows: []
                };

                // 🔹 HEADER
                $('#formKalibrasi').find('input, select, textarea').each(function() {
                    if (this.name && !this.name.includes('naik_') && !this.name.includes('turun_')) {
                        data.header[this.name] = $(this).val();
                    }
                });

                // 🔹 ROWS
                const totalRows = $('#tabelNaik tbody tr').length;

                for (let i = 0; i < totalRows; i++) {

                    let rowData = {
                        titik_naik: $(`[name="naik_titik_${i}"]`).val(),
                        titik_turun: $(`[name="turun_titik_${i}"]`).val(),
                        naik: {},
                        turun: {}
                    };

                    // Ambil input naik
                    $('#tabelNaik tbody tr').eq(i).find('input').each(function() {
                        rowData.naik[this.name] = $(this).val();
                    });

                    // Ambil input turun
                    $('#tabelTurun tbody tr').eq(i).find('input').each(function() {
                        rowData.turun[this.name] = $(this).val();
                    });

                    data.rows.push(rowData);
                }

                localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            }

            function loadForm() {

                const saved = localStorage.getItem(STORAGE_KEY);
                if (!saved) return;

                const data = JSON.parse(saved);

                if (data.rows.length > 0) {
                    $('#jumlahTitik').val(data.rows.length);
                    generateTabel(data.rows.length);
                }

                // 🔹 Load Header
                Object.keys(data.header).forEach(name => {
                    const $el = $(`[name="${name}"]`);
                    if ($el.length) {
                        $el.val(data.header[name]).trigger('change');
                    }
                });

                // 🔹 Load Rows
                data.rows.forEach((row, i) => {

                    // Naik
                    Object.keys(row.naik).forEach(name => {
                        $(`[name="${name}"]`).val(row.naik[name]);
                    });

                    // Turun
                    Object.keys(row.turun).forEach(name => {
                        $(`[name="${name}"]`).val(row.turun[name]);
                    });

                });
            }

            let rowIndex = 0;

            window.generateTabel = function(jumlah = null) {

                if (jumlah === null) {
                    jumlah = parseInt(document.getElementById('jumlahTitik').value);
                }

                if (!jumlah || jumlah <= 0) return;

                let rowsNaik = '';
                let rowsTurun = '';

                for (let i = 0; i < jumlah; i++) {
                    rowsNaik += createRow(i, 'naik', jumlah);
                    rowsTurun += createRow(i, 'turun', jumlah);
                }

                document.getElementById('kalibrasiContainer').innerHTML = `
                    ${createTable('Tekanan Naik', 'tabelNaik', rowsNaik)}
                    <br>
                    ${createTable('Tekanan Turun', 'tabelTurun', rowsTurun)}
                `;

                saveForm();
            };

            function createTable(judul, tableId, rows) {
                return `
                    <h5 class="mt-4">${judul}</h5>
                    <div class="table-responsive">
                        <table id="${tableId}" class="table table-bordered table-sm text-center" style="width:100%;">
                            <thead>
                                <tr>
                                    <th rowspan="2">Titik</th>
                                    <th colspan="3" class="text-center">Penunjuk Alat</th>
                                    <th colspan="3" class="text-center">Penunjuk Standar</th>
                                    <th rowspan="2" class="text-center">Aksi</th>
                                </tr>
                                <tr>
                                    <th class="text-center">1</th><th class="text-center">2</th><th class="text-center">3</th>
                                    <th class="text-center"  >1</th><th class="text-center">2</th><th class="text-center">3</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rows}
                            </tbody>
                        </table>
                    </div>
                `;
            }

            function createRow(index, tipe, totalTitik) {

                let nilaiTitik;

                if (tipe === 'naik') {
                    nilaiTitik = index;
                } else {
                    nilaiTitik = totalTitik - 1 - index;
                }

                const deleteButton = tipe === 'naik' ?
                    `<button class="btn btn-sm btn-danger" type="button" onclick="deleteRow(${index})">X</button>` :
                    '';

                return `
                    <tr id="row_${tipe}_${index}">
                        <td class="text-center">
                            ${nilaiTitik}
                            <input type="hidden" name="${tipe}_titik_${index}" value="${nilaiTitik}">
                        </td>

                        <td><input type="number" step="0.01" name="${tipe}_alat_${index}_1" required></td>
                        <td><input type="number" step="0.01" name="${tipe}_alat_${index}_2" required></td>
                        <td><input type="number" step="0.01" name="${tipe}_alat_${index}_3" required></td>

                        <td><input type="number" step="0.01" name="${tipe}_standar_${index}_1" required></td>
                        <td><input type="number" step="0.01" name="${tipe}_standar_${index}_2" required></td>
                        <td><input type="number" step="0.01" name="${tipe}_standar_${index}_3" required></td>

                        <td>${deleteButton}</td>
                    </tr>
                `;
            }

            window.addRow = function() {

                const tbodyNaik = document.querySelector('#tabelNaik tbody');
                const tbodyTurun = document.querySelector('#tabelTurun tbody');

                if (!tbodyNaik || !tbodyTurun) return;

                const newIndex = tbodyNaik.querySelectorAll('tr').length;

                tbodyNaik.insertAdjacentHTML('beforeend', createRow(newIndex, 'naik'));
                tbodyTurun.insertAdjacentHTML('beforeend', createRow(newIndex, 'turun'));

                saveForm();
            };

            window.deleteRow = function(index) {

                const rowNaik = document.getElementById(`row_naik_${index}`);
                const rowTurun = document.getElementById(`row_turun_${index}`);

                if (rowNaik) rowNaik.remove();
                if (rowTurun) rowTurun.remove();

                renumberRows(); // 🔥 ini kuncinya
                saveForm();
            }

            function renumberRows() {

                const rowsNaik = document.querySelectorAll('#tabelNaik tbody tr');
                const rowsTurun = document.querySelectorAll('#tabelTurun tbody tr');

                rowsNaik.forEach((row, i) => {
                    const total = rowsNaik.length;

                    row.querySelector('td').innerHTML = `
                        ${i}
                        <input type="hidden" name="naik_titik_${i}" value="${i}">
                    `;

                    // 🔹 Update row id
                    row.id = `row_naik_${i}`;

                    // 🔹 Update input TITIK
                    const titikInput = row.querySelector('.titik-input');
                    if (titikInput) {
                        titikInput.name = `titik_${i}`;
                        titikInput.setAttribute('data-index', i);
                        titikInput.setAttribute('oninput', `syncTitik(${i}, this.value)`);
                    }

                    // 🔹 Update input alat & standar
                    row.querySelectorAll('input').forEach(input => {

                        input.name = input.name
                            .replace(/naik_(alat|standar)_\d+_/,
                                `naik_$1_${i}_`);
                    });

                    // 🔹 Update delete button
                    const btn = row.querySelector('button');
                    if (btn) btn.setAttribute('onclick', `deleteRow(${i})`);
                });


                rowsTurun.forEach((row, i) => {
                    const total = rowsNaik.length;
                    row.querySelector('td').innerHTML = `
                        ${total - 1 - i}
                        <input type="hidden" name="turun_titik_${i}" value="${total - 1 - i}">
                    `;

                    row.id = `row_turun_${i}`;

                    // 🔹 Update display titik id
                    const span = row.querySelector('span');
                    if (span) {
                        span.id = `titik_display_${i}`;
                    }

                    // 🔹 Update name alat & standar
                    row.querySelectorAll('input').forEach(input => {

                        input.name = input.name
                            .replace(/turun_(alat|standar)_\d+_/,
                                `turun_$1_${i}_`);
                    });
                });
            }

            // Jalankan load saat halaman dibuka
            loadForm();

            // simpan button
            $(document).on('click', '.btnSaveKalibrasi', function(e) {
                e.preventDefault();

                let headerData = $('#formKalibrasi').serializeArray();
                let data = {};

                headerData.forEach(function(item) {
                    if (item.name !== '_token') {
                        data[item.name] = item.value;
                    }
                });

                // Cek header kosong
                let isFormFilled = Object.values(data).some(val => val && val.trim() !== '');
                if (!isFormFilled) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Form Kosong!',
                        text: 'Silakan isi data header terlebih dahulu.'
                    });
                    return;
                }

                data.pressure = [];

                let rows = $('#tabelNaik tbody tr');

                if (rows.length === 0) {
                    Swal.fire('Error', 'Belum ada titik kalibrasi digenerate!', 'error');
                    return;
                }

                rows.each(function(i) {

                    let titik = $(`[name="naik_titik_${i}"]`).val();

                    if (!titik) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Titik kosong',
                            text: `Titik pada baris ${i + 1} belum ada`
                        });
                        return false; // stop loop
                    }

                    let alatNaik = [
                        $(`[name="naik_alat_${i}_1"]`).val(),
                        $(`[name="naik_alat_${i}_2"]`).val(),
                        $(`[name="naik_alat_${i}_3"]`).val()
                    ];

                    let standarNaik = [
                        $(`[name="naik_standar_${i}_1"]`).val(),
                        $(`[name="naik_standar_${i}_2"]`).val(),
                        $(`[name="naik_standar_${i}_3"]`).val()
                    ];

                    let alatTurun = [
                        $(`[name="turun_alat_${i}_1"]`).val(),
                        $(`[name="turun_alat_${i}_2"]`).val(),
                        $(`[name="turun_alat_${i}_3"]`).val()
                    ];

                    let standarTurun = [
                        $(`[name="turun_standar_${i}_1"]`).val(),
                        $(`[name="turun_standar_${i}_2"]`).val(),
                        $(`[name="turun_standar_${i}_3"]`).val()
                    ];

                    data.pressure.push({
                        titik_kalibrasi: titik,
                        naik: {
                            alat: alatNaik,
                            standar: standarNaik
                        },
                        turun: {
                            alat: alatTurun,
                            standar: standarTurun
                        }
                    });
                });

                // 🔥 Kirim sebagai JSON
                $.ajax({
                    url: `{{ route('kalibrasi.pressure.store') }}`,
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        ...data,
                        _token: '{{ csrf_token() }}'
                    }),

                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Success', res.message, 'success');

                            localStorage.removeItem(STORAGE_KEY);
                            if ($('#formKalibrasi')[0]) {
                                $('#formKalibrasi')[0].reset();
                            }
                            $('#jumlahTitik').val('');
                            $('#kalibrasiContainer').html('');
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi Kesalahan!', 'error');
                        }
                    },

                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let response = xhr.responseJSON;
                            let msg = '';

                            if (response.errors) {
                                Object.keys(response.errors).forEach(function(key) {
                                    msg += response.errors[key].join("<br>") + "<br>";
                                });
                            } else {
                                msg = response.message || "Validasi gagal.";
                            }

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

            // reset button
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
                        localStorage.removeItem(STORAGE_KEY);
                        if ($('#formKalibrasi')[0]) {
                            $('#formKalibrasi')[0].reset();
                        }
                        $('#jumlahTitik').val('');
                        $('#kalibrasiContainer').html('');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data sementara kalibrasi pressure berhasil direset.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });

            });

        })
    </script>
@endsection
