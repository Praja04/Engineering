@extends('layouts.app')

@section('title', 'Dashboard KPI Boiler')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Engineering</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="javascript: void(0);">Dashboards</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Engineering
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Batu Bara / Steam</h5>
                            <div class="dropdown">
                                <a href="#"
                                    class="dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 rounded text-white shadow-sm"
                                    id="dropdownFilter" data-bs-toggle="dropdown" aria-expanded="false"
                                    style="background-color: #83c5be">
                                    <i class="bx bx-filter-alt fs-5"></i>
                                    <span>Filter</span>
                                </a>


                                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-3 rounded-3"
                                    style="min-width: 280px;" aria-labelledby="dropdownFilter">

                                    <h6 class="fw-bold mb-3">Filter Data</h6>

                                    <div class="mb-3">
                                        <label for="bulanTerpal" class="form-label">Start Date</label>
                                        <input type="date" id="startDateBBSteam" class="form-control shadow-sm">
                                    </div>
                                    <div class="mb-3">
                                        <label for="bulanTerpal" class="form-label">End Date</label>
                                        <input type="date" id="endDateBBSteam" class="form-control shadow-sm">
                                    </div>

                                    <button class="btn btn-primary w-100 rounded-3 shadow-sm" id="filterBBSteam">
                                        <i class="bx bx-check-circle me-1"></i> Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chartMonthly" style="height: 350px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Steam / Finish Goods</h5>
                            <div class="dropdown">
                                <a href="#"
                                    class="dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 rounded text-white shadow-sm"
                                    id="dropdownFilter" data-bs-toggle="dropdown" aria-expanded="false"
                                    style="background-color: #457b9d">
                                    <i class="bx bx-filter-alt fs-5"></i>
                                    <span>Filter</span>
                                </a>


                                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-3 rounded-3"
                                    style="min-width: 280px;" aria-labelledby="dropdownFilter">

                                    <h6 class="fw-bold mb-3">Filter Data</h6>

                                    <div class="mb-3">
                                        <label for="startDateSteamFg" class="form-label">Start Date</label>
                                        <input type="date" id="startDateSteamFg" class="form-control shadow-sm">
                                    </div>
                                    <div class="mb-3">
                                        <label for="endDateSteamFg" class="form-label">End Date</label>
                                        <input type="date" id="endDateSteamFg" class="form-control shadow-sm">
                                    </div>

                                    <button class="btn btn-primary w-100 rounded-3 shadow-sm" id="filterSteamFg">
                                        <i class="bx bx-check-circle me-1"></i> Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chartSteamFg" style="height: 350px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Batu Bara / Finish Goods</h5>
                            <div class="dropdown">
                                <a href="#"
                                    class="dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 rounded text-white shadow-sm"
                                    id="dropdownFilter" data-bs-toggle="dropdown" aria-expanded="false"
                                    style="background-color: #f78104">
                                    <i class="bx bx-filter-alt fs-5"></i>
                                    <span>Filter</span>
                                </a>


                                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-3 rounded-3"
                                    style="min-width: 280px;" aria-labelledby="dropdownFilter">

                                    <h6 class="fw-bold mb-3">Filter Data</h6>

                                    <div class="mb-3">
                                        <label for="startDateBBFg" class="form-label">Start Date</label>
                                        <input type="date" id="startDateBBFg" class="form-control shadow-sm">
                                    </div>
                                    <div class="mb-3">
                                        <label for="endDateBBFg" class="form-label">End Date</label>
                                        <input type="date" id="endDateBBFg" class="form-control shadow-sm">
                                    </div>

                                    <button class="btn btn-primary w-100 rounded-3 shadow-sm" id="filterBBFg">
                                        <i class="bx bx-check-circle me-1"></i> Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="charBBFg" style="height: 350px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            fetchBBSteamData();
            fetchSteamFgData();
            fetchBBFgData();

            function formatMonthLabel(monthStr) {
                if (!monthStr) return monthStr;
                const parts = monthStr.split('-');
                if (parts.length < 2) return monthStr;
                const y = parseInt(parts[0], 10);
                const m = parseInt(parts[1], 10) - 1;
                const dt = new Date(y, m, 1);
                return dt.toLocaleString('default', {
                    month: 'short',
                    year: 'numeric'
                }); // e.g. "Nov 2025"
            }

            // Chart BB Steam
            function fetchBBSteamData(startDate = null, endDate = null) {
                $.getJSON("{{ url('api/boiler/dashboard/bbsteam') }}", {
                    start_date: startDate,
                    end_date: endDate
                }, function(res) {
                    const data = res.data || [];

                    const categories = data.map(item => item.date);

                    const rasioValues = data.map(item => parseFloat(item.rasio) || 0);

                    Highcharts.chart('chartMonthly', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        xAxis: {
                            categories: categories,
                            title: {
                                text: 'Tanggal'
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Rasio (BB/Steam x 1000)'
                            }
                        },
                        tooltip: {
                            shared: true,
                            useHTML: true,
                            formatter: function() {
                                const i = this.points[0].point.index;
                                const row = data[i];

                                return `
                                <b>${this.x}</b><br/>
                                Batu Bara: <b>${Highcharts.numberFormat(row.batu_bara, 1, '.', ',')} Ton</b><br/>
                                Steam: <b>${Highcharts.numberFormat(row.steam, 1, '.', ',')} m³</b><br/>
                                Rasio: <b>${Highcharts.numberFormat(row.rasio, 2, '.', ',')} Kg/Ton Kecap</b>
                            `;
                            }
                        },
                        series: [{
                            name: 'Rasio',
                            data: rasioValues,
                            color: '#83c5be',
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    return Highcharts.numberFormat(this.y, 2);
                                },
                                style: {
                                    fontWeight: 'bold',
                                    color: '#006d77',
                                }
                            }
                        }],
                        plotOptions: {
                            column: {
                                borderRadius: 6,
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        }
                    });
                });
            }

            // Chart Steam FG
            function fetchSteamFgData(startDate = null, endDate = null) {
                $.getJSON("{{ url('api/boiler/dashboard/steamfg') }}", {
                    start_date: startDate,
                    end_date: endDate
                }, function(res) {
                    const data = res.data || [];

                    const categories = data.map(m => m.date);
                    const rasioValues = data.map(m => parseFloat(m.rasio) || 0);

                    Highcharts.chart('chartSteamFg', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        xAxis: {
                            categories: categories,
                            title: {
                                text: 'Tanggal'
                            },
                        },
                        yAxis: {
                            title: {
                                text: 'Rasio (Steam / FG x 1000)'
                            },
                            labels: {
                                formatter: function() {
                                    return Highcharts.numberFormat(this.value, 2, '.', ',');
                                }
                            }
                        },
                        tooltip: {
                            shared: true,
                            useHTML: true,
                            formatter: function() {
                                const i = this.points[0].point.index;
                                const row = data[i];

                                return `
                                    <b>${row.date}</b><br/>
                                    Steam: <b>${Highcharts.numberFormat(row.steam, 1, '.', ',')} </b><br/>
                                    Finish Goods: <b>${Highcharts.numberFormat(row.finish_goods, 1, '.', ',')}</b><br/>
                                    Rasio: <b>${Highcharts.numberFormat(row.rasio, 4, '.', ',')} Kg/Ton Kecap</b>
                                `;
                            }
                        },
                        series: [{
                            name: 'Rasio',
                            data: rasioValues,
                            color: '#457b9d',
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    return Highcharts.numberFormat(this.y, 2, '.', ',');
                                },
                                style: {
                                    fontWeight: '600',
                                    fontSize: '11px',
                                    color: '#1d3557'
                                },
                                y: -5
                            }
                        }],

                        credits: {
                            enabled: false
                        }
                    });

                });
            }

            function fetchBBFgData(startDate = null, endDate = null) {
                $.getJSON("{{ url('api/boiler/dashboard/bbfg') }}", {
                    start_date: startDate,
                    end_date: endDate
                }, function(res) {

                    const data = Array.isArray(res.data) ? res.data : [];

                    const categories = data.map(item => formatMonthLabel(item.month));
                    const rasioValues = data.map(item => Number(item.rasio) || 0);

                    Highcharts.chart('charBBFg', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },

                        xAxis: {
                            categories: categories,
                            title: {
                                text: 'Month'
                            }
                        },

                        yAxis: {
                            title: {
                                text: 'Rasio'
                            },
                            labels: {
                                formatter: function() {
                                    return Highcharts.numberFormat(this.value, 2, '.', ',');
                                }
                            }
                        },
                        tooltip: {
                            shared: true,
                            useHTML: true,
                            formatter: function() {
                                const i = this.points[0].point.index;
                                const row = data[i];

                                return `
                                    <b>${row.date}</b><br/>
                                    Batu Bara: <b>${Highcharts.numberFormat(row.batu_bara, 1, '.', ',')} </b><br/>
                                    Finish Goods: <b>${Highcharts.numberFormat(row.finish_goods, 1, '.', ',')}</b><br/>
                                    Rasio: <b>${Highcharts.numberFormat(row.rasio, 4, '.', ',')} Kg/Ton Kecap</b>
                                `;
                            }
                        },
                        series: [{
                            name: 'Rasio',
                            data: rasioValues,
                            color: '#f78104',
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    return Highcharts.numberFormat(this.y, 4, '.', ',');
                                },
                                style: {
                                    fontWeight: '600',
                                    fontSize: '11px',
                                    color: '#c43a17'
                                },
                                y: -5
                            }
                        }],

                        credits: {
                            enabled: false
                        }
                    });

                });
            }

            $('#filterBBSteam').on('click', function() {
                const start = $('#startDateBBSteam').val();
                const end = $('#endDateBBSteam').val();

                fetchBBSteamData(start, end);
            });

            $('#filterSteamFg').on('click', function() {
                const start = $('#startDateSteamFg').val();
                const end = $('#endDateSteamFg').val();

                fetchSteamFgData(start, end);
            });

            $('#filterBBFg').on('click', function() {
                const start = $('#startDateBBFg').val();
                const end = $('#endDateBBFg').val();

                fetchBBFgData(start, end);
            });
        });
    </script>
@endsection
