@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;
@endphp

@if ($jabatan != 'operator' && $bagian == 'Engineering')
    <li class="nav-item">
        <a class="nav-link menu-link" href="#sidebarDashboardBoiler" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="sidebarDashboardBoiler">
            <i class="mdi mdi-cog"></i>
            <span data-key="t-dashboards">Dashboard Boiler</span>
        </a>
        <div class="collapse menu-dropdown" id="sidebarDashboardBoiler">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a href="{{ route('boiler.dashboard') }}" class="nav-link" data-key="t-analytics">
                        <i class="mdi mdi-chart-line me-2"></i> Dashboard Realtime
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('dashboard/mesin/downtime') }}" class="nav-link" data-key="t-analytics">
                        <i class="mdi mdi-bullseye-arrow me-2"></i> Dashboard KPI
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endif
