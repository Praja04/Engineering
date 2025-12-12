@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
@endphp

@if (
$jabatan === 'dept_head' ||
$jabatan === 'supervisor' ||
($jabatan === 'operator' && in_array($bagian, ['Engineering', 'Engineering WWTP'])) ||
($jabatan === 'foreman' && in_array($bagian, ['Engineering', 'Engineering WWTP']))
)
<li class="nav-item">
    <a class="nav-link menu-link {{ request()->routeIs('wwtp.*') ? '' : 'collapsed' }}" href="#sidebarWWTP" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('wwtp.*') ? 'true' : 'false' }}" aria-controls="sidebarWWTP">
        <i class="mdi mdi-water-outline"></i>
        <span data-key="t-dashboards">WWTP</span>
    </a>

    <div class="collapse menu-dropdown {{ request()->routeIs('wwtp.*') ? 'show' : '' }}" id="sidebarWWTP">
        <ul class="nav nav-sm flex-column">

            <li class="nav-item">
                <a class="nav-link menu-link" href="{{ url('wwtp/proses') }}">
                    <i class="mdi mdi-water-pump"></i>
                    <span data-key="t-widgets">Proses WWTP</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link" href="{{ url('wwtp/performance') }}">
                    <i class="mdi mdi-water-pump"></i>
                    <span data-key="t-widgets">Performance WWTP</span>
                </a>
            </li>



        </ul>
    </div>
</li>
@endif