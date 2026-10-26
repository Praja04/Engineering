@php
    $jabatan = Auth::user()->jabatan ?? '';
    $bagian = Auth::user()->bagian ?? '';
    $isProjectActive = request()->is('utility/project*') && !request()->routeIs('utility.project.dashboard');
@endphp

@if (
    $jabatan === 'admin' ||
        $jabatan === 'dept_head' ||
        $jabatan === 'supervisor' ||
        ($jabatan === 'foreman' && in_array($bagian, ['Engineering', 'Engineering WWTP'])))
    {{-- ($jabatan === 'operator' && in_array($bagian, ['Engineering', 'Engineering WWTP'])) || --}}
    <li class="nav-item">
        <a class="nav-link menu-link {{ $isProjectActive ? '' : 'collapsed' }}" href="#sidebarUtilityProject"
            data-bs-toggle="collapse" role="button" aria-expanded="{{ $isProjectActive ? 'true' : 'false' }}"
            aria-controls="sidebarUtilityProject">
            <i class="mdi mdi-clipboard-check-multiple-outline"></i>
            <span data-key="t-dashboards">Project Eng Utility</span>
        </a>

        <div class="collapse menu-dropdown {{ $isProjectActive ? 'show' : '' }}" id="sidebarUtilityProject">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('utility.project.form') || request()->routeIs('utility.project.create') ? 'active' : '' }}"
                        href="{{ route('utility.project.form') }}">
                        <i class="mdi mdi-file-document-edit-outline me-2"></i>
                        <span data-key="t-widgets">Form Project</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('utility.project.data') || (request()->is('utility/project') && !request()->routeIs('utility.project.dashboard')) ? 'active' : '' }}"
                        href="{{ route('utility.project.data') }}">
                        <i class="mdi mdi-database-eye-outline me-2"></i>
                        <span data-key="t-widgets">Data Project</span>
                    </a>
                </li>

                @if ($jabatan != 'operator')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('utility.project.master*') ? 'active' : '' }}"
                            href="{{ route('utility.project.master') }}">
                            <i class="mdi mdi-database-cog-outline me-2"></i>
                            <span data-key="t-widgets">Master Project</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </li>
@endif
