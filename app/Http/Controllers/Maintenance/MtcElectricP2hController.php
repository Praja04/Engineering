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
use App\Http\Requests\Maintenance\MtcMainRequest;
use App\Models\Maintenance\MtcElectricP2hItemModel;
use App\Http\Requests\Maintenance\MtcElectricP2hRequest;
use App\Models\Maintenance\MtcElectricP2hInspectionModel;

class MtcElectricP2hController extends Controller
{
    public function index()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'Electric P2H')
            ->orderBy('id')->get();

        return view('maintenance.form.electric_p2h', compact('mesin'));
    }

    public function viewData()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'Electrical P2H')
            ->orderBy('id')->get();

        return view('maintenance.data.electric_p2h_data', compact('mesin'));
    }

    public function store(
        MtcMainRequest $mainRequest,
        MtcElectricP2hRequest $detailRequest
    ) {
        $persentase = 0.0;

        DB::transaction(function () use ($mainRequest, $detailRequest, &$persentase) {

            $userId = Auth::id();

            $tanggal = $mainRequest->validated()['tanggal'];
            $shift   = $detailRequest->validated()['shift'];
            $no_unit = $detailRequest->validated()['no_unit'];

            $mesin = MtcMasterMesinModel::find($no_unit);
            $namaMesin = $mesin ? strtoupper($mesin->nama_mesin) : '';

            // CEK DATA SUDAH ADA ATAU BELUM
            $exists = MtcElectricP2hInspectionModel::where('shift', $shift)
                ->whereHas('main', function ($q) use ($tanggal) {
                    $q->where('tanggal', $tanggal)
                        ->where('jenis_mtc', 'Electrical P2H');
                })
                ->exists();

            if ($exists) {
                abort(response()->json([
                    'status'  => false,
                    'message' => 'Data Electric P2H untuk tanggal dan shift tersebut sudah ada.',
                ], 422));
            }

            // Simpan Main
            $main = MtcMainModel::create([
                ...$mainRequest->validated(),
                'jenis_mtc'  => 'Electrical P2H',
                'status'     => 'pending',
                'created_by' => $userId,
            ]);

            $persentase = $this->calculatePercentage($namaMesin, $detailRequest->validated());

            MtcElectricP2hInspectionModel::create([
                ...$detailRequest->validated(),
                'mtc_main_id' => $main->id,
                'persentase'  => $persentase,
            ]);

            $ttdPaths = [
                'teknisi' => 'mtc/ttd/ttd_teknisi.jpeg',  // TTD operator/teknisi
                'staff'   => 'mtc/ttd/ttd_staff.jpeg',     // TTD supervisor
                'user'    => 'mtc/ttd/ttd_user.jpeg',      // TTD user MT/MTC
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
                    'approver_id' => $mainRequest->staff_id,
                    'auto'  => false,
                ],
                [
                    'level' => 3,
                    'role'  => 'user',
                    'approver_id' => $mainRequest->user_id,
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
                        'message'         => 'Maintenance Electric P2H tanggal ' . date('d F Y', strtotime($main->tanggal)) . ' menunggu persetujuan Anda',
                        'url'             => route('mtc.approval.index'),
                        'is_read'         => false,
                    ]);

                    $notificationSent = true;
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data P2H Electric berhasil disimpan',
            'persentase' => $persentase,
        ]);
    }

    public function getData(Request $request)
    {
        $query = MtcMainModel::query()
            ->where('jenis_mtc', 'Electrical P2H')
            ->with([
                'createdBy:id,username',
                'electricP2h.mesin'
            ]);

        // 🔍 filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // 🔍 filter no unit
        if ($request->filled('no_unit')) {
            $query->whereHas('electricP2h', function ($q) use ($request) {
                $q->where('no_unit', 'like', '%' . $request->no_unit . '%');
            });
        }

        // 🔍 filter shift
        if ($request->filled('shift')) {
            $query->whereHas('electricP2h', function ($q) use ($request) {
                $q->where('shift', 'like', '%' . $request->shift . '%');
            });
        }

        // 🔍 filter departemen
        if ($request->filled('departemen')) {
            $query->where('departemen', 'like', '%' . $request->departemen . '%');
        }

        // 🔥 total setelah filter
        $total = $query->count();

        // 🔥 ambil data sesuai DataTables
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

    public function update(
        MtcMainRequest $mainRequest,
        MtcElectricP2hRequest $detailRequest,
        $id
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $id) {

            $userId = Auth::id();

            $tanggal = $mainRequest->validated()['tanggal'];
            $shift   = $detailRequest->validated()['shift'];
            $no_unit = $detailRequest->validated()['no_unit'];

            $mesin = MtcMasterMesinModel::find($no_unit);
            $namaMesin = $mesin ? strtoupper($mesin->nama_mesin) : '';

            $main = MtcMainModel::findOrFail($id);
            $inspection = MtcElectricP2hInspectionModel::where('mtc_main_id', $main->id)->firstOrFail();

            // CEK DATA SUDAH ADA ATAU BELUM (abaikan jika itu row ini sendiri)
            $exists = MtcElectricP2hInspectionModel::where('shift', $shift)
                ->where('id', '!=', $inspection->id)
                ->whereHas('main', function ($q) use ($tanggal) {
                    $q->where('tanggal', $tanggal)
                        ->where('jenis_mtc', 'Electrical P2H');
                })
                ->exists();

            if ($exists) {
                abort(response()->json([
                    'status'  => false,
                    'message' => 'Data Electric P2H untuk tanggal dan shift tersebut sudah ada.',
                ], 422));
            }

            $main->update([
                ...$mainRequest->validated(),
                'updated_by' => $userId,
            ]);

            $persentase = $this->calculatePercentage($namaMesin, $detailRequest->validated());

            $inspection->update([
                ...$detailRequest->validated(),
                'persentase' => $persentase,
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Data P2H Electric berhasil diperbarui',
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
}
