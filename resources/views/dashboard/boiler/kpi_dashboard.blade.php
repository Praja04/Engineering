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

            {{-- <div class="row">
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
            </div> --}}

            <!-- CARD STEAM / FG -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Steam / Finish Goods</h5>

                            <div class="d-flex align-items-center gap-3">
                                <!-- Tabs -->
                                <ul class="nav nav-tabs nav-tabs-custom nav-tabs-small" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#steam-weekly"
                                            role="tab">Weekly</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#steam-monthly"
                                            role="tab">Monthly</a>
                                    </li>
                                </ul>

                                <!-- Filter Container -->
                                <div class="dropdown">
                                    <a href="#" class="btn btn-soft-primary dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-filter-alt"></i> Filter
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 300px;">
                                        <!-- Filter Weekly -->
                                        <div id="filter-steam-weekly" class="filter-group">
                                            <h6 class="fw-bold mb-3">Filter Weekly</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Start Date</label>
                                                <input type="date" id="startSteamWeekly" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">End Date</label>
                                                <input type="date" id="endSteamWeekly" class="form-control">
                                            </div>
                                        </div>

                                        <!-- Filter Monthly -->
                                        <div id="filter-steam-monthly" class="filter-group" style="display: none;">
                                            <h6 class="fw-bold mb-3">Filter Monthly</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Tahun</label>
                                                <select id="yearSteamMonthly" class="form-control"></select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Bulan</label>
                                                <select id="monthSteamMonthly" class="form-control">
                                                    <option value="">Semua Bulan</option>
                                                    <option value="01">Januari</option>
                                                    <option value="02">Februari</option>
                                                    <option value="03">Maret</option>
                                                    <option value="04">April</option>
                                                    <option value="05">Mei</option>
                                                    <option value="06">Juni</option>
                                                    <option value="07">Juli</option>
                                                    <option value="08">Agustus</option>
                                                    <option value="09">September</option>
                                                    <option value="10">Oktober</option>
                                                    <option value="11">November</option>
                                                    <option value="12">Desember</option>
                                                </select>
                                            </div>
                                        </div>

                                        <button class="btn btn-primary w-100 mt-3" id="applyFilterSteam">Terapkan
                                            Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="steam-weekly">
                                    <div id="chartSteamWeekly" style="height: 380px;"></div>
                                </div>
                                <div class="tab-pane" id="steam-monthly">
                                    <div id="chartSteamMonthly" style="height: 380px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD BATU BARA / FG (sama persis, hanya ganti nama ID) -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Batu Bara / Finish Goods</h5>

                            <div class="d-flex align-items-center gap-3">
                                <ul class="nav nav-tabs nav-tabs-custom nav-tabs-small" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#bb-weekly"
                                            role="tab">Weekly</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#bb-monthly"
                                            role="tab">Monthly</a>
                                    </li>
                                </ul>

                                <div class="dropdown">
                                    <a href="#" class="btn btn-soft-warning dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-filter-alt"></i> Filter
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 300px;">
                                        <div id="filter-bb-weekly" class="filter-group">
                                            <h6 class="fw-bold mb-3">Filter Weekly</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Start Date</label>
                                                <input type="date" id="startBBWeekly" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">End Date</label>
                                                <input type="date" id="endBBWeekly" class="form-control">
                                            </div>
                                        </div>

                                        <div id="filter-bb-monthly" class="filter-group" style="display: none;">
                                            <h6 class="fw-bold mb-3">Filter Monthly</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Tahun</label>
                                                <select id="yearBBMonthly" class="form-control"></select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Bulan</label>
                                                <select id="monthBBMonthly" class="form-control">
                                                    <option value="">Semua Bulan</option>
                                                    <option value="01">Januari</option>
                                                    <option value="02">Februari</option>
                                                    <option value="03">Maret</option>
                                                    <option value="04">April</option>
                                                    <option value="05">Mei</option>
                                                    <option value="06">Juni</option>
                                                    <option value="07">Juli</option>
                                                    <option value="08">Agustus</option>
                                                    <option value="09">September</option>
                                                    <option value="10">Oktober</option>
                                                    <option value="11">November</option>
                                                    <option value="12">Desember</option>
                                                </select>
                                            </div>
                                        </div>

                                        <button class="btn btn-primary w-100 mt-3" id="applyFilterBB">Terapkan
                                            Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="bb-weekly">
                                    <div id="chartBBWeekly" style="height: 380px;"></div>
                                </div>
                                <div class="tab-pane" id="bb-monthly">
                                    <div id="chartBBMonthly" style="height: 380px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/stock/highstock.js"></script>
    <script>
        $(document).ready(function() {
            let currentStartDate = null;
            let currentEndDate = null;
            populateYearSelect('yearSteamMonthly');
            populateYearSelect('yearBBMonthly');
            fetchBBSteamData();
            // fetchSteamFgData();
            // fetchBBFgData();
            // loadCurrentTabData();
            loadSteamWeekly();
            loadBBWeekly();

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

                    // Format label minggu: "6-12 Jan 2025" atau "Minggu 6-12 Jan"
                    const categories = data.map(item => {
                        const start = new Date(item.week_start);
                        const end = new Date(item.week_end);
                        const options = {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        };
                        const startStr = start.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short'
                        });
                        const endStr = end.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                        return `${startStr} - ${endStr}`;
                    });

                    const rasioValues = data.map(item => parseFloat(item.rasio) || 0);

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
                                text: 'Periode Minggu'
                            },
                            labels: {
                                rotation: -45,
                                style: {
                                    fontSize: '11px'
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Rasio (Kg Steam / Ton FG)'
                            },
                            labels: {
                                formatter: function() {
                                    return Highcharts.numberFormat(this.value, 0, '.', ',');
                                }
                            }
                        },
                        tooltip: {
                            shared: true,
                            useHTML: true,
                            formatter: function() {
                                const i = this.points[0].point.index;
                                const row = data[i];

                                const start = new Date(row.week_start).toLocaleDateString(
                                    'id-ID', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                    });
                                const end = new Date(row.week_end).toLocaleDateString('id-ID', {
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric'
                                });

                                return `
                                    <b>Minggu: ${start} - ${end}</b><br/>
                                    Total Steam: <b>${Highcharts.numberFormat(row.steam, 1, '.', ',')} Kg</b><br/>
                                    Finish Goods: <b>${Highcharts.numberFormat(row.finish_goods, 0, '.', ',')} Ton</b><br/>
                                    Rasio: <b>${Highcharts.numberFormat(row.rasio, 2, '.', ',')} Kg/Ton Kecap</b>
                                `;
                            }
                        },
                        series: [{
                            name: 'Rasio Steam/FG',
                            data: rasioValues,
                            color: '#457b9d',
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    return Highcharts.numberFormat(this.y, 0, '.', ',');
                                },
                                style: {
                                    fontWeight: 'bold',
                                    fontSize: '11px',
                                    color: '#1d3557'
                                }
                            }
                        }],
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.1,
                                borderWidth: 0
                            }
                        }
                    });
                }).fail(function() {
                    alert('Gagal mengambil data Steam/FG');
                });
            }

            function fetchBBFgData(startDate = null, endDate = null) {
                $.getJSON("{{ url('api/boiler/dashboard/bbfg') }}", {
                    start_date: startDate,
                    end_date: endDate
                }, function(res) {
                    const data = res.data || [];

                    const categories = data.map(item => {
                        const start = new Date(item.week_start);
                        const end = new Date(item.week_end);
                        const startStr = start.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short'
                        });
                        const endStr = end.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                        return `${startStr} - ${endStr}`;
                    });

                    const rasioValues = data.map(item => parseFloat(item.rasio) || 0);

                    Highcharts.chart('charBBFg', { // pastikan ID chart-nya benar, mungkin 'chartBBFg'?
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        xAxis: {
                            categories: categories,
                            title: {
                                text: 'Periode Minggu'
                            },
                            labels: {
                                rotation: -45,
                                style: {
                                    fontSize: '11px'
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Rasio (Kg Batu Bara / Ton FG)'
                            },
                            labels: {
                                formatter: function() {
                                    return Highcharts.numberFormat(this.value, 0, '.', ',');
                                }
                            }
                        },
                        tooltip: {
                            shared: true,
                            useHTML: true,
                            formatter: function() {
                                const i = this.points[0].point.index;
                                const row = data[i];

                                const start = new Date(row.week_start).toLocaleDateString(
                                    'id-ID', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                    });
                                const end = new Date(row.week_end).toLocaleDateString('id-ID', {
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric'
                                });

                                return `
                                    <b>Minggu: ${start} - ${end}</b><br/>
                                    Total Batu Bara: <b>${Highcharts.numberFormat(row.batu_bara, 1, '.', ',')} Kg</b><br/>
                                    Finish Goods: <b>${Highcharts.numberFormat(row.finish_goods, 0, '.', ',')} Ton</b><br/>
                                    Rasio: <b>${Highcharts.numberFormat(row.rasio, 2, '.', ',')} Kg/Ton Kecap</b>
                                `;
                            }
                        },
                        series: [{
                            name: 'Rasio Batu Bara/FG',
                            data: rasioValues,
                            color: '#f78104',
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    return Highcharts.numberFormat(this.y, 0, '.', ',');
                                },
                                style: {
                                    fontWeight: 'bold',
                                    fontSize: '11px',
                                    color: '#c43a17'
                                }
                            }
                        }],
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.1,
                                borderWidth: 0
                            }
                        }
                    });
                }).fail(function() {
                    alert('Gagal mengambil data Batu Bara/FG');
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

            function formatWeekLabel(start, end) {
                const s = new Date(start).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                });
                const e = new Date(end).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });
                return `${s} - ${e}`;
            }

            function formatMonthLabel(month) {
                const date = new Date(month + '-01');
                return date.toLocaleDateString('id-ID', {
                    month: 'short',
                    year: 'numeric'
                });
            }

            // Load Steam Weekly
            function loadSteamWeekly(start = null, end = null) {
                $.getJSON("{{ url('api/boiler/dashboard/steamfg') }}", {
                    start_date: start,
                    end_date: end
                }, function(res) {
                    const data = res.data || [];
                    const categories = data.map(item => formatWeekLabel(item.week_start, item.week_end));
                    const rasioValues = data.map(item => parseFloat(item.rasio) || 0);

                    Highcharts.chart('chartSteamWeekly', {
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
                            labels: {
                                rotation: -45,
                                style: {
                                    fontSize: '11px'
                                }
                            }
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
                                    <b>${row.week_start} s/d ${row.week_end}</b><br/>
                                    Steam: <b>${Highcharts.numberFormat(row.steam, 1, '.', ',')}</b><br/>
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

            // Load Steam Monthly
            function loadSteamMonthly(start = null, end = null) {
                $.getJSON("{{ url('api/boiler/dashboard/steamfg-monthly') }}", {
                    start_date: start,
                    end_date: end
                }, function(res) {
                    const data = res.data || [];
                    const categories = data.map(item => formatMonthLabel(item.month));
                    const rasioValues = data.map(item => parseFloat(item.rasio) || 0);

                    Highcharts.chart('chartSteamMonthly', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        xAxis: {
                            categories: categories,
                            title: {
                                text: 'Bulan'
                            }
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
                                    <b>${formatMonthLabel(row.month)}</b><br/>
                                    Steam: <b>${Highcharts.numberFormat(row.steam, 1, '.', ',')}</b><br/>
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

            // Load Batu Bara Weekly
            function loadBBWeekly(start = null, end = null) {
                $.getJSON("{{ url('api/boiler/dashboard/bbfg') }}", {
                    start_date: start,
                    end_date: end
                }, function(res) {
                    const data = res.data || [];
                    const categories = data.map(item => formatWeekLabel(item.week_start, item.week_end));
                    const rasioValues = data.map(item => parseFloat(item.rasio) || 0);

                    Highcharts.chart('chartBBWeekly', {
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
                            labels: {
                                rotation: -45,
                                style: {
                                    fontSize: '11px'
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Rasio (Batu Bara / FG x 1000)'
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
                                <b>${row.week_start} s/d ${row.week_end}</b><br/>
                                Batu Bara: <b>${Highcharts.numberFormat(row.batu_bara, 1, '.', ',')}</b><br/>
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

            // Load Batu Bara Monthly
            function loadBBMonthly(start = null, end = null) {
                $.getJSON("{{ url('api/boiler/dashboard/bbfg-monthly') }}", {
                    start_date: start,
                    end_date: end
                }, function(res) {
                    const data = res.data || [];
                    const categories = data.map(item => formatMonthLabel(item.month));
                    const rasioValues = data.map(item => parseFloat(item.rasio) || 0);

                    Highcharts.chart('chartBBMonthly', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        xAxis: {
                            categories: categories,
                            title: {
                                text: 'Bulan'
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Rasio (Batu Bara / FG x 1000)'
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
                                    <b>${formatMonthLabel(row.month)}</b><br/>
                                    Batu Bara: <b>${Highcharts.numberFormat(row.batu_bara, 1, '.', ',')}</b><br/>
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

            // Tab Steam
            $('a[data-bs-toggle="tab"][href="#steam-weekly"]').on('shown.bs.tab', () => loadSteamWeekly($(
                '#startSteam').val(), $('#endSteam').val()));
            $('a[data-bs-toggle="tab"][href="#steam-monthly"]').on('shown.bs.tab', () => loadSteamMonthly($(
                '#startSteam').val(), $('#endSteam').val()));

            // Tab Batu Bara
            $('a[data-bs-toggle="tab"][href="#bb-weekly"]').on('shown.bs.tab', () => loadBBWeekly($('#startBB')
                .val(), $('#endBB').val()));
            $('a[data-bs-toggle="tab"][href="#bb-monthly"]').on('shown.bs.tab', () => loadBBMonthly($('#startBB')
                .val(), $('#endBB').val()));

            // Apply Filter Steam
            $('#applyFilterSteam').on('click', function() {
                if ($('#steam-weekly').hasClass('active')) {
                    const start = $('#startSteamWeekly').val();
                    const end = $('#endSteamWeekly').val();
                    loadSteamWeekly(start, end);
                } else {
                    const year = $('#yearSteamMonthly').val();
                    const month = $('#monthSteamMonthly').val();
                    const start = month ? `${year}-${month}-01` : `${year}-01-01`;
                    const end = month ?
                        new Date(year, month, 0).toISOString().slice(0, 10) :
                        `${year}-12-31`;
                    loadSteamMonthly(start, end);
                }
            });

            // Apply Filter Batu Bara
            $('#applyFilterBB').on('click', function() {
                if ($('#bb-weekly').hasClass('active')) {
                    const start = $('#startBBWeekly').val();
                    const end = $('#endBBWeekly').val();
                    loadBBWeekly(start, end);
                } else {
                    const year = $('#yearBBMonthly').val();
                    const month = $('#monthBBMonthly').val();
                    const start = month ? `${year}-${month}-01` : `${year}-01-01`;
                    const end = month ?
                        new Date(year, month, 0).toISOString().slice(0, 10) :
                        `${year}-12-31`;
                    loadBBMonthly(start, end);
                }
            });

            function populateYearSelect(selectId) {
                const currentYear = new Date().getFullYear();
                const startYear = currentYear - 5;
                const select = document.getElementById(selectId);

                for (let year = currentYear; year >= startYear; year--) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    select.appendChild(option);
                }
                select.value = currentYear; // default tahun ini
            }

            function setupTabFilterSwitch(tabPrefix) {
                $(`a[data-bs-toggle="tab"][href="#${tabPrefix}-weekly"]`).on('shown.bs.tab', function() {
                    $(`#filter-${tabPrefix}-weekly`).show();
                    $(`#filter-${tabPrefix}-monthly`).hide();
                });

                $(`a[data-bs-toggle="tab"][href="#${tabPrefix}-monthly"]`).on('shown.bs.tab', function() {
                    $(`#filter-${tabPrefix}-weekly`).hide();
                    $(`#filter-${tabPrefix}-monthly`).show();
                });
            }

            setupTabFilterSwitch('steam');
            setupTabFilterSwitch('bb');
        });
    </script>
@endsection
