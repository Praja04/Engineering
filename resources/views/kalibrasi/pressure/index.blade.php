@extends('layouts.app')

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

        .measurement-row {
            background-color: white;
            border-radius: 0.25rem;
            border: 1px solid #e9ecef;
            margin-bottom: 0.5rem;
        }

        .measurement-header {
            background-color: #e9ecef;
            font-weight: 600;
            font-size: 0.875rem;
            color: #495057;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title d-sm-flex align-items-center justify-content-between">
                        <div>
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Kalibrasi</a></li>
                                <li class="breadcrumb-item">Pressure</li>
                                <li class="breadcrumb-item active">Form Pressure</li>
                            </ol>
                        </div>
                        <div>
                            <i class="mdi mdi-information text-info ms-3" id="infoBtn"
                                style="cursor:pointer; font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    {{-- Input data header --}}
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse"
                            data-bs-target="#collapseDataInformation" style="cursor:pointer;">
                            <h5 class="mb-0">Form Pressure Information</h5>
                            <i class="mdi mdi-chevron-up toggle-icon"></i>
                        </div>

                        <div id="collapseDataInformation" class="collapse show">
                            <div class="card-body">
                                <form id="formKalibrasi">

                                    <!-- Section Pilih Alat -->
                                    <div class="card mb-3 border-primary">
                                        <div class="card-header bg-primary text-white py-2">
                                            Informasi Alat (Auto Fill)
                                        </div>
                                        <div class="card-body">
                                            <div class="row gy-3">
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="alat_id" class="form-label">Pilih Kode Alat</label>
                                                    <select class="form-select" id="alat_id" name="alat_id">
                                                        <option value="">-- Pilih Kode Alat --</option>
                                                        @foreach ($alat as $a)
                                                            <option value="{{ $a->id }}">{{ $a->kode_alat }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="departemen_pemilik" class="form-label">Departemen
                                                        Pemilik</label>
                                                    <input type="text" class="form-control text-muted"
                                                        id="departemen_pemilik" name="departemen_pemilik" readonly>
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="lokasi_alat" class="form-label">Lokasi Alat</label>
                                                    <input type="text" class="form-control text-muted" id="lokasi_alat"
                                                        name="lokasi_alat" readonly>
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="no_kalibrasi" class="form-label">Nomor Kalibrasi</label>
                                                    <input type="text" class="form-control text-muted" id="no_kalibrasi"
                                                        name="no_kalibrasi" readonly>
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="nama_alat" class="form-label">Nama Alat</label>
                                                    <input type="text" class="form-control text-muted" id="nama_alat"
                                                        name="nama_alat" readonly>
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="merk" class="form-label">Merk</label>
                                                    <input type="text" class="form-control text-muted" id="merk"
                                                        name="merk" readonly>
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="tipe" class="form-label">Tipe</label>
                                                    <input type="text" class="form-control text-muted" id="tipe"
                                                        name="tipe" readonly>
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="kapasitas" class="form-label">Kapasitas</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control text-muted" id="kapasitas"
                                                            name="kapasitas" readonly>
                                                        <span class="input-group-text">Bar</span>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="resolusi" class="form-label">Resolusi</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control text-muted" id="resolusi"
                                                            name="resolusi" readonly>
                                                        <span class="input-group-text">Bar</span>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label class="form-label">Range Penggunaan</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control text-muted"
                                                            id="min_range_use" name="min_range_use" placeholder="Min"
                                                            step="any" readonly>
                                                        <span class="input-group-text">–</span>
                                                        <input type="number" class="form-control text-muted"
                                                            id="max_range_use" name="max_range_use" placeholder="Max"
                                                            step="any" readonly>
                                                        <span class="input-group-text" id="unit_range">Bar</span>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="limits_error" class="form-label">Limits of Permissible
                                                        Error</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">&plusmn;</span>
                                                        <input type="text" class="form-control text-muted"
                                                            id="limits_error" name="limits_error" readonly>
                                                        <span class="input-group-text" id="unit_range">Bar</span>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="metode_kalibrasi" class="form-label">Metode
                                                        Kalibrasi</label>
                                                    <textarea class="form-control text-muted" name="metode_kalibrasi" id="metode_kalibrasi" cols="30"
                                                        rows="2" readonly></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section Data Kalibrasi -->
                                    <div class="card mb-3 border-success">

                                        <div class="card-header bg-success text-white py-2">
                                            Data Kalibrasi (Input User)
                                        </div>
                                        <div class="card-body">
                                            <div class="row gy-3">
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="lokasi_kalibrasi" class="form-label">Lokasi
                                                        Kalibrasi</label>
                                                    <input type="text" class="form-control" id="lokasi_kalibrasi"
                                                        name="lokasi_kalibrasi">
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="suhu_ruangan" class="form-label">Suhu Ruangan</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="suhu_ruangan"
                                                            name="suhu_ruangan" placeholder="25">
                                                        <span class="input-group-text">&plusmn;</span>
                                                        <input type="number" class="form-control" id="suhu_ruangan_tol"
                                                            name="suhu_ruangan_tol" placeholder="1">
                                                        <span class="input-group-text">°C</span>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="kelembaban" class="form-label">Kelembaban</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="kelembaban"
                                                            name="kelembaban" placeholder="45">
                                                        <span class="input-group-text">&plusmn;</span>
                                                        <input type="number" class="form-control" id="kelembaban_tol"
                                                            name="kelembaban_tol" placeholder="3">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>

                                                <div class="col-xxl-3 col-md-3">
                                                    <label for="tgl_kalibrasi" class="form-label">Tanggal
                                                        Kalibrasi</label>
                                                    <input type="date" class="form-control" id="tgl_kalibrasi"
                                                        name="tgl_kalibrasi">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>



                    {{-- Input data pressure --}}
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse" data-bs-target="#collapseDataPressure" style="cursor:pointer;">
                            <h4 class="card-title mb-0">Form Pengukuran Pressure</h4>
                            <i class="mdi mdi-chevron-up toggle-icon"></i>
                        </div>
                        <div id="collapseDataPressure" class="collapse show">
                            <div class="card-body">
                                <!-- Nav tabs -->
                                <ul class="nav nav-pills" id="kalibrasiTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link waves-effect waves-light active" data-bs-toggle="tab"
                                            href="#tekanan-naik">
                                            <i class="fas fa-arrow-up me-1"></i>Tekanan Naik
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link waves-effect waves-light" data-bs-toggle="tab"
                                            href="#tekanan-turun">
                                            <i class="fas fa-arrow-down me-1"></i>Tekanan Turun
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-4">
                                    <!-- Tekanan Naik -->
                                    <div class="tab-pane fade show active" id="tekanan-naik">
                                        <div class="col-xxl-3 col-md-3 mb-3">
                                            <label for="titik_naik" class="form-label">Jumlah Titik Kalibrasi</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="titik_naik"
                                                    min="1" max="10" value="1">
                                                <button type="button" class="btn btn-info btn-generate"
                                                    id="generateNaik">
                                                    <i class="mdi mdi-plus me-1"></i>Buat Titik
                                                </button>
                                            </div>
                                        </div>

                                        <div id="containerNaik"></div>
                                    </div>

                                    <!-- Tekanan Turun -->
                                    <div class="tab-pane fade" id="tekanan-turun">
                                        <div class="col-xxl-3 col-md-3 mb-3">
                                            <label for="titik_turun" class="form-label">Jumlah Titik Kalibrasi</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="titik_turun"
                                                    min="1" max="10" value="1">

                                                <button type="button" class="btn btn-info btn-generate"
                                                    id="generateTurun">
                                                    <i class="mdi mdi-plus me-1"></i>Buat Titik
                                                </button>
                                            </div>
                                        </div>
                                        <div id="containerTurun"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="gap-2">
                                    <button type="button" class="btn btn-outline-secondary"
                                        id="btnCancelKalibrasi">Cancel
                                    </button>
                                    <button type="submit" id="btnSimpanKalibrasi" class="btn btn-success">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal flow Information --}}
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">
                        <i class="mdi mdi-information-outline me-2"></i> Alur Pengisian Form Kalibrasi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item">Pilih <b>Kode Alat</b>, sistem akan otomatis mengisi informasi alat.
                        </li>
                        <li class="list-group-item">Isi <b>Lokasi Kalibrasi</b> sesuai tempat pelaksanaan.</li>
                        <li class="list-group-item">Masukkan <b>Suhu Ruangan</b> beserta toleransinya (±).</li>
                        <li class="list-group-item">Masukkan <b>Kelembaban</b> beserta toleransinya (±).</li>
                        <li class="list-group-item">Pilih <b>Tanggal Kalibrasi</b>.</li>
                        <li class="list-group-item">Klik <b>Simpan</b> untuk menyimpan data.</li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#alat_id').change(function() {
                var id = $(this).val();
                if (!id) return;

                $.get('/api/kalibrasi/pressure/data/alat/' + id, function(res) {
                    let data = res.data;
                    $('#departemen_pemilik').val(data.departemen_pemilik);
                    $('#lokasi_alat').val(data.lokasi_alat);
                    $('#no_kalibrasi').val(data.no_kalibrasi);
                    $('#nama_alat').val(data.nama_alat);
                    $('#merk').val(data.merk);
                    $('#tipe').val(data.tipe);
                    $('#kapasitas').val(data.kapasitas);
                    $('#resolusi').val(data.resolusi);
                    if (data.range_use) {
                        let parts = data.range_use.split('s/d');
                        let minVal = parts[0] ? parts[0].trim() : '';
                        let maxVal = parts[1] ? parts[1].trim() : '';
                        $('#min_range_use').val(minVal);
                        $('#max_range_use').val(maxVal);
                    }
                    $('#limits_error').val(data.limits_permissible_error);
                    $('#metode_kalibrasi').val(
                        `Diadopsi dari : "The Expression of Uncertainty and Confidence in Measurement" Oleh UKAS (United Kingdom Accreditation Service) M3003, Edition 3, November 2012`
                    );
                });
            });

            // generate data tekanan pressure
            generateTitikKalibrasi('containerNaik', 0);
            generateTitikKalibrasi('containerTurun', 0);

            function generateTitikKalibrasi(containerId, jumlah) {
                const $container = $('#' + containerId);
                $container.empty();

                if (jumlah === 0) return;

                for (let i = 1; i <= jumlah; i++) {
                    const titikBlock = `
                        <div class="titik-kalibrasi-block p-3 mb-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-primary">
                                    Titik Kalibrasi ${i}
                                    <span class="badge bg-primary ms-2" id="badge_${containerId}_${i}">0.0</span>
                                </h6>
                            </div>
                            ${generateMeasurementRows(containerId, i)}
                        </div>
                    `;
                    $container.append(titikBlock);

                    // binding event: ambil nilai awal penunjuk alat pertama
                    const $inputAlat1 = $(`#${containerId}_alat_${i}_1`);
                    const $badge = $(`#badge_${containerId}_${i}`);

                    // set nilai awal badge
                    $badge.text($inputAlat1.val() || "0.0");

                    // update badge setiap kali input berubah
                    $inputAlat1.on("input", function() {
                        $badge.text($(this).val() || "0.0");
                    });
                }
            }

            function generateMeasurementRows(containerId, titikNo) {
                let rows = `
                        <div class="row g-3 mb-3">
                            <!-- Penunjuk Standar -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold d-block">Penunjuk Standar</label>
                                <input type="number" class="form-control mb-2"
                                    id="${containerId}_standar_${titikNo}_1"
                                    name="${containerId}_standar_${titikNo}_1"
                                    step="0.001" placeholder="0.0">
                                <input type="number" class="form-control mb-2"
                                    id="${containerId}_standar_${titikNo}_2"
                                    name="${containerId}_standar_${titikNo}_2"
                                    step="0.001" placeholder="0.0">
                                <input type="number" class="form-control"
                                    id="${containerId}_standar_${titikNo}_3"
                                    name="${containerId}_standar_${titikNo}_3"
                                    step="0.001" placeholder="0.0">
                            </div>

                            <!-- Penunjuk Alat -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold d-block">Penunjuk Alat</label>
                                <input type="number" class="form-control mb-2"
                                    id="${containerId}_alat_${titikNo}_1"
                                    name="${containerId}_alat_${titikNo}_1"
                                    step="0.001" placeholder="0.0">
                                <input type="number" class="form-control mb-2"
                                    id="${containerId}_alat_${titikNo}_2"
                                    name="${containerId}_alat_${titikNo}_2"
                                    step="0.001" placeholder="0.0">
                                <input type="number" class="form-control"
                                    id="${containerId}_alat_${titikNo}_3"
                                    name="${containerId}_alat_${titikNo}_3"
                                    step="0.001" placeholder="0.0">
                            </div>
                        </div>
                    `;
                return rows;
            }

            // Event handlers tabs
            $('#generateNaik').click(function() {
                const jumlah = parseInt($('#titik_naik').val()) || 0;
                generateTitikKalibrasi('containerNaik', jumlah);
            });

            $('#generateTurun').click(function() {
                const jumlah = parseInt($('#titik_turun').val()) || 0;
                generateTitikKalibrasi('containerTurun', jumlah);
            });

            // simpan button
            $(document).on('click', '#btnSimpanKalibrasi', function(e) {
                e.preventDefault();

                let formData = $('#formKalibrasi').serializeArray();
                let data = {};

                formData.forEach(function(item) {
                    data[item.name] = item.value;
                });

                let isFormFilled = formData.some(item => {
                    if (item.name === '_token') return false;
                    return item.value && item.value.trim() !== '';
                });

                if (!isFormFilled) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Form is empty!',
                        text: 'Please fill in the calibration form first.'
                    });
                    return;
                }

                // Array gabungan pressure
                data.pressure = [];
                let adaNaik = false;
                let adaTurun = false;
                let naikCount = 0;
                let turunCount = 0;

                // Ambil data TEKANAN NAIK
                // Ambil data TEKANAN NAIK
                $('#containerNaik .titik-kalibrasi-block').each(function(i, block) {
                    for (let k = 1; k <= 3; k++) {
                        let standar = $(block).find(
                            `input[name="containerNaik_standar_${i+1}_${k}"]`).val();
                        let alat = $(block).find(
                            `input[name="containerNaik_alat_${i+1}_${k}"]`).val();

                        if (standar || alat) {
                            adaNaik = true;
                            naikCount++;

                            // jadikan nilai alat sebagai titik_kalibrasi
                            let titik = alat;

                            data.pressure.push({
                                titik_kalibrasi: titik,
                                tekanan: 'naik',
                                penunjuk_standar: standar,
                                penunjuk_alat: alat,
                                koreksi_standar: standar - alat
                            });
                        }
                    }
                });

                // Ambil data TEKANAN TURUN
                $('#containerTurun .titik-kalibrasi-block').each(function(i, block) {
                    for (let k = 1; k <= 3; k++) {
                        let standar = $(block).find(
                            `input[name="containerTurun_standar_${i+1}_${k}"]`).val();
                        let alat = $(block).find(
                            `input[name="containerTurun_alat_${i+1}_${k}"]`).val();

                        if (standar || alat) {
                            adaTurun = true;
                            turunCount++;

                            let titik = alat; // samakan titik dengan nilai alat

                            data.pressure.push({
                                titik_kalibrasi: titik,
                                tekanan: 'turun',
                                penunjuk_standar: standar,
                                penunjuk_alat: alat,
                                koreksi_standar: standar - alat
                            });
                        }
                    }
                });


                // Validasi isi
                if (!adaNaik || !adaTurun) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Incomplete data!',
                        text: 'Please fill in the calibration points for UP and DOWN pressure..'
                    });
                    return; // stop proses
                }

                // Validasi panjang data naik & turun harus sama
                if (naikCount !== turunCount) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Mismatch data!',
                        text: 'The number of calibration points for UP and DOWN must be the same.'
                    });
                    return;
                }

                $.ajax({
                    url: `{{ route('kalibrasi.pressure.store') }}`,
                    method: 'POST',
                    data: {
                        ...data,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Success', res.message, 'success');

                            $('#formKalibrasi')[0].reset();
                            $('#containerNaik').empty();
                            $('#containerTurun').empty();
                            $('#titikNaik').val('');
                            $('#titikTurun').val('');
                        } else {
                            Swal.fire('Failed', res.message || 'Something went wrong!',
                                'error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Ini validasi gagal
                            let errors = xhr.responseJSON.errors;
                            let msg = "";
                            Object.keys(errors).forEach(function(key) {
                                msg += errors[key][0] + "<br>";
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation failed!',
                                html: msg, // pake html biar <br> bisa kebaca
                            });
                        } else {
                            // Error selain validasi (500, 404, dll)
                            Swal.fire('Error', 'Server error occurred!', 'error');
                        }
                    }
                });
            });

            // Cancel button
            $(document).on('click', '#btnCancelKalibrasi', function() {
                // reset semua field
                $('#formKalibrasi')[0].reset();

                // kosongkan container titik
                $('#containerNaik').empty();
                $('#containerTurun').empty();

                // kalau ada select2 atau plugin lain, reset juga
                $('#alat_id').val('').trigger('change');
            });

            // saat dibuka
            $('.collapse').on('show.bs.collapse', function() {
                $(this).prev('.card-header').find('.toggle-icon')
                    .removeClass('mdi-chevron-down')
                    .addClass('mdi-chevron-up');
            });

            // saat ditutup
            $('.collapse').on('hide.bs.collapse', function() {
                $(this).prev('.card-header').find('.toggle-icon')
                    .removeClass('mdi-chevron-up')
                    .addClass('mdi-chevron-down');
            });

            // Info alert
            $('#infoBtn').on('click', function(e) {
                e.stopPropagation(); // biar ga ikut trigger collapse
                Swal.fire({
                    title: 'Alur Pengisian Form',
                    html: `
                        <style>
                            .swal2-popup ol {
                                padding-left: 25px;
                                margin: 0;
                                text-align: left;
                            }
                            .swal2-popup ol li {
                                margin-bottom: 6px;
                            }
                        </style>
                        <ol>
                            <li>Pilih <b>Kode Alat</b>, sistem akan auto-fill data alat.</li>
                            <li>Isi <b>Lokasi Kalibrasi</b> sesuai tempat.</li>
                            <li>Masukkan <b>Suhu Ruangan</b> & toleransinya (±).</li>
                            <li>Masukkan <b>Kelembaban</b> & toleransinya (±).</li>
                            <li>Pilih <b>Tanggal Kalibrasi</b>.</li>
                            <li>Isi pengukuran <b>Tekanan Naik & Tekanan Turun</b>.</li>
                            <li>Isi pengukuran <b>Jangan sampai ada yang kosong dan berbeda</b>.</li>
                            <li>Klik <b>Save</b> untuk menyimpan data.</li>
                        </ol>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        popup: 'rounded-4 shadow'
                    }
                });
            });
        })
    </script>
@endsection
