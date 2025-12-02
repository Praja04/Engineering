@extends('layouts.app')

@section('title', 'Data KPI')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            {{-- Card Filter --}}
            {{-- <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-body">
                    <form id="filterForm" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="filterPeriode" class="form-label">Tipe Periode</label>
                            <select id="filterPeriode" class="form-select">
                                <option value="">-- Semua Periode --</option>
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

            {{-- Card Table --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data KPI</h5>
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
                                id="tableWeekly">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tgl Awal</th>
                                        <th>Tgl Akhir</th>
                                        <th>Finish Goods (Ton)</th>
                                        <th>Kecap Matang (Ton)</th>
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
                                        <th>Finish Goods (Ton)</th>
                                        <th>Kecap Matang (Ton)</th>
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
    <div class="modal fade" id="modalKpi" tabindex="-1" aria-labelledby="modalKpiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-3">
                <div class="modal-header">
                    <i class="mdi mdi-pencil me-2"></i>
                    <h5 class="modal-title" id="modalKpiLabel">Tambah Data KPI</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formKpi">
                    <div class="modal-body">
                        <input type="hidden" id="kpiId">

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
                            <label for="finish_goods" class="form-label">Finish Goods (Ton)</label>
                            <input type="number" id="finish_goods" class="form-control" step="0.01" min="0"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="kecap_matang" class="form-label">Kecap Matang (Ton)</label>
                            <input type="number" id="kecap_matang" class="form-control" step="0.01" min="0"
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
            const tableBody = $('#tableKpi tbody');
            const $modal = new bootstrap.Modal('#modalKpi');
            const $form = $('#formKpi');
            const today = new Date().toISOString().split('T')[0];
            $('#filterTanggal').val(today);

            const formatNumber = (num) => {
                const val = parseFloat(num);
                if (isNaN(val)) return '-';
                return val % 1 === 0 ? val.toFixed(0) : parseFloat(val.toString()).toString();
            };

            loadData('weekly');

            function loadData(periode = '', tanggal = '') {
                $.get("{{ route('kpi.get-data') }}", {
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
                        const finishGoods = formatNumber(item.finish_goods);
                        const kecapMatang = formatNumber(item.kecap_matang);

                        if (periode === 'weekly') {
                            const startDate = item.start_date ? `${item.start_date}` : '-';
                            const endDate = item.end_date ? `${item.end_date}` : '-';

                            tableBody.append(`
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${startDate}</td>
                                    <td>${endDate}</td>
                                    <td>${finishGoods}</td>
                                    <td>${kecapMatang}</td>
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
                                    <td>${finishGoods}</td>
                                    <td>${kecapMatang}</td>
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
            // $('#filterPeriode, #filterTanggal').on('change', function() {
            //     const periode = $('#filterPeriode').val();
            //     const tanggal = $('#filterTanggal').val();

            //     // panggil fungsi loadData dengan parameter filter
            //     loadData(periode, tanggal);
            // });

            // $('#btnReset').on('click', function() {
            //     $('#filterForm')[0].reset();
            //     $('#filterTanggal').val(today);

            //     // Reload data dengan kondisi default
            //     loadData($('#filterPeriode').val(), $('#filterTanggal').val());
            // });

            // Simpan / Update data
            $form.on('submit', function(e) {
                e.preventDefault();

                const id = $('#kpiId').val();
                const isUpdate = id !== '';
                const url = isUpdate ?
                    "{{ url('kpi/update') }}/" + id :
                    "{{ route('kpi.store') }}";
                const method = isUpdate ? 'PUT' : 'POST';
                const periode = $('#periodeTipe').val();

                const payload = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    periode_tipe: periode,
                    finish_goods: $('#finish_goods').val(),
                    kecap_matang: $('#kecap_matang').val(),
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
                                $('#modalKpi').modal('hide');
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
                        let message = 'Terjadi kesalahan saat menyimpan data!';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan!',
                            text: message
                        });
                    }
                });
            });

            // Edit data
            $('#periodeTipe').on('change', function() {
                const tipe = $(this).val();

                $('#groupWeeklyEdit, #groupMonthlyEdit').addClass('d-none');

                if (tipe === 'weekly') {
                    $('#groupWeeklyEdit').removeClass('d-none');
                } else if (tipe === 'monthly') {
                    $('#groupMonthlyEdit').removeClass('d-none');
                }
            });

            $(document).on('click', '.btnEdit', function() {
                const id = $(this).data('id');
                $.get("{{ url('kpi/show') }}/" + id, function(res) {
                    if (res.success) {
                        if (!res.success) {
                            return Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: res.message
                            });
                        }
                        const data = res.data;

                        $('#modalKpiLabel').text('Edit Data Kpi');
                        $('#kpiId').val(data.id);

                        $('#periodeTipe').val(data.periode_tipe).trigger('change');

                        if (data.periode_tipe === 'weekly') {
                            $('#editStartDate').val(data.start_date);
                            $('#editEndDate').val(data.end_date);
                        }

                        if (data.periode_tipe === 'monthly') {
                            $('#editMonth').val(data.month);
                        }

                        $('#finish_goods').val(formatNumber(res.data.finish_goods));
                        $('#kecap_matang').val(formatNumber(res.data.kecap_matang));

                        $('#modalKpi').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: res.message
                        });
                    }
                });
            });

            // Hapus
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
                            url: "{{ url('kpi/delete') }}/" + id,
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
                                    loadData();
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
                                let message = 'Terjadi kesalahan saat menghapus data!';

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan!',
                                    text: message
                                });
                            }
                        });
                    }
                });
            });
        })
    </script>
@endsection
