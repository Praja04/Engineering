<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\CompressorDetails;
use App\Models\Utility\AhuDetails;
use App\Models\Utility\MdpMonitoring;
use App\Models\Utility\WarmingUpGenset;
use App\Models\Utility\CoolingTowerDetails;
use App\Models\Utility\AnalisisUtilityDetails;
use App\Models\Utility\CapacitorBankMachineData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardUtilityMachineController extends Controller
{
    public function machine()
    {
        return view('dashboard.utility.dashboard_machine');
    }

    public function machine_data(Request $request)
    {
        $section = $request->query('section', 'all');
        $mode = $request->query('mode', 'today'); // today, harian, mingguan, bulanan, custom
        $reqDate = $request->query('tanggal');
        $startDateStr = $request->query('start_date');
        $endDateStr = $request->query('end_date');
        $gensetYear = (int)$request->query('genset_year', 0);

        $latestDates = array_filter([
            CompressorDetails::max('tanggal'),
            AhuDetails::max('tanggal'),
            MdpMonitoring::max('tanggal_laporan'),
            CoolingTowerDetails::max('tanggal'),
            AnalisisUtilityDetails::max('tanggal'),
            WarmingUpGenset::max('tanggal_laporan'),
            CapacitorBankMachineData::max('tanggal'),
        ]);
        $defaultDate = !empty($latestDates) ? max($latestDates) : Carbon::today()->toDateString();

        if ($mode === 'today') {
            $today = Carbon::today()->toDateString();
            $hasTodayData = CompressorDetails::where('tanggal', $today)->exists()
                || CoolingTowerDetails::where('tanggal', $today)->exists()
                || AhuDetails::where('tanggal', $today)->exists();
            $selectedDate = $hasTodayData ? $today : $defaultDate;
            $startDate = $selectedDate;
            $endDate = $selectedDate;
        } else {
            if (!$reqDate && !$startDateStr) {
                $reqDate = $defaultDate;
            }

            $selectedDate = $reqDate ? Carbon::parse($reqDate)->toDateString() : $defaultDate;

            if ($mode === 'harian') {
                $startDate = $selectedDate;
                $endDate = $selectedDate;
            } elseif ($mode === 'mingguan') {
                $endDate = $selectedDate;
                $startDate = Carbon::parse($selectedDate)->subDays(6)->toDateString();
            } elseif ($mode === 'bulanan') {
                $carbonSel = Carbon::parse($selectedDate);
                $startDate = $carbonSel->copy()->startOfMonth()->toDateString();
                $endOfMonth = $carbonSel->copy()->endOfMonth()->toDateString();
                $todayStr = Carbon::today()->toDateString();
                // Batasi endDate maksimal sampai hari ini (today) agar tidak menampilkan hari masa depan
                $endDate = $endOfMonth > $todayStr ? $todayStr : $endOfMonth;
            } else { // custom
                $startDate = $startDateStr ? Carbon::parse($startDateStr)->toDateString() : $selectedDate;
                $endDate = $endDateStr ? Carbon::parse($endDateStr)->toDateString() : $selectedDate;
            }
        }

        if ($startDate > $endDate) {
            $tmp = $startDate;
            $startDate = $endDate;
            $endDate = $tmp;
        }

        if ($gensetYear === 0) {
            $gensetYear = Carbon::parse($selectedDate)->year;
        }

        $baseResponse = [
            'status' => 'success',
            'section' => $section,
            'mode' => $mode,
            'selected_date' => $selectedDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'last_update' => Carbon::now()->format('d M Y H:i:s'),
        ];

        switch ($section) {
            case 'compressor':
                return response()->json(array_merge($baseResponse, [
                    'compressor' => $this->getCompressorData($startDate, $endDate, $selectedDate, $mode),
                ]));

            case 'ahu':
                return response()->json(array_merge($baseResponse, [
                    'ahu' => $this->getAhuData($startDate, $endDate, $selectedDate, $mode),
                ]));

            case 'mdp':
                return response()->json(array_merge($baseResponse, [
                    'mdp' => $this->getMdpData($startDate, $endDate, $selectedDate, $mode),
                ]));

            case 'genset':
                return response()->json(array_merge($baseResponse, [
                    'genset' => $this->getGensetData($gensetYear),
                ]));

            case 'cooling_tower':
                return response()->json(array_merge($baseResponse, [
                    'cooling_tower' => $this->getCoolingTowerData($startDate, $endDate, $selectedDate, $mode),
                ]));

            case 'water':
            case 'analisis_utility':
                return response()->json(array_merge($baseResponse, [
                    'analisis_utility' => $this->getWaterAnalysisData($startDate, $endDate, $selectedDate, $mode),
                ]));

            case 'all':
            default:
                return response()->json(array_merge($baseResponse, [
                    'compressor' => $this->getCompressorData($startDate, $endDate, $selectedDate, $mode),
                    'ahu' => $this->getAhuData($startDate, $endDate, $selectedDate, $mode),
                    'mdp' => $this->getMdpData($startDate, $endDate, $selectedDate, $mode),
                    'genset' => $this->getGensetData($gensetYear),
                    'cooling_tower' => $this->getCoolingTowerData($startDate, $endDate, $selectedDate, $mode),
                    'analisis_utility' => $this->getWaterAnalysisData($startDate, $endDate, $selectedDate, $mode),
                ]));
        }
    }

    /**
     * 1. COMPRESSOR
     */
    private function getCompressorData($startDate, $endDate, $selectedDate, $mode)
    {
        $compQuery = CompressorDetails::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->orderBy('jam');
        $compDetails = $compQuery->get();

        $compDailyGroup = $compDetails->groupBy(function ($item) {
            return is_string($item->tanggal) ? $item->tanggal : Carbon::parse($item->tanggal)->format('Y-m-d');
        });

        // 1. Forward-fill RH1 for daily trends:
        // Ambil data running_hour_1 terakhir sebelum $startDate jika ada
        $prevRh1Record = CompressorDetails::where('tanggal', '<', $startDate)
            ->whereNotNull('running_hour_1')
            ->where('running_hour_1', '>', 0)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->first();

        $currentRh1 = $prevRh1Record ? (float)$prevRh1Record->running_hour_1 : null;
        if (is_null($currentRh1)) {
            $firstInRange = $compDetails->first(function ($r) {
                return !is_null($r->running_hour_1) && (float)$r->running_hour_1 > 0;
            });
            if ($firstInRange) {
                $currentRh1 = (float)$firstInRange->running_hour_1;
            }
        }

        // Generate continuous date range from $startDate to $endDate (agar hari Minggu/libur tetap tampil di chart)
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        // Pastikan tidak pernah meng-generate tanggal yang melebihi hari ini
        $today = Carbon::today();
        if ($end->gt($today)) {
            $end = $today;
        }
        $allDates = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $allDates[] = $d->format('Y-m-d');
        }

        $compDailyTrends = [];
        foreach ($allDates as $dt) {
            if ($compDailyGroup->has($dt)) {
                $rows = $compDailyGroup->get($dt);
                $validRh1 = $rows->filter(function ($r) {
                    return !is_null($r->running_hour_1) && (float)$r->running_hour_1 > 0;
                });
                if ($validRh1->isNotEmpty()) {
                    $currentRh1 = round((float)$validRh1->last()->running_hour_1, 2);
                }

                $compDailyTrends[] = [
                    'tanggal' => $dt,
                    'rh1_avg' => $currentRh1,
                    'ga57_avg' => round($rows->avg('running_hour_2') ?? 0, 2),
                    'ir55_avg' => round($rows->avg('running_hour_3') ?? 0, 2),
                    'ga55_avg' => round($rows->avg('running_hour_4') ?? 0, 2),
                    'count' => $rows->count(),
                ];
            } else {
                // Hari tanpa data (misal hari Minggu / libur operasional) -> nolkan (sama seperti Cooling Tower)
                $compDailyTrends[] = [
                    'tanggal' => $dt,
                    'rh1_avg' => 0,
                    'ga57_avg' => 0,
                    'ir55_avg' => 0,
                    'ga55_avg' => 0,
                    'count' => 0,
                ];
            }
        }

        // 2. Forward-fill RH1 for hourly readings:
        $selectedDayComp = $compDetails->filter(function ($item) use ($selectedDate) {
            $d = is_string($item->tanggal) ? $item->tanggal : Carbon::parse($item->tanggal)->format('Y-m-d');
            return $d === $selectedDate;
        })->values();

        $prevHourlyRh1 = CompressorDetails::where('tanggal', '<', $selectedDate)
            ->whereNotNull('running_hour_1')
            ->where('running_hour_1', '>', 0)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->value('running_hour_1');

        $hourlyRh1 = !is_null($prevHourlyRh1) ? (float)$prevHourlyRh1 : null;
        if (is_null($hourlyRh1)) {
            $firstDayRh1 = $selectedDayComp->first(function ($r) {
                return !is_null($r->running_hour_1) && (float)$r->running_hour_1 > 0;
            });
            if ($firstDayRh1) {
                $hourlyRh1 = (float)$firstDayRh1->running_hour_1;
            }
        }

        $compHourly = $selectedDayComp->map(function ($item) use (&$hourlyRh1) {
            if (!is_null($item->running_hour_1) && (float)$item->running_hour_1 > 0) {
                $hourlyRh1 = (float)$item->running_hour_1;
            }
            return [
                'jam' => substr($item->jam, 0, 5),
                'rh1' => !is_null($hourlyRh1) ? round($hourlyRh1, 2) : null,
                'ga57' => (float)($item->running_hour_2 ?? 0),
                'ir55' => (float)($item->running_hour_3 ?? 0),
                'ga55' => (float)($item->running_hour_4 ?? 0),
                'load_percent' => (float)($item->load_percent ?? 0),
            ];
        });

        $compTarget = ($mode === 'harian') ? $selectedDayComp : $compDetails;

        $validDailyTrends = array_values(array_filter($compDailyTrends, function ($t) {
            return $t['count'] > 0;
        }));
        $latestTrend = !empty($validDailyTrends) ? $validDailyTrends[count($validDailyTrends) - 1] : null;
        $latestDate = $latestTrend ? $latestTrend['tanggal'] : null;
        $latestDateFormatted = $latestDate ? Carbon::parse($latestDate)->translatedFormat('d M Y') : null;

        $compSummary = [
            'rh1_avg' => round($compTarget->whereNotNull('running_hour_1')->avg('running_hour_1') ?: ($currentRh1 ?? 0), 2),
            'ga57_avg' => round($compTarget->avg('running_hour_2') ?? 0, 2),
            'ir55_avg' => round($compTarget->avg('running_hour_3') ?? 0, 2),
            'ga55_avg' => round($compTarget->avg('running_hour_4') ?? 0, 2),
            'rh1_latest_avg' => $latestTrend && !is_null($latestTrend['rh1_avg']) ? (float)$latestTrend['rh1_avg'] : ($currentRh1 ?? 0),
            'ga57_latest_avg' => $latestTrend ? (float)$latestTrend['ga57_avg'] : round($compTarget->avg('running_hour_2') ?? 0, 2),
            'ir55_latest_avg' => $latestTrend ? (float)$latestTrend['ir55_avg'] : round($compTarget->avg('running_hour_3') ?? 0, 2),
            'ga55_latest_avg' => $latestTrend ? (float)$latestTrend['ga55_avg'] : round($compTarget->avg('running_hour_4') ?? 0, 2),
            'latest_date' => $latestDate,
            'latest_date_formatted' => $latestDateFormatted,
            'rh1_latest' => (float)($selectedDayComp->whereNotNull('running_hour_1')->last()->running_hour_1 ?? ($compDetails->whereNotNull('running_hour_1')->last()->running_hour_1 ?? ($currentRh1 ?? 0))),
            'ga57_latest' => (float)($selectedDayComp->last()->running_hour_2 ?? ($compDetails->last()->running_hour_2 ?? 0)),
            'ir55_latest' => (float)($selectedDayComp->last()->running_hour_3 ?? ($compDetails->last()->running_hour_3 ?? 0)),
            'ga55_latest' => (float)($selectedDayComp->last()->running_hour_4 ?? ($compDetails->last()->running_hour_4 ?? 0)),
            'ga57_delta' => $selectedDayComp->count() > 1 ? round(($selectedDayComp->max('running_hour_2') - $selectedDayComp->min('running_hour_2')), 2) : 0,
            'ir55_delta' => $selectedDayComp->count() > 1 ? round(($selectedDayComp->max('running_hour_3') - $selectedDayComp->min('running_hour_3')), 2) : 0,
            'ga55_delta' => $selectedDayComp->count() > 1 ? round(($selectedDayComp->max('running_hour_4') - $selectedDayComp->min('running_hour_4')), 2) : 0,
            'count' => $compTarget->count(),
        ];

        return [
            'summary' => $compSummary,
            'hourly' => $compHourly,
            'daily_trends' => $compDailyTrends,
        ];
    }

    /**
     * 2. AHU
     */
    private function getAhuData($startDate, $endDate, $selectedDate, $mode)
    {
        $ahuDetails = AhuDetails::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->orderBy('jam')
            ->get();

        $ahuDailyGroup = $ahuDetails->groupBy(function ($item) {
            return is_string($item->tanggal) ? $item->tanggal : Carbon::parse($item->tanggal)->format('Y-m-d');
        });

        $ahuDailyTrends = [];
        foreach ($ahuDailyGroup as $dt => $rows) {
            $allTemps = [];
            foreach ($rows as $r) {
                foreach ([1, 2, 3, 4] as $idx) {
                    $v = $r->{'temp_out_' . $idx};
                    if (!is_null($v) && is_numeric($v)) $allTemps[] = (float)$v;
                }
            }
            $ahuDailyTrends[] = [
                'tanggal' => $dt,
                'ampere_1' => round($rows->avg('ampere_1') ?? 0, 2),
                'ampere_2' => round($rows->avg('ampere_2') ?? 0, 2),
                'ampere_3' => round($rows->avg('ampere_3') ?? 0, 2),
                'ampere_4' => round($rows->avg('ampere_4') ?? 0, 2),
                'temp_out_avg' => count($allTemps) ? round(array_sum($allTemps) / count($allTemps), 2) : 0,
            ];
        }

        $selectedDayAhu = $ahuDetails->filter(function ($item) use ($selectedDate) {
            $d = is_string($item->tanggal) ? $item->tanggal : Carbon::parse($item->tanggal)->format('Y-m-d');
            return $d === $selectedDate;
        })->values();

        $ahuHourly = $selectedDayAhu->map(function ($item) {
            $rowTemps = [];
            foreach ([1, 2, 3, 4] as $idx) {
                $v = $item->{'temp_out_' . $idx};
                if (!is_null($v) && is_numeric($v)) $rowTemps[] = (float)$v;
            }
            $rowTempAvg = count($rowTemps) ? round(array_sum($rowTemps) / count($rowTemps), 2) : 0;
            return [
                'jam' => substr($item->jam, 0, 5),
                'ampere_1' => (float)($item->ampere_1 ?? 0),
                'ampere_2' => (float)($item->ampere_2 ?? 0),
                'ampere_3' => (float)($item->ampere_3 ?? 0),
                'ampere_4' => (float)($item->ampere_4 ?? 0),
                'temp_out' => $rowTempAvg,
                'temp_out_1' => (float)($item->temp_out_1 ?? 0),
                'temp_out_2' => (float)($item->temp_out_2 ?? 0),
                'temp_out_3' => (float)($item->temp_out_3 ?? 0),
                'temp_out_4' => (float)($item->temp_out_4 ?? 0),
            ];
        });

        $ahuTarget = ($mode === 'harian') ? $selectedDayAhu : $ahuDetails;
        $ahuAllTemps = [];
        foreach ($ahuTarget as $r) {
            foreach ([1, 2, 3, 4] as $idx) {
                $v = $r->{'temp_out_' . $idx};
                if (!is_null($v) && is_numeric($v)) $ahuAllTemps[] = (float)$v;
            }
        }
        $ahuSummary = [
            'ampere_1_avg' => round($ahuTarget->avg('ampere_1') ?? 0, 2),
            'ampere_2_avg' => round($ahuTarget->avg('ampere_2') ?? 0, 2),
            'ampere_3_avg' => round($ahuTarget->avg('ampere_3') ?? 0, 2),
            'ampere_4_avg' => round($ahuTarget->avg('ampere_4') ?? 0, 2),
            'temp_out_avg' => count($ahuAllTemps) ? round(array_sum($ahuAllTemps) / count($ahuAllTemps), 2) : 0,
            'temp_out_min' => count($ahuAllTemps) ? min($ahuAllTemps) : 0,
            'temp_out_max' => count($ahuAllTemps) ? max($ahuAllTemps) : 0,
            'count' => $ahuTarget->count(),
        ];

        return [
            'summary' => $ahuSummary,
            'hourly' => $ahuHourly,
            'daily_trends' => $ahuDailyTrends,
        ];
    }

    /**
     * 3. MDP
     */
    private function getMdpData($startDate, $endDate, $selectedDate, $mode)
    {
        $mdpDetails = MdpMonitoring::whereBetween('tanggal_laporan', [$startDate, $endDate])
            ->orderBy('tanggal_laporan')
            ->orderBy('jam_pencatatan')
            ->get();

        $selectedDayMdp = $mdpDetails->filter(function ($item) use ($selectedDate) {
            $d = is_string($item->tanggal_laporan) ? $item->tanggal_laporan : Carbon::parse($item->tanggal_laporan)->format('Y-m-d');
            return $d === $selectedDate;
        })->values();

        $mdpDailyTrends = [];
        $mdpDailyGroup = $mdpDetails->groupBy(function ($item) {
            return is_string($item->tanggal_laporan) ? $item->tanggal_laporan : Carbon::parse($item->tanggal_laporan)->format('Y-m-d');
        });
        foreach ($mdpDailyGroup as $dt => $rows) {
            $mdpDailyTrends[] = [
                'tanggal' => $dt,
                'arus_avg' => round($rows->avg('arus_rata_rata') ?? 0, 2),
                'tegangan_avg' => round($rows->avg('tegangan_rata_rata') ?? 0, 2),
                'daya_total_avg' => round($rows->avg('daya_total') ?? 0, 2),
                'temp_trafo_avg' => round($rows->avg('temperatur_transformator') ?? 0, 2),
                'suhu_trafo_avg' => round($rows->avg('suhu_ruang_trafo') ?? 0, 2),
                'suhu_genset_avg' => round($rows->avg('suhu_ruang_genset') ?? 0, 2),
            ];
        }

        $mdpTarget = ($mode === 'harian') ? $selectedDayMdp : $mdpDetails;
        $latestMdp = $selectedDayMdp->last() ?? $mdpDetails->last();

        // ── Cos Phi dari Capacitor Bank Mesin (per 30 menit -> rata-rata harian -> rata-rata periode) ──
        $capData = CapacitorBankMachineData::where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->whereNull('tanggal')
                     ->whereDate('created_at', '>=', $startDate)
                     ->whereDate('created_at', '<=', $endDate);
              });
        })->orderBy('created_at', 'asc')->get();

        // Jika mode harian, filter tepat ke selectedDate
        if ($mode === 'harian') {
            $capData = $capData->filter(function ($item) use ($selectedDate) {
                $d = !empty($item->tanggal)
                    ? (is_string($item->tanggal) ? substr($item->tanggal, 0, 10) : Carbon::parse($item->tanggal)->format('Y-m-d'))
                    : Carbon::parse($item->created_at)->format('Y-m-d');
                return $d === $selectedDate;
            })->values();
        }

        // Kelompokkan per hari untuk menghitung rata-rata harian
        $capDailyGroup = $capData->groupBy(function ($item) {
            if (!empty($item->tanggal)) {
                return is_string($item->tanggal) ? substr($item->tanggal, 0, 10) : Carbon::parse($item->tanggal)->format('Y-m-d');
            }
            return Carbon::parse($item->created_at)->format('Y-m-d');
        });

        $dailyCosPhiList = [];
        foreach ($capDailyGroup as $dt => $rows) {
            $rowCosPhis = [];
            foreach ($rows as $r) {
                $phases = array_filter([(float)$r->cosphi_dPFa, (float)$r->cosphi_dPFb, (float)$r->cosphi_dPFc], fn($v) => !is_null($v) && is_numeric($v));
                if (!empty($phases)) {
                    $rowCosPhis[] = array_sum($phases) / count($phases);
                }
            }
            if (!empty($rowCosPhis)) {
                $dailyCosPhiList[] = array_sum($rowCosPhis) / count($rowCosPhis);
            }
        }

        $cosPhiAvg = !empty($dailyCosPhiList) ? round(array_sum($dailyCosPhiList) / count($dailyCosPhiList), 2) : 0;

        $mdpSummary = [
            'arus_rata_rata' => round($mdpTarget->avg('arus_rata_rata') ?? 0, 2),
            'tegangan_rata_rata' => round($mdpTarget->avg('tegangan_rata_rata') ?? 0, 2),
            'daya_total' => round($mdpTarget->avg('daya_total') ?? 0, 2),
            'temperatur_transformator' => round($mdpTarget->avg('temperatur_transformator') ?? 0, 2),
            'suhu_ruang_trafo' => round($mdpTarget->avg('suhu_ruang_trafo') ?? 0, 2),
            'suhu_ruang_genset' => round($mdpTarget->avg('suhu_ruang_genset') ?? 0, 2),
            'cos_phi' => $cosPhiAvg,
            'has_cos_phi_data' => !empty($dailyCosPhiList),
            'latest' => $latestMdp ? [
                'jam' => substr($latestMdp->jam_pencatatan, 0, 5),
                'arus_i1' => (float)$latestMdp->arus_i1,
                'arus_i2' => (float)$latestMdp->arus_i2,
                'arus_i3' => (float)$latestMdp->arus_i3,
                'tegangan_v1' => (float)$latestMdp->tegangan_v1,
                'tegangan_v2' => (float)$latestMdp->tegangan_v2,
                'tegangan_v3' => (float)$latestMdp->tegangan_v3,
                'daya_p1' => (float)$latestMdp->daya_p1,
                'daya_p2' => (float)$latestMdp->daya_p2,
                'daya_p3' => (float)$latestMdp->daya_p3,
                'level_oil' => $latestMdp->level_oil,
                'status' => $latestMdp->status,
            ] : null,
            'count' => $mdpTarget->count(),
        ];

        return [
            'summary' => $mdpSummary,
            'daily_trends' => $mdpDailyTrends,
        ];
    }

    /**
     * 4. GENSET
     */
    private function getGensetData($gensetYear)
    {
        $gensetAllYears = WarmingUpGenset::selectRaw('YEAR(tanggal_laporan) as yr')
            ->distinct()->pluck('yr')->filter()->sortDesc()->values()->toArray();
        if (!in_array(Carbon::today()->year, $gensetAllYears)) {
            array_unshift($gensetAllYears, Carbon::today()->year);
        }

        $allGensetRuns = WarmingUpGenset::whereYear('tanggal_laporan', $gensetYear)
            ->orderBy('tanggal_laporan', 'asc')
            ->orderBy('jam_pencatatan', 'asc')
            ->get();

        // Validasi running hour genset di tahun terpilih
        $validRhGenset = $allGensetRuns->filter(function ($item) {
            return !is_null($item->running_hour) && is_numeric($item->running_hour) && (float)$item->running_hour > 0;
        })->values();

        $firstRun = $validRhGenset->first();
        $latestRun = $validRhGenset->last();

        $rhStart = $firstRun ? (float)$firstRun->running_hour : 0;
        $rhLatest = $latestRun ? (float)$latestRun->running_hour : 0;
        // Data terbaru dikurangin paling awal
        $rhDelta = ($firstRun && $latestRun) ? round(max(0, $rhLatest - $rhStart), 2) : 0;

        // Ambil 7 data warming up terakhir untuk grafik timeline
        $gensetRuns = $allGensetRuns->take(-7)->values();

        return [
            'year' => $gensetYear,
            'available_years' => array_values(array_unique($gensetAllYears)),
            'total_runs' => $allGensetRuns->count(),
            'avg_speed' => round($allGensetRuns->avg('engine_speed') ?? 0, 2),
            'avg_temperature' => round($allGensetRuns->avg('engine_temperature') ?? 0, 2),
            'avg_oil_pressure' => round($allGensetRuns->avg('engine_oil_pressure') ?? 0, 2),
            'running_hour_delta' => $rhDelta,
            'running_hour_start' => $rhStart,
            'running_hour_latest' => $rhLatest,
            'rh_start_date' => $firstRun ? (is_string($firstRun->tanggal_laporan) ? $firstRun->tanggal_laporan : Carbon::parse($firstRun->tanggal_laporan)->format('Y-m-d')) : null,
            'rh_latest_date' => $latestRun ? (is_string($latestRun->tanggal_laporan) ? $latestRun->tanggal_laporan : Carbon::parse($latestRun->tanggal_laporan)->format('Y-m-d')) : null,
            'latest_run' => $allGensetRuns->last(),
            'runs' => $gensetRuns->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => is_string($item->tanggal_laporan) ? $item->tanggal_laporan : Carbon::parse($item->tanggal_laporan)->format('Y-m-d'),
                    'jam' => substr($item->jam_pencatatan, 0, 5),
                    'engine_speed' => (float)($item->engine_speed ?? 0),
                    'engine_temperature' => (float)($item->engine_temperature ?? 0),
                    'engine_oil_pressure' => (float)($item->engine_oil_pressure ?? 0),
                    'battery_voltage' => (float)($item->battery_voltage ?? 0),
                    'running_hour' => (float)($item->running_hour ?? 0),
                    'status' => $item->status,
                ];
            }),
        ];
    }

    /**
     * 5. COOLING TOWER
     */
    private function getCoolingTowerData($startDate, $endDate, $selectedDate, $mode)
    {
        $ctDetails = CoolingTowerDetails::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')->orderBy('jam')->get();

        $selectedDayCt = $ctDetails->filter(function ($item) use ($selectedDate) {
            $d = is_string($item->tanggal) ? $item->tanggal : Carbon::parse($item->tanggal)->format('Y-m-d');
            return $d === $selectedDate;
        })->values();

        $ctHourly = $selectedDayCt->map(function ($item) {
            $jam = $item->jam;
            if ($jam instanceof \DateTimeInterface) {
                $jamStr = $jam->format('H:i');
            } elseif (is_string($jam)) {
                $jamStr = strlen($jam) >= 16 ? substr($jam, 11, 5) : substr($jam, 0, 5);
            } else {
                $jamStr = (string)$jam;
            }

            return [
                'jam' => $jamStr,
                'temp_out' => (float)($item->temp_ct_out ?? 0),
                'temp_in' => (float)($item->temp_ct_in ?? 0),
                'pressure_out' => (float)($item->pressure_ct_out ?? 0),
                'pressure_in' => (float)($item->pressure_ct_in ?? 0),
            ];
        });

        $ctDailyTrends = [];
        $ctDailyGroup = $ctDetails->groupBy(function ($item) {
            return is_string($item->tanggal) ? $item->tanggal : Carbon::parse($item->tanggal)->format('Y-m-d');
        });
        foreach ($ctDailyGroup as $dt => $rows) {
            $ctDailyTrends[] = [
                'tanggal' => $dt,
                'temp_out_avg' => round($rows->avg('temp_ct_out') ?? 0, 2),
                'pressure_out_avg' => round($rows->avg('pressure_ct_out') ?? 0, 2),
            ];
        }

        $ctTarget = ($mode === 'harian') ? $selectedDayCt : $ctDetails;
        $ctSummary = [
            'temp_out_avg' => round($ctTarget->avg('temp_ct_out') ?? 0, 2),
            'temp_out_min' => $ctTarget->count() ? (float)$ctTarget->min('temp_ct_out') : 0,
            'temp_out_max' => $ctTarget->count() ? (float)$ctTarget->max('temp_ct_out') : 0,
            'pressure_out_avg' => round($ctTarget->avg('pressure_ct_out') ?? 0, 2),
            'pressure_out_min' => $ctTarget->count() ? (float)$ctTarget->min('pressure_ct_out') : 0,
            'pressure_out_max' => $ctTarget->count() ? (float)$ctTarget->max('pressure_ct_out') : 0,
            'count' => $ctTarget->count(),
        ];

        return [
            'summary' => $ctSummary,
            'hourly' => $ctHourly,
            'daily_trends' => $ctDailyTrends,
        ];
    }

    /**
     * 6. ANALISIS UTILITY (WATER QUALITY)
     */
    private function getWaterAnalysisData($startDate, $endDate, $selectedDate, $mode)
    {
        $analisisDetails = AnalisisUtilityDetails::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')->get();

        $selectedDayAnalisis = $analisisDetails->filter(function ($item) use ($selectedDate) {
            $d = is_string($item->tanggal) ? $item->tanggal : Carbon::parse($item->tanggal)->format('Y-m-d');
            return $d === $selectedDate;
        })->first();

        $analisisTarget = ($mode === 'harian' && $selectedDayAnalisis)
            ? collect([$selectedDayAnalisis])
            : $analisisDetails;

        $waterAnalysis = [
            'cooling_tower' => [
                'name' => 'Cooling Tower',
                'ph' => round($analisisTarget->avg('ph_cooling_tower') ?? 0, 2),
                'tds' => round($analisisTarget->avg('tds_cooling_tower') ?? 0, 2),
                'hardness' => round($analisisTarget->avg('hardness_ct') ?? 0, 2),
                'std_ph' => '7.5 - 8.5',
                'std_tds' => '≤ 2500 ppm',
                'std_hardness' => '≤ 800 ppm',
            ],
            'reverse_osmosis' => [
                'name' => 'Reverse Osmosis (RO Storage)',
                'ph' => round($analisisTarget->avg('ph_ro_storage') ?? 0, 2),
                'tds' => round($analisisTarget->avg('tds_ro_storage') ?? 0, 2),
                'hardness' => round($analisisTarget->avg('hardness_ro') ?? 0, 2),
                'std_ph' => '6.5 - 7.5',
                'std_tds' => '≤ 50 ppm',
                'std_hardness' => '0 - 10 ppm',
            ],
            'water_softener' => [
                'name' => 'Water Softener (WS Storage)',
                'ph' => round($analisisTarget->avg('ph_ws_storage') ?? 0, 2),
                'tds' => round($analisisTarget->avg('tds_ws_storage') ?? 0, 2),
                'hardness' => round($analisisTarget->avg('hardness_ws_storage') ?? 0, 2),
                'std_ph' => '6.8 - 7.8',
                'std_tds' => '≤ 300 ppm',
                'std_hardness' => '0 - 10 ppm',
            ],
            'boiler' => [
                'name' => 'Boiler',
                'ph' => round($analisisTarget->avg('ph_boiler') ?? 0, 2),
                'tds' => round($analisisTarget->avg('tds_boiler') ?? 0, 2),
                'hardness' => round($analisisTarget->avg('hardness_boiler') ?? 0, 2),
                'std_ph' => '10.5 - 11.5',
                'std_tds' => '≤ 3500 ppm',
                'std_hardness' => '0 ppm',
            ],
            'fresh_water' => [
                'name' => 'Fresh Water (FW Storage)',
                'ph' => round($analisisTarget->avg('ph_fw_storage') ?? 0, 2),
                'tds' => round($analisisTarget->avg('tds_fw_storage') ?? 0, 2),
                'hardness' => round($analisisTarget->avg('hardness_inlet_ws') ?? 0, 2),
                'std_ph' => '6.5 - 8.5',
                'std_tds' => '≤ 500 ppm',
                'std_hardness' => '≤ 150 ppm',
            ],
        ];

        $analisisDailyTrends = $analisisDetails->map(function ($item) {
            $dt = is_string($item->tanggal) ? $item->tanggal : Carbon::parse($item->tanggal)->format('Y-m-d');
            return [
                'tanggal' => $dt,
                'ct_ph' => (float)($item->ph_cooling_tower ?? 0),
                'ro_ph' => (float)($item->ph_ro_storage ?? 0),
                'ws_ph' => (float)($item->ph_ws_storage ?? 0),
                'boiler_ph' => (float)($item->ph_boiler ?? 0),
                'fw_ph' => (float)($item->ph_fw_storage ?? 0),
                'ct_tds' => (float)($item->tds_cooling_tower ?? 0),
                'ro_tds' => (float)($item->tds_ro_storage ?? 0),
                'ws_tds' => (float)($item->tds_ws_storage ?? 0),
                'boiler_tds' => (float)($item->tds_boiler ?? 0),
                'fw_tds' => (float)($item->tds_fw_storage ?? 0),
                'ct_hardness' => (float)($item->hardness_ct ?? 0),
                'ro_hardness' => (float)($item->hardness_ro ?? 0),
                'ws_hardness' => (float)($item->hardness_ws_storage ?? 0),
                'boiler_hardness' => (float)($item->hardness_boiler ?? 0),
                'fw_hardness' => (float)($item->hardness_inlet_ws ?? 0),
            ];
        });

        return [
            'water_systems' => $waterAnalysis,
            'daily_trends' => $analisisDailyTrends,
            'count' => $analisisDetails->count(),
        ];
    }
}
