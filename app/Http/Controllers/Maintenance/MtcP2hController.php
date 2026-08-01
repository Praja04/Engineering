<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Models\NotificationsModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance\MtcMainModel;
use App\Models\Maintenance\MtcApprovalModel;
use App\Models\Maintenance\MtcMasterMesinModel;
use App\Models\Maintenance\MtcElectricP2hInspectionModel;
use App\Models\Maintenance\MtcDieselP2hInspectionModel;
use App\Models\Maintenance\MtcGensetP2hInspectionModel;

class MtcP2hController extends Controller
{
    public function form()
    {
        $mesin = MtcMasterMesinModel::whereIn('jenis_mtc', ['Electric P2H', 'Diesel P2H', 'Genset P2H'])
            ->orderBy('id')->get();

        return view('maintenance.p2h.p2h_form', compact('mesin'));
    }

    public function data()
    {
        $mesin = MtcMasterMesinModel::whereIn('jenis_mtc', ['Electric P2H', 'Diesel P2H', 'Electrical P2H', 'Genset P2H'])
            ->orderBy('id')->get();

        return view('maintenance.p2h.p2h_data', compact('mesin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mesin_id' => 'required|exists:mtc_master_mesin,id',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'nullable',
            'departemen' => 'required|string',
            'shift' => 'required|integer|in:1,2,3',
            'hours_meter' => 'nullable|numeric',
            'catatan' => 'nullable|string',
            'staff_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $mesin = MtcMasterMesinModel::findOrFail($request->mesin_id);
        $jenisMtc = $mesin->jenis_mtc;
        $tanggal = $request->tanggal;
        $shift = $request->shift;

        // Legacy compatibility: check both Electric P2H and Electrical P2H for duplicates
        $checkJenis = ($jenisMtc === 'Electric P2H') ? ['Electric P2H', 'Electrical P2H'] : [$jenisMtc];

        // CHECK DUPLICATE
        if ($jenisMtc === 'Electric P2H') {
            $exists = MtcElectricP2hInspectionModel::where('shift', $shift)
                ->whereHas('main', function ($q) use ($tanggal) {
                    $q->whereDate('tanggal', $tanggal)
                        ->whereIn('jenis_mtc', ['Electric P2H', 'Electrical P2H']);
                })
                ->exists();
            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Electric P2H untuk tanggal dan shift tersebut sudah ada.',
                ], 422);
            }
        } elseif ($jenisMtc === 'Diesel P2H') {
            $exists = MtcDieselP2hInspectionModel::where('shift', $shift)
                ->whereHas('main', function ($q) use ($tanggal) {
                    $q->whereDate('tanggal', $tanggal)
                        ->where('jenis_mtc', 'Diesel P2H');
                })
                ->exists();
            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Diesel P2H untuk tanggal dan shift tersebut sudah ada.',
                ], 422);
            }
        } elseif ($jenisMtc === 'Genset P2H') {
            $exists = MtcGensetP2hInspectionModel::where('shift', $shift)
                ->whereHas('main', function ($q) use ($tanggal) {
                    $q->whereDate('tanggal', $tanggal)
                        ->where('jenis_mtc', 'Genset P2H');
                })
                ->exists();
            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Genset P2H untuk tanggal dan shift tersebut sudah ada.',
                ], 422);
            }
        }

        // Hours meter validation
        if ($request->filled('hours_meter')) {
            $hoursMeterError = $this->checkHoursMeterValidation(
                $request->mesin_id,
                $jenisMtc,
                $tanggal,
                $shift,
                $request->hours_meter
            );
            if ($hoursMeterError) {
                return response()->json([
                    'status' => false,
                    'message' => $hoursMeterError,
                ], 422);
            }
        }

        $persentase = 0.0;

        DB::transaction(function () use ($request, $mesin, $jenisMtc, &$persentase) {
            $userId = Auth::id();

            // Save MtcMainModel
            $main = MtcMainModel::create([
                'jenis_mtc' => $jenisMtc,
                'tanggal' => $request->tanggal,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'departemen' => $request->departemen,
                'status' => 'pending',
                'keterangan' => $request->keterangan, // Contains concatenation of NOK details
                'created_by' => $userId,
            ]);

            if ($jenisMtc === 'Electric P2H') {
                $checklistFields = [
                    'level_minyak_rem',
                    'level_oli_hydraulic',
                    'isi_air_aki',
                    'baterai',
                    'hydraulic_system',
                    'selang_hydraulic',
                    'lift_chains',
                    'fork',
                    'body_unit',
                    'lampu_kombinasi_kiri',
                    'lampu_kombinasi_kanan',
                    'lampu_sorot',
                    'lampu_sign_depan_kanan',
                    'lampu_sign_depan_kiri',
                    'klakson',
                    'buzzer_back',
                    'kaca_spion',
                    'baut_roda',
                    'ban',
                    'kebersihan_unit',
                    'panel_display',
                    'sistem_kemudi'
                ];

                $checklistData = [];
                foreach ($checklistFields as $field) {
                    $val = $request->input($field);
                    $checklistData[$field] = ($val !== null && $val !== '') ? (int)$val : null;
                }

                $namaMesin = strtoupper($mesin->nama_mesin);
                $persentase = $this->calculatePercentage($namaMesin, array_merge($checklistData, ['hours_meter' => $request->hours_meter]));

                MtcElectricP2hInspectionModel::create(array_merge($checklistData, [
                    'mtc_main_id' => $main->id,
                    'no_unit' => $mesin->id,
                    'shift' => $request->shift,
                    'hours_meter' => $request->hours_meter,
                    'catatan' => $request->catatan,
                    'persentase' => $persentase,
                ]));
            } elseif ($jenisMtc === 'Diesel P2H') {
                // Diesel P2H
                $checklistFields = [
                    'klakson',
                    'buzzer_back',
                    'oli_mesin',
                    'radiator_hose',
                    'water_pump',
                    'injection_system',
                    'fan_vbelt',
                    'turbocharger_manifold',
                    'tensioner_belt',
                    'starting_motor',
                    'alternator',
                    'control_display',
                    'oli_transmisi',
                    'aki',
                    'engine_mounting',
                    'filter_oli_transmisi',
                    'fungsi_rem',
                    'fungsi_kopling',
                    'oli_hydraulic',
                    'hydraulic_system',
                    'steering_system',
                    'body_back_rest',
                    'kaca_spion',
                    'bucket_pin',
                    'dump_pin_bushing',
                    'seal_hydraulic',
                    'roda_ban_baut',
                    'lampu_unit',
                    'baut_bearing_molen',
                    'baut_hanger_as',
                    'baut_grease',
                    'katup_pembuangan_angin'
                ];

                $checklistData = [];
                foreach ($checklistFields as $field) {
                    $val = $request->input($field);
                    $checklistData[$field] = ($val !== null && $val !== '') ? (int)$val : null;
                }

                MtcDieselP2hInspectionModel::create(array_merge($checklistData, [
                    'mtc_main_id' => $main->id,
                    'mesin_id' => $mesin->id,
                    'no_unit' => $request->no_unit ?? '',
                    'shift' => $request->shift,
                    'hours_meter' => $request->hours_meter,
                    'catatan' => $request->catatan,
                ]));
            } elseif ($jenisMtc === 'Genset P2H') {
                // Genset P2H
                $checklistFields = [
                    'level_oli_mesin',
                    'kebocoran_oli_mesin',
                    'level_coolant_radiator',
                    'kebocoran_coolant',
                    'level_bahan_bakar',
                    'kebocoran_bahan_bakar',
                    'kondisi_aki_baterai',
                    'tegangan_baterai',
                    'filter_udara',
                    'kondisi_panel_genset',
                    'emergency_stop',
                    'suara_mesin_running',
                    'kebersihan_area_genset',
                    'kondisi_knalpot_exhaust'
                ];

                $checklistData = [];
                foreach ($checklistFields as $field) {
                    $val = $request->input($field);
                    $checklistData[$field] = ($val !== null && $val !== '') ? (int)$val : null;
                }

                MtcGensetP2hInspectionModel::create(array_merge($checklistData, [
                    'mtc_main_id' => $main->id,
                    'mesin_id' => $mesin->id,
                    'no_unit' => $request->no_unit ?? '',
                    'shift' => $request->shift,
                    'hours_meter' => $request->hours_meter,
                    'catatan' => $request->catatan,
                ]));
            }

            // Approvals flow
            $ttdPaths = [
                'teknisi' => 'mtc/ttd/ttd_teknisi.jpeg',
                'staff'   => 'mtc/ttd/ttd_staff.jpeg',
                'user'    => 'mtc/ttd/ttd_user.jpeg',
            ];

            $approvalFlows = [
                [
                    'level' => 1,
                    'role'  => 'teknisi',
                    'approver_id' => $userId,
                    'auto'  => true,
                ],
                [
                    'level' => 2,
                    'role'  => 'staff',
                    'approver_id' => $request->staff_id,
                    'auto'  => false,
                ],
                [
                    'level' => 3,
                    'role'  => 'user',
                    'approver_id' => $request->user_id,
                    'auto'  => false,
                ],
            ];

            $notificationSent = false;
            foreach ($approvalFlows as $flow) {
                $isAutoApproved = $flow['auto'];
                $ttdPath = $isAutoApproved ? ($ttdPaths[$flow['role']] ?? null) : null;

                MtcApprovalModel::create([
                    'mtc_main_id' => $main->id,
                    'level'       => $flow['level'],
                    'role'        => $flow['role'],
                    'approver_id' => $flow['approver_id'],
                    'status'      => $isAutoApproved ? 'approved' : 'pending',
                    'ttd'         => $isAutoApproved ? $ttdPath : null,
                    'action_at'   => $isAutoApproved ? now() : null,
                    'action_by'   => $isAutoApproved ? $userId : null,
                ]);

                if (!$isAutoApproved && !$notificationSent) {
                    NotificationsModel::create([
                        'user_id'         => $flow['approver_id'],
                        'notifiable_type' => MtcMainModel::class,
                        'notifiable_id'   => $main->id,
                        'title'           => 'Approval Maintenance',
                        'message'         => 'Maintenance ' . $jenisMtc . ' tanggal ' . date('d F Y', strtotime($main->tanggal)) . ' menunggu persetujuan Anda',
                        'url'             => route('mtc.approval.index'),
                        'is_read'         => false,
                    ]);
                    $notificationSent = true;
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data P2H berhasil disimpan',
            'persentase' => $persentase,
        ]);
    }

    public function getData(Request $request)
    {
        $query = MtcMainModel::query()
            ->whereIn('jenis_mtc', ['Electric P2H', 'Electrical P2H', 'Diesel P2H', 'Genset P2H'])
            ->with([
                'createdBy:id,username',
                'electricP2h.mesin',
                'dieselP2h.mesin',
                'gensetP2h.mesin'
            ]);

        // Filter date
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        // Filter no_unit / machine
        if ($request->filled('no_unit')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('electricP2h.mesin', function ($sq) use ($request) {
                    $sq->where('nama_mesin', 'like', '%' . $request->no_unit . '%');
                })->orWhereHas('dieselP2h.mesin', function ($sq) use ($request) {
                    $sq->where('nama_mesin', 'like', '%' . $request->no_unit . '%');
                })->orWhereHas('gensetP2h.mesin', function ($sq) use ($request) {
                    $sq->where('nama_mesin', 'like', '%' . $request->no_unit . '%');
                })->orWhere('area', 'like', '%' . $request->no_unit . '%');
            });
        }

        // Filter shift
        if ($request->filled('shift')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('electricP2h', function ($sq) use ($request) {
                    $sq->where('shift', $request->shift);
                })->orWhereHas('dieselP2h', function ($sq) use ($request) {
                    $sq->where('shift', $request->shift);
                })->orWhereHas('gensetP2h', function ($sq) use ($request) {
                    $sq->where('shift', $request->shift);
                });
            });
        }

        // Filter departemen
        if ($request->filled('departemen')) {
            $query->where('departemen', 'like', '%' . $request->departemen . '%');
        }

        $total = $query->count();

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->skip($request->start)
            ->take($request->length)
            ->get();

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'departemen' => 'required|string',
            'shift' => 'required|integer|in:1,2,3',
            'hours_meter' => 'nullable|numeric',
            'catatan' => 'nullable|string',
        ]);

        $main = MtcMainModel::findOrFail($id);

        $mesinId = null;
        if ($main->jenis_mtc === 'Electric P2H' || $main->jenis_mtc === 'Electrical P2H') {
            $inspection = MtcElectricP2hInspectionModel::where('mtc_main_id', $main->id)->first();
            if ($inspection) {
                $mesinId = $inspection->no_unit;
            }
        } elseif ($main->jenis_mtc === 'Diesel P2H') {
            $inspection = MtcDieselP2hInspectionModel::where('mtc_main_id', $main->id)->first();
            if ($inspection) {
                $mesinId = $inspection->mesin_id;
            }
        } elseif ($main->jenis_mtc === 'Genset P2H') {
            $inspection = MtcGensetP2hInspectionModel::where('mtc_main_id', $main->id)->first();
            if ($inspection) {
                $mesinId = $inspection->mesin_id;
            }
        }

        if ($request->filled('hours_meter') && $mesinId) {
            $hoursMeterError = $this->checkHoursMeterValidation(
                $mesinId,
                $main->jenis_mtc,
                $request->tanggal,
                $request->shift,
                $request->hours_meter,
                $main->id
            );
            if ($hoursMeterError) {
                return response()->json([
                    'status' => false,
                    'message' => $hoursMeterError,
                ], 422);
            }
        }

        DB::transaction(function () use ($request, $main) {
            $userId = Auth::id();

            $main->update([
                'tanggal' => $request->tanggal,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'departemen' => $request->departemen,
                'keterangan' => $request->keterangan, // Update concatenation of NOK details
                'updated_by' => $userId,
            ]);

            if ($main->jenis_mtc === 'Electric P2H' || $main->jenis_mtc === 'Electrical P2H') {
                $inspection = MtcElectricP2hInspectionModel::where('mtc_main_id', $main->id)->firstOrFail();

                $checklistFields = [
                    'level_minyak_rem',
                    'level_oli_hydraulic',
                    'isi_air_aki',
                    'baterai',
                    'hydraulic_system',
                    'selang_hydraulic',
                    'lift_chains',
                    'fork',
                    'body_unit',
                    'lampu_kombinasi_kiri',
                    'lampu_kombinasi_kanan',
                    'lampu_sorot',
                    'lampu_sign_depan_kanan',
                    'lampu_sign_depan_kiri',
                    'klakson',
                    'buzzer_back',
                    'kaca_spion',
                    'baut_roda',
                    'ban',
                    'kebersihan_unit',
                    'panel_display',
                    'sistem_kemudi'
                ];

                $checklistData = [];
                foreach ($checklistFields as $field) {
                    $val = $request->input($field);
                    $checklistData[$field] = ($val !== null && $val !== '') ? (int)$val : null;
                }

                $mesin = MtcMasterMesinModel::find($inspection->no_unit);
                $namaMesin = $mesin ? strtoupper($mesin->nama_mesin) : '';
                $persentase = $this->calculatePercentage($namaMesin, array_merge($checklistData, ['hours_meter' => $request->hours_meter]));

                $inspection->update(array_merge($checklistData, [
                    'shift' => $request->shift,
                    'hours_meter' => $request->hours_meter,
                    'catatan' => $request->catatan,
                    'persentase' => $persentase,
                ]));
            } else if ($main->jenis_mtc === 'Diesel P2H') {
                $inspection = MtcDieselP2hInspectionModel::where('mtc_main_id', $main->id)->firstOrFail();

                $checklistFields = [
                    'klakson',
                    'buzzer_back',
                    'oli_mesin',
                    'radiator_hose',
                    'water_pump',
                    'injection_system',
                    'fan_vbelt',
                    'turbocharger_manifold',
                    'tensioner_belt',
                    'starting_motor',
                    'alternator',
                    'control_display',
                    'oli_transmisi',
                    'aki',
                    'engine_mounting',
                    'filter_oli_transmisi',
                    'fungsi_rem',
                    'fungsi_kopling',
                    'oli_hydraulic',
                    'hydraulic_system',
                    'steering_system',
                    'body_back_rest',
                    'kaca_spion',
                    'bucket_pin',
                    'dump_pin_bushing',
                    'seal_hydraulic',
                    'roda_ban_baut',
                    'lampu_unit',
                    'baut_bearing_molen',
                    'baut_hanger_as',
                    'baut_grease',
                    'katup_pembuangan_angin'
                ];

                $checklistData = [];
                foreach ($checklistFields as $field) {
                    $val = $request->input($field);
                    $checklistData[$field] = ($val !== null && $val !== '') ? (int)$val : null;
                }

                $inspection->update(array_merge($checklistData, [
                    'no_unit' => $request->no_unit ?? $inspection->no_unit,
                    'shift' => $request->shift,
                    'hours_meter' => $request->hours_meter,
                    'catatan' => $request->catatan,
                ]));
            } else if ($main->jenis_mtc === 'Genset P2H') {
                $inspection = MtcGensetP2hInspectionModel::where('mtc_main_id', $main->id)->firstOrFail();

                $checklistFields = [
                    'level_oli_mesin',
                    'kebocoran_oli_mesin',
                    'level_coolant_radiator',
                    'kebocoran_coolant',
                    'level_bahan_bakar',
                    'kebocoran_bahan_bakar',
                    'kondisi_aki_baterai',
                    'tegangan_baterai',
                    'filter_udara',
                    'kondisi_panel_genset',
                    'emergency_stop',
                    'suara_mesin_running',
                    'kebersihan_area_genset',
                    'kondisi_knalpot_exhaust'
                ];

                $checklistData = [];
                foreach ($checklistFields as $field) {
                    $val = $request->input($field);
                    $checklistData[$field] = ($val !== null && $val !== '') ? (int)$val : null;
                }

                $inspection->update(array_merge($checklistData, [
                    'no_unit' => $request->no_unit ?? $inspection->no_unit,
                    'shift' => $request->shift,
                    'hours_meter' => $request->hours_meter,
                    'catatan' => $request->catatan,
                ]));
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data P2H berhasil diperbarui',
        ]);
    }

    private function calculatePercentage(string $namaMesin, array $data): float
    {
        $isForklift = str_contains($namaMesin, 'FORKLIFT');
        $isPM = str_contains($namaMesin, 'PALLET MOVER') || str_contains($namaMesin, 'PM');
        $isES = str_contains($namaMesin, 'STACKER') || str_contains($namaMesin, 'STEKER') || str_contains($namaMesin, 'ES');

        $activeType = '';
        if ($isForklift) {
            $activeType = 'forklift';
        } elseif ($isPM) {
            $activeType = 'pm';
        } elseif ($isES) {
            $activeType = 'es';
        }

        if (!$activeType) {
            return 0.0;
        }

        $categories = [];

        if ($activeType === 'forklift') {
            $categories = [
                'fisik' => [
                    'items' => [
                        'body_unit',
                        'lampu_kombinasi_kiri',
                        'lampu_kombinasi_kanan',
                        'lampu_sorot',
                        'lampu_sign_depan_kanan',
                        'lampu_sign_depan_kiri',
                        'kebersihan_unit',
                    ],
                    'weight' => 20,
                    'item_weight' => 2.9,
                ],
                'operational' => [
                    'items' => [
                        'level_oli_hydraulic',
                        'isi_air_aki',
                        'baterai',
                        'hydraulic_system',
                        'selang_hydraulic',
                        'lift_chains',
                        'fork',
                        'baut_roda',
                        'panel_display',
                        'hours_meter',
                        'sistem_kemudi',
                    ],
                    'weight' => 50,
                    'item_weight' => 4.5,
                ],
                'safety' => [
                    'items' => [
                        'klakson',
                        'buzzer_back',
                        'kaca_spion',
                        'ban',
                        'level_minyak_rem',
                    ],
                    'weight' => 30,
                    'item_weight' => 6.0,
                ],
            ];
        } elseif ($activeType === 'pm') {
            $categories = [
                'fisik' => [
                    'items' => [
                        'body_unit',
                        'kebersihan_unit',
                    ],
                    'weight' => 20,
                    'item_weight' => 10.0,
                ],
                'operational' => [
                    'items' => [
                        'isi_air_aki',
                        'baterai',
                        'hydraulic_system',
                        'fork',
                        'baut_roda',
                        'panel_display',
                        'hours_meter',
                        'sistem_kemudi',
                    ],
                    'weight' => 50,
                    'item_weight' => 6.3,
                ],
                'safety' => [
                    'items' => [
                        'klakson',
                        'ban',
                    ],
                    'weight' => 30,
                    'item_weight' => 15.0,
                ],
            ];
        } elseif ($activeType === 'es') {
            $categories = [
                'fisik' => [
                    'items' => [
                        'body_unit',
                        'kebersihan_unit',
                    ],
                    'weight' => 20,
                    'item_weight' => 10.0,
                ],
                'operational' => [
                    'items' => [
                        'isi_air_aki',
                        'baterai',
                        'hydraulic_system',
                        'fork',
                        'baut_roda',
                        'panel_display',
                        'hours_meter',
                        'lift_chains',
                        'sistem_kemudi',
                    ],
                    'weight' => 50,
                    'item_weight' => 5.6,
                ],
                'safety' => [
                    'items' => [
                        'klakson',
                        'ban',
                    ],
                    'weight' => 30,
                    'item_weight' => 15.0,
                ],
            ];
        }

        $totalScore = 0.0;

        foreach ($categories as $cat) {
            $catScore = 0.0;
            $catItemsCount = count($cat['items']);
            $okCount = 0;

            foreach ($cat['items'] as $item) {
                if ($item === 'hours_meter') {
                    if (isset($data['hours_meter']) && $data['hours_meter'] !== '') {
                        $okCount++;
                        $catScore += $cat['item_weight'];
                    }
                } else {
                    if (isset($data[$item]) && ($data[$item] == '1' || $data[$item] === true || $data[$item] === 1)) {
                        $okCount++;
                        $catScore += $cat['item_weight'];
                    }
                }
            }

            if ($okCount === $catItemsCount) {
                $totalScore += $cat['weight'];
            } else {
                $totalScore += $catScore;
            }
        }

        return min(100.00, round($totalScore, 2));
    }

    private function checkHoursMeterValidation($mesinId, $jenisMtc, $tanggal, $shift, $hoursMeter, $ignoreMainId = null)
    {
        if ($hoursMeter === null || $hoursMeter === '') {
            return null;
        }

        $hoursMeter = (float)$hoursMeter;
        $targetDate = \Carbon\Carbon::parse($tanggal)->format('Y-m-d');
        $targetShift = (int)$shift;

        // Query the correct table based on jenisMtc
        if ($jenisMtc === 'Electric P2H' || $jenisMtc === 'Electrical P2H') {
            $query = MtcElectricP2hInspectionModel::where('no_unit', $mesinId)
                ->whereNotNull('hours_meter')
                ->whereHas('main', function ($q) use ($ignoreMainId) {
                    $q->whereIn('jenis_mtc', ['Electric P2H', 'Electrical P2H']);
                    if ($ignoreMainId) {
                        $q->where('id', '!=', $ignoreMainId);
                    }
                });
        } elseif ($jenisMtc === 'Diesel P2H') {
            $query = MtcDieselP2hInspectionModel::where('mesin_id', $mesinId)
                ->whereNotNull('hours_meter')
                ->whereHas('main', function ($q) use ($ignoreMainId) {
                    $q->where('jenis_mtc', 'Diesel P2H');
                    if ($ignoreMainId) {
                        $q->where('id', '!=', $ignoreMainId);
                    }
                });
        } elseif ($jenisMtc === 'Genset P2H') {
            $query = MtcGensetP2hInspectionModel::where('mesin_id', $mesinId)
                ->whereNotNull('hours_meter')
                ->whereHas('main', function ($q) use ($ignoreMainId) {
                    $q->where('jenis_mtc', 'Genset P2H');
                    if ($ignoreMainId) {
                        $q->where('id', '!=', $ignoreMainId);
                    }
                });
        } else {
            return null;
        }

        $records = $query->with('main')->get();

        $priorRecord = null;
        $nextRecord = null;

        foreach ($records as $rec) {
            if (!$rec->main) continue;
            $eDate = \Carbon\Carbon::parse($rec->main->tanggal)->format('Y-m-d');
            $eShift = (int)$rec->shift;
            $eHM = (float)$rec->hours_meter;

            // Check if it is prior
            if ($eDate < $targetDate || ($eDate === $targetDate && $eShift < $targetShift)) {
                if ($priorRecord === null) {
                    $priorRecord = ['date' => $eDate, 'shift' => $eShift, 'hours_meter' => $eHM];
                } else {
                    if ($eDate > $priorRecord['date'] || ($eDate === $priorRecord['date'] && $eShift > $priorRecord['shift'])) {
                        $priorRecord = ['date' => $eDate, 'shift' => $eShift, 'hours_meter' => $eHM];
                    }
                }
            }
            // Check if it is subsequent (next)
            elseif ($eDate > $targetDate || ($eDate === $targetDate && $eShift > $targetShift)) {
                if ($nextRecord === null) {
                    $nextRecord = ['date' => $eDate, 'shift' => $eShift, 'hours_meter' => $eHM];
                } else {
                    if ($eDate < $nextRecord['date'] || ($eDate === $nextRecord['date'] && $eShift < $nextRecord['shift'])) {
                        $nextRecord = ['date' => $eDate, 'shift' => $eShift, 'hours_meter' => $eHM];
                    }
                }
            }
        }

        if ($priorRecord !== null && $hoursMeter < $priorRecord['hours_meter']) {
            $formattedDate = \Carbon\Carbon::parse($priorRecord['date'])->format('d-m-Y');
            return "Hours meter tidak boleh kurang dari data sebelumnya (Shift {$priorRecord['shift']} tanggal {$formattedDate} memiliki nilai {$priorRecord['hours_meter']}).";
        }

        if ($nextRecord !== null && $hoursMeter > $nextRecord['hours_meter']) {
            $formattedDate = \Carbon\Carbon::parse($nextRecord['date'])->format('d-m-Y');
            return "Hours meter tidak boleh melebihi data berikutnya (Shift {$nextRecord['shift']} tanggal {$formattedDate} memiliki nilai {$nextRecord['hours_meter']}).";
        }

        return null;
    }
}
