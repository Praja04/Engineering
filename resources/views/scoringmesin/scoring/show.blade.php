@extends('layouts.app')

@section('title', 'Detail Scoring - ' . $scoring->machineProcess->machine->name)

@section('styles')
<style>
    .info-card {
        background: linear-gradient(to right, #003c8f 0%, #6ab7ff 100%);
        color: white;
        border: none;
    }

    .summary-card {
        border-left: 4px solid #405189;
    }

    .result-badge-ok {
        background: #198754;
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
    }

    .result-badge-not-ok {
        background: #dc3545;
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
    }

    .part-detail-item {

        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        border-left: 4px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .part-detail-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .part-detail-item.ok {
        border-left-color: #198754;
    }

    .part-detail-item.not-ok {
        border-left-color: #dc3545;
    }

    .critical-badge {
        background: #dc3545;
        color: white;
        padding: 0.125rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .non-critical-badge {
        background: #c9dc35ff;
        color: white;
        padding: 0.125rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .chart-container {
        position: relative;
    }

    .section-header {
        background: #5688ecff;
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .summary-stat {
        text-align: center;
        padding: 1.5rem;
        border-radius: 8px;

        margin-bottom: 1rem;
    }

    .summary-stat h2 {
        margin: 0;
        font-size: 2.5rem;
        font-weight: 700;
    }

    .summary-stat.ok {
        background: linear-gradient(135deg, #198754 0%, #20c997 100%);
        color: white;
    }

    .summary-stat.not-ok {
        background: linear-gradient(135deg, #dc3545 0%, #e35d6a 100%);
        color: white;
    }

    @media print {

        .btn,
        .breadcrumb,
        .page-title-box {
            display: none;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ri-file-list-3-line align-middle me-2"></i>
                        Detail Scoring
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('scoring.index') }}">Scoring Mesin</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('scoring.history') }}">History</a>
                            </li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card info-card" data-aos="fade-up">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="mb-2 text-white">{{ $scoring->machineProcess->machine->name }}</h3>
                                <h5 class="opacity-90 mb-3 text-white">{{ $scoring->machineProcess->processParameter->name }}</h5>
                                <div class="d-flex gap-3 mb-2">
                                    <span class="badge">
                                        <i class="ri-calendar-line me-1"></i>
                                        {{ $scoring->scoring_date->format('d M Y, H:i') }}
                                    </span>
                                    <span class="badge">
                                        <i class="ri-user-line me-1"></i>
                                        {{ $scoring->user->username ?? $scoring->user->name ?? '-' }}
                                    </span>
                                    <span class="badge">
                                        <i class="ri-shield-check-line me-1"></i>
                                        {{ $scoring->status }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('scoring.history') }}" class="btn btn-light mb-2">
                                    <i class="ri-arrow-left-line me-1"></i> Kembali
                                </a>
                                <br>
                                <button class="btn btn-light" onclick="window.print()">
                                    <i class="ri-printer-line me-1"></i> Print
                                </button>
                            </div>
                        </div>
                        @if($scoring->notes)
                        <div class="mt-3 pt-3 border-top border-white border-opacity-25">
                            <p class="mb-0 opacity-90">
                                <i class="ri-file-text-line me-2"></i>
                                <strong>Catatan:</strong> {{ $scoring->notes }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Summary Cards -->
            <div class="col-lg-4">
                <div class="card summary-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="ri-bar-chart-line me-2"></i>
                            Ringkasan Scoring
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="summary-stat ok">
                            <h2>{{ $summary['ok'] }}</h2>
                            <p class="mb-0">Part OK</p>
                            <h4 class="mt-2">{{ number_format($summary['ok_percentage'], 1) }}%</h4>
                        </div>

                        <div class="summary-stat not-ok">
                            <h2>{{ $summary['not_ok'] }}</h2>
                            <p class="mb-0">Part NOT OK</p>
                            <h4 class="mt-2">{{ number_format($summary['not_ok_percentage'], 1) }}%</h4>
                        </div>

                        <div class="summary-stat">
                            <h2>{{ $summary['total'] }}</h2>
                            <p class="mb-0">Total Part Checked</p>
                        </div>

                        <!-- Chart -->
                        <div class="chart-container mt-4">
                            <canvas id="scoringChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Parts -->
            <div class="col-lg-8">
                <div class="card" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="ri-list-check-2 me-2"></i>
                            Detail Hasil Scoring
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                        $detailsBySection = $scoring->scoringDetails->groupBy('part.section.id');
                        @endphp

                        @forelse($detailsBySection as $sectionId => $details)
                        @php
                        $section = $details->first()->part->section;
                        @endphp

                        <div class="section-header">
                            <h6 class="mb-0 text-white">
                                <i class="ri-folder-line me-2"></i>
                                {{ $section->name }}
                            </h6>
                        </div>

                        @foreach($details as $detail)
                        <div class="part-detail-item {{ strtolower(str_replace(' ', '-', $detail->result)) }}">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1">
                                        {{ $detail->part->name }}
                                        @if($detail->part->critical == 'Y')
                                        <span class="critical-badge ms-2">CRITICAL</span>
                                        @else
                                        <span class="non-critical-badge ms-2">Non Critical</span>
                                        @endif
                                    </h6>
                                    @if($detail->part->standar)
                                    <p class=" mb-0 small">
                                        <i class="ri-bookmark-line me-1"></i>
                                        Standar: {{ $detail->part->standar }}
                                    </p>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    @if($detail->result == 'OK')
                                    <span class="result-badge-ok">
                                        <i class="ri-check-line me-1"></i>OK
                                    </span>
                                    @else
                                    <span class="result-badge-not-ok">
                                        <i class="ri-close-line me-1"></i>NOT OK
                                    </span>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    @if($detail->notes)
                                    <small class="text-muted">
                                        <i class="ri-message-3-line me-1"></i>
                                        {{ $detail->notes }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @empty
                        <div class="text-center py-5">
                            <div class="avatar-lg mx-auto mb-3">
                                <div class="avatar-title bg-soft-warning text-warning rounded-circle fs-2">
                                    <i class="ri-alert-line"></i>
                                </div>
                            </div>
                            <h6 class="mb-1">Tidak Ada Data Detail</h6>
                            <p class="text-muted">Belum ada detail scoring yang tersedia</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    $(document).ready(function() {

        // 🔹 Fungsi untuk memuat data chart via AJAX
        function loadChart(month = $('#filterMonth').val()) {
            $.ajax({
                url: "{{ route('scoring.statistics') }}", // Ganti sesuai route kamu
                type: "GET",
                data: {
                    month: month
                },
                dataType: "json",
                beforeSend: function() {
                    $('#scoringChart').html('<div class="text-center py-4 text-muted">Loading chart...</div>');
                },
                success: function(response) {
                    // Pastikan response punya data summary
                    const summary = response.summary || {
                        ok: 0,
                        not_ok: 0,
                        total: 0
                    };

                    // Render ApexChart
                    renderChart(summary);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading chart data:', error);
                    $('#scoringChart').html('<div class="text-center text-danger py-4">Gagal memuat data</div>');
                }
            });
        }

        // 🔹 Fungsi render ApexChart
        function renderChart(summary) {
            const chartElement = document.querySelector('#scoringChart');
            if (!chartElement) return;

            const options = {
                series: [summary.ok, summary.not_ok],
                chart: {
                    type: 'donut',
                    height: 250
                },
                labels: ['OK', 'NOT OK'],
                colors: ['#198754', '#dc3545'],
                legend: {
                    position: 'bottom',
                    fontSize: '14px',
                    markers: {
                        width: 12,
                        height: 12
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + '%';
                    },
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold',
                        colors: ['#fff']
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Parts',
                                    fontSize: '16px',
                                    fontWeight: 600,
                                    color: '#405189',
                                    formatter: function() {
                                        return summary.total;
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + ' parts';
                        }
                    }
                }
            };

            // Hapus chart lama (kalau ada)
            $('#scoringChart').empty();

            // Render chart baru
            const chart = new ApexCharts(chartElement, options);
            chart.render();
        }

        // 🔹 Event: ketika bulan diubah
        $('#filterMonth').on('change', function() {
            const selectedMonth = $(this).val();
            loadChart(selectedMonth);
        });

        // 🔹 Inisialisasi awal
        loadChart();

        // 🔹 Inisialisasi AOS
        AOS.init({
            duration: 800,
            once: true
        });
    });
</script>

@endsection