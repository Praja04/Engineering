@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;
@endphp

@if ($jabatan != 'operator' && in_array($bagian, ['Engineering', 'Engineering Utility', 'Engineering WWTP']))
    <li class="nav-item">
        <a class="nav-link menu-link" href="#sidebarDashboardBoiler" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="sidebarDashboardBoiler">
            <i class="mdi mdi-cog"></i>
            <span data-key="t-dashboards">Dashboard Boiler</span>
        </a>
        <div class="collapse menu-dropdown {{ request()->routeIs('dashboard.boiler.*') ? 'show' : '' }}"
            id="sidebarDashboardBoiler">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a href="{{ route('dashboard.boiler.realtime') }}"
                        class="nav-link {{ request()->routeIs('dashboard.boiler.realtime') ? 'active' : '' }}"
                        data-key="t-analytics">
                        <i class="mdi mdi-chart-line me-2"></i> Dashboard Realtime
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('dashboard.boiler.kpi') }}"
                        class="nav-link {{ request()->routeIs('dashboard.boiler.kpi') ? 'active' : '' }}"
                        data-key="t-analytics">
                        <i class="mdi mdi-bullseye-arrow me-2"></i> Dashboard KPI Boiler
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endif
