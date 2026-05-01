@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;

$ejoMenu = [
[
'type' => 'Drawing',
'classifications' => [
['id' => 1, 'name' => 'Sipil'],
['id' => 2, 'name' => 'Mekanik'],
],
],
[
'type' => 'Project',
'classifications' => [
['id' => 3, 'name' => 'Mekanik'],
['id' => 4, 'name' => 'Sipil'],
['id' => 5, 'name' => 'Maintenance / Improvement'],
['id' => 6, 'name' => 'Repair Part'],
],
],
];
@endphp

@if (
$jabatan === 'admin' ||
$jabatan === 'dept_head' ||
$jabatan === 'supervisor' ||
($jabatan === 'operator' && in_array($bagian, ['Engineering', 'Engineering Workshop & Project'])) ||
($jabatan === 'foreman' && in_array($bagian, ['Engineering', 'Engineering Workshop & Project']))
)

<li class="nav-item">
    {{-- Parent: EJO --}}
    <a class="nav-link menu-link" href="{{ url('ejo') }}">
        <i class="mdi mdi-clipboard-text-outline"></i>
        <span data-key="t-ejo">EJO</span>
    </a>
</li>


<li class="nav-item">
    {{-- Parent: Project --}}
    <a class="nav-link menu-link" href="{{ url('project') }}">
        <i class="mdi mdi-folder-outline"></i>
        <span data-key="t-project">Project</span>
    </a>


</li>
@endif