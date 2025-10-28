<div class="app-menu navbar-menu">
    @php
        $jabatan = Auth::user()->jabatan;
        $bagian = Auth::user()->bagian;
    @endphp

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ route('dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo/kecap.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo/kecap.png') }}" alt="" height="100">
            </span>
        </a>
        <a href="{{ route('dashboard') }}" class="logo logo-light">
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
                @if (in_array($jabatan, ['dept_head', 'supervisor', 'foreman']))
                    <li class="menu-title"><span data-key="t-dashboard">Dashboard</span></li>

                    @include('layouts.components.sidebar-scoring.dashboard-scoring')
                    @include('layouts.components.sidebar-boiler.dashboard')
                    @include('layouts.components.sidebar-utility.dashboard')
                @endif


                <!-- /////////////////////menu/////////////// -->
                <li class="menu-title"><span data-key="t-menu">Engineering Menu</span></li>
                @include('layouts.components.sidebar-utility.utility')
                @include('layouts.components.sidebar-kalibrasi.menu-kalibrasi')
                @include('layouts.components.sidebar-scoring.menu-scoring-input')


                <!-- /////////////////////Data Master/////////////// -->
                @if (in_array($jabatan, ['dept_head', 'supervisor', 'foreman']))
                    <li class="menu-title"><span data-key="t-menu">Data Master</span></li>

                    @include('layouts.components.sidebar-kalibrasi.data-master')
                    @include('layouts.components.sidebar-scoring.menu-scoring-master')
                @endif

                <!-- /////////////////////Manage User/////////////// -->
                @if (in_array($jabatan, ['dept_head', 'foreman', 'supervisor']))
                    <li class="nav-item">
                        <a href="{{ url('users/index') }}"
                            class="nav-link menu-link  {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="mdi mdi-folder-account"></i> <span data-key="t-tkbm">Manage User</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>

<div class="vertical-overlay"></div>
