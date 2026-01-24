@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;
@endphp

@if (in_array($jabatan, ['operator', 'foreman', 'supervisor', 'dept_head']) &&
        in_array($bagian, ['Engineering', 'Engineering Utility']))
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->routeIs('mtc.*') ? '' : 'collapsed' }}" href="#sidebarMtc"
            data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('mtc.*') ? 'true' : 'false' }}"
            aria-controls="sidebarMtc">
            <i class="mdi mdi-account-wrench"></i> <span data-key="t-mtc">Maintenance</span>
        </a>
        <div class="collapse menu-dropdown {{ request()->routeIs('mtc.*') ? 'show' : '' }}" id="sidebarMtc">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a href="{{ route('mtc.motor-pump.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.motor-pump.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Form Mtc Motor Pump</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.motor-pump.data.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.motor-pump.data.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Data Mtc Motor Pump</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.utility.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.utility.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Form Mtc Utility</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.utility.data.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.utility.data.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Data Mtc Utility</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.electrical.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.electrical.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Form Mtc Electrical</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.electrical.data.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.electrical.data.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Data Mtc Electrical</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.refrigerasi.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.refrigerasi.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Form Mtc Refrigerasi</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.refrigerasi.data.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.refrigerasi.data.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Data Mtc Refrigerasi</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.electric-engine.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.electric-engine.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Form Mtc Electric Engine</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.electric-engine.data.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.electric-engine.data.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Data Mtc Electric Engine</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.diesel-engine.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.diesel-engine.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Form Mtc Diesel Engine</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.diesel-engine.data.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.diesel-engine.data.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Data Mtc Diesel Engine</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.sipil.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.sipil.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Form Mtc Sipil</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mtc.sipil.data.index') }}"
                        class="nav-link {{ request()->routeIs('mtc.sipil.data.index') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Data Mtc Sipil</a>
                </li>
            </ul>
        </div>
    </li>
@endif
