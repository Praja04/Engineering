@extends('layouts.app')

@section('title', 'Dashboard WWTP')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dashboard WWTP (Wastewater Treatment Plant)</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Utility</a></li>
                            <li class="breadcrumb-item active">WWTP Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Records</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="totalRecords">0</span>
                                </h4>
                                <p class="text-muted mb-0 text-truncate">All time data</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle rounded fs-3">
                                    <i class="bx bx-data text-primary"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Influent Records</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="totalInfluent">0</span>
                                </h4>
                                <p class="text-muted mb-0">
                                    <span id="weeklyInfluentStatus" class="badge bg-success-subtle text-success"></span>
                                </p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle rounded fs-3">
                                    <i class="bx bx-import text-info"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Effluent Records</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="totalEffluent">0</span>
                                </h4>
                                <p class="text-muted mb-0">
                                    <span id="weeklyEffluentCount" class="badge bg-success-subtle text-success"></span>
                                </p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success-subtle rounded fs-3">
                                    <i class="bx bx-export text-success"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Last Update</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h6 class="fs-16 fw-semibold mb-4">
                                    <span id="lastUpdate">-</span>
                                </h6>
                                <p class="text-muted mb-0 text-truncate">Latest record date</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning-subtle rounded fs-3">
                                    <i class="bx bx-calendar text-warning"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Average Cards -->
        <div class="row">
            <div class="col-xl-6">
                <div class="card card-height-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                                    <i class="bx bxs-droplet"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-1">Avg Influent (This Month)</p>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                    <span id="monthlyInfluentAvg">0</span> <small class="fs-14 text-muted">m³</small>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card card-height-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success-subtle text-success rounded fs-3">
                                    <i class="bx bxs-droplet-half"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-1">Avg Effluent (This Month)</p>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                    <span id="monthlyEffluentAvg">0</span> <small class="fs-14 text-muted">m³</small>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Influent Trend Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Influent Data Trend</h4>
                        <div class="flex-shrink-0">
                            <select class="form-select form-select-sm" id="influentPeriod" style="width: 150px;">
                                <option value="7">Last 7 Days</option>
                                <option value="14">Last 14 Days</option>
                                <option value="30" selected>Last 30 Days</option>
                                <option value="60">Last 60 Days</option>
                                <option value="90">Last 90 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="influentChart" height="320"></canvas>
                    </div>
                </div>
            </div>

            <!-- Influent Breakdown Pie Chart -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Influent Source Distribution</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="influentPieChart" height="320"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Effluent Trend Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Effluent Data Trend</h4>
                        <div class="flex-shrink-0">
                            <select class="form-select form-select-sm" id="effluentPeriod" style="width: 150px;">
                                <option value="7">Last 7 Days</option>
                                <option value="14">Last 14 Days</option>
                                <option value="30" selected>Last 30 Days</option>
                                <option value="60">Last 60 Days</option>
                                <option value="90">Last 90 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="effluentChart" height="320"></canvas>
                    </div>
                </div>
            </div>

            <!-- Effluent Breakdown Pie Chart -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Effluent Process Distribution</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="effluentPieChart" height="320"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Comparison Chart -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">6-Month Comparison</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyComparisonChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Records Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Recent Records</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Kategori</th>
                                        <th scope="col">Details</th>
                                        <th scope="col">Total Volume</th>
                                    </tr>
                                </thead>
                                <tbody id="recentRecordsTable">
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            Loading data...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .card-animate {
        transition: all 0.3s ease;
    }

    .card-animate:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .counter-value {
        animation: countUp 1s ease-out;
    }

    @keyframes countUp {
        from {
            opacity: 0;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>\
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let influentChart, effluentChart, influentPieChart, effluentPieChart, monthlyComparisonChart;
    let allInfluentData = [];
    let allEffluentData = [];

    // Load all data on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadStatistics();
        initCharts();
        loadMonthlyComparison();
        loadRecentRecords();

        // Event listeners
        document.getElementById('influentPeriod').addEventListener('change', updateInfluentChart);
        document.getElementById('effluentPeriod').addEventListener('change', updateEffluentChart);
    });

    // Load Statistics
    async function loadStatistics() {
        try {
            const response = await fetch('/api/wwtp/dashboard/statistics');
            const data = await response.json();

            // Animate counter
            animateValue('totalRecords', 0, data.total_records, 1000);
            animateValue('totalInfluent', 0, data.total_influent, 1000);
            animateValue('totalEffluent', 0, data.total_effluent, 1000);

            // Last update
            if (data.last_update) {
                const date = new Date(data.last_update);
                document.getElementById('lastUpdate').textContent = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            // Weekly status
            if (data.weekly_influent) {
                document.getElementById('weeklyInfluentStatus').textContent = 'Data minggu ini tersedia';
            } else {
                document.getElementById('weeklyInfluentStatus').textContent = 'Belum ada data minggu ini';
                document.getElementById('weeklyInfluentStatus').className = 'badge bg-warning-subtle text-warning';
            }

            document.getElementById('weeklyEffluentCount').textContent =
                data.weekly_effluent_count + ' record minggu ini';

            // Monthly averages
            document.getElementById('monthlyInfluentAvg').textContent =
                data.monthly_influent_avg || '0';
            document.getElementById('monthlyEffluentAvg').textContent =
                data.monthly_effluent_avg || '0';

        } catch (error) {
            console.error('Error loading statistics:', error);
        }
    }

    // Animate counter
    function animateValue(id, start, end, duration) {
        const obj = document.getElementById(id);
        const range = end - start;
        const increment = end > start ? 1 : -1;
        const stepTime = Math.abs(Math.floor(duration / range));
        let current = start;

        const timer = setInterval(function() {
            current += increment;
            obj.textContent = current;
            if (current == end) {
                clearInterval(timer);
            }
        }, stepTime);
    }

    // Initialize Charts
    function initCharts() {
        // Influent Line Chart
        const influentCtx = document.getElementById('influentChart').getContext('2d');
        influentChart = new Chart(influentCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                        label: 'Pit Sparta',
                        data: [],
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Pit Garam',
                        data: [],
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Pit Domestik',
                        data: [],
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' m³';
                            }
                        }
                    }
                }
            }
        });

        // Effluent Line Chart
        const effluentCtx = document.getElementById('effluentChart').getContext('2d');
        effluentChart = new Chart(effluentCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                        label: 'Full Proses',
                        data: [],
                        borderColor: 'rgb(153, 102, 255)',
                        backgroundColor: 'rgba(153, 102, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'DAF Pre',
                        data: [],
                        borderColor: 'rgb(255, 159, 64)',
                        backgroundColor: 'rgba(255, 159, 64, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' m³';
                            }
                        }
                    }
                }
            }
        });

        // Influent Pie Chart
        const influentPieCtx = document.getElementById('influentPieChart').getContext('2d');
        influentPieChart = new Chart(influentPieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pit Sparta', 'Pit Garam', 'Pit Domestik'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Effluent Pie Chart
        const effluentPieCtx = document.getElementById('effluentPieChart').getContext('2d');
        effluentPieChart = new Chart(effluentPieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Full Proses', 'DAF Pre'],
                datasets: [{
                    data: [0, 0],
                    backgroundColor: [
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Load initial data
        updateInfluentChart();
        updateEffluentChart();
    }

    // Update Influent Chart
    async function updateInfluentChart() {
        try {
            const period = document.getElementById('influentPeriod').value;
            const response = await fetch(`/api/wwtp/dashboard/influent-chart/${period}`);
            const data = await response.json();

            allInfluentData = data;

            influentChart.data.labels = data.map(d => {
                const date = new Date(d.tanggal);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });
            influentChart.data.datasets[0].data = data.map(d => d.pit_sparta);
            influentChart.data.datasets[1].data = data.map(d => d.pit_garam);
            influentChart.data.datasets[2].data = data.map(d => d.pit_domestik);
            influentChart.update();

            // Update pie chart
            const totalSparta = data.reduce((sum, d) => sum + d.pit_sparta, 0);
            const totalGaram = data.reduce((sum, d) => sum + d.pit_garam, 0);
            const totalDomestik = data.reduce((sum, d) => sum + d.pit_domestik, 0);

            influentPieChart.data.datasets[0].data = [totalSparta, totalGaram, totalDomestik];
            influentPieChart.update();

        } catch (error) {
            console.error('Error updating influent chart:', error);
        }
    }

    // Update Effluent Chart
    async function updateEffluentChart() {
        try {
            const period = document.getElementById('effluentPeriod').value;
            const response = await fetch(`/api/wwtp/dashboard/effluent-chart/${period}`);
            const data = await response.json();

            allEffluentData = data;

            effluentChart.data.labels = data.map(d => {
                const date = new Date(d.tanggal);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });
            effluentChart.data.datasets[0].data = data.map(d => d.full_proses);
            effluentChart.data.datasets[1].data = data.map(d => d.daf_pre);
            effluentChart.update();

            // Update pie chart
            const totalFullProses = data.reduce((sum, d) => sum + d.full_proses, 0);
            const totalDafPre = data.reduce((sum, d) => sum + d.daf_pre, 0);

            effluentPieChart.data.datasets[0].data = [totalFullProses, totalDafPre];
            effluentPieChart.update();

        } catch (error) {
            console.error('Error updating effluent chart:', error);
        }
    }

    // Load Monthly Comparison
    async function loadMonthlyComparison() {
        try {
            const response = await fetch('/api/wwtp/dashboard/monthly-comparison');
            const data = await response.json();

            const ctx = document.getElementById('monthlyComparisonChart').getContext('2d');
            monthlyComparisonChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.month),
                    datasets: [{
                            label: 'Influent',
                            data: data.map(d => d.influent),
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgb(54, 162, 235)',
                            borderWidth: 1
                        },
                        {
                            label: 'Effluent',
                            data: data.map(d => d.effluent),
                            backgroundColor: 'rgba(153, 102, 255, 0.8)',
                            borderColor: 'rgb(153, 102, 255)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' m³';
                                }
                            }
                        }
                    }
                }
            });

        } catch (error) {
            console.error('Error loading monthly comparison:', error);
        }
    }

    // Load Recent Records
    async function loadRecentRecords() {
        try {
            const response = await fetch('/api/wwtp/dashboard/recent-records/10');
            const data = await response.json();

            const tbody = document.getElementById('recentRecordsTable');

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No data available</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(record => {
                const date = new Date(record.tanggal);
                const formattedDate = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });

                let details = '';
                let total = 0;
                let badge = '';

                if (record.kategori === 'influent' && record.influent) {
                    badge = '<span class="badge bg-info-subtle text-info">Influent</span>';
                    details = `
                    <small class="text-muted">
                        Sparta: ${record.influent.pit_sparta} m³<br>
                        Garam: ${record.influent.pit_garam} m³<br>
                        Domestik: ${record.influent.pit_domestik} m³
                    </small>
                `;
                    total = parseFloat(record.influent.pit_sparta) +
                        parseFloat(record.influent.pit_garam) +
                        parseFloat(record.influent.pit_domestik);
                } else if (record.kategori === 'effluent' && record.effluent) {
                    badge = '<span class="badge bg-success-subtle text-success">Effluent</span>';
                    details = `
                    <small class="text-muted">
                        Full Proses: ${record.effluent.full_proses} m³<br>
                        DAF Pre: ${record.effluent.daf_pre} m³
                    </small>
                `;
                    total = parseFloat(record.effluent.full_proses) +
                        parseFloat(record.effluent.daf_pre);
                }

                return `
                <tr>
                    <td><strong>${formattedDate}</strong></td>
                    <td>${badge}</td>
                    <td>${details}</td>
                    <td><strong>${total.toFixed(2)} m³</strong></td>
                </tr>
            `;
            }).join('');

        } catch (error) {
            console.error('Error loading recent records:', error);
            document.getElementById('recentRecordsTable').innerHTML =
                '<tr><td colspan="4" class="text-center text-danger">Error loading data</td></tr>';
        }
    }
</script>
@endsection