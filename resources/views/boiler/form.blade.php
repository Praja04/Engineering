@extends('layouts.app')

@section('title', 'Form Boiler')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Boiler</h5>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalBoiler">
                        <i class="mdi mdi-plus-circle-outline me-2"></i> Tambah Data Boiler
                    </button>
                </div>

                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-4">
                            <label for="periodeTipe" class="form-label">Start Date</label>
                            <input type="date" id="startDate" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="periodeTipe" class="form-label">End Date</label>
                            <input type="date" id="endDate" class="form-control">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" id="filterButton" class="btn btn-primary w-100 me-2">
                                <i class="mdi mdi-filter-variant me-2"></i> Filter
                            </button>
                            <button type="button" id="resetButton" class="btn btn-secondary w-100">
                                <i class="mdi mdi-refresh me-2"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table Data --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Boiler</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderedless table-striped text-nowrap" id="boilerTable"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Batu Bara (Ton)</th>
                                    <th>Steam (m³)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data akan diisi melalui AJAX --}}
                            </tbody>
                        </table>
                        <div id="pagination-container" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah/Edit --}}
    <div class="modal fade" id="modalBoiler" tabindex="-1" aria-labelledby="modalBoilerLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-3">
                <div class="modal-header">
                    <i class="mdi mdi-pencil me-2"></i>
                    <h5 class="modal-title" id="modalBoilerLabel">Tambah Data Boiler</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formBoiler">
                    <div class="modal-body">
                        <input type="hidden" id="boilerId">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" id="date" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="batuBara" class="form-label">Batu Bara (Ton)</label>
                            <input type="number" id="batuBara" class="form-control" step="0.01" min="0">
                        </div>

                        <div class="mb-3">
                            <label for="steam" class="form-label">Steam (m³)</label>
                            <input type="number" id="steam" class="form-control" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const formBoiler = $('#formBoiler');

            // Submit form
            formBoiler.on('submit', function(e) {
                e.preventDefault();

                const date = $('#date').val();
                const batuBara = $('#batuBara').val();
                const steam = $('#steam').val();

                if (!date || !steam || !batuBara) {
                    Swal.fire('Error', 'Field wajib diisi!', 'error');
                    return;
                }

                const id = $('#boilerId').val();
                const isUpdate = id !== '';
                const url = isUpdate ?
                    "{{ url('boiler/update') }}/" + id :
                    "{{ route('boiler.store') }}";
                const method = isUpdate ? 'PUT' : 'POST';

                const formData = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    date: date,
                    batu_bara: $('#batuBara').val(),
                    steam: $('#steam').val(),
                };

                $.ajax({
                    url: url,
                    type: method,
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

                            formBoiler.trigger('reset');
                            $('#modalBoiler').modal('hide');
                            loadData();
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

            loadData();

            function loadData(startDate = '', endDate = '', page = 1) {
                $.get("{{ url('api/boiler/get-data') }}", {
                    start_date: startDate,
                    end_date: endDate,
                    page: page,
                    per_page: 10
                }, function(response) {
                    const tableBody = $('#boilerTable tbody');
                    tableBody.empty();

                    // Tampilkan data
                    if (response.data.length === 0) {
                        tableBody.append(`
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="mdi mdi-database-off mdi-36px mb-2"></i>
                                        <span class="fw-semibold">Tidak ada data ditemukan</span>
                                    </div>
                                </td>
                            </tr>
                        `);
                    } else {
                        $.each(response.data, function(i, item) {
                            const no = (response.current_page - 1) * response.per_page + i + 1;
                            const batuBara = formatNumber(item.batu_bara);
                            const steam = formatNumber(item.steam);
                            const date = item.date ? item.date : '-';

                            tableBody.append(`
                                <tr>
                                    <td>${no}</td>
                                    <td>${date}</td>
                                    <td>${batuBara}</td>
                                    <td>${steam}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-1 btnEdit" data-id="${item.id}">
                                            <i class="mdi mdi-pencil"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger btnDelete" data-id="${item.id}">
                                            <i class="mdi mdi-delete"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    }

                    renderPagination(response);
                });
            }

            function renderPagination(data) {
                const pagination = $(
                    '#pagination-container');
                pagination.empty();

                if (data.last_page <= 1) return;

                let html = '<nav><ul class="pagination justify-content-center">';

                // Previous
                html += `<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${data.current_page - 1}">Previous</a>
                    </li>
                `;

                // Nomor halaman (simple: tampilkan 5 halaman sekitar current)
                const startPage = Math.max(1, data.current_page - 2);
                const endPage = Math.min(data.last_page, data.current_page + 2);

                for (let i = startPage; i <= endPage; i++) {
                    html += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                // Next
                html += `<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${data.current_page + 1}">Next</a>
                    </li>
                `;

                html += '</ul></nav>';
                pagination.html(html);
            }

            // Event listener untuk klik pagination
            $(document).on('click', '.pagination a[data-page]', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page) {
                    loadData($('#start_date').val(), $('#end_date').val(),
                        page);
                }
            });

            const formatNumber = (num) => {
                const val = parseFloat(num);
                if (isNaN(val)) return '-';
                return val % 1 === 0 ? val.toFixed(0) : parseFloat(val.toString()).toString();
            };

            $(document).on('click', '.btnEdit', function() {
                const id = $(this).data('id');

                $.get("{{ url('boiler/show') }}/" + id, function(res) {

                    if (!res.success) {
                        return Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: res.message
                        });
                    }

                    const data = res.data;

                    $('#modalBoilerLabel').text('Edit Data Boiler');
                    $('#boilerId').val(data.id);

                    // SET VALUE NORMAL
                    $('#date').val(data.date);
                    $('#batuBara').val(formatNumber(data.batu_bara));
                    $('#steam').val(formatNumber(data.steam));

                    $('#modalBoiler').modal('show');
                });
            });

            $('#modalBoiler').on('hidden.bs.modal', function() {
                $('#modalBoilerLabel').text('Tambah Data Boiler');
                $('#boilerId').val('');
                $('#formBoiler')[0].reset();
            });

            // Hapus data pakai SweetAlert2
            $(document).on('click', '.btnDelete', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin ingin menghapus data ini?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('boiler/delete') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: res.message,
                                        timer: 1000,
                                        showConfirmButton: false
                                    });
                                    loadData('weekly');
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: 'Gagal menghapus data.'
                                    });
                                }
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan!',
                                    text: 'Terjadi kesalahan saat menghapus data!'
                                });
                            }
                        });
                    }
                });
            });

            $('#filterButton').on('click', function() {
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();

                loadData(startDate, endDate);
            });

            $('#resetButton').on('click', function() {
                $('#startDate').val('');
                $('#endDate').val('');
                loadData();
            });
        });
    </script>
@endsection
