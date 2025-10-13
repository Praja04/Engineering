@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
@endphp

@if (
($jabatan === 'dept_head') ||
($jabatan === 'supervisor') ||
($jabatan === 'foreman' && $bagian === 'Engineering Kalibrasi')|| ($jabatan === 'operator' && $bagian === 'Engineering Kalibrasi')
)

<li class="nav-item">
    <a class="nav-link menu-link {{ request()->routeIs('kalibrasi.*') ? '' : 'collapsed' }}" href="#sideBarPressure" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('kalibrasi.*') ? 'true' : 'false' }}" aria-controls="sideBarPressure">
        <i class="mdi mdi-ruler-square"></i> <span data-key="t-kalibrasi">Kalibrasi</span>
    </a>
    <div class="collapse menu-dropdown {{ request()->routeIs('kalibrasi.*') ? 'show' : '' }}" id="sideBarPressure">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="#" data-bs-target="#sidebarPressure" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('kalibrasi.pressure.*') ? 'true' : 'false' }}" aria-controls="sidebarPressure" class="nav-link" data-key="t-m-tkbm">
                    <i class="mdi mdi-gauge"></i>Pressure
                </a>
                <div class="collapse menu-dropdown {{ request()->routeIs('kalibrasi.pressure.*') ? 'show' : '' }}" id="sidebarPressure">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('kalibrasi.pressure.index') }}" class="nav-link {{ request()->routeIs('kalibrasi.pressure.index') ? 'active' : '' }}" data-key="t-input-p2h">
                                Form Pressure</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('kalibrasi.pressure.data') }}" class="nav-link {{ request()->routeIs('kalibrasi.pressure.data') ? 'active' : '' }}" data-key="t-chat">
                                Data Pressure </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a href="{{ route('kalibrasi.schedule') }}" class="nav-link {{ request()->routeIs('kalibrasi.schedule') ? 'active' : '' }}" data-key="t-tkbm">
                    <i class="mdi mdi-calendar"></i>Schedule</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('kalibrasi.certificate') }}" class="nav-link {{ request()->routeIs('kalibrasi.certificate') ? 'active' : '' }}" data-key="t-tkbm">
                    <i class="mdi mdi-certificate"></i>Cetificate</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('kalibrasi.certificate.approvals') }}" class="nav-link {{ request()->routeIs(['kalibrasi.certificate.approvals', 'kalibrasi.certificate.approval.detail']) ? 'active' : '' }}" data-key="t-tkbm">
                    <i class="mdi mdi-check-decagram"></i>Approval</a>
            </li>
        </ul>
    </div>
</li>
@endif