@extends('layouts.app')

@section('title', 'Data Kalibrasi Temperature')

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
                            <h4 class="card-title">History Kalibrasi Temperature</h4>
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
                        <i class="bi bi-clipboard-data me-2"></i>
                        Detail Kalibrasi Temperature
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
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#result-pane">Hitung
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
                                <div class="card-header bg-soft-primary text-primary">
                                    <i class="bi bi-arrow-up-circle-fill me-2"></i>
                                    <strong>Pengukuran</strong>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light align-middle">
                                                <tr>
                                                    <th>Titik Kalibrasi (°C)</th>
                                                    <th>Penunjuk Standar</th>
                                                    <th>Penunjuk Alat</th>
                                                    <th>Koreksi Standar</th>
                                                    <th>Suhu Standar</th>
                                                    <th>Koreksi Alat</th>
                                                </tr>
                                            </thead>
                                            <tbody id="hitung_data"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Hasil -->
                        <div class="tab-pane fade" id="result-pane">
                            <div class="card border-success">
                                <div class="card-header bg-soft-primary text-primary">
                                    <i class="bi bi-calculator-fill me-2"></i>
                                    <strong>Hasil Perhitungan Summary</strong>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Titik Kalibrasi (°C)</th>
                                                    <th>Avg Penunjuk Alat</th>
                                                    <th>Avg Suhu Standar</th>
                                                    <th>Avg Kor Alat</th>
                                                    <th>Std Deviasi</th>
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
                    url: `{{ url('api/kalibrasi/temperature/data') }}`,
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
                            data: "temperature",
                            className: "text-center",
                            render: data => data.length
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

            // Show detail modal
            $(document).on('click', '.btn-detail', function() {
                let id = $(this).data('id');
                showDetailModal(id, historyData);
            });

            function formatNumberDynamic(value, maxDecimals = 2) {
                if (value === null || value === undefined || value === '' || isNaN(value)) return '-';
                const num = parseFloat(value);
                if (Number.isInteger(num)) return num.toString(); // tanpa koma kalau bulat
                return num.toFixed(maxDecimals).replace(/\.?0+$/, ''); // hapus nol di belakang koma
            }

            function showDetailModal(id, historyData) {
                let item = historyData.find(x => x.id === id);
                if (!item) return;

                console.log(item);

                // Isi data umum
                $('#detail_kode_alat').text(item.alat.kode_alat);
                $('#detail_nama_alat').text(item.alat.nama_alat);
                $('#detail_tgl_kalibrasi').text(formatDate(item.tgl_kalibrasi));
                $('#detail_tgl_ulang').text(formatDate(item.tgl_kalibrasi_ulang));
                $('#detail_lokasi').text(item.lokasi_kalibrasi);
                $('#detail_suhu').text(item.suhu_ruangan);
                $('#detail_kelembaban').text(item.kelembaban);
                $('#detail_jenis').text(item.jenis_kalibrasi.toUpperCase());
                $('#detail_metode').text(item.alat.metode_kalibrasi);

                // Render volumetrik data
                const temperatures = Array.isArray(item.temperature) ? item.temperature : [];
                const hitungBody = $('#hitung_data');
                hitungBody.empty();

                if (temperatures.length > 0) {
                    temperatures.forEach(v => {
                        hitungBody.append(`
                            <tr>
                                <td>${formatNumberDynamic(v.titik_kalibrasi, 2)}</td>
                                <td>${formatNumberDynamic(v.penunjuk_standar, 2)}</td>
                                <td>${formatNumberDynamic(v.penunjuk_alat, 2)}</td>
                                <td>${formatNumberDynamic(v.koreksi_standar, 2)}</td>
                                <td>${formatNumberDynamic(v.suhu_standar, 2)}</td>
                                <td>${formatNumberDynamic(v.koreksi_alat, 2)}</td>
                            </tr>
                        `);
                    });
                } else {
                    hitungBody.append(`
                        <tr>
                            <td colspan="6" class="text-muted fst-italic">Tidak ada data pengukuran</td>
                        </tr>
                    `);
                }

                // === Render data gabungan ===
                const gabungan = item.temperature_gabungan ?? [];
                const gabunganBody = $('#detail_gabungan');
                gabunganBody.empty();

                if (Array.isArray(gabungan) && gabungan.length > 0) {
                    gabungan.forEach(row => {
                        gabunganBody.append(`
                            <tr>
                                <td>${formatNumberDynamic(row.titik_kalibrasi ?? '-')}</td>
                                <td>${formatNumberDynamic(row.avg_penunjuk_alat, 8)}</td>
                                <td>${formatNumberDynamic(row.avg_suhu_standar, 8)}</td>
                                <td>${formatNumberDynamic(row.avg_kor_alat, 8)}</td>
                                <td>${formatNumberDynamic(row.stdev, 8)}</td>
                                <td>${formatNumberDynamic(row.ketidakpastian, 8)}</td>
                            </tr>
                        `);
                    });
                } else {
                    gabunganBody.append(`
                        <tr>
                            <td colspan="6" class="text-muted fst-italic">Data gabungan belum tersedia</td>
                        </tr>
                    `);
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
                            url: `{{ route('kalibrasi.temperature.delete', '') }}/` + id,
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
