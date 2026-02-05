@extends('layouts.app')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Master Mesin Maintenance</h5>
                    <button class="btn btn-primary" id="btnAdd"> <i class="mdi mdi-plus me-2"></i>Tambah Mesin</button>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover" id="tableMesin">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Maintenance</th>
                                <th>Nama Mesin</th>
                                <th>Lokasi</th>
                                <th>Frekuensi</th>
                                <th>Aktif</th>
                                <th>Dibuat</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Form -->
            <div class="modal fade" id="modalMesin" tabindex="-1">
                <div class="modal-dialog">
                    <form id="formMesin">
                        @csrf
                        <input type="hidden" id="id">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Form Mesin</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-2">
                                    <label>Jenis Maintenance <span class="text-danger">*</span></label>
                                    <select class="form-control" id="jenis_mtc" required>
                                        <option value="">-- Pilih Jenis Maintenance --</option>
                                        <option value="motor_pump">Motor Pump</option>
                                        <option value="utility">Utility</option>
                                        <option value="electrical">Electrical</option>
                                        <option value="refrigerasi">Refrigerasi</option>
                                        <option value="electric_engine">Electric Engine</option>
                                        <option value="diesel_engine">Diesel Engine</option>
                                        <option value="sipil">Sipil</option>
                                        <option value="battery">Battery</option>
                                        <option value="electric_p2h">Electric P2h</option>
                                        <option value="diesel_p2h">Diesel P2h</option>
                                        <option value="others">Lainnya</option>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label>Nama Mesin <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_mesin" required>
                                </div>
                                <div class="mb-2">
                                    <label>Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lokasi" required>
                                </div>
                                <div class="mb-2">
                                    <label>Frekuensi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="frekuensi" required>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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

            loadData();

            function formatJenisMtc(value) {
                if (!value) return '-';

                return value
                    .replace(/_/g, ' ') // ganti underscore → spasi
                    .replace(/\b\w/g, char => char.toUpperCase()); // Capitalize tiap kata
            }

            function loadData() {
                $.get("{{ url('api/mtc/master/mesin/get-data') }}", function(res) {
                    let html = '';
                    res.data.forEach((item, index) => {
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${formatJenisMtc(item.jenis_mtc)}</td>
                                <td>${item.nama_mesin}</td>
                                <td>${item.lokasi}</td>
                                <td>${item.frekuensi}</td>
                                <td>
                                    <span class="badge ${item.aktif ? 'bg-success' : 'bg-danger'}">
                                        ${item.aktif ? 'Aktif' : 'Non Aktif'}
                                    </span>
                                </td>
                                <td>${item.created_at.substring(0, 10)}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm btnEdit" data-id='${JSON.stringify(item)}'>Edit</button>
                                    <button class="btn btn-danger btn-sm btnDelete" data-id="${item.id}">Hapus</button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tableMesin tbody').html(html);
                });
            }

            // Tambah
            $('#btnAdd').click(function() {
                $('#formMesin')[0].reset();
                $('#id').val('');
                $('#modalMesin').modal('show');
            });

            // Simpan / Update
            $('#formMesin').submit(function(e) {
                e.preventDefault();

                let id = $('#id').val();
                let isUpdate = id !== '';

                let url = isUpdate ?
                    `{{ url('mtc/master/mesin/update') }}/${id}` :
                    `{{ url('mtc/master/mesin/store') }}`;

                $.ajax({
                    url: url,
                    type: 'POST', // SELALU POST
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: isUpdate ? 'PUT' : 'POST',

                        jenis_mtc: $('#jenis_mtc').val(),
                        nama_mesin: $('#nama_mesin').val(),
                        lokasi: $('#lokasi').val(),
                        frekuensi: $('#frekuensi').val(),
                        aktif: $('#aktif').val()
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Menyimpan data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message ?? 'Data berhasil disimpan',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        $('#modalMesin').modal('hide');
                        $('#formMesin')[0].reset();
                        $('#id').val('');

                        loadData();
                    },
                    error: function(xhr) {
                        let msg = 'Terjadi kesalahan';

                        if (xhr.status === 422) {
                            msg = Object.values(xhr.responseJSON.errors)
                                .map(err => err[0])
                                .join('\n');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: msg
                        });
                    }
                });
            });

            // Edit
            $(document).on('click', '.btnEdit', function() {
                let data = $(this).data('id');
                if (typeof data === 'string') {
                    data = JSON.parse(data);
                }

                $('#id').val(data.id);
                $('#jenis_mtc').val(data.jenis_mtc);
                $('#nama_mesin').val(data.nama_mesin);
                $('#lokasi').val(data.lokasi);
                $('#frekuensi').val(data.frekuensi);

                // boolean → string (select)
                $('#aktif').val(data.aktif ? '1' : '0');

                $('#modalMesin').modal('show');
            });


            // Delete
            $(document).on('click', '.btnDelete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus?',
                    text: 'Data tidak bisa dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('mtc/master/mesin/delete') }}/${id}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                loadData();
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
