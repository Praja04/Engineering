<div class="app-menu navbar-menu">
    @php
        $jabatan = Auth::user()->jabatan;
        $bagian = Auth::user()->bagian;
        $departement = Auth::user()->departemen;
    @endphp

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ route('home') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo/kecap.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo/kecap.png') }}" alt="" height="100">
            </span>
        </a>
        <a href="{{ route('home') }}" class="logo logo-light">
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

    <div id="scrollbar" class="p-3">
        <div class="container-fluid">
            <div id="two-column-menu"></div>

            <ul class="navbar-nav" id="navbar-nav">
                <!-- /////////////////////Dashboard/////////////// -->
                @if ($departement === 'engineering' || $departement === 'IT')
                    <li class="menu-title"><span data-key="t-dashboard">Dashboard</span></li>

                    @include('layouts.components.sidebar-scoring.dashboard-scoring')
                    @include('layouts.components.sidebar-boiler.dashboard')
                    @include('layouts.components.sidebar-utility.dashboard')
                    @include('layouts.components.sidebar-maintenance.dashboard-maintenance')
                    @include('layouts.components.sidebar-ejo.dashboard-ejo')
                    @include('layouts.components.sidebar-epr.dashboard-epr')
                    @include('layouts.components.sidebar-operasional.dashboard')
                @endif


                @if ($departement === 'engineering' || $departement === 'IT')

                    <!-- /////////////////////menu/////////////// -->
                    <li class="menu-title"><span data-key="t-menu">Engineering Menu</span></li>
                    @include('layouts.components.sidebar-utility.kpi')
                    @include('layouts.components.sidebar-utility.utility')
                    @include('layouts.components.sidebar-epr.menu-epr')
                    @include('layouts.components.sidebar-utility.wwtp')
                    @include('layouts.components.sidebar-boiler.menu-boiler')
                    @include('layouts.components.sidebar-kalibrasi.menu-kalibrasi')
                    @include('layouts.components.sidebar-scoring.menu-scoring-input')
                    @include('layouts.components.sidebar-maintenance.menu-maintenance')
                    @include('layouts.components.sidebar-ejo.menu-ejo')


                    <!-- /////////////////////Data Master/////////////// -->
                    @if (in_array($jabatan, ['admin', 'dept_head', 'supervisor', 'foreman']))
                        <li class="menu-title"><span data-key="t-menu">Data Master</span></li>

                        @include('layouts.components.sidebar-kalibrasi.data-master')
                        @include('layouts.components.sidebar-scoring.menu-scoring-master')
                        @include('layouts.components.sidebar-maintenance.master-maintenance')
                        @include('layouts.components.sidebar-epr.master-epr')
                    @endif

                    <!-- /////////////////////Manage User/////////////// -->
                    @if (in_array($jabatan, ['admin', 'dept_head', 'foreman', 'supervisor']))
                        <li class="nav-item">
                            <a href="{{ url('users/index') }}"
                                class="nav-link menu-link  {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="mdi mdi-folder-account"></i> <span data-key="t-tkbm">Manage User</span>
                            </a>
                        </li>
                    @endif
                @else
                    <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                    @include('layouts.components.sidebar-dept.dept_lain')
                @endif
            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>

<div class="vertical-overlay"></div>
