@extends('layouts.app')

@section('title', 'Form Boiler')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header">
                    <h5 class="mb-0">Form Boiler</h5>
                </div>

                <div class="card-body">
                    {{-- Pilihan Jenis Input --}}
                    <div class="mb-4">
                        <label for="tipePeriode" class="form-label fw-bold">Pilih Tipe Periode</label>
                        <select id="tipePeriode" class="form-select" required>
                            <option value="">Pilih tipe periode</option>
                            <option value="weekly">Mingguan</option>
                            <option value="monthly">Bulanan</option>
                        </select>
                    </div>

                    {{-- Form Input Data --}}
                    <form id="formBoiler" class="d-none">
                        <div class="row mb-3 g-3">
                            <!-- Weekly -->
                            <div id="groupWeekly" class="d-none">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Tanggal Mulai</label>
                                        <input type="date" id="startDate" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Tanggal Akhir</label>
                                        <input type="date" id="endDate" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly -->
                            <div id="groupMonthly" class="d-none">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Pilih Bulan</label>
                                        <input type="month" id="monthPicker" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- Batu Bara -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Batu Bara (Ton)</label>
                                <input type="number" id="batuBara" name="batu_bara" step="0.01" min="0"
                                    class="form-control" placeholder="Contoh: 25.5" required>
                            </div>

                            <!-- Steam -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Steam (m³)</label>
                                <input type="number" id="steam" name="steam" step="0.01" min="0"
                                    class="form-control" placeholder="Contoh: 180.0" required>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success px-4">
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
            const $tipePeriode = $('#tipePeriode');
            const $formBoiler = $('#formBoiler');
            const $groupWeekly = $('#groupWeekly');
            const $groupMonthly = $('#groupMonthly');

            // Saat memilih jenis input
            $tipePeriode.on('change', function() {
                const val = $(this).val();

                // Reset tampilan
                $groupWeekly.addClass('d-none');
                $groupMonthly.addClass('d-none');
                $formBoiler.addClass('d-none');

                if (val === 'weekly') {
                    $groupWeekly.removeClass('d-none');
                    $formBoiler.removeClass('d-none');
                }

                if (val === 'monthly') {
                    $groupMonthly.removeClass('d-none');
                    $formBoiler.removeClass('d-none');
                }
            });

            // Submit form
            $formBoiler.on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    periode_tipe: $tipePeriode.val(),
                    batu_bara: $('#batuBara').val(),
                    steam: $('#steam').val(),
                };

                const val = $tipePeriode.val();

                if (val === 'weekly') {
                    const start = $('#startDate').val();
                    const end = $('#endDate').val();

                    if (!start || !end) {
                        Swal.fire('Error', 'Tanggal mulai dan akhir wajib diisi!', 'error');
                        return;
                    }

                    const diffMs = new Date(end) - new Date(start);
                    const diffDays = diffMs / (1000 * 60 * 60 * 24);

                    if (diffDays < 6) {
                        Swal.fire('Error', 'Rentang tanggal minimal harus 7 hari!', 'error');
                        return;
                    }

                    formData.start_date = start;
                    formData.end_date = end;
                }

                if (val === 'monthly') {
                    const month = $('#monthPicker').val();

                    if (!month) {
                        Swal.fire('Error', 'Silakan pilih bulan!', 'error');
                        return;
                    }

                    formData.month = month;
                }

                $.ajax({
                    url: "{{ route('boiler.store') }}", // route dinamis Laravel
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1000,
                                showConfirmButton: false
                            });

                            $formBoiler.trigger('reset');
                            $tipePeriode.val('');
                            $formBoiler.addClass('d-none');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal menyimpan data!'
                            });
                        }
                    },
                    error: function(xhr) {
                        // console.error(xhr.responseText);
                        let message = 'Terjadi kesalahan saat menyimpan data!';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: message
                        });
                    }
                });
            });
        });
    </script>
@endsection
