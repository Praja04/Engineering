@extends('layouts.app')

@section('title', 'Form Thermohygrometer')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title d-sm-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('kalibrasi.form.dashboard') }}"
                                class="btn btn-outline-primary rounded-pill px-4 d-flex align-items-center">
                                <i class="mdi mdi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-soft-primary py-3">
                    <h5 class="mb-0"><i class="mdi mdi-thermometer me-2"></i>Form Kalibrasi Thermohygrometer</h5>
                </div>

                <div class="card-body">
                    <form id="formKalibrasiThermo" method="POST">
                        @csrf

                        {{-- === Data Utama === --}}
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
                                    <div class="col-md-6">
                                        <label for="lokasi_kalibrasi" class="form-label">Lokasi Kalibrasi</label>
                                        <input type="text" class="form-control" id="lokasi_kalibrasi"
                                            name="lokasi_kalibrasi" placeholder="Laboratorium Kalibrasi" required>
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

                                    <div class="col-md-4">
                                        <label for="tgl_kalibrasi" class="form-label">Tanggal Kalibrasi</label>
                                        <input type="date" class="form-control" id="tgl_kalibrasi"
                                            name="tgl_kalibrasi" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- === Data Pengukuran === --}}
                        <div class="card border border-primary border-opacity-50">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-thermometer-lines me-2"></i>Data Pengukuran</strong>
                            </div>
                            <div class="card-body">

                                <ul class="nav nav-pills" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-suhu">
                                            <i class="mdi mdi-thermometer me-1"></i>Suhu (°C)
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#tab-rh">
                                            <i class="mdi mdi-water-percent me-1"></i>Kelembaban (RH)
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-4">
                                    {{-- Tab SUHU --}}
                                    <div class="tab-pane fade show active" id="tab-suhu">
                                        <table class="table table-bordered align-middle text-center">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Posisi Bagian</th>
                                                    <th>Penunjuk Standar</th>
                                                    <th>Penunjuk Alat</th>
                                                    <th>Koreksi Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (['kanan depan', 'kiri depan', 'kanan belakang', 'kiri belakang', 'tengah'] as $pIndex => $pos)
                                                    @for ($i = 1; $i <= 3; $i++)
                                                        <tr>
                                                            @if ($i === 1)
                                                                {{-- merge 3 row untuk posisi --}}
                                                                <td rowspan="3"><strong>{{ ucfirst($pos) }}</strong>
                                                                </td>
                                                            @endif
                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="thermo[suhu][{{ $pIndex }}][{{ $i }}][penunjuk_standar]"
                                                                    class="form-control bg-light" placeholder="0.0"
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="thermo[suhu][{{ $pIndex }}][{{ $i }}][penunjuk_alat]"
                                                                    class="form-control bg-light" placeholder="0.0"
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="thermo[suhu][{{ $pIndex }}][{{ $i }}][koreksi_standar]"
                                                                    class="form-control bg-light" placeholder="0.0">
                                                            </td>
                                                            <input type="hidden"
                                                                name="thermo[suhu][{{ $pIndex }}][{{ $i }}][posisi]"
                                                                value="{{ $pos }}">
                                                            <input type="hidden"
                                                                name="thermo[suhu][{{ $pIndex }}][{{ $i }}][tipe_hitung]"
                                                                value="suhu">
                                                        </tr>
                                                    @endfor
                                                    {{-- Tambahkan garis/pemisah visual setelah 3 baris --}}
                                                    <tr class="table-primary">
                                                        <td colspan="4"></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Tab RH --}}
                                    <div class="tab-pane fade" id="tab-rh">
                                        <table class="table table-bordered align-middle text-center">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Posisi Bagian</th>
                                                    <th>Penunjuk Standar</th>
                                                    <th>Penunjuk Alat</th>
                                                    <th>Koreksi Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (['kanan depan', 'kiri depan', 'kanan belakang', 'kiri belakang', 'tengah'] as $pIndex => $pos)
                                                    @for ($i = 1; $i <= 3; $i++)
                                                        <tr>
                                                            @if ($i === 1)
                                                                <td rowspan="3"><strong>{{ ucfirst($pos) }}</strong>
                                                                </td>
                                                            @endif
                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="thermo[rh][{{ $pIndex }}][{{ $i }}][penunjuk_standar]"
                                                                    class="form-control bg-light" placeholder="0.0"
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="thermo[rh][{{ $pIndex }}][{{ $i }}][penunjuk_alat]"
                                                                    class="form-control bg-light" placeholder="0.0"
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="thermo[rh][{{ $pIndex }}][{{ $i }}][koreksi_standar]"
                                                                    class="form-control bg-light" placeholder="0.0">
                                                            </td>
                                                            <input type="hidden"
                                                                name="thermo[rh][{{ $pIndex }}][{{ $i }}][posisi]"
                                                                value="{{ $pos }}">
                                                            <input type="hidden"
                                                                name="thermo[rh][{{ $pIndex }}][{{ $i }}][tipe_hitung]"
                                                                value="rh">
                                                        </tr>
                                                    @endfor
                                                    {{-- Tambahkan garis/pemisah visual setelah 3 baris --}}
                                                    <tr class="table-primary">
                                                        <td colspan="4"></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="text-start mt-4">
                                    <button type="submit" class="btn btn-success rounded-pill px-4"
                                        id="btnSubmitThermo">
                                        <i class="mdi mdi-send-check-outline me-1"></i> Submit & Simpan
                                    </button>
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

            $('#formKalibrasiThermo').on('submit', function(e) {
                e.preventDefault();
                // let formData = $(this).serialize();
                const suhu = $('#suhu_ruangan').val();
                const toleransiSuhu = $('#toleransi_suhu').val();
                const kelembaban = $('#kelembaban').val();
                const toleransiKelembaban = $('#toleransi_kelembaban').val();

                // Format data gabungan
                const suhuFormatted = suhu && toleransiSuhu ?
                    `${suhu}°C ± ${toleransiSuhu}°C` :
                    suhu ? `${suhu}°C` : '';

                const kelembabanFormatted = kelembaban && toleransiKelembaban ?
                    `${kelembaban}% ± ${toleransiKelembaban}%` :
                    kelembaban ? `${kelembaban}%` : '';

                // Masukkan hasil ke hidden input
                $('#suhu_ruangan_final').val(suhuFormatted);
                $('#kelembaban_final').val(kelembabanFormatted);

                let formData = $('#formKalibrasiThermo').serializeArray();

                $.ajax({
                    url: "{{ route('kalibrasi.thermohygrometer.store') }}",
                    type: 'POST',
                    data: formData,
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#formKalibrasiThermo')[0].reset();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan!',
                        });
                    }
                });
            });
        });
    </script>
@endsection
