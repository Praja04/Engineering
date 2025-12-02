@extends('layouts.app')

@section('title', 'Data Thermohygrometer')

@section('styles')
    <style>
        .badge-point {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            font-size: 0.875rem;
        }

        @media print {

            .btn,
            .card-header,
            .modal-footer {
                display: none !important;
            }
        }
    </style>
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
                            <h4 class="card-title">History Kalibrasi Pressure</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover nowrap dt-responsive" id="historyTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Alat</th>
                                        <th>Tgl Kalibrasi</th>
                                        <th>Tgl Kalibrasi Ulang</th>
                                        <th>Lokasi</th>
                                        <th>Kondisi Ruangan</th>
                                        <th>Titik Kalibrasi</th>
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
                        <i class="bi bi-thermometer-half me-2"></i>
                        Detail Kalibrasi Thermohygrometer
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
                                data-bs-target="#measurement-pane">Pengukuran</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#result-pane">Perhitungan
                                Gabungan</button>
                        </li>
                    </ul>

                    <div class="tab-content p-3">
                        <!-- Tab 1: Informasi -->
                        <div class="tab-pane fade show active" id="info-pane">
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <div class="card border-start border-primary border-3 card-animate">
                                        <div class="card-body">
                                            <small class="text-muted d-block"><i class="bi bi-upc"></i> Kode Alat</small>
                                            <strong id="detail_kode_alat"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-start border-primary border-3 card-animate">
                                        <div class="card-body">
                                            <small class="text-muted d-block"><i class="bi bi-box"></i> Nama Alat</small>
                                            <strong id="detail_nama_alat"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-start border-primary border-3 card-animate">
                                        <div class="card-body">
                                            <small class="text-muted d-block"><i class="bi bi-calendar-event"></i> Tanggal
                                                Kalibrasi</small>
                                            <strong id="detail_tgl_kalibrasi"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-start border-success border-3 card-animate">
                                        <div class="card-body">
                                            <small class="text-muted d-block"><i class="bi bi-calendar-check"></i> Kalibrasi
                                                Ulang</small>
                                            <strong id="detail_tgl_ulang"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-start border-info border-3 card-animate">
                                        <div class="card-body">
                                            <small class="text-muted d-block"><i class="bi bi-geo-alt"></i> Lokasi</small>
                                            <strong id="detail_lokasi"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-start border-warning border-3 card-animate">
                                        <div class="card-body">
                                            <small class="text-muted d-block"><i class="bi bi-thermometer-half"></i> Suhu
                                                Ruangan</small>
                                            <strong id="detail_suhu"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-start border-info border-3 card-animate">
                                        <div class="card-body">
                                            <small class="text-muted d-block"><i class="bi bi-droplet-half"></i>
                                                Kelembaban</small>
                                            <strong id="detail_kelembaban"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-start border-primary border-3 card-animate">
                                        <div class="card-body">
                                            <small class="text-muted d-block"><i class="bi bi-gear"></i> Jenis
                                                Kalibrasi</small>
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

                        <!-- Tab 2: Pengukuran -->
                        <div class="tab-pane fade" id="measurement-pane">
                            <!-- Suhu -->
                            <div class="card mb-3 border-danger">
                                <div class="card-header bg-danger text-white">
                                    <i class="bi bi-thermometer-half me-2"></i><strong>Pengukuran Suhu (°C)</strong>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light align-middle">
                                                <tr>
                                                    <th>Posisi Bagian</th>
                                                    <th>Penunjuk Standar</th>
                                                    <th>Penunjuk Alat</th>
                                                    <th>Koreksi Standar</th>
                                                    <th>Nilai Standar</th>
                                                    <th>Koreksi Alat</th>
                                                    <th>Ketidakpastian</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_suhu"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- RH -->
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <i class="bi bi-droplet-half me-2"></i><strong>Pengukuran Kelembaban (RH %)</strong>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light align-middle">
                                                <tr>
                                                    <th>Posisi Bagian</th>
                                                    <th>Penunjuk Standar</th>
                                                    <th>Penunjuk Alat</th>
                                                    <th>Koreksi Standar</th>
                                                    <th>Nilai Standar</th>
                                                    <th>Koreksi Alat</th>
                                                    <th>Ketidakpastian</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_rh"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Hasil Gabungan -->
                        <div class="tab-pane fade" id="result-pane">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <i class="bi bi-bar-chart-fill me-2"></i><strong>Hasil Perhitungan Gabungan</strong>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Posisi Bagian</th>
                                                    <th>Avg. Penunjuk Alat (°C)</th>
                                                    <th>Avg. Penunjuk Alat (RH %)</th>
                                                    <th>Standar Deviasi (°C)</th>
                                                    <th>Standar Deviasi (RH %)</th>
                                                    <th>Ketidakpastian</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detail_gabungan"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    url: `{{ url('api/kalibrasi/thermohygrometer/data') }}`,
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
                    processing: true,
                    serverSide: false,
                    responsive: true,
                    scrollX: true,
                    ordering: false,
                    language: {
                        lengthMenu: "Show _MENU_ entries",
                    },
                    columns: [{
                            data: null, // auto numbering
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            },
                        },
                        {
                            data: "alat.kode_alat",
                        },
                        {
                            data: "tgl_kalibrasi",
                            render: function(data) {
                                return formatDate(data);
                            },
                        },
                        {
                            data: "tgl_kalibrasi_ulang",
                            render: function(data) {
                                return formatDate(data);
                            },
                        },
                        {
                            data: "lokasi_kalibrasi",
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
                            data: "thermohygrometer_gabungan",
                            render: function(data) {
                                return data.length;
                            },
                        },
                        {
                            data: null,
                            orderable: false,
                            render: function(data, type, row) {
                                return `
                                    <button class="btn btn-sm btn-soft-info btn-detail" data-id="${row.id}" title="Detail">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-soft-danger delete-btn" data-id="${row.id}" title="Delete">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                `;
                            }
                        }
                    ]
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

            // Show detail modal
            $(document).on('click', '.btn-detail', function() {
                let id = $(this).data('id');
                showDetailModal(id, historyData);
            });

            function showDetailModal(id, historyData) {
                const item = historyData.find(x => x.id === id);
                if (!item) return;

                // === Informasi Umum ===
                $('#detail_kode_alat').text(item.alat.kode_alat);
                $('#detail_nama_alat').text(item.alat.nama_alat);
                $('#detail_tgl_kalibrasi').text(formatDate(item.tgl_kalibrasi));
                $('#detail_tgl_ulang').text(formatDate(item.tgl_kalibrasi_ulang));
                $('#detail_lokasi').text(item.lokasi_kalibrasi);
                $('#detail_suhu').text(item.suhu_ruangan + '°C');
                $('#detail_kelembaban').text(item.kelembaban + '%');
                $('#detail_jenis').text(item.jenis_kalibrasi.toUpperCase());
                $('#detail_metode').text(item.alat.metode_kalibrasi);

                const tableSuhu = $('#table_suhu');
                const tableRh = $('#table_rh');
                const gabBody = $('#detail_gabungan');
                tableSuhu.empty();
                tableRh.empty();
                gabBody.empty();

                const thermo = item.thermohygrometer || [];
                const gab = item.thermohygrometer_gabungan || [];

                const grouped = thermo.reduce((acc, d) => {
                    const key = `${d.tipe_hitung}_${d.titik_kalibrasi}`;
                    if (!acc[key]) acc[key] = [];
                    acc[key].push(d);
                    return acc;
                }, {});

                const formatNum = (v, dec = 2) => (isNaN(parseFloat(v)) ? '—' : parseFloat(v).toFixed(dec));

                function renderTable(type, $body) {
                    const filtered = Object.keys(grouped).filter(k => k.startsWith(type + '_'));
                    if (!filtered.length) {
                        $body.append(
                            '<tr><td colspan="11" class="text-center text-muted">Tidak ada data</td></tr>');
                        return;
                    }

                    filtered.forEach(key => {
                        const [_, titik] = key.split('_');
                        const data = grouped[key];
                        const g = gab.find(x => x.titik_kalibrasi == titik);

                        data.forEach((r, i) => {
                            const show = i === 0;
                            $body.append(`
                                <tr>
                                    ${show ? `<td rowspan="${data.length}" class="align-middle text-center"><span class="badge bg-primary">${r.posisi}</span></td>` : ''}
                                    <td>${formatNum(r.penunjuk_standar)}</td>
                                    <td>${formatNum(r.penunjuk_alat)}</td>
                                    <td>${formatNum(r.koreksi_standar)}</td>
                                    <td>${formatNum(r.tekanan_standar)}</td>
                                    <td>${formatNum(r.koreksi_alat)}</td>
                                     ${show && g ? `<td rowspan="${data.length}" class="align-middle">${formatNum(g['ketidak_pastian_' + type])}</td>` : ''}
                                </tr>
                            `);
                        });
                    });
                }

                renderTable('suhu', tableSuhu);
                renderTable('rh', tableRh);

                // === Gabungan ===
                if (gab.length > 0) {
                    gab.forEach(g => {
                        gabBody.append(`
                            <tr>
                                <td><span class="badge bg-success">${g.posisi || '—'}</span></td>
                                <td>${formatNum(g.avg_penunjuk_alat_suhu)}</td>
                                <td>${formatNum(g.avg_penunjuk_alat_rh)}</td>
                                <td>${formatNum(g.std_deviasi_suhu)}</td>
                                <td>${formatNum(g.std_deviasi_rh)}</td>
                                <td class="highlight-value">${formatNum(g.ketidak_pastian_suhu)} / ${formatNum(g.ketidak_pastian_rh)}</td>
                            </tr>
                        `);
                    });
                } else {
                    gabBody.append(
                        '<tr><td colspan="6" class="text-center text-muted">Tidak ada data gabungan</td></tr>');
                }

                $('#detailModal').modal('show');
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
                            url: `{{ route('kalibrasi.thermohygrometer.delete', '') }}/` +
                                id,
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
