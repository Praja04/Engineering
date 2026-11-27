@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('styles')
    <style>
        .profile-cover {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            height: 140px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            position: relative;
        }

        .profile-avatar-wrapper {
            position: relative;
            margin-top: -65px;
            display: inline-block;
        }

        .profile-avatar-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        [data-layout-mode="dark"] .profile-avatar-img,
        [data-bs-theme="dark"] .profile-avatar-img {
            border-color: #212529;
        }

        .avatar-edit-overlay {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 34px;
            height: 34px;
            background: #0d6efd;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .avatar-edit-overlay:hover {
            transform: scale(1.1);
            background: #0b5ed7;
        }

        .nav-tabs-custom {
            border-bottom: 2px solid #e9ebec;
        }

        [data-layout-mode="dark"] .nav-tabs-custom,
        [data-bs-theme="dark"] .nav-tabs-custom {
            border-bottom-color: #32383e;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            font-weight: 600;
            color: #495057;
            padding: 12px 20px;
            transition: all 0.2s ease;
        }

        [data-layout-mode="dark"] .nav-tabs-custom .nav-link,
        [data-bs-theme="dark"] .nav-tabs-custom .nav-link {
            color: #adb5bd;
        }

        .nav-tabs-custom .nav-link.active {
            color: #0d6efd !important;
            border-bottom-color: #0d6efd;
            background: transparent;
        }

        .password-toggle-btn {
            cursor: pointer;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- ── Breadcrumb Title ── --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 fw-bold"><i class="ri-user-settings-line me-2 text-primary"></i>Profil Pengguna
                        </h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Profil</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Profile Header Card ── --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden mb-3">
                        <div class="profile-cover d-flex align-items-center justify-content-end p-3">
                            <span class="badge bg-white text-dark shadow-sm px-3 py-2 fs-12 fw-semibold">
                                <i class="ri-shield-check-fill text-success me-1"></i> Akun Terverifikasi
                            </span>
                        </div>
                        <div class="card-body pt-0 px-4 pb-4">
                            <div
                                class="d-flex flex-column flex-md-row align-items-center align-items-md-end justify-content-between gap-3">
                                <div
                                    class="d-flex flex-column flex-md-row align-items-center align-items-md-end gap-3 text-center text-md-start">
                                    <div class="profile-avatar-wrapper">
                                        <img src="{{ $user->image_url }}" alt="Avatar" id="previewAvatarImg"
                                            class="rounded-circle profile-avatar-img">
                                        <label for="profilePhotoInput" class="avatar-edit-overlay"
                                            title="Ganti Foto Profil">
                                            <i class="ri-camera-fill fs-16"></i>
                                        </label>
                                    </div>
                                    <div class="mb-2">
                                        <h4 class="fw-bold mb-1 text-dark" id="headerDisplayFullName">
                                            {{ $user->fullname ?: $user->username }}
                                        </h4>
                                        <div
                                            class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2">
                                            <span class="text-muted fs-13">
                                                <i class="ri-at-line text-primary"></i><span
                                                    id="headerDisplayUsername">{{ $user->username }}</span>
                                            </span>
                                            <span
                                                class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-12">
                                                {{ strtoupper($user->jabatan ?: 'Staff') }}
                                            </span>
                                            @if ($user->departemen)
                                                <span
                                                    class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fs-12">
                                                    {{ strtoupper($user->departemen) }}
                                                </span>
                                            @endif
                                            @if ($user->role)
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fs-12">
                                                    {{ $user->role }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span
                                        class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-12">
                                        <i class="ri-checkbox-circle-fill me-1"></i> Aktif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Main Form Cards (Tabs) ── --}}
            <div class="row">
                <div class="col-xl-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent p-0">
                            <ul class="nav nav-tabs nav-tabs-custom px-4" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#tabEditProfile" role="tab">
                                        <i class="ri-user-3-line me-1 align-bottom"></i> Informasi Profil
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tabChangePassword" role="tab">
                                        <i class="ri-lock-password-line me-1 align-bottom"></i> Ganti Password
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-4">
                            <div class="tab-content">

                                {{-- ── TAB 1: Edit Informasi Profil ── --}}
                                <div class="tab-pane active" id="tabEditProfile" role="tabpanel">
                                    <form id="formUpdateProfile" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" id="profilePhotoInput" name="image" class="d-none"
                                            accept="image/jpeg,image/png,image/jpg,image/webp">

                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold fs-13">Nama Lengkap</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-muted"><i
                                                            class="ri-user-line"></i></span>
                                                    <input type="text" name="fullname" class="form-control"
                                                        placeholder="Masukkan nama lengkap Anda"
                                                        value="{{ old('fullname', $user->fullname) }}">
                                                </div>
                                                <small class="text-muted">Nama lengkap resmi pengguna.</small>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold fs-13">Username <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-muted"><i
                                                            class="ri-at-line"></i></span>
                                                    <input type="text" name="username" class="form-control" required
                                                        placeholder="Username akun"
                                                        value="{{ old('username', $user->username) }}">
                                                </div>
                                                <small class="text-muted">Digunakan untuk login ke sistem.</small>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold fs-13">Email <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-muted"><i
                                                            class="ri-mail-line"></i></span>
                                                    <input type="email" name="email" class="form-control" required
                                                        placeholder="contoh@pt-bas-id.com"
                                                        value="{{ old('email', $user->email) }}">
                                                </div>
                                                <small class="text-muted">Email untuk menerima notifikasi sistem.</small>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold fs-13">NIK (Nomor Induk
                                                    Karyawan)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-muted"><i
                                                            class="mdi mdi-id-card"></i></span>
                                                    <input type="text" name="nik" class="form-control"
                                                        placeholder="Nomor NIK karyawan"
                                                        value="{{ old('nik', $user->nik) }}">
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold fs-13">Departemen</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-muted"><i
                                                            class="ri-building-line"></i></span>
                                                    <input type="text" name="departemen" class="form-control"
                                                        placeholder="Contoh: Engineering / Utility / Maintenance"
                                                        value="{{ old('departemen', $user->departemen) }}">
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold fs-13">Bagian / Section</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-muted"><i
                                                            class="ri-node-tree"></i></span>
                                                    <input type="text" name="bagian" class="form-control"
                                                        placeholder="Contoh: Electrical, Mechanical, Utility Water"
                                                        value="{{ old('bagian', $user->bagian) }}">
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold fs-13 text-muted">Jabatan
                                                    (Struktural)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-muted"><i
                                                            class="ri-briefcase-line"></i></span>
                                                    <input type="text" class="form-control bg-light" readonly
                                                        value="{{ ucfirst($user->jabatan ?: '-') }}">
                                                    <span class="input-group-text bg-light text-muted"
                                                        title="Jabatan dikelola oleh Administrator"><i
                                                            class="ri-lock-line"></i></span>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold fs-13 text-muted">Role Akses
                                                    Sistem</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-muted"><i
                                                            class="ri-shield-user-line"></i></span>
                                                    <input type="text" class="form-control bg-light" readonly
                                                        value="{{ $user->role ?: ($user->customRoles->pluck('display_name')->implode(', ') ?: '-') }}">
                                                    <span class="input-group-text bg-light text-muted"
                                                        title="Role dikelola oleh Administrator"><i
                                                            class="ri-lock-line"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                                            <button type="reset" class="btn btn-outline-secondary">
                                                <i class="ri-refresh-line me-1"></i> Reset
                                            </button>
                                            <button type="submit" id="btnSubmitProfile" class="btn btn-primary px-4">
                                                <i class="ri-save-line me-1"></i> Simpan Perubahan Profil
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {{-- ── TAB 2: Ganti Password ── --}}
                                <div class="tab-pane" id="tabChangePassword" role="tabpanel">
                                    <form id="formUpdatePassword">
                                        @csrf
                                        <div class="row justify-content-center">
                                            <div class="col-12 col-md-8 col-lg-6">

                                                <div class="alert alert-info border-0 shadow-sm mb-4">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri-information-line fs-20 me-2 text-info"></i>
                                                        <div class="fs-13">
                                                            Gunakan kombinasi minimal <strong>6 karakter</strong>. Demi
                                                            keamanan, jangan gunakan password yang mudah ditebak.
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold fs-13">Password Saat Ini <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light text-muted"><i
                                                                class="ri-key-line"></i></span>
                                                        <input type="password" name="current_password"
                                                            id="inputCurrentPassword" class="form-control" required
                                                            placeholder="Masukkan password saat ini">
                                                        <button class="btn btn-outline-secondary password-toggle-btn"
                                                            type="button" data-target="#inputCurrentPassword">
                                                            <i class="ri-eye-line"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold fs-13">Password Baru <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light text-muted"><i
                                                                class="ri-lock-2-line"></i></span>
                                                        <input type="password" name="password" id="inputNewPassword"
                                                            class="form-control" required minlength="6"
                                                            placeholder="Minimal 6 karakter">
                                                        <button class="btn btn-outline-secondary password-toggle-btn"
                                                            type="button" data-target="#inputNewPassword">
                                                            <i class="ri-eye-line"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="form-label fw-semibold fs-13">Konfirmasi Password Baru
                                                        <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light text-muted"><i
                                                                class="ri-lock-check-line"></i></span>
                                                        <input type="password" name="password_confirmation"
                                                            id="inputConfirmPassword" class="form-control" required
                                                            minlength="6" placeholder="Ulangi password baru">
                                                        <button class="btn btn-outline-secondary password-toggle-btn"
                                                            type="button" data-target="#inputConfirmPassword">
                                                            <i class="ri-eye-line"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="d-grid">
                                                    <button type="submit" id="btnSubmitPassword"
                                                        class="btn btn-success py-2 fw-semibold">
                                                        <i class="ri-shield-keyhole-line me-1"></i> Perbarui Password
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    {{-- SweetAlert2 JS --}}
    <script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            // ── 1. Live Preview Saat Pilih Foto Profil ──
            $('#profilePhotoInput').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Ukuran Terlalu Besar',
                            text: 'Ukuran foto maksimal adalah 2MB.',
                            confirmButtonColor: '#0d6efd'
                        });
                        $(this).val('');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        $('#previewAvatarImg').attr('src', evt.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // ── 2. Toggle Visibility Password (Icon Mata) ──
            $('.password-toggle-btn').on('click', function() {
                const targetSelector = $(this).data('target');
                const $input = $(targetSelector);
                const $icon = $(this).find('i');

                if ($input.attr('type') === 'password') {
                    $input.attr('type', 'text');
                    $icon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
                } else {
                    $input.attr('type', 'password');
                    $icon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
                }
            });

            // ── 3. Submit Form Update Profil via AJAX ──
            $('#formUpdateProfile').on('submit', function(e) {
                e.preventDefault();

                const $btn = $('#btnSubmitProfile');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('profile.update') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(res) {
                        $btn.prop('disabled', false).html(origHtml);

                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Update header teks profil di halaman
                            if (res.user) {
                                $('#headerDisplayFullName').text(res.user.fullname || res.user
                                    .username);
                                $('#headerDisplayUsername').text(res.user.username);
                                $('.user-name-text').text(res.user.username);
                            }

                            // Update avatar di topbar secara real-time
                            if (res.image_url) {
                                const freshUrl = res.image_url + (res.image_url.includes('?') ?
                                    '&' : '?') + 't=' + new Date().getTime();
                                $('.profil-avt').attr('src', freshUrl);
                                $('#previewAvatarImg').attr('src', freshUrl);
                            }
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(origHtml);

                        let errMsg = 'Terjadi kesalahan saat menyimpan profil.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errs = xhr.responseJSON.errors;
                            const firstKey = Object.keys(errs)[0];
                            errMsg = errs[firstKey][0];
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            text: errMsg,
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                });
            });

            // ── 4. Submit Form Ganti Password via AJAX ──
            $('#formUpdatePassword').on('submit', function(e) {
                e.preventDefault();

                const newPass = $('#inputNewPassword').val();
                const confirmPass = $('#inputConfirmPassword').val();

                if (newPass !== confirmPass) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Password Tidak Cocok',
                        text: 'Konfirmasi password baru tidak cocok dengan password baru.',
                        confirmButtonColor: '#0d6efd'
                    });
                    return;
                }

                const $btn = $('#btnSubmitPassword');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

                $.ajax({
                    url: '{{ route('profile.password.update') }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(res) {
                        $btn.prop('disabled', false).html(origHtml);

                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Password Diperbarui!',
                                text: res.message,
                                confirmButtonColor: '#0d6efd'
                            });

                            $('#formUpdatePassword')[0].reset();
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(origHtml);

                        let errMsg = 'Terjadi kesalahan saat memperbarui password.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errs = xhr.responseJSON.errors;
                            const firstKey = Object.keys(errs)[0];
                            errMsg = errs[firstKey][0];
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Ganti Password',
                            text: errMsg,
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                });
            });

        });
    </script>
@endsection
