@extends('layouts.app')

@section('content')
    <div class="page-content">
        <div class="container-fluid pb-5">

            <!-- Page Header with Gradient Background -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between p-4 rounded-3 mb-4 text-white shadow-lg"
                        style="background: linear-gradient(135deg, #0d6efd, #0dcaf0);">
                        <div>
                            <h4 class="mb-1 text-white fw-bold">Dashboard Kebutuhan Material</h4>
                            <p class="mb-0 opacity-75">Informasi dan analisis kebutuhan material pada setiap pelaksanaan
                                maintenance</p>
                        </div>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0 bg-transparent">
                                <li class="breadcrumb-item">
                                    <a href="javascript: void(0);" class="text-white-50">Dashboards</a>
                                </li>
                                <li class="breadcrumb-item active text-white">Maintenance</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Panel -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <form id="filterForm">
                                <div class="row g-3 align-items-end">
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-muted">TANGGAL MULAI</label>
                                        <div class="input-group border rounded">
                                            <span class="input-group-text border-0 bg-transparent"><i
                                                    class="ri-calendar-2-line"></i></span>
                                            <input type="text" id="startDate" name="start_date"
                                                class="form-control border-0 flatpickr-input"
                                                placeholder="Pilih tanggal mulai">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-muted">TANGGAL SELESAI</label>
                                        <div class="input-group border rounded">
                                            <span class="input-group-text border-0 bg-transparent"><i
                                                    class="ri-calendar-2-line"></i></span>
                                            <input type="text" id="endDate" name="end_date"
                                                class="form-control border-0 flatpickr-input"
                                                placeholder="Pilih tanggal selesai">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-muted">JENIS MAINTENANCE</label>
                                        <select id="filterJenisMtc" name="jenis_mtc" class="form-select border">
                                            <option value="">Semua Jenis</option>
                                            @foreach ($jenisMtcList as $jenis)
                                                <option value="{{ $jenis }}">{{ $jenis }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-muted">PAKET MAINTENANCE</label>
                                        <select id="filterPaket" name="paket" class="form-select border">
                                            <option value="">Semua Paket</option>
                                            @foreach ($paketList as $paket)
                                                <option value="{{ $paket }}">{{ $paket }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-12 d-flex gap-2">
                                        <button type="button" id="btnFilter" class="btn btn-primary w-100 py-2">
                                            <i class="ri-filter-line me-1"></i> Filter
                                        </button>
                                        <button type="button" id="btnReset" class="btn btn-light border w-100 py-2">
                                            <i class="ri-refresh-line me-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards Row -->
            <div class="row g-4 mb-4">
                <!-- Card 1 -->
                <div class="col-xl-4 col-md-6">
                    <div class="card stat-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center"
                                        style="width: 48px; height: 48px;">
                                        <i class="ri-stack-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted fw-semibold text-uppercase tracking-wide small">Total
                                        Kuantitas Material</h6>
                                    <h4 class="mb-0 fw-bold" id="valTotalQty">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="col-xl-4 col-md-6">
                    <div class="card stat-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-success-subtle d-flex align-items-center justify-content-center"
                                        style="width: 48px; height: 48px;">
                                        <i class="ri-shape-line fs-4 text-success"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted fw-semibold text-uppercase tracking-wide small">Jenis Barang
                                        Unik</h6>
                                    <h4 class="mb-0 fw-bold" id="valUniqueItems">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="col-xl-4 col-md-12">
                    <div class="card stat-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-warning-subtle d-flex align-items-center justify-content-center"
                                        style="width: 48px; height: 48px;">
                                        <i class="ri-tools-line fs-4 text-warning"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted fw-semibold text-uppercase tracking-wide small">Total
                                        Pekerjaan MTC</h6>
                                    <h4 class="mb-0 fw-bold" id="valTotalJobs">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="row">
                <!-- Top Materials Bar Chart -->
                <div class="col-lg-7 col-md-12 col-12">
                    <div class="card chart-card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom-0 pb-0">
                            <h5 class="card-title mb-1 fw-semibold">
                                <i class="ri-bar-chart-line text-primary me-2"></i>Top 10 Material Paling Sering Digunakan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="chartTopMaterials" style="min-height: 350px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Distribution Pie Chart -->
                <div class="col-lg-5 col-md-12 col-12">
                    <div class="card chart-card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom-0 pb-0">
                            <h5 class="card-title mb-1 fw-semibold">
                                <i class="ri-donut-chart-line text-primary me-2"></i>Distribusi Material Berdasarkan Jenis
                                MTC
                            </h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div id="chartDistribution" style="width: 100%; min-height: 350px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trend Chart Row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card chart-card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom-0 pb-0">
                            <h5 class="card-title mb-1 fw-semibold">
                                <i class="ri-line-chart-line text-primary me-2"></i>Tren Bulanan Penggunaan Material
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="chartMonthlyTrend" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed List Table -->
            <div class="row">
                <div class="col-md-12 col-12">
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header bg-transparent border-bottom-0 p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h5 class="fw-bold mb-1 text-primary"><i class="ri-list-check-2 me-2"></i>Detail Kebutuhan
                                    Material</h5>
                                <p class="text-muted mb-0 small">Rincian data kebutuhan material yang pernah diinput</p>
                            </div>
                            <div>
                                <input type="text" id="tableSearch" class="form-control form-control-sm border"
                                    placeholder="Cari material..." style="width: 250px;">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableMaterials" class="table table-hover table-striped align-middle mb-0 w-100"
                                    style="font-size: 13px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3" style="width: 60px;">NO</th>
                                            <th>TANGGAL</th>
                                            <th>JENIS MAINTENANCE</th>
                                            <th>MESIN / UNIT / AREA</th>
                                            <th>MID</th>
                                            <th>DESKRIPSI MATERIAL</th>
                                            <th class="text-end">QTY</th>
                                            <th>SATUAN</th>
                                            <th class="pe-3">TEKNISI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Loaded dynamically via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <!-- Load ApexCharts if not loaded globally -->
    <script src="{{ asset('material/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Init Date Pickers
            flatpickr("#startDate", {
                dateFormat: "Y-m-d",
                defaultDate: new Date(new Date().setDate(new Date().getDate() - 30))
            });
            flatpickr("#endDate", {
                dateFormat: "Y-m-d",
                defaultDate: new Date()
            });

            // Chart References
            let barChart = null;
            let pieChart = null;
            let trendChart = null;

            // Load dashboard data
            function loadDashboardData() {
                const formData = $('#filterForm').serialize();

                $.ajax({
                    url: "{{ route('mtc.dashboard.material.charts') }}",
                    type: "GET",
                    data: formData,
                    success: function(res) {
                        if (res.status === 200) {
                            // Summary metrics
                            $('#valTotalQty').text(res.summary.total_qty.toLocaleString('id-ID'));
                            $('#valUniqueItems').text(res.summary.unique_items.toLocaleString('id-ID'));
                            $('#valTotalJobs').text(res.summary.total_jobs.toLocaleString('id-ID'));

                            // Top 10 materials chart
                            renderBarChart(res.charts.top_materials);

                            // Donut distribution chart
                            renderPieChart(res.charts.distribution);

                            // Trend chart
                            renderTrendChart(res.charts.trend);
                        }
                    },
                    error: function(err) {
                        console.error("Dashboard error:", err);
                    }
                });
            }

            // Render Bar Chart (Top Materials)
            function renderBarChart(data) {
                const container = document.querySelector("#chartTopMaterials");
                if (!data || data.length === 0) {
                    if (barChart) {
                        barChart.destroy();
                        barChart = null;
                    }
                    container.innerHTML = '<div class="text-center p-5 text-muted">Tidak ada data untuk periode ini</div>';
                    return;
                }
                const categories = data.map(item => item.label);
                const seriesData = data.map(item => item.qty);

                const options = {
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Jumlah Qty',
                        data: seriesData
                    }],
                    colors: ['#0d6efd'],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 6,
                            barHeight: '60%'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val.toLocaleString('id-ID');
                        },
                        style: {
                            colors: ['#fff']
                        }
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            formatter: function(val) {
                                return Math.floor(val);
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9'
                    }
                };

                if (barChart) {
                    barChart.destroy();
                }
                barChart = new ApexCharts(document.querySelector("#chartTopMaterials"), options);
                barChart.render();
            }

            // Render Pie Chart (Distribution)
            function renderPieChart(data) {
                const container = document.querySelector("#chartDistribution");
                if (!data || data.length === 0) {
                    if (pieChart) {
                        pieChart.destroy();
                        pieChart = null;
                    }
                    container.innerHTML = '<div class="text-center p-5 text-muted">Tidak ada data untuk periode ini</div>';
                    return;
                }
                const labels = data.map(item => item.jenis_mtc);
                const series = data.map(item => item.qty);

                const options = {
                    chart: {
                        type: 'donut',
                        height: 350
                    },
                    series: series,
                    labels: labels,
                    colors: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#d63384', '#6c757d',
                        '#0dcaf0', '#20c997', '#fd7e14'
                    ],
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }],
                    legend: {
                        position: 'bottom',
                        fontSize: '12px'
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val, opts) {
                            return opts.w.config.series[opts.seriesIndex].toLocaleString('id-ID');
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Qty',
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                                .toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        }
                    }
                };

                if (pieChart) {
                    pieChart.destroy();
                }
                pieChart = new ApexCharts(document.querySelector("#chartDistribution"), options);
                pieChart.render();
            }

            // Render Trend Line/Area Chart
            function renderTrendChart(data) {
                const container = document.querySelector("#chartMonthlyTrend");
                if (!data || data.length === 0) {
                    if (trendChart) {
                        trendChart.destroy();
                        trendChart = null;
                    }
                    container.innerHTML = '<div class="text-center p-5 text-muted">Tidak ada data untuk periode ini</div>';
                    return;
                }
                const categories = data.map(item => item.formatted_month);
                const seriesData = data.map(item => parseFloat(item.total_qty));

                const options = {
                    chart: {
                        type: 'area',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Material Qty',
                        data: seriesData
                    }],
                    colors: ['#198754'],
                    xaxis: {
                        categories: categories
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    dataLabels: {
                        enabled: false
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [0, 100]
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9'
                    }
                };

                if (trendChart) {
                    trendChart.destroy();
                }
                trendChart = new ApexCharts(document.querySelector("#chartMonthlyTrend"), options);
                trendChart.render();
            }

            // Datatable Init
            const table = $('#tableMaterials').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                pageLength: 10,
                ajax: {
                    url: "{{ route('mtc.dashboard.material.list') }}",
                    type: "GET",
                    data: function(d) {
                        d.start_date = $('#startDate').val();
                        d.end_date = $('#endDate').val();
                        d.jenis_mtc = $('#filterJenisMtc').val();
                        d.paket = $('#filterPaket').val();
                        d.search_val = $('#tableSearch').val();
                    }
                },
                columns: [{
                        data: null,
                        className: 'ps-3 text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'main.tanggal',
                        render: function(data) {
                            if (!data) return '-';
                            const d = new Date(data);
                            return d.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });
                        }
                    },
                    {
                        data: 'main.jenis_mtc',
                        render: function(data) {
                            return `<span class="badge text-dark border">${data || '-'}</span>`;
                        }
                    },
                    {
                        data: 'nama_mesin',
                        defaultContent: '-'
                    },
                    {
                        data: 'mid',
                        defaultContent: '-'
                    },
                    {
                        data: 'deskripsi',
                        render: function(data) {
                            return `<span class="fw-semibold text-secondary">${data || '-'}</span>`;
                        }
                    },
                    {
                        data: 'qty',
                        className: 'text-end fw-bold text-primary',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'uom',
                        defaultContent: '-'
                    },
                    {
                        data: 'main.created_by.username',
                        className: 'pe-3',
                        defaultContent: '-'
                    }
                ],
                language: {
                    emptyTable: '<div class="py-4 text-muted text-center">Tidak ada data material yang ditemukan</div>',
                    paginate: {
                        previous: '‹',
                        next: '›'
                    },
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ baris',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 baris',
                    lengthMenu: 'Tampilkan _MENU_ baris',
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Memuat...</span></div>'
                }
            });

            // Trigger filters
            $('#btnFilter').click(function() {
                loadDashboardData();
                table.ajax.reload();
            });

            $('#btnReset').click(function() {
                $('#filterForm')[0].reset();
                // Reset flatpickr to original default values
                document.getElementById('startDate')._flatpickr.setDate(new Date(new Date().setDate(
                    new Date().getDate() - 30)));
                document.getElementById('endDate')._flatpickr.setDate(new Date());
                $('#tableSearch').val('');

                loadDashboardData();
                table.ajax.reload();
            });

            // Live table search on keyup
            let searchTimer = null;
            $('#tableSearch').keyup(function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    table.ajax.reload();
                }, 300);
            });

            // Initial Load
            loadDashboardData();
        });
    </script>
@endsection
