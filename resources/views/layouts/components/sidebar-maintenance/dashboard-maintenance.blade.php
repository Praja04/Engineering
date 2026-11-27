@can('permission', 'mtc_dashboard')
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->is('mtc/dashboard/*') || request()->is('mtc/dashboard/agenda') ? '' : 'collapsed' }}"
            href="#sidebarMtcDashboard" data-bs-toggle="collapse" role="button"
            aria-expanded="{{ request()->is('mtc/dashboard/*') || request()->is('mtc/dashboard/agenda') ? 'true' : 'false' }}"
            aria-controls="sidebarMtcDashboard">
            <i class="mdi mdi-toolbox"></i>
            <span data-key="t-dashboards">Dashboard MTC</span>
        </a>
        <div class="collapse menu-dropdown {{ request()->is('mtc/dashboard/*') || request()->is('mtc/dashboard/agenda') ? 'show' : '' }}"
            id="sidebarMtcDashboard">
            <ul class="nav nav-sm flex-column">
                @can('permission', 'mtc_performance_dashboard')
                    <li class="nav-item">
                        <a href="{{ route('mtc.dashboard.index') }}"
                            class="nav-link {{ request()->routeIs('mtc.dashboard.index') ? 'active' : '' }}"
                            data-key="t-maintenance-dashboard">
                            <i class="mdi mdi-view-dashboard-outline me-2"></i> Maintenance Dashboard
                        </a>
                    </li>
                @endcan
                @can('permission', 'mtc_agenda_dashboard')
                    <li class="nav-item">
                        <a href="{{ url('mtc/dashboard/agenda') }}"
                            class="nav-link {{ request()->is('mtc/dashboard/agenda') ? 'active' : '' }}" data-key="t-analytics">
                            <i class="mdi mdi-chart-bar me-2"></i> Agenda
                        </a>
                    </li>
                @endcan
                @can('permission', 'mtc_machine_ledger_dashboard')
                    <li class="nav-item">
                        <a href="{{ route('mtc.dashboard.material') }}"
                            class="nav-link {{ request()->routeIs('mtc.dashboard.material') ? 'active' : '' }}"
                            data-key="t-machine-ledger">
                            <i class="mdi mdi-file-cog-outline me-2"></i> Machine Ledger
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </li>
@endcan
