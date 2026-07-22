@extends('layouts.app')

@section('title', 'Master Jenis DT')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Master Jenis DT</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">EPR</a></li>
                            <li class="breadcrumb-item active">Master Jenis DT</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow border-0">
                    <div class="card-header border-0 align-items-center d-flex justify-content-between p-3" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                        <h5 class="card-title mb-0 text-white"><i class="ri-book-cog-line me-2 text-warning"></i>Daftar Jenis DT</h5>
                        @if(in_array(Auth::user()->jabatan, ['admin', 'dept_head', 'supervisor', 'foreman']))
                        <button class="btn btn-warning btn-sm" onclick="openAddModal()">
                            <i class="ri-add-line me-1 align-bottom"></i> Tambah Baru
                        </button>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle" id="tableJenisDt" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80" class="text-center">No</th>
                                        <th>Nama Jenis DT</th>
                                        <th width="150" class="text-center">Status</th>
                                        <th>Dibuat Oleh</th>
                                        <th width="150" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create / Edit -->
<div class="modal fade" id="modalJenisDt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom p-3">
                <h5 class="modal-title fw-bold" id="modalTitle">Tambah Jenis DT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formJenisDt">
                @csrf
                <input type="hidden" id="item-id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Jenis DT <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="item-name" placeholder="cth: Electrical (coding), Finger..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status Aktif</label>
                        <select class="form-select" id="item-aktif">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let items = [];

    function loadItems() {
        $.ajax({
            url: "{{ route('epr.master.jenis-dt.json') }}",
            method: 'GET',
            success: function(res) {
                items = res;
                renderTable();
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat data', 'error');
            }
        });
    }

    function renderTable() {
        const tbody = $('#tableBody');
        tbody.empty();
        if (!items.length) {
            tbody.append('<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data jenis DT</td></tr>');
            return;
        }

        items.forEach((item, i) => {
            const statusBadge = item.aktif 
                ? '<span class="badge bg-success-subtle text-success py-1 px-2">Aktif</span>'
                : '<span class="badge bg-danger-subtle text-danger py-1 px-2">Tidak Aktif</span>';

            const creator = item.creator ? item.creator.username : '—';

            let actionHtml = `
                <div class="d-flex gap-1 justify-content-center">
                    <button class="btn btn-sm btn-outline-warning" onclick="openEditModal(${item.id})" title="Edit">
                        <i class="ri-edit-line"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteItem(${item.id})" title="Hapus">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            `;

            tbody.append(`
                <tr>
                    <td class="text-center font-monospace">${i + 1}</td>
                    <td class="fw-semibold">${esc(item.name)}</td>
                    <td class="text-center">${statusBadge}</td>
                    <td>${esc(creator)}</td>
                    <td>${actionHtml}</td>
                </tr>
            `);
        });
    }

    window.openAddModal = function() {
        $('#formJenisDt')[0].reset();
        $('#item-id').val('');
        $('#modalTitle').text('Tambah Jenis DT');
        $('#modalJenisDt').modal('show');
    };

    window.openEditModal = function(id) {
        const item = items.find(x => x.id == id);
        if (!item) return;

        $('#item-id').val(item.id);
        $('#item-name').val(item.name);
        $('#item-aktif').val(item.aktif ? '1' : '0');
        $('#modalTitle').text('Edit Jenis DT');
        $('#modalJenisDt').modal('show');
    };

    $('#formJenisDt').submit(function(e) {
        e.preventDefault();
        const id = $('#item-id').val();
        const name = $('#item-name').val().trim();
        const aktif = $('#item-aktif').val();

        if (!name) return;

        const btn = $('#btnSave');
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{ route('epr.master.jenis-dt.store') }}",
            method: 'POST',
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: JSON.stringify({
                id: id || null,
                name: name,
                aktif: aktif === '1'
            }),
            success: function(res) {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data jenis DT berhasil disimpan', timer: 1500, showConfirmButton: false });
                    $('#modalJenisDt').modal('hide');
                    loadItems();
                } else {
                    Swal.fire('Error', res.message || 'Gagal menyimpan', 'error');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Terjadi kesalahan server';
                Swal.fire('Error', msg, 'error');
            },
            complete: function() {
                btn.prop('disabled', false).text('Simpan');
            }
        });
    });

    window.deleteItem = function(id) {
        Swal.fire({
            title: 'Hapus Jenis DT?',
            text: 'Data yang dihapus tidak bisa dikembalikan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/epr/master/jenis-dt/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'Dihapus!', text: 'Data berhasil dihapus', timer: 1500, showConfirmButton: false });
                            loadItems();
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus data', 'error');
                    }
                });
            }
        });
    };

    function esc(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    loadItems();
});
</script>
@endsection
