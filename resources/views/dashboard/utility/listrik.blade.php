@extends('layouts.app')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <!-- Page Header with Gradient Background -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-gradient-warning p-4 rounded-3 mb-4 text-white shadow-lg">
                    <div>
                        <h4 class="mb-1 text-white fw-bold">Engineering Dashboard - Listrik</h4>
                        <p class="mb-0 opacity-75">Monitor konsumsi listrik secara real-time</p>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0 bg-transparent">
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);" class="text-white-50">Dashboards</a>
                            </li>
                            <li class="breadcrumb-item active text-white">
                                Listrik
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>


        <!-- KPI Listrik Section -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient-warning text-white border-0">
                        <div class="d-flex align-items-center gap-2">
                            <!-- Toggle Filter Type -->
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="filter_type_kpi" id="filter_type_monthly" value="monthly" checked>
                                <label class="btn btn-outline-warning btn-sm" for="filter_type_monthly">
                                    <i class="ri-calendar-line me-1"></i>Bulanan
                                </label>

                                <input type="radio" class="btn-check" name="filter_type_kpi" id="filter_type_weekly" value="weekly">
                                <label class="btn btn-outline-warning btn-sm" for="filter_type_weekly">
                                    <i class="ri-calendar-2-line me-1"></i>Mingguan
                                </label>
                            </div>

                            <!-- Monthly Filter -->
                            <div id="monthly_filter_container">
                                <input type="month" id="filter_kpi_monthly" class="form-control form-control-sm bg-white" style="width: 140px;">
                            </div>

                            <!-- Weekly Filter -->
                            <div id="weekly_filter_container" style="display: none;">
                                <select id="filter_kpi_weekly" class="form-select form-select-sm bg-white" style="width: 200px;">
                                    <option value="">Pilih Minggu...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-loading" id="loading-kpi">
                            <div class="text-center p-5">
                                <div class="spinner-grow text-warning" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted mb-0">Memuat data KPI...</p>
                            </div>
                        </div>

                        <div id="kpi-content" style="display: none;">
                            <div class="row g-4">
                                <!-- KPI Listrik Produksi -->
                                <div class="col-md-6">
                                    <div class="kpi-card p-4 rounded-3" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); border-left: 4px solid #F59E0B;">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-sm rounded-circle bg-warning d-flex align-items-center justify-content-center me-3">
                                                <i class="ri-settings-3-line fs-5 text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold text-warning">KPI Listrik Produksi</h6>
                                                <p class="mb-0 small text-muted">Total SDP 1+2+3+5+9+10+11 / FG</p>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="bg-white p-3 rounded-2 shadow-sm">
                                                    <h6 class="text-muted small mb-1">Finish Goods</h6>
                                                    <h5 class="mb-0 fw-bold" id="kpi_finish_goods">-</h5>
                                                    <span class="badge bg-info-subtle text-info small mt-1">Ton</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="bg-white p-3 rounded-2 shadow-sm">
                                                    <h6 class="text-muted small mb-1">Total Listrik</h6>
                                                    <h5 class="mb-0 fw-bold" id="kpi_total_listrik_produksi">-</h5>
                                                    <span class="badge bg-info-subtle text-info small mt-1">Kwh</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3 p-3 bg-white rounded-2 shadow-sm">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="text-muted small mb-1">KPI Hasil</h6>
                                                    <h4 class="mb-0 fw-bold text-warning" id="kpi_hasil_produksi">-</h4>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success-subtle text-success">Target: 51 Kwh/10 ton FG</span>
                                                </div>
                                            </div>
                                            <div class="progress mt-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" role="progressbar" id="kpi_progress_produksi" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- KPI Listrik BAS -->
                                <div class="col-md-6">
                                    <div class="kpi-card p-4 rounded-3" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); border-left: 4px solid #F59E0B;">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-sm rounded-circle bg-primary d-flex align-items-center justify-content-center me-3">
                                                <i class="ri-building-line fs-5 text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold text-primary">KPI Listrik BAS</h6>
                                                <p class="mb-0 small text-muted">Total SDP Seluruh(tanpa data total MDP) / Kecap Matang</p>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="bg-white p-3 rounded-2 shadow-sm">
                                                    <h6 class="text-muted small mb-1">Kecap Matang</h6>
                                                    <h5 class="mb-0 fw-bold" id="kpi_kecap_matang">-</h5>
                                                    <span class="badge bg-info-subtle text-info small mt-1">Ton</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="bg-white p-3 rounded-2 shadow-sm">
                                                    <h6 class="text-muted small mb-1">Total Listrik</h6>
                                                    <h5 class="mb-0 fw-bold" id="kpi_total_listrik_bas">-</h5>
                                                    <span class="badge bg-info-subtle text-info small mt-1">Kwh</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3 p-3 bg-white rounded-2 shadow-sm">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="text-muted small mb-1">KPI Hasil</h6>
                                                    <h4 class="mb-0 fw-bold text-primary" id="kpi_hasil_bas">-</h4>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success-subtle text-success">Target: 75 Kwh/ton</span>
                                                </div>
                                            </div>
                                            <div class="progress mt-2" style="height: 8px;">
                                                <div class="progress-bar bg-primary" role="progressbar" id="kpi_progress_bas" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="row g-4">
            <!-- Electricity Usage -->
            <div class="col-xxl-12 col-lg-12">
                <!-- Filter Section - Di luar card -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1 fw-semibold">Pemakaian Listrik</h5>
                        <p class="text-muted mb-0 small">Konsumsi energi listrik</p>
                    </div>

                    <!-- Dropdown Filter -->
                    <div class="dropdown">
                        <button class="btn btn-warning btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <i class="ri-calendar-line me-1"></i>
                            <span id="selectedBulanListrik">Pilih Periode</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3 border-0 shadow-lg" style="min-width: 300px;">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                                <input type="date" id="startDateListrik" class="form-control form-control-sm" onclick="event.stopPropagation();">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                                <input type="date" id="endDateListrik" class="form-control form-control-sm" onclick="event.stopPropagation();">
                            </div>
                            <button class="btn btn-primary btn-sm w-100" id="applyListrikRange">
                                <i class="ri-refresh-line me-1"></i>Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card Chart -->
                <div class="card chart-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <!-- MDP Info Badge -->
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-info-subtle text-info fw-semibold">
                                <i class="ri-information-line me-1"></i>MDP
                            </span>
                            <span id="mdp_listrik" class="text-muted small">-</span>
                        </div>

                        <!-- Loading State -->
                        <div class="chart-loading" id="loading-listrik">
                            <div class="text-center p-5">
                                <div class="spinner-grow text-warning" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted mb-0">Memuat data listrik...</p>
                            </div>
                        </div>

                        <!-- Chart Container -->
                        <div id="pemakaian-listrik-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Analysis Section -->
        <div class="row g-4 mt-2">
            <!-- Electricity Trend -->
            <div class="col-xl-12">
                <div class="card trend-card border-0 shadow-sm">
                    <div class="card-header bg-gradient-warning text-white border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1 text-white fw-semibold">Trend Pemakaian Listrik</h5>
                                <p class="mb-0 opacity-75 small">Analisis pola konsumsi listrik harian</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="month" id="filter_bulan_listrik" class="form-control form-control-sm bg-white" style="width: 140px;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="chart-loading" id="loading-trend-listrik">
                            <div class="text-center p-5">
                                <div class="spinner-grow text-warning" role="status"></div>
                                <p class="mt-3 text-muted mb-0">Memuat trend data listrik...</p>
                            </div>
                        </div>
                        <div id="pemakaian_listrik_chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Simpan instance chart
    const chartInstances = {};

    function showLoading(id) {
        $(`#${id}`).fadeIn(200);
    }

    function hideLoading(id) {
        $(`#${id}`).fadeOut(300);
    }

    // Base options
    function baseOptions(type = 'bar') {
        return {
            chart: {
                type,
                height: 300,
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: type === 'line' ? 'smooth' : 'straight',
                width: 2
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 3
            },
            xaxis: {
                labels: {
                    style: {
                        fontSize: '12px',
                        colors: '#8c9097'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '12px',
                        colors: '#8c9097'
                    }
                }
            },
            legend: {
                show: true,
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: 12,
                markers: {
                    radius: 4,
                    width: 12,
                    height: 12
                }
            },
            noData: {
                text: 'Memuat data...'
            }
        };
    }

    // Render bar chart
    function renderBar(labels, values, selector, key, unit, color) {
        const options = baseOptions('bar');
        options.series = [{
            name: `Total (${unit})`,
            data: values
        }];
        options.xaxis.categories = labels;
        options.colors = [color];

        options.dataLabels = {
            enabled: true,
            position: 'top',
            formatter: function(val) {
                return val + ' ' + unit;
            },
            style: {
                fontSize: '12px',
                fontWeight: 600,
                colors: ['#1F2937']
            },
            offsetY: -20
        };

        options.chart.height = 350;

        const maxValue = Math.max(...values);
        options.yaxis = {
            max: maxValue * 1.15,
            title: {
                text: `Pemakaian (${unit})`
            },
            labels: {
                style: {
                    fontSize: '12px',
                    colors: '#8c9097'
                }
            }
        };

        options.tooltip = {
            y: {
                formatter: val => `${val} ${unit}`
            }
        };

        options.plotOptions = {
            bar: {
                columnWidth: '60%',
                borderRadius: 8,
                dataLabels: {
                    position: 'top'
                }
            }
        };

        options.legend = {
            position: 'bottom',
            horizontalAlign: 'center',
            fontSize: 12,
            markers: {
                radius: 4,
                width: 12,
                height: 12
            }
        };

        if (chartInstances[key]) {
            chartInstances[key].updateOptions(options, false, false);
        } else {
            chartInstances[key] = new ApexCharts(document.querySelector(selector), options);
            chartInstances[key].render();
        }
    }

    // Helper function to format date
    function formatDate(dateStr) {
        const date = new Date(dateStr);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
    }

    // Function to load available weeks filtered by month
    function loadAvailableWeeks(selectedMonth = null) {
        // Jika tidak ada selectedMonth, gunakan bulan yang sedang dipilih
        if (!selectedMonth) {
            selectedMonth = $('#filter_kpi_monthly').val();
        }

        $.getJSON('{{ url("api/utility/kpi/listrik/weeks") }}', function(data) {
            const select = $('#filter_kpi_weekly');
            select.empty();
            select.append('<option value="">Pilih Minggu...</option>');

            // Filter weeks berdasarkan bulan yang dipilih
            const filteredWeeks = data.filter(week => {
                const weekMonth = week.start_date.substring(0, 7); // Format: YYYY-MM
                return weekMonth === selectedMonth;
            });

            if (filteredWeeks.length === 0) {
                select.append('<option value="" disabled>Tidak ada data mingguan di bulan ini</option>');
            } else {
                filteredWeeks.forEach(week => {
                    const label = `${formatDate(week.start_date)} - ${formatDate(week.end_date)}`;
                    select.append(`<option value="${week.id}">${label}</option>`);
                });
            }
        }).fail(function() {
            console.error('Gagal memuat data mingguan');
            alert('Gagal memuat daftar minggu dari database');
        });
    }

    // Fetch KPI Data - Updated version
    function fetchKPIListrik(filterType = null, filterValue = null) {
        showLoading('loading-kpi');

        let url = '{{ url("api/utility/kpi/listrik") }}';
        const params = [];

        if (filterType && filterValue) {
            params.push(`filter_type=${filterType}`);
            params.push(`filter_value=${filterValue}`);
        }

        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        $.getJSON(url, function(data) {
            // Update periode display (bisa ditampilkan di UI jika diperlukan)
            console.log('Periode:', data.periode.display);
            console.log('Has KPI Data:', data.periode.has_kpi_data);

            // Check if KPI data is available
            const hasKpiData = data.periode.has_kpi_data;

            if (hasKpiData) {
                // Sembunyikan alert dan tampilkan KPI cards
                $('#no-kpi-alert').hide();
                $('.kpi-cards-container').show();

                // Update values - KPI data tersedia
                $('#kpi_finish_goods').text(parseFloat(data.kpi_data.finish_goods).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                $('#kpi_kecap_matang').text(parseFloat(data.kpi_data.kecap_matang).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                $('#kpi_total_listrik_produksi').text((parseFloat(data.listrik.total_produksi) * 1000).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                $('#kpi_total_listrik_bas').text((parseFloat(data.listrik.total_bas)*1000).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                // KPI Hasil Produksi: 510 Kwh / 10 ton FG
                const kpiProduksi = (parseFloat(data.kpi.listrik_produksi)*1000).toFixed(4);
                $('#kpi_hasil_produksi').text(kpiProduksi + ' Kwh/10 ton FG');

                // Calculate percentage for progress bar (target = 51)
                const targetProduksi = 51;
                const percentageProduksi = Math.min((kpiProduksi / targetProduksi) * 100, 100);
                $('#kpi_progress_produksi').css('width', percentageProduksi + '%');

                // Change color based on achievement
                if (kpiProduksi <= targetProduksi) {
                    $('#kpi_progress_produksi').removeClass('bg-danger').addClass('bg-success');
                    $('#kpi_hasil_produksi').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#kpi_progress_produksi').removeClass('bg-success').addClass('bg-danger');
                    $('#kpi_hasil_produksi').removeClass('text-success').addClass('text-danger');
                }

                // KPI Hasil BAS: 75 Kwh/ton kecap matang
                const kpiBas = (parseFloat(data.kpi.listrik_bas)*1000).toFixed(4);
                $('#kpi_hasil_bas').text(kpiBas + ' Kwh/ton');

                // Calculate percentage for progress bar (target = 75)
                const targetBas = 75;
                const percentageBas = Math.min((kpiBas / targetBas) * 100, 100);
                $('#kpi_progress_bas').css('width', percentageBas + '%');

                // Change color based on achievement
                if (kpiBas <= targetBas) {
                    $('#kpi_progress_bas').removeClass('bg-danger').addClass('bg-success');
                    $('#kpi_hasil_bas').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#kpi_progress_bas').removeClass('bg-success').addClass('bg-danger');
                    $('#kpi_hasil_bas').removeClass('text-success').addClass('text-danger');
                }
            } else {
                // Tampilkan alert dan sembunyikan KPI cards
                $('.kpi-cards-container').hide();
                $('#no-kpi-alert').show();

                // Update periode di alert
                $('#alert-periode').text(data.periode.display);
            }

            $('#kpi-content').fadeIn(300);
        }).fail(function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Gagal memuat data KPI';
            alert(errorMsg);
        }).always(function() {
            hideLoading('loading-kpi');
        });
    }

    function fetchListrik(start, end) {
        showLoading('loading-listrik');
        $.getJSON(`{{ url('api/utility/top5/listrik') }}?start_date=${start}&end_date=${end}`, data => {
            $('#mdp_listrik').text(data.find(d => d.panel_type === 'MDP') ? data.find(d => d.panel_type === 'MDP').total_usage + ' mWh' : '0 mWh');
            const filteredData = data.filter(d => d.panel_type !== 'MDP');
            renderBar(filteredData.map(d => d.panel_type), filteredData.map(d => +d.total_usage),
                "#pemakaian-listrik-chart", 'listrik', 'mWh', '#F59E0B');
        }).always(() => hideLoading('loading-listrik'));
    }

    // Trend chart setup
    function setupTrend(selector, url, ySuffix, key, loadingId) {
        const options = baseOptions('line');
        options.chart.height = 350;
        options.series = [];
        options.xaxis.type = 'datetime';
        options.yaxis.title = {
            text: `Pemakaian (${ySuffix})`
        };
        options.tooltip = {
            x: {
                format: 'dd MMM yyyy'
            },
            y: {
                formatter: v => `${v} ${ySuffix}`
            }
        };

        const chart = new ApexCharts(document.querySelector(selector), options);
        chart.render();
        chartInstances[key] = chart;

        function fetchTrend(params = {}) {
            showLoading(loadingId);
            $.getJSON(url, params, data => {
                chart.updateSeries(data);
            }).always(() => hideLoading(loadingId));
        }

        fetchTrend(); // initial load
        return fetchTrend;
    }

    // Init
    $(function() {
        const today = new Date();
        const start = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().slice(0, 10);
        const end = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().slice(0, 10);
        const currentMonth = today.toISOString().slice(0, 7); // Format: YYYY-MM

        // Set default value for month filter
        $('#filter_kpi_monthly').val(currentMonth);

        // Load KPI data (default - will use monthly current or weekly latest)
        fetchKPIListrik();

        // Load available weeks for dropdown (filtered by current month)
        loadAvailableWeeks(currentMonth);

        // Load bar chart
        fetchListrik(start, end);

        // Trend chart
        const fetchTrendListrik = setupTrend("#pemakaian_listrik_chart", "{{ url('api/utility/trend-pemakaian-listrik') }}", "mWh", "trendListrik", "loading-trend-listrik");

        // ========== EVENT LISTENERS ==========

        // Toggle between monthly and weekly filter
        $('input[name="filter_type_kpi"]').on('change', function() {
            const filterType = $(this).val();

            if (filterType === 'monthly') {
                $('#monthly_filter_container').slideDown(200);
                $('#weekly_filter_container').slideUp(200);

                // Load monthly data
                const selectedMonth = $('#filter_kpi_monthly').val();
                if (selectedMonth) {
                    fetchKPIListrik('monthly', selectedMonth);
                }
            } else {
                $('#monthly_filter_container').slideUp(200);
                $('#weekly_filter_container').slideDown(200);

                // Auto-select first week if available
                const firstWeekValue = $('#filter_kpi_weekly option:eq(1)').val();
                if (firstWeekValue) {
                    $('#filter_kpi_weekly').val(firstWeekValue);
                    fetchKPIListrik('weekly', firstWeekValue);
                }
            }
        });

        // Event listener for monthly filter
        $('#filter_kpi_monthly').on('change', function() {
            const selectedMonth = $(this).val();
            if (selectedMonth) {
                // Update weekly dropdown berdasarkan bulan yang dipilih
                loadAvailableWeeks(selectedMonth);

                // Jika sedang di mode monthly, langsung fetch data
                const filterType = $('input[name="filter_type_kpi"]:checked').val();
                if (filterType === 'monthly') {
                    fetchKPIListrik('monthly', selectedMonth);
                }
            }
        });

        // Event listener for weekly filter
        $('#filter_kpi_weekly').on('change', function() {
            const selectedWeek = $(this).val();
            if (selectedWeek) {
                fetchKPIListrik('weekly', selectedWeek);
            }
        });

        // Event listener filter bulan trend
        $('#filter_bulan_listrik').on('change', function() {
            fetchTrendListrik({
                bulan: this.value
            });
        });

        // Event listener filter date-range
        $("#applyListrikRange").on("click", function() {
            const start = $("#startDateListrik").val();
            const end = $("#endDateListrik").val();
            if (!start || !end) {
                alert("Pilih tanggal awal & akhir!");
                return;
            }
            $("#selectedBulanListrik").text(`${start} s/d ${end}`);
            fetchListrik(start, end);
        });

        // Prevent dropdown from closing when clicking inside
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });

        // Close dropdown after apply button is clicked
        $('#applyListrikRange').on('click', function() {
            const dropdown = $(this).closest('.dropdown');
            dropdown.find('[data-bs-toggle="dropdown"]').dropdown('hide');
        });
    });
</script>
<!-- Enhanced CSS Styles -->
<style>
    :root {
        --warning-gradient: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        /* background-color: #F8FAFC; */
    }

    .page-content {
        animation: fadeInUp 0.6s ease-out;
    }

    .bg-gradient-warning {
        background: var(--warning-gradient);
    }

    /* Enhanced Card Styles */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        /* background: #FFFFFF; */
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .stat-card {
        background: linear-gradient(135deg, #FFFFFF 0%, #FEF3C7 100%);
        border-left: 4px solid #F59E0B;
    }

    /* .chart-card {
        background: #FFFFFF;
    } */

    .trend-card .card-header {
        border-radius: 16px 16px 0 0;
        padding: 1.5rem;
    }

    /* KPI Card Styles */
    .kpi-card {
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    /* Enhanced Loading States */
    .chart-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(4px);
        z-index: 10;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .spinner-grow {
        animation-duration: 1.5s;
    }

    /* Enhanced Buttons */
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .btn:hover {
        transform: translateY(-2px);
    }


    /* Enhanced Dropdowns */
    .dropdown-menu {
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border-radius: 12px;
        padding: 1rem;
        backdrop-filter: blur(16px);
        background: rgba(255, 255, 255, 0.95);
    }

    .dropdown-toggle::after {
        transition: transform 0.2s ease;
    }

    .dropdown[aria-expanded="true"] .dropdown-toggle::after {
        transform: rotate(180deg);
    }

    /* Enhanced Form Controls */
    .form-control {
        border-radius: 8px;
        border: 2px solid #E2E8F0;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .form-control:focus {
        border-color: #F59E0B;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        transform: scale(1.02);
    }

    /* Enhanced Breadcrumbs */
    .breadcrumb {
        background: transparent;
        margin: 0;
    }

    .breadcrumb-item a {
        text-decoration: none;
        transition: opacity 0.2s ease;
    }

    .breadcrumb-item a:hover {
        opacity: 0.8;
    }

    /* Enhanced Avatar */
    .avatar-sm {
        width: 40px;
        height: 40px;
        font-size: 14px;
    }

    /* Progress Bar */
    .progress {
        background-color: #E5E7EB;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        transition: width 0.6s ease;
    }

    .progress-bar.bg-success {
        background: linear-gradient(90deg, #10B981 0%, #059669 100%);
    }

    .progress-bar.bg-danger {
        background: linear-gradient(90deg, #EF4444 0%, #DC2626 100%);
    }

    .progress-bar.bg-warning {
        background: linear-gradient(90deg, #F59E0B 0%, #D97706 100%);
    }

    .progress-bar.bg-primary {
        background: linear-gradient(90deg, #3B82F6 0%, #2563EB 100%);
    }

    /* Enhanced Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #F1F5F9;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 4px;
        transition: background 0.2s ease;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }

    /* Enhanced Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Responsive Enhancements */
    @media (max-width: 768px) {
        .card {
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .page-title-box {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }

        .dropdown-menu {
            min-width: 280px !important;
        }

        .apex-charts {
            height: 300px !important;
        }

        .kpi-card {
            margin-bottom: 1rem;
        }

        .kpi-card .row.g-3 {
            gap: 0.5rem !important;
        }

        .alert {
            font-size: 0.875rem;
        }
    }

    /* Utility Classes */
    .tracking-wide {
        letter-spacing: 0.05em;
    }

    /* Badge Styles */
    .badge {
        padding: 0.35rem 0.65rem;
        font-weight: 500;
        border-radius: 6px;
    }

    .badge.bg-info-subtle {
        background-color: #E0F2FE;
        color: #0369A1;
    }

    .badge.bg-success-subtle {
        background-color: #D1FAE5;
        color: #059669;
    }

    .badge.bg-warning-subtle {
        background-color: #FEF3C7;
        color: #D97706;
    }

    /* Alert Styles */
    .alert-info {
        background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
        color: #1E40AF;
        border-left: 4px solid #3B82F6;
    }

    .alert-heading {
        color: #1E3A8A;
    }

    code {
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    /* KPI Text Colors */
    .text-success {
        color: #059669 !important;
    }

    .text-danger {
        color: #DC2626 !important;
    }

    .text-warning {
        color: #D97706 !important;
    }

    .text-primary {
        color: #2563EB !important;
    }

    /* Card Header Gradients */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
    }

    /* Shadow Utilities */
    .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .shadow-lg {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Smooth Transitions */
    * {
        transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
    }

    /* Loading Animation */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .spinner-grow {
        animation: spinner-grow 1.5s linear infinite;
    }

    @keyframes spinner-grow {
        0% {
            transform: scale(0);
            opacity: 0;
        }

        50% {
            opacity: 1;
        }

        100% {
            transform: scale(1);
            opacity: 0;
        }
    }

    /* Hover Effects */
    .card-header:hover {
        opacity: 0.95;
    }

    /* Print Styles */
    @media print {

        .btn,
        .dropdown,
        .breadcrumb {
            display: none;
        }

        .card {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>

@endsection