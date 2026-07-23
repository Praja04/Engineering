 @php
 $checklist = [
 'motor' => [
 'electrical_motor' => 'Kondisi Electrical',
 'putaran_motor' => 'Putaran Motor',
 'fibrasi_suara_motor' => 'Fibrasi dan Suara Motor',
 'bearing_motor' => 'Bearing Motor',
 'pelumasan_motor' => 'Pelumasan Motor',
 'kebersihan_unit_body_motor' => 'Kebersihan Unit Body Motor',
 ],
 'pump' => [
 'putaran_pompa' => 'Putaran Pompa',
 'shaft_karet_coupling_pompa' => 'Shaft & Karet Coupling',
 'fan_belt_pompa' => 'Fan Belt',
 'pressure_pompa' => 'Pressure Pompa',
 'mechanical_seal_pompa' => 'Mechanical Seal',
 'gasket_pompa' => 'Gasket Pompa',
 'impeler' => 'Kondisi Impeller',
 'kebersihan_unit_body_pompa' => 'Kebersihan Unit & Body Pompa',
 ],
 'aksesoris' => [
 'valve_aksesoris' => 'Valve',
 'cek_valve_aksesoris' => 'Cek Valve',
 'flow_meter_aksesoris' => 'Flow Meter',
 'strainer_aksesoris' => 'Strainer / Saringan',
 'alat_ukur_aksesoris' => 'Alat Ukur',
 'kelengkapan_baut_mur_aksesoris' => 'Kelengkapan Baut & Mur',
 ],
 'gearbox' => [
 'tambah_ganti_oli_gearbox' => 'Penambahan / Penggantian Oli Gearbox',
 'unit_area_gearbox' => 'Unit & Area Gearbox',
 'oil_seal_gearbox' => 'Oil Seal Gearbox',
 'filter_udara_gearbox' => 'Filter Udara Gearbox',
 'bearing_gearbox' => 'Bearing Gearbox',
 ],
 ];

 function badge($v)
 {
 return $v === null
 ? '<span class="badge bg-secondary">No Check</span>'
 : ($v
 ? '<span class="badge bg-success">OK</span>'
 : '<span class="badge bg-danger">NG</span>');
 }

 @endphp

 <h5 class="mb-3">Motor Pump Inspection</h5>

 <table class="table table-sm table-borderless mb-4">
     <tr>
         <th width="160">Mesin</th>
         <td>: {{ $data->mesin->nama_mesin ?? '-' }}</td>
     </tr>
     <tr>
         <th>Tanggal</th>
         <td>: {{ $main->tanggal }}</td>
     </tr>
     <tr>
         <th>Waktu Mulai</th>
         <td>: {{ $main->waktu_mulai ?? '-' }}</td>
     </tr>
     <tr>
         <th>Waktu Selesai</th>
         <td>: {{ $main->waktu_selesai ?? '-' }}</td>
     </tr>
     <tr>
         <th>Paket</th>
         <td>: {{ $main->paket ?? '-' }}</td>
     </tr>
     <tr>
         <th>Lokasi</th>
         <td>: {{ $main->lokasi ?? '-' }}</td>
     </tr>
     <tr>
         <th>Departemen</th>
         <td>: {{ $main->departemen ?? '-' }}</td>
     </tr>
     <tr>
         <th>Korektif</th>
         <td>: {{ $main->korektif ?? '-' }}</td>
     </tr>
     <tr>
         <th>Keterangan NOK</th>
         <td>: {{ $main->keterangan ?? '-' }}</td>
     </tr>
 </table>

 <h6 class="mb-2">Checklist</h6>

 <table class="table table-sm table-bordered align-middle">
     <thead>
         <tr class="table-light text-center">
             <th width="120">Bagian</th>
             <th>Item</th>
             <th width="80">Status</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($checklist as $section => $items)
         @php $rowspan = count($items); @endphp
         @foreach ($items as $field => $label)
         <tr>
             @if ($loop->first)
             <td rowspan="{{ $rowspan }}" class="fw-bold align-middle text-center">
                 {{ $section }}
             </td>
             @endif
             <td>{{ $label }}</td>
             <td class="text-center">{!! badge($data->$field) !!}</td>
         </tr>
         @endforeach
         @endforeach
     </tbody>
 </table>

 <h6 class="mt-4 mb-2">Kebutuhan Material</h6>

 @if ($main->kebutuhanMaterial->isEmpty())
 <div class="text-muted fst-italic">
     Tidak ada kebutuhan material
 </div>
 @else
 <table class="table table-sm table-bordered align-middle">
     <thead class="table-light text-center">
         <tr>
             <th width="120">MID</th>
             <th>Deskripsi</th>
             <th width="80">Qty</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($main->kebutuhanMaterial as $material)
         <tr>
             <td class="text-center">{{ $material->mid }}</td>
             <td>{{ $material->deskripsi }}</td>
             <td class="text-center">{{ $material->qty }}</td>
         </tr>
         @endforeach
     </tbody>
 </table>
 @endif

 <h6 class="mt-4 mb-2">Penggantian Material</h6>

 @if ($main->penggantianMaterial->isEmpty())
 <div class="text-muted fst-italic">
     Tidak ada penggantian material
 </div>
 @else
 <table class="table table-sm table-bordered align-middle">
     <thead class="table-light text-center">
         <tr>
             <th width="120">MID</th>
             <th>Deskripsi</th>
             <th width="80">Qty</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($main->penggantianMaterial as $replacement)
         <tr>
             <td class="text-center">{{ $replacement->mid }}</td>
             <td>{{ $replacement->deskripsi }}</td>
             <td class="text-center">{{ $replacement->qty }}</td>
         </tr>
         @endforeach
     </tbody>
 </table>
 @endif