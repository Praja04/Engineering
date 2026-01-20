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
            <div class="card shadow-sm border-0 rounded-3 mb-3" id="filterCard">
                <div class="card-body">
                    <form id="filterForm" class="row g-3 align-items-end">
                        <div class="col-md-6" id="filterRangeContainer">
                            <!-- Akan diisi dinamis berdasarkan tab -->
                        </div>

                        <div class="col-md-6 d-flex align-items-end gap-2">
                            <button type="button" id="btnFilter" class="btn btn-primary w-50">
                                <i class="mdi mdi-filter me-1"></i> Filter
                            </button>
                            <button type="button" id="btnReset" class="btn btn-secondary w-50">
                                <i class="mdi mdi-refresh me-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data KPI <span id="accounting" class="d-none">Accounting</span></h5>
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
                                        {{-- <th>Invoice Listrik</th> --}}
                                        <th>Steam</th>
                                        <th>Batubara</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div id="pagination-weekly" class="mt-3"></div>
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
                                        <th>Invoice Listrik</th>
                                        <th>Steam</th>
                                        <th>Batubara</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div id="pagination-monthly" class="mt-3"></div>
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
                        <div class="mb-3 monthly-only">
                            <label for="invoice_listrik" class="form-label">Invoice Listrik</label>
                            <input type="number" id="invoice_listrik" class="form-control" step="0.01"
                                min="0">
                        </div>
                        <div class="mb-3">
                            <label for="steam" class="form-label">Steam</label>
                            <input type="number" id="steam" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="batubara" class="form-label">Batubara</label>
                            <input type="number" id="batubara" class="form-control" step="0.01" min="0">
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
            let currentTab = 'weekly';
            const $weeklyTab = $('#weekly-tab');
            const $monthlyTab = $('#monthly-tab');
            const $filterRangeContainer = $('#filterRangeContainer');

            function updateFilterUI(tab) {
                if (tab === 'weekly') {
                    $filterRangeContainer.html(`
                        <label class="form-label">Rentang Tanggal</label>
                        <div class="input-group">
                            <input type="date" id="start_date" class="form-control" placeholder="Mulai">
                            <input type="date" id="end_date" class="form-control" placeholder="Sampai">
                        </div>
                    `);
                } else if (tab === 'monthly') {
                    $filterRangeContainer.html(`
                        <label class="form-label">Bulan</label>
                        <input type="month" id="filterMonth" class="form-control">
                    `);
                }

                $('#filterForm')[0].reset();
            }

            // Default awal
            updateFilterUI('weekly');
            loadData('weekly');

            // Saat tab berubah → update state & UI
            $weeklyTab.on('shown.bs.tab', function() {
                currentTab = 'weekly';
                updateFilterUI('weekly');
                loadData('weekly');
                $('#accounting').addClass('d-none').text('');
            });

            $monthlyTab.on('shown.bs.tab', function() {
                currentTab = 'monthly';
                updateFilterUI('monthly');
                loadData('monthly');
                $('#accounting').removeClass('d-none').text('Accounting');
            });

            $('#btnFilter').on('click', function() {
                let params = {
                    periode_tipe: currentTab
                };

                if (currentTab === 'weekly') {
                    const start = $('#start_date').val();
                    const end = $('#end_date').val();
                    if (start) params.start_date = start;
                    if (end) params.end_date = end;
                } else if (currentTab === 'monthly') {
                    const month = $('#filterMonth').val();
                    if (month) params.month = month;
                }

                loadData(currentTab, params);
            });

            // Tombol Reset → pakai state currentTab
            $('#btnReset').on('click', function() {
                $('#filterForm')[0].reset();
                loadData(currentTab); // tanpa params
            });

            // Fungsi loadData (update agar terima extra params)
            function loadData(periode = 'weekly', extraParams = {}, page = 1) {
                const params = {
                    periode_tipe: periode,
                    page: page,
                    per_page: 10, // bisa diubah sesuai kebutuhan
                    ...extraParams
                };

                $.get("{{ route('kpi.get-data') }}", params, function(response) {
                    const tableBody = periode === 'weekly' ?
                        $('#tableWeekly tbody') :
                        $('#tableMonthly tbody');

                    const paginationContainer = periode === 'weekly' ?
                        $('#pagination-weekly') :
                        $('#pagination-monthly');

                    tableBody.empty();

                    if (response.data.length === 0) {
                        tableBody.append(`
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="mdi mdi-database-off mdi-36px mb-2"></i>
                                        <span class="fw-semibold">Tidak ada data ditemukan</span>
                                    </div>
                                </td>
                            </tr>
                        `);
                        paginationContainer.empty();
                        return;
                    }

                    $.each(response.data, function(i, item) {
                        const no = (response.current_page - 1) * response.per_page + i + 1;
                        const finishGoods = formatNumber(item.finish_goods || 0);
                        const kecapMatang = formatNumber(item.kecap_matang || 0);
                        const invoiceListrik = formatNumber(item.invoice_listrik || '-');
                        const steam = formatNumber(item.steam || '-');
                        const batubara = formatNumber(item.batubara || '-');

                        let row = `<tr><td>${no}</td>`;

                        if (periode === 'weekly') {
                            row += `
                                <td>${item.start_date || '-'}</td>
                                <td>${item.end_date || '-'}</td>
                                <td>${finishGoods}</td>
                                <td>${kecapMatang}</td>
                                <td>${steam}</td>
                                <td>${batubara}</td>
                            `;
                        } else {
                            row += `
                                <td>${item.month || '-'}</td>
                                <td>${finishGoods}</td>
                                <td>${kecapMatang}</td>
                                <td>${invoiceListrik}</td>
                                <td>${steam}</td>
                                <td>${batubara}</td>
                            `;
                        }

                        row += `
                            <td>
                                <button class="btn btn-sm btn-info me-1 btnEdit" data-id="${item.id}">
                                    <i class="mdi mdi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger btnDelete" data-id="${item.id}">
                                    <i class="mdi mdi-delete"></i> Hapus
                                </button>
                            </td></tr>`;

                        tableBody.append(row);
                    });

                    // Render pagination sederhana
                    renderPagination(response, periode);
                });
            }

            // Load awal
            loadData('weekly');

            const formatNumber = (num) => {
                const val = parseFloat(num);
                if (isNaN(val)) return '-';
                return val % 1 === 0 ? val.toFixed(0) : parseFloat(val.toString()).toString();
            };

            function renderPagination(data, periode) {
                const container = periode === 'weekly' ? $('#pagination-weekly') : $('#pagination-monthly');
                container.empty();

                if (data.last_page <= 1) return;

                let html = '<nav><ul class="pagination justify-content-center">';

                // Previous
                html += `<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${data.current_page - 1}">Previous</a>
                </li>`;

                // Nomor halaman (tampilkan 5 sekitar current)
                const startPage = Math.max(1, data.current_page - 2);
                const endPage = Math.min(data.last_page, data.current_page + 2);

                for (let i = startPage; i <= endPage; i++) {
                    html += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
                }

                // Next
                html += `<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${data.current_page + 1}">Next</a>
                </li>`;

                html += '</ul></nav>';
                container.html(html);
            }

            // Event klik pagination
            $(document).on('click', '.pagination a[data-page]', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page) {
                    const periode = $('.nav-link.active').attr('id')?.replace('-tab', '') || 'weekly';
                    loadData(periode, {}, page); // reload dengan page baru
                }
            });

            $('#weekly-tab').on('click', () => loadData('weekly'));
            $('#monthly-tab').on('click', () => loadData('monthly'));

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
                    invoice_listrik: $('#invoice_listrik').val(),
                    steam: $('#steam').val(),
                    batubara: $('#batubara').val()
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
            const periodeTipe = $('#periodeTipe');
            const groupWeeklyEdit = $('#groupWeeklyEdit');
            const groupMonthlyEdit = $('#groupMonthlyEdit');
            const monthlyOnlyFields = $('.monthly-only');
            const invoiceListrik = $('#invoice_listrik');

            // Fungsi untuk update tampilan & required berdasarkan tipe periode
            function updatePeriodeFields() {
                const tipe = periodeTipe.val();

                // Reset dulu
                groupWeeklyEdit.addClass('d-none');
                groupMonthlyEdit.addClass('d-none');
                monthlyOnlyFields.addClass('d-none');
                invoiceListrik.prop('required', false);

                if (tipe === 'weekly') {
                    groupWeeklyEdit.removeClass('d-none');
                } else if (tipe === 'monthly') {
                    groupMonthlyEdit.removeClass('d-none');
                    monthlyOnlyFields.removeClass('d-none');
                    invoiceListrik.prop('required', true); // wajib hanya di monthly
                }
            }

            // Event change periode tipe
            periodeTipe.on('change', updatePeriodeFields);

            $(document).on('click', '.btnEdit', function() {
                const id = $(this).data('id');
                $.get("{{ url('kpi/show') }}/" + id, function(res) {
                    if (res.success) {
                        const data = res.data;

                        $('#modalKpiLabel').text('Edit Data Kpi');
                        $('#kpiId').val(data.id);

                        $('#periodeTipe').val(data.periode_tipe).trigger('change');

                        if (data.periode_tipe === 'weekly') {
                            $('#editStartDate').val(data.start_date);
                            $('#editEndDate').val(data.end_date);
                        } else if (data.periode_tipe === 'monthly') {
                            $('#editMonth').val(data.month);
                        }

                        // Isi nilai lain
                        $('#finish_goods').val(formatNumber(data.finish_goods));
                        $('#kecap_matang').val(formatNumber(data.kecap_matang));
                        $('#invoice_listrik').val(formatNumber(data.invoice_listrik || 0));
                        $('#steam').val(formatNumber(data.steam || 0));
                        $('#batubara').val(formatNumber(data.batubara || 0));

                        $('#modalKpi').modal('show');

                        // Update tampilan field setelah data diisi
                        updatePeriodeFields();
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
