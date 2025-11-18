@extends('layouts.app')
@section('title', 'Form Timbangan')

@section('styles')
    <style>
        #collapseDetailAlat strong {
            color: #495057;
        }

        #collapseDetailAlat span {
            color: #0d6efd;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title d-sm-flex align-items-center justify-content-start">
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
                                    Form Kalibrasi Timbangan
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card-body">
                    <form id="formTimbangan" method="POST">
                        @csrf

                        {{-- === Data Utama Kalibrasi === --}}
                        <div class="card border border-primary border-opacity-50 mb-4">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-clipboard-list-outline me-2"></i>Data Utama Kalibrasi</strong>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 gy-3">
                                    <div class="col-lg-6 col-md-8 col-sm-12">
                                        <label for="alat_id" class="form-label">Pilih Alat</label>
                                        <div class="input-group">
                                            <select class="form-select" id="alat_id" name="alat_id" required>
                                                <option value="">-- Pilih Alat --</option>
                                                @foreach ($alat as $alat)
                                                    <option value="{{ $alat->id }}">{{ $alat->nama_alat }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" id="btnDetail" class="btn btn-outline-primary"
                                                data-bs-toggle="collapse" data-bs-target="#collapseAlatDetail" disabled>
                                                <i class="mdi mdi-information-outline me-2"></i>Detail
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-4 col-sm-12">
                                        <label class="form-label fw-semibold">Lokasi Kalibrasi</label>
                                        <input type="text" name="lokasi_kalibrasi" id="lokasi_kalibrasi"
                                            class="form-control" placeholder="Lab Kalibrasi 1" required>
                                    </div>

                                    <!-- DETAIL ALAT (COLLAPSE) -->
                                    <div class="collapse mb-3 mt-4" id="collapseAlatDetail">
                                        <div class="card border-info shadow-sm">
                                            <div class="card-header bg-soft-info">
                                                <strong>Detail Alat</strong>
                                            </div>
                                            <div class="card-body small">
                                                <div class="row g-2 gy-3">
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Departemen Pemilik:</strong> <span
                                                            id="departemen_pemilik">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Lokasi Alat:</strong> <span id="lokasi_alat">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>No. Kalibrasi:</strong> <span id="no_kalibrasi">-</span>
                                                    </div>

                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Nama Alat:</strong> <span id="nama_alat">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Merk:</strong> <span id="merk">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Tipe:</strong> <span id="tipe">-</span>
                                                    </div>

                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Kapasitas:</strong> <span id="kapasitas">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Resolusi:</strong> <span id="resolusi">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Range Penggunaan:</strong> <span
                                                            id="range_penggunaan_alat">-</span>
                                                    </div>

                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Limits of Permissible Error:</strong> <span
                                                            id="limits_of_permissible_error">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Metode Kalibrasi:</strong> <span
                                                            id="metode_kalibrasi">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-4 col-12">
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
                                    <div class="col-md-4 col-12">
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
                                    <div class="col-md-4 col-sm-12">
                                        <label class="form-label fw-semibold">Tanggal Kalibrasi</label>
                                        <input type="date" name="tgl_kalibrasi" id="tgl_kalibrasi"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- === Input Jumlah Baris === --}}
                        <div class="card border border-primary border-opacity-50 mb-4">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-scale-balance me-2"></i>Form Pengukuran Kalibrasi
                                    Timbangan</strong>
                            </div>

                            <div class="card-body">
                                <div class="accordion" id="accordionKalibrasi">

                                    {{-- === Bagian 1: Pembacaan === --}}
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingPembacaan">
                                            <button class="accordion-button fw-semibold collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapsePembacaan"
                                                aria-expanded="false" aria-controls="collapsePembacaan">
                                                <i class="mdi mdi-beaker-outline me-2 text-primary"></i>
                                                Kemampuan Ulang Pembacaan
                                            </button>
                                        </h2>
                                        <div id="collapsePembacaan" class="accordion-collapse collapse"
                                            aria-labelledby="headingPembacaan" data-bs-parent="#accordionKalibrasi">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    @foreach (['Mendekati Nol', 'Setengah Kapasitas Maksimum', 'Kapasitas Maksimum'] as $kemampuan)
                                                        <div class="col-md-6 mb-4">
                                                            <div class="border rounded p-3 h-100">
                                                                <h5 class="mb-3 text-primary text-center fs-6">Kemampuan
                                                                    {{ $kemampuan }}</h5>

                                                                <div
                                                                    class="d-flex justify-content-center align-items-center mb-3">
                                                                    <label for="titik_{{ Str::slug($kemampuan) }}"
                                                                        class="form-label fw-bold me-3 mb-0">
                                                                        Titik Massa:
                                                                    </label>
                                                                    <input type="number"
                                                                        class="form-control form-control-sm w-50"
                                                                        id="titik_{{ Str::slug($kemampuan) }}"
                                                                        name="titik_massa_{{ Str::slug($kemampuan) }}"
                                                                        placeholder="Masukkan massa">
                                                                </div>

                                                                <div class="table-responsive">
                                                                    <table
                                                                        class="table table-sm table-bordered align-middle text-center mb-0">
                                                                        <thead class="table-secondary"
                                                                            style="font-size: 11px;">
                                                                            <tr>
                                                                                <th style="width: 60px;">Ulangan</th>
                                                                                <th>Pembacaan Z</th>
                                                                                <th>Pembacaan M</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @for ($i = 1; $i <= 10; $i++)
                                                                                <tr>
                                                                                    <td class="form-control-sm">
                                                                                        {{ $i }}</td>
                                                                                    <td>
                                                                                        <input type="number"
                                                                                            step="0.0001"
                                                                                            class="form-control form-control-sm text-center bg-light"
                                                                                            name="pembacaan_z_{{ Str::slug($kemampuan) }}_{{ $i }}">
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="number"
                                                                                            step="0.0001"
                                                                                            class="form-control form-control-sm text-center bg-light"
                                                                                            name="pembacaan_m_{{ Str::slug($kemampuan) }}_{{ $i }}">
                                                                                    </td>
                                                                                </tr>
                                                                            @endfor
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- === Bagian 2: KeseragamanSkala === --}}
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingKeseragamanSkala">
                                            <button class="accordion-button fw-semibold collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseKeseragamanSkala"
                                                aria-expanded="false" aria-controls="collapseKeseragamanSkala">
                                                <i class="mdi mdi-sigma me-2 text-info"></i>
                                                Keseragaman Skala
                                            </button>
                                        </h2>
                                        <div id="collapseKeseragamanSkala" class="accordion-collapse collapse"
                                            aria-labelledby="headingKeseragamanSkala"
                                            data-bs-parent="#accordionKalibrasi">
                                            <div class="accordion-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Massa Pengkalibrasi
                                                            (g)</label>
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="massa_pengkalibrasi"
                                                            placeholder="Masukkan massa pengkalibrasi">
                                                    </div>
                                                </div>

                                                <hr>

                                                <div id="keseragaman-container">
                                                    <!-- Set Keseragaman akan ditambahkan dinamis di sini -->
                                                </div>

                                                <div class="mt-3">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        id="addKeseragaman">
                                                        + Tambah Massa (g)
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    {{-- === Bagian 3: Pinggan === --}}
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingPinggan">
                                            <button class="accordion-button fw-semibold collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapsePinggan"
                                                aria-expanded="false" aria-controls="collapsePinggan">
                                                <i class="mdi mdi-circle-multiple-outline me-2 text-success"></i>
                                                Pengaruh Penyimpanan Pada Pinggan
                                            </button>
                                        </h2>
                                        <div id="collapsePinggan" class="accordion-collapse collapse"
                                            aria-labelledby="headingPinggan" data-bs-parent="#accordionKalibrasi">
                                            <div class="accordion-body">
                                                <div class="mb-3 row">
                                                    <div class="col-md-6">
                                                        <label for="diameter_pinggan" class="form-label fw-semibold">
                                                            Diameter Pinggan (cm)
                                                        </label>
                                                        <input type="number" step="0.01" class="form-control"
                                                            id="diameter_pinggan" name="pinggan[diameter]"
                                                            placeholder="Masukkan diameter pinggan">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="massa_pinggan" class="form-label fw-semibold">
                                                            Massa Pinggan (g)
                                                        </label>
                                                        <input type="number" step="0.0001" class="form-control"
                                                            id="massa_pinggan" name="pinggan[massa]"
                                                            placeholder="Masukkan massa pinggan">
                                                    </div>
                                                </div>

                                                {{-- Tabel Percobaan --}}
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle text-center">
                                                        <thead>
                                                            <tr>
                                                                <th rowspan="2" style="width: 80px;">
                                                                    Percobaan</th>
                                                                <th colspan="5">Sisi Pengukuran</th>
                                                            </tr>
                                                            <tr>
                                                                <th>Tengah</th>
                                                                <th>Depan</th>
                                                                <th>Belakang</th>
                                                                <th>Kiri</th>
                                                                <th>Kanan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @for ($i = 1; $i <= 3; $i++)
                                                                <tr class="pinggan-percobaan">
                                                                    <td><strong>{{ $i }}</strong>
                                                                    </td>
                                                                    @foreach (['tengah', 'depan', 'belakang', 'kiri', 'kanan'] as $sisi)
                                                                        <td>
                                                                            <input type="number" step="0.0001"
                                                                                class="form-control form-control-sm text-center bg-light sisi-{{ $sisi }}"
                                                                                placeholder="0.0000">
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endfor
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- === Bagian 4: Tare === --}}
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingTare">
                                            <button class="accordion-button fw-semibold collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseTare"
                                                aria-expanded="false" aria-controls="collapseTare">
                                                <i class="mdi mdi-arrow-decision-outline me-2 text-warning"></i>
                                                Pengaruh Pengenolan Beban (Tare)
                                            </button>
                                        </h2>
                                        <div id="collapseTare" class="accordion-collapse collapse"
                                            aria-labelledby="headingTare" data-bs-parent="#accordionKalibrasi">
                                            <div class="accordion-body">{{-- === Input Pengenolan (Tare) === --}}
                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">
                                                            Massa (gram)
                                                        </label>
                                                        <input type="number" step="0.0001" id="massa_tare"
                                                            class="form-control form-control-sm bg-light"
                                                            placeholder="Masukkan massa diatas pinggan...">
                                                    </div>
                                                </div>

                                                {{-- === Tabel Tanpa & Dengan Pengenolan === --}}
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle text-center">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th colspan="2" class="text-danger">Tanpa Pengenolan
                                                                </th>
                                                                <th colspan="2" class="text-success">Memakai Pengenolan
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th>Beban (g)</th>
                                                                <th>Pembacaan (g)</th>
                                                                <th>Beban (g)</th>
                                                                <th>Pembacaan (g)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tare_table_body">
                                                            @php
                                                                $rows = [
                                                                    [
                                                                        'beban_tanpa' => [
                                                                            'label' => 'Z',
                                                                            'value' => 'Z1',
                                                                        ],
                                                                        'beban_tare' => [
                                                                            'label' => 'Z',
                                                                            'value' => 'Z1',
                                                                        ],
                                                                    ],
                                                                    [
                                                                        'beban_tanpa' => [
                                                                            'label' => 'M',
                                                                            'value' => 'M1',
                                                                        ],
                                                                        'beban_tare' => [
                                                                            'label' => 'M',
                                                                            'value' => 'M1',
                                                                        ],
                                                                    ],
                                                                    [
                                                                        'beban_tanpa' => [
                                                                            'label' => 'M',
                                                                            'value' => 'M2',
                                                                        ],
                                                                        'beban_tare' => [
                                                                            'label' => 'M',
                                                                            'value' => 'M2',
                                                                        ],
                                                                    ],
                                                                    [
                                                                        'beban_tanpa' => [
                                                                            'label' => 'Z',
                                                                            'value' => 'Z2',
                                                                        ],
                                                                        'beban_tare' => [
                                                                            'label' => 'Z',
                                                                            'value' => 'Z2',
                                                                        ],
                                                                    ],
                                                                ];
                                                            @endphp

                                                            @foreach ($rows as $row)
                                                                <tr class="tare-row">
                                                                    <td>
                                                                        <strong
                                                                            data-value="{{ $row['beban_tanpa']['value'] }}">
                                                                            {{ $row['beban_tanpa']['label'] }}
                                                                        </strong>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" step="0.0001"
                                                                            class="form-control form-control-sm text-center bg-light tare-tanpa"
                                                                            placeholder="0.0000">
                                                                    </td>
                                                                    <td>
                                                                        <strong
                                                                            data-value="{{ $row['beban_tare']['value'] }}">
                                                                            {{ $row['beban_tare']['label'] }}
                                                                        </strong>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" step="0.0001"
                                                                            class="form-control form-control-sm text-center bg-light tare-dengan"
                                                                            placeholder="0.0000">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- === Bagian 5: Histerisis === --}}
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingHisterisis">
                                            <button class="accordion-button fw-semibold collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseHisterisis"
                                                aria-expanded="false" aria-controls="collapseHisterisis">
                                                <i class="mdi mdi-chart-bell-curve-cumulative me-2 text-danger"></i>
                                                Histerisis
                                            </button>
                                        </h2>
                                        <div id="collapseHisterisis" class="accordion-collapse collapse"
                                            aria-labelledby="headingHisterisis" data-bs-parent="#accordionKalibrasi">
                                            <div class="accordion-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="massa_terkecil" class="form-label fw-semibold">Massa
                                                            Terkecil</label>
                                                        <input type="number" step="0.0001" id="massa_terkecil"
                                                            class="form-control form-control-sm bg-light"
                                                            placeholder="Masukkan massa terkecil">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="massa_setengah" class="form-label fw-semibold">Massa
                                                            Setengah Kapasitas</label>
                                                        <input type="number" step="0.0001" id="massa_setengah"
                                                            class="form-control form-control-sm bg-light"
                                                            placeholder="Masukkan massa setengah kapasitas">
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered align-middle text-center">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th rowspan="2" class="align-middle">Beban</th>
                                                                <th colspan="3">Percobaan</th>
                                                            </tr>
                                                            <tr>
                                                                <th>1</th>
                                                                <th>2</th>
                                                                <th>3</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="histerisis_table_body">
                                                            @php
                                                                $rows = ['Z', 'M', 'M+M', 'M', 'Z'];
                                                            @endphp
                                                            @foreach ($rows as $index => $beban)
                                                                <tr data-beban="{{ $beban }}_{{ $index + 1 }}">
                                                                    <td><strong>{{ $beban }}</strong></td>
                                                                    @for ($i = 1; $i <= 3; $i++)
                                                                        <td>
                                                                            <input type="number" step="0.0001"
                                                                                class="form-control form-control-sm text-center bg-light histerisis-input"
                                                                                placeholder="0.0000">
                                                                        </td>
                                                                    @endfor
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-start mt-4">
                                    <div class="d-flex flex-wrap gap-2 justify-content-start">
                                        <button type="button" class="btn btn-outline-danger rounded-pill px-4"
                                            id="btnReset">
                                            <i class="mdi mdi-close-circle-outline me-1"></i> Reset
                                        </button>
                                        {{-- 
                                        <button type="button" id="btnPreview"
                                            class="btn btn-outline-info rounded-pill px-4">
                                            <i class="mdi mdi-eye-outline me-1"></i> Preview
                                        </button> --}}

                                        <button type="submit" class="btn btn-success rounded-pill px-4">
                                            <i class="mdi mdi-content-save me-1"></i> Submit Kalibrasi
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
            $('#alat_id').change(function() {
                var id = $(this).val();
                if (!id) {
                    $('#btnDetail').prop('disabled', true);
                    return;
                }

                $.get('/api/kalibrasi/pressure/data/alat/' + id, function(res) {
                    let data = res.data;

                    // Aktifkan tombol "Lihat Detail"
                    $('#btnDetail').prop('disabled', false);

                    // Isi data ke span (bukan input)
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

            $('#addKeseragaman').on('click', function() {
                const index = $('.keseragaman-set').length + 1;

                const template = `
                    <div class="keseragaman-set border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Set Massa #${index}</h6>
                            <button type="button" class="btn btn-sm btn-danger remove-set">Hapus</button>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label class="form-label">Massa (g)</label>
                                <input type="number" step="0.0001" class="form-control form-control-sm massa-keseragaman" placeholder="5000">
                            </div>
                        </div>

                        <table class="table table-sm table-bordered text-center align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Beban Timbangan</th>
                                    <th>Pembacaan Skala</th>
                                </tr>
                            </thead>
                            <tbody class="keseragaman-body">
                                <tr>
                                    <td data-beban="Z">Z</td>
                                    <td><input type="number" step="0.0001" class="form-control form-control-sm pembacaan-skala" placeholder="0.0000"></td>
                                </tr>
                                <tr>
                                    <td data-beban="M1">M</td>
                                    <td><input type="number" step="0.0001" class="form-control form-control-sm pembacaan-skala" placeholder="0.0000"></td>
                                </tr>
                                <tr>
                                    <td data-beban="M2">M</td>
                                    <td><input type="number" step="0.0001" class="form-control form-control-sm pembacaan-skala" placeholder="0.0000"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                `;

                $('#keseragaman-container').append(template);
            });

            // Hapus set keseragaman
            $(document).on('click', '.remove-set', function() {
                $(this).closest('.keseragaman-set').remove();
            });

            $('#formTimbangan').on('submit', function(e) {
                e.preventDefault();
                let formData = {};

                // === 1. Format suhu & kelembaban ===
                const suhu = $('#suhu_ruangan').val();
                const toleransiSuhu = $('#toleransi_suhu').val();
                const kelembaban = $('#kelembaban').val();
                const toleransiKelembaban = $('#toleransi_kelembaban').val();

                const suhuFormatted = suhu && toleransiSuhu ?
                    `${suhu}°C ± ${toleransiSuhu}°C` :
                    suhu ? `${suhu}°C` : '';

                const kelembabanFormatted = kelembaban && toleransiKelembaban ?
                    `${kelembaban}% ± ${toleransiKelembaban}%` :
                    kelembaban ? `${kelembaban}%` : '';

                $('#suhu_ruangan_final').val(suhuFormatted);
                $('#kelembaban_final').val(kelembabanFormatted);

                // 2 kemampuan pembacaan
                let pembacaan = {};
                ['Mendekati Nol', 'Setengah Kapasitas Maksimum', 'Kapasitas Maksimum'].forEach(function(
                    kemampuan) {
                    const slug = kemampuan.toLowerCase().replace(/\s+/g,
                        '-'); // slugify untuk cari input sesuai nama

                    // Ambil nilai titik massa
                    const titikMassa = $(`#titik_${slug}`).val();

                    // Siapkan array percobaan (ulangan)
                    let percobaanList = [];

                    for (let i = 1; i <= 10; i++) {
                        const z = $(`input[name="pembacaan_z_${slug}_${i}"]`).val();
                        const m = $(`input[name="pembacaan_m_${slug}_${i}"]`).val();

                        // Skip jika kosong semua
                        if (z === '' && m === '') continue;

                        percobaanList.push({
                            ulangan_ke: i,
                            pembacaan_z: z || null,
                            pembacaan_m: m || null,
                        });
                    }

                    // Masukkan ke pembacaan jika ada data
                    pembacaan[kemampuan] = [{
                        titik: titikMassa || null,
                        percobaan: percobaanList
                    }];
                });

                // === 3. Data pinggan ===
                const diameter = parseFloat($('#diameter_pinggan').val()) || null;
                const massa = parseFloat($('#massa_pinggan').val()) || null;
                const percobaanPinggan = [];

                $('.pinggan-percobaan').each(function(index) {
                    percobaanPinggan.push({
                        percobaan_ke: index + 1,
                        tengah: parseFloat($(this).find('.sisi-tengah').val()) || null,
                        depan: parseFloat($(this).find('.sisi-depan').val()) || null,
                        belakang: parseFloat($(this).find('.sisi-belakang').val()) || null,
                        kiri: parseFloat($(this).find('.sisi-kiri').val()) || null,
                        kanan: parseFloat($(this).find('.sisi-kanan').val()) || null,
                    });
                });

                // === 4. Data pengenolan tare ===
                const massaTare = parseFloat($('#massa_tare').val()) || null;
                const dataTare = [];

                $('#tare_table_body tr').each(function(index) {
                    dataTare.push({
                        beban_tanpa: $(this).find('td:eq(0) strong').data('value'),
                        pembacaan_tanpa: parseFloat($(this).find('.tare-tanpa').val()) ||
                            null,
                        beban_dengan: $(this).find('td:eq(0) strong').data('value'),
                        pembacaan_dengan: parseFloat($(this).find('.tare-dengan').val()) ||
                            null,
                    });
                });

                // === 5. Data Histerisis ===
                const massaTerkecil = parseFloat($('#massa_terkecil').val()) || null;
                const massaSetengah = parseFloat($('#massa_setengah').val()) || null;
                const dataHisterisis = [];

                $('#histerisis_table_body tr').each(function() {
                    const percobaan = [];
                    $(this).find('input').each(function() {
                        percobaan.push(parseFloat($(this).val()) || null);
                    });

                    dataHisterisis.push({
                        massa_terkecil: massaTerkecil,
                        massa_setengah: massaSetengah,
                        beban: $(this).data('beban'),
                        percobaan: percobaan
                    });
                });

                const massaPengkalibrasi = parseFloat($('#massa_pengkalibrasi').val()) || null;
                const dataKeseragaman = [];

                $('.keseragaman-set').each(function() {
                    const massa = parseFloat($(this).find('.massa-keseragaman').val()) || null;
                    const bebanList = [];
                    const pembacaanList = [];

                    $(this).find('.keseragaman-body tr').each(function() {
                        const beban = $(this).find('td').eq(0).data('beban');
                        const pembacaan = parseFloat($(this).find('.pembacaan-skala')
                            .val()) || null;
                        bebanList.push(beban);
                        pembacaanList.push(pembacaan);
                    });

                    dataKeseragaman.push({
                        massa_pengkalibrasi: massaPengkalibrasi,
                        massa: massa,
                        beban_timbangan: bebanList,
                        pembacaan_skala: pembacaanList
                    });
                });

                // === 4. Siapkan form data utama ===
                formData = {
                    alat_id: $('#alat_id').val(),
                    lokasi_kalibrasi: $('#lokasi_kalibrasi').val(),
                    suhu_ruangan_final: $('#suhu_ruangan_final').val(),
                    kelembaban_final: $('#kelembaban_final').val(),
                    tgl_kalibrasi: $('#tgl_kalibrasi').val(),
                    pembacaan: pembacaan,
                    pinggan: {
                        diameter: diameter,
                        massa: massa,
                        percobaan: percobaanPinggan
                    },
                    tare: {
                        massa: massaTare,
                        percobaan: dataTare
                    },
                    histerisis: dataHisterisis,
                    keseragaman_skala: dataKeseragaman
                };

                $.ajax({
                    url: "{{ route('kalibrasi.timbangan.store') }}",
                    method: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data pembacaan berhasil disimpan!',
                            showConfirmButton: false,
                            timer: 2000
                        });

                        $('#formTimbangan')[0].reset();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal menyimpan data, silakan coba lagi.',
                        });
                    }
                });
            });

            // reset button
            $(document).on('click', '#btnReset', function() {
                $('#formTimbangan')[0].reset();
                $('#alat_id').val('').trigger('change');

                $('#collapseAlatDetail').collapse('hide');
            });
        });
    </script>
@endsection
