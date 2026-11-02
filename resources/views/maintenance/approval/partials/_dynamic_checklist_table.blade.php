<h6 class="mb-2">Checklist Inspeksi (Master Dinamis)</h6>

<table class="table table-sm table-bordered align-middle mb-4">
    <thead>
        <tr class="table-light text-center">
            <th width="150">Bagian / Group</th>
            <th>Item Checklist</th>
            <th width="90">Status</th>
            <th>Nilai / Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @php
            $groupedResults = $main->checklistResults->groupBy(function($res) {
                return $res->checklist && $res->checklist->group ? $res->checklist->group->nama_group : 'Umum';
            });
        @endphp
        @foreach ($groupedResults as $groupName => $results)
            @php $rowspan = count($results); @endphp
            @foreach ($results as $res)
                <tr>
                    @if ($loop->first)
                        <td rowspan="{{ $rowspan }}" class="fw-bold align-middle text-center bg-light">
                            {{ $groupName }}
                        </td>
                    @endif
                    <td>
                        <div class="fw-semibold">{{ $res->checklist->label ?? '-' }}</div>
                        @if(!empty($res->checklist->standar))
                            <small class="text-muted d-block">{{ $res->checklist->standar }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($res->status === 'OK' || $res->status == '1')
                            <span class="badge bg-success">OK</span>
                        @elseif ($res->status === 'NG' || $res->status == '0')
                            <span class="badge bg-danger">NG</span>
                        @else
                            <span class="badge bg-secondary">{{ $res->status ?? 'No Check' }}</span>
                        @endif
                    </td>
                    <td>
                        @if(!empty($res->nilai))
                            <span class="badge bg-info-subtle text-info me-1">{{ $res->nilai }}</span>
                        @endif
                        <small class="{{ $res->status === 'NG' || $res->status == '0' ? 'text-danger fw-semibold' : 'text-muted' }}">
                            {{ $res->keterangan ?? '-' }}
                        </small>
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
