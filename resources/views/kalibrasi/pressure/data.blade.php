@extends('layouts.app')

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

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Kalibrasi</a></li>
                                <li class="breadcrumb-item active">List History</a></li>
                                {{-- <li class="breadcrumb-item active">Alat Kalibrasi</li> --}}
                            </ol>
                        </div>
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
                                        <th>Tanggal Kalibrasi</th>
                                        <th>Lokasi</th>
                                        <th>Kondisi Ruangan</th>
                                        <th>Titik Kalibrasi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle">
                                    <!-- Data akan diisi via jQuery -->
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <h6>Keterangan Status:</h6>
                                <div class="d-flex gap-4 flex-wrap">
                                    <div>
                                        <span class="badge rounded-pill badge-outline-success px-3 py-1">Valid</span>
                                        <small class="text-muted">Masih dalam masa berlaku</small>
                                    </div>
                                    <div>
                                        <span class="badge rounded-pill badge-outline-warning px-3 py-1">Almost
                                            Expired</span>
                                        <small class="text-muted">Waktu kalibrasi ulang &lt; 30 hari</small>
                                    </div>
                                    <div>
                                        <span class="badge rounded-pill badge-outline-danger px-3 py-1">Expired</span>
                                        <small class="text-muted">Sudah melewati tanggal ulang</small>
                                    </div>
                                </div>
                            </div>
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
                        Detail Kalibrasi Pressure
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
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#result-pane">Hitung U
                                Gabungan</button>
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

                        <!-- Tab 2: Pengukuran -->
                        <div class="tab-pane fade" id="measurement-pane">
                            <!-- Tekanan Naik -->
                            <div class="card mb-3 border-success">
                                <div class="card-header bg-success text-white">
                                    <i class="bi bi-arrow-up-circle-fill me-2"></i>
                                    <strong>Tekanan Naik</strong>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Titik</th>
                                                    <th>Penunjuk Standar</th>
                                                    <th>Penunjuk Alat</th>
                                                    <th>Koreksi Standar</th>
                                                    <th>Tekanan Standar</th>
                                                    <th>Koreksi Alat</th>
                                                </tr>
                                            </thead>
                                            <tbody id="pressure_naik"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Tekanan Turun -->
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <i class="bi bi-arrow-down-circle-fill me-2"></i>
                                    <strong>Tekanan Turun</strong>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Titik</th>
                                                    <th>Penunjuk Standar</th>
                                                    <th>Penunjuk Alat</th>
                                                    <th>Koreksi Standar</th>
                                                    <th>Tekanan Standar</th>
                                                    <th>Koreksi Alat</th>
                                                </tr>
                                            </thead>
                                            <tbody id="pressure_turun"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Hasil -->
                        <div class="tab-pane fade" id="result-pane">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <i class="bi bi-calculator-fill me-2"></i>
                                    <strong>Hasil Perhitungan Ketidakpastian</strong>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Titik</th>
                                                    <th>U Naik</th>
                                                    <th>U Turun</th>
                                                    <th>U Naik²</th>
                                                    <th>U Turun²</th>
                                                    <th class="bg-success-subtle">U Gabungan</th>
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
                    url: `{{ url('api/kalibrasi/pressure/data') }}`,
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
                            data: "lokasi_kalibrasi",
                        },
                        {
                            data: null,
                            render: function(data) {
                                return `
                                    <p class="condition-text mb-0">
                                        Suhu: ${data.suhu_ruangan}°C<br>
                                        Kelembaban: ${data.kelembaban}%
                                    </p>
                                `;
                            },
                        },
                        {
                            data: "pressure_gabungan",
                            render: function(data) {
                                return data.length;
                            },
                        },
                        {
                            data: "tgl_kalibrasi_ulang",
                            render: function(data) {
                                return getStatusBadge(data);
                            },
                        },
                        {
                            data: null,
                            orderable: false,
                            render: function(data, type, row) {
                                return `
                                    <button class="btn btn-sm btn-outline-primary btn-detail" data-id="${row.id}" title="Detail Data">
                                        <i class="mdi mdi-eye"></i> Detail
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${row.id}" title="Delete Data">
                                        <i class="mdi mdi-delete"></i> Delete
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

            function getStatusBadge(tglUlang) {
                let today = new Date();
                let ulangDate = new Date(tglUlang);
                let diffDays = Math.ceil((ulangDate - today) / (1000 * 60 * 60 * 24));

                if (diffDays < 0) {
                    return '<span class="badge badge-soft-danger px-3 py-2">Expired</span>';
                } else if (diffDays <= 30) {
                    return '<span class="badge badge-soft-warning px-3 py-2">Almost Expired</span>';
                } else {
                    return '<span class="badge badge-soft-success px-3 py-2">Valid</span>';
                }
            }

            // Show detail modal
            $(document).on('click', '.btn-detail', function() {
                let id = $(this).data('id');
                showDetailModal(id, historyData);
            });

            function showDetailModal(id, historyData) {
                let item = historyData.find(x => x.id === id);
                if (!item) return;

                // Isi data umum
                $('#detail_kode_alat').text(item.alat.kode_alat);
                $('#detail_nama_alat').text(item.alat.nama_alat);
                $('#detail_tgl_kalibrasi').text(formatDate(item.tgl_kalibrasi));
                $('#detail_tgl_ulang').text(formatDate(item.tgl_kalibrasi_ulang));
                $('#detail_lokasi').text(item.lokasi_kalibrasi);
                $('#detail_suhu').text(item.suhu_ruangan + '°C');
                $('#detail_kelembaban').text(item.kelembaban + '%');
                $('#detail_jenis').text(item.jenis_kalibrasi.toUpperCase());
                $('#detail_metode').text(item.metode_kalibrasi);

                // Render pressure data
                let naikBody = $('#pressure_naik');
                let turunBody = $('#pressure_turun');
                naikBody.empty();
                turunBody.empty();
                let lastTitik = null;

                const pressures = (item.pressure || []).map(p => ({
                    ...p,
                    tekanan: (p.tekanan || '').toString().toLowerCase().trim(),
                    titik_kalibrasi: p.titik_kalibrasi ?? '',
                    penunjuk_standar: p.penunjuk_standar ?? '',
                    penunjuk_alat: p.penunjuk_alat ?? '',
                    koreksi_standar: p.koreksi_standar ?? '',
                    tekanan_standar: p.tekanan_standar ?? '',
                    koreksi_alat: p.koreksi_alat ?? ''
                }));

                const naikArr = pressures.filter(p => p.tekanan === 'naik')
                    .sort((a, b) => a.titik_kalibrasi - b.titik_kalibrasi);
                const turunArr = pressures.filter(p => p.tekanan === 'turun')
                    .sort((a, b) => a.titik_kalibrasi - b.titik_kalibrasi);

                function renderList(arr, $body) {
                    if (arr.length === 0) {
                        $body.append('<tr><td colspan="6" class="text-center text-muted">No data</td></tr>');
                        return;
                    }

                    let lastTitik = null; // <- reset per tabel

                    arr.forEach(p => {
                        const showTitik = lastTitik !== p.titik_kalibrasi;
                        const titikCell = showTitik ?
                            `<span class="badge badge-soft-primary">${p.titik_kalibrasi}</span>` : '';
                        lastTitik = p.titik_kalibrasi;

                        $body.append(`
                            <tr>
                                <td>${titikCell}</td>
                                <td>${p.penunjuk_standar}</td>
                                <td>${p.penunjuk_alat}</td>
                                <td>${p.koreksi_standar}</td>
                                <td>${p.tekanan_standar}</td>
                                <td>${p.koreksi_alat}</td>
                            </tr>
                        `);
                    });
                }

                renderList(naikArr, naikBody);
                renderList(turunArr, turunBody);

                // Render data gabungan
                if (item.pressure_gabungan && item.pressure_gabungan.length > 0) {
                    let tbody = $('#detail_gabungan');
                    tbody.empty();

                    $.each(item.pressure_gabungan, function(i, pg) {
                        let row = `
                        <tr>
                            <td><span class="badge badge-soft-primary">${pg.titik_kalibrasi}</span></td>
                            <td>${parseFloat(pg.u_naik).toFixed(9)}</td>
                            <td>${parseFloat(pg.u_turun).toFixed(9)}</td>
                            <td>${parseFloat(pg.u_naik_kuadrat).toFixed(9)}</td>
                            <td>${parseFloat(pg.u_turun_kuadrat).toFixed(9)}</td>
                            <td class="highlight-value">${parseFloat(pg.u_gabungan).toFixed(9)}</td>
                        </tr>
                    `;
                        tbody.append(row);
                    });
                }

                // Show modal
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
                            url: `{{ route('kalibrasi.pressure.delete', '') }}/` + id,
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
