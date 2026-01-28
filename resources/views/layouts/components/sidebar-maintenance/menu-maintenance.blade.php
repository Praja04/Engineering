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
                    <a href="#" data-bs-target="#sidebarFormMtc" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ request()->is('mtc/form/*') ? 'true' : 'false' }}"
                        aria-controls="sidebarFormMtc" class="nav-link" data-key="t-m-mtc">
                        <i class="mdi mdi-view-grid"></i>Form Mtc
                    </a>
                    <div class="collapse menu-dropdown {{ request()->is('mtc/form/*') ? 'show' : '' }}"
                        id="sidebarFormMtc">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('mtc.motor-pump.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.motor-pump.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Form Motor Pump</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.utility.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.utility.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Form Utility</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.electrical.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.electrical.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Form Electrical</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.refrigerasi.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.refrigerasi.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Form Refrigerasi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.electric-engine.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.electric-engine.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Form Electric Engine</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.diesel-engine.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.diesel-engine.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Form Diesel Engine</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.sipil.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.sipil.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Form Sipil</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.battery.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.battery.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Form Battery</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.electric-p2h.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.electric-p2h.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Form Electric P2H</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.diesel-p2h.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.diesel-p2h.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Form Diesel P2H</a>
                            </li>
                        </ul>
                    </div>

                    <a href="#" data-bs-target="#sidebarDataMtc" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ request()->is('mtc/data/*') ? 'true' : 'false' }}"
                        aria-controls="sidebarDataMtc" class="nav-link" data-key="t-m-mtc">
                        <i class="mdi mdi-view-grid"></i>Data Mtc
                    </a>
                    <div class="collapse menu-dropdown {{ request()->is('mtc/data/*') ? 'show' : '' }}"
                        id="sidebarDataMtc">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('mtc.motor-pump.data.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.motor-pump.data.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Data Motor Pump</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.utility.data.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.utility.data.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Data Utility</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.electrical.data.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.electrical.data.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Data Electrical</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.refrigerasi.data.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.refrigerasi.data.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Data Refrigerasi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.electric-engine.data.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.electric-engine.data.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Data Electric Engine</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.diesel-engine.data.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.diesel-engine.data.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Data Diesel Engine</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.sipil.data.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.sipil.data.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Data Sipil</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.battery.data.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.battery.data.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Data Battery</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.electric-p2h.data.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.electric-p2h.data.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Data Electric P2H</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mtc.diesel-p2h.data.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.diesel-p2h.data.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Data Diesel P2H</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </li>
@endif
