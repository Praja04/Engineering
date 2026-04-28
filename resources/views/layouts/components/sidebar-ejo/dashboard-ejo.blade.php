@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
@endphp

@if (
($jabatan === 'dept_head' || $jabatan === 'admin') ||
($jabatan === 'supervisor')||($jabatan === 'operator' && $bagian === 'Engineering Workshop & Project') ||
($jabatan === 'foreman' && $bagian === 'Engineering Workshop & Project')
)
<li class="nav-item">
    <a class="nav-link menu-link" href="#sidebarEjoDashboard" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarEjoDashboard">
        <i class="mdi mdi-wrench"></i>
        <span data-key="t-dashboards">Dashboard Ejo</span>
    </a>
    <div class="collapse menu-dropdown" id="sidebarEjoDashboard">
        <ul class="nav nav-sm flex-column">

            <li class="nav-item">
                <a href="{{ url('ejo/dashboard') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-chart-bar me-2"></i> Ejo Project
                </a>
            </li>


        </ul>
    </div>
</li>
@endif