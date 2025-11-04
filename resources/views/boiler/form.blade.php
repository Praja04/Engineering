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
                        <label for="jenisInput" class="form-label fw-bold">Pilih Jenis Input</label>
                        <select id="jenisInput" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="weekly">Mingguan</option>
                            <option value="monthly">Bulanan</option>
                        </select>
                    </div>

                    {{-- Form Input Data --}}
                    <form id="formBoiler" class="d-none">
                        <div class="row mb-3 g-3">
                            <div class="col-md-4">
                                <label for="tanggal" class="form-label fw-bold">Tanggal</label>
                                <input type="date" id="tanggal" name="tanggal" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label for="batuBara" class="form-label fw-bold">Batu Bara (Ton)</label>
                                <input type="number" id="batuBara" name="batu_bara" step="0.01" min="0"
                                    class="form-control" placeholder="Contoh: 25.5" required>
                            </div>

                            <div class="col-md-4">
                                <label for="steam" class="form-label fw-bold">Steam (m³)</label>
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
            const $jenisInput = $('#jenisInput');
            const $formBoiler = $('#formBoiler');

            // Saat memilih jenis input
            $jenisInput.on('change', function() {
                if ($(this).val() === 'weekly' || $(this).val() === 'monthly') {
                    $formBoiler.removeClass('d-none');
                } else {
                    $formBoiler.addClass('d-none');
                }
            });

            // Submit form
            $formBoiler.on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    jenis_input: $jenisInput.val(), // sesuaikan field di backend
                    tanggal: $('#tanggal').val(),
                    batu_bara: $('#batuBara').val(),
                    steam: $('#steam').val(),
                };

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
                            $jenisInput.val('');
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
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan!',
                            text: 'Terjadi kesalahan saat menyimpan data!'
                        });
                    }
                });
            });
        });
    </script>
@endsection
