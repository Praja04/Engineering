@extends('layouts.app')

@section('title', 'Form KPI')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="mdi mdi-chart-line me-1"></i> Form Input KPI</h5>
                </div>
                <div class="card-body p-4">
                    <form id="formKpi" method="POST">
                        @csrf

                        <!-- Jenis Periode -->
                        <div class="mb-3">
                            <label for="periode_tipe" class="form-label fw-semibold">Tipe Periode</label>
                            <select class="form-select" id="periode_tipe" name="periode_tipe" required>
                                <option value="" disabled selected>Pilih tipe periode</option>
                                <option value="weekly">Mingguan</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>

                        <!-- Periode Dinamis -->
                        <div id="formWeekly" class="d-none">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Mulai (Start Date)</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Sampai (End Date)</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div id="formMonthly" class="d-none">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Bulan</label>
                                <input type="month" id="month" name="month" class="form-control">
                            </div>
                        </div>

                        <!-- Nilai FG -->
                        <div class="mb-3">
                            <label for="finish_goods" class="form-label fw-semibold">Nilai Finish Goods (Ton)</label>
                            <input type="number" step="0.01" id="finish_goods" name="finish_goods" class="form-control"
                                placeholder="Contoh: 120.5" required>
                        </div>

                        <!-- Nilai Kecap Matang -->
                        <div class="mb-3">
                            <label for="kecap_matang" class="form-label fw-semibold">Kecap Matang (Ton)</label>
                            <input type="number" step="0.01" id="kecap_matang" name="kecap_matang" class="form-control"
                                placeholder="Contoh: 85.75" required>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="reset" class="btn btn-secondary">
                                <i class="mdi mdi-refresh me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-content-save me-1"></i> Simpan
                            </button>
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
            $('#periode_tipe').on('change', function() {
                const val = $(this).val();

                // Hide semua dulu
                $('#formWeekly').addClass('d-none');
                $('#formMonthly').addClass('d-none');

                // Show sesuai tipe
                if (val === 'weekly') {
                    $('#formWeekly').removeClass('d-none');
                } else if (val === 'monthly') {
                    $('#formMonthly').removeClass('d-none');
                }
            });

            $('#formKpi').on('submit', function(e) {
                e.preventDefault();

                const tipe = $('#periode_tipe').val();

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

                $.ajax({
                    url: "{{ route('kpi.store') }}",
                    type: 'POST',
                    data: formData,
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            $('#formKpi')[0].reset();
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
                    }
                });
            });
        });
    </script>
@endsection
