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
        <a class="nav-link menu-link" href="#sidebarOperasionalDashboard" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="sidebarOperasionalDashboard">
            <i class="mdi mdi-wrench"></i>
            <span data-key="t-dashboards">Dashboard <br> Operasional</span>
        </a>
        <div class="collapse menu-dropdown" id="sidebarOperasionalDashboard">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a href="http://10.11.11.200:3000" target="_blank" class="nav-link" data-key="t-analytics">
                        <i class="mdi mdi-chart-bar me-2"></i> Dashboard Capacitor Bank
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('capacitor-bank.report') }}"
                        class="nav-link {{ request()->routeIs('capacitor-bank.report') ? 'active' : '' }}"
                        data-key="t-materials">
                        <i class="mdi mdi-cube-outline me-2"></i> Report Capacitor Bank
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endif
