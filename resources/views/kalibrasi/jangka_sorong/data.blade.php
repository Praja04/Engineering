@extends('layouts.app')

@section('title', 'Data Kalibrasi jangka Sorong')

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
                            <h4 class="card-title">History Kalibrasi Jangka Sorong</h4>
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
                        Detail Kalibrasi Jangka Sorong
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
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#measurement-pane">Data
                                Pengukuran</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#summary-pane">Summary</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#final-pane">Final
                                Summary</button>
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

                        <!-- Tab 2: Data Pengukuran -->
                        <div class="tab-pane fade" id="measurement-pane">
                            <div class="card border-success mb-3">
                                <div class="card-header bg-soft-primary text-primary">
                                    <i class="bi bi-rulers me-2"></i> Data Pengukuran
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm text-center mb-0">
                                            <thead class="table-light align-middle">
                                                <tr>
                                                    <th>N</th>
                                                    <th>Nilai Master (mm)</th>
                                                    <th>Nilai Pembacaan (mm)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detail_pengukuran"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Summary -->
                        <div class="tab-pane fade" id="summary-pane">
                            <div class="card border-success mb-3">
                                <div class="card-header bg-soft-primary text-primary">
                                    <i class="bi bi-bar-chart-line me-2"></i> Hasil Perhitungan Summary
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No Mastes</th>
                                                    <th>Nilai Master (mm)</th>
                                                    <th>Avg Pembacaan (mm)</th>
                                                    <th>Std Deviasi</th>
                                                    <th>Koreksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detail_summary"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 4: Final Summary -->
                        <div class="tab-pane fade" id="final-pane">
                            <div class="card border-success">
                                <div class="card-header bg-soft-primary text-primary">
                                    <i class="bi bi-clipboard-check me-2"></i> Summary 2
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Std Dev Total</th>
                                                    <th>Ketidakpastian</th>
                                                    <th>K (K=2)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detail_final_summary"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
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
                    url: `{{ url('api/kalibrasi/jangka-sorong/data') }}`,
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
                            data: "jangka_sorong",
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
                const summaryList = item.jangka_sorong_summary ?? [];
                const finalSummaryList = item.jangka_sorong_final_summary ?? [];
                const pengukuranList = item.jangka_sorong ?? [];

                // === Isi Tab Informasi ===
                $('#detail_kode_alat').text(alat.kode_alat ?? '-');
                $('#detail_nama_alat').text(alat.nama_alat ?? '-');
                $('#detail_tgl_kalibrasi').text(item.tgl_kalibrasi ?? '-');
                $('#detail_tgl_ulang').text(item.tgl_kalibrasi_ulang ?? '-');
                $('#detail_lokasi').text(item.lokasi_kalibrasi ?? '-');
                $('#detail_suhu').text(item.suhu_ruangan ?? '-');
                $('#detail_kelembaban').text(item.kelembaban ?? '-');
                $('#detail_jenis').text(alat.jenis_kalibrasi ?? '-');
                $('#detail_metode').text(item.alat.metode_kalibrasi ?? 'Tidak ada data metode.');

                // === Tab Data Pengukuran (Grouped by nilai_master) ===
                const pengukuranBody = $('#detail_pengukuran');
                pengukuranBody.empty();

                if (pengukuranList.length > 0) {
                    // Group berdasarkan nilai_master (bukan master_id lagi)
                    const grouped = {};
                    pengukuranList.forEach(p => {
                        const nilaiMaster = p.master?.nilai_master ?? `Master ID: ${p.master_id}`;
                        if (!grouped[nilaiMaster]) grouped[nilaiMaster] = [];
                        grouped[nilaiMaster].push(p);
                    });

                    Object.keys(grouped).forEach(nilaiMaster => {
                        pengukuranBody.append(`
                            <tr class="table-primary fw-bold">
                                <td colspan="3" class="text-start ps-3">
                                    Titik Master: ${formatNumber(nilaiMaster)} mm
                                </td>
                            </tr>
                        `);

                        grouped[nilaiMaster].forEach((p, i) => {
                            pengukuranBody.append(`
                                <tr>
                                    <td>${p.no ?? i + 1}</td>
                                    <td>${formatNumber(p.master?.nilai_master ?? '-')}</td>
                                    <td>${formatNumber(p.nilai_pembacaan ?? '-')}</td>
                                </tr>
                            `);
                        });
                    });
                } else {
                    pengukuranBody.append(
                        '<tr><td colspan="3" class="text-muted">Tidak ada data pengukuran.</td></tr>');
                }

                // === Isi Tab Summary ===
                const summaryBody = $('#detail_summary');
                summaryBody.empty();

                if (summaryList.length > 0) {
                    summaryList.forEach(s => {
                        summaryBody.append(`
                            <tr>
                                <td>${s.master.no ?? '-'}</td>
                                <td>${formatNumber(s.master.nilai_master) ?? '-'}</td>
                                <td>${formatNumber(s.avg_pembacaan) ?? '-'}</td>
                                <td>${s.std_dev ?? '-'}</td>
                                <td>${s.koreksi ?? '-'}</td>
                            </tr>
                        `);
                    });
                } else {
                    summaryBody.append('<tr><td colspan="4" class="text-muted">Tidak ada data summary.</td></tr>');
                }

                // === Isi Tab Final Summary ===
                const finalBody = $('#detail_final_summary');
                finalBody.empty();

                if (finalSummaryList.length > 0) {
                    const f = finalSummaryList[0];
                    finalBody.append(`
                        <tr>
                            <td>${f.std_dev_total ?? '0.00000'}</td>
                            <td>${f.ketidakpastian ?? '0.0000'}</td>
                            <td>${f.k_2 ?? '2'}</td>
                        </tr>
                    `);
                } else {
                    finalBody.append(
                        '<tr><td colspan="3" class="text-muted">Tidak ada data final summary.</td></tr>');
                }

                // === Tampilkan modal ===
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
