@extends('layouts.app')

@section('title', 'Master Timbangan')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="mdi mdi-ruler me-2"></i> Master Timbangan
                    </h5>
                    <button class="btn btn-primary" id="btnAdd">
                        <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Data
                    </button>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-hover align-middle text-center" id="tableMaster">
                        <thead class="table-light">
                            <tr>
                                <th>Beban</th>
                                <th>Standar Massa</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data via AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Tambah/Edit -->
            <div class="modal fade" id="modalMaster" tabindex="-1" aria-labelledby="modalMasterLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header">
                            <h5 class="modal-title fw-semibold" id="modalTitle">Tambah Data</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="formMaster">
                            @csrf
                            <input type="hidden" id="id" name="id">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="beban" class="form-label fw-semibold">Beban</label>
                                    <input type="text" name="beban" id="beban" class="form-control"
                                        placeholder="Contoh: 5000" required>
                                </div>
                                <div class="mb-3">
                                    <label for="standar_massa" class="form-label fw-semibold">Standar Massa</label>
                                    <input type="number" step="0.0001" name="standar_massa" id="standar_massa"
                                        class="form-control" placeholder="Contoh: 10.0000" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const modal = new bootstrap.Modal('#modalMaster');
            const tableBody = $('#tableMaster tbody');

            function formatNumber(value) {
                if (value === null || value === undefined || value === '') return '-';
                let num = parseFloat(value);
                return num.toString().replace(/(\.\d*?[1-9])0+$|\.0*$/, '$1');
            }
            // Fetch Data
            function loadData() {
                $.get("{{ route('timbangan.index') }}", function(data) {
                    let rows = '';
                    if (data.length === 0) {
                        rows = `<tr><td colspan="4" class="text-muted">Belum ada data.</td></tr>`;
                    } else {
                        $.each(data, function(i, item) {
                            rows += `
                        <tr>
                            <td>${item.beban}</td>
                            <td>${formatNumber(item.standar_massa)}</td>
                            <td>
                                <button class="btn btn-sm btn-warning me-1 btnEdit" data-id="${item.id}" title="Edit">
                                    <i class="mdi mdi-pencil-outline"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btnDelete" data-id="${item.id}" title="Hapus">
                                    <i class="mdi mdi-delete-outline"></i>
                                </button>
                            </td>
                        </tr>`;
                        });
                    }
                    tableBody.html(rows);
                });
            }
            loadData();

            // Add Button
            $('#btnAdd').click(function() {
                $('#formMaster')[0].reset();
                $('#id').val('');
                $('#modalTitle').text('Tambah Data');
                modal.show();
            });

            // Save Data
            $('#formMaster').submit(function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                const id = $('#id').val();
                const url = id ?
                    "{{ route('timbangan.update', ':id') }}".replace(':id', id) :
                    "{{ route('timbangan.store') }}";
                const method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function() {
                        modal.hide();
                        loadData();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data berhasil disimpan.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        let response = xhr.responseJSON;
                        if (response && response.errors) {
                            let message = Object.values(response.errors).flat().join('<br>');
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                html: message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan',
                                text: 'Terjadi kesalahan server.'
                            });
                        }
                    }
                });
            });

            // Edit Button
            $(document).on('click', '.btnEdit', function() {
                const id = $(this).data('id');
                $.get("{{ url('kalibrasi/master/timbangan') }}/" + id, function(data) {
                    $('#modalTitle').text('Edit Data');
                    $('#id').val(data.id);
                    $('#no').val(data.no);
                    $('#nilai_master').val(data.nilai_master);
                    modal.show();
                });
            });

            // Delete Button
            $(document).on('click', '.btnDelete', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('kalibrasi/master/timbangan') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                loadData();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: 'Data berhasil dihapus.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Tidak dapat menghapus data.',
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
