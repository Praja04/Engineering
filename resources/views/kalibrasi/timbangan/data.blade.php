@extends('layouts.app')

@section('title', 'Data Kalibrasi Timbangan')

@section('styles')
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title d-sm-flex align-items-center justify-content-between">
                        {{-- <h4 class="mb-sm-0">Form Input TKBM</h4> --}}

                        <a href="{{ route('kalibrasi.data.dashboard') }}"
                            class="btn btn-outline-primary rounded-pill px-4 d-flex align-items-center">
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">History Kalibrasi Timbangan</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover nowrap dt-responsive" id="historyTable" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Alat</th>
                                        <th>Tgl Kalibrasi</th>
                                        <th>Tgl Kalibrasi Ulang</th>
                                        <th>Lokasi</th>
                                        <th>Kondisi Ruangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle">
                                    <!-- Data akan diisi via jQuery -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Detail Kalibrasi Timbangan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    <!-- Tabs -->
                    <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab"
                                data-bs-target="#info-pane">Informasi</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#pembacaan-pane">Pembacaan</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#keseragaman-pane">Keseragaman
                                Skala</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pinggan-pane">Pinggan</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tare-pane">Tare</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#histerisis-pane">Histerisis</button>
                        </li>
                    </ul>

                    <div class="tab-content p-3">
                        <!-- Tab 1: Informasi -->
                        <div class="tab-pane fade show active" id="info-pane">
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-primary border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar-event"></i> Kode Alat
                                            </small>
                                            <strong id="detail_kode_alat"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-primary border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar-event"></i> Nama Alat
                                            </small>
                                            <strong id="detail_nama_alat"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-primary border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar-event"></i> Tanggal Kalibrasi
                                            </small>
                                            <strong id="detail_tgl_kalibrasi"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-success border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar-check"></i> Tgl Kalibrasi Ulang
                                            </small>
                                            <strong id="detail_tgl_ulang"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-info border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-geo-alt"></i> Lokasi
                                            </small>
                                            <strong id="detail_lokasi"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-warning border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-thermometer-half"></i> Suhu Ruangan
                                            </small>
                                            <strong id="detail_suhu"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-info border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-droplet"></i> Kelembaban
                                            </small>
                                            <strong id="detail_kelembaban"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-primary border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-gear"></i> Jenis Kalibrasi
                                            </small>
                                            <strong id="detail_jenis"></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-primary">
                                <strong><i class="bi bi-book me-2"></i>Metode Kalibrasi</strong>
                                <p class="mb-0 mt-2 small" id="detail_metode"></p>
                            </div>
                        </div>

                        <!-- Tab Pembacaan -->
                        <div class="tab-pane fade" id="pembacaan-pane">
                            <div id="detail_pembacaan" class="table-responsive"></div>
                            <hr>
                            <div id="detail_pembacaan_summary"></div>
                        </div>

                        <!-- Tab Keseragaman Skala -->
                        <div class="tab-pane fade" id="keseragaman-pane">
                            <div id="detail_keseragaman" class="table-responsive"></div>
                            <hr>
                            <div id="detail_keseragaman_summary"></div>
                        </div>

                        <!-- Tab Pinggan -->
                        <div class="tab-pane fade" id="pinggan-pane">
                            <div id="detail_pinggan" class="table-responsive"></div>
                            <hr>
                            <div id="detail_pinggan_summary"></div>
                        </div>

                        <!-- Tab Tare -->
                        <div class="tab-pane fade" id="tare-pane">
                            <div id="detail_tare" class="table-responsive"></div>
                            <hr>
                            <div id="detail_tare_summary"></div>
                        </div>

                        <!-- Tab Histerisis -->
                        <div class="tab-pane fade" id="histerisis-pane">
                            <div id="detail_histerisis" class="table-responsive"></div>
                            <hr>
                            <div id="detail_histerisis_summary"></div>
                        </div>
                    </div> <!-- /tab-content -->
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            fetchHistoryData();

            // Data dari API
            let historyData = [];

            // Fetch data dari API
            function fetchHistoryData() {
                $.ajax({
                    url: `{{ url('api/kalibrasi/timbangan/data') }}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        historyData = response.data;
                        renderTable();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                        alert('Gagal mengambil data. Silakan coba lagi.');
                    }
                });
            }

            // Render table
            function renderTable() {
                if ($.fn.DataTable.isDataTable('#historyTable')) {
                    $('#historyTable').DataTable().destroy();
                }

                $('#historyTable').DataTable({
                    data: historyData,
                    responsive: true,
                    processing: true,
                    serverSide: false,
                    destroy: true,
                    scrollX: true, // hilangkan horizontal scroll biar lebih clean
                    ordering: false,
                    language: {
                        lengthMenu: "Show _MENU_ entries",
                        search: "Cari:",
                        info: "Showing _START_ - _END_ from _TOTAL_ data",
                        infoEmpty: "Tidak ada data",
                        infoFiltered: "(difilter dari total _MAX_ data)"
                    },
                    columns: [{
                            data: null,
                            className: "text-center",
                            render: (data, type, row, meta) => meta.row + 1
                        },
                        {
                            data: "alat.kode_alat"
                        },
                        {
                            data: "tgl_kalibrasi",
                            render: data => formatDate(data)
                        },
                        {
                            data: "tgl_kalibrasi_ulang",
                            render: data => formatDate(data)
                        },
                        {
                            data: "lokasi_kalibrasi"
                        },
                        {
                            data: null,
                            render: data => `
                                <div>
                                    <small class="text-muted d-block">Suhu: ${data.suhu_ruangan}</small>
                                    <small class="text-muted">Kelembaban: ${data.kelembaban}</small>
                                </div>
                            `
                        },
                        {
                            data: null,
                            orderable: false,
                            className: "text-center",
                            render: (data, type, row) => `
                                <div>
                                    <button class="btn btn-sm btn-soft-info btn-detail" data-id="${row.id}" title="Detail">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-soft-danger delete-btn" data-id="${row.id}" title="Hapus">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            `
                        }
                    ],
                    dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6 text-end"p>>',
                });

            }

            function formatDate(dateString) {
                let date = new Date(dateString);
                let options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                return date.toLocaleDateString('id-ID', options);
            }

            $(document).on('click', '.btn-detail', function() {
                let id = $(this).data('id');
                showDetailModal(id, historyData);
            });

            function formatNumber(val) {
                if (val === null || val === undefined || val === '') return '-';
                const num = parseFloat(val);
                if (isNaN(num)) return val;
                return parseFloat(num.toFixed(2)).toString();
            }

            function showDetailModal(id, historyData) {
                const item = historyData.find(d => d.id === id);
                if (!item) {
                    Swal.fire('Oops!', 'Data tidak ditemukan!', 'error');
                    return;
                }

                const alat = item.alat ?? {};

                // === Informasi Umum ===
                $('#detail_kode_alat').text(alat.kode_alat ?? '-');
                $('#detail_nama_alat').text(alat.nama_alat ?? '-');
                $('#detail_tgl_kalibrasi').text(item.tgl_kalibrasi ?? '-');
                $('#detail_tgl_ulang').text(item.tgl_kalibrasi_ulang ?? '-');
                $('#detail_lokasi').text(item.lokasi_kalibrasi ?? '-');
                $('#detail_suhu').text(item.suhu_ruangan ?? '-');
                $('#detail_kelembaban').text(item.kelembaban ?? '-');
                $('#detail_jenis').text(alat.jenis_kalibrasi ?? '-');
                $('#detail_metode').text(alat.metode_kalibrasi ?? 'Tidak ada data metode.');

                renderPembacaan(item);
                renderKeseragamanSkala(item);
                renderPinggan(item);
                renderTare(item);
                renderHisterisis(item);

                // === Tampilkan modal ===
                $('#detailModal').modal('show');
            }

            function renderPembacaan(item) {
                const pembacaan = Array.isArray(item.pembacaan) ? item.pembacaan : [];
                const pembacaanSummary = item.pembacaan_summary ?? {};

                if (pembacaan.length === 0) {
                    $('#detail_pembacaan').html('<p class="text-muted">Tidak ada data pembacaan.</p>');
                } else {
                    // Kelompokkan berdasarkan 'kemampuan'
                    const grouped = pembacaan.reduce((acc, row) => {
                        if (!acc[row.kemampuan]) acc[row.kemampuan] = [];
                        acc[row.kemampuan].push(row);
                        return acc;
                    }, {});

                    let html = '';

                    Object.entries(grouped).forEach(([kemampuan, rows]) => {
                        // Ambil semua nilai titik unik di kelompok ini
                        const titikUnik = [...new Set(rows.map(r => r.titik).filter(Boolean))].join(', ');

                        html += `
                            <h6 class="mt-3">
                                <strong>${kemampuan}</strong>
                                ${titikUnik ? `<p class="text-muted">${titikUnik} gram</p>` : ''}
                            </h6>
                            <table class="table table-bordered table-sm mb-3">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ulangan</th>
                                        <th>Pembacaan Z</th>
                                        <th>Pembacaan M</th>
                                        <th>Selisih</th>
                                        <th>Maks. Perbedaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows.map(row => `<tr><td>${row.ulangan ?? '-'}</td><td>${row.pembacaan_z ?? '-'}</td><td>${row.pembacaan_m ?? '-'}</td><td>${row.selisih ?? '-'}</td><td>${row.maks_perbedaan ?? '-'}</td></tr>`).join('')}
                                </tbody>
                            </table>
                        `;
                    });

                    $('#detail_pembacaan').html(html);
                }

                // === Bagian Summary Pembacaan (Horizontal) ===
                if (pembacaanSummary.length > 0) {
                    let summaryHtml = `
                        <h6 class="mt-4"><strong>Summary Pembacaan</strong></h6>
                        <table class="table table-bordered table-sm w-100 mb-3">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Standar Deviasi</th>
                                    <th>Maks. Perbedaan Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${pembacaanSummary.map((summary, index) => `<tr><td>${index + 1}</td><td>${summary.std_dev ?? '-'}</td><td>${summary.maks_perbedaan_akhir ?? '-'}</td></tr>`).join('')}
                            </tbody>
                        </table>
                    `;

                    $('#detail_pembacaan_summary').html(summaryHtml);
                } else {
                    $('#detail_pembacaan_summary').html('<p class="text-muted">Tidak ada data summary.</p>');
                }
            }

            function renderKeseragamanSkala(item) {
                const keseragaman = Array.isArray(item.keseragaman_skala) ? item.keseragaman_skala : [];
                const summary = item.keseragaman_summary ?? {};

                // Jika tidak ada data
                if (keseragaman.length === 0) {
                    $('#detail_keseragaman').html('<p class="text-muted">Tidak ada data keseragaman skala.</p>');
                    $('#detail_keseragaman_summary').html('');
                    return;
                }

                // === Kelompokkan berdasarkan nilai beban ===
                const grouped = keseragaman.reduce((acc, row) => {
                    const key = row.beban || 'Tidak diketahui';
                    if (!acc[key]) acc[key] = [];
                    acc[key].push(row);
                    return acc;
                }, {});

                let html = '';

                Object.entries(grouped).forEach(([beban, rows]) => {
                    html += `
                        <h6 class="mt-3"><strong>Beban ${beban}</strong></h6>
                        <table class="table table-bordered table-sm mb-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Beban Timbangan</th>
                                    <th>Pembacaan Skala</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rows.map(r => `<tr><td>${r.beban_timbangan ?? '-'}</td><td>${r.pembacaan_skala ?? '-'}</td></tr>`).join('')}
                            </tbody>
                        </table>
                    `;
                });

                // Tampilkan tabel keseragaman skala
                $('#detail_keseragaman').html(html);

                let summaryHtml = '';
                if (summary.length === 0) {
                    summaryHtml = '<p class="text-muted">Tidak ada data summary keseragaman.</p>';
                } else {
                    summaryHtml = `
                        <h6 class="mt-3"><strong>Summary Keseragaman Skala</strong></h6>
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Beban</th>
                                    <th>Avg Z</th>
                                    <th>Avg M</th>
                                    <th>Selisih Z-M</th>
                                    <th>Standar Massa</th>
                                    <th>Koreksi Skala</th>
                                    <th>Absolut Koreksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${summary.map((s, i) => `<tr><td>${i + 1}</td><td>${s.beban ?? '-'}</td><td>${s.avg_z ?? '-'}</td><td>${s.avg_m ?? '-'}</td><td>${s.selisih_zm ?? '-'}</td><td>${s.standar_massa ?? '-'}</td><td>${s.koreksi_skala ?? '-'}</td><td>${s.absolut_koreksi ?? '-'}</td></tr>`).join('')}
                            </tbody>
                        </table>
                    `;
                }

                $('#detail_keseragaman_summary').html(summaryHtml);
            }

            function renderPinggan(item) {
                // === Render Pinggan ===
                const pinggan = item.pinggan ?? [];
                const pingganSummary = item.pinggan_summary ?? {};

                let pingganHtml = '';
                if (pinggan.length === 0) {
                    pingganHtml = '<p class="text-muted">Tidak ada data pinggan.</p>';
                } else {
                    pingganHtml += `
                        <h6 class="mt-3"><strong>Data Pengujian Pinggan</strong></h6>
                        <table class="table table-bordered table-sm text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Diameter</th>
                                    <th>Massa</th>
                                    <th>Tengah</th>
                                    <th>Depan</th>
                                    <th>Belakang</th>
                                    <th>Kiri</th>
                                    <th>Kanan</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    pinggan.forEach((item, index) => {
                        pingganHtml += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.diameter ?? '-'}</td>
                                <td>${item.massa ?? '-'}</td>
                                <td>${item.tengah ?? '-'}</td>
                                <td>${item.depan ?? '-'}</td>
                                <td>${item.belakang ?? '-'}</td>
                                <td>${item.kiri ?? '-'}</td>
                                <td>${item.kanan ?? '-'}</td>
                            </tr>
                        `;
                    });

                    pingganHtml += `
                            </tbody>
                        </table>
                    `;
                }

                let summaryHtml = '';
                if (pingganSummary.length === 0) {
                    summaryHtml = '<p class="text-muted">Tidak ada data summary pinggan.</p>';
                } else {
                    summaryHtml = `
                        <h6 class="mt-3"><strong>Summary Pinggan</strong></h6>
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tengah</th>
                                    <th>Depan</th>
                                    <th>Belakang</th>
                                    <th>Kiri</th>
                                    <th>Kanan</th>
                                    <th>Minimum</th>
                                    <th>Maximum</th>
                                    <th>Selisih Maks</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    pingganSummary.forEach((smry, index) => {
                        summaryHtml += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${smry.smry_tengah ?? '-'}</td>
                                <td>${smry.smry_depan ?? '-'}</td>
                                <td>${smry.smry_belakang ?? '-'}</td>
                                <td>${smry.smry_kiri ?? '-'}</td>
                                <td>${smry.smry_kanan ?? '-'}</td>
                                <td>${smry.minimum ?? '-'}</td>
                                <td>${smry.maximum ?? '-'}</td>
                                <td>${smry.selisih_maks ?? '-'}</td>
                            </tr>
                        `;
                    });

                    summaryHtml += `
                            </tbody>
                        </table>
                    `;
                }

                $('#detail_pinggan').html(pingganHtml);
                $('#detail_pinggan_summary').html(summaryHtml);

            }

            function renderTare(item) {
                const tare = Array.isArray(item.tare) ? item.tare : [];
                const summary = item.tare_summary ?? null;

                // === Bagian Data Tare ===
                if (tare.length === 0) {
                    $('#detail_tare').html('<p class="text-muted">Tidak ada data tare.</p>');
                } else {
                    // Kelompokkan berdasarkan tipe_tare (tanpa_pengenolan / dengan_pengenolan)
                    const grouped = tare.reduce((acc, row) => {
                        const key = row.tipe_tare || 'tidak_diketahui';
                        if (!acc[key]) acc[key] = [];
                        acc[key].push(row);
                        return acc;
                    }, {});

                    let html = '';

                    Object.entries(grouped).forEach(([tipe, rows]) => {
                        const tipeLabel = tipe === 'tanpa_pengenolan' ?
                            'Tanpa Pengenolan' :
                            tipe === 'dengan_pengenolan' ?
                            'Dengan Pengenolan' :
                            'Tidak Diketahui';

                        html += `
                            <h6 class="mt-3"><strong>${tipeLabel}</strong></h6>
                            <table class="table table-bordered table-sm mb-3 text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Beban</th>
                                        <th>Massa</th>
                                        <th>Pembacaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows.map(row => `<tr><td>${row.beban ?? '-'}</td><td>${row.massa ?? '-'}</td><td>${row.pembacaan ?? '-'}</td></tr>`).join('')}
                                </tbody>
                            </table>
                        `;
                    });

                    $('#detail_tare').html(html);
                }

                // === Bagian Summary Tare (Horizontal) ===
                if (summary) {
                    const summaryHtml = `
                        <h6 class="mt-4"><strong>Summary Tare</strong></h6>
                        <table class="table table-bordered table-sm text-center w-100 mb-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Massa</th>
                                    <th>Selisih MZ Tanpa Nol</th>
                                    <th>Selisih MZ Dengan Nol</th>
                                    <th>Pengaruh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>${summary.massa ?? '-'}</td>
                                    <td>${summary.selisih_mz_tanpa_nol ?? '-'}</td>
                                    <td>${summary.selisih_mz_dengan_nol ?? '-'}</td>
                                    <td>${summary.pengaruh ?? '-'}</td>
                                </tr>
                            </tbody>
                        </table>
                    `;
                    $('#detail_tare_summary').html(summaryHtml);
                } else {
                    $('#detail_tare_summary').html('<p class="text-muted">Tidak ada data summary tare.</p>');
                }
            }

            function renderHisterisis(item) {
                const histerisis = item.histerisis ?? [];
                const histerisisSummary = item.histerisis_summary ?? null;

                const histerisisBody = $('#detail_histerisis');
                histerisisBody.empty();

                if (histerisis.length > 0) {
                    let html = `
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">Percobaan</th>
                                    <th colspan="2">M</th>
                                    <th colspan="2">Z</th>
                                    <th colspan="2">Selisih</th>
                                </tr>
                                <tr>
                                    <th>M1</th>
                                    <th>M2</th>
                                    <th>Z1</th>
                                    <th>Z2</th>
                                    <th>M1 - M2</th>
                                    <th>Z1 - Z2</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    histerisis.forEach(h => {
                        html += `
                            <tr>
                                <td>${h.percobaan ?? '-'}</td>
                                <td>${h.m1 ?? '-'}</td>
                                <td>${h.m2 ?? '-'}</td>
                                <td>${h.z1 ?? '-'}</td>
                                <td>${h.z2 ?? '-'}</td>
                                <td>${h.m1_m2 ?? '-'}</td>
                                <td>${h.z1_z2 ?? '-'}</td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table>`;
                    histerisisBody.append(html);

                    if (histerisisSummary) {
                        const s = histerisisSummary;
                        const summaryHtml = `
                            <div class="mt-3">
                                <h6 class="fw-bold mb-2">Ringkasan Histerisis</h6>
                                <table class="table table-sm table-bordered text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Pembacaan Terkecil</th>
                                            <th>½ Kapasitas</th>
                                            <th>Rata-rata M1 - M2</th>
                                            <th>Rata-rata Z1 - Z2</th>
                                            <th>Rata-rata MZ</th>
                                            <th>Histerisis</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>${s.pembacaan_terkecil ?? '-'}</td>
                                            <td>${s.setengah_kapasitas ?? '-'}</td>
                                            <td>${s.avg_m1m2 ?? '-'}</td>
                                            <td>${s.avg_z1z2 ?? '-'}</td>
                                            <td>${s.avg_mz ?? '-'}</td>
                                            <td>${s.histerisis ?? '-'}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        `;
                        histerisisBody.append(summaryHtml);
                    }

                } else {
                    histerisisBody.append('<p class="text-muted">Tidak ada data histerisis.</p>');
                }

            }

            // Delete btn
            $('#historyTable').on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('kalibrasi.jangka-sorong.delete', '') }}/` + id,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message ||
                                        'Your file has been deleted.'
                                });
                                fetchHistoryData();
                            },
                            error: function(err) {
                                console.error("Error deleting data:", err);
                                Swal.fire(
                                    'Error!',
                                    'There was an error deleting the data.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
