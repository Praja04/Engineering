@php
$jabatan = strtolower(Auth::user()->jabatan ?? '');
$bagian = trim(Auth::user()->bagian ?? '');
@endphp

@if (
$jabatan === 'admin' ||
$jabatan === 'dept_head' ||
$jabatan === 'supervisor' ||
(in_array($jabatan, ['foreman', 'operator']) && in_array($bagian, ['Engineering Maintenance & Improvement', 'Engineering Produksi', 'Engineering Kalibrasi', 'Kalibrasi']))
)
<li class="nav-item">
    <a class="nav-link menu-link" href="#sidebarScoringMesinInput" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarScoringMesinInput">
        <i class="mdi mdi-robot-industrial"></i>
        <span data-key="t-dashboards">Scoring Mesin</span>
    </a>
    <div class="collapse menu-dropdown" id="sidebarScoringMesinInput">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ url('scoring') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-factory me-2"></i> List Mesin Retail
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('scoring/history') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-history me-2"></i> History Scoring Mesin
                </a>
            </li>
        </ul>
    </div>
</li>
@endif
