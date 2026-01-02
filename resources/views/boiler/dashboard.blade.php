@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Engineering Dashboard</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboards</a></li>
                            <li class="breadcrumb-item active">Engineering</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary bg-gradient">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="text-white mb-2">Welcome, {{ Session::get('username') }}!</h4>
                                <p class="text-white-50 mb-0">Real-time monitoring of your boiler system performance</p>
                            </div>
                            <div class="text-end">
                                <div class="bg-white bg-opacity-25 rounded p-3">
                                    <h6 class="text-white mb-1 text-uppercase small">Current PV Steam</h6>
                                    <h2 class="text-white mb-0" id="PV-display">-- Bar</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sensor Boiler Chart -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0 d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="ri-line-chart-line me-2"></i>Sensor Boiler Chart
                        </h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <select id="filterData" class="form-select form-select-sm" style="width: auto;">
                                <option value="latest">Latest</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                            </select>
                            <input type="date" id="datePicker" class="form-control form-control-sm d-none" style="width: auto;">
                            <input type="date" id="startDate" class="form-control form-control-sm d-none" style="width: auto;">
                            <input type="date" id="endDate" class="form-control form-control-sm d-none" style="width: auto;">
                            <button id="applyFilter" class="btn btn-primary btn-sm">
                                <i class="ri-refresh-line"></i> Apply
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="boiler_chart" class="apex-charts" dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kondensat Chart -->
        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0 d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="ri-line-chart-line me-2"></i>Kondensat Chart
                        </h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <input type="date" id="kondensatStart" class="form-control form-control-sm" style="width: auto;">
                            <input type="date" id="kondensatEnd" class="form-control form-control-sm" style="width: auto;">
                            <button id="applyKondensat" class="btn btn-primary btn-sm">
                                <i class="ri-refresh-line"></i> Apply
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="kondensat_chart" class="apex-charts" dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PV Steam PRD Chart -->
        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0 d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="ri-line-chart-line me-2"></i>PV Steam PRD Chart
                        </h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <input type="date" id="pvsteamStart" class="form-control form-control-sm" style="width: auto;">
                            <input type="date" id="pvsteamEnd" class="form-control form-control-sm" style="width: auto;">
                            <button id="applyPvsteam" class="btn btn-primary btn-sm">
                                <i class="ri-refresh-line"></i> Apply
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="pvsteam_chart" class="apex-charts" dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Abnormal Detail Modal -->
        <div class="modal fade" id="abnormalModal" tabindex="-1" aria-labelledby="abnormalModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="abnormalModalLabel">
                            <i class="ri-alert-line me-2"></i>Abnormal Event Details
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="abnormalModalBody">
                        <!-- Data will be loaded here -->
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
        let chart, chartKondensat, chartPvsteam;

        const fetchData = (url, params = {}) =>
            $.ajax({
                url,
                type: "GET",
                data: params,
                dataType: "json"
            });

        const UpdateChartSensor = (data) => {
            if (!data.length) {
                chart?.updateSeries([{
                    data: []
                }]);
                Swal.fire({
                    icon: "warning",
                    title: "Data Tidak Ditemukan",
                    text: "Tidak ada data untuk rentang waktu yang dipilih."
                });
                return;
            }

            const categories = data.map(i => i.waktu);
            const series = [{
                    name: "Level Feed Water",
                    data: data.map(i => i.LevelFeedWater)
                },
                {
                    name: "PV Steam",
                    data: data.map(i => i.PVSteam)
                },
                {
                    name: "Batu Bara",
                    data: data.map(i => i.Batubara_FK)
                }
            ];

            const options = {
                chart: {
                    type: "line",
                    height: 380,
                    toolbar: {
                        show: true
                    }
                },
                stroke: {
                    width: 3,
                    curve: "smooth"
                },
                series,
                colors: ["#0acf97", "#fa5c7c", "#ffbc00"],
                xaxis: {
                    categories,
                    title: {
                        text: "Time"
                    },
                    labels: {
                        rotate: -45
                    }
                },
                yaxis: {
                    title: {
                        text: "Sensor Value"
                    }
                },
                tooltip: {
                    x: {
                        format: "dd MMM HH:mm"
                    }
                },
                legend: {
                    position: "bottom",
                    horizontalAlign: "right"
                },
                grid: {
                    borderColor: "#f1f1f1"
                }
            };

            if (chart) {
                chart.updateOptions(options);
            } else {
                chart = new ApexCharts(document.querySelector("#boiler_chart"), options);
                chart.render();
            }
        };

        const updatePVSteam = () => {
            $.getJSON("{{ url('http://10.11.11.200/mybas/public/api/sensor/boiler-realtime') }}", (response) => {
                if (response) {
                    const pvValue = parseFloat(response.PVSteam);
                    $('#PV-display').text(`${pvValue.toFixed(2)} Bar`);

                    if (response.PVSteam > 6) {
                        $.ajax({
                            url: "{{ url('http://10.11.11.200/mybas/public/api/send/tele') }}",
                            type: "GET",
                            dataType: "json"
                        });
                    }
                }
            }).fail((xhr, status, error) => console.error(`AJAX Error: ${status} ${error}`));
        };

        const updateInputFields = () => {
            const filter = $("#filterData").val();
            $("#datePicker, #startDate, #endDate").addClass("d-none");
            if (filter === "daily") $("#datePicker").removeClass("d-none");
            else if (filter === "weekly") $("#startDate, #endDate").removeClass("d-none");
        };

        $("#filterData").on("change", updateInputFields);

        $("#applyFilter").on("click", () => {
            const filter = $("#filterData").val();
            let url = "",
                params = {};

            if (filter === "latest") {
                url = "{{ url('http://10.11.11.200/mybas/public/api/sensor/boiler-data') }}";
            } else if (filter === "daily") {
                const tanggal = $("#datePicker").val();
                if (!tanggal) return Swal.fire({
                    icon: "warning",
                    title: "Select Date!",
                    text: "Please select a date first."
                });
                url = "{{ url('sensor/boiler/data-harian') }}";
                params = {
                    tanggal
                };
            } else if (filter === "weekly") {
                const start = $("#startDate").val(),
                    end = $("#endDate").val();
                if (!start || !end) return Swal.fire({
                    icon: "warning",
                    title: "Select Date Range!",
                    text: "Please select start and end dates."
                });
                url = "{{ url('http://10.11.11.200/mybas/public/api/sensor/boiler/data-mingguan') }}";
                params = {
                    tanggal_mulai: start,
                    tanggal_selesai: end
                };
            }

            fetchData(url, params).done(response => {
                response.success ? UpdateChartSensor(response.data) :
                    Swal.fire({
                        icon: "warning",
                        title: "Data Not Found",
                        text: "No data available for the selected time range."
                    });
            });
        });

        updateInputFields();
        $("#applyFilter").trigger("click");
        updatePVSteam();
        setInterval(updatePVSteam, 15000);


        // Kondensat Chart
        const UpdateChartKondensat = (response) => {
            const data = response.data || [];
            if (!data.length) {
                chartKondensat?.updateSeries([{
                    data: []
                }]);
                Swal.fire({
                    icon: "warning",
                    title: "Data Not Found",
                    text: "No kondensat data for the selected time range."
                });
                return;
            }

            const categories = data.map(i => i.waktu);
            const series = [];

            for (let i = 1; i <= 5; i++) {
                const key = `Suhu${i}`;
                if (data.some(d => d[key] !== null)) {
                    series.push({
                        name: key,
                        data: data.map(d => d[key] !== null ? parseFloat(d[key]) : null)
                    });
                }
            }

            const options = {
                chart: {
                    type: "line",
                    height: 380,
                    toolbar: {
                        show: true
                    }
                },
                stroke: {
                    width: 3,
                    curve: "smooth"
                },
                series,
                colors: ["#008FFB", "#FEB019", "#00E396", "#FF4560", "#775DD0"],
                xaxis: {
                    categories,
                    title: {
                        text: "Time"
                    },
                    labels: {
                        rotate: -45
                    }
                },
                yaxis: {
                    title: {
                        text: "Temperature (°C)"
                    }
                },
                tooltip: {
                    x: {
                        format: "dd MMM HH:mm"
                    }
                },
                legend: {
                    position: "bottom",
                    horizontalAlign: "right"
                },
                grid: {
                    borderColor: "#f1f1f1"
                }
            };

            if (chartKondensat) {
                chartKondensat.updateOptions(options);
            } else {
                chartKondensat = new ApexCharts(document.querySelector("#kondensat_chart"), options);
                chartKondensat.render();
            }
        };

        $("#applyKondensat").on("click", () => {
            const start = $("#kondensatStart").val();
            const end = $("#kondensatEnd").val();

            if (!start || !end) {
                return Swal.fire({
                    icon: "warning",
                    title: "Select Date Range!",
                    text: "Please select start and end dates."
                });
            }

            $.ajax({
                url: "{{ url('http://10.11.11.200/mybas/public/api/kondensat/data') }}",
                method: "GET",
                data: {
                    start_date: start,
                    end_date: end
                },
                success: function(response) {
                    UpdateChartKondensat(response);
                },
                error: function(xhr, status, error) {
                    console.error(`AJAX Error: ${status} ${error}`);
                    Swal.fire({
                        icon: "error",
                        title: "Failed!",
                        text: "Unable to fetch kondensat data."
                    });
                }
            });
        });

        // Load initial kondensat data for today
        const today = new Date().toISOString().split("T")[0];
        $("#kondensatStart").val(today);
        $("#kondensatEnd").val(today);
        $("#applyKondensat").trigger("click");


        // PV Steam PRD Chart
        const UpdateChartPvsteam = (data) => {
            if (!data.length) {
                chartPvsteam?.updateSeries([{
                    data: []
                }]);
                Swal.fire({
                    icon: "warning",
                    title: "Data Not Found",
                    text: "No PV Steam PRD data for the selected time range."
                });
                return;
            }

            const categories = data.map(i => i.minute);

            // Calculate difference (selisih)
            const selisihData = data.map(i => {
                const pvsteam = parseFloat(i.pvsteam);
                const pressPasteur = parseFloat(i.press_pasteur);
                return (pvsteam - pressPasteur).toFixed(2);
            });

            const series = [{
                    name: "PV Steam Boiler",
                    data: data.map(i => parseFloat(i.pvsteam).toFixed(2))
                },
                {
                    name: "PV Steam PRD",
                    data: data.map(i => parseFloat(i.press_pasteur).toFixed(2))
                },
                {
                    name: "Selisih (PV Steam - Press Pasteur)",
                    data: selisihData
                }
            ];

            const options = {
                chart: {
                    type: "line",
                    height: 380,
                    toolbar: {
                        show: true
                    }
                },
                stroke: {
                    width: 3,
                    curve: "smooth"
                },
                series,
                colors: ["#556ee6", "#f46a6a", "#34c38f"],
                xaxis: {
                    categories,
                    title: {
                        text: "Time"
                    },
                    labels: {
                        rotate: -45
                    }
                },
                yaxis: {
                    title: {
                        text: "Pressure (Bar)"
                    }
                },
                tooltip: {
                    x: {
                        format: "dd MMM HH:mm"
                    },
                    y: {
                        formatter: function(value) {
                            return value + " Bar";
                        }
                    }
                },
                legend: {
                    position: "bottom",
                    horizontalAlign: "right"
                },
                grid: {
                    borderColor: "#f1f1f1"
                }
            };

            if (chartPvsteam) {
                chartPvsteam.updateOptions(options);
            } else {
                chartPvsteam = new ApexCharts(document.querySelector("#pvsteam_chart"), options);
                chartPvsteam.render();
            }
        };

        $("#applyPvsteam").on("click", () => {
            const start = $("#pvsteamStart").val();
            const end = $("#pvsteamEnd").val();

            if (!start || !end) {
                return Swal.fire({
                    icon: "warning",
                    title: "Select Date Range!",
                    text: "Please select start and end dates."
                });
            }

            $.ajax({
                url: "http://10.11.11.200/mybas/public/api/sensor/pvsteamprd/data",
                method: "GET",
                data: {
                    start_date: start,
                    end_date: end
                },
                success: function(response) {
                    // Check if response is array or has data property
                    const data = Array.isArray(response) ? response : (response.data || []);
                    UpdateChartPvsteam(data);
                },
                error: function(xhr, status, error) {
                    console.error(`AJAX Error: ${status} ${error}`);
                    Swal.fire({
                        icon: "error",
                        title: "Failed!",
                        text: "Unable to fetch PV Steam PRD data."
                    });
                }
            });
        });

        // Load initial PV Steam PRD data for today
        $("#pvsteamStart").val(today);
        $("#pvsteamEnd").val(today);
        $("#applyPvsteam").trigger("click");
    });
</script>
@endsection