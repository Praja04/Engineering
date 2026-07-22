@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
$isEpr = request()->is('epr/*');
@endphp

@if (
($jabatan === 'admin') ||
($jabatan === 'dept_head') ||
($jabatan === 'supervisor' && $bagian === 'Engineering Produksi (EPR)') ||
($jabatan === 'operator' && $bagian === 'Engineering Produksi (EPR)') ||
($jabatan === 'foreman' && $bagian === 'Engineering Produksi (EPR)'))
<li class="nav-item">
    <a class="nav-link menu-link {{ $isEpr ? '' : 'collapsed' }}" href="#sidebarEPR"
        data-bs-toggle="collapse" role="button" aria-expanded="{{ $isEpr ? 'true' : 'false' }}"
        aria-controls="sidebarEPR">
        <i class="mdi mdi-file-document-multiple-outline"></i>
        <span>EPR</span>
    </a>

    <div class="collapse menu-dropdown {{ $isEpr ? 'show' : '' }}" id="sidebarEPR">
        <ul class="nav nav-sm flex-column">

            {{-- PM (Predictive Maintenance) Submenu --}}
            @php
                $isPmActive = request()->is('epr/predictive-maintenance*') || request()->is('epr/work-orders*');
            @endphp
            <li class="nav-item">
                <a href="#sidebarPm" data-bs-toggle="collapse" role="button" 
                    aria-expanded="{{ $isPmActive ? 'true' : 'false' }}" 
                    aria-controls="sidebarPm" class="nav-link {{ $isPmActive ? 'active' : '' }}">
                    <i class="ri-loader-4-line me-2"></i><span>PM (Predictive)</span>
                </a>
                <div class="collapse menu-dropdown {{ $isPmActive ? 'show' : '' }}" id="sidebarPm">
                    <ul class="nav nav-sm flex-column">
                        @if(in_array($jabatan, ['operator', 'foreman', 'supervisor', 'dept_head', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('epr/predictive-maintenance/form') ? 'active' : '' }}" href="{{ route('epr.pm.form') }}">
                                <i class="ri-edit-box-line me-2"></i><span>Predictive Form</span>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('epr/predictive-maintenance/data') ? 'active' : '' }}" href="{{ route('epr.pm.data') }}">
                                <i class="ri-database-2-line me-2"></i><span>Predictive Data</span>
                            </a>
                        </li>
                        {{-- Management WO: Foreman, Supervisor, Dept Head, Admin --}}
                        @if(in_array($jabatan, ['foreman', 'supervisor', 'dept_head', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('epr/work-orders') && !request()->is('epr/work-orders/approval') ? 'active' : '' }}" href="{{ route('epr.wo.index') }}">
                                <i class="ri-task-line me-2"></i>
                                <span>Management WO</span>
                            </a>
                        </li>
                        @endif
                        {{-- Approval WO: Supervisor, Dept Head, Admin --}}
                        @if(in_array($jabatan, ['supervisor', 'dept_head', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('epr/work-orders/approval') ? 'active' : '' }}" href="{{ route('epr.wo.approval') }}">
                                <i class="ri-checkbox-circle-line me-2"></i>
                                <span>Approval WO</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>

            {{-- CM (Corrective Maintenance) Submenu --}}
            <li class="nav-item">
                <a href="#sidebarCm" data-bs-toggle="collapse" role="button" 
                    aria-expanded="{{ request()->is('epr/corrective-maintenance*') ? 'true' : 'false' }}" 
                    aria-controls="sidebarCm" class="nav-link {{ request()->is('epr/corrective-maintenance*') ? 'active' : '' }}">
                    <i class="ri-tools-line me-2"></i><span>CM (Corrective)</span>
                </a>
                <div class="collapse menu-dropdown {{ request()->is('epr/corrective-maintenance*') ? 'show' : '' }}" id="sidebarCm">
                    <ul class="nav nav-sm flex-column">
                        @if(in_array($jabatan, ['operator', 'foreman', 'supervisor', 'dept_head', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('epr/corrective-maintenance/form') ? 'active' : '' }}" href="{{ route('epr.cm.form') }}">
                                <i class="ri-edit-box-line me-2"></i><span>Corrective Form</span>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('epr/corrective-maintenance/data') ? 'active' : '' }}" href="{{ route('epr.cm.data') }}">
                                <i class="ri-database-2-line me-2"></i><span>Corrective Data</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

        </ul>
    </div>
</li>
@endif
