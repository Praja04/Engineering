@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;
@endphp

@if (
    $jabatan === 'admin' ||
        $jabatan === 'dept_head' ||
        $jabatan === 'supervisor' ||
        ($jabatan === 'operator' && $bagian === 'Engineering WWTP') ||
        ($jabatan === 'foreman' && $bagian === 'Engineering WWTP'))
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->is('dashboard/wwtp/*') ? '' : 'collapsed' }}"
            href="#sidebarUtilityDashboard" data-bs-toggle="collapse" role="button" aria-expanded="false"
            aria-controls="sidebarUtilityDashboard">
            <i class="mdi mdi-water"></i>
            <span data-key="t-dashboards">Dashboard WWTP</span>
        </a>
        <div class="collapse menu-dropdown {{ request()->is('dashboard/wwtp/*') ? 'show' : '' }}"
            id="sidebarUtilityDashboard">
            <ul class="nav nav-sm flex-column">
                <!-- <li class="nav-item">
                <a href="{{ url('utility/dashboard') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-chart-bar me-2"></i> Dashboard Utility
                </a>
            </li> -->
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('dashboard/wwtp/proses') ? 'active' : '' }}"
                        href="{{ url('dashboard/wwtp/proses') }}">
                        <i class="mdi mdi-chart-bar me-2"></i> <span data-key="t-widgets1">Proses WWTP</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('dashboard/wwtp/performance') ? 'active' : '' }}"
                        href="{{ url('dashboard/wwtp/performance') }}">
                        <i class="mdi mdi-chart-bar me-2"></i> <span data-key="t-widgets1">Performance WWTP</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('dashboard/wwtp/sludge') ? 'active' : '' }}"
                        href="{{ url('dashboard/wwtp/sludge') }}">
                        <i class="mdi mdi-chart-bar me-2"></i> <span data-key="t-widgets1">Sludge WWTP</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('dashboard/wwtp/visualisasi') ? 'active' : '' }}"
                        href="{{ url('dashboard/wwtp/visualisasi') }}">
                        <i class="mdi mdi-chart-bar me-2"></i> <span data-key="t-widgets2">Flow Proses WWTP</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('dashboard/wwtp/wco') ? 'active' : '' }}"
                        href="{{ url('dashboard/wwtp/wco') }}">
                        <i class="mdi mdi-shield-check me-2"></i> <span data-key="t-widgets3">WCO - SHE Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a href="{{ route('utility.project.dashboard') }}"
            class="nav-link menu-link {{ request()->routeIs('utility.project.dashboard') ? 'active' : '' }}"
            data-key="t-analytics">
            <i class="ri-dashboard-2-line"></i>Project Utility
        </a>
    </li>
@endif
