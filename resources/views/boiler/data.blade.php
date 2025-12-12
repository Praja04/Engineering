@extends('layouts.app')

@section('title', 'Data Boiler')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            {{-- Card Filter --}}
            {{-- <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-body">
                    <form id="filterForm" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="filterJenis" class="form-label">Jenis Input</label>
                            <select id="filterJenis" class="form-select">
                                <option value="">-- Semua Jenis --</option>
                                <option value="weekly">Mingguan</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="filterTanggal" class="form-label">Tanggal</label>
                            <input type="date" id="filterTanggal" class="form-control">
                        </div>

                        <div class="col-md-4 d-flex">
                            <button type="button" id="btnReset" class="btn btn-secondary w-100">
                                <i class="mdi mdi-refresh me-1"></i> Reset Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div> --}}

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Boiler</h5>
                </div>

                <div class="card-body">

                    <!-- TABS -->
                    <ul class="nav nav-pills nav-justified mb-3" id="boilerTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="weekly-tab" data-bs-toggle="tab"
                                data-bs-target="#weeklyPane" type="button" role="tab">
                                Mingguan
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthlyPane"
                                type="button" role="tab">
                                Bulanan
                            </button>
                        </li>
                    </ul>

                    <!-- TAB CONTENT -->
                    <div class="tab-content">

                        <!-- WEEKLY TAB -->
                        <div class="tab-pane fade show active" id="weeklyPane" role="tabpanel">
                            <table class="table table-borderless table-striped table-hover align-middle text-nowrap"
                                id="tableBoiler">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Batu Bara (Ton)</th>
                                        <th>Steam (m³)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <!-- MONTHLY TAB -->
                        <div class="tab-pane fade" id="monthlyPane" role="tabpanel">
                            <table class="table table-borderless table-striped table-hover align-middle text-nowrap"
                                id="tableMonthly">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Bulan</th>
                                        <th>Batu Bara (Ton)</th>
                                        <th>Steam (m³)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

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
                            <label for="periodeTipe" class="form-label">Periode Tipe</label>
                            <select id="periodeTipe" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="weekly">Mingguan</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="groupWeeklyEdit">
                            <label class="form-label fw-bold">Periode Mingguan</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="date" id="editStartDate" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" id="editEndDate" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="groupMonthlyEdit">
                            <label class="form-label fw-bold">Periode Bulanan</label>
                            <input type="month" id="editMonth" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="batuBara" class="form-label">Batu Bara (Ton)</label>
                            <input type="number" id="batuBara" class="form-control" step="0.01" min="0"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="steam" class="form-label">Steam (m³)</label>
                            <input type="number" id="steam" class="form-control" step="0.01" min="0"
                                required>
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
            const tableBody = $('#tableBoiler tbody');
            const form = $('#formBoiler');
            const today = new Date().toISOString().split('T')[0];
            $('#filterTanggal').val(today);

            const formatNumber = (num) => {
                const val = parseFloat(num);
                if (isNaN(val)) return '-';
                return val % 1 === 0 ? val.toFixed(0) : parseFloat(val.toString()).toString();
            };

            loadData('weekly');

            function loadData(periode = '', tanggal = '') {
                $.get("{{ route('boiler.get-data') }}", {
                    periode_tipe: periode,
                    tanggal: tanggal
                }, function(data) {

                    const tableBody = periode === 'weekly' ?
                        $('#tableWeekly tbody') :
                        $('#tableMonthly tbody');

                    tableBody.empty();

                    if (data.length === 0) {
                        const colspan = periode === 'weekly' ? 6 : 5;

                        tableBody.append(`
                            <tr>
                                <td colspan="${colspan}" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="mdi mdi-database-off mdi-36px mb-2"></i>
                                        <span class="fw-semibold">Tidak ada data ditemukan</span>
                                    </div>
                                </td>
                            </tr>
                        `);
                        return;
                    }

                    $.each(data, function(i, item) {
                        const batuBara = formatNumber(item.batu_bara);
                        const steam = formatNumber(item.steam);

                        if (periode === 'weekly') {
                            const startDate = item.start_date ? `${item.start_date}` : '-';
                            const endDate = item.end_date ? `${item.end_date}` : '-';

                            tableBody.append(`
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${startDate}</td>
                                    <td>${endDate}</td>
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

                        } else if (periode === 'monthly') {
                            const month = item.month ?? '-';

                            tableBody.append(`
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${month}</td>
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
                        }
                    });

                });
            }

            $('#weekly-tab').on('click', () => loadData('weekly'));
            $('#monthly-tab').on('click', () => loadData('monthly'));

            // Filter otomatis ketika jenis atau tanggal berubah
            // $('#filterJenis, #filterTanggal').on('change', function() {
            //     const jenis = $('#filterJenis').val();
            //     const tanggal = $('#filterTanggal').val();

            //     // panggil fungsi loadData dengan parameter filter
            //     loadData(jenis, tanggal);
            // });

            // $('#btnReset').on('click', function() {
            //     $('#filterForm')[0].reset();
            //     $('#filterTanggal').val(today);

            //     // Reload data dengan kondisi default
            //     loadData($('#filterJenis').val(), $('#filterTanggal').val());
            // });

            // Simpan / Update data
            form.on('submit', function(e) {
                e.preventDefault();

                const id = $('#boilerId').val();
                const isUpdate = id !== '';
                const url = isUpdate ?
                    "{{ url('boiler/update') }}/" + id :
                    "{{ route('boiler.store') }}";
                const method = isUpdate ? 'PUT' : 'POST';
                const periode = $('#periodeTipe').val();

                let payload = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    periode_tipe: periode,
                    batu_bara: $('#batuBara').val(),
                    steam: $('#steam').val(),
                };

                // WEEKLY
                if (periode === 'weekly') {
                    payload.start_date = $('#editStartDate').val();
                    payload.end_date = $('#editEndDate').val();
                }

                // MONTHLY
                if (periode === 'monthly') {
                    payload.month = $('#editMonth').val();
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: payload,
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => {
                                $('#modalBoiler').modal('hide');
                                loadData(periode);
                            });
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

            // Edit data
            $('#periodeTipe').on('change', function() {
                const tipe = $(this).val();

                // Reset visibility
                $('#groupWeeklyEdit, #groupMonthlyEdit').addClass('d-none');

                if (tipe === 'weekly') {
                    $('#groupWeeklyEdit').removeClass('d-none');
                } else if (tipe === 'monthly') {
                    $('#groupMonthlyEdit').removeClass('d-none');
                }
            });

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

                    // SET PERIODE TIPE
                    $('#periodeTipe').val(data.periode_tipe).trigger('change');

                    if (data.periode_tipe === 'weekly') {
                        $('#editStartDate').val(data.start_date);
                        $('#editEndDate').val(data.end_date);
                    }

                    if (data.periode_tipe === 'monthly') {
                        $('#editMonth').val(data.month);
                    }

                    // SET VALUE NORMAL
                    $('#editMonth').val(data.month);
                    $('#batuBara').val(formatNumber(data.batu_bara));
                    $('#steam').val(formatNumber(data.steam));

                    $('#modalBoiler').modal('show');
                });
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
        })
    </script>
@endsection
