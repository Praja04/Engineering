<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\Utility\WwtpDailyApproval;
use App\Models\Utility\WwtpEffluent;
use App\Models\Utility\WwtpInfluent;
use App\Models\Utility\WwtpInfluentHarian;
use App\Models\Utility\WwtpRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WWTPControllerProses extends Controller
{
    //
    public function proses()
    {
        return view('utility.wwtp.proses');
    }

    //form wwtp proses

    public function form_proses()
    {
        return view('utility.wwtp.form_proses');
    }

    //data wwtp proses
    public function data_proses()
    {
        return view('utility.wwtp.data_proses');
    }

    /**
     * Menampilkan semua record WWTP
     */

    public function index(Request $request)
    {
        $query = WwtpRecord::with(['influent', 'effluent'])
            ->orderBy('tanggal', 'desc');

        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->bulan) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$request->bulan]);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('tanggal', 'like', "%{$request->search}%")
                    ->orWhere('kategori', 'like', "%{$request->search}%");
            });
        }

        return response()->json(
            $query->paginate($request->input('per_page', 10))
        );
    }

    /**
     * Menyimpan data WWTP (influent / effluent)
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'  => 'required|date',
            'kategori' => 'required|in:influent,effluent',
        ]);

        if ($request->kategori === 'influent') {
            return $this->storeInfluent($request);
        } else {
            return $this->storeEffluent($request);
        }
    }

    /**
     * Simpan data kategori INFLUENT
     */
    private function storeInfluent(Request $request)
    {
        $request->validate([
            'pit_sparta'   => 'required|numeric|min:0',
            'pit_garam'    => 'required|numeric|min:0',
            'pit_domestik' => 'required|numeric|min:0',
            'pit_produksi_step3' => 'nullable|numeric|min:0',
            'pit_storage' => 'nullable|numeric|min:0',
            'pit_proses_wwtp2' => 'nullable|numeric|min:0',
            'pit_outlet' => 'nullable|numeric|min:0',
            'pit_boiler' => 'nullable|numeric|min:0',
        ]);

        // Cek apakah minggu ini sudah ada data influent
        $tanggal = Carbon::parse($request->tanggal);
        $startWeek = $tanggal->copy()->startOfWeek();
        $endWeek   = $tanggal->copy()->endOfWeek();

        $existing = WwtpRecord::where('kategori', 'influent')
            ->whereBetween('tanggal', [$startWeek, $endWeek])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Input influent minggu ini sudah ada.'
            ], 409);
        }

        // Simpan header
        $record = WwtpRecord::create([
            'tanggal'  => $request->tanggal,
            'kategori' => 'influent',
        ]);

        // Simpan detail
        WwtpInfluent::create([
            'wwtp_record_id' => $record->id,
            'pit_sparta'     => $request->pit_sparta,
            'pit_garam'      => $request->pit_garam,
            'pit_domestik'   => $request->pit_domestik,
            'pit_produksi_step3' => $request->pit_produksi_step3,
            'pit_storage' => $request->pit_storage,
            'pit_proses_wwtp2' => $request->pit_proses_wwtp2,
            'pit_outlet' => $request->pit_outlet,
            'pit_boiler' => $request->pit_boiler,
        ]);

        return response()->json([
            'message' => 'Data influent berhasil disimpan.',
            'data'    => $record->load('influent')
        ]);
    }

    /**
     * Simpan data kategori EFFLUENT
     */
    private function storeEffluent(Request $request)
    {
        $request->validate([
            'full_proses' => 'required|numeric|min:0',
            'daf_pre'     => 'required|numeric|min:0',
        ]);

        // Buat header
        $record = WwtpRecord::create([
            'tanggal'  => $request->tanggal,
            'kategori' => 'effluent',
        ]);

        // Simpan detail
        WwtpEffluent::create([
            'wwtp_record_id' => $record->id,
            'full_proses'    => $request->full_proses,
            'daf_pre'        => $request->daf_pre,
        ]);

        return response()->json([
            'message' => 'Data effluent berhasil disimpan.',
            'data'    => $record->load('effluent')
        ]);
    }



    /**
     * Simpan data kategori Influent harian
     */


    /**
     * API: Get previous influent harian reading for _awal fields in form
     */
    public function getPreviousData(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'shift'   => 'required|in:shift1,shift2,shift3',
        ]);

        $tanggal = $request->tanggal;
        $shift   = $request->shift;

        if ($shift !== 'shift1') {
            $previous = WwtpInfluentHarian::where('tanggal', $tanggal)
                ->where('shift', '<', $shift)
                ->orderByRaw("CASE 
                    WHEN shift = 'shift3' THEN 3 
                    WHEN shift = 'shift2' THEN 2 
                    WHEN shift = 'shift1' THEN 1 
                    ELSE 0 
                END DESC")
                ->first();

            if (!$previous) {
                $previous = WwtpInfluentHarian::where('tanggal', '<', $tanggal)
                    ->orderBy('tanggal', 'desc')
                    ->orderByRaw("CASE 
                        WHEN shift = 'shift3' THEN 3 
                        WHEN shift = 'shift2' THEN 2 
                        WHEN shift = 'shift1' THEN 1 
                        ELSE 0 
                    END DESC")
                    ->first();
            }
        } else {
            $previous = WwtpInfluentHarian::where('tanggal', '<', $tanggal)
                ->orderBy('tanggal', 'desc')
                ->orderByRaw("CASE 
                    WHEN shift = 'shift3' THEN 3 
                    WHEN shift = 'shift2' THEN 2 
                    WHEN shift = 'shift1' THEN 1 
                    ELSE 0 
                END DESC")
                ->first();
        }

        return response()->json([
            'pit_sparta_awal'         => $previous ? ($previous->pit_sparta !== null ? (float) $previous->pit_sparta : ($previous->pit_sparta_awal !== null ? (float) $previous->pit_sparta_awal : 0)) : 0,
            'pit_garam_awal'          => $previous ? ($previous->pit_garam !== null ? (float) $previous->pit_garam : ($previous->pit_garam_awal !== null ? (float) $previous->pit_garam_awal : 0)) : 0,
            'pit_domestik_awal'       => $previous ? ($previous->pit_domestik !== null ? (float) $previous->pit_domestik : ($previous->pit_domestik_awal !== null ? (float) $previous->pit_domestik_awal : 0)) : 0,
            'pit_produksi_step3_awal' => $previous ? ($previous->pit_produksi_step3 !== null ? (float) $previous->pit_produksi_step3 : ($previous->pit_produksi_step3_awal !== null ? (float) $previous->pit_produksi_step3_awal : 0)) : 0,
            'pit_storage_awal'        => $previous ? ($previous->pit_storage !== null ? (float) $previous->pit_storage : ($previous->pit_storage_awal !== null ? (float) $previous->pit_storage_awal : 0)) : 0,
            'pit_proses_wwtp2_awal'   => $previous ? ($previous->pit_proses_wwtp2 !== null ? (float) $previous->pit_proses_wwtp2 : ($previous->pit_proses_wwtp2_awal !== null ? (float) $previous->pit_proses_wwtp2_awal : 0)) : 0,
            'pit_outlet_awal'         => $previous ? ($previous->pit_outlet !== null ? (float) $previous->pit_outlet : ($previous->pit_outlet_awal !== null ? (float) $previous->pit_outlet_awal : 0)) : 0,
            'pit_boiler_awal'         => $previous ? ($previous->pit_boiler !== null ? (float) $previous->pit_boiler : ($previous->pit_boiler_awal !== null ? (float) $previous->pit_boiler_awal : 0)) : 0,
        ]);
    }

    public function getFilledShifts(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $shifts = WwtpInfluentHarian::where('tanggal', $request->tanggal)
            ->pluck('shift')
            ->toArray();

        return response()->json([
            'success' => true,
            'filled_shifts' => $shifts
        ]);
    }

    private function checkDailyApprovalPermission($tanggal, $action)
    {
        $user = Auth::user();
        $jabatan = $user ? strtolower($user->jabatan) : null;

        $approval = WwtpDailyApproval::where('tanggal', $tanggal)->first();
        if ($approval) {
            if ($approval->status === 'approved_supervisor') {
                if (!in_array($jabatan, ['supervisor', 'admin', 'dept_head'])) {
                    return 'Laporan harian untuk tanggal ini sudah disetujui Supervisor. Hanya Supervisor yang dapat ' . $action . ' data.';
                }
            } elseif ($approval->status === 'approved_foreman') {
                if (!in_array($jabatan, ['foreman', 'supervisor', 'admin', 'dept_head'])) {
                    return 'Laporan harian untuk tanggal ini sudah disetujui Foreman. Operator tidak dapat ' . $action . ' data.';
                }
            }
        }
        return null;
    }

    public function storeinfluentHarian(Request $request)
    {
        $err = $this->checkDailyApprovalPermission($request->tanggal, 'menambah');
        if ($err) {
            return response()->json(['message' => $err], 403);
        }

        $approval = WwtpDailyApproval::where('tanggal', $request->tanggal)->first();

        if (!$approval) {
            $request->validate([
                'foreman_id' => 'required|exists:users,id',
                'supervisor_id' => 'required|exists:users,id',
            ]);
        }

        $request->validate([
            'tanggal'  => 'required|date',
            'shift'    => 'required|in:shift1,shift2,shift3',
            'pit_sparta'   => 'required|numeric|min:0',
            'pit_garam'    => 'required|numeric|min:0',
            'pit_domestik' => 'required|numeric|min:0',
            'pit_produksi_step3' => 'nullable|numeric|min:0',
            'pit_storage' => 'nullable|numeric|min:0',
            'pit_proses_wwtp2' => 'nullable|numeric|min:0',
            'pit_outlet' => 'nullable|numeric|min:0',
            'pit_boiler' => 'nullable|numeric|min:0',
            'debit1' => 'nullable|numeric|min:0',
            'running_wwtp1' => 'nullable|string',
            'debit2' => 'nullable|numeric|min:0',
            'running_wwtp2' => 'nullable|string',

        ]);

        // Cek apakah shift pada tanggal tersebut sudah ada
        $existing = WwtpInfluentHarian::where('tanggal', $request->tanggal)
            ->where('shift', $request->shift)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Data untuk shift ini pada tanggal tersebut sudah ada. Setiap tanggal hanya boleh memiliki maksimal 3 shift (shift1, shift2, shift3).'
            ], 409);
        }

        // Cek jumlah shift pada tanggal tersebut (maksimal 3)
        $shiftCount = WwtpInfluentHarian::where('tanggal', $request->tanggal)->count();

        if ($shiftCount >= 3) {
            return response()->json([
                'message' => 'Tanggal ini sudah memiliki 3 shift. Tidak dapat menambah data lagi.'
            ], 409);
        }

        // Find preceding record
        $preceding = null;
        if ($request->shift !== 'shift1') {
            $preceding = WwtpInfluentHarian::where('tanggal', $request->tanggal)
                ->where('shift', '<', $request->shift)
                ->orderByRaw("CASE 
                    WHEN shift = 'shift3' THEN 3 
                    WHEN shift = 'shift2' THEN 2 
                    WHEN shift = 'shift1' THEN 1 
                    ELSE 0 
                END DESC")
                ->first();

            if (!$preceding) {
                $preceding = WwtpInfluentHarian::where('tanggal', '<', $request->tanggal)
                    ->orderBy('tanggal', 'desc')
                    ->orderByRaw("CASE 
                        WHEN shift = 'shift3' THEN 3 
                        WHEN shift = 'shift2' THEN 2 
                        WHEN shift = 'shift1' THEN 1 
                        ELSE 0 
                    END DESC")
                    ->first();
            }
        } else {
            $preceding = WwtpInfluentHarian::where('tanggal', '<', $request->tanggal)
                ->orderBy('tanggal', 'desc')
                ->orderByRaw("CASE 
                    WHEN shift = 'shift3' THEN 3 
                    WHEN shift = 'shift2' THEN 2 
                    WHEN shift = 'shift1' THEN 1 
                    ELSE 0 
                END DESC")
                ->first();
        }

        // Simpan data harian
        $harian = WwtpInfluentHarian::create([
            'tanggal' => $request->tanggal,
            'shift'   => $request->shift,
            'pit_sparta'     => $request->pit_sparta,
            'pit_sparta_awal' => $request->has('pit_sparta_awal') && $request->pit_sparta_awal !== null ? (float) $request->pit_sparta_awal : ($preceding ? ($preceding->pit_sparta !== null ? (float) $preceding->pit_sparta : ($preceding->pit_sparta_awal !== null ? (float) $preceding->pit_sparta_awal : 0)) : (float) $request->pit_sparta),
            'pit_garam'      => $request->pit_garam,
            'pit_garam_awal' => $request->has('pit_garam_awal') && $request->pit_garam_awal !== null ? (float) $request->pit_garam_awal : ($preceding ? ($preceding->pit_garam !== null ? (float) $preceding->pit_garam : ($preceding->pit_garam_awal !== null ? (float) $preceding->pit_garam_awal : 0)) : (float) $request->pit_garam),
            'pit_domestik'   => $request->pit_domestik,
            'pit_domestik_awal' => $request->has('pit_domestik_awal') && $request->pit_domestik_awal !== null ? (float) $request->pit_domestik_awal : ($preceding ? ($preceding->pit_domestik !== null ? (float) $preceding->pit_domestik : ($preceding->pit_domestik_awal !== null ? (float) $preceding->pit_domestik_awal : 0)) : (float) $request->pit_domestik),
            'pit_produksi_step3' => $request->pit_produksi_step3,
            'pit_produksi_step3_awal' => $request->has('pit_produksi_step3_awal') && $request->pit_produksi_step3_awal !== null ? (float) $request->pit_produksi_step3_awal : ($preceding ? ($preceding->pit_produksi_step3 !== null ? (float) $preceding->pit_produksi_step3 : ($preceding->pit_produksi_step3_awal !== null ? (float) $preceding->pit_produksi_step3_awal : 0)) : (float) ($request->pit_produksi_step3 ?? 0)),
            'pit_storage' => $request->pit_storage,
            'pit_storage_awal' => $request->has('pit_storage_awal') && $request->pit_storage_awal !== null ? (float) $request->pit_storage_awal : ($preceding ? ($preceding->pit_storage !== null ? (float) $preceding->pit_storage : ($preceding->pit_storage_awal !== null ? (float) $preceding->pit_storage_awal : 0)) : (float) ($request->pit_storage ?? 0)),
            'pit_proses_wwtp2' => $request->pit_proses_wwtp2,
            'pit_proses_wwtp2_awal' => $request->has('pit_proses_wwtp2_awal') && $request->pit_proses_wwtp2_awal !== null ? (float) $request->pit_proses_wwtp2_awal : ($preceding ? ($preceding->pit_proses_wwtp2 !== null ? (float) $preceding->pit_proses_wwtp2 : ($preceding->pit_proses_wwtp2_awal !== null ? (float) $preceding->pit_proses_wwtp2_awal : 0)) : (float) ($request->pit_proses_wwtp2 ?? 0)),
            'pit_outlet' => $request->pit_outlet,
            'pit_outlet_awal' => $request->has('pit_outlet_awal') && $request->pit_outlet_awal !== null ? (float) $request->pit_outlet_awal : ($preceding ? ($preceding->pit_outlet !== null ? (float) $preceding->pit_outlet : ($preceding->pit_outlet_awal !== null ? (float) $preceding->pit_outlet_awal : 0)) : (float) ($request->pit_outlet ?? 0)),
            'pit_boiler' => $request->pit_boiler,
            'pit_boiler_awal' => $request->has('pit_boiler_awal') && $request->pit_boiler_awal !== null ? (float) $request->pit_boiler_awal : ($preceding ? ($preceding->pit_boiler !== null ? (float) $preceding->pit_boiler : ($preceding->pit_boiler_awal !== null ? (float) $preceding->pit_boiler_awal : 0)) : (float) ($request->pit_boiler ?? 0)),
            'debit1' => $request->debit1,
            'running_wwtp1' => $request->running_wwtp1,
            'debit2' => $request->debit2,
            'running_wwtp2' => $request->running_wwtp2,
        ]);

        WwtpInfluentHarian::recalculateAwalFieldsFrom($harian->tanggal, $harian->id);

        // Create or update daily approval
        $approval = WwtpDailyApproval::where('tanggal', $request->tanggal)->first();
        if (!$approval) {
            $approval = WwtpDailyApproval::create([
                'tanggal' => $request->tanggal,
                'operator_id' => Auth::id(),
                'foreman_id' => $request->foreman_id,
                'supervisor_id' => $request->supervisor_id,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Notify foreman
            NotificationsModel::create([
                'user_id' => $request->foreman_id,
                'title' => 'Approval Harian WWTP',
                'message' => 'Data harian WWTP tanggal ' . Carbon::parse($request->tanggal)->format('d/m/Y') . ' telah diajukan dan menunggu persetujuan Anda.',
                'url' => url('/wwtp/approval'),
                'notifiable_type' => WwtpDailyApproval::class,
                'notifiable_id' => $approval->id,
                'is_read' => 0,
            ]);
        } else {
            if ($approval->status === 'rejected') {
                $approval->update([
                    'status' => 'submitted',
                    'reject_reason' => null,
                    'operator_id' => Auth::id(),
                    'submitted_at' => now(),
                ]);

                // Re-notify foreman
                if ($approval->foreman_id) {
                    NotificationsModel::updateOrCreate(
                        [
                            'user_id' => $approval->foreman_id,
                            'notifiable_type' => WwtpDailyApproval::class,
                            'notifiable_id' => $approval->id,
                        ],
                        [
                            'title' => 'Approval Harian WWTP',
                            'message' => 'Data harian WWTP tanggal ' . Carbon::parse($request->tanggal)->format('d/m/Y') . ' telah diperbarui dan menunggu persetujuan Anda.',
                            'url' => url('/wwtp/approval'),
                            'is_read' => 0,
                        ]
                    );
                }
            }
        }

        return response()->json([
            'message' => 'Data influent harian berhasil disimpan.',
            'data'    => $harian->fresh()
        ]);
    }



    /**
     * Menampilkan detail record
     */
    public function show($id)
    {
        $data = WwtpRecord::with(['influent', 'effluent'])->findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update record WWTP
     */
    public function update(Request $request, $id)
    {
        $record = WwtpRecord::with(['influent', 'effluent'])->findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
        ]);

        // Jika kategori influent → tetap jaga rule perminggu
        if ($record->kategori === 'influent') {

            $request->validate([
                'pit_sparta'   => 'required|numeric|min:0',
                'pit_garam'    => 'required|numeric|min:0',
                'pit_domestik' => 'required|numeric|min:0',
            ]);

            // Cek apakah tanggal barunya masuk minggu yang sudah ada data lain
            $tanggal = Carbon::parse($request->tanggal);
            $startWeek = $tanggal->copy()->startOfWeek();
            $endWeek   = $tanggal->copy()->endOfWeek();

            $existing = WwtpRecord::where('kategori', 'influent')
                ->whereBetween('tanggal', [$startWeek, $endWeek])
                ->where('id', '!=', $id)  // abaikan data dirinya sendiri
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Input influent minggu ini sudah ada.'
                ], 409);
            }

            // Update header
            $record->update([
                'tanggal' => $request->tanggal,
            ]);

            // Update detail
            $record->influent->update([
                'pit_sparta'   => $request->pit_sparta,
                'pit_garam'    => $request->pit_garam,
                'pit_domestik' => $request->pit_domestik,
            ]);
        }

        // Jika kategori effluent
        elseif ($record->kategori === 'effluent') {

            $request->validate([
                'full_proses' => 'required|numeric|min:0',
                'daf_pre'     => 'required|numeric|min:0',
            ]);

            // Update header
            $record->update([
                'tanggal' => $request->tanggal,
            ]);

            // Update detail
            $record->effluent->update([
                'full_proses' => $request->full_proses,
                'daf_pre'     => $request->daf_pre,
            ]);
        }

        return response()->json([
            'message' => 'Data berhasil diperbarui.',
            'data' => $record->fresh(['influent', 'effluent'])
        ]);
    }

    /**
     * Hapus record
     */
    public function destroy($id)
    {
        $record = WwtpRecord::findOrFail($id);
        $record->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }



    //harian
    /**
     * Menampilkan semua data harian
     */
    public function indexHarian(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page    = $request->input('page', 1);
        $bulan   = $request->input('bulan');

        $query = WwtpInfluentHarian::orderBy('tanggal', 'desc')
            ->orderBy('shift', 'asc');

        if ($bulan) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$request->bulan]);
        }

        $data = $query->paginate($perPage, ['*'], 'page', $page);

        $dates = $data->pluck('tanggal')->unique()->toArray();
        $approvals = WwtpDailyApproval::whereIn('tanggal', $dates)->get()->keyBy(function($item) {
            return \Carbon\Carbon::parse($item->tanggal)->toDateString();
        });

        $data->getCollection()->transform(function ($item) use ($approvals) {
            $dateKey = \Carbon\Carbon::parse($item->tanggal)->toDateString();
            $approval = $approvals->get($dateKey);
            $item->approval_status = $approval ? $approval->status : null;
            return $item;
        });

        return response()->json($data);
    }
    /**
     * Menampilkan detail data harian
     */
    public function showHarian($id)
    {
        $data = WwtpInfluentHarian::findOrFail($id);
        $approval = WwtpDailyApproval::where('tanggal', $data->tanggal)->first();
        $data->approval_status = $approval ? $approval->status : null;
        return response()->json($data);
    }

    /**
     * Update data harian
     */
    public function updateHarian(Request $request, $id)
    {
        $harian = WwtpInfluentHarian::findOrFail($id);

        $err = $this->checkDailyApprovalPermission($request->tanggal, 'mengubah');
        if ($err) {
            return response()->json(['message' => $err], 403);
        }

        $err = $this->checkDailyApprovalPermission($harian->tanggal, 'mengubah');
        if ($err) {
            return response()->json(['message' => $err], 403);
        }

        $request->validate([
            'tanggal'  => 'required|date',
            'shift'    => 'required|in:shift1,shift2,shift3',
            'pit_sparta'   => 'required|numeric|min:0',
            'pit_garam'    => 'required|numeric|min:0',
            'pit_domestik' => 'required|numeric|min:0',
            'pit_produksi_step3' => 'nullable|numeric|min:0',
            'pit_storage' => 'nullable|numeric|min:0',
            'pit_proses_wwtp2' => 'nullable|numeric|min:0',
            'pit_outlet' => 'nullable|numeric|min:0',
            'pit_boiler' => 'nullable|numeric|min:0',
            'debit1' => 'nullable|numeric|min:0',
            'running_wwtp1' => 'nullable|string',
            'debit2' => 'nullable|numeric|min:0',
            'running_wwtp2' => 'nullable|string',
        ]);

        $approval = \App\Models\Utility\WwtpDailyApproval::where('tanggal', $request->tanggal)->first();

        $oldDate = $harian->tanggal;

        // Find preceding record for new date/shift (excluding this record itself)
        $preceding = null;
        if ($request->shift !== 'shift1') {
            $preceding = WwtpInfluentHarian::where('tanggal', $request->tanggal)
                ->where('shift', '<', $request->shift)
                ->where('id', '!=', $id)
                ->orderByRaw("CASE 
                    WHEN shift = 'shift3' THEN 3 
                    WHEN shift = 'shift2' THEN 2 
                    WHEN shift = 'shift1' THEN 1 
                    ELSE 0 
                END DESC")
                ->first();

            if (!$preceding) {
                $preceding = WwtpInfluentHarian::where('tanggal', '<', $request->tanggal)
                    ->where('id', '!=', $id)
                    ->orderBy('tanggal', 'desc')
                    ->orderByRaw("CASE 
                        WHEN shift = 'shift3' THEN 3 
                        WHEN shift = 'shift2' THEN 2 
                        WHEN shift = 'shift1' THEN 1 
                        ELSE 0 
                    END DESC")
                    ->first();
            }
        } else {
            $preceding = WwtpInfluentHarian::where('tanggal', '<', $request->tanggal)
                ->where('id', '!=', $id)
                ->orderBy('tanggal', 'desc')
                ->orderByRaw("CASE 
                    WHEN shift = 'shift3' THEN 3 
                    WHEN shift = 'shift2' THEN 2 
                    WHEN shift = 'shift1' THEN 1 
                    ELSE 0 
                END DESC")
                ->first();
        }

        $data = $request->all();
        $fields = [
            'pit_sparta',
            'pit_garam',
            'pit_domestik',
            'pit_produksi_step3',
            'pit_storage',
            'pit_proses_wwtp2',
            'pit_outlet',
            'pit_boiler'
        ];
        foreach ($fields as $field) {
            $awalField = $field . '_awal';
            if ($request->has($awalField) && $request->$awalField !== null) {
                $data[$awalField] = (float) $request->$awalField;
            } else {
                $data[$awalField] = $preceding ? ($preceding->$field !== null ? (float) $preceding->$field : ($preceding->$awalField !== null ? (float) $preceding->$awalField : 0)) : 0;
            }
        }

        $harian->update($data);

        $newDate = $harian->tanggal;
        $startDate = $oldDate < $newDate ? $oldDate : $newDate;
        WwtpInfluentHarian::recalculateAwalFieldsFrom($startDate, $harian->id);

        // If daily approval exists and is rejected, reset it to submitted
        if ($approval && $approval->status === 'rejected') {
            $approval->update([
                'status' => 'submitted',
                'reject_reason' => null,
                'operator_id' => \Illuminate\Support\Facades\Auth::id(),
                'submitted_at' => now(),
            ]);

            if ($approval->foreman_id) {
                \App\Models\NotificationsModel::updateOrCreate(
                    [
                        'user_id' => $approval->foreman_id,
                        'notifiable_type' => \App\Models\Utility\WwtpDailyApproval::class,
                        'notifiable_id' => $approval->id,
                    ],
                    [
                        'title' => 'Approval Harian WWTP',
                        'message' => 'Data harian WWTP tanggal ' . Carbon::parse($request->tanggal)->format('d/m/Y') . ' telah diperbarui dan menunggu persetujuan Anda.',
                        'url' => url('/wwtp/approval'),
                        'is_read' => 0,
                    ]
                );
            }
        }

        return response()->json([
            'message' => 'Data harian berhasil diperbarui.',
            'data' => $harian->fresh()
        ]);
    }

    /**
     * Hapus data harian
     */
    public function destroyHarian($id)
    {
        $harian = WwtpInfluentHarian::findOrFail($id);

        $err = $this->checkDailyApprovalPermission($harian->tanggal, 'menghapus');
        if ($err) {
            return response()->json(['message' => $err], 403);
        }

        $oldDate = $harian->tanggal;
        $harian->delete();

        WwtpInfluentHarian::recalculateAwalFieldsFrom($oldDate);

        return response()->json(['message' => 'Data harian berhasil dihapus.']);
    }


    /**
     * API: Get dashboard statistics
     */
    // =============================
    // DASHBOARD API - DATA MINGGUAN
    // =============================

    /**
     * API: Get dashboard statistics (MINGGUAN)
     */
    public function getStatistics()
    {
        $totalRecords = WwtpRecord::count();
        $totalInfluent = WwtpRecord::where('kategori', 'influent')->count();
        $totalEffluent = WwtpRecord::where('kategori', 'effluent')->count();

        $lastUpdate = WwtpRecord::orderBy('tanggal', 'desc')->first();

        $startWeek = Carbon::now()->startOfWeek();
        $endWeek = Carbon::now()->endOfWeek();

        $weeklyInfluent = WwtpRecord::where('kategori', 'influent')
            ->whereBetween('tanggal', [$startWeek, $endWeek])
            ->with('influent')
            ->first();

        $weeklyEffluentCount = WwtpRecord::where('kategori', 'effluent')
            ->whereBetween('tanggal', [$startWeek, $endWeek])
            ->count();

        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        $monthlyInfluentAvg = WwtpRecord::where('kategori', 'influent')
            ->whereBetween('tanggal', [$startMonth, $endMonth])
            ->with('influent')
            ->get()
            ->avg(function ($record) {
                if ($record->influent) {
                    return $record->influent->pit_sparta +
                        $record->influent->pit_garam +
                        $record->influent->pit_domestik +
                        $record->influent->pit_produksi_step3 +
                        $record->influent->pit_storage +
                        $record->influent->pit_proses_wwtp2 +
                        $record->influent->pit_outlet +
                        $record->influent->pit_boiler;
                }
                return 0;
            });

        $monthlyEffluentAvg = WwtpRecord::where('kategori', 'effluent')
            ->whereBetween('tanggal', [$startMonth, $endMonth])
            ->with('effluent')
            ->get()
            ->avg(function ($record) {
                if ($record->effluent) {
                    return $record->effluent->full_proses +
                        $record->effluent->daf_pre;
                }
                return 0;
            });

        return response()->json([
            'total_records' => $totalRecords,
            'total_influent' => $totalInfluent,
            'total_effluent' => $totalEffluent,
            'last_update' => $lastUpdate ? $lastUpdate->tanggal : null,
            'weekly_influent' => $weeklyInfluent,
            'weekly_effluent_count' => $weeklyEffluentCount,
            'monthly_influent_avg' => round($monthlyInfluentAvg, 2),
            'monthly_effluent_avg' => round($monthlyEffluentAvg, 2),
        ]);
    }

    /**
     * API: Get chart data for influent (MINGGUAN)
     */
    public function getInfluentChartData(Request $request)
    {
        // Jika ada start_date dan end_date dari request, gunakan itu
        // Jika tidak, default ke awal dan akhir bulan
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now()->endOfMonth();

        $data = WwtpRecord::where('kategori', 'influent')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->with('influent')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($record) {
                return [
                    'tanggal' => $record->tanggal,
                    'pit_sparta' => $record->influent->pit_sparta ?? 0,
                    'pit_garam' => $record->influent->pit_garam ?? 0,
                    'pit_domestik' => $record->influent->pit_domestik ?? 0,
                    'pit_produksi_step3' => $record->influent->pit_produksi_step3 ?? 0,
                    'pit_storage' => $record->influent->pit_storage ?? 0,
                    'pit_proses_wwtp2' => $record->influent->pit_proses_wwtp2 ?? 0,
                    'pit_outlet' => $record->influent->pit_outlet ?? 0,
                    'pit_boiler' => $record->influent->pit_boiler ?? 0,
                    'total' => ($record->influent->pit_sparta ?? 0) +
                        ($record->influent->pit_garam ?? 0) +
                        ($record->influent->pit_domestik ?? 0),
                ];
            });

        return response()->json($data);
    }

    /**
     * API: Get chart data for effluent (MINGGUAN)
     */
    public function getEffluentChartData(Request $request)
    {
        // Jika ada start_date dan end_date dari request, gunakan itu
        // Jika tidak, default ke awal dan akhir bulan
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now()->endOfMonth();

        $data = WwtpRecord::where('kategori', 'effluent')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->with('effluent')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($record) {
                return [
                    'tanggal' => $record->tanggal,
                    'full_proses' => $record->effluent->full_proses ?? 0,
                    'daf_pre' => $record->effluent->daf_pre ?? 0,
                    'total' => ($record->effluent->full_proses ?? 0) +
                        ($record->effluent->daf_pre ?? 0),
                ];
            });

        return response()->json($data);
    }

    /**
     * API: Get monthly comparison (MINGGUAN)
     */
    public function getMonthlyComparison()
    {
        $months = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startMonth = $date->copy()->startOfMonth();
            $endMonth = $date->copy()->endOfMonth();

            $influentTotal = WwtpRecord::where('kategori', 'influent')
                ->whereBetween('tanggal', [$startMonth, $endMonth])
                ->with('influent')
                ->get()
                ->sum(function ($record) {
                    if ($record->influent) {
                        return $record->influent->pit_sparta +
                            $record->influent->pit_garam +
                            $record->influent->pit_domestik +
                            $record->influent->pit_produksi_step3 +
                            $record->influent->pit_storage +
                            $record->influent->pit_proses_wwtp2 +
                            $record->influent->pit_outlet +
                            $record->influent->pit_boiler;
                    }
                    return 0;
                });

            $effluentTotal = WwtpRecord::where('kategori', 'effluent')
                ->whereBetween('tanggal', [$startMonth, $endMonth])
                ->with('effluent')
                ->get()
                ->sum(function ($record) {
                    if ($record->effluent) {
                        return $record->effluent->full_proses +
                            $record->effluent->daf_pre;
                    }
                    return 0;
                });

            $months[] = [
                'month' => $date->format('M Y'),
                'influent' => round($influentTotal, 2),
                'effluent' => round($effluentTotal, 2),
            ];
        }

        return response()->json($months);
    }

    /**
     * API: Get recent records (MINGGUAN)
     */
    public function getRecentRecords($limit = 10)
    {
        $data = WwtpRecord::with(['influent', 'effluent'])
            ->orderBy('tanggal', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($data);
    }



    // =============================
    // DASHBOARD API - DATA HARIAN
    // =============================

    /**
     * API: Get dashboard statistics (HARIAN)
     */
    public function getStatisticsHarian()
    {
        $totalRecords = WwtpInfluentHarian::count();

        // Hitung total records per tanggal (1 tanggal bisa punya 3 shift)
        $totalDays = WwtpInfluentHarian::select('tanggal')
            ->distinct()
            ->count();

        $lastUpdate = WwtpInfluentHarian::orderBy('tanggal', 'desc')
            ->orderBy('shift', 'desc')
            ->first();

        // Data hari ini
        $today = Carbon::today();
        $todayRecords = WwtpInfluentHarian::where('tanggal', $today)->count();

        // Data minggu ini
        $startWeek = Carbon::now()->startOfWeek();
        $endWeek = Carbon::now()->endOfWeek();

        $weeklyRecords = WwtpInfluentHarian::whereBetween('tanggal', [$startWeek, $endWeek])
            ->count();        // Data bulan ini - rata-rata per hari
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        $monthlyAvg = WwtpInfluentHarian::whereBetween('tanggal', [$startMonth, $endMonth])
            ->selectRaw('tanggal, 
                SUM(COALESCE(pit_sparta, 0) - COALESCE(pit_sparta_awal, 0)) as total_sparta,
                SUM(COALESCE(pit_garam, 0) - COALESCE(pit_garam_awal, 0)) as total_garam,
                SUM(COALESCE(pit_domestik, 0) - COALESCE(pit_domestik_awal, 0)) as total_domestik,
                SUM(COALESCE(pit_produksi_step3, 0) - COALESCE(pit_produksi_step3_awal, 0)) as total_produksi_step3,
                SUM(COALESCE(pit_storage, 0) - COALESCE(pit_storage_awal, 0)) as total_storage,
                SUM(COALESCE(pit_proses_wwtp2, 0) - COALESCE(pit_proses_wwtp2_awal, 0)) as total_proses_wwtp2,
                SUM(COALESCE(pit_outlet, 0) - COALESCE(pit_outlet_awal, 0)) as total_outlet,
                SUM(COALESCE(pit_boiler, 0) - COALESCE(pit_boiler_awal, 0)) as total_boiler')
            ->groupBy('tanggal')
            ->get()
            ->avg(function ($record) {
                return $record->total_sparta + $record->total_garam + $record->total_domestik +
                    $record->total_produksi_step3 + $record->total_storage + $record->total_proses_wwtp2 +
                    $record->total_outlet + $record->total_boiler;
            });

        return response()->json([
            'total_records' => $totalRecords,
            'total_days' => $totalDays,
            'total_shifts_today' => $todayRecords,
            'total_shifts_this_week' => $weeklyRecords,
            'last_update' => $lastUpdate ? $lastUpdate->tanggal : null,
            'last_shift' => $lastUpdate ? $lastUpdate->shift : null,
            'monthly_avg_per_day' => round($monthlyAvg ?? 0, 2),
        ]);
    }

    /**
     * API: Get chart data for influent harian
     */
    public function getInfluentHarianChartData(Request $request)
    {
        // Ambil start_date dan end_date dari request, default ke awal & akhir bulan
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        // Agregasi data per hari (sum dari semua shift)
        $data = WwtpInfluentHarian::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get()
            ->groupBy('tanggal')
            ->map(function ($records, $date) {
                $totalSparta   = $records->sum(function ($r) { return ($r->pit_sparta ?? 0) - ($r->pit_sparta_awal ?? 0); });
                $totalGaram    = $records->sum(function ($r) { return ($r->pit_garam ?? 0) - ($r->pit_garam_awal ?? 0); });
                $totalDomestik = $records->sum(function ($r) { return ($r->pit_domestik ?? 0) - ($r->pit_domestik_awal ?? 0); });
                $totalProduksiStep3 = $records->sum(function ($r) { return ($r->pit_produksi_step3 ?? 0) - ($r->pit_produksi_step3_awal ?? 0); });
                $totalStorage = $records->sum(function ($r) { return ($r->pit_storage ?? 0) - ($r->pit_storage_awal ?? 0); });
                $totalProsesWwtp2 = $records->sum(function ($r) { return ($r->pit_proses_wwtp2 ?? 0) - ($r->pit_proses_wwtp2_awal ?? 0); });
                $totalOutlet = $records->sum(function ($r) { return ($r->pit_outlet ?? 0) - ($r->pit_outlet_awal ?? 0); });
                $totalBoiler = $records->sum(function ($r) { return ($r->pit_boiler ?? 0) - ($r->pit_boiler_awal ?? 0); });

                return [
                    'tanggal'      => $date,
                    'pit_sparta'   => max(0, $totalSparta),
                    'pit_garam'    => max(0, $totalGaram),
                    'pit_domestik' => max(0, $totalDomestik),
                    'pit_produksi_step3' => max(0, $totalProduksiStep3),
                    'pit_storage' => max(0, $totalStorage),
                    'pit_proses_wwtp2' => max(0, $totalProsesWwtp2),
                    'pit_outlet' => max(0, $totalOutlet),
                    'pit_boiler' => max(0, $totalBoiler),
                    'total'        => max(0, $totalSparta) + max(0, $totalGaram) + max(0, $totalDomestik) + max(0, $totalProduksiStep3) + max(0, $totalStorage) + max(0, $totalProsesWwtp2) + max(0, $totalOutlet) + max(0, $totalBoiler),
                    'shift_count'  => $records->count(),
                ];
            })
            ->values();

        return response()->json($data);
    }

    /**
     * API: Get shift breakdown data (untuk pie chart)
     */
    public function getShiftBreakdownData(Request $request)
    {
        $startDate = $request->query('start_date')
            ? Carbon::parse($request->query('start_date'))->startOfDay()
            : Carbon::now()->startOfDay();

        $endDate = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth(); // Keep dynamic, but use request parameters

        $data = WwtpInfluentHarian::whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('
            SUM(COALESCE(pit_sparta, 0) - COALESCE(pit_sparta_awal, 0)) as total_sparta,
            SUM(COALESCE(pit_garam, 0) - COALESCE(pit_garam_awal, 0)) as total_garam,
            SUM(COALESCE(pit_domestik, 0) - COALESCE(pit_domestik_awal, 0)) as total_domestik,
            SUM(COALESCE(pit_produksi_step3, 0) - COALESCE(pit_produksi_step3_awal, 0)) as total_produksi_step3,
            SUM(COALESCE(pit_storage, 0) - COALESCE(pit_storage_awal, 0)) as total_storage,
            SUM(COALESCE(pit_proses_wwtp2, 0) - COALESCE(pit_proses_wwtp2_awal, 0)) as total_proses_wwtp2,
            SUM(COALESCE(pit_outlet, 0) - COALESCE(pit_outlet_awal, 0)) as total_outlet,
            SUM(COALESCE(pit_boiler, 0) - COALESCE(pit_boiler_awal, 0)) as total_boiler')
            ->first();

        return response()->json([
            'total_sparta'         => max(0, (float) $data->total_sparta),
            'total_garam'          => max(0, (float) $data->total_garam),
            'total_domestik'       => max(0, (float) $data->total_domestik),
            'total_produksi_step3' => max(0, (float) $data->total_produksi_step3),
            'total_storage'        => max(0, (float) $data->total_storage),
            'total_proses_wwtp2'   => max(0, (float) $data->total_proses_wwtp2),
            'total_outlet'         => max(0, (float) $data->total_outlet),
            'total_boiler'         => max(0, (float) $data->total_boiler),
        ]);
    }

    /**
     * API: Get monthly comparison (HARIAN)
     */
    public function getMonthlyComparisonHarian()
    {
        $months = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startMonth = $date->copy()->startOfMonth();
            $endMonth = $date->copy()->endOfMonth();

            $influentTotal = WwtpInfluentHarian::whereBetween('tanggal', [$startMonth, $endMonth])
                ->sum(DB::raw('(COALESCE(pit_sparta, 0) - COALESCE(pit_sparta_awal, 0)) + 
                               (COALESCE(pit_garam, 0) - COALESCE(pit_garam_awal, 0)) + 
                               (COALESCE(pit_domestik, 0) - COALESCE(pit_domestik_awal, 0)) + 
                               (COALESCE(pit_produksi_step3, 0) - COALESCE(pit_produksi_step3_awal, 0)) + 
                               (COALESCE(pit_storage, 0) - COALESCE(pit_storage_awal, 0)) + 
                               (COALESCE(pit_proses_wwtp2, 0) - COALESCE(pit_proses_wwtp2_awal, 0)) + 
                               (COALESCE(pit_outlet, 0) - COALESCE(pit_outlet_awal, 0)) + 
                               (COALESCE(pit_boiler, 0) - COALESCE(pit_boiler_awal, 0))'));

            $months[] = [
                'month' => $date->format('M Y'),
                'influent' => round($influentTotal, 2),
            ];
        }

        return response()->json($months);
    }

    /**
     * API: Get recent records harian (grouped by date)
     */
    public function getRecentRecordsHarian($limit = 10)
    {
        // Ambil tanggal-tanggal terakhir
        $recentDates = WwtpInfluentHarian::select('tanggal')
            ->distinct()
            ->orderBy('tanggal', 'desc')
            ->limit($limit)
            ->pluck('tanggal');

        // Ambil semua data untuk tanggal-tanggal tersebut
        $data = WwtpInfluentHarian::whereIn('tanggal', $recentDates)
            ->orderBy('tanggal', 'desc')
            ->orderBy('shift', 'asc')
            ->get()
            ->groupBy('tanggal')
            ->map(function ($records, $date) {
                $shifts = $records->map(function ($record) {
                    return [
                        'shift' => $record->shift,
                        'pit_sparta' => max(0, ($record->pit_sparta ?? 0) - ($record->pit_sparta_awal ?? 0)),
                        'pit_garam' => max(0, ($record->pit_garam ?? 0) - ($record->pit_garam_awal ?? 0)),
                        'pit_domestik' => max(0, ($record->pit_domestik ?? 0) - ($record->pit_domestik_awal ?? 0)),
                        'pit_produksi_step3' => max(0, ($record->pit_produksi_step3 ?? 0) - ($record->pit_produksi_step3_awal ?? 0)),
                        'pit_storage' => max(0, ($record->pit_storage ?? 0) - ($record->pit_storage_awal ?? 0)),
                        'pit_proses_wwtp2' => max(0, ($record->pit_proses_wwtp2 ?? 0) - ($record->pit_proses_wwtp2_awal ?? 0)),
                        'pit_outlet' => max(0, ($record->pit_outlet ?? 0) - ($record->pit_outlet_awal ?? 0)),
                        'pit_boiler' => max(0, ($record->pit_boiler ?? 0) - ($record->pit_boiler_awal ?? 0)),
                        'debit1' => $record->debit1,
                        'debit2' => $record->debit2,
                    ];
                });

                $totalVolume = $records->sum(function ($record) {
                    return max(0, ($record->pit_sparta ?? 0) - ($record->pit_sparta_awal ?? 0)) +
                        max(0, ($record->pit_garam ?? 0) - ($record->pit_garam_awal ?? 0)) +
                        max(0, ($record->pit_domestik ?? 0) - ($record->pit_domestik_awal ?? 0)) +
                        max(0, ($record->pit_produksi_step3 ?? 0) - ($record->pit_produksi_step3_awal ?? 0)) +
                        max(0, ($record->pit_storage ?? 0) - ($record->pit_storage_awal ?? 0)) +
                        max(0, ($record->pit_proses_wwtp2 ?? 0) - ($record->pit_proses_wwtp2_awal ?? 0)) +
                        max(0, ($record->pit_outlet ?? 0) - ($record->pit_outlet_awal ?? 0)) +
                        max(0, ($record->pit_boiler ?? 0) - ($record->pit_boiler_awal ?? 0));
                });

                return [
                    'tanggal' => $date,
                    'shift_count' => $records->count(),
                    'shifts' => $shifts,
                    'total_volume' => $totalVolume,
                ];
            })
            ->values();

        return response()->json($data);
    }
}
