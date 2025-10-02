<div class="app-menu navbar-menu">
    @php
        $jabatan = Auth::user()->jabatan;
        $bagian = Auth::user()->bagian;
    @endphp

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <div class="navbar-brand-box ">
            <a href="{{ $jabatan != 'operator' ? route('dashboard') : route('dashboard') }}" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="{{ asset('assets/images/logo/kecap.png') }}" alt="" height="22">
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('assets/images/logo/kecap.png') }}" alt="" height="100">
                </span>
            </a>
            <a href="{{ $jabatan != 'operator' ? route('dashboard') : route('dashboard') }}" class="logo logo-light">
                <span class="logo-sm">
                    <img src="{{ asset('assets/images/logo/kecap.png') }}" alt="" height="22">
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('assets/images/logo/kecap.png') }}" alt="" height="100">
                </span>
            </a>
            <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                id="vertical-hover">
                <i class="ri-record-circle-line"></i>
            </button>
        </div>
    </div>
    {{-- </div> --}}

    <div id="scrollbar" class="p-3">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            @if (in_array($jabatan, ['dept_head', 'foreman', 'operator', 'supervisor']))
                <ul class="navbar-nav" id="navbar-nav">
                    @if ($jabatan !== 'operator')
                        {{-- <li class="menu-title"><span data-key="t-menu">Dashboard</span></li>
                        <li class="nav-item">
                            <a href="#" class="nav-link menu-link">
                                <i class="mdi mdi-chart-box"></i> <span data-key="p2h-dashboard">P2H
                                    Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link menu-link">
                                <i class="mdi mdi-account-hard-hat"></i> <span data-key="tkbm-dashboard">TKBM
                                    Dashboard</span>
                            </a>
                        </li> --}}
                    @endif
                    <li class="menu-title"><span data-key="t-menu">Engineering Menu</span></li>
                    {{-- <li class="nav-item">
                        <a class="nav-link menu-link  {{ request()->routeIs('tkbm.*') ? '' : 'collapsed' }}"
                            href="#sideBarTkbm" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('tkbm.*') ? 'true' : 'false' }}"
                            aria-controls="sideBarTkbm">
                            <i class="mdi mdi-human-dolly"></i> <span data-key="t-tkbm">TKBM</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->routeIs('tkbm.*') ? 'show' : '' }}"
                            id="sideBarTkbm">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('tkbm.stock') }}"
                                        class="nav-link {{ request()->routeIs('tkbm.stock') ? 'active' : '' }}"
                                        data-key="t-input-tkbm">
                                        Form BPS </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('tkbm.data') }}"
                                        class="nav-link {{ request()->routeIs('tkbm.data') ? 'active' : '' }}"
                                        data-key="t-tkbm">
                                        Data TKBM </a>
                                </li>
                                @if (Session::get('jabatan') !== 'operator')
                                    <li class="nav-item">
                                        <a href="{{ route('tkbm.master.fee') }}"
                                            class="nav-link {{ request()->routeIs('tkbm.master.fee') ? 'active' : '' }}"
                                            data-key="t-input-tkbm">
                                            Manage Fees & Harga </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('kalibrasi.*') ? '' : 'collapsed' }}"
                            href="#sideBarPressure" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('kalibrasi.*') ? 'true' : 'false' }}"
                            aria-controls="sideBarPressure">
                            <i class="mdi mdi-ruler-square"></i> <span data-key="t-kalibrasi">Kalibrasi</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->routeIs('kalibrasi.*') ? 'show' : '' }}"
                            id="sideBarPressure">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="#" data-bs-target="#sidebarPressure" data-bs-toggle="collapse"
                                        role="button"
                                        aria-expanded="{{ request()->routeIs('kalibrasi.pressure.*') ? 'true' : 'false' }}"
                                        aria-controls="sidebarPressure" class="nav-link" data-key="t-m-tkbm">
                                        <i class="mdi mdi-gauge"></i>Pressure
                                    </a>
                                    <div class="collapse menu-dropdown {{ request()->routeIs('kalibrasi.pressure.*') ? 'show' : '' }}"
                                        id="sidebarPressure">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="{{ route('kalibrasi.pressure.index') }}"
                                                    class="nav-link {{ request()->routeIs('kalibrasi.pressure.index') ? 'active' : '' }}"
                                                    data-key="t-input-p2h">
                                                    Form Pressure</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('kalibrasi.pressure.data') }}"
                                                    class="nav-link {{ request()->routeIs('kalibrasi.pressure.data') ? 'active' : '' }}"
                                                    data-key="t-chat">
                                                    Data Pressure </a>
                                            </li>
                                        </ul>
                                    </div>
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
                            </ul>
                        </div>
                    </li>
                    @if ($jabatan !== 'operator')
                        <li class="menu-title"><span data-key="t-menu">Data Master</span></li>
                        <li class="nav-item">
                            <a href="{{ route('master.alat') }}"
                                class="nav-link menu-link {{ request()->routeIs('master.alat') ? 'active' : '' }}">
                                <i class="mdi mdi-book-cog"></i> <span data-key="t-albras">Alat Kalibrasi</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link menu-link ">
                                <i class="mdi mdi-folder-account"></i> <span data-key="t-tkbm">Manage User</span>
                            </a>
                        </li>
                    @endif
                </ul>
            @endif
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>

<div class="vertical-overlay"></div>
