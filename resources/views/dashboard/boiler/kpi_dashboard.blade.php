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

            {{-- <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Rasio Batu Bara / Steam (Mingguan)</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartWeekly" style="height: 350px;"></div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Rasio Batu Bara / Steam Monthly</h5>
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
                        <div class="card-header">
                            <h5 class="card-title mb-0">Rasio Steam / Finish Goods Monthly</h5>
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
                        <div class="card-header">
                            <h5 class="card-title mb-0">Rasio Batu Bara / Finish Goods Monthly</h5>
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
            $.getJSON("{{ url('api/boiler/dashboard/bbsteam') }}", function(res) {
                const data = res.data || [];
                const categories = data.map(m => formatMonthLabel(m.month));
                const rasioValues = data.map(m => parseFloat(m.rasio) || 0);

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
                            text: 'Month'
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Rasio'
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
                                Total Steam: <b>${Highcharts.numberFormat(row.total_steam, 1, '.', ',')} Ton</b><br/>
                                Total Batu Bara: <b>${Highcharts.numberFormat(row.total_batubara, 1, '.', ',')} Ton</b><br/>
                                Rasio: <b>${Highcharts.numberFormat(row.rasio, 2, '.', ',')} Ton</b>
                            `;
                        }
                    },
                    series: [{
                        name: 'Rasio',
                        data: rasioValues,

                        // Warna bar custom
                        color: '#83c5be',

                        // Data label
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

            // Chart Steam FG
            $.getJSON("{{ url('api/boiler/dashboard/steamfg') }}", function(res) {
                const data = res.data || [];

                const categories = data.map(m => formatMonthLabel(m.month));
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
                                <b>${this.x}</b><br/>
                                Total Steam: <b>${Highcharts.numberFormat(row.steam, 1, '.', ',')} Ton</b><br/>
                                Total Finish Goods: <b>${Highcharts.numberFormat(row.finish_goods, 1, '.', ',')} Ton</b><br/>
                                Rasio: <b>${Highcharts.numberFormat(row.rasio, 4, '.', ',')} Ton</b>
                            `;
                        }
                    },
                    series: [{
                        name: 'Rasio',
                        data: rasioValues,
                        color: '#457b9d',
                        marker: {
                            radius: 4
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return Highcharts.numberFormat(this.y, 3, '.', ',');
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

            $.getJSON("{{ url('api/boiler/dashboard/bbfg') }}", function(res) {
                const data = res.data || [];

                const categories = data.map(m => formatMonthLabel(m.month));
                const rasioValues = data.map(m => parseFloat(m.rasio) || 0);

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
                                <b>${this.x}</b><br/>
                                Total Batu Bara: <b>${Highcharts.numberFormat(row.batu_bara, 1, '.', ',')} Ton</b><br/>
                                Total Finish Goods: <b>${Highcharts.numberFormat(row.finish_goods, 1, '.', ',')} Ton</b><br/>
                                Rasio: <b>${Highcharts.numberFormat(row.rasio, 4, '.', ',')} Ton</b>
                            `;
                        }
                    },
                    series: [{
                        name: 'Rasio',
                        data: rasioValues,
                        color: '#f78104',
                        marker: {
                            radius: 4
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return Highcharts.numberFormat(this.y, 3, '.', ',');
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

        });
    </script>
@endsection
