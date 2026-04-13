 @php
     $sipil = [
         'plumbing' => [
             'label' => 'Plumbing',
             'standar' => 'Tidak ada kebocoran dan mampet saluran air pada pipa',
         ],
         'plafon' => [
             'label' => 'Plafon',
             'standar' => 'Tidak berlubang, berjamur dan retakan pada plafon',
         ],
         'lantai' => [
             'label' => 'Lantai',
             'standar' => 'Tidak berlubang, retak, gompal dan jamur pada lantai',
         ],
         'dinding' => [
             'label' => 'Dinding',
             'standar' => 'Tidak ada dinding retak, gompal dan cat atau wallpaper (mengelupas, berjamur, kusam)',
         ],
         'jendela' => [
             'label' => 'Jendela',
             'standar' =>
                 'Engsel berfungsi dengan baik, tidak ada retakan kaca atau cover, cat tidak kusam dan tulangan tidak cacat',
         ],
         'pintu' => [
             'label' => 'Pintu',
             'standar' =>
                 'Engsel berfungsi dengan baik, tidak ada retakan kaca atau cover, cat tidak kusam dan tulangan tidak cacat',
         ],
         'rooling_fast_door' => [
             'label' => 'Rooling / Fast Door',
             'standar' => 'Suara halus, rel terlubrikasi, naik dan turun normal',
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

 <h5 class="mb-3">Mtc Sipil Inspection</h5>

 <table class="table table-sm table-borderless mb-4">
     <tr>
         <th>Tanggal</th>
         <td>: {{ $main->tanggal ?? '-' }}</td>
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

         <th>Departemen</th>
         <td>: {{ $main->departemen ?? '-' }}</td>
     </tr>
     <tr>
         <th>Lokasi</th>
         <td>: {{ $main->lokasi ?? '-' }}</td>
     </tr>
     <tr>
         <th>Area</th>
         <td>: {{ $main->area ?? '-' }}</td>
     </tr>
     <tr>
         <th>Korektif</th>
         <td>: {{ $main->korektif ?? '-' }}</td>
     </tr>
     <tr>
         <th>Rekomendasi</th>
         <td>: {{ $main->rekomendasi ?? '-' }}</td>
     </tr>
     <tr>
         <th>Keterangan NOK</th>
         <td>: {{ $main->keterangan ?? '-' }}</td>
     </tr>
 </table>

 <h6 class="mb-2">Checklist Sipil</h6>

 <table class="table table-sm table-bordered align-middle">
     <thead>
         <tr class="table-light text-center">
             <th width="160">Item</th>
             <th>Standar</th>
             <th width="80">Status</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($sipil as $field => $item)
             <tr>
                 <td class="fw-semibold">{{ $item['label'] }}</td>
                 <td>{{ $item['standar'] }}</td>
                 <td class="text-center">
                     {!! badge($data->$field ?? null) !!}
                 </td>
             </tr>
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
