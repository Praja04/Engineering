@extends('layouts.app')

@section('title', 'Manajemen Role & Permission')

@section('styles')
    <style>
        /* Styling Nav Tabs Access Control */
        .access-tabs .nav-link {
            font-size: 13.5px;
            font-weight: 600;
            color: #495057;
            padding: 12px 20px;
            border: none;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .access-tabs .nav-link.active {
            color: #405189;
            border-bottom: 2px solid #405189;
            background: transparent;
        }

        .access-tabs .nav-link:hover {
            color: #405189;
        }

        /* Card Subtle Hover */
        .card {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        /* Permission Tag */
        .perm-slug-tag {
            font-family: monospace;
            font-size: 11.5px;
            font-weight: 700;
            color: #e02d1b;
            background: #fdf2f2;
            border: 1px solid #fbd5d5;
            padding: 2px 7px;
            border-radius: 4px;
            cursor: pointer;
        }

        .perm-slug-tag:hover {
            background: #fee2e2;
        }

        /* Group Module Accordion / Card */
        .perm-group-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #f8fafc;
            margin-bottom: 10px;
        }

        .perm-group-header {
            background: #ffffff;
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .perm-item-label {
            font-size: 12px;
            cursor: pointer;
            user-select: none;
        }

        /* Custom Checkbox Pill */
        .role-select-pill {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.15s ease;
            background: #ffffff;
        }

        .role-select-pill:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .role-select-pill input:checked+label {
            color: #405189;
            font-weight: 700;
        }

        .role-select-pill:has(input:checked) {
            border-color: #405189;
            background: #f0f3ff;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="row mb-2">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1 fw-bold text-dark">
                                <i class="mdi mdi-shield-key-outline text-primary me-2"></i>Manajemen Hak Akses & Permission
                            </h4>
                            <p class="text-muted mb-0 fs-13">Kelola hak akses granular per fitur sistem, struktur roles, dan
                                delegasi izin ke pengguna.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                                <i class="ri-refresh-line me-1"></i> Refresh
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ri-user-settings-line me-1"></i> Kelola Akun User
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4 Metric Cards -->
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
                                <h4 class="mb-0 fw-bold text-dark" id="statTotalPerms">{{ $permissions->count() }}</h4>
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
                                <h4 class="mb-0 fw-bold text-dark" id="statTotalRoles">{{ $roles->count() }}</h4>
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
                                <h4 class="mb-0 fw-bold text-dark">{{ $groups->count() }}</h4>
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
                                <h4 class="mb-0 fw-bold text-dark">{{ $users->count() }}</h4>
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
                                        <i class="ri-key-2-line me-1 align-middle text-primary"></i>
                                        Daftar Permission Fitur
                                        <span class="badge bg-primary-subtle text-primary rounded-pill ms-1"
                                            id="tabPermCount">{{ $permissions->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tabRoles" role="tab">
                                        <i class="ri-shield-user-line me-1 align-middle text-success"></i>
                                        Daftar Role
                                        <span class="badge bg-success-subtle text-success rounded-pill ms-1"
                                            id="tabRoleCount">{{ $roles->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tabUsers" role="tab">
                                        <i class="ri-user-settings-line me-1 align-middle text-warning"></i>
                                        Hak Akses Pengguna
                                        <span
                                            class="badge bg-warning-subtle text-warning rounded-pill ms-1">{{ $users->count() }}</span>
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
                                                @foreach ($groups as $grp)
                                                    <option value="{{ $grp }}">{{ $grp }}</option>
                                                @endforeach
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
                                            <tbody>
                                                @php
                                                    $groupColorMap = [
                                                        'Project Utility' => 'cyan',
                                                        'Compressor' => 'warning',
                                                        'WWTP' => 'info',
                                                        'Boiler' => 'danger',
                                                        'Maintenance' => 'success',
                                                        'Kalibrasi' => 'purple',
                                                        'Master Data' => 'secondary',
                                                        'User Management' => 'dark',
                                                    ];
                                                @endphp

                                                @forelse ($permissions as $index => $perm)
                                                    @php
                                                        $color = $groupColorMap[$perm->group] ?? 'primary';
                                                    @endphp
                                                    <tr class="permission-row" id="perm_row_{{ $perm->id }}"
                                                        data-group="{{ strtolower($perm->group) }}"
                                                        data-search="{{ strtolower($perm->name . ' ' . $perm->group . ' ' . ($perm->description ?? '')) }}">
                                                        <td class="text-center text-muted fs-12">{{ $index + 1 }}</td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle px-2 py-1 fs-11">
                                                                <i class="ri-folder-2-line me-1"></i>{{ $perm->group }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="perm-slug-tag" title="Klik untuk menyalin"
                                                                onclick="copySlugToClipboard('{{ $perm->name }}')">
                                                                <i class="ri-file-copy-line me-1"></i>{{ $perm->name }}
                                                            </span>
                                                        </td>
                                                        <td class="text-muted fs-12">{{ $perm->description ?: '-' }}</td>
                                                        <td class="text-center">
                                                            <div class="d-inline-flex gap-1">
                                                                <button type="button"
                                                                    class="btn btn-soft-primary btn-sm p-1 px-2"
                                                                    title="Edit Permission"
                                                                    onclick="editPermission({{ json_encode($perm) }})">
                                                                    <i class="ri-edit-2-line"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-soft-danger btn-sm p-1 px-2"
                                                                    title="Hapus Permission"
                                                                    onclick="deletePermission({{ $perm->id }}, '{{ $perm->name }}')">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-muted">Belum ada
                                                            permission terdaftar.</td>
                                                    </tr>
                                                @endforelse
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
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Kelola Role & Hak Akses Grup</h6>
                                            <p class="text-muted mb-0 fs-12">Role memaketkan sekumpulan izin untuk
                                                mempermudah delegasi akses ke pengguna.</p>
                                        </div>
                                        <button type="button" class="btn btn-success btn-sm fw-semibold shadow-sm"
                                            onclick="openCreateRoleModal()">
                                            <i class="ri-add-line align-middle me-1"></i> Tambah Role Baru
                                        </button>
                                    </div>

                                    <div class="row g-3" id="rolesContainer">
                                        @foreach ($roles as $role)
                                            <div class="col-xl-4 col-md-6" id="role_card_{{ $role->id }}">
                                                <div class="card border shadow-sm h-100 mb-0">
                                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                                        <div>
                                                            <div
                                                                class="d-flex justify-content-between align-items-start mb-2">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="avatar-sm flex-shrink-0">
                                                                        <span
                                                                            class="avatar-title bg-success-subtle text-success rounded-3 fs-18">
                                                                            <i class="ri-shield-check-line"></i>
                                                                        </span>
                                                                    </div>
                                                                    <div>
                                                                        <h5
                                                                            class="card-title mb-0 fw-bold text-dark fs-14">
                                                                            {{ $role->display_name }}
                                                                        </h5>
                                                                        <code class="text-muted fs-11">role:
                                                                            {{ $role->name }}</code>
                                                                    </div>
                                                                </div>
                                                                <span class="badge bg-primary-subtle text-primary fs-11">
                                                                    {{ $role->permissions->count() }} Izin
                                                                </span>
                                                            </div>
                                                            <p class="text-muted fs-12 mb-3" style="min-height: 38px;">
                                                                {{ $role->description ?: 'Tidak ada deskripsi peran.' }}
                                                            </p>

                                                            <!-- Mini Preview Permissions -->
                                                            <div class="mb-3">
                                                                <div class="d-flex flex-wrap gap-1"
                                                                    style="max-height: 52px; overflow-y: hidden;">
                                                                    @foreach ($role->permissions->take(4) as $rp)
                                                                        <span
                                                                            class="badge bg-light text-secondary border fs-10">{{ $rp->name }}</span>
                                                                    @endforeach
                                                                    @if ($role->permissions->count() > 4)
                                                                        <span
                                                                            class="badge bg-light text-muted border fs-10">+{{ $role->permissions->count() - 4 }}
                                                                            lainnya</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex gap-2 pt-2 border-top">
                                                            <button type="button"
                                                                class="btn btn-outline-primary btn-sm flex-grow-1 fw-semibold"
                                                                onclick="manageRolePermissions({{ $role->id }}, '{{ $role->name }}', '{{ $role->display_name }}', '{{ $role->description }}', {{ json_encode($role->permissions->pluck('id')) }})">
                                                                <i class="ri-checkbox-multiple-line me-1"></i> Atur Izin
                                                                Role
                                                            </button>
                                                            @if ($role->name !== 'admin')
                                                                <button type="button"
                                                                    class="btn btn-soft-danger btn-sm px-2"
                                                                    title="Hapus Role"
                                                                    onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- ========================================== -->
                                <!-- TAB 3: USER PERMISSIONS (HAK AKSES USER)   -->
                                <!-- ========================================== -->
                                <div class="tab-pane" id="tabUsers" role="tabpanel">
                                    <div
                                        class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="input-group input-group-sm" style="width: 280px;">
                                                <span class="input-group-text bg-light border-end-0"><i
                                                        class="ri-search-line text-muted"></i></span>
                                                <input type="text" id="searchUserAccess"
                                                    class="form-control border-start-0 ps-0"
                                                    placeholder="Cari user (nama, jabatan)...">
                                            </div>
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
                                            <tbody>
                                                @foreach ($users as $idx => $u)
                                                    <tr class="user-row" id="user_row_{{ $u->id }}"
                                                        data-search="{{ strtolower($u->username . ' ' . $u->fullname . ' ' . $u->jabatan . ' ' . $u->departemen) }}">
                                                        <td class="text-center text-muted fs-12">{{ $idx + 1 }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="avatar-xs">
                                                                    <span
                                                                        class="avatar-title rounded-circle bg-primary-subtle fw-bold fs-12">
                                                                        {{ strtoupper(substr($u->username, 0, 1)) }}
                                                                    </span>
                                                                </div>
                                                                <div>
                                                                    <div class="fw-semibold text-dark fs-13">
                                                                        {{ $u->fullname ?: $u->username }}</div>
                                                                    <div class="text-muted fs-11">&#64;{{ $u->username }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-light text-dark border px-2 py-1 fs-11">{{ $u->jabatan ?: 'Staff' }}</span>
                                                            <small
                                                                class="text-muted d-block fs-11 mt-1">{{ $u->departemen ?: '-' }}</small>
                                                        </td>
                                                        <td class="user-roles-cell">
                                                            @forelse ($u->customRoles as $cr)
                                                                <span
                                                                    class="badge bg-success-subtle text-success border border-success-subtle me-1 px-2 py-1 fs-11">
                                                                    <i
                                                                        class="ri-shield-check-line me-1"></i>{{ $cr->display_name }}
                                                                </span>
                                                            @empty
                                                                <span class="text-muted fs-12">-</span>
                                                            @endforelse
                                                        </td>
                                                        <td class="user-perms-cell">
                                                            @if ($u->directPermissions->count() > 0)
                                                                <span
                                                                    class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-11">
                                                                    <i
                                                                        class="ri-key-line me-1"></i>{{ $u->directPermissions->count() }}
                                                                    direct izin
                                                                </span>
                                                            @else
                                                                <span class="text-muted fs-12">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-outline-primary btn-sm px-2 py-1 fw-semibold"
                                                                onclick="openUserAccessModal({{ $u->id }})">
                                                                <i class="ri-user-shared-line me-1"></i> Atur Izin
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
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
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title text-white fs-15 fw-bold" id="modalPermissionTitle">
                        <i class="ri-key-2-line me-1"></i> Tambah Permission Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formPermission" onsubmit="submitPermissionForm(event)">
                    @csrf
                    <input type="hidden" id="perm_id">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Modul / Group Fitur <span class="text-danger">*</span></label>
                            <select id="perm_group_select" class="form-select" onchange="handleGroupSelectChange(this.value)" required>
                                <option value="" disabled selected>-- Pilih Modul / Group --</option>
                                @foreach ($groups as $grp)
                                    <option value="{{ $grp }}">{{ $grp }}</option>
                                @endforeach
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
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: TAMBAH / EDIT ROLE & CHECKLIST PERMISSIONS        -->
    <!-- ======================================================== -->
    <div class="modal fade" id="modalRole" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title text-white fs-15 fw-bold" id="modalRoleTitle">
                        <i class="ri-shield-user-line me-1"></i> Atur Role & Hak Akses
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formRole" onsubmit="submitRoleForm(event)" class="d-flex flex-column h-100">
                    @csrf
                    <input type="hidden" id="role_id">
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Tampilan Role <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="display_name" id="role_display_name" class="form-control"
                                    placeholder="Contoh: Supervisor Utility" required
                                    onkeyup="autoGenerateRoleSlug(this.value)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Slug / Key Role <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="role_name" class="form-control font-monospace"
                                    placeholder="Contoh: supervisor_utility" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Deskripsi Role</label>
                                <input type="text" name="description" id="role_desc" class="form-control"
                                    placeholder="Keterangan cakupan kerja role ini...">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2 pt-2 border-top">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><i
                                        class="ri-checkbox-multiple-line text-success me-1"></i>Pilih Hak Akses
                                    (Permissions):</h6>
                                <small class="text-muted">Centang izin yang diberikan kepada role ini.</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success-subtle text-success fs-12 px-2 py-1"
                                    id="roleSelectedCount">0 dipilih</span>
                                <button type="button" class="btn btn-outline-secondary btn-sm py-1"
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

                        <div class="p-2 border rounded bg-light" style="max-height: 380px; overflow-y: auto;">
                            @foreach ($permissionsGrouped as $groupName => $items)
                                <div class="perm-group-box perm-modal-group"
                                    data-group-name="{{ strtolower($groupName) }}">
                                    <div class="perm-group-header">
                                        <span class="fw-bold text-primary fs-12">
                                            <i class="ri-folder-3-line me-1"></i>{{ $groupName }}
                                            <span
                                                class="badge bg-light text-muted border ms-1">{{ $items->count() }}</span>
                                        </span>
                                        <a href="javascript:void(0)" class="fs-11 text-decoration-none fw-semibold"
                                            onclick="toggleGroupRolePermissions('{{ Str::slug($groupName) }}')">
                                            Toggle Semua
                                        </a>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-2">
                                            @foreach ($items as $item)
                                                <div class="col-md-6 perm-modal-item"
                                                    data-perm-text="{{ strtolower($item->name . ' ' . ($item->description ?? '')) }}">
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input role-perm-checkbox role-group-{{ Str::slug($groupName) }}"
                                                            type="checkbox" name="permissions[]"
                                                            value="{{ $item->id }}"
                                                            id="role_perm_{{ $item->id }}"
                                                            onchange="updateRoleSelectedCounter()">
                                                        <label class="form-check-label perm-item-label"
                                                            for="role_perm_{{ $item->id }}">
                                                            <span
                                                                class="fw-semibold text-dark font-monospace fs-12">{{ $item->name }}</span>
                                                            @if ($item->description)
                                                                <small
                                                                    class="d-block text-muted fs-11">{{ $item->description }}</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: ATUR HAK AKSES PENGGUNA (ROLES & DIRECT PERMISSIONS) -->
    <!-- ======================================================== -->
    <div class="modal fade" id="modalUserAccess" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark py-3">
                    <h5 class="modal-title fs-15 fw-bold" id="modalUserAccessTitle">
                        <i class="ri-user-settings-line me-1"></i> Atur Hak Akses Pengguna
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formUserAccess" onsubmit="submitUserAccessForm(event)" class="d-flex flex-column h-100">
                    @csrf
                    <input type="hidden" id="access_user_id">
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
                            <div class="row g-2">
                                @foreach ($roles as $r)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="role-select-pill">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input user-role-checkbox" type="checkbox"
                                                    name="roles[]" value="{{ $r->id }}"
                                                    id="user_role_{{ $r->id }}">
                                                <label class="form-check-label fs-12 fw-semibold cursor-pointer w-100"
                                                    for="user_role_{{ $r->id }}">
                                                    {{ $r->display_name }}
                                                    <small
                                                        class="d-block text-muted fw-normal">{{ $r->permissions->count() }}
                                                        izin</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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

                            <div class="p-2 border rounded bg-light" style="max-height: 280px; overflow-y: auto;">
                                @foreach ($permissionsGrouped as $groupName => $items)
                                    <div class="perm-group-box perm-user-group"
                                        data-group-name="{{ strtolower($groupName) }}">
                                        <div class="perm-group-header py-1">
                                            <span class="fw-bold text-primary fs-11">
                                                <i class="ri-folder-3-line me-1"></i>{{ $groupName }}
                                            </span>
                                            <a href="javascript:void(0)" class="fs-11 text-decoration-none"
                                                onclick="toggleGroupUserPermissions('{{ Str::slug($groupName) }}')">
                                                Toggle
                                            </a>
                                        </div>
                                        <div class="p-2">
                                            <div class="row g-2">
                                                @foreach ($items as $item)
                                                    <div class="col-md-6 perm-user-item"
                                                        data-perm-text="{{ strtolower($item->name . ' ' . ($item->description ?? '')) }}">
                                                        <div class="form-check">
                                                            <input
                                                                class="form-check-input user-perm-checkbox user-group-{{ Str::slug($groupName) }}"
                                                                type="checkbox" name="permissions[]"
                                                                value="{{ $item->id }}"
                                                                id="user_perm_{{ $item->id }}">
                                                            <label class="form-check-label perm-item-label"
                                                                for="user_perm_{{ $item->id }}">
                                                                <span
                                                                    class="fw-semibold text-dark font-monospace fs-12">{{ $item->name }}</span>
                                                                @if ($item->description)
                                                                    <small
                                                                        class="d-block text-muted fs-11">{{ $item->description }}</small>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
            if (!$('#role_id').val()) { // Hanya saat create baru
                const slug = val.toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s]/g, '')
                    .replace(/\s+/g, '_');
                $('#role_name').val(slug);
            }
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

                this.refresh();
            }

            refresh() {
                const rows = $(this.rowSelector);
                const matchingRows = [];

                rows.each((_, el) => {
                    if (this.filterFn(el)) {
                        matchingRows.push(el);
                    } else {
                        $(el).hide();
                    }
                });

                const totalItems = matchingRows.length;
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
                if (totalItems === 0) {
                    $(this.infoSelector).html('<span class="text-muted">' + this.emptyMessage + '</span>');
                } else {
                    $(this.infoSelector).html(
                        `Menampilkan <strong>${startIndex + 1}</strong> - <strong>${endIndex}</strong> dari <strong>${totalItems}</strong> data`
                    );
                }

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

        let permPaginator = null;
        let userPaginator = null;

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
                    const selectedGroup = ($('#filterGroupPermission').val() || '').toLowerCase()
                        .trim();
                    const query = ($('#searchPermission').val() || '').toLowerCase().trim();
                    const rowGroup = ($(el).data('group') || '').toLowerCase();
                    const rowSearch = ($(el).data('search') || '').toLowerCase();
                    const matchGroup = !selectedGroup || rowGroup === selectedGroup;
                    const matchQuery = !query || rowSearch.includes(query);
                    return matchGroup && matchQuery;
                }
            });

            $('#filterGroupPermission').on('change', function() {
                permPaginator.currentPage = 1;
                permPaginator.refresh();
            });
            $('#searchPermission').on('input', function() {
                permPaginator.currentPage = 1;
                permPaginator.refresh();
            });
            $('#btnResetFilterPerm').on('click', function() {
                $('#filterGroupPermission').val('');
                $('#searchPermission').val('');
                permPaginator.currentPage = 1;
                permPaginator.refresh();
            });

            // Inisialisasi Paginator User Access Tab
            userPaginator = new TablePaginator({
                rowSelector: '.user-row',
                perPageSelector: '#userPerPage',
                infoSelector: '#userPaginationInfo',
                navSelector: '#userPaginationNav',
                updateNumber: true,
                emptyMessage: 'Tidak ada user yang cocok dengan pencarian.',
                filterFn: function(el) {
                    const q = ($('#searchUserAccess').val() || '').toLowerCase().trim();
                    const searchTxt = ($(el).data('search') || '').toLowerCase();
                    return !q || searchTxt.includes(q);
                }
            });

            $('#searchUserAccess').on('input', function() {
                userPaginator.currentPage = 1;
                userPaginator.refresh();
            });

            // Tab persistence agar tab aktif tetap terjaga saat reload
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                history.replaceState(null, null, $(e.target).attr('href'));
            });
            const activeHash = window.location.hash;
            if (activeHash && $(`a[href="${activeHash}"]`).length) {
                new bootstrap.Tab($(`a[href="${activeHash}"]`)[0]).show();
            }

            // Search inside Role Modal
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

            // Search inside User Access Modal
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
                $select.find('option[value="__new__"]').before(`<option value="${group}">${group}</option>`);
                $select.val(group);
                $('#wrapperNewGroup').hide();
                $('#perm_group_new').val('').prop('required', false);
            } else {
                $select.val('');
                $('#wrapperNewGroup').hide();
                $('#perm_group_new').val('').prop('required', false);
            }

            $('#modalPermissionTitle').html(
                '<i class="ri-edit-2-line me-1"></i> Edit Permission: <span class="badge bg-light text-primary">' + perm
                .name + '</span>');
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
                Swal.fire('Perhatian', 'Silakan pilih atau buat grup fitur.', 'warning');
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

                    const p = res.data || postData;
                    const pId = id || p.id;

                    // Pastikan grup baru masuk ke opsi select modal dan filter
                    if (p.group) {
                        if (!$(`#perm_group_select option[value="${p.group}"]`).length) {
                            $('#perm_group_select option[value="__new__"]').before(
                                `<option value="${p.group}">${p.group}</option>`
                            );
                        }
                        if (!$(`#filterGroupPermission option[value="${p.group}"]`).length) {
                            $('#filterGroupPermission').append(
                                `<option value="${p.group}">${p.group}</option>`
                            );
                        }
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
                    const color = groupColorMap[(p.group || '').toLowerCase()] || 'primary';

                    if (isEdit) {
                        const $row = $(`#perm_row_${pId}`);
                        if ($row.length) {
                            $row.attr('data-group', (p.group || '').toLowerCase());
                            $row.attr('data-search',
                                `${p.name} ${p.group} ${p.description || ''}`
                                .toLowerCase());
                            $row.find('td:nth-child(2)').html(
                                `<span class="badge bg-${color}-subtle text-${color} border border-${color}-subtle px-2 py-1 fs-11"><i class="ri-folder-2-line me-1"></i>${p.group}</span>`
                            );
                            $row.find('td:nth-child(3)').html(
                                `<span class="perm-slug-tag" title="Klik untuk menyalin" onclick="copySlugToClipboard('${p.name}')"><i class="ri-file-copy-line me-1"></i>${p.name}</span>`
                            );
                            $row.find('td:nth-child(4)').text(p.description || '-');
                            $row.find('button[title="Edit Permission"]').attr('onclick',
                                `editPermission(${JSON.stringify(p)})`);
                        }
                    } else {
                        // Tambah row baru ke tabel
                        const newRowHtml = `
                            <tr class="permission-row" id="perm_row_${pId}"
                                data-group="${(p.group || '').toLowerCase()}"
                                data-search="${`${p.name} ${p.group} ${p.description || ''}`.toLowerCase()}">
                                <td class="text-center text-muted fs-12">1</td>
                                <td>
                                    <span class="badge bg-${color}-subtle text-${color} border border-${color}-subtle px-2 py-1 fs-11">
                                        <i class="ri-folder-2-line me-1"></i>${p.group}
                                    </span>
                                </td>
                                <td>
                                    <span class="perm-slug-tag" title="Klik untuk menyalin" onclick="copySlugToClipboard('${p.name}')">
                                        <i class="ri-file-copy-line me-1"></i>${p.name}
                                    </span>
                                </td>
                                <td class="text-muted fs-12">${p.description || '-'}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-soft-primary btn-sm p-1 px-2" title="Edit Permission"
                                            onclick='editPermission(${JSON.stringify(p)})'>
                                            <i class="ri-edit-2-line"></i>
                                        </button>
                                        <button type="button" class="btn btn-soft-danger btn-sm p-1 px-2" title="Hapus Permission"
                                            onclick="deletePermission(${pId}, '${p.name}')">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        $('#tablePermissions tbody').prepend(newRowHtml);
                        const curCount = parseInt($('#tabPermCount').text()) || 0;
                        $('#tabPermCount').text(curCount + 1);
                    }

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
                text: `Yakin ingin menghapus permission "${name}"? Izin ini akan dicabut dari seluruh role dan user.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                confirmButtonColor: '#d33',
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
                            $(`#perm_row_${id}`).fadeOut(300, function() {
                                $(this).remove();
                                const curCount = parseInt($('#tabPermCount').text()) || 1;
                                $('#tabPermCount').text(Math.max(0, curCount - 1));
                                if (permPaginator) permPaginator.refresh();
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function(err) {
                            Swal.fire('Gagal', err.responseJSON?.message || 'Gagal menghapus.',
                                'error');
                        }
                    });
                }
            });
        }

        // ========================================================
        // ROLES CRUD & PERMISSIONS MAPPING
        // ========================================================
        function updateRoleSelectedCounter() {
            const count = $('.role-perm-checkbox:checked').length;
            $('#roleSelectedCount').text(count + ' dipilih');
        }

        function openCreateRoleModal() {
            $('#formRole')[0].reset();
            $('#role_id').val('');
            $('.role-perm-checkbox').prop('checked', false);
            updateRoleSelectedCounter();
            $('#searchPermInRoleModal').val('');
            $('.perm-modal-item').show();
            $('.perm-modal-group').show();
            $('#modalRoleTitle').html('<i class="ri-shield-user-line me-1"></i> Tambah Role Baru');
            $('#btnSubmitRole').html('<i class="ri-save-line me-1"></i> Simpan Role');
            const modal = new bootstrap.Modal(document.getElementById('modalRole'));
            modal.show();
        }

        function manageRolePermissions(id, name, displayName, desc, permIds) {
            $('#formRole')[0].reset();
            $('#role_id').val(id);
            $('#role_name').val(name);
            $('#role_display_name').val(displayName);
            $('#role_desc').val(desc || '');
            $('.role-perm-checkbox').prop('checked', false);

            if (Array.isArray(permIds)) {
                permIds.forEach(pId => {
                    $(`#role_perm_${pId}`).prop('checked', true);
                });
            }
            updateRoleSelectedCounter();
            $('#searchPermInRoleModal').val('');
            $('.perm-modal-item').show();
            $('.perm-modal-group').show();

            $('#modalRoleTitle').html(
                '<i class="ri-shield-check-line me-1"></i> Atur Role: <span class="badge bg-light text-success">' +
                displayName + '</span>');
            $('#btnSubmitRole').html('<i class="ri-check-line me-1"></i> Simpan Perubahan Role');
            const modal = new bootstrap.Modal(document.getElementById('modalRole'));
            modal.show();
        }

        function toggleAllRolePermissions(check) {
            $('.role-perm-checkbox').prop('checked', check);
            updateRoleSelectedCounter();
        }

        function toggleGroupRolePermissions(groupSlug) {
            const boxes = $(`.role-group-${groupSlug}`);
            const allChecked = boxes.length > 0 && boxes.filter(':checked').length === boxes.length;
            boxes.prop('checked', !allChecked);
            updateRoleSelectedCounter();
        }

        function submitRoleForm(e) {
            e.preventDefault();
            const id = $('#role_id').val();
            const isEdit = Boolean(id);
            const url = isEdit ?
                "{{ url('access-control/roles') }}/" + id :
                "{{ route('access-control.roles.store') }}";

            const checkedPerms = $('.role-perm-checkbox:checked').map(function() {
                return parseInt($(this).val());
            }).get();

            const roleName = $('#role_name').val();
            const roleDisplayName = $('#role_display_name').val();
            const roleDesc = $('#role_desc').val();

            const postData = {
                _token: "{{ csrf_token() }}",
                name: roleName,
                display_name: roleDisplayName,
                description: roleDesc,
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
                    $('#btnSubmitRole').prop('disabled', false).html(
                        '<i class="ri-save-line me-1"></i> Simpan Role');

                    const r = res.data || postData;
                    const rId = id || r.id;

                    if (isEdit) {
                        const $card = $(`#role_card_${rId}`);
                        if ($card.length) {
                            $card.find('.card-title').text(roleDisplayName);
                            $card.find('code').text(`role: ${roleName}`);
                            $card.find('p.text-muted').text(roleDesc || 'Tidak ada deskripsi peran.');
                            $card.find('.badge.bg-primary-subtle').text(`${checkedPerms.length} Izin`);
                            $card.find('button[onclick^="manageRolePermissions"]').attr('onclick',
                                `manageRolePermissions(${rId}, '${roleName}', '${roleDisplayName}', '${roleDesc || ''}', ${JSON.stringify(checkedPerms)})`
                            );
                        }
                    } else {
                        // Card baru
                        const newCardHtml = `
                            <div class="col-xl-4 col-md-6" id="role_card_${rId}">
                                <div class="card border shadow-sm h-100 mb-0">
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
                                                        <h5 class="card-title mb-0 fw-bold text-dark fs-14">${roleDisplayName}</h5>
                                                        <code class="text-muted fs-11">role: ${roleName}</code>
                                                    </div>
                                                </div>
                                                <span class="badge bg-primary-subtle text-primary fs-11">${checkedPerms.length} Izin</span>
                                            </div>
                                            <p class="text-muted fs-12 mb-3" style="min-height: 38px;">${roleDesc || 'Tidak ada deskripsi peran.'}</p>
                                        </div>
                                        <div class="d-flex gap-2 pt-2 border-top">
                                            <button type="button" class="btn btn-outline-success btn-sm w-100 fw-semibold"
                                                onclick="manageRolePermissions(${rId}, '${roleName}', '${roleDisplayName}', '${roleDesc || ''}', ${JSON.stringify(checkedPerms)})">
                                                <i class="ri-settings-4-line me-1"></i> Atur Izin Role
                                            </button>
                                            <button type="button" class="btn btn-soft-danger btn-sm px-2" title="Hapus Role"
                                                onclick="deleteRole(${rId}, '${roleDisplayName}')">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#rolesContainer').append(newCardHtml);
                        const curRoleCount = parseInt($('#tabRoleCount').text()) || 0;
                        $('#tabRoleCount').text(curRoleCount + 1);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(err) {
                    $('#btnSubmitRole').prop('disabled', false).html(
                        '<i class="ri-save-line me-1"></i> Simpan Role');
                    Swal.fire('Gagal', err.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                }
            });
        }

        function deleteRole(id, name) {
            Swal.fire({
                title: 'Hapus Role?',
                text: `Yakin ingin menghapus role "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                confirmButtonColor: '#d33',
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
                            $(`#role_card_${id}`).fadeOut(300, function() {
                                $(this).remove();
                                const curRoleCount = parseInt($('#tabRoleCount').text()) || 1;
                                $('#tabRoleCount').text(Math.max(0, curRoleCount - 1));
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function(err) {
                            Swal.fire('Gagal', err.responseJSON?.message || 'Gagal menghapus.',
                                'error');
                        }
                    });
                }
            });
        }

        // ========================================================
        // USER ACCESS MANAGEMENT
        // ========================================================
        function openUserAccessModal(userId) {
            $('#formUserAccess')[0].reset();
            $('#access_user_id').val(userId);
            $('.user-role-checkbox').prop('checked', false);
            $('.user-perm-checkbox').prop('checked', false);
            $('#searchPermInUserModal').val('');
            $('.perm-user-item').show();
            $('.perm-user-group').show();

            $.get("{{ url('access-control/user') }}/" + userId, function(res) {
                if (res.status === 'success') {
                    const u = res.data;
                    const displayName = u.fullname || u.username;
                    $('#access_user_name').text(displayName);
                    $('#access_user_jabatan').text(u.jabatan || 'User');
                    $('#access_avatar_letter').text((u.username || 'U').charAt(0).toUpperCase());

                    if (Array.isArray(u.roles)) {
                        u.roles.forEach(rId => {
                            $(`#user_role_${rId}`).prop('checked', true);
                        });
                    }

                    if (Array.isArray(u.direct_permissions)) {
                        u.direct_permissions.forEach(pId => {
                            $(`#user_perm_${pId}`).prop('checked', true);
                        });
                    }

                    const modal = new bootstrap.Modal(document.getElementById('modalUserAccess'));
                    modal.show();
                }
            }).fail(function() {
                Swal.fire('Gagal', 'Tidak dapat memuat data hak akses user.', 'error');
            });
        }

        function toggleGroupUserPermissions(groupSlug) {
            const boxes = $(`.user-group-${groupSlug}`);
            const allChecked = boxes.length > 0 && boxes.filter(':checked').length === boxes.length;
            boxes.prop('checked', !allChecked);
        }

        function submitUserAccessForm(e) {
            e.preventDefault();
            const userId = $('#access_user_id').val();
            const url = "{{ url('access-control/user') }}/" + userId;

            const roles = $('.user-role-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            const perms = $('.user-perm-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            $('#btnSubmitUserAccess').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    roles: roles,
                    permissions: perms
                },
                success: function(res) {
                    bootstrap.Modal.getInstance(document.getElementById('modalUserAccess'))?.hide();
                    $('#btnSubmitUserAccess').prop('disabled', false).html(
                        '<i class="ri-save-line me-1"></i> Simpan Hak Akses');

                    // ── Client-side Live Update DOM Baris User Tanpa Reload ──
                    const $userRow = $(`#user_row_${userId}`);
                    if ($userRow.length) {
                        // 1. Update Kolom Assigned Roles
                        const $rolesCell = $userRow.find('.user-roles-cell');
                        const assignedRoles = (res.data && Array.isArray(res.data.roles)) ? res.data.roles : [];
                        if (assignedRoles.length > 0) {
                            let badgesHtml = '';
                            assignedRoles.forEach(r => {
                                badgesHtml +=
                                    `<span class="badge bg-success-subtle text-success border border-success-subtle me-1 px-2 py-1 fs-11"><i class="ri-shield-check-line me-1"></i>${r.display_name}</span>`;
                            });
                            $rolesCell.html(badgesHtml);
                        } else {
                            $rolesCell.html('<span class="text-muted fs-12">-</span>');
                        }

                        // 2. Update Kolom Direct Permissions
                        const $permsCell = $userRow.find('.user-perms-cell');
                        const directCount = res.data && typeof res.data.direct_permissions_count !==
                            'undefined' ?
                            res.data.direct_permissions_count :
                            perms.length;

                        if (directCount > 0) {
                            $permsCell.html(
                                `<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-11"><i class="ri-key-line me-1"></i>${directCount} direct izin</span>`
                            );
                        } else {
                            $permsCell.html('<span class="text-muted fs-12">-</span>');
                        }
                    }

                    // Notifikasi sukses tanpa reload
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(err) {
                    $('#btnSubmitUserAccess').prop('disabled', false).html(
                        '<i class="ri-save-line me-1"></i> Simpan Hak Akses');
                    Swal.fire('Gagal', err.responseJSON?.message || 'Gagal memperbarui izin user.', 'error');
                }
            });
        }
    </script>
@endsection
