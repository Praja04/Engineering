@can('permission', 'mtc_master')
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->routeIs('master.mtc.*') ? '' : 'collapsed' }}" href="#sidebarMasterMtc"
            data-bs-toggle="collapse" role="button"
            aria-expanded="{{ request()->routeIs('master.mtc.*') ? 'true' : 'false' }}" aria-controls="sidebarMasterMtc">
            <i class="mdi mdi-account-wrench"></i> <span data-key="t-mtc">Master Mtc</span>
        </a>
        <div class="collapse menu-dropdown {{ request()->routeIs('master.mtc.*') ? 'show' : '' }}" id="sidebarMasterMtc">
            <ul class="nav nav-sm flex-column">
                @can('permission', 'mtc_master_mesin')
                    <li class="nav-item">
                        <a href="{{ route('master.mtc.mesin.index') }}"
                            class="nav-link {{ request()->routeIs('master.mtc.mesin.*') ? 'active' : '' }}">
                            <i class="mdi mdi-view-grid"></i>Master Mesin
                        </a>
                    </li>
                @endcan
                @can('permission', 'mtc_master_material')
                    <li class="nav-item">
                        <a href="{{ route('master.mtc.material.index') }}"
                            class="nav-link {{ request()->routeIs('master.mtc.material.*') ? 'active' : '' }}">
                            <i class="mdi mdi-cube-outline"></i>Master Material
                        </a>
                    </li>
                @endcan
                @can('permission', 'mtc_master_checklist')
                    <li class="nav-item">
                        <a href="{{ route('master.mtc.checklist.index') }}"
                            class="nav-link {{ request()->routeIs('master.mtc.checklist.*') ? 'active' : '' }}">
                            <i class="mdi mdi-checkbox-marked-circle-outline"></i>Master Checklist
                        </a>
                    </li>
                @endcan
                @can('permission', 'mtc_master_agenda')
                    <li class="nav-item">
                        <a href="{{ url('mtc/master/agenda/master') }}"
                            class="nav-link {{ request()->is('mtc/master/agenda/master') ? 'active' : '' }}">
                            <i class="mdi mdi-chart-bar"></i>Master Agenda
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </li>
@endcan
