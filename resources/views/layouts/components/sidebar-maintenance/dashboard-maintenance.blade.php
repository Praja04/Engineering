@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
@endphp

@if (
($jabatan === 'dept_head') ||
($jabatan === 'supervisor')||($jabatan === 'operator' && $bagian === 'Engineering Maintenance') ||
($jabatan === 'foreman' && $bagian === 'Engineering Maintenance')
)
<li class="nav-item">
    <a class="nav-link menu-link" href="#sidebarMtcDashboard" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMtcDashboard">
        <i class="mdi mdi-wrench"></i>
        <span data-key="t-dashboards">Dashboard MTC</span>
    </a>
    <div class="collapse menu-dropdown" id="sidebarMtcDashboard">
        <ul class="nav nav-sm flex-column">
          
            <li class="nav-item">
                <a href="{{ url('mtc/agenda') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-chart-bar me-2"></i> Agenda
                </a>
            </li>
           
            
        </ul>
    </div>
</li>
@endif