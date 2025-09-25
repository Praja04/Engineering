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
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Kalibrasi</a></li>
                                <li class="breadcrumb-item">Pressure</li>
                                <li class="breadcrumb-item active">Form Pressure</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    {{-- Input data header --}}
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse"
                            data-bs-target="#collapseFormPressure" style="cursor:pointer;">
                            <h4 class="card-title mb-0">Form Pressure Information</h4>
                            <i class="mdi mdi-chevron-up toggle-icon"></i>
                        </div>
                        <div id="collapseFormPressure" class="collapse show">
                            <div class="card-body">
                                <form id="formKalibrasi">
                                    <div class="row gy-3">
                                        <!-- Pilih Alat -->
                                        <div class="col-xxl-3 col-md-3">
                                            <label for="alat_id" class="form-label">Pilih Kode Alat</label>
                                            <select class="form-select" id="alat_id" name="alat_id">
                                                <option value="">-- Pilih Kode Alat --</option>
                                                @foreach ($alat as $a)
                                                    <option value="{{ $a->id }}">{{ $a->kode_alat }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Info Alat (readonly, auto fill) -->
                                        <div class="col-xxl-3 col-md-3">
                                            <label for="departemen_pemilik" class="form-label">Departemen
                                                Pemilik</label>
                                            <input type="text" class="form-control text-muted" id="departemen_pemilik"
                                                name="departemen_pemilik" readonly>
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
                                            <input type="text" class="form-control text-muted" id="kapasitas"
                                                name="kapasitas" readonly>
                                        </div>
                                        <div class="col-xxl-3 col-md-3">
                                            <label for="resolusi" class="form-label">Resolusi</label>
                                            <input type="text" class="form-control text-muted" id="resolusi"
                                                name="resolusi" readonly>
                                        </div>
                                        <div class="col-xxl-3 col-md-3">
                                            <label for="range_penggunaan" class="form-label">Range Penggunaan
                                                Alat</label>
                                            <input type="text" class="form-control text-muted" id="range_penggunaan"
                                                name="range_penggunaan" readonly>
                                        </div>
                                        <div class="col-xxl-3 col-md-3">
                                            <label for="limits_error" class="form-label">Limits of Permissible
                                                Error</label>
                                            <input type="text" class="form-control text-muted" id="limits_error"
                                                name="limits_error" readonly>
                                        </div>

                                        <!-- Data Kalibrasi (user input) -->
                                        <div class="col-xxl-3 col-md-3">
                                            <label for="lokasi_kalibrasi" class="form-label">Lokasi Kalibrasi</label>
                                            <input type="text" class="form-control" id="lokasi_kalibrasi"
                                                name="lokasi_kalibrasi">
                                        </div>
                                        <div class="col-xxl-3 col-md-3">
                                            <label for="suhu_ruangan" class="form-label">Suhu Ruangan</label>
                                            <input type="text" class="form-control" id="suhu_ruangan"
                                                name="suhu_ruangan">
                                        </div>
                                        <div class="col-xxl-3 col-md-3">
                                            <label for="kelembaban" class="form-label">Kelembaban</label>
                                            <input type="text" class="form-control" id="kelembaban"
                                                name="kelembaban">
                                        </div>
                                        <div class="col-xxl-3 col-md-3">
                                            <label for="tgl_kalibrasi" class="form-label">Tanggal Kalibrasi</label>
                                            <input type="date" class="form-control" id="tgl_kalibrasi"
                                                name="tgl_kalibrasi">
                                        </div>
                                        <div class="col-xxl-3 col-md-3">
                                            <label for="tgl_kalibrasi_ulang" class="form-label">Tanggal Kalibrasi
                                                Ulang</label>
                                            <input type="date" class="form-control" id="tgl_kalibrasi_ulang"
                                                name="tgl_kalibrasi_ulang">
                                        </div>
                                        <div class="col-xxl-6 col-md-6">
                                            <label for="metode_kalibrasi" class="form-label">Metode Kalibrasi</label>
                                            <textarea class="form-control text-muted" name="metode_kalibrasi" id="metode_kalibrasi" cols="30"
                                                rows="3" readonly></textarea>
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
                            <h4 class="card-title mb-0">Form Pressure Hasil</h4>
                            <i class="mdi mdi-chevron-up toggle-icon"></i>
                        </div>
                        <div id="collapseDataPressure" class="collapse show">
                            <div class="card-body">
                                <!-- Nav tabs -->
                                <ul class="nav nav-pills" id="kalibrasiTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#tekanan-naik">
                                            <i class="fas fa-arrow-up me-1"></i>Tekanan Naik
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#tekanan-turun">
                                            <i class="fas fa-arrow-down me-1"></i>Tekanan Turun
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-4">
                                    <!-- Tekanan Naik -->
                                    <div class="tab-pane fade show active" id="tekanan-naik">
                                        <div class="row mb-3 align-items-end">
                                            <div class="col-md-3">
                                                <label for="titik_naik" class="form-label">Jumlah Titik Kalibrasi</label>
                                                <input type="number" class="form-control" id="titik_naik"
                                                    min="1" max="10" value="1">
                                            </div>
                                            <div class="col-md-3">
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
                                        <div class="row mb-3 align-items-end">
                                            <div class="col-md-3">
                                                <label for="titik_turun" class="form-label">Jumlah Titik Kalibrasi</label>
                                                <input type="number" class="form-control" id="titik_turun"
                                                    min="1" max="10" value="1">
                                            </div>
                                            <div class="col-md-3">
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
                                    <button type="button" class="btn btn-outline-dark" id="btnCancelKalibrasi">Batal
                                    </button>
                                    <button type="submit" id="btnSimpanKalibrasi" class="btn btn-success">
                                        Simpan Data Kalibrasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    $('#range_penggunaan').val(data.range_penggunaan);
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
                            <div class="titik-kalibrasi-block p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 text-primary">Titik Kalibrasi ${i}</h6>
                                </div>
                                ${generateMeasurementRows(containerId, i)}
                            </div>
                        `;
                    $container.append(titikBlock);
                }
            }

            function generateMeasurementRows(containerId, titikNo) {
                let headerRow = `
                        <div class="row g-3 mb-2">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Penunjuk Standar</label>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Penunjuk Alat</label>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Koreksi Standar</label>
                            </div>
                        </div>
                    `;

                let rows = headerRow;
                for (let j = 1; j <= 3; j++) {
                    rows += `
                            <div class="row g-3 mb-2">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" 
                                            id="${containerId}_standar_${titikNo}_${j}"
                                            name="${containerId}_standar_${titikNo}_${j}"
                                            step="0.001" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" 
                                            id="${containerId}_alat_${titikNo}_${j}"
                                            name="${containerId}_alat_${titikNo}_${j}"
                                            step="0.001" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" 
                                            id="${containerId}_koreksi_${titikNo}_${j}"
                                            name="${containerId}_koreksi_${titikNo}_${j}"
                                            step="any" placeholder="0.0" readonly>
                                    </div>
                                </div>
                            </div>
                        `;
                }
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

            // Auto calculate correction
            $(document).on('input', 'input[name*="_standar_"], input[name*="_alat_"]', function() {
                const $this = $(this);
                const name = $this.attr('name');
                const parts = name.split('_');

                if (parts.length >= 4) {
                    const container = parts[0];
                    const titik = parts[2];
                    const measurement = parts[3];

                    const standarVal = parseFloat($(`#${container}_standar_${titik}_${measurement}`)
                        .val()) || 0;
                    const alatVal = parseFloat($(`#${container}_alat_${titik}_${measurement}`).val()) || 0;
                    const koreksi = standarVal - alatVal;

                    $(`#${container}_koreksi_${titik}_${measurement}`).val(koreksi.toFixed(3));
                }
            });

            // simpan button
            $(document).on('click', '#btnSimpanKalibrasi', function(e) {
                e.preventDefault();

                let formData = $('#formKalibrasi').serializeArray();
                let data = {};

                formData.forEach(function(item) {
                    data[item.name] = item.value;
                });

                // Array gabungan pressure
                data.pressure = [];

                // Ambil data TEKANAN NAIK
                $('#containerNaik .titik-kalibrasi-block').each(function(i, block) {
                    let titikNo = i + 1;
                    $(block).find('.row.g-3.mb-2').each(function(j, row) {
                        let standar = $(row).find('input[name*="_standar_"]').val();
                        let alat = $(row).find('input[name*="_alat_"]').val();
                        let koreksi = $(row).find('input[name*="_koreksi_"]').val();

                        if (standar || alat || koreksi) {
                            data.pressure.push({
                                titik_kalibrasi: titikNo,
                                tekanan: 'naik',
                                penunjuk_standar: standar,
                                penunjuk_alat: alat,
                                koreksi_standar: koreksi
                            });
                        }
                    });
                });

                // Ambil data TEKANAN TURUN
                $('#containerTurun .titik-kalibrasi-block').each(function(i, block) {
                    let titikNo = i + 1;
                    $(block).find('.row.g-3.mb-2').each(function(j, row) {
                        let standar = $(row).find('input[name*="_standar_"]').val();
                        let alat = $(row).find('input[name*="_alat_"]').val();
                        let koreksi = $(row).find('input[name*="_koreksi_"]').val();

                        if (standar || alat || koreksi) {
                            data.pressure.push({
                                titik_kalibrasi: titikNo,
                                tekanan: 'turun',
                                penunjuk_standar: standar,
                                penunjuk_alat: alat,
                                koreksi_standar: koreksi
                            });
                        }
                    });
                });

                // console.log("DATA KIRIM:", data); // cek dulu di console

                $.ajax({
                    url: `{{ url('api/kalibrasi/pressure/store') }}`,
                    method: 'POST',
                    data: data,
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Berhasil', 'Data kalibrasi berhasil disimpan!',
                                'success');

                            $('#formKalibrasi')[0].reset();
                            $('#containerNaik').empty();
                            $('#containerTurun').empty();
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan!', 'error');
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
                                title: 'Validasi gagal!',
                                html: msg, // pake html biar <br> bisa kebaca
                            });
                        } else {
                            // Error selain validasi (500, 404, dll)
                            Swal.fire('Error', 'Terjadi kesalahan server!', 'error');
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
        })
    </script>
@endsection
