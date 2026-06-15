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
            'pit_sparta_awal'         => $previous ? ((float) $previous->pit_sparta ?: (float) $previous->pit_sparta_awal ?: 0) : 0,
            'pit_garam_awal'          => $previous ? ((float) $previous->pit_garam ?: (float) $previous->pit_garam_awal ?: 0) : 0,
            'pit_domestik_awal'       => $previous ? ((float) $previous->pit_domestik ?: (float) $previous->pit_domestik_awal ?: 0) : 0,
            'pit_produksi_step3_awal' => $previous ? ((float) $previous->pit_produksi_step3 ?: (float) $previous->pit_produksi_step3_awal ?: 0) : 0,
            'pit_storage_awal'        => $previous ? ((float) $previous->pit_storage ?: (float) $previous->pit_storage_awal ?: 0) : 0,
            'pit_proses_wwtp2_awal'   => $previous ? ((float) $previous->pit_proses_wwtp2 ?: (float) $previous->pit_proses_wwtp2_awal ?: 0) : 0,
            'pit_outlet_awal'         => $previous ? ((float) $previous->pit_outlet ?: (float) $previous->pit_outlet_awal ?: 0) : 0,
            'pit_boiler_awal'         => $previous ? ((float) $previous->pit_boiler ?: (float) $previous->pit_boiler_awal ?: 0) : 0,
        ]);
    }

    public function storeinfluentHarian(Request $request)
    {
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
            'pit_sparta_awal' => $preceding ? ((float) $preceding->pit_sparta ?: (float) $preceding->pit_sparta_awal ?: 0) : (float) $request->pit_sparta,
            'pit_garam'      => $request->pit_garam,
            'pit_garam_awal' => $preceding ? ((float) $preceding->pit_garam ?: (float) $preceding->pit_garam_awal ?: 0) : (float) $request->pit_garam,
            'pit_domestik'   => $request->pit_domestik,
            'pit_domestik_awal' => $preceding ? ((float) $preceding->pit_domestik ?: (float) $preceding->pit_domestik_awal ?: 0) : (float) $request->pit_domestik,
            'pit_produksi_step3' => $request->pit_produksi_step3,
            'pit_produksi_step3_awal' => $preceding ? ((float) $preceding->pit_produksi_step3 ?: (float) $preceding->pit_produksi_step3_awal ?: 0) : (float) ($request->pit_produksi_step3 ?? 0),
            'pit_storage' => $request->pit_storage,
            'pit_storage_awal' => $preceding ? ((float) $preceding->pit_storage ?: (float) $preceding->pit_storage_awal ?: 0) : (float) ($request->pit_storage ?? 0),
            'pit_proses_wwtp2' => $request->pit_proses_wwtp2,
            'pit_proses_wwtp2_awal' => $preceding ? ((float) $preceding->pit_proses_wwtp2 ?: (float) $preceding->pit_proses_wwtp2_awal ?: 0) : (float) ($request->pit_proses_wwtp2 ?? 0),
            'pit_outlet' => $request->pit_outlet,
            'pit_outlet_awal' => $preceding ? ((float) $preceding->pit_outlet ?: (float) $preceding->pit_outlet_awal ?: 0) : (float) ($request->pit_outlet ?? 0),
            'pit_boiler' => $request->pit_boiler,
            'pit_boiler_awal' => $preceding ? ((float) $preceding->pit_boiler ?: (float) $preceding->pit_boiler_awal ?: 0) : (float) ($request->pit_boiler ?? 0),
            'debit1' => $request->debit1,
            'running_wwtp1' => $request->running_wwtp1,
            'debit2' => $request->debit2,
            'running_wwtp2' => $request->running_wwtp2,
        ]);

        WwtpInfluentHarian::recalculateAwalFieldsFrom($harian->tanggal);

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
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan]);
        }

        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($data);
    }
    /**
     * Menampilkan detail data harian
     */
    public function showHarian($id)
    {
        $data = WwtpInfluentHarian::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update data harian
     */
    public function updateHarian(Request $request, $id)
    {
        $harian = WwtpInfluentHarian::findOrFail($id);

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

        // Check daily approval for both new and old date
        $approval = \App\Models\Utility\WwtpDailyApproval::where('tanggal', $request->tanggal)->first();
        if ($approval && in_array($approval->status, ['approved_foreman', 'approved_supervisor'])) {
            return response()->json([
                'message' => 'Laporan harian untuk tanggal ini sudah disetujui, tidak dapat mengubah data.'
            ], 422);
        }

        $oldApproval = \App\Models\Utility\WwtpDailyApproval::where('tanggal', $harian->tanggal)->first();
        if ($oldApproval && in_array($oldApproval->status, ['approved_foreman', 'approved_supervisor'])) {
            return response()->json([
                'message' => 'Laporan harian untuk tanggal awal sudah disetujui, tidak dapat mengubah data.'
            ], 422);
        }

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
            $data[$awalField] = $preceding ? ((float) $preceding->$field ?: (float) $preceding->$awalField ?: 0) : (float) ($request->$field ?? 0);
        }

        $harian->update($data);

        $newDate = $harian->tanggal;
        $startDate = $oldDate < $newDate ? $oldDate : $newDate;
        WwtpInfluentHarian::recalculateAwalFieldsFrom($startDate);

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
        $approval = \App\Models\Utility\WwtpDailyApproval::where('tanggal', $harian->tanggal)->first();
        if ($approval && in_array($approval->status, ['approved_foreman', 'approved_supervisor'])) {
            return response()->json([
                'message' => 'Laporan harian untuk tanggal ini sudah disetujui, tidak dapat dihapus.'
            ], 422);
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
            ->count();

        // Data bulan ini - rata-rata per hari
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        $monthlyAvg = WwtpInfluentHarian::whereBetween('tanggal', [$startMonth, $endMonth])
            ->selectRaw('tanggal, 
                SUM(pit_sparta) as total_sparta,
                SUM(pit_garam) as total_garam,
                SUM(pit_domestik) as total_domestik,
                SUM(pit_produksi_step3) as total_produksi_step3,
                SUM(pit_storage) as total_storage,
                SUM(pit_proses_wwtp2) as total_proses_wwtp2,
                SUM(pit_outlet) as total_outlet,
                SUM(pit_boiler) as total_boiler')
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
                $totalSparta   = $records->sum('pit_sparta');
                $totalGaram    = $records->sum('pit_garam');
                $totalDomestik = $records->sum('pit_domestik');
                $totalProduksiStep3 = $records->sum('pit_produksi_step3');
                $totalStorage = $records->sum('pit_storage');
                $totalProsesWwtp2 = $records->sum('pit_proses_wwtp2');
                $totalOutlet = $records->sum('pit_outlet');
                $totalBoiler = $records->sum('pit_boiler');

                return [
                    'tanggal'      => $date,
                    'pit_sparta'   => $totalSparta,
                    'pit_garam'    => $totalGaram,
                    'pit_domestik' => $totalDomestik,
                    'pit_produksi_step3' => $totalProduksiStep3,
                    'pit_storage' => $totalStorage,
                    'pit_proses_wwtp2' => $totalProsesWwtp2,
                    'pit_outlet' => $totalOutlet,
                    'pit_boiler' => $totalBoiler,
                    'total'        => $totalSparta + $totalGaram + $totalDomestik + $totalProduksiStep3 + $totalStorage + $totalProsesWwtp2 + $totalOutlet + $totalBoiler,
                    'shift_count'  => $records->count(),
                ];
            })
            ->values();

        return response()->json($data);
    }

    /**
     * API: Get shift breakdown data (untuk pie chart)
     */
    // public function getShiftBreakdownData($period = 30)
    // {
    //     $startDate = Carbon::now()->subDays($period);

    //     $data = WwtpInfluentHarian::where('tanggal', '>=', $startDate)
    //         ->selectRaw('shift, 
    //             SUM(pit_sparta) as total_sparta,
    //             SUM(pit_garam) as total_garam,
    //             SUM(pit_domestik) as total_domestik,
    //             SUM(pit_produksi_step3) as total_produksi_step3,
    //             SUM(pit_storage) as total_storage,
    //             SUM(pit_proses_wwtp2) as total_proses_wwtp2,
    //             SUM(pit_outlet) as total_outlet,
    //             SUM(pit_boiler) as total_boiler')
    //         ->groupBy('shift')
    //         ->get()
    //         ->map(function ($record) {
    //             return [
    //                 'shift' => $record->shift,
    //                 'total' => $record->total_sparta + $record->total_garam + $record->total_domestik + 
    //                            $record->total_produksi_step3 + $record->total_storage + $record->total_proses_wwtp2 + 
    //                            $record->total_outlet + $record->total_boiler,
    //             ];
    //         });

    //     return response()->json($data);
    // }

    public function getShiftBreakdownData(Request $request)
    {
        $startDate = $request->query('start_date')
            ? Carbon::parse($request->query('start_date'))->startOfDay()
            : Carbon::now()->startOfDay();

        $endDate = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $data = WwtpInfluentHarian::whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('
            SUM(pit_sparta) as total_sparta,
            SUM(pit_garam) as total_garam,
            SUM(pit_domestik) as total_domestik,
            SUM(pit_produksi_step3) as total_produksi_step3,
            SUM(pit_storage) as total_storage,
            SUM(pit_proses_wwtp2) as total_proses_wwtp2,
            SUM(pit_outlet) as total_outlet,
            SUM(pit_boiler) as total_boiler')
            ->first();

        return response()->json([
            'total_sparta'         => (float) $data->total_sparta,
            'total_garam'          => (float) $data->total_garam,
            'total_domestik'       => (float) $data->total_domestik,
            'total_produksi_step3' => (float) $data->total_produksi_step3,
            'total_storage'        => (float) $data->total_storage,
            'total_proses_wwtp2'   => (float) $data->total_proses_wwtp2,
            'total_outlet'         => (float) $data->total_outlet,
            'total_boiler'         => (float) $data->total_boiler,
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
                ->sum(DB::raw('pit_sparta + pit_garam + pit_domestik + pit_produksi_step3 + pit_storage + pit_proses_wwtp2 + pit_outlet + pit_boiler'));

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
                        'pit_sparta' => $record->pit_sparta,
                        'pit_garam' => $record->pit_garam,
                        'pit_domestik' => $record->pit_domestik,
                        'pit_produksi_step3' => $record->pit_produksi_step3,
                        'pit_storage' => $record->pit_storage,
                        'pit_proses_wwtp2' => $record->pit_proses_wwtp2,
                        'pit_outlet' => $record->pit_outlet,
                        'pit_boiler' => $record->pit_boiler,
                        'debit1' => $record->debit1,
                        'debit2' => $record->debit2,
                    ];
                });

                $totalVolume = $records->sum(function ($record) {
                    return $record->pit_sparta + $record->pit_garam + $record->pit_domestik +
                        $record->pit_produksi_step3 + $record->pit_storage + $record->pit_proses_wwtp2 +
                        $record->pit_outlet + $record->pit_boiler;
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
