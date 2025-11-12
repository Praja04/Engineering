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

                        <!-- Tanggal -->
                        <div class="mb-3">
                            <label for="tanggal" class="form-label fw-semibold">Tanggal</label>
                            <input type="date" id="tanggal" name="tanggal" class="form-control" required>
                        </div>

                        <!-- Nilai FG -->
                        <div class="mb-3">
                            <label for="fg" class="form-label fw-semibold">Nilai FG (Ton)</label>
                            <input type="number" step="0.01" id="fg" name="fg" class="form-control"
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
            $('#formKpi').on('submit', function(e) {
                e.preventDefault();

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
