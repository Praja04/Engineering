@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;
@endphp

@if ($jabatan != 'operator' && in_array($bagian, ['IT', 'Engineering', 'Engineering Utility', 'Engineering WWTP']))
    <li class="nav-item">
        <a class="nav-link menu-link
            {{ request()->routeIs('dashboard.boiler.*') || request()->Is('utility/dashboard/listrik') || request()->Is('utility/dashboard/air') || request()->Is('utility/dashboard/chemical') || request()->Is('utility/dashboard/machine') || request()->is('utility/project/dashboard*') ? '' : 'collapsed' }}"
            href="#sidebarDashboardBoiler" data-bs-toggle="collapse" role="button"
            aria-expanded="{{ request()->routeIs('dashboard.boiler.*') || request()->Is('utility/dashboard/listrik') || request()->Is('utility/dashboard/air') || request()->Is('utility/dashboard/chemical') || request()->Is('utility/dashboard/machine') || request()->is('utility/project/dashboard*') ? 'true' : 'false' }}"
            aria-controls="sidebarDashboardBoiler">
            <i class="mdi mdi-cog"></i>
            <span data-key="t-dashboards">Dashboard Utility & Boiler</span>
        </a>
        <div class="collapse menu-dropdown
            {{ request()->routeIs('dashboard.boiler.*') || request()->Is('utility/dashboard/listrik') || request()->Is('utility/dashboard/air') || request()->Is('utility/dashboard/chemical') || request()->Is('utility/dashboard/machine') || request()->is('utility/project/dashboard*') ? 'show' : '' }}"
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
                <li class="nav-item">
                    <a href="{{ url('utility/dashboard/listrik') }}"
                        class="nav-link {{ request()->Is('utility/dashboard/listrik') ? 'active' : '' }}"
                        data-key="t-analytics">
                        <i class="mdi mdi-chart-bar me-2"></i> Pemakaian Listrik
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('utility/dashboard/air') }}"
                        class="nav-link {{ request()->Is('utility/dashboard/air') ? 'active' : '' }}"
                        data-key="t-analytics">
                        <i class="mdi mdi-chart-bar me-2"></i> Pemakaian Air
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('utility/dashboard/chemical') }}"
                        class="nav-link {{ request()->Is('utility/dashboard/chemical') ? 'active' : '' }}"
                        data-key="t-analytics">
                        <i class="mdi mdi-chart-bar me-2"></i> Pemakaian chemical
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('dashboard/engineering-utility') }}"
                        class="nav-link {{ request()->Is('dashboard/engineering-utility') ? 'active' : '' }}"
                        data-key="t-analytics">
                        <i class="mdi mdi-chart-bar me-2"></i> Engineering Utility Dashboard </a>
                </li>
                @if ($jabatan === 'admin')
                    <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link text-warning fw-semibold" data-bs-toggle="modal"
                            data-bs-target="#modalFilterPptxWeekly" data-key="t-pptx-weekly">
                            <i class="mdi mdi-file-powerpoint-box text-warning me-2 fs-15"></i> PPTX Weekly Utility
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{ route('utility.slide_editor') }}"
                            class="nav-link text-info fw-semibold {{ request()->routeIs('utility.slide_editor') ? 'active' : '' }}"
                            data-key="t-slide-editor">
                            <i class="mdi mdi-presentation text-info me-2 fs-15"></i> Slide Studio (Canva)
                        </a>
                    </li> --}}
                @endif
            </ul>
        </div>
    </li>

    @include('layouts.components.modal-pptx-weekly')
@endif
