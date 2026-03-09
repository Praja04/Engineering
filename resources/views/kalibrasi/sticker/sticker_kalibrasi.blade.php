@extends('layouts.app')

@section('title', 'Sticker Kalibrasi')

@section('styles')
    <style>

    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow-sm rounded-3 mb-4" data-aos="fade-up">
                <div class="card-header">
                    <h4 class="fw-bold">Sticker Kalibrasi</h4>
                    <p class="card-subtitle">Data kalibrasi yang sudah ter approve sertifikatnya</p>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4 align-items-end">

                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label small text-muted">Kode Alat</label>
                            <input type="text" id="kode_alat" class="form-control" placeholder="Cari Kode Alat">
                        </div>

                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label small text-muted">Tanggal Kalibrasi</label>
                            <input type="date" id="tanggal" class="form-control">
                        </div>

                        <div class="col-6 col-lg-3">
                            <button id="btn-filter" class="btn btn-primary w-100">
                                <i class="mdi mdi-filter"></i> Filter
                            </button>
                        </div>

                        <div class="col-6 col-lg-3">
                            <button id="btn-reset" class="btn btn-outline-danger w-100">
                                <i class="mdi mdi-refresh"></i> Reset
                            </button>
                        </div>

                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Kode Alat</th>
                                    <th>Nama Alat</th>
                                    <th>Jenis Kalibrasi</th>
                                    <th>Tanggal Kalibrasi</th>
                                    <th>Dibuat Oleh</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                            </tbody>
                        </table>
                        <div id="pagination-links" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            function loadData(page = 1) {
                $.ajax({
                    url: "{{ route('kalibrasi.sticker.data') }}",
                    type: "GET",
                    data: {
                        page: page,
                        kode_alat: $('#kode_alat').val(),
                        tanggal: $('#tanggal').val()
                    },
                    success: function(response) {

                        let rows = '';
                        let no = (response.current_page - 1) * response.per_page + 1;

                        if (response.data.length > 0) {
                            $.each(response.data, function(i, item) {
                                rows += `
                                    <tr>
                                        <td class="text-center">${no++}</td>
                                        <td>${item.kalibrasi?.alat?.kode_alat ?? '-'}</td>
                                        <td>${item.kalibrasi?.alat?.nama_alat ?? '-'}</td>
                                        <td>${item.kalibrasi?.jenis_kalibrasi.replaceAll('_',' ')}</td>
                                        <td>${item.kalibrasi?.tgl_kalibrasi ?? '-'}</td>
                                        <td>${item.kalibrasi?.user?.username ?? '-'}</td>
                                        <td class="text-center">
                                            <span class="badge badge-soft-success text-uppercase">Approved</span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info btn-download"
                                                data-id="${item.id}">
                                                <i class="mdi mdi-download me-2"></i> Download
                                            </button>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            rows = `
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="mdi mdi-check-circle-outline fs-3 d-block mb-2"></i>
                                        <strong>Tidak ada sticker</strong>
                                        <div class="small">Data kalibrasi belum diapprove</div>
                                    </td>
                                </tr>
                            `;
                        }

                        $('#table-body').html(rows);

                        // pagination
                        $('#pagination-links').html(response.links.map(link => `
                            <button class="btn btn-sm ${link.active ? 'btn-primary' : 'btn-light'}"
                                data-page="${link.url ? new URL(link.url).searchParams.get('page') : ''}"
                                ${link.url ? '' : 'disabled'}>
                                ${link.label.replace('&laquo;', '«').replace('&raquo;', '»')}
                            </button>
                        `).join(''));
                    }
                });
            }

            // load pertama
            loadData();

            // filter click
            $('#btn-filter').click(function() {
                loadData();
            });

            $('#btn-reset').click(function() {
                $('#kode_alat').val('');
                $('#tanggal').val('');

                loadData(1);
            });

            // pagination click
            $(document).on('click', '#pagination-links button', function() {
                let page = $(this).data('page');
                if (page) loadData(page);
            });

            $(document).on('click', '.btn-download', function() {
                let id = $(this).data('id');

                window.open("{{ route('kalibrasi.sticker.download', ':id') }}".replace(':id', id),
                    '_blank');
            });

        })
    </script>
@endsection
