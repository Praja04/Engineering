@extends('layouts.app')

@section('title', 'Manajemen Hak Akses & Permission')

@section('styles')
    <style>
        .perm-group-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .perm-group-box:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .perm-group-header {
            background-color: #f1f5f9;
            padding: 0.5rem 0.85rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .role-card {
            transition: all 0.2s ease-in-out;
            border-radius: 0.65rem;
        }

        .role-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08) !important;
            border-color: #93c5fd !important;
        }

        .access-tabs .nav-link {
            font-weight: 600;
            color: #64748b;
            padding: 0.75rem 1.25rem;
            border: none;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .access-tabs .nav-link:hover {
            color: #1e293b;
        }

        .access-tabs .nav-link.active {
            color: #405189;
            border-bottom: 2px solid #405189;
            background: transparent;
        }

        .perm-slug-tag {
            font-family: Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 0.75rem;
            background: #f1f5f9;
            color: #0f172a;
            padding: 0.2rem 0.45rem;
            border-radius: 0.25rem;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .perm-slug-tag:hover {
            background: #e2e8f0;
            color: #2563eb;
            border-color: #cbd5e1;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .role-select-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            transition: all 0.15s ease;
        }

        .role-select-pill:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .role-select-pill .form-check-input:checked~.form-check-label {
            color: #059669;
            font-weight: 700;
        }

        .perm-item-label {
            cursor: pointer;
            user-select: none;
            font-size: 0.825rem;
            line-height: 1.35;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title Header -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h4 class="mb-1 text-dark fw-bold">
                                <i class="ri-shield-keyhole-line text-primary me-1"></i> Manajemen Hak Akses & Permission
                            </h4>
                            <p class="text-muted mb-0 fs-13">Kelola hak akses granular per fitur sistem, struktur roles, dan
                                delegasi izin ke pengguna.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadAccessControlData(true)">
                                <i class="ri-refresh-line me-1" id="iconRefresh"></i> Refresh
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ri-user-settings-line me-1"></i> Kelola Akun User
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4 Metric Cards (100% Client-Side Rendered) -->
            <div class="row g-3 mb-3">
                <div class="col-xl-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 mb-0">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle rounded-3 fs-20">
                                    <i class="ri-key-2-fill"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-muted fs-12 text-uppercase fw-semibold d-block">Total Permissions</span>
                                <h4 class="mb-0 fw-bold text-dark" id="statTotalPerms">
                                    <span class="spinner-border spinner-border-sm text-muted"></span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 mb-0">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success-subtle text-success rounded-3 fs-20">
                                    <i class="ri-shield-user-fill"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-muted fs-12 text-uppercase fw-semibold d-block">Total Roles</span>
                                <h4 class="mb-0 fw-bold text-dark" id="statTotalRoles">
                                    <span class="spinner-border spinner-border-sm text-muted"></span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 mb-0">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle text-info rounded-3 fs-20">
                                    <i class="ri-folder-shared-fill"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-muted fs-12 text-uppercase fw-semibold d-block">Modul Fitur</span>
                                <h4 class="mb-0 fw-bold text-dark" id="statTotalGroups">
                                    <span class="spinner-border spinner-border-sm text-muted"></span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 mb-0">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning-subtle text-warning rounded-3 fs-20">
                                    <i class="ri-team-fill"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-muted fs-12 text-uppercase fw-semibold d-block">User Terdaftar</span>
                                <h4 class="mb-0 fw-bold text-dark" id="statTotalUsers">
                                    <span class="spinner-border spinner-border-sm text-muted"></span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card with Tab Navigations -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom p-0">
                            <ul class="nav nav-tabs access-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#tabPermissions" role="tab">
                                        <i class="ri-key-2-line me-1"></i> Daftar Permission Fitur
                                        <span class="badge bg-primary-subtle text-primary ms-1" id="tabPermCount">-</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tabRoles" role="tab">
                                        <i class="ri-shield-line me-1"></i> Daftar Role
                                        <span class="badge bg-success-subtle text-success ms-1" id="tabRoleCount">-</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tabUsers" role="tab">
                                        <i class="ri-user-shared-line me-1"></i> Hak Akses Pengguna
                                        <span class="badge bg-warning-subtle text-warning ms-1" id="tabUserCount">-</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-4">
                            <div class="tab-content">

                                <!-- ========================================== -->
                                <!-- TAB 1: PERMISSIONS FITUR                   -->
                                <!-- ========================================== -->
                                <div class="tab-pane active" id="tabPermissions" role="tabpanel">
                                    <div
                                        class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <div class="input-group input-group-sm" style="width: 250px;">
                                                <span class="input-group-text bg-light border-end-0"><i
                                                        class="ri-search-line text-muted"></i></span>
                                                <input type="text" id="searchPermission"
                                                    class="form-control border-start-0 ps-0"
                                                    placeholder="Cari permission / slug...">
                                            </div>
                                            <select id="filterGroupPermission" class="form-select form-select-sm"
                                                style="width: 200px;">
                                                <option value="">-- Semua Modul / Group --</option>
                                            </select>
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                id="btnResetFilterPerm" title="Reset Filter">
                                                <i class="ri-restart-line"></i>
                                            </button>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm fw-semibold shadow-sm"
                                            onclick="openCreatePermissionModal()">
                                            <i class="ri-add-line align-middle me-1"></i> Tambah Permission Baru
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle table-sm mb-0" id="tablePermissions">
                                            <thead class="table-light fs-12 text-muted text-uppercase">
                                                <tr>
                                                    <th style="width: 50px;" class="text-center">No</th>
                                                    <th style="width: 180px;">Modul / Group</th>
                                                    <th style="width: 280px;">Key Permission</th>
                                                    <th>Deskripsi</th>
                                                    <th class="text-center" style="width: 110px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablePermissionsBody">
                                                <tr id="permLoadingRow">
                                                    <td colspan="5" class="text-center py-5 text-muted">
                                                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                                        Memuat data permission...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination Footer: Permissions -->
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-3 border-top"
                                        id="permPaginationWrapper">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted fs-12">Tampilkan:</span>
                                            <select id="permPerPage" class="form-select form-select-sm"
                                                style="width: 75px;">
                                                <option value="10" selected>10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="all">Semua</option>
                                            </select>
                                            <span class="text-muted fs-12 ms-2" id="permPaginationInfo">Memuat
                                                data...</span>
                                        </div>
                                        <nav aria-label="Navigasi Halaman Permission">
                                            <ul class="pagination pagination-sm mb-0 shadow-none" id="permPaginationNav">
                                                <!-- Dynamic via JS -->
                                            </ul>
                                        </nav>
                                    </div>
                                </div>

                                <!-- ========================================== -->
                                <!-- TAB 2: ROLES MANAGEMENT                     -->
                                <!-- ========================================== -->
                                <div class="tab-pane" id="tabRoles" role="tabpanel">
                                    <div
                                        class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <div class="input-group input-group-sm" style="width: 250px;">
                                                <span class="input-group-text bg-light border-end-0"><i
                                                        class="ri-search-line text-muted"></i></span>
                                                <input type="text" id="searchRole" class="form-control border-start-0 ps-0"
                                                    placeholder="Cari role...">
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-success btn-sm fw-semibold shadow-sm"
                                            onclick="openCreateRoleModal()">
                                            <i class="ri-add-line align-middle me-1"></i> Tambah Role Baru
                                        </button>
                                    </div>

                                    <div class="row g-3" id="rolesContainer">
                                        <div class="col-12 text-center py-5 text-muted" id="rolesLoading">
                                            <div class="spinner-border spinner-border-sm text-success me-2"></div>
                                            Memuat daftar role...
                                        </div>
                                    </div>
                                </div>

                                <!-- ========================================== -->
                                <!-- TAB 3: USER PERMISSIONS (HAK AKSES USER)   -->
                                <!-- ========================================== -->
                                <div class="tab-pane" id="tabUsers" role="tabpanel">
                                    <div
                                        class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <div class="input-group input-group-sm" style="width: 280px;">
                                                <span class="input-group-text bg-light border-end-0"><i
                                                        class="ri-search-line text-muted"></i></span>
                                                <input type="text" id="searchUserAccess"
                                                    class="form-control border-start-0 ps-0"
                                                    placeholder="Cari user (nama, jabatan, dept)...">
                                            </div>
                                            <select id="filterRoleUser" class="form-select form-select-sm"
                                                style="width: 180px;">
                                                <option value="">-- Semua Role --</option>
                                            </select>
                                        </div>
                                        <span class="text-muted fs-12">
                                            <i class="ri-information-line me-1 text-info"></i>Klik <strong>Atur
                                                Izin</strong> untuk menugaskan Role atau Direct Permission spesifik ke akun.
                                        </span>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle table-sm mb-0" id="tableUserAccess">
                                            <thead class="table-light fs-12 text-muted text-uppercase">
                                                <tr>
                                                    <th style="width: 50px;" class="text-center">No</th>
                                                    <th>Nama / Username</th>
                                                    <th>Jabatan & Departemen</th>
                                                    <th>Assigned Roles</th>
                                                    <th>Direct Permissions</th>
                                                    <th class="text-center" style="width: 140px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableUserAccessBody">
                                                <tr id="usersLoadingRow">
                                                    <td colspan="6" class="text-center py-5 text-muted">
                                                        <div class="spinner-border spinner-border-sm text-warning me-2"></div>
                                                        Memuat data pengguna...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination Footer: User Access -->
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-3 border-top"
                                        id="userPaginationWrapper">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted fs-12">Tampilkan:</span>
                                            <select id="userPerPage" class="form-select form-select-sm"
                                                style="width: 75px;">
                                                <option value="10" selected>10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="all">Semua</option>
                                            </select>
                                            <span class="text-muted fs-12 ms-2" id="userPaginationInfo">Memuat
                                                data...</span>
                                        </div>
                                        <nav aria-label="Navigasi Halaman Pengguna">
                                            <ul class="pagination pagination-sm mb-0 shadow-none" id="userPaginationNav">
                                                <!-- Dynamic via JS -->
                                            </ul>
                                        </nav>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: TAMBAH / EDIT PERMISSION                          -->
    <!-- ======================================================== -->
    <div class="modal fade" id="modalPermission" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formPermission" onsubmit="submitPermissionForm(event)" class="modal-content border-0 shadow-lg">
                @csrf
                <input type="hidden" id="perm_id">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title text-white fs-15 fw-bold" id="modalPermissionTitle">
                        <i class="ri-key-2-line me-1"></i> Tambah Permission Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Modul / Group Fitur <span class="text-danger">*</span></label>
                        <select id="perm_group_select" class="form-select" onchange="handleGroupSelectChange(this.value)" required>
                            <option value="" disabled selected>-- Pilih Modul / Group --</option>
                            <option value="__new__" class="fw-bold text-primary">+ Buat Grup Baru...</option>
                        </select>

                        <!-- Input Grup Baru (Tampil saat opsi + Buat Grup Baru dipilih) -->
                        <div id="wrapperNewGroup" class="mt-2" style="display: none;">
                            <div class="input-group">
                                <span class="input-group-text bg-primary-subtle text-primary border-primary-subtle fs-12">
                                    <i class="ri-folder-add-line me-1"></i> Nama Grup Baru
                                </span>
                                <input type="text" id="perm_group_new" class="form-control border-primary-subtle"
                                    placeholder="Contoh: Genset, Water Treatment, dsb.">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cancelNewGroup()" title="Batal buat grup baru">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                            <small class="text-muted fs-11">Masukkan nama grup baru yang ingin ditambahkan.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Key Permission <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fs-12"><i
                                    class="ri-key-2-line"></i></span>
                            <input type="text" name="name" id="perm_name" class="form-control font-monospace"
                                placeholder="Contoh: project_create" required oninput="sanitizePermKey(this)">
                        </div>
                        <small class="text-muted fs-11">Format key: <code>modul_aksi</code> (gunakan huruf kecil &
                            underscore <code>_</code>).</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Deskripsi / Keterangan</label>
                        <textarea name="description" id="perm_desc" class="form-control" rows="2"
                            placeholder="Keterangan singkat fungsi permission ini (contoh: Izin membuat project baru)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnSubmitPermission">
                        <i class="ri-save-line me-1"></i> Simpan Permission
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: TAMBAH / EDIT ROLE & CHECKLIST PERMISSIONS        -->
    <!-- ======================================================== -->
    <div class="modal fade" id="modalRole" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <form id="formRole" onsubmit="submitRoleForm(event)" class="modal-content border-0 shadow-lg">
                @csrf
                <input type="hidden" id="role_id">
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title text-white fs-15 fw-bold" id="modalRoleTitle">
                        <i class="ri-shield-line me-1"></i> Tambah Role Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Tampilan Role <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="display_name" id="role_display_name" class="form-control"
                                placeholder="Contoh: Operator Utility" required
                                onkeyup="autoGenerateRoleSlug(this.value)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Key / Slug Role <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="role_name" class="form-control font-monospace"
                                placeholder="Contoh: operator_utility" required>
                            <small class="text-muted fs-11">Huruf kecil, tanpa spasi (gunakan underscore
                                <code>_</code>).</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi Role</label>
                            <input type="text" name="description" id="role_desc" class="form-control"
                                placeholder="Deskripsi singkat tanggung jawab peran ini...">
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Checklist Permissions per Group -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="fw-bold text-dark mb-0"><i class="ri-checkbox-multiple-line me-1"></i>
                                Tetapkan Permissions untuk Role ini:</h6>
                            <span class="text-muted fs-11">Dipilih: <strong id="roleSelectedCount"
                                    class="text-success">0</strong> permission</span>
                        </div>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-outline-primary btn-sm py-1"
                                onclick="toggleAllRolePermissions(true)">Pilih Semua</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1"
                                onclick="toggleAllRolePermissions(false)">Kosongkan</button>
                        </div>
                    </div>

                    <!-- Filter search in modal -->
                    <div class="mb-3">
                        <input type="text" id="searchPermInRoleModal" class="form-control form-control-sm"
                            placeholder="Saring permission di daftar bawah...">
                    </div>

                    <div class="p-2 border rounded bg-light" id="rolePermModalContainer"
                        style="max-height: 280px; overflow-y: auto;">
                        <!-- Dynamic via Client-Side JS -->
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold" id="btnSubmitRole">
                        <i class="ri-save-line me-1"></i> Simpan Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: ATUR HAK AKSES PENGGUNA (ROLES & DIRECT PERMISSIONS) -->
    <!-- ======================================================== -->
    <div class="modal fade" id="modalUserAccess" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <form id="formUserAccess" onsubmit="submitUserAccessForm(event)" class="modal-content border-0 shadow-lg">
                @csrf
                <input type="hidden" id="access_user_id">
                <div class="modal-header bg-warning text-dark py-3">
                    <h5 class="modal-title fs-15 fw-bold" id="modalUserAccessTitle">
                        <i class="ri-user-settings-line me-1"></i> Atur Hak Akses Pengguna
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-soft-info border-0 d-flex align-items-center mb-3 py-2 px-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title rounded-circle bg-info text-white fw-bold"
                                id="access_avatar_letter">U</span>
                        </div>
                        <div>
                            User: <strong id="access_user_name" class="text-dark">-</strong>
                            <span class="badge bg-light text-dark border ms-2" id="access_user_jabatan">-</span>
                        </div>
                    </div>

                    <!-- 1. Section Roles -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-1"><i class="ri-shield-user-fill text-success me-1"></i>1.
                            Pilih Role(s) untuk Pengguna:</h6>
                        <p class="text-muted fs-12 mb-2">Pengguna akan mewarisi semua izin yang terikat pada role yang
                            dipilih.</p>
                        <div class="row g-2" id="userRolesModalContainer">
                            <!-- Dynamic via Client-Side JS -->
                        </div>
                    </div>

                    <!-- 2. Section Direct Permissions -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold text-dark mb-0"><i class="ri-key-fill text-warning me-1"></i>2. Direct
                                Permissions Tambahan (Opsional):</h6>
                            <input type="text" id="searchPermInUserModal" class="form-control form-control-sm"
                                placeholder="Cari izin khusus..." style="width: 220px;">
                        </div>
                        <p class="text-muted fs-12 mb-2">Berikan izin spesifik langsung ke akun ini di luar izin bawaan
                            role.</p>

                        <div class="p-2 border rounded bg-light" id="userPermsModalContainer"
                            style="max-height: 240px; overflow-y: auto;">
                            <!-- Dynamic via Client-Side JS -->
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm px-4 fw-semibold" id="btnSubmitUserAccess">
                        <i class="ri-save-line me-1"></i> Simpan Hak Akses
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // ── Global Helper: Copy slug to clipboard ──
        function copySlugToClipboard(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    toastr.success(`Slug "${text}" disalin ke clipboard.`);
                });
            } else {
                const input = document.createElement('input');
                input.value = text;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                toastr.success(`Slug "${text}" disalin.`);
            }
        }

        // ── Permission Key Sanitizer ──
        function sanitizePermKey(el) {
            el.value = el.value.toLowerCase().replace(/[^a-z0-9_]/g, '_');
        }

        // ── Group Select & Create Helper ──
        function handleGroupSelectChange(val) {
            if (val === '__new__') {
                $('#wrapperNewGroup').slideDown(150);
                $('#perm_group_new').focus().prop('required', true);
            } else {
                $('#wrapperNewGroup').slideUp(150);
                $('#perm_group_new').val('').prop('required', false);
            }
        }

        function cancelNewGroup() {
            $('#wrapperNewGroup').slideUp(150);
            $('#perm_group_new').val('').prop('required', false);
            $('#perm_group_select').val('');
        }

        function autoGenerateRoleSlug(val) {
            if (!$('#role_id').val()) {
                const slug = val.toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s]/g, '')
                    .replace(/\s+/g, '_');
                $('#role_name').val(slug);
            }
        }

        function slugify(text) {
            return (text || '').toString().toLowerCase().trim()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.toString().replace(/[&<>"']/g, m => map[m]);
        }

        function escapeAttr(text) {
            if (text === null || text === undefined) return '';
            return text.toString().replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }

        const groupColorMap = {
            'project utility': 'cyan',
            'compressor': 'warning',
            'wwtp': 'info',
            'boiler': 'danger',
            'maintenance': 'success',
            'kalibrasi': 'purple',
            'master data': 'secondary',
            'user management': 'dark'
        };

        function getGroupColor(group) {
            return groupColorMap[(group || '').toLowerCase()] || 'primary';
        }

        // ── Class TablePaginator untuk Pagination Tabular Otomatis ──
        class TablePaginator {
            constructor(options) {
                this.rowSelector = options.rowSelector;
                this.perPageSelector = options.perPageSelector;
                this.infoSelector = options.infoSelector;
                this.navSelector = options.navSelector;
                this.filterFn = options.filterFn || (() => true);
                this.updateNumber = options.updateNumber ?? true;
                this.emptyMessage = options.emptyMessage || 'Tidak ada data ditemukan';

                this.currentPage = 1;
                this.perPage = parseInt($(this.perPageSelector).val()) || 10;

                $(this.perPageSelector).on('change', () => {
                    const val = $(this.perPageSelector).val();
                    this.perPage = val === 'all' ? 999999 : parseInt(val);
                    this.currentPage = 1;
                    this.refresh();
                });
            }

            refresh() {
                const rows = $(this.rowSelector);
                const $tbody = rows.parent();

                // Bersihkan baris pesan kosong lama
                $tbody.find('.paginator-empty-row').remove();

                if (rows.length === 0) {
                    $(this.infoSelector).html('<span class="text-muted">' + this.emptyMessage + '</span>');
                    this.renderNav(0);
                    return;
                }

                const matchingRows = [];
                rows.each((_, el) => {
                    if (this.filterFn(el)) {
                        matchingRows.push(el);
                    } else {
                        $(el).hide();
                    }
                });

                const totalItems = matchingRows.length;

                if (totalItems === 0) {
                    const colCount = rows.first().find('td').length || 5;
                    $tbody.append(`
                        <tr class="paginator-empty-row">
                            <td colspan="${colCount}" class="text-center py-4 text-muted">
                                <i class="ri-search-eye-line fs-20 d-block mb-1 text-secondary"></i>
                                ${this.emptyMessage}
                            </td>
                        </tr>
                    `);
                    $(this.infoSelector).html('<span class="text-muted">' + this.emptyMessage + '</span>');
                    this.renderNav(0);
                    return;
                }

                const totalPages = Math.max(1, Math.ceil(totalItems / this.perPage));
                if (this.currentPage > totalPages) {
                    this.currentPage = totalPages;
                }

                const startIndex = (this.currentPage - 1) * this.perPage;
                const endIndex = Math.min(startIndex + this.perPage, totalItems);

                matchingRows.forEach((el, index) => {
                    if (index >= startIndex && index < endIndex) {
                        $(el).show();
                        if (this.updateNumber) {
                            $(el).find('td:first').text(index + 1);
                        }
                    } else {
                        $(el).hide();
                    }
                });

                // Update Info
                $(this.infoSelector).html(
                    `Menampilkan <strong>${startIndex + 1}</strong> - <strong>${endIndex}</strong> dari <strong>${totalItems}</strong> data`
                );

                // Render Nav
                this.renderNav(totalPages);
            }

            renderNav(totalPages) {
                const $nav = $(this.navSelector);
                $nav.empty();

                if (totalPages <= 1) {
                    return;
                }

                // Prev
                const prevDisabled = this.currentPage === 1 ? 'disabled' : '';
                $nav.append(
                    `<li class="page-item ${prevDisabled}"><a class="page-link" href="javascript:void(0)" data-page="${this.currentPage - 1}" title="Sebelumnya"><i class="ri-arrow-left-s-line"></i></a></li>`
                );

                let startPage = Math.max(1, this.currentPage - 2);
                let endPage = Math.min(totalPages, this.currentPage + 2);

                if (startPage > 1) {
                    $nav.append(
                        `<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="1">1</a></li>`
                    );
                    if (startPage > 2) {
                        $nav.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    const active = i === this.currentPage ? 'active' : '';
                    $nav.append(
                        `<li class="page-item ${active}"><a class="page-link" href="javascript:void(0)" data-page="${i}">${i}</a></li>`
                    );
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        $nav.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                    }
                    $nav.append(
                        `<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="${totalPages}">${totalPages}</a></li>`
                    );
                }

                // Next
                const nextDisabled = this.currentPage === totalPages ? 'disabled' : '';
                $nav.append(
                    `<li class="page-item ${nextDisabled}"><a class="page-link" href="javascript:void(0)" data-page="${this.currentPage + 1}" title="Selanjutnya"><i class="ri-arrow-right-s-line"></i></a></li>`
                );

                $nav.find('a.page-link').on('click', (e) => {
                    const p = parseInt($(e.currentTarget).data('page'));
                    if (p && p !== this.currentPage && p >= 1 && p <= totalPages) {
                        this.currentPage = p;
                        this.refresh();
                    }
                });
            }
        }

        // ========================================================
        // CLIENT-SIDE STATE & RENDERING ENGINE
        // ========================================================
        let accessData = {
            permissions: [],
            roles: [],
            users: [],
            groups: [],
            stats: {}
        };

        let permPaginator = null;
        let userPaginator = null;

        function loadAccessControlData(showToast = false) {
            $('#iconRefresh').addClass('ri-spin');

            $.ajax({
                url: "{{ route('access-control.data') }}",
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $('#iconRefresh').removeClass('ri-spin');
                    accessData = res;

                    // 1. Render Metrics & Tabs Count
                    renderStats(res.stats);

                    // 2. Render Group & Role Filter Options
                    renderGroupOptions(res.groups);
                    renderRoleFilterOptions(res.roles);

                    // 3. Render Tables & Cards
                    renderPermissionsTable(res.permissions);
                    renderRolesCards(res.roles);
                    renderUsersTable(res.users);

                    // 4. Render Modals Dynamic Checkboxes
                    renderRoleModalCheckboxes(res.permissions);
                    renderUserModalRoles(res.roles);
                    renderUserModalPermissions(res.permissions);

                    // 5. Refresh Client Paginators
                    if (permPaginator) permPaginator.refresh();
                    if (userPaginator) userPaginator.refresh();

                    if (showToast) {
                        toastr.success('Data hak akses berhasil dimuat ulang!');
                    }
                },
                error: function(err) {
                    $('#iconRefresh').removeClass('ri-spin');
                    const msg = err.responseJSON?.message || 'Gagal memuat data dari server.';
                    Swal.fire('Error', msg, 'error');
                }
            });
        }

        // ── Render 4 Metric Cards & Tab Badges ──
        function renderStats(stats) {
            const s = stats || {};
            $('#statTotalPerms').text(s.total_permissions ?? accessData.permissions.length);
            $('#statTotalRoles').text(s.total_roles ?? accessData.roles.length);
            $('#statTotalGroups').text(s.total_groups ?? accessData.groups.length);
            $('#statTotalUsers').text(s.total_users ?? accessData.users.length);

            $('#tabPermCount').text(s.total_permissions ?? accessData.permissions.length);
            $('#tabRoleCount').text(s.total_roles ?? accessData.roles.length);
            $('#tabUserCount').text(s.total_users ?? accessData.users.length);
        }

        // ── Render Dropdown Opsi Groups ──
        function renderGroupOptions(groups) {
            const currentFilter = $('#filterGroupPermission').val();
            const $filter = $('#filterGroupPermission');
            $filter.empty().append('<option value="">-- Semua Modul / Group --</option>');

            const $modalSelect = $('#perm_group_select');
            $modalSelect.find('option:not([value=""]):not([value="__new__"])').remove();

            if (groups && groups.length) {
                groups.forEach(grp => {
                    $filter.append(`<option value="${escapeHtml(grp)}">${escapeHtml(grp)}</option>`);
                    $modalSelect.find('option[value="__new__"]').before(
                        `<option value="${escapeHtml(grp)}">${escapeHtml(grp)}</option>`
                    );
                });
            }

            if (currentFilter) {
                $filter.val(currentFilter);
            }
        }

        // ── Render Role Filter for User Tab ──
        function renderRoleFilterOptions(roles) {
            const currentRoleFilter = $('#filterRoleUser').val();
            const $select = $('#filterRoleUser');
            $select.empty().append('<option value="">-- Semua Role --</option>');

            if (roles && roles.length) {
                roles.forEach(r => {
                    $select.append(`<option value="${escapeHtml(r.name)}">${escapeHtml(r.display_name || r.name)}</option>`);
                });
            }

            if (currentRoleFilter) {
                $select.val(currentRoleFilter);
            }
        }

        // ── Render Table Permissions (Tab 1) ──
        function renderPermissionsTable(permissions) {
            const $tbody = $('#tablePermissionsBody');
            $tbody.empty();

            if (!permissions || permissions.length === 0) {
                $tbody.html(`
                    <tr class="empty-state-row">
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="ri-inbox-line fs-28 d-block mb-1 text-secondary"></i>
                            Belum ada permission terdaftar.
                        </td>
                    </tr>
                `);
                return;
            }

            permissions.forEach((perm, index) => {
                const color = getGroupColor(perm.group);
                const searchStr = `${perm.name} ${perm.group} ${perm.description || ''}`.toLowerCase();
                const rowHtml = `
                    <tr class="permission-row" id="perm_row_${perm.id}"
                        data-group="${(perm.group || '').toLowerCase()}"
                        data-search="${searchStr}">
                        <td class="text-center text-muted fs-12">${index + 1}</td>
                        <td>
                            <span class="badge bg-${color}-subtle text-${color} border border-${color}-subtle px-2 py-1 fs-11">
                                <i class="ri-folder-2-line me-1"></i>${escapeHtml(perm.group)}
                            </span>
                        </td>
                        <td>
                            <span class="perm-slug-tag" title="Klik untuk menyalin" onclick="copySlugToClipboard('${escapeAttr(perm.name)}')">
                                <i class="ri-file-copy-line me-1"></i>${escapeHtml(perm.name)}
                            </span>
                        </td>
                        <td class="text-muted fs-12">${escapeHtml(perm.description || '-')}</td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-soft-primary btn-sm p-1 px-2" title="Edit Permission"
                                    onclick='editPermissionById(${perm.id})'>
                                    <i class="ri-edit-2-line"></i>
                                </button>
                                <button type="button" class="btn btn-soft-danger btn-sm p-1 px-2" title="Hapus Permission"
                                    onclick="deletePermission(${perm.id}, '${escapeAttr(perm.name)}')">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                $tbody.append(rowHtml);
            });
        }

        // ── Render Cards Roles (Tab 2) ──
        function renderRolesCards(roles) {
            const $container = $('#rolesContainer');
            $container.empty();

            if (!roles || roles.length === 0) {
                $container.html(`
                    <div class="col-12 text-center py-5 text-muted empty-state-card">
                        <i class="ri-shield-line fs-28 d-block mb-1 text-secondary"></i>
                        Belum ada role terdaftar.
                    </div>
                `);
                return;
            }

            roles.forEach(role => {
                const perms = role.permissions || [];
                const permsPreview = perms.slice(0, 4).map(p =>
                    `<span class="badge bg-light text-secondary border fs-10 font-monospace">${escapeHtml(p.name)}</span>`
                ).join(' ');
                const extraPerms = perms.length > 4 ?
                    `<span class="badge bg-light text-muted border fs-10">+${perms.length - 4} lainnya</span>` : '';

                const deleteBtn = role.name !== 'admin' ? `
                    <button type="button" class="btn btn-soft-danger btn-sm px-2" title="Hapus Role"
                        onclick="deleteRole(${role.id}, '${escapeAttr(role.name)}')">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                ` : '';

                const cardHtml = `
                    <div class="col-xl-4 col-md-6 role-card-col" id="role_card_${role.id}"
                        data-search="${(role.name + ' ' + (role.display_name || '') + ' ' + (role.description || '')).toLowerCase()}">
                        <div class="card border shadow-sm h-100 mb-0 role-card">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-success-subtle text-success rounded-3 fs-18">
                                                    <i class="ri-shield-check-line"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="card-title mb-0 fw-bold text-dark fs-14">
                                                    ${escapeHtml(role.display_name || role.name)}
                                                </h5>
                                                <code class="text-muted fs-11">role: ${escapeHtml(role.name)}</code>
                                            </div>
                                        </div>
                                        <span class="badge bg-primary-subtle text-primary fs-11">
                                            ${perms.length} Izin
                                        </span>
                                    </div>
                                    <p class="text-muted fs-12 mb-3" style="min-height: 38px;">
                                        ${escapeHtml(role.description || 'Tidak ada deskripsi peran.')}
                                    </p>

                                    <!-- Mini Preview Permissions -->
                                    <div class="mb-3">
                                        <div class="d-flex flex-wrap gap-1" style="max-height: 52px; overflow-y: hidden;">
                                            ${permsPreview} ${extraPerms}
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 pt-2 border-top">
                                    <button type="button" class="btn btn-outline-primary btn-sm flex-grow-1 fw-semibold"
                                        onclick="manageRolePermissionsById(${role.id})">
                                        <i class="ri-checkbox-multiple-line me-1"></i> Atur Izin Role
                                    </button>
                                    ${deleteBtn}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $container.append(cardHtml);
            });
        }

        // ── Render Table Users (Tab 3) ──
        function renderUsersTable(users) {
            const $tbody = $('#tableUserAccessBody');
            $tbody.empty();

            if (!users || users.length === 0) {
                $tbody.html(`
                    <tr class="empty-state-row">
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="ri-user-unfollow-line fs-28 d-block mb-1 text-secondary"></i>
                            Belum ada data user.
                        </td>
                    </tr>
                `);
                return;
            }

            users.forEach((u, idx) => {
                const initial = (u.username || 'U').substring(0, 1).toUpperCase();
                const fullname = u.fullname || u.username;
                const userRoles = u.custom_roles || [];
                const rolesNames = userRoles.map(r => r.name).join(' ');
                const searchStr = `${u.username} ${fullname} ${u.jabatan || ''} ${u.departemen || ''} ${rolesNames}`.toLowerCase();

                let rolesBadges = '<span class="text-muted fs-12">-</span>';
                if (userRoles.length > 0) {
                    rolesBadges = userRoles.map(r =>
                        `<span class="badge bg-success-subtle text-success border border-success-subtle me-1 px-2 py-1 fs-11">
                            <i class="ri-shield-check-line me-1"></i>${escapeHtml(r.display_name || r.name)}
                        </span>`
                    ).join('');
                }

                let permsBadge = '<span class="text-muted fs-12">-</span>';
                const directCount = u.direct_permissions ? u.direct_permissions.length : 0;
                if (directCount > 0) {
                    permsBadge = `
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-11">
                            <i class="ri-key-line me-1"></i>${directCount} direct izin
                        </span>
                    `;
                }

                const rowHtml = `
                    <tr class="user-row" id="user_row_${u.id}" data-search="${searchStr}" data-roles="${rolesNames.toLowerCase()}">
                        <td class="text-center text-muted fs-12">${idx + 1}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs">
                                    <span class="avatar-title rounded-circle bg-primary-subtle fw-bold fs-12">
                                        ${initial}
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark fs-13">${escapeHtml(fullname)}</div>
                                    <div class="text-muted fs-11">&#64;${escapeHtml(u.username)}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1 fs-11">${escapeHtml(u.jabatan || 'Staff')}</span>
                            <small class="text-muted d-block fs-11 mt-1">${escapeHtml(u.departemen || '-')}</small>
                        </td>
                        <td class="user-roles-cell">${rolesBadges}</td>
                        <td class="user-perms-cell">${permsBadge}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-primary btn-sm px-2 py-1 fw-semibold"
                                onclick="openUserAccessModal(${u.id})">
                                <i class="ri-user-shared-line me-1"></i> Atur Izin
                            </button>
                        </td>
                    </tr>
                `;
                $tbody.append(rowHtml);
            });
        }

        // ── Render Modal Role Checkboxes ──
        function renderRoleModalCheckboxes(permissions) {
            const $container = $('#rolePermModalContainer');
            $container.empty();

            if (!permissions || permissions.length === 0) {
                $container.html('<div class="text-muted text-center py-3">Belum ada permission terdaftar.</div>');
                return;
            }

            const grouped = {};
            permissions.forEach(p => {
                const grp = p.group || 'General';
                if (!grouped[grp]) grouped[grp] = [];
                grouped[grp].push(p);
            });

            Object.keys(grouped).forEach(grpName => {
                const items = grouped[grpName];
                const slug = slugify(grpName);

                let itemsHtml = '';
                items.forEach(item => {
                    itemsHtml += `
                        <div class="col-md-6 perm-modal-item" data-perm-text="${(item.name + ' ' + (item.description || '')).toLowerCase()}">
                            <div class="form-check">
                                <input class="form-check-input role-perm-checkbox role-group-${slug}"
                                    type="checkbox" name="permissions[]" value="${item.id}"
                                    id="role_perm_${item.id}" onchange="updateRoleSelectedCounter()">
                                <label class="form-check-label perm-item-label" for="role_perm_${item.id}">
                                    <span class="fw-semibold text-dark font-monospace fs-12">${escapeHtml(item.name)}</span>
                                    ${item.description ? `<small class="d-block text-muted fs-11">${escapeHtml(item.description)}</small>` : ''}
                                </label>
                            </div>
                        </div>
                    `;
                });

                const boxHtml = `
                    <div class="perm-group-box perm-modal-group" data-group-name="${grpName.toLowerCase()}">
                        <div class="perm-group-header">
                            <span class="fw-bold text-primary fs-12">
                                <i class="ri-folder-3-line me-1"></i>${escapeHtml(grpName)}
                                <span class="badge bg-light text-muted border ms-1">${items.length}</span>
                            </span>
                            <a href="javascript:void(0)" class="fs-11 text-decoration-none fw-semibold"
                                onclick="toggleGroupRolePermissions('${slug}')">
                                Toggle Semua
                            </a>
                        </div>
                        <div class="p-2">
                            <div class="row g-2">
                                ${itemsHtml}
                            </div>
                        </div>
                    </div>
                `;
                $container.append(boxHtml);
            });
        }

        // ── Render Modal User Roles ──
        function renderUserModalRoles(roles) {
            const $container = $('#userRolesModalContainer');
            $container.empty();

            if (!roles || roles.length === 0) {
                $container.html('<div class="text-muted fs-12">Belum ada role terdaftar.</div>');
                return;
            }

            roles.forEach(r => {
                const permCount = r.permissions ? r.permissions.length : 0;
                const html = `
                    <div class="col-md-4 col-sm-6">
                        <div class="role-select-pill">
                            <div class="form-check mb-0">
                                <input class="form-check-input user-role-checkbox" type="checkbox"
                                    name="roles[]" value="${r.id}" id="user_role_${r.id}">
                                <label class="form-check-label fs-12 fw-semibold cursor-pointer w-100" for="user_role_${r.id}">
                                    ${escapeHtml(r.display_name || r.name)}
                                    <small class="d-block text-muted fw-normal">${permCount} izin</small>
                                </label>
                            </div>
                        </div>
                    </div>
                `;
                $container.append(html);
            });
        }

        // ── Render Modal User Direct Permissions ──
        function renderUserModalPermissions(permissions) {
            const $container = $('#userPermsModalContainer');
            $container.empty();

            if (!permissions || permissions.length === 0) {
                $container.html('<div class="text-muted fs-12">Belum ada permission.</div>');
                return;
            }

            const grouped = {};
            permissions.forEach(p => {
                const grp = p.group || 'General';
                if (!grouped[grp]) grouped[grp] = [];
                grouped[grp].push(p);
            });

            Object.keys(grouped).forEach(grpName => {
                const items = grouped[grpName];
                const slug = slugify(grpName);

                let itemsHtml = '';
                items.forEach(item => {
                    itemsHtml += `
                        <div class="col-md-6 perm-user-item" data-perm-text="${(item.name + ' ' + (item.description || '')).toLowerCase()}">
                            <div class="form-check">
                                <input class="form-check-input user-perm-checkbox user-group-${slug}"
                                    type="checkbox" name="permissions[]" value="${item.id}" id="user_perm_${item.id}">
                                <label class="form-check-label perm-item-label" for="user_perm_${item.id}">
                                    <span class="fw-semibold text-dark font-monospace fs-12">${escapeHtml(item.name)}</span>
                                    ${item.description ? `<small class="d-block text-muted fs-11">${escapeHtml(item.description)}</small>` : ''}
                                </label>
                            </div>
                        </div>
                    `;
                });

                const boxHtml = `
                    <div class="perm-group-box perm-user-group" data-group-name="${grpName.toLowerCase()}">
                        <div class="perm-group-header py-1">
                            <span class="fw-bold text-primary fs-11">
                                <i class="ri-folder-3-line me-1"></i>${escapeHtml(grpName)}
                            </span>
                            <a href="javascript:void(0)" class="fs-11 text-decoration-none"
                                onclick="toggleGroupUserPermissions('${slug}')">
                                Toggle
                            </a>
                        </div>
                        <div class="p-2">
                            <div class="row g-2">
                                ${itemsHtml}
                            </div>
                        </div>
                    </div>
                `;
                $container.append(boxHtml);
            });
        }

        // ========================================================
        // INITIALIZATION & EVENT LISTENERS
        // ========================================================
        $(document).ready(function() {
            // Inisialisasi Paginator Permission Tab
            permPaginator = new TablePaginator({
                rowSelector: '.permission-row',
                perPageSelector: '#permPerPage',
                infoSelector: '#permPaginationInfo',
                navSelector: '#permPaginationNav',
                updateNumber: true,
                emptyMessage: 'Tidak ada permission yang cocok dengan filter.',
                filterFn: function(el) {
                    const selectedGroup = ($('#filterGroupPermission').val() || '').toLowerCase().trim();
                    const query = ($('#searchPermission').val() || '').toLowerCase().trim();
                    const rowGroup = ($(el).data('group') || '').toLowerCase();
                    const rowSearch = ($(el).data('search') || '').toLowerCase();
                    const matchGroup = !selectedGroup || rowGroup === selectedGroup;
                    const matchQuery = !query || rowSearch.includes(query);
                    return matchGroup && matchQuery;
                }
            });

            // Inisialisasi Paginator User Access Tab
            userPaginator = new TablePaginator({
                rowSelector: '.user-row',
                perPageSelector: '#userPerPage',
                infoSelector: '#userPaginationInfo',
                navSelector: '#userPaginationNav',
                updateNumber: true,
                emptyMessage: 'Tidak ada pengguna yang cocok dengan pencarian.',
                filterFn: function(el) {
                    const query = ($('#searchUserAccess').val() || '').toLowerCase().trim();
                    const selectedRole = ($('#filterRoleUser').val() || '').toLowerCase().trim();
                    const rowSearch = ($(el).data('search') || '').toLowerCase();
                    const rowRoles = ($(el).data('roles') || '').toLowerCase();

                    const matchQuery = !query || rowSearch.includes(query);
                    const matchRole = !selectedRole || rowRoles.includes(selectedRole);
                    return matchQuery && matchRole;
                }
            });

            // Listeners Filter Permissions
            $('#filterGroupPermission').on('change', function() {
                permPaginator.currentPage = 1;
                permPaginator.refresh();
            });

            $('#searchPermission').on('input', function() {
                permPaginator.currentPage = 1;
                permPaginator.refresh();
            });

            $('#btnResetFilterPerm').on('click', function() {
                $('#searchPermission').val('');
                $('#filterGroupPermission').val('');
                permPaginator.currentPage = 1;
                permPaginator.refresh();
            });

            // Listeners Search Roles
            $('#searchRole').on('input', function() {
                const q = $(this).val().toLowerCase().trim();
                let matchCount = 0;
                $('.role-card-col').each(function() {
                    const text = ($(this).data('search') || '').toLowerCase();
                    const match = !q || text.includes(q);
                    $(this).toggle(match);
                    if (match) matchCount++;
                });

                $('#rolesContainer .paginator-empty-row').remove();
                if (matchCount === 0 && $('.role-card-col').length > 0) {
                    $('#rolesContainer').append(`
                        <div class="col-12 text-center py-4 text-muted paginator-empty-row">
                            <i class="ri-search-eye-line fs-20 d-block mb-1 text-secondary"></i>
                            Tidak ada role yang cocok dengan pencarian.
                        </div>
                    `);
                }
            });

            // Listeners Filter Users
            $('#searchUserAccess').on('input', function() {
                userPaginator.currentPage = 1;
                userPaginator.refresh();
            });

            $('#filterRoleUser').on('change', function() {
                userPaginator.currentPage = 1;
                userPaginator.refresh();
            });

            // Tab persistence via URL hash
            const hash = window.location.hash;
            if (hash) {
                const triggerEl = document.querySelector(`.access-tabs a[href="${hash}"]`);
                if (triggerEl) {
                    const tab = new bootstrap.Tab(triggerEl);
                    tab.show();
                }
            }

            $('.access-tabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                history.replaceState(null, null, $(e.target).attr('href'));
                if (permPaginator) permPaginator.refresh();
                if (userPaginator) userPaginator.refresh();
            });

            // Search filter inside Modal Role
            $('#searchPermInRoleModal').on('input', function() {
                const q = $(this).val().toLowerCase().trim();
                if (!q) {
                    $('.perm-modal-item').show();
                    $('.perm-modal-group').show();
                    return;
                }
                $('.perm-modal-group').each(function() {
                    let groupHasMatch = false;
                    $(this).find('.perm-modal-item').each(function() {
                        const text = ($(this).data('perm-text') || '').toLowerCase();
                        const match = text.includes(q);
                        $(this).toggle(match);
                        if (match) groupHasMatch = true;
                    });
                    $(this).toggle(groupHasMatch);
                });
            });

            // Search filter inside Modal User
            $('#searchPermInUserModal').on('input', function() {
                const q = $(this).val().toLowerCase().trim();
                if (!q) {
                    $('.perm-user-item').show();
                    $('.perm-user-group').show();
                    return;
                }
                $('.perm-user-group').each(function() {
                    let groupHasMatch = false;
                    $(this).find('.perm-user-item').each(function() {
                        const text = ($(this).data('perm-text') || '').toLowerCase();
                        const match = text.includes(q);
                        $(this).toggle(match);
                        if (match) groupHasMatch = true;
                    });
                    $(this).toggle(groupHasMatch);
                });
            });

            // Pertama kali buka halaman: Muat data via AJAX
            loadAccessControlData(false);
        });

        // ========================================================
        // PERMISSIONS CRUD
        // ========================================================
        function openCreatePermissionModal() {
            $('#formPermission')[0].reset();
            $('#perm_id').val('');
            $('#perm_group_select').val('');
            $('#wrapperNewGroup').hide();
            $('#perm_group_new').val('').prop('required', false);
            $('#modalPermissionTitle').html('<i class="ri-key-2-line me-1"></i> Tambah Permission Baru');
            $('#btnSubmitPermission').html('<i class="ri-save-line me-1"></i> Simpan Permission');
            const modal = new bootstrap.Modal(document.getElementById('modalPermission'));
            modal.show();
        }

        function editPermissionById(id) {
            const perm = accessData.permissions.find(p => p.id === id);
            if (perm) {
                editPermission(perm);
            }
        }

        function editPermission(perm) {
            $('#formPermission')[0].reset();
            $('#perm_id').val(perm.id);
            $('#perm_name').val(perm.name);
            $('#perm_desc').val(perm.description || '');

            const group = perm.group || '';
            const $select = $('#perm_group_select');
            let optionExists = false;
            $select.find('option').each(function() {
                if ($(this).val() === group) {
                    optionExists = true;
                    return false;
                }
            });

            if (optionExists) {
                $select.val(group);
                $('#wrapperNewGroup').hide();
                $('#perm_group_new').val('').prop('required', false);
            } else if (group) {
                $select.find('option[value="__new__"]').before(`<option value="${escapeHtml(group)}">${escapeHtml(group)}</option>`);
                $select.val(group);
                $('#wrapperNewGroup').hide();
                $('#perm_group_new').val('').prop('required', false);
            } else {
                $select.val('');
                $('#wrapperNewGroup').hide();
                $('#perm_group_new').val('').prop('required', false);
            }

            $('#modalPermissionTitle').html(
                '<i class="ri-edit-2-line me-1"></i> Edit Permission: <span class="badge bg-light text-primary">' + escapeHtml(perm.name) + '</span>');
            $('#btnSubmitPermission').html('<i class="ri-check-line me-1"></i> Perbarui Permission');
            const modal = new bootstrap.Modal(document.getElementById('modalPermission'));
            modal.show();
        }

        function submitPermissionForm(e) {
            e.preventDefault();
            const id = $('#perm_id').val();
            const isEdit = Boolean(id);
            const url = isEdit ?
                "{{ url('access-control/permissions') }}/" + id :
                "{{ route('access-control.permissions.store') }}";

            let selectedGroup = $('#perm_group_select').val();
            if (selectedGroup === '__new__') {
                selectedGroup = $('#perm_group_new').val().trim();
                if (!selectedGroup) {
                    Swal.fire('Perhatian', 'Silakan masukkan nama grup baru.', 'warning');
                    $('#perm_group_new').focus();
                    return;
                }
            }

            if (!selectedGroup) {
                Swal.fire('Perhatian', 'Silakan pilih atau buat modul / grup fitur.', 'warning');
                $('#perm_group_select').focus();
                return;
            }

            const postData = {
                _token: "{{ csrf_token() }}",
                group: selectedGroup,
                name: $('#perm_name').val().trim(),
                description: $('#perm_desc').val(),
            };

            if (isEdit) {
                postData._method = 'PUT';
            }

            $('#btnSubmitPermission').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

            $.ajax({
                url: url,
                type: 'POST',
                data: postData,
                success: function(res) {
                    bootstrap.Modal.getInstance(document.getElementById('modalPermission'))?.hide();
                    $('#btnSubmitPermission').prop('disabled', false).html(
                        '<i class="ri-save-line me-1"></i> Simpan Permission');

                    const p = res.data;

                    // Update local state
                    if (isEdit) {
                        const idx = accessData.permissions.findIndex(item => item.id == id);
                        if (idx !== -1) {
                            accessData.permissions[idx] = p;
                        }
                    } else {
                        accessData.permissions.unshift(p);
                        if (!accessData.groups.includes(p.group)) {
                            accessData.groups.push(p.group);
                        }
                    }

                    // Re-render
                    renderStats();
                    renderGroupOptions(accessData.groups);
                    renderPermissionsTable(accessData.permissions);
                    renderRoleModalCheckboxes(accessData.permissions);
                    renderUserModalPermissions(accessData.permissions);

                    if (permPaginator) permPaginator.refresh();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(err) {
                    $('#btnSubmitPermission').prop('disabled', false).html(
                        '<i class="ri-save-line me-1"></i> Simpan Permission');
                    const msg = err.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.';
                    Swal.fire('Gagal', msg, 'error');
                }
            });
        }

        function deletePermission(id, name) {
            Swal.fire({
                title: 'Hapus Permission?',
                html: `Apakah Anda yakin ingin menghapus permission <code>${escapeHtml(name)}</code>?<br><small class="text-danger">Aksi ini juga akan mencabut izin ini dari semua role dan pengguna terkait.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="ri-delete-bin-line me-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('access-control/permissions') }}/" + id,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: 'DELETE'
                        },
                        success: function(res) {
                            // Update local state
                            accessData.permissions = accessData.permissions.filter(p => p.id !== id);

                            // Animasi fade out row
                            const $row = $(`#perm_row_${id}`);
                            $row.fadeOut(200, function() {
                                renderPermissionsTable(accessData.permissions);
                                renderStats();
                                renderRoleModalCheckboxes(accessData.permissions);
                                renderUserModalPermissions(accessData.permissions);
                                if (permPaginator) permPaginator.refresh();
                            });

                            toastr.success(res.message);
                        },
                        error: function(err) {
                            const msg = err.responseJSON?.message || 'Gagal menghapus permission.';
                            Swal.fire('Gagal', msg, 'error');
                        }
                    });
                }
            });
        }

        // ========================================================
        // ROLES CRUD
        // ========================================================
        function openCreateRoleModal() {
            $('#formRole')[0].reset();
            $('#role_id').val('');
            $('#modalRoleTitle').html('<i class="ri-shield-line me-1"></i> Tambah Role Baru');
            $('#btnSubmitRole').html('<i class="ri-save-line me-1"></i> Simpan Role');
            $('.role-perm-checkbox').prop('checked', false);
            updateRoleSelectedCounter();
            const modal = new bootstrap.Modal(document.getElementById('modalRole'));
            modal.show();
        }

        function manageRolePermissionsById(id) {
            const role = accessData.roles.find(r => r.id === id);
            if (role) {
                const permIds = (role.permissions || []).map(p => p.id);
                manageRolePermissions(role.id, role.name, role.display_name, role.description, permIds);
            }
        }

        function manageRolePermissions(id, name, displayName, desc, assignedPermIds) {
            $('#formRole')[0].reset();
            $('#role_id').val(id);
            $('#role_name').val(name);
            $('#role_display_name').val(displayName || name);
            $('#role_desc').val(desc || '');
            $('.role-perm-checkbox').prop('checked', false);

            if (assignedPermIds && Array.isArray(assignedPermIds)) {
                assignedPermIds.forEach(pId => {
                    $(`#role_perm_${pId}`).prop('checked', true);
                });
            }

            updateRoleSelectedCounter();
            $('#modalRoleTitle').html(
                '<i class="ri-shield-check-line me-1"></i> Atur Izin Role: <span class="badge bg-light text-success">' +
                escapeHtml(displayName || name) + '</span>');
            $('#btnSubmitRole').html('<i class="ri-check-line me-1"></i> Simpan Perubahan Role');
            const modal = new bootstrap.Modal(document.getElementById('modalRole'));
            modal.show();
        }

        function updateRoleSelectedCounter() {
            const checked = $('.role-perm-checkbox:checked').length;
            $('#roleSelectedCount').text(checked);
        }

        function toggleAllRolePermissions(status) {
            $('.role-perm-checkbox:visible').prop('checked', status);
            updateRoleSelectedCounter();
        }

        function toggleGroupRolePermissions(groupSlug) {
            const $chk = $(`.role-group-${groupSlug}:visible`);
            const allChecked = $chk.length && $chk.filter(':checked').length === $chk.length;
            $chk.prop('checked', !allChecked);
            updateRoleSelectedCounter();
        }

        function submitRoleForm(e) {
            e.preventDefault();
            const id = $('#role_id').val();
            const isEdit = Boolean(id);
            const url = isEdit ?
                "{{ url('access-control/roles') }}/" + id :
                "{{ route('access-control.roles.store') }}";

            const checkedPerms = [];
            $('.role-perm-checkbox:checked').each(function() {
                checkedPerms.push($(this).val());
            });

            const postData = {
                _token: "{{ csrf_token() }}",
                name: $('#role_name').val().trim(),
                display_name: $('#role_display_name').val().trim(),
                description: $('#role_desc').val(),
                permissions: checkedPerms
            };

            if (isEdit) {
                postData._method = 'PUT';
            }

            $('#btnSubmitRole').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

            $.ajax({
                url: url,
                type: 'POST',
                data: postData,
                success: function(res) {
                    bootstrap.Modal.getInstance(document.getElementById('modalRole'))?.hide();
                    $('#btnSubmitRole').prop('disabled', false).html('<i class="ri-save-line me-1"></i> Simpan Role');

                    // Muat ulang data terbaru tanpa reload halaman
                    loadAccessControlData(false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(err) {
                    $('#btnSubmitRole').prop('disabled', false).html('<i class="ri-save-line me-1"></i> Simpan Role');
                    const msg = err.responseJSON?.message || 'Terjadi kesalahan saat menyimpan role.';
                    Swal.fire('Gagal', msg, 'error');
                }
            });
        }

        function deleteRole(id, name) {
            Swal.fire({
                title: 'Hapus Role?',
                html: `Apakah Anda yakin ingin menghapus role <code>${escapeHtml(name)}</code>?<br><small class="text-danger">Pengguna yang memiliki role ini tidak akan lagi memiliki hak akses terkait role tersebut.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="ri-delete-bin-line me-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('access-control/roles') }}/" + id,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: 'DELETE'
                        },
                        success: function(res) {
                            $(`#role_card_${id}`).fadeOut(200, function() {
                                $(this).remove();
                                accessData.roles = accessData.roles.filter(r => r.id !== id);
                                renderStats();
                                renderRoleFilterOptions(accessData.roles);
                                renderUserModalRoles(accessData.roles);
                            });
                            toastr.success(res.message);
                        },
                        error: function(err) {
                            const msg = err.responseJSON?.message || 'Gagal menghapus role.';
                            Swal.fire('Gagal', msg, 'error');
                        }
                    });
                }
            });
        }

        // ========================================================
        // USER ACCESS (ROLES & DIRECT PERMISSIONS MAPPING)
        // ========================================================
        function openUserAccessModal(userId) {
            $('#formUserAccess')[0].reset();
            $('#access_user_id').val(userId);
            $('.user-role-checkbox').prop('checked', false);
            $('.user-perm-checkbox').prop('checked', false);

            // Fetch user permissions
            $.ajax({
                url: "{{ url('access-control/user') }}/" + userId,
                type: 'GET',
                success: function(res) {
                    const u = res.data;
                    $('#access_user_name').text(u.fullname || u.username);
                    $('#access_user_jabatan').text(u.jabatan || 'Staff');
                    $('#access_avatar_letter').text((u.username || 'U').substring(0, 1).toUpperCase());

                    if (u.roles && Array.isArray(u.roles)) {
                        u.roles.forEach(rId => {
                            $(`#user_role_${rId}`).prop('checked', true);
                        });
                    }

                    if (u.direct_permissions && Array.isArray(u.direct_permissions)) {
                        u.direct_permissions.forEach(pId => {
                            $(`#user_perm_${pId}`).prop('checked', true);
                        });
                    }

                    const modal = new bootstrap.Modal(document.getElementById('modalUserAccess'));
                    modal.show();
                },
                error: function(err) {
                    Swal.fire('Error', 'Gagal mengambil data hak akses user.', 'error');
                }
            });
        }

        function toggleGroupUserPermissions(groupSlug) {
            const $chk = $(`.user-group-${groupSlug}:visible`);
            const allChecked = $chk.length && $chk.filter(':checked').length === $chk.length;
            $chk.prop('checked', !allChecked);
        }

        function submitUserAccessForm(e) {
            e.preventDefault();
            const userId = $('#access_user_id').val();
            const url = "{{ url('access-control/user') }}/" + userId;

            const selectedRoles = [];
            $('.user-role-checkbox:checked').each(function() {
                selectedRoles.push($(this).val());
            });

            const selectedPerms = [];
            $('.user-perm-checkbox:checked').each(function() {
                selectedPerms.push($(this).val());
            });

            $('#btnSubmitUserAccess').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    roles: selectedRoles,
                    permissions: selectedPerms
                },
                success: function(res) {
                    bootstrap.Modal.getInstance(document.getElementById('modalUserAccess'))?.hide();
                    $('#btnSubmitUserAccess').prop('disabled', false).html(
                        '<i class="ri-save-line me-1"></i> Simpan Hak Akses');

                    // Update live cell DOM
                    const updatedUser = res.data;
                    const uId = updatedUser?.user_id || userId;
                    const $userRow = $(`#user_row_${uId}`);

                    if ($userRow.length && updatedUser) {
                        const $rolesCell = $userRow.find('.user-roles-cell');
                        const $permsCell = $userRow.find('.user-perms-cell');

                        const assignedRoles = updatedUser.roles || [];
                        if (assignedRoles.length === 0) {
                            $rolesCell.html('<span class="text-muted fs-12">-</span>');
                        } else {
                            let badgesHtml = '';
                            assignedRoles.forEach(r => {
                                badgesHtml +=
                                    `<span class="badge bg-success-subtle text-success border border-success-subtle me-1 px-2 py-1 fs-11"><i class="ri-shield-check-line me-1"></i>${escapeHtml(r.display_name || r.name)}</span>`;
                            });
                            $rolesCell.html(badgesHtml);
                        }

                        const directCount = updatedUser.direct_permissions_count ?? selectedPerms.length;
                        if (directCount > 0) {
                            $permsCell.html(
                                `<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-11"><i class="ri-key-line me-1"></i>${directCount} direct izin</span>`
                            );
                        } else {
                            $permsCell.html('<span class="text-muted fs-12">-</span>');
                        }
                    }

                    toastr.success(res.message);
                },
                error: function(err) {
                    $('#btnSubmitUserAccess').prop('disabled', false).html(
                        '<i class="ri-save-line me-1"></i> Simpan Hak Akses');
                    const msg = err.responseJSON?.message || 'Gagal menyimpan hak akses pengguna.';
                    Swal.fire('Gagal', msg, 'error');
                }
            });
        }
    </script>
@endsection
