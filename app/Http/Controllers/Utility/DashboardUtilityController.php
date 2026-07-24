<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardUtilityController extends Controller
{
    public function getApprovers()
    {
        $staff = User::where('departemen', 'engineering')
            ->where('jabatan', 'foreman')
            ->where(function ($q) {
                $q->where('bagian', 'Engineering WWTP')
                    ->orWhere('bagian', 'Engineering');
            })
            ->get(['id', 'username']);

        $spv = User::where('jabatan', 'supervisor')
            ->where(function ($q) {
                $q->where('departemen', 'engineering');
            })
            ->get(['id', 'username']);

        $user = User::where('jabatan', 'supervisor')
            ->Where('departemen', 'engineering')
            ->get(['id', 'username', 'departemen']);

        return response()->json([
            'staff' => $staff,
            'supervisor' => $spv,
            'user'  => $user,
        ]);
    }

    //
    public function utility()
    {
        return view('dashboard.utility.dashboard');
    }
    public function listrik()
    {
        return view('dashboard.utility.listrik');
    }
    public function air()
    {
        return view('dashboard.utility.air');
    }
    public function chemical()
    {
        return view('dashboard.utility.chemical');
    }
    public function wwtp_proses()
    {
        return view('dashboard.wwtp.dashboard_proses');
    }
    public function wwtp_performance()
    {
        return view('dashboard.wwtp.dashboard_performance');
    }
    public function wwtp_sludge()
    {
        return view('dashboard.wwtp.dashboard_sludge');
    }
    public function wwtp_visualisasi()
    {
        return view('dashboard.wwtp.dashboard_visualisasi');
    }
    public function wwtp_visualisasi_data(Request $request)
    {
        $dateStr = $request->query('tanggal');
        if (!$dateStr) {
            $latestInfluent = \App\Models\Utility\WwtpInfluentHarian::orderBy('tanggal', 'desc')->first();
            $dateStr = $latestInfluent ? $latestInfluent->tanggal : Carbon::today()->toDateString();
        }

        $date = \Carbon\Carbon::parse($dateStr);
        $dateFormatted = $date->toDateString();

        $influentRecords = \App\Models\Utility\WwtpInfluentHarian::whereDate('tanggal', $dateFormatted)->get();
        $proses = [
            'debit1' => $influentRecords->avg('debit1') ?? 0,
            'debit2' => $influentRecords->avg('debit2') ?? 0,
            'pit_outlet' => $influentRecords->sum('pit_outlet') ?? 0,
            'pit_produksi_step3' => $influentRecords->sum('pit_produksi_step3') ?? 0,
            'pit_sparta' => $influentRecords->sum('pit_sparta') ?? 0,
            'pit_garam' => $influentRecords->sum('pit_garam') ?? 0,
            'pit_boiler' => $influentRecords->sum('pit_boiler') ?? 0,
            'pit_domestik' => $influentRecords->sum('pit_domestik') ?? 0,
            'pit_storage' => $influentRecords->sum('pit_storage') ?? 0,
        ];

        $analisaRecords = \App\Models\Utility\wwtp_analisa\WwtpAnalisa::with('details')
            ->whereDate('analisa_date', $dateFormatted)
            ->get();

        $paramCOD = \App\Models\Utility\wwtp_analisa\WwtpParameter::where('parameter_name', 'like', '%COD%')->first();
        $paramTSS = \App\Models\Utility\wwtp_analisa\WwtpParameter::where('parameter_name', 'like', '%TSS%')->first();
        $paramPH  = \App\Models\Utility\wwtp_analisa\WwtpParameter::where('parameter_name', 'like', '%pH%')->first();
        $paramEC  = \App\Models\Utility\wwtp_analisa\WwtpParameter::where('parameter_name', 'like', '%EC%')->first();

        $pointNamesMap = [
            'Influent'           => ['Influent', 'Influent COD'],
            'Outlet DAF'         => ['Outlet DAF', 'DAF pre', 'DAF post', 'DAF'],
            'Equalisasi 2'       => ['Equalisasi 2', 'New Anaerob', 'Sparta', 'Equalisasi'],
            'Inlet Anaerob'      => ['Inlet Anaerob'],
            'Outlet Anaerob'     => ['Outlet Anaerob', 'Anaerob'],
            'Aerasi-1'           => ['Aerasi-1'],
            'Aerasi-2'           => ['Aerasi-2'],
            'Aerasi-3'           => ['Aerasi-3'],
            'Aerasi-4'           => ['Aerasi-4'],
            'Aerasi-5'           => ['Aerasi-5', 'Aerasi 6', 'Aerasi-6', 'Aerob'],
            'Lumpur Aktif'       => ['Lumpur Aktif'],
            'Clarifier 1'        => ['Clarifier 1', 'Clarifier-1'],
            'Clarifier 2'        => ['Clarifier 2', 'Clarifier-2'],
            'SDM 1'              => ['SDM 1', 'Sedimen-1', 'Sedimen 1'],
            'Filtrat SCP'        => ['Filtrat SCP', 'Fitrat SCP'],
            'Outlet Sand Filter' => ['Outlet Sand Filter', 'Sandfilter'],
            'Effluent'           => ['Effluent', 'Pit Outlet (Effluent)', 'Effluent COD (max 300 ppm)', 'Outlet'],
        ];

        $dbPoints = \App\Models\Utility\wwtp_analisa\WwtpPoint::all();
        $pointIdMap = [];
        foreach ($pointNamesMap as $key => $names) {
            foreach ($names as $name) {
                $found = $dbPoints->first(function ($p) use ($name) {
                    return strtolower(trim($p->point_name)) === strtolower(trim($name));
                });
                if ($found) {
                    $pointIdMap[$key] = $found->id;
                    break;
                }
            }
        }

        $getAnalisaVal = function ($parameterId, $pointKey) use ($analisaRecords, $pointIdMap) {
            if (!$parameterId || !isset($pointIdMap[$pointKey])) {
                return 0;
            }
            $pointId = $pointIdMap[$pointKey];
            if ($analisaRecords->isEmpty()) {
                return 0;
            }
            $values = collect();
            foreach ($analisaRecords as $rec) {
                $detail = $rec->details->first(function ($d) use ($parameterId, $pointId) {
                    return $d->parameter_id == $parameterId && $d->point_id == $pointId;
                });
                if ($detail && $detail->hasil_analisa !== null) {
                    $values->push((float)$detail->hasil_analisa);
                }
            }
            return $values->isNotEmpty() ? $values->average() : 0;
        };

        $analisaData = [];
        $points = array_keys($pointNamesMap);
        foreach ($points as $point) {
            $analisaData[$point] = [
                'ph'  => $getAnalisaVal($paramPH?->id, $point),
                'tss' => $getAnalisaVal($paramTSS?->id, $point),
                'cod' => $getAnalisaVal($paramCOD?->id, $point),
                'ec'  => $getAnalisaVal($paramEC?->id, $point),
            ];
        }

        $removals = [];
        $calcRemoval = function ($in, $out) {
            if ($in <= 0) return 0;
            return (($in - $out) / $in) * 100;
        };

        $removals['anaerob'] = [
            'tss' => $calcRemoval($analisaData['Influent']['tss'], $analisaData['Outlet Anaerob']['tss']),
            'cod' => $calcRemoval($analisaData['Influent']['cod'], $analisaData['Outlet Anaerob']['cod']),
        ];

        $aerasiTSS = $analisaData['Aerasi-5']['tss'] > 0 ? $analisaData['Aerasi-5']['tss'] : $analisaData['Aerasi-1']['tss'];
        $aerasiCOD = $analisaData['Aerasi-5']['cod'] > 0 ? $analisaData['Aerasi-5']['cod'] : $analisaData['Aerasi-1']['cod'];
        $removals['aerob'] = [
            'tss' => $calcRemoval($analisaData['Outlet Anaerob']['tss'], $aerasiTSS),
            'cod' => $calcRemoval($analisaData['Outlet Anaerob']['cod'], $aerasiCOD),
        ];

        $removals['lumpur_aktif'] = [
            'tss' => $calcRemoval($analisaData['Outlet Anaerob']['tss'], $analisaData['Lumpur Aktif']['tss']),
            'cod' => $calcRemoval($analisaData['Outlet Anaerob']['cod'], $analisaData['Lumpur Aktif']['cod']),
        ];

        $clarifierAvgTSS = ($analisaData['Clarifier 1']['tss'] + $analisaData['Clarifier 2']['tss']) / 2;
        $clarifierAvgCOD = ($analisaData['Clarifier 1']['cod'] + $analisaData['Clarifier 2']['cod']) / 2;
        $removals['daf'] = [
            'tss' => $calcRemoval($clarifierAvgTSS, $analisaData['Outlet DAF']['tss']),
            'cod' => $calcRemoval($clarifierAvgCOD, $analisaData['Outlet DAF']['cod']),
        ];

        $removals['sandfilter'] = [
            'tss' => $calcRemoval($analisaData['Outlet DAF']['tss'], $analisaData['Outlet Sand Filter']['tss']),
            'cod' => $calcRemoval($analisaData['Outlet DAF']['cod'], $analisaData['Outlet Sand Filter']['cod']),
        ];

        $removals['outlet'] = [
            'tss' => $calcRemoval($analisaData['Outlet Sand Filter']['tss'], $analisaData['Effluent']['tss']),
            'cod' => $calcRemoval($analisaData['Outlet Sand Filter']['cod'], $analisaData['Effluent']['cod']),
        ];

        $removals['total'] = [
            'tss' => $calcRemoval($analisaData['Influent']['tss'], $analisaData['Effluent']['tss']),
            'cod' => $calcRemoval($analisaData['Influent']['cod'], $analisaData['Effluent']['cod']),
        ];

        $sludgeRecords = \App\Models\Utility\WwtpSludge::whereDate('tanggal', $dateFormatted)->get();
        $pengangkutan = \App\Models\Utility\WwtpPengangkutanSludge::where('week_start', '<=', $dateFormatted)
            ->where('week_end', '>=', $dateFormatted)
            ->first();

        $sludge = [
            'drain_lumpur' => $sludgeRecords->sum('drain_lumpur') ?? 0,
            'running_hour_scp' => $sludgeRecords->sum('running_hour_scp') ?? 0,
            'hasil_lumpur' => $sludgeRecords->sum('hasil_lumpur') ?? 0,
            'sludge_content' => $sludgeRecords->avg('sludge_content') ?? 0,
            'pengangkutan' => $pengangkutan ? $pengangkutan->jumlah_pengangkutan : 0,
        ];

        return response()->json([
            'status' => 'success',
            'tanggal' => $dateFormatted,
            'proses' => $proses,
            'analisa' => $analisaData,
            'removals' => $removals,
            'sludge' => $sludge,
        ]);
    }
}
