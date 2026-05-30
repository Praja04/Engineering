@extends('layouts.app')

@section('title', 'Dashboard WWTP Lumpur')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Judul Halaman -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dashboard WWTP Lumpur</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Utilitas</a></li>
                            <li class="breadcrumb-item active">Dashboard WWTP Lumpur</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================= -->
        <!-- BAGIAN: DATA LUMPUR -->
        <!-- ========================================= -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary-subtle">
                    <div class="card-body">
                        <h4 class="card-title text-primary mb-0">
                            <i class="bx bx-droplet"></i> Data Manajemen Lumpur
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Ringkasan -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Shift</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="totalShift">0</span>
                                </h4>
                                <p class="text-muted mb-0 text-truncate">Semua shift</p>
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
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Hari</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="totalHari">0</span>
                                </h4>
                                <p class="text-muted mb-0">
                                    <span id="statusHariIni" class="badge bg-success-subtle text-success"></span>
                                </p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle rounded fs-3">
                                    <i class="bx bx-calendar-alt text-info"></i>
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
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Shift Minggu Ini</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="shiftMingguIni">0</span>
                                </h4>
                                <p class="text-muted mb-0 text-truncate">Shift minggu ini</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success-subtle rounded fs-3">
                                    <i class="bx bx-time text-success"></i>
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
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Pembaruan Terakhir</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h6 class="fs-16 fw-semibold mb-4">
                                    <span id="pembaruanTerakhir">-</span>
                                </h6>
                                <p class="text-muted mb-0 text-truncate">Shift terbaru</p>
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

        <!-- Kartu Rata-rata Bulanan -->
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
                                <p class="text-uppercase fw-medium text-muted mb-1">Rata-rata Drain Lumpur (Bulan Ini)</p>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                    <span id="rataRataDrainBulanan">0</span> <small class="fs-14 text-muted">m³</small>
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
                                    <i class="bx bx-time-five"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-1">Rata-rata Jam Operasi SCP (Bulan Ini)</p>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                    <span id="rataRataJamOperasiBulanan">0</span> <small class="fs-14 text-muted">jam</small>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris Grafik -->
        <div class="row">
            <!-- Grafik Tren Drain Lumpur -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Tren Drain Lumpur</h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="tanggalMulaiDrain" style="width: 150px;">
                                <span class="align-self-center">s/d</span>
                                <input type="date" class="form-control form-control-sm" id="tanggalAkhirDrain" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="perbaruiGrafikDrain()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="grafikDrainLumpur"></div>
                    </div>
                </div>
            </div>

            <!-- Grafik Distribusi Shift -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Distribusi per Kategori</h4>
                    </div>
                    <div class="card-body">
                        <div id="distribusiFilter" class="mb-2 d-flex flex-wrap gap-1"></div>
                        <div id="grafikDistribusi"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Jam Operasi -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Tren Jam Operasi SCP</h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="tanggalMulaiJamOperasi" style="width: 150px;">
                                <span class="align-self-center">s/d</span>
                                <input type="date" class="form-control form-control-sm" id="tanggalAkhirJamOperasi" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="perbaruiGrafikJamOperasi()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="grafikJamOperasi"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Hasil Lumpur -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Tren Hasil Lumpur</h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="tanggalMulaiHasilLumpur" style="width: 150px;">
                                <span class="align-self-center">s/d</span>
                                <input type="date" class="form-control form-control-sm" id="tanggalAkhirHasilLumpur" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="perbaruiGrafikHasilLumpur()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="grafikHasilLumpur"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Perbandingan Bulanan -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Perbandingan 6 Bulan Terakhir</h4>
                    </div>
                    <div class="card-body">
                        <div id="grafikPerbandinganBulanan"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Sludge Content -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Tren Sludge Content</h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="tanggalMulaiSludgeContent" style="width: 150px;">
                                <span class="align-self-center">s/d</span>
                                <input type="date" class="form-control form-control-sm" id="tanggalAkhirSludgeContent" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="perbaruiGrafikSludgeContent()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="grafikSludgeContent"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Pengangkutan Sludge -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Pengangkutan Sludge (Per Minggu)</h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="tanggalMulaiPengangkutan" style="width: 150px;">
                                <span class="align-self-center">s/d</span>
                                <input type="date" class="form-control form-control-sm" id="tanggalAkhirPengangkutan" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="perbaruiGrafikPengangkutan()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="grafikPengangkutan"></div>
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

    .apexcharts-tooltip {
        background: #fff !important;
        border: 1px solid #e3e6ef !important;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Instansi grafik
    let grafikDrainLumpur, grafikJamOperasi, grafikDistribusi, grafikPerbandinganBulanan, grafikHasilLumpur, grafikSludgeContent, grafikPengangkutan;

    // Muat semua data saat halaman dibuka
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard mulai dimuat...');

        // Set tanggal default
        setTanggalDefault();

        // Inisialisasi grafik terlebih dahulu
        inisialisasiGrafik();

        // Muat data setelah grafik siap
        setTimeout(function() {
            muatStatistik();
            perbaruiGrafikDrain();
            perbaruiGrafikJamOperasi();
            perbaruiDistribusi();
            muatPerbandinganBulanan();
            perbaruiGrafikHasilLumpur();
            perbaruiGrafikSludgeContent();
            perbaruiGrafikPengangkutan();
        }, 100);
    });

    // Set tanggal default untuk filter
    function setTanggalDefault() {
        const hari = new Date();
        const awalBulan = new Date(hari.getFullYear(), hari.getMonth(), 1);
        const akhirBulan = new Date(hari.getFullYear(), hari.getMonth() + 1, 0);

        const formatTanggal = (date) => {
            const tahun = date.getFullYear();
            const bulan = String(date.getMonth() + 1).padStart(2, '0');
            const tgl = String(date.getDate()).padStart(2, '0');
            return `${tahun}-${bulan}-${tgl}`;
        };

        document.getElementById('tanggalMulaiDrain').value = formatTanggal(awalBulan);
        document.getElementById('tanggalAkhirDrain').value = formatTanggal(akhirBulan);
        document.getElementById('tanggalMulaiJamOperasi').value = formatTanggal(awalBulan);
        document.getElementById('tanggalAkhirJamOperasi').value = formatTanggal(akhirBulan);
        document.getElementById('tanggalMulaiHasilLumpur').value = formatTanggal(awalBulan);
        document.getElementById('tanggalAkhirHasilLumpur').value = formatTanggal(akhirBulan);
        document.getElementById('tanggalMulaiSludgeContent').value = formatTanggal(awalBulan);
        document.getElementById('tanggalAkhirSludgeContent').value = formatTanggal(akhirBulan);

        const duaBulanLalu = new Date(hari.getFullYear(), hari.getMonth() - 2, 1);
        document.getElementById('tanggalMulaiPengangkutan').value = formatTanggal(duaBulanLalu);
        document.getElementById('tanggalAkhirPengangkutan').value = formatTanggal(akhirBulan);
    }
    // =============================
    // PIE CHART FILTER
    // =============================
    const distribusiFullData = {
        labels: [],
        series: [],
        colors: []
    };
    const distribusiColors = ['#4bc0c0', '#ff6384', '#36a2eb'];

    function buildDistribusiFilter() {
        const container = document.getElementById('distribusiFilter');
        if (!container) return;
        container.innerHTML = '';

        distribusiFullData.labels.forEach((label, i) => {
            const color = distribusiColors[i % distribusiColors.length];
            const checkId = `distribusiFilter_cb_${i}`;
            const wrapper = document.createElement('div');
            wrapper.className = 'd-inline-flex align-items-center me-2 mb-1';
            wrapper.innerHTML = `
            <input type="checkbox" id="${checkId}" checked
                class="form-check-input me-1"
                style="cursor:pointer; accent-color:${color}; width:14px; height:14px;"
                onchange="applyDistribusiFilter()">
            <label for="${checkId}" class="form-check-label small mb-0"
                style="cursor:pointer; color:${color}; font-weight:600;">
                ${label}
            </label>`;
            container.appendChild(wrapper);
        });
    }

    function applyDistribusiFilter() {
        const checkboxes = document.querySelectorAll('#distribusiFilter input[type=checkbox]');
        const selected = Array.from(checkboxes)
            .map((cb, i) => cb.checked ? i : -1)
            .filter(i => i !== -1);

        if (!selected.length) {
            grafikDistribusi.updateOptions({
                labels: ['No Data'],
                colors: ['#ccc']
            });
            grafikDistribusi.updateSeries([0]);
            return;
        }

        grafikDistribusi.updateOptions({
            labels: selected.map(i => distribusiFullData.labels[i]),
            colors: selected.map(i => distribusiColors[i % distribusiColors.length])
        });
        grafikDistribusi.updateSeries(selected.map(i => distribusiFullData.series[i]));
    }
    // =============================
    // MUAT STATISTIK
    // =============================

    async function muatStatistik() {
        try {
            const response = await fetch('/api/wwtp-sludge/dashboard/statistics');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            console.log('Statistik:', data);

            animasiNilai('totalShift', 0, data.total_shifts || 0, 1000);
            animasiNilai('totalHari', 0, data.total_days || 0, 1000);
            animasiNilai('shiftMingguIni', 0, data.shifts_this_week || 0, 1000);

            if (data.last_update) {
                const tgl = new Date(data.last_update);
                const tglStr = tgl.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
                const labelShift = data.last_shift ? ` (Shift ${data.last_shift})` : '';
                document.getElementById('pembaruanTerakhir').textContent = tglStr + labelShift;
            }

            const teksStatus = (data.shifts_today || 0) === 0 ?
                'Belum ada data hari ini' :
                `${data.shifts_today} shift hari ini`;
            const kelasStatus = (data.shifts_today || 0) === 0 ?
                'badge bg-warning-subtle text-warning' :
                'badge bg-success-subtle text-success';

            document.getElementById('statusHariIni').textContent = teksStatus;
            document.getElementById('statusHariIni').className = kelasStatus;

            document.getElementById('rataRataDrainBulanan').textContent = data.monthly_drain_avg || '0';
            document.getElementById('rataRataJamOperasiBulanan').textContent = data.monthly_running_hour_avg || '0';

        } catch (error) {
            console.error('Gagal memuat statistik:', error);
            alert('Gagal memuat statistik. Periksa konsol untuk detail.');
        }
    }

    // =============================
    // PERBARUI GRAFIK DRAIN LUMPUR
    // =============================

    async function perbaruiGrafikDrain() {
        try {
            const tanggalMulai = document.getElementById('tanggalMulaiDrain').value;
            const tanggalAkhir = document.getElementById('tanggalAkhirDrain').value;

            if (!tanggalMulai || !tanggalAkhir) {
                console.warn('Tanggal belum diisi');
                return;
            }
            if (new Date(tanggalMulai) > new Date(tanggalAkhir)) {
                alert('Tanggal mulai harus sebelum tanggal akhir');
                return;
            }

            const response = await fetch(`/api/wwtp-sludge/dashboard/drain-chart?start_date=${tanggalMulai}&end_date=${tanggalAkhir}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            console.log('Data grafik drain:', data);

            if (!data || data.length === 0) {
                grafikDrainLumpur.updateSeries([{
                    name: 'Drain Lumpur',
                    data: []
                }]);
                return;
            }

            const kategori = data.map(d => new Date(d.tanggal).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short'
            }));
            const nilaiDrain = data.map(d => parseFloat(d.total_drain) || 0);

            grafikDrainLumpur.updateOptions({
                xaxis: {
                    categories: kategori
                }
            });
            grafikDrainLumpur.updateSeries([{
                name: 'Drain Lumpur',
                data: nilaiDrain
            }]);

            await perbaruiDistribusi();

        } catch (error) {
            console.error('Gagal memperbarui grafik drain:', error);
            alert('Gagal memuat data grafik drain. Coba lagi.');
        }
    }

    // =============================
    // PERBARUI GRAFIK JAM OPERASI
    // =============================

    async function perbaruiGrafikJamOperasi() {
        try {
            const tanggalMulai = document.getElementById('tanggalMulaiJamOperasi').value;
            const tanggalAkhir = document.getElementById('tanggalAkhirJamOperasi').value;

            if (!tanggalMulai || !tanggalAkhir) {
                console.warn('Tanggal belum diisi');
                return;
            }
            if (new Date(tanggalMulai) > new Date(tanggalAkhir)) {
                alert('Tanggal mulai harus sebelum tanggal akhir');
                return;
            }

            const response = await fetch(`/api/wwtp-sludge/dashboard/running-hour-chart?start_date=${tanggalMulai}&end_date=${tanggalAkhir}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            console.log('Data grafik jam operasi:', data);

            if (!data || data.length === 0) {
                grafikJamOperasi.updateSeries([{
                    name: 'Jam Operasi SCP',
                    data: []
                }]);
                return;
            }

            const kategori = data.map(d => new Date(d.tanggal).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short'
            }));
            const nilaiJamOperasi = data.map(d => parseFloat(d.total_running_hour) || 0);

            grafikJamOperasi.updateOptions({
                xaxis: {
                    categories: kategori
                }
            });
            grafikJamOperasi.updateSeries([{
                name: 'Jam Operasi SCP',
                data: nilaiJamOperasi
            }]);

        } catch (error) {
            console.error('Gagal memperbarui grafik jam operasi:', error);
            alert('Gagal memuat data grafik jam operasi. Coba lagi.');
        }
    }

    // =============================
    // PERBARUI GRAFIK HASIL LUMPUR
    // =============================

    async function perbaruiGrafikHasilLumpur() {
        try {
            const tanggalMulai = document.getElementById('tanggalMulaiHasilLumpur').value;
            const tanggalAkhir = document.getElementById('tanggalAkhirHasilLumpur').value;

            if (!tanggalMulai || !tanggalAkhir) {
                console.warn('Tanggal belum diisi');
                return;
            }
            if (new Date(tanggalMulai) > new Date(tanggalAkhir)) {
                alert('Tanggal mulai harus sebelum tanggal akhir');
                return;
            }

            const response = await fetch(`/api/wwtp-sludge/dashboard/hasil-lumpur-chart?start_date=${tanggalMulai}&end_date=${tanggalAkhir}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            console.log('Data grafik hasil lumpur:', data);

            if (!data || data.length === 0) {
                grafikHasilLumpur.updateSeries([{
                    name: 'Hasil Lumpur',
                    data: []
                }]);
                return;
            }

            const kategori = data.map(d => new Date(d.tanggal).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short'
            }));
            const nilaiHasilLumpur = data.map(d => parseFloat(d.total_hasil_lumpur) || 0);

            grafikHasilLumpur.updateOptions({
                xaxis: {
                    categories: kategori
                }
            });
            grafikHasilLumpur.updateSeries([{
                name: 'Hasil Lumpur',
                data: nilaiHasilLumpur
            }]);

        } catch (error) {
            console.error('Gagal memperbarui grafik hasil lumpur:', error);
        }
    }

    // =============================
    // PERBARUI GRAFIK SLUDGE CONTENT
    // =============================

    async function perbaruiGrafikSludgeContent() {
        try {
            const tanggalMulai = document.getElementById('tanggalMulaiSludgeContent').value;
            const tanggalAkhir = document.getElementById('tanggalAkhirSludgeContent').value;

            if (!tanggalMulai || !tanggalAkhir) {
                console.warn('Tanggal belum diisi');
                return;
            }

            const response = await fetch(`/api/wwtp-sludge/dashboard/sludge-content-chart?start_date=${tanggalMulai}&end_date=${tanggalAkhir}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            console.log('Data grafik sludge content:', data);

            if (!data || data.length === 0) {
                grafikSludgeContent.updateSeries([{
                    name: 'Sludge Content',
                    data: []
                }]);
                return;
            }

            const kategori = data.map(d => new Date(d.tanggal).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short'
            }));
            const nilai = data.map(d => parseFloat(d.avg_sludge_content) || 0);

            grafikSludgeContent.updateOptions({
                xaxis: {
                    categories: kategori
                }
            });
            grafikSludgeContent.updateSeries([{
                name: 'Sludge Content (%)',
                data: nilai
            }]);

        } catch (error) {
            console.error('Gagal memperbarui grafik sludge content:', error);
        }
    }

    // =============================
    // PERBARUI GRAFIK PENGANGKUTAN
    // =============================

    async function perbaruiGrafikPengangkutan() {
        try {
            const startDate = document.getElementById('tanggalMulaiPengangkutan').value;
            const endDate = document.getElementById('tanggalAkhirPengangkutan').value;
            const url = (startDate && endDate) ? `/api/wwtp-sludge/dashboard/pengangkutan-chart?start_date=${startDate}&end_date=${endDate}` : `/api/wwtp-sludge/dashboard/pengangkutan-chart`;
            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            console.log('Data grafik pengangkutan:', data);

            if (!data || data.length === 0) {
                grafikPengangkutan.updateSeries([{
                    name: 'Pengangkutan',
                    data: []
                }]);
                return;
            }

            const kategori = data.map(d => `${d.week_start} - ${d.week_end}`);
            const nilai = data.map(d => parseFloat(d.jumlah_pengangkutan) || 0);

            grafikPengangkutan.updateOptions({
                xaxis: {
                    categories: kategori
                }
            });
            grafikPengangkutan.updateSeries([{
                name: 'Pengangkutan (Ton)',
                data: nilai
            }]);

        } catch (error) {
            console.error('Gagal memperbarui grafik pengangkutan:', error);
        }
    }

    // =============================
    // PERBARUI DISTRIBUSI
    // =============================

    async function perbaruiDistribusi() {
        try {
            const tanggalMulai = document.getElementById('tanggalMulaiDrain').value;
            const tanggalAkhir = document.getElementById('tanggalAkhirDrain').value;

            if (!tanggalMulai || !tanggalAkhir) {
                console.warn('Tanggal belum diisi');
                return;
            }

            const response = await fetch(`/api/wwtp-sludge/dashboard/shift-breakdown?start_date=${tanggalMulai}&end_date=${tanggalAkhir}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            console.log('Data distribusi:', data);

            const totalDrain = parseFloat(data.total_drain_lumpur) || 0;
            const totalJamOperasi = parseFloat(data.total_running_hour_scp) || 0;
            const totalHasilLumpur = parseFloat(data.total_hasil_lumpur) || 0;

            const labels = ['Drain Lumpur', 'Jam Operasi SCP', 'Hasil Lumpur'];
            const series = [totalDrain, totalJamOperasi, totalHasilLumpur];

            distribusiFullData.labels = labels;
            distribusiFullData.series = series;

            buildDistribusiFilter();
            grafikDistribusi.updateOptions({
                labels,
                colors: distribusiColors
            });
            grafikDistribusi.updateSeries(series);
        } catch (error) {
            console.error('Gagal memperbarui distribusi:', error);
        }
    }

    // =============================
    // MUAT PERBANDINGAN BULANAN
    // =============================

    async function muatPerbandinganBulanan() {
        try {
            const response = await fetch('/api/wwtp-sludge/dashboard/monthly-comparison');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            console.log('Perbandingan bulanan:', data);

            if (!data || data.length === 0) {
                console.warn('Tidak ada data perbandingan bulanan');
                return;
            }

            grafikPerbandinganBulanan.updateOptions({
                xaxis: {
                    categories: data.map(d => d.month)
                }
            });

            grafikPerbandinganBulanan.updateSeries([{
                    name: 'Drain Lumpur',
                    data: data.map(d => parseFloat(d.drain_lumpur) || 0)
                },
                {
                    name: 'Jam Operasi SCP',
                    data: data.map(d => parseFloat(d.running_hour_scp) || 0)
                }
            ]);

        } catch (error) {
            console.error('Gagal memuat perbandingan bulanan:', error);
        }
    }

    // =============================
    // FUNGSI UTILITAS
    // =============================

    function animasiNilai(id, awal, akhir, durasi) {
        const elemen = document.getElementById(id);
        if (!elemen) {
            console.warn(`Elemen dengan id '${id}' tidak ditemukan`);
            return;
        }

        const rentang = akhir - awal;
        if (rentang === 0) {
            elemen.textContent = akhir;
            return;
        }

        const langkah = akhir > awal ? 1 : -1;
        const waktuLangkah = Math.abs(Math.floor(durasi / rentang));
        let sekarang = awal;

        const timer = setInterval(function() {
            sekarang += langkah;
            elemen.textContent = sekarang;
            if (sekarang == akhir) clearInterval(timer);
        }, waktuLangkah);
    }

    // =============================
    // INISIALISASI GRAFIK
    // =============================

    function inisialisasiGrafik() {
        console.log('Menginisialisasi grafik...');

        // Opsi grafik area umum
        const opsiGrafikArea = {
            chart: {
                type: 'area',
                height: 320,
                toolbar: {
                    show: true
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            dataLabels: {
                enabled: true,
                formatter: (val) => val ? val.toFixed(0) + ' m³' : '0 m³',
                style: {
                    fontSize: '12px',
                    colors: ['#000']
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1
                }
            },
            xaxis: {
                categories: [],
                labels: {
                    rotate: -45
                }
            },
            tooltip: {
                shared: true,
                intersect: false
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };

        // Grafik Drain Lumpur
        grafikDrainLumpur = new ApexCharts(document.querySelector("#grafikDrainLumpur"), {
            ...opsiGrafikArea,
            colors: ['#4bc0c0'],
            series: [{
                name: 'Drain Lumpur',
                data: []
            }],
            yaxis: {
                title: {
                    text: 'Volume (m³)'
                },
                labels: {
                    formatter: (v) => v ? v.toFixed(0) + ' m³' : '0 m³'
                }
            },
            tooltip: {
                y: {
                    formatter: (v) => v ? v.toFixed(2) + ' m³' : '0 m³'
                }
            }
        });
        grafikDrainLumpur.render();

        // Grafik Jam Operasi
        grafikJamOperasi = new ApexCharts(document.querySelector("#grafikJamOperasi"), {
            ...opsiGrafikArea,
            colors: ['#ff6384'],
            series: [{
                name: 'Jam Operasi SCP',
                data: []
            }],
            dataLabels: {
                enabled: true,
                formatter: (val) => val ? val.toFixed(0) + ' jam' : '0 jam',
                style: {
                    fontSize: '12px',
                    colors: ['#000']
                }
            },
            yaxis: {
                title: {
                    text: 'Jam'
                },
                labels: {
                    formatter: (v) => v ? v.toFixed(0) + ' jam' : '0 jam'
                }
            },
            tooltip: {
                y: {
                    formatter: (v) => v ? v.toFixed(2) + ' jam' : '0 jam'
                }
            }
        });
        grafikJamOperasi.render();

        // Grafik Hasil Lumpur
        grafikHasilLumpur = new ApexCharts(document.querySelector("#grafikHasilLumpur"), {
            ...opsiGrafikArea,
            colors: ['#36a2eb'],
            series: [{
                name: 'Hasil Lumpur',
                data: []
            }],
            yaxis: {
                title: {
                    text: 'Volume (ton)'
                },
                labels: {
                    formatter: (v) => v ? v.toFixed(0) + ' m³' : '0 m³'
                }
            },
            tooltip: {
                y: {
                    formatter: (v) => v ? v.toFixed(2) + ' m³' : '0 m³'
                }
            }
        });
        grafikHasilLumpur.render();

        // Grafik Distribusi (Donut)
        grafikDistribusi = new ApexCharts(document.querySelector("#grafikDistribusi"), {
            chart: {
                type: 'donut',
                height: 320,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#4bc0c0', '#ff6384', '#36a2eb'],
            series: [0, 0, 0],
            labels: ['Drain Lumpur', 'Jam Operasi SCP', 'Hasil Lumpur'],
            legend: {
                show: false
            },
            dataLabels: {
                enabled: true,
                formatter: (val, opts) => {
                    const nilai = opts.w.globals.series[opts.seriesIndex];
                    return nilai.toFixed(1);
                },
                style: {
                    fontSize: '11px',
                    fontWeight: 'bold',
                    colors: ['#010101'] // warna teks tetap putih
                },
                background: {
                    enabled: true,
                    foreColor: '#fff', // warna teks di dalam background
                    borderRadius: 4,
                    padding: 4,
                    opacity: 0.9,
                    borderWidth: 1,
                    borderColor: 'transparent',
                    // background mengikuti warna slice otomatis (default ApexCharts)
                },
                dropShadow: {
                    enabled: true,
                    top: 1,
                    left: 1,
                    blur: 2,
                    opacity: 0.6
                },
                dropShadow: {
                    enabled: true,
                    top: 1,
                    left: 1,
                    blur: 1,
                    opacity: 0.45
                }
            },
            tooltip: {
                y: {
                    formatter: (nilai, {
                        seriesIndex
                    }) => {
                        if (seriesIndex === 1) return nilai ? nilai.toFixed(2) + ' jam' : '0 jam';
                        return nilai ? nilai.toFixed(2) + ' m³' : '0 m³';
                    }
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: (w) => {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return total.toFixed(2);
                                }
                            }
                        }
                    }
                }
            }
        });
        grafikDistribusi.render();

        // Grafik Perbandingan Bulanan
        grafikPerbandinganBulanan = new ApexCharts(document.querySelector("#grafikPerbandinganBulanan"), {
            series: [{
                    name: 'Drain Lumpur',
                    data: []
                },
                {
                    name: 'Jam Operasi SCP',
                    data: []
                }
            ],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: true
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#4bc0c0', '#ff6384'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                }
            },
            dataLabels: {
                enabled: true,
                formatter: (nilai, opts) => {
                    return opts.seriesIndex === 0 ?
                        (nilai ? nilai.toFixed(0) + ' m³' : '0 m³') :
                        (nilai ? nilai.toFixed(0) + ' jam' : '0 jam');
                },
                style: {
                    fontSize: '12px',
                    colors: ['#000']
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: []
            },
            yaxis: [{
                    title: {
                        text: 'Drain Lumpur (m³)'
                    },
                    labels: {
                        formatter: (v) => v ? v.toFixed(0) + ' m³' : '0 m³'
                    }
                },
                {
                    opposite: true,
                    title: {
                        text: 'Jam Operasi (jam)'
                    },
                    labels: {
                        formatter: (v) => v ? v.toFixed(0) + ' jam' : '0 jam'
                    }
                }
            ],
            fill: {
                opacity: 1
            },
            tooltip: {
                y: [{
                        formatter: (v) => v ? v.toFixed(2) + ' m³' : '0 m³'
                    },
                    {
                        formatter: (v) => v ? v.toFixed(2) + ' jam' : '0 jam'
                    }
                ]
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        });
        grafikPerbandinganBulanan.render();

        // Grafik Sludge Content
        grafikSludgeContent = new ApexCharts(document.querySelector("#grafikSludgeContent"), {
            ...opsiGrafikArea,
            colors: ['#feb019'],
            series: [{
                name: 'Sludge Content (%)',
                data: []
            }],
            dataLabels: {
                enabled: true,
                formatter: (val) => val ? val.toFixed(0) + '%' : '0%',
                style: {
                    fontSize: '12px',
                    colors: ['#000']
                }
            },
            yaxis: {
                title: {
                    text: 'Persentase (%)'
                },
                labels: {
                    formatter: (v) => v ? v.toFixed(0) + '%' : '0%'
                }
            },
            tooltip: {
                y: {
                    formatter: (v) => v ? v.toFixed(0) + '%' : '0%'
                }
            }
        });
        grafikSludgeContent.render();

        // Grafik Pengangkutan (Bar)
        grafikPengangkutan = new ApexCharts(document.querySelector("#grafikPengangkutan"), {
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: true
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#775dd0'],
            series: [{
                name: 'Pengangkutan',
                data: []
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded'
                }
            },
            dataLabels: {
                enabled: true,
                formatter: (nilai) => {
                    return nilai ? nilai.toFixed(0) + ' Ton' : '0 Ton';
                },
                style: {
                    fontSize: '12px',
                    colors: ['#000']
                }
            },
            xaxis: {
                categories: [],
                labels: {
                    rotate: -45
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah'
                }
            },
            grid: {
                borderColor: '#f1f1f1'
            },
            tooltip: {
                y: {
                    formatter: (nilai) => {
                        return nilai ? nilai.toFixed(0) + ' Ton' : '0 Ton';
                    }
                }
            }
        });
        grafikPengangkutan.render();

        console.log('Semua grafik berhasil diinisialisasi');
    }
</script>
@endsection