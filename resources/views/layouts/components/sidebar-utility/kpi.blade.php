@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;
@endphp

@if (
    $jabatan === 'dept_head' ||
        $jabatan === 'supervisor' ||
        ($jabatan === 'operator' && in_array($bagian, ['Engineering', 'Engineering WWTP'])) ||
        ($jabatan === 'foreman' && in_array($bagian, ['Engineering', 'Engineering WWTP'])))
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->is('kpi/*') ? '' : 'collapsed' }}" href="#sidebarKPI"
            data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('kpi/*') ? 'true' : 'false' }}"
            aria-controls="sidebarKPI">
            <i class="mdi mdi-target"></i>
            <span data-key="t-dashboards">KPI</span>
        </a>

        <div class="collapse menu-dropdown {{ request()->is('kpi/*') ? 'show' : '' }}" id="sidebarKPI">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('kpi/form') ? 'active' : '' }}" href="{{ url('kpi/form') }}">
                        <i class="mdi mdi-chart-line"></i>
                        <span data-key="t-widgets">Form Kpi</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('kpi/data') ? 'active' : '' }}" href="{{ url('kpi/data') }}">
                        <i class="mdi mdi-chart-box"></i>
                        <span data-key="t-widgets">Data Kpi</span>
                    </a>
                </li>

            </ul>
        </div>
    </li>
@endif
