@extends('layouts.app')

@section('title', 'Form KPI')

@section('styles')
    <style>
        .kpi-type-card {
            cursor: pointer;
            transition: all 0.25s ease-in-out;
            border: 2px solid var(--vz-border-color, #e9ebec) !important;
            position: relative;
            background-color: var(--vz-card-bg, #fff);
        }

        .kpi-type-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
            border-color: rgba(var(--vz-primary-rgb, 64, 81, 137), 0.5) !important;
        }

        .kpi-type-card.active {
            border-color: var(--vz-primary, #405189) !important;
            background-color: rgba(var(--vz-primary-rgb, 64, 81, 137), 0.04);
            box-shadow: 0 4px 12px rgba(var(--vz-primary-rgb, 64, 81, 137), 0.12);
        }

        .kpi-type-card .check-indicator {
            display: none;
        }

        .kpi-type-card.active .check-indicator {
            display: block !important;
        }

        .input-group-text {
            min-width: 44px;
            justify-content: center;
        }

        .unit-addon {
            min-width: 55px;
            background-color: var(--vz-light, #f3f6f9);
            color: var(--vz-secondary-color, #6c757d);
            font-weight: 600;
            font-size: 0.82rem;
        }

        .section-title {
            position: relative;
            padding-bottom: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Header / Breadcrumb -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1 text-dark fw-bold">
                                <i class="mdi mdi-chart-line text-primary me-2"></i>Form Input Key Performance Indicator
                                (KPI)
                            </h4>
                            <p class="text-muted mb-0 fs-13">Pencatatan data produksi, konsumsi energi, dan utility mingguan
                                atau bulanan</p>
                        </div>
                        <div class="mt-3 mt-sm-0">
                            <a href="{{ route('kpi.data') }}" class="btn btn-outline-primary btn-sm">
                                <i class="mdi mdi-table me-1"></i> Data KPI
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-10">
                    <form id="formKpi" method="POST">
                        @csrf
                        <input type="hidden" id="periode_tipe" name="periode_tipe" value="">

                        <!-- STEP 1: PEMILIHAN PERIODE -->
                        <div class="card shadow-sm border-0 rounded-3 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="text-uppercase fw-bold text-muted mb-0 fs-12">
                                        <i class="mdi mdi-calendar-clock text-primary me-1"></i> 1. Tentukan Tipe Periode
                                    </h6>
                                    <span class="badge bg-light text-muted fw-normal fs-11">Pilih salah satu</span>
                                </div>

                                <div class="row g-3">
                                    <!-- Option Weekly -->
                                    <div class="col-md-6">
                                        <div class="card kpi-type-card rounded-3 mb-0 h-100" data-type="weekly">
                                            <div class="card-body p-3 d-flex align-items-start">
                                                <div class="avatar-sm flex-shrink-0 me-3">
                                                    <span class="avatar-title bg-primary-subtle rounded-3 fs-20">
                                                        <i class="mdi mdi-calendar-week"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <h5 class="fs-15 mb-1 fw-semibold">Periode Mingguan</h5>
                                                        <div class="check-indicator text-primary">
                                                            <i class="mdi mdi-check-circle fs-18"></i>
                                                        </div>
                                                    </div>
                                                    <p class="text-muted mb-0 fs-13">Pencatatan siklus mingguan (minimal 7
                                                        hari kerja)</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Option Monthly -->
                                    <div class="col-md-6">
                                        <div class="card kpi-type-card rounded-3 mb-0 h-100" data-type="monthly">
                                            <div class="card-body p-3 d-flex align-items-start">
                                                <div class="avatar-sm flex-shrink-0 me-3">
                                                    <span class="avatar-title bg-info-subtle rounded-3 fs-20">
                                                        <i class="mdi mdi-calendar-month"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <h5 class="fs-15 mb-1 fw-semibold">Periode Bulanan</h5>
                                                            <span
                                                                class="badge bg-info-subtle text-info fs-10 mb-1">Accounting</span>
                                                        </div>
                                                        <div class="check-indicator text-info">
                                                            <i class="mdi mdi-check-circle fs-18"></i>
                                                        </div>
                                                    </div>
                                                    <p class="text-muted mb-0 fs-13">Rekonsiliasi data bulanan & Biaya
                                                        Utility Cost</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Tanggal Weekly -->
                                <div id="formWeekly" class="d-none mt-4 pt-3 border-top">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="start_date" class="form-label fw-semibold">Tanggal Mulai (Start
                                                Date) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="mdi mdi-calendar text-muted"></i></span>
                                                <input type="date" id="start_date" name="start_date"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_date" class="form-label fw-semibold">Tanggal Selesai (End Date)
                                                <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="mdi mdi-calendar-check text-muted"></i></span>
                                                <input type="date" id="end_date" name="end_date" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-muted fs-12">
                                        <i class="mdi mdi-information-outline text-primary me-1"></i>Rentang waktu antara
                                        Start Date dan End Date minimal 7 hari.
                                    </div>
                                </div>

                                <!-- Form Tanggal Monthly -->
                                <div id="formMonthly" class="d-none mt-4 pt-3 border-top">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="month" class="form-label fw-semibold">Bulan Periode <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="mdi mdi-calendar-month text-muted"></i></span>
                                                <input type="month" id="month" name="month" class="form-control">
                                            </div>
                                            <div class="mt-2 text-muted fs-12">
                                                <i class="mdi mdi-information-outline text-info me-1"></i>Pilih bulan untuk
                                                data KPI bulanan (Accounting).
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: PARAMETER PRODUKSI & ENERGI -->
                        <div class="card shadow-sm border-0 rounded-3 mb-4">
                            <div class="card-body p-4">

                                <!-- Section Produksi -->
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title bg-primary-subtle rounded-circle">
                                            <i class="mdi mdi-factory fs-16"></i>
                                        </span>
                                    </div>
                                    <h6 class="fs-15 mb-0 fw-semibold">Hasil Produksi</h6>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="finish_goods" class="form-label fw-semibold">
                                            <span class="accounting-label" for="finish_goods">Nilai Finish Goods</span>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-package-variant-closed text-muted"></i></span>
                                            <input type="number" step="0.01" min="0" id="finish_goods"
                                                name="finish_goods" class="form-control" placeholder="Contoh: 120.50"
                                                required>
                                            <span class="input-group-text unit-addon">Ton</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="kecap_matang" class="form-label fw-semibold">
                                            <span class="accounting-label" for="kecap_matang">Kecap Matang</span> <span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-barrel text-muted"></i></span>
                                            <input type="number" step="0.01" min="0" id="kecap_matang"
                                                name="kecap_matang" class="form-control" placeholder="Contoh: 85.75"
                                                required>
                                            <span class="input-group-text unit-addon">Ton</span>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4 border-dashed opacity-50">

                                <!-- Section Energi & Utility -->
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle">
                                            <i class="mdi mdi-lightning-bolt fs-16"></i>
                                        </span>
                                    </div>
                                    <h6 class="fs-15 mb-0 fw-semibold">Konsumsi Energi & Utility</h6>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="listrik_prd" class="form-label fw-semibold">
                                            <span class="accounting-label" for="listrik_prd">Listrik PRD</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-flash text-warning"></i></span>
                                            <input type="number" step="0.01" min="0" id="listrik_prd"
                                                name="listrik_prd" class="form-control" placeholder="Contoh: 1500000">
                                            <span class="input-group-text unit-addon">KWH</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="listrik_bas" class="form-label fw-semibold">
                                            <span class="accounting-label" for="listrik_bas">Listrik BAS</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-office-building text-info"></i></span>
                                            <input type="number" step="0.01" min="0" id="listrik_bas"
                                                name="listrik_bas" class="form-control" placeholder="Contoh: 1500000">
                                            <span class="input-group-text unit-addon">KWH</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="steam" class="form-label fw-semibold">
                                            <span class="accounting-label" for="steam">Steam</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-cloud text-primary"></i></span>
                                            <input type="number" step="0.01" min="0" id="steam"
                                                name="steam" class="form-control" placeholder="Contoh: 5000">
                                            <span class="input-group-text unit-addon">Kg</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="batubara" class="form-label fw-semibold">
                                            <span class="accounting-label" for="batubara">Batubara</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="mdi mdi-fire text-danger"></i></span>
                                            <input type="number" step="0.01" min="0" id="batubara"
                                                name="batubara" class="form-control" placeholder="Contoh: 3000">
                                            <span class="input-group-text unit-addon">Kg</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section Utility Cost (Hanya Periode Bulanan) -->
                                <div id="groupMonthlyUtilityCost" class="d-none">
                                    <hr class="my-4 border-dashed opacity-50">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-xs me-2">
                                            <span class="avatar-title bg-success-subtle text-success rounded-circle">
                                                <i class="mdi mdi-cash-multiple fs-16"></i>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="fs-15 mb-0 fw-semibold">Utility Cost</h6>
                                            <span class="badge bg-success-subtle text-success fs-11">Bulanan Only</span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-7 col-lg-6">
                                            <label for="utility_cost" class="form-label fw-semibold">Total Utility Cost
                                                (Rp)</label>
                                            <div class="input-group">
                                                <span class="input-group-text fw-bold text-success">Rp</span>
                                                <input type="number" step="0.01" min="0" id="utility_cost"
                                                    name="utility_cost" class="form-control"
                                                    placeholder="Contoh: 150000000">
                                            </div>
                                            <div class="mt-1 text-muted fs-12">
                                                <i class="mdi mdi-information-outline me-1"></i>Masukkan total nominal
                                                biaya pemakaian utility untuk bulan ini.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Card Footer / Actions -->
                            <div class="card-footer bg-light-subtle d-flex justify-content-between align-items-center p-3">
                                <a href="{{ route('kpi.data') }}" class="btn btn-ghost-secondary">
                                    <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Data KPI
                                </a>
                                <div class="d-flex gap-2">
                                    <button type="button" id="btnReset" class="btn btn-secondary px-3">
                                        <i class="mdi mdi-refresh me-1"></i> Reset
                                    </button>
                                    <button type="submit" id="btnSubmit" class="btn btn-success px-4">
                                        <i class="mdi mdi-content-save me-1"></i> Simpan Data
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const $periodeTipe = $('#periode_tipe');
            const $btnSubmit = $('#btnSubmit');

            // Simpan label asli saat load pertama kali
            const originalLabels = {};
            $('.accounting-label').each(function() {
                const $label = $(this);
                const id = $label.attr('for');
                originalLabels[id] = $label.text().trim();
            });

            function updateForm() {
                const val = $periodeTipe.val();

                // Reset visual card selection
                $('.kpi-type-card').removeClass('active');
                if (val) {
                    $(`.kpi-type-card[data-type="${val}"]`).addClass('active');
                }

                // Reset containers
                $('#formWeekly').addClass('d-none');
                $('#formMonthly').addClass('d-none');
                $('#groupMonthlyUtilityCost').addClass('d-none');

                // Reset semua label ke teks asli
                $('.accounting-label').each(function() {
                    const $label = $(this);
                    const id = $label.attr('for');
                    if (originalLabels[id]) {
                        $label.text(originalLabels[id]);
                    }
                });

                if (val === 'weekly') {
                    $('#formWeekly').removeClass('d-none');
                } else if (val === 'monthly') {
                    $('#formMonthly').removeClass('d-none');
                    $('#groupMonthlyUtilityCost').removeClass('d-none');

                    // Tambahkan "Accounting" ke label
                    $('.accounting-label').each(function() {
                        const $label = $(this);
                        const currentText = $label.text().trim();
                        if (!currentText.toLowerCase().includes('accounting')) {
                            $label.text(currentText + ' Accounting');
                        }
                    });
                }
            }

            // Click event pada interactive cards
            $('.kpi-type-card').on('click', function() {
                const type = $(this).data('type');
                $periodeTipe.val(type);
                updateForm();
            });

            // Reset manual
            $('#btnReset').on('click', function() {
                $('#formKpi')[0].reset();
                $periodeTipe.val('');
                updateForm();
            });

            // Submit form
            $('#formKpi').on('submit', function(e) {
                e.preventDefault();

                const tipe = $periodeTipe.val();

                if (!tipe) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Tipe Periode',
                        text: 'Silakan tentukan tipe periode (Mingguan atau Bulanan) terlebih dahulu!',
                    });
                    return;
                }

                // VALIDASI WEEKLY
                if (tipe === 'weekly') {
                    const start = $('#start_date').val();
                    const end = $('#end_date').val();

                    if (!start || !end) {
                        Swal.fire('Error', 'Start dan End Date wajib diisi!', 'error');
                        return;
                    }

                    const diffMs = new Date(end) - new Date(start);
                    const diffDays = diffMs / (1000 * 60 * 60 * 24);

                    if (diffDays < 6) {
                        Swal.fire('Error', 'Rentang tanggal minimal 7 hari!', 'error');
                        return;
                    }
                }

                // VALIDASI MONTHLY
                if (tipe === 'monthly') {
                    const month = $('#month').val();

                    if (!month) {
                        Swal.fire('Error', 'Bulan wajib dipilih!', 'error');
                        return;
                    }
                }

                const formData = $(this).serialize();

                // Set loading state
                $btnSubmit.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...'
                );

                $.ajax({
                    url: "{{ route('kpi.store') }}",
                    type: 'POST',
                    data: formData,
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Disimpan!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#formKpi')[0].reset();
                            $periodeTipe.val('');
                            updateForm();
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan',
                                text: res.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const response = xhr.responseJSON;
                            if (response.message) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message,
                                });
                            } else if (response.errors) {
                                let pesan = '';
                                Object.values(response.errors).forEach(err => {
                                    pesan += `• ${err[0]}\n`;
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validasi Gagal!',
                                    text: pesan,
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan!',
                                text: 'Gagal menyimpan data KPI. Coba lagi nanti.',
                            });
                        }
                    },
                    complete: function() {
                        $btnSubmit.prop('disabled', false).html(
                            '<i class="mdi mdi-content-save me-1"></i> Simpan Data KPI');
                    }
                });
            });

            // Inisialisasi awal
            updateForm();
        });
    </script>
@endsection
