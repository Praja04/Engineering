@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
@endphp

@if (
($jabatan === 'dept_head') ||
($jabatan === 'supervisor')||($jabatan === 'operator' && $bagian === 'Engineering WWTP') ||
($jabatan === 'foreman' && $bagian === 'Engineering WWTP')
)
<li class="nav-item">
    <a class="nav-link menu-link" href="#sidebarUtility" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUtility">
        <i class="mdi mdi-wrench"></i>
        <span data-key="t-dashboards">Utility</span>
    </a>
    <div class="collapse menu-dropdown" id="sidebarUtility">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a class="nav-link menu-link" href="{{ url('utility/form') }}">
                    <i class="mdi mdi-card-account-details"></i> <span data-key="t-widgets">Form
                        Utility</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link menu-link" href="{{ url('utility/data') }}">
                    <i class="mdi mdi-card-account-details"></i> <span data-key="t-widgets">Data
                        Utility</span>
                </a>
            </li>
        </ul>
    </div>
</li>
@endif