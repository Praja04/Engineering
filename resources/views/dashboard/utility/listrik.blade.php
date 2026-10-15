@extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Header with Gradient Background -->
            <div class="row">
                <div class="col-12">
                    <div
                        class="page-title-box d-sm-flex align-items-center justify-content-between bg-gradient-warning p-4 rounded-3 mb-4 text-white shadow-lg">
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
                            <button class="btn btn-warning btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside">
                                <i class="ri-calendar-line me-1"></i>
                                <span id="selectedBulanListrik">Pilih Periode</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3 border-0 shadow-lg" style="min-width: 300px;">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                                    <input type="date" id="startDateListrik" class="form-control form-control-sm"
                                        onclick="event.stopPropagation();">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                                    <input type="date" id="endDateListrik" class="form-control form-control-sm"
                                        onclick="event.stopPropagation();">
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
                                    <input type="month" id="filter_bulan_listrik"
                                        class="form-control form-control-sm bg-white" style="width: 140px;">
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


            <!-- Chart KPI Listrik Weekly Section -->
            <div class="row g-4 mt-2">
                <!-- Chart: Listrik PRD / 10 Ton FG - Weekly -->
                <div class="col-xl-6">
                    <div class="card trend-card border-0 shadow-sm">
                        <div class="card-header bg-gradient-warning text-white border-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-1 text-white fw-semibold">KPI Listrik PRD (Mingguan)</h5>
                                    <p class="mb-0 opacity-75 small">(Listrik PRD ÷ Finish Goods) × 10 — Mingguan</p>
                                </div>
                                <div>
                                    <input type="number" id="filter_year_weekly_prd"
                                        class="form-control form-control-sm bg-white" style="width: 90px;" min="2020"
                                        max="2099">
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0" style="position: relative; min-height: 350px;">
                            <div class="chart-loading" id="loading-chart-weekly-prd">
                                <div class="text-center p-5">
                                    <div class="spinner-grow text-warning" role="status"></div>
                                    <p class="mt-3 text-muted mb-0">Memuat data...</p>
                                </div>
                            </div>
                            <div id="chart_weekly_listrik_prd" class="apex-charts p-3"></div>
                        </div>
                    </div>
                </div>

                <!-- Chart: Listrik BAS / Kecap Matang - Weekly -->
                <div class="col-xl-6">
                    <div class="card trend-card border-0 shadow-sm">
                        <div class="card-header bg-gradient-warning text-white border-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-1 text-white fw-semibold">KPI Listrik BAS (Mingguan)</h5>
                                    <p class="mb-0 opacity-75 small">Listrik BAS ÷ Kecap Matang — Mingguan</p>
                                </div>
                                <div>
                                    <input type="number" id="filter_year_weekly_bas"
                                        class="form-control form-control-sm bg-white" style="width: 90px;" min="2020"
                                        max="2099">
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0" style="position: relative; min-height: 350px;">
                            <div class="chart-loading" id="loading-chart-weekly-bas">
                                <div class="text-center p-5">
                                    <div class="spinner-grow text-warning" role="status"></div>
                                    <p class="mt-3 text-muted mb-0">Memuat data...</p>
                                </div>
                            </div>
                            <div id="chart_weekly_listrik_bas" class="apex-charts p-3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart KPI Listrik Monthly Section -->
            <div class="row g-4 mt-2">
                <!-- Chart 5: Listrik PRD / 10 Ton FG -->
                <div class="col-xl-6">
                    <div class="card trend-card border-0 shadow-sm">
                        <div class="card-header bg-gradient-warning text-white border-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-1 text-white fw-semibold">KPI Listrik PRD (Bulanan)</h5>
                                    <p class="mb-0 opacity-75 small">(Listrik PRD ÷ Finish Goods) × 10 — Bulanan</p>
                                </div>
                                <div>
                                    <input type="number" id="filter_year_prd_fg"
                                        class="form-control form-control-sm bg-white" style="width: 90px;" min="2020"
                                        max="2099">
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0" style="position: relative; min-height: 350px;">
                            <div class="chart-loading" id="loading-chart-prd-fg">
                                <div class="text-center p-5">
                                    <div class="spinner-grow text-warning" role="status"></div>
                                    <p class="mt-3 text-muted mb-0">Memuat data...</p>
                                </div>
                            </div>
                            <div id="chart_listrik_prd_fg" class="apex-charts p-3"></div>
                        </div>
                    </div>
                </div>

                <!-- Chart 6: Listrik BAS / Kecap Matang -->
                <div class="col-xl-6">
                    <div class="card trend-card border-0 shadow-sm">
                        <div class="card-header bg-gradient-warning text-white border-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-1 text-white fw-semibold">KPI Listrik BAS (Bulanan)</h5>
                                    <p class="mb-0 opacity-75 small">Listrik BAS ÷ Kecap Matang — Bulanan</p>
                                </div>
                                <div>
                                    <input type="number" id="filter_year_bas_kecap"
                                        class="form-control form-control-sm bg-white" style="width: 90px;" min="2020"
                                        max="2099">
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0" style="position: relative; min-height: 350px;">
                            <div class="chart-loading" id="loading-chart-bas-kecap">
                                <div class="text-center p-5">
                                    <div class="spinner-grow text-warning" role="status"></div>
                                    <p class="mt-3 text-muted mb-0">Memuat data...</p>
                                </div>
                            </div>
                            <div id="chart_listrik_bas_kecap" class="apex-charts p-3"></div>
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



        function fetchListrik(start, end) {
            showLoading('loading-listrik');
            $.getJSON(`{{ url('api/utility/top5/listrik') }}?start_date=${start}&end_date=${end}`, data => {
                $('#mdp_listrik').text(data.find(d => d.panel_type === 'MDP') ? data.find(d => d.panel_type ===
                    'MDP').total_usage + ' mWh' : '0 mWh');
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



            // Load bar chart
            fetchListrik(start, end);

            // Trend chart
            const fetchTrendListrik = setupTrend("#pemakaian_listrik_chart",
                "{{ url('api/utility/trend-pemakaian-listrik') }}", "mWh", "trendListrik",
                "loading-trend-listrik");



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

            // ========== CHART 5 & 6: KPI LISTRIK MONTHLY ==========

            function fetchChartPrdFg(year) {
                showLoading('loading-chart-prd-fg');
                $.getJSON(`/api/utility/kpi/chart/listrik-prd-fg?year=${year}`, function(res) {
                    const labels = res.data.map(d => d.bulan);
                    const values = res.data.map(d => d.nilai);
                    const target = 510;

                    const options = baseOptions('bar');
                    options.chart.height = 320;
                    options.series = [{
                        name: 'Listrik PRD / 10 Ton FG',
                        data: values
                    }];
                    options.xaxis.categories = labels;
                    options.colors = ['#F59E0B'];
                    options.yaxis = {
                        title: {
                            text: 'kWh / 10 Ton FG'
                        },
                        labels: {
                            style: {
                                fontSize: '12px',
                                colors: '#8c9097'
                            }
                        }
                    };
                    options.annotations = {
                        yaxis: [{
                            y: target,
                            borderColor: '#EF4444',
                            strokeDashArray: 4,
                            label: {
                                text: `Target: ${target}`,
                                style: {
                                    color: '#fff',
                                    background: '#EF4444',
                                    fontSize: '11px'
                                }
                            }
                        }]
                    };
                    options.dataLabels = {
                        enabled: true,
                        formatter: val => val.toFixed(2),
                        style: {
                            fontSize: '11px',
                            colors: ['#1F2937']
                        },
                        offsetY: -20
                    };
                    options.plotOptions = {
                        bar: {
                            columnWidth: '55%',
                            borderRadius: 6,
                            dataLabels: {
                                position: 'top'
                            },
                            colors: {
                                ranges: [{
                                    from: 0,
                                    to: target,
                                    color: '#10B981'
                                }, {
                                    from: target,
                                    to: 9999,
                                    color: '#EF4444'
                                }]
                            }
                        }
                    };
                    options.tooltip = {
                        y: {
                            formatter: val => `${val.toFixed(4)} kWh/10 Ton FG`
                        }
                    };

                    if (chartInstances['chartPrdFg']) {
                        chartInstances['chartPrdFg'].updateOptions(options, true, true);
                    } else {
                        chartInstances['chartPrdFg'] = new ApexCharts(document.querySelector(
                            '#chart_listrik_prd_fg'), options);
                        chartInstances['chartPrdFg'].render();
                    }
                }).fail(function() {
                    $('#chart_listrik_prd_fg').html(
                        '<p class="text-center text-muted p-4">Gagal memuat data.</p>');
                }).always(function() {
                    hideLoading('loading-chart-prd-fg');
                });
            }

            function fetchChartBasKecap(year) {
                showLoading('loading-chart-bas-kecap');
                $.getJSON(`/api/utility/kpi/chart/listrik-bas-kecap?year=${year}`, function(res) {
                    const labels = res.data.map(d => d.bulan);
                    const values = res.data.map(d => d.nilai);
                    const target = 75;

                    const options = baseOptions('bar');
                    options.chart.height = 320;
                    options.series = [{
                        name: 'Listrik BAS / Kecap Matang',
                        data: values
                    }];
                    options.xaxis.categories = labels;
                    options.colors = ['#3B82F6'];
                    options.yaxis = {
                        title: {
                            text: 'kWh / Ton'
                        },
                        labels: {
                            style: {
                                fontSize: '12px',
                                colors: '#8c9097'
                            }
                        }
                    };
                    options.annotations = {
                        yaxis: [{
                            y: target,
                            borderColor: '#EF4444',
                            strokeDashArray: 4,
                            label: {
                                text: `Target: ${target}`,
                                style: {
                                    color: '#fff',
                                    background: '#EF4444',
                                    fontSize: '11px'
                                }
                            }
                        }]
                    };
                    options.dataLabels = {
                        enabled: true,
                        formatter: val => val.toFixed(2),
                        style: {
                            fontSize: '11px',
                            colors: ['#1F2937']
                        },
                        offsetY: -20
                    };
                    options.plotOptions = {
                        bar: {
                            columnWidth: '55%',
                            borderRadius: 6,
                            dataLabels: {
                                position: 'top'
                            },
                            colors: {
                                ranges: [{
                                    from: 0,
                                    to: target,
                                    color: '#10B981'
                                }, {
                                    from: target,
                                    to: 9999,
                                    color: '#EF4444'
                                }]
                            }
                        }
                    };
                    options.tooltip = {
                        y: {
                            formatter: val => `${val.toFixed(4)} kWh/Ton`
                        }
                    };

                    if (chartInstances['chartBasKecap']) {
                        chartInstances['chartBasKecap'].updateOptions(options, true, true);
                    } else {
                        chartInstances['chartBasKecap'] = new ApexCharts(document.querySelector(
                            '#chart_listrik_bas_kecap'), options);
                        chartInstances['chartBasKecap'].render();
                    }
                }).fail(function() {
                    $('#chart_listrik_bas_kecap').html(
                        '<p class="text-center text-muted p-4">Gagal memuat data.</p>');
                }).always(function() {
                    hideLoading('loading-chart-bas-kecap');
                });
            }

            // ========== CHART: KPI LISTRIK WEEKLY ==========

            function fetchChartWeeklyPrd(year) {
                showLoading('loading-chart-weekly-prd');
                $.getJSON(`/api/utility/kpi/chart/weekly/listrik-prd-fg?year=${year}`, function(res) {
                    const labels = res.data.map(d => d.label);
                    const values = res.data.map(d => d.nilai);
                    const target = 510;

                    const options = baseOptions('bar');
                    options.chart.height = 320;
                    options.series = [{
                        name: 'Listrik PRD / 10 Ton FG (Weekly)',
                        data: values
                    }];
                    options.xaxis.categories = labels;
                    options.xaxis.labels = {
                        rotate: -45,
                        rotateAlways: labels.length > 8,
                        style: {
                            fontSize: '11px',
                            colors: '#8c9097'
                        }
                    };
                    options.colors = ['#F59E0B'];
                    options.yaxis = {
                        title: {
                            text: 'kWh / 10 Ton FG'
                        },
                        labels: {
                            style: {
                                fontSize: '12px',
                                colors: '#8c9097'
                            }
                        }
                    };
                    options.annotations = {
                        yaxis: [{
                            y: target,
                            borderColor: '#EF4444',
                            strokeDashArray: 4,
                            label: {
                                text: `Target: ${target}`,
                                style: {
                                    color: '#fff',
                                    background: '#EF4444',
                                    fontSize: '11px'
                                }
                            }
                        }]
                    };
                    options.dataLabels = {
                        enabled: true,
                        formatter: val => val.toFixed(2),
                        style: {
                            fontSize: '11px',
                            colors: ['#1F2937']
                        },
                        offsetY: -20
                    };
                    options.plotOptions = {
                        bar: {
                            columnWidth: '55%',
                            borderRadius: 6,
                            dataLabels: {
                                position: 'top'
                            },
                            colors: {
                                ranges: [{
                                    from: 0,
                                    to: target,
                                    color: '#10B981'
                                }, {
                                    from: target,
                                    to: 999999,
                                    color: '#EF4444'
                                }]
                            }
                        }
                    };
                    options.tooltip = {
                        custom: function({
                            series,
                            seriesIndex,
                            dataPointIndex,
                            w
                        }) {
                            const d = res.data[dataPointIndex];
                            if (!d) return '';
                            return `
                            <div class="p-2" style="background:#fff; border:1px solid #e2e8f0; border-radius:6px; font-size:12px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                                <div class="fw-bold text-dark mb-1">${d.periode} (${d.label})</div>
                                <div class="text-warning fw-semibold">Ratio: ${d.nilai.toFixed(2)} kWh/10 Ton FG</div>
                                <div class="text-muted small">Listrik PRD: ${Number(d.listrik_prd).toLocaleString('id-ID')} kWh</div>
                                <div class="text-muted small">Finish Goods: ${Number(d.finish_goods).toLocaleString('id-ID')} Ton</div>
                            </div>
                        `;
                        }
                    };

                    if (chartInstances['chartWeeklyPrd']) {
                        chartInstances['chartWeeklyPrd'].updateOptions(options, true, true);
                    } else {
                        chartInstances['chartWeeklyPrd'] = new ApexCharts(document.querySelector(
                            '#chart_weekly_listrik_prd'), options);
                        chartInstances['chartWeeklyPrd'].render();
                    }
                }).fail(function() {
                    $('#chart_weekly_listrik_prd').html(
                        '<p class="text-center text-muted p-4">Gagal memuat data.</p>');
                }).always(function() {
                    hideLoading('loading-chart-weekly-prd');
                });
            }

            function fetchChartWeeklyBas(year) {
                showLoading('loading-chart-weekly-bas');
                $.getJSON(`/api/utility/kpi/chart/weekly/listrik-bas-kecap?year=${year}`, function(res) {
                    const labels = res.data.map(d => d.label);
                    const values = res.data.map(d => d.nilai);
                    const target = 75;

                    const options = baseOptions('bar');
                    options.chart.height = 320;
                    options.series = [{
                        name: 'Listrik BAS / Kecap Matang (Weekly)',
                        data: values
                    }];
                    options.xaxis.categories = labels;
                    options.xaxis.labels = {
                        rotate: -45,
                        rotateAlways: labels.length > 8,
                        style: {
                            fontSize: '11px',
                            colors: '#8c9097'
                        }
                    };
                    options.colors = ['#3B82F6'];
                    options.yaxis = {
                        title: {
                            text: 'kWh / Ton'
                        },
                        labels: {
                            style: {
                                fontSize: '12px',
                                colors: '#8c9097'
                            }
                        }
                    };
                    options.annotations = {
                        yaxis: [{
                            y: target,
                            borderColor: '#EF4444',
                            strokeDashArray: 4,
                            label: {
                                text: `Target: ${target}`,
                                style: {
                                    color: '#fff',
                                    background: '#EF4444',
                                    fontSize: '11px'
                                }
                            }
                        }]
                    };
                    options.dataLabels = {
                        enabled: true,
                        formatter: val => val.toFixed(2),
                        style: {
                            fontSize: '11px',
                            colors: ['#1F2937']
                        },
                        offsetY: -20
                    };
                    options.plotOptions = {
                        bar: {
                            columnWidth: '55%',
                            borderRadius: 6,
                            dataLabels: {
                                position: 'top'
                            },
                            colors: {
                                ranges: [{
                                    from: 0,
                                    to: target,
                                    color: '#10B981'
                                }, {
                                    from: target,
                                    to: 999999,
                                    color: '#EF4444'
                                }]
                            }
                        }
                    };
                    options.tooltip = {
                        custom: function({
                            series,
                            seriesIndex,
                            dataPointIndex,
                            w
                        }) {
                            const d = res.data[dataPointIndex];
                            if (!d) return '';
                            return `
                            <div class="p-2" style="background:#fff; border:1px solid #e2e8f0; border-radius:6px; font-size:12px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                                <div class="fw-bold text-dark mb-1">${d.periode} (${d.label})</div>
                                <div class="text-primary fw-semibold">Ratio: ${d.nilai.toFixed(2)} kWh/Ton</div>
                                <div class="text-muted small">Listrik BAS: ${Number(d.listrik_bas).toLocaleString('id-ID')} kWh</div>
                                <div class="text-muted small">Kecap Matang: ${Number(d.kecap_matang).toLocaleString('id-ID')} Ton</div>
                            </div>
                        `;
                        }
                    };

                    if (chartInstances['chartWeeklyBas']) {
                        chartInstances['chartWeeklyBas'].updateOptions(options, true, true);
                    } else {
                        chartInstances['chartWeeklyBas'] = new ApexCharts(document.querySelector(
                            '#chart_weekly_listrik_bas'), options);
                        chartInstances['chartWeeklyBas'].render();
                    }
                }).fail(function() {
                    $('#chart_weekly_listrik_bas').html(
                        '<p class="text-center text-muted p-4">Gagal memuat data.</p>');
                }).always(function() {
                    hideLoading('loading-chart-weekly-bas');
                });
            }

            // Set default year & initial load
            const currentYear = today.getFullYear();
            $('#filter_year_prd_fg').val(currentYear);
            $('#filter_year_bas_kecap').val(currentYear);
            $('#filter_year_weekly_prd').val(currentYear);
            $('#filter_year_weekly_bas').val(currentYear);

            fetchChartPrdFg(currentYear);
            fetchChartBasKecap(currentYear);
            fetchChartWeeklyPrd(currentYear);
            fetchChartWeeklyBas(currentYear);

            // Filter year change
            $('#filter_year_prd_fg').on('change', function() {
                if ($(this).val()) fetchChartPrdFg($(this).val());
            });
            $('#filter_year_bas_kecap').on('change', function() {
                if ($(this).val()) fetchChartBasKecap($(this).val());
            });
            $('#filter_year_weekly_prd').on('change', function() {
                if ($(this).val()) fetchChartWeeklyPrd($(this).val());
            });
            $('#filter_year_weekly_bas').on('change', function() {
                if ($(this).val()) fetchChartWeeklyBas($(this).val());
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
