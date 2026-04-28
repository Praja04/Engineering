@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
@endphp

@if ($jabatan === 'admin' ||in_array($jabatan, ['operator', 'foreman', 'supervisor', 'dept_head']) &&
in_array($bagian, ['Engineering', 'Engineering Kalibrasi']))
<li class="nav-item">
    <a class="nav-link menu-link {{ request()->routeIs('kalibrasi.*') ? '' : 'collapsed' }}" href="#sideBarPressure"
        data-bs-toggle="collapse" role="button"
        aria-expanded="{{ request()->routeIs('kalibrasi.*') ? 'true' : 'false' }}" aria-controls="sideBarPressure">
        <i class="mdi mdi-ruler-square"></i> <span data-key="t-kalibrasi">Kalibrasi</span>
    </a>
    <div class="collapse menu-dropdown {{ request()->routeIs('kalibrasi.*') ? 'show' : '' }}" id="sideBarPressure">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ route('kalibrasi.form.dashboard') }}"
                    class="nav-link {{ request()->routeIs('kalibrasi.form*') ? 'active' : '' }}">
                    <i class="mdi mdi-file-document"></i>Form Kalibrasi</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('kalibrasi.data.dashboard') }}"
                    class="nav-link {{ request()->routeIs('kalibrasi.data.*') ? 'active' : '' }}" data-key="t-tkbm">
                    <i class="mdi mdi-book-open"></i>Data Kalibrasi</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('kalibrasi.schedule') }}"
                    class="nav-link {{ request()->routeIs('kalibrasi.schedule') ? 'active' : '' }}"
                    data-key="t-tkbm">
                    <i class="mdi mdi-calendar"></i>Schedule</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('kalibrasi.certificate') }}"
                    class="nav-link {{ request()->routeIs('kalibrasi.certificate') ? 'active' : '' }}"
                    data-key="t-tkbm">
                    <i class="mdi mdi-certificate"></i>Cetificate</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('kalibrasi.sticker') }}"
                    class="nav-link {{ request()->routeIs('kalibrasi.sticker') ? 'active' : '' }}"
                    data-key="t-tkbm">
                    <i class="mdi mdi-sticker"></i>Sticker</a>
            </li>
            @if ($jabatan != 'operator')
            <li class="nav-item">
                <a href="{{ route('kalibrasi.certificate.approvals') }}"
                    class="nav-link {{ request()->routeIs(['kalibrasi.certificate.approvals', 'kalibrasi.certificate.approval.detail']) ? 'active' : '' }}"
                    data-key="t-tkbm">
                    <i class="mdi mdi-check-decagram"></i>Approval</a>
            </li>
            @endif
        </ul>
    </div>
</li>
@endif