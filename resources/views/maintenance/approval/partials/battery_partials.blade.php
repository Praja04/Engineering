 @php

 $batteryItems = [
 'voltase' => 'Voltase',
 'level_air_aki' => 'Level Air Aki',
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

 <h5 class="mb-3">Mtc Battery Inspection</h5>

 <table class="table table-sm table-borderless mb-4">

     <tr>
         <th width="150">Tanggal</th>
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
         <th>Tipe Battery</th>
         <td>: {{ $data->battery_type ?? '-' }}</td>
     </tr>
     <tr>
         <th>No Unit</th>
         <td>: {{ $data->no_unit ?? '-' }}</td>
     </tr>
     <tr>
         <th>No Seri</th>
         <td>: {{ $data->no_seri ?? '-' }}</td>
     </tr>
     <tr>
         <th>Total Voltase</th>
         <td>: {{ $data->total_voltase ?? '-' }} V</td>
     </tr>
     <tr>
         <th>Grounding</th>
         <td>: {{ $data->grounding ?? '-' }} V</td>
     </tr>
     <tr>
         <th>Intercell</th>
         <td>: {!! badge($data->intercell) !!}</td>
     </tr>
     <tr>
         <th>Kondisi Skun</th>
         <td>: {!! badge($data->kondisi_skun) !!}</td>
     </tr>
     <tr>
         <th>Kondisi Unit</th>
         <td>: {!! badge($data->kondisi_unit) !!}</td>
     </tr>
     <tr>
         <th>Kondisi Plug Battery</th>
         <td>: 
             @if(($data->kondisi_plug_battery ?? '') == 'OK')
                 <span class="badge bg-success">OK</span>
             @elseif(($data->kondisi_plug_battery ?? '') == 'Tidak OK')
                 <span class="badge bg-danger">Tidak OK</span>
             @else
                 <span class="badge bg-secondary">No Check</span>
             @endif
         </td>
     </tr>
     <tr>
         <th>Catatan</th>
         <td>: {{ $data->catatan ?? '-' }}</td>
     </tr>
     <tr>
         <th>Keterangan NOK</th>
         <td>: {{ $main->keterangan ?? '-' }}</td>
     </tr>
 </table>

 <h6 class="mb-2">Checklist Battery</h6>

 <table class="table table-sm table-bordered align-middle text-center">
     <thead class="table-light">
         <tr>
             <th>Cell</th>
             @foreach ($batteryItems as $label)
             <th>{{ $label }}</th>
             @endforeach
         </tr>
     </thead>
     <tbody>
         @forelse ($data->details as $detail)
         <tr>
             <td class="fw-bold">{{ $detail->cell }}</td>

             @foreach ($batteryItems as $field => $label)
             <td>{!! badge($detail->$field) !!}</td>
             @endforeach
         </tr>
         @empty
         <tr>
             <td colspan="{{ count($batteryItems) + 1 }}" class="text-muted text-center">
                 Tidak ada data inspeksi battery
             </td>
         </tr>
         @endforelse
     </tbody>
 </table>