<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\WwtpMasterKoloni;
use App\Models\Utility\WwtpKoloni;
use App\Models\Utility\WwtpKoloniDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class WWTPControllerKoloni extends Controller
{
    // Render View Rute
    public function form_koloni()
    {
        $samples = WwtpMasterKoloni::orderBy('nama_sample')->get();
        return view('utility.wwtp.form_koloni', compact('samples'));
    }

    public function data_koloni()
    {
        $samples = WwtpMasterKoloni::orderBy('nama_sample')->get();
        return view('utility.wwtp.data_koloni', compact('samples'));
    }

    public function master_koloni()
    {
        // Akses dibatasi untuk non-operator via route middleware
        return view('utility.wwtp.master_koloni');
    }

    // --- API TRANSACTION DATA KOLONI ---

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page    = $request->input('page', 1);
        $sampleId = $request->input('master_koloni_id');
        $bulan   = $request->input('bulan'); // Format: YYYY-MM
        $search  = $request->input('search');

        $query = WwtpKoloni::with(['details.masterKoloni:id,nama_sample', 'details.createdBy:id,username', 'details.updatedBy:id,username'])
            ->orderBy('week_start', 'desc');

        if ($sampleId) {
            $query->whereHas('details', function ($q) use ($sampleId) {
                $q->where('master_koloni_id', $sampleId);
            });
        }

        if ($bulan) {
            $query->whereRaw("DATE_FORMAT(week_start, '%Y-%m') = ?", [$bulan]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('week_start', 'like', "%{$search}%")
                    ->orWhere('week_end', 'like', "%{$search}%")
                    ->orWhereHas('details.masterKoloni', function ($sq) use ($search) {
                        $sq->where('nama_sample', 'like', "%{$search}%");
                    });
            });
        }

        return response()->json($query->paginate($perPage, ['*'], 'page', $page));
    }

    /**
     * Simpan data koloni baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date',
            'master_koloni_id' => 'required|exists:wwtp_master_koloni,id',
            'nilai_base'       => 'required|numeric|min:0',
            'nilai_pangkat'    => 'required|integer',
        ]);

        // Hitung rentang minggu (Senin - Minggu)
        $tanggal = Carbon::parse($request->tanggal);
        $startWeek = $tanggal->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endWeek   = $tanggal->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

        // Cari atau buat Header WwtpKoloni berdasarkan rentang minggu
        $koloniHeader = WwtpKoloni::firstOrCreate(
            ['week_start' => $startWeek],
            ['week_end' => $endWeek]
        );

        // Cek validasi keunikan sampel per minggu (validasi week sample)
        $existing = WwtpKoloniDetail::where('wwtp_koloni_id', $koloniHeader->id)
            ->where('master_koloni_id', $request->master_koloni_id)
            ->first();

        if ($existing) {
            $sampleName = WwtpMasterKoloni::find($request->master_koloni_id)->nama_sample;
            return response()->json([
                'status' => 'error',
                'message' => "Sample '{$sampleName}' sudah diinput untuk minggu ini ({$startWeek} s/d {$endWeek})."
            ], 409);
        }

        // Simpan data detail
        $detail = WwtpKoloniDetail::create([
            'wwtp_koloni_id'   => $koloniHeader->id,
            'master_koloni_id' => $request->master_koloni_id,
            'tanggal'          => $request->tanggal,
            'nilai_base'       => $request->nilai_base,
            'nilai_pangkat'    => $request->nilai_pangkat,
            'created_by'       => Auth::id() ?? 1
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data koloni berhasil disimpan.',
            'data' => $detail->load(['koloni', 'masterKoloni'])
        ]);
    }

    /**
     * Tampilkan detail data koloni
     */
    public function show($id)
    {
        $detail = WwtpKoloniDetail::with(['koloni', 'masterKoloni', 'createdBy:id,username', 'updatedBy'])->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $detail
        ]);
    }

    /**
     * Update data koloni
     */
    public function update(Request $request, $id)
    {
        $detail = WwtpKoloniDetail::findOrFail($id);

        $request->validate([
            'nilai_base'    => 'required|numeric|min:0',
            'nilai_pangkat' => 'required|integer',
        ]);

        $detail->update([
            'nilai_base'    => $request->nilai_base,
            'nilai_pangkat' => $request->nilai_pangkat,
            'updated_by'    => Auth::id() ?? 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data koloni berhasil diperbarui.',
            'data' => $detail->load(['koloni', 'masterKoloni'])
        ]);
    }

    /**
     * Hapus data koloni
     */
    public function destroy($id)
    {
        $detail = WwtpKoloniDetail::findOrFail($id);
        $headerId = $detail->wwtp_koloni_id;

        $detail->delete();

        // Cek jika header tidak memiliki detail lagi, hapus headernya agar db bersih
        $header = WwtpKoloni::find($headerId);
        if ($header && $header->details()->count() === 0) {
            $header->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data koloni berhasil dihapus.'
        ]);
    }

    // --- API MASTER SAMPLE KOLONI ---

    public function indexMaster(Request $request)
    {
        $query = WwtpMasterKoloni::with('createdBy:id,username')
            ->orderBy('nama_sample', 'asc');

        if ($request->filled('search')) {
            $query->where('nama_sample', 'like', "%{$request->search}%");
        }

        return response()->json($query->get());
    }

    public function storeMaster(Request $request)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $request->validate([
            'nama_sample' => 'required|string|max:255|unique:wwtp_master_koloni,nama_sample',
        ]);

        $sample = WwtpMasterKoloni::create([
            'nama_sample' => $request->nama_sample,
            'created_by' => Auth::id() ?? 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Master sample koloni berhasil ditambahkan.',
            'data' => $sample
        ]);
    }

    public function showMaster($id)
    {
        $sample = WwtpMasterKoloni::findOrFail($id);
        return response()->json($sample);
    }

    public function updateMaster(Request $request, $id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);
        $sample = WwtpMasterKoloni::findOrFail($id);

        $request->validate([
            'nama_sample' => 'required|string|max:255|unique:wwtp_master_koloni,nama_sample,' . $id,
        ]);

        $sample->update([
            'nama_sample' => $request->nama_sample,
            'updated_by' => Auth::id() ?? 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Master sample koloni berhasil diubah.',
            'data' => $sample
        ]);
    }

    public function destroyMaster($id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);
        $sample = WwtpMasterKoloni::findOrFail($id);
        $sample->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Master sample koloni berhasil dihapus.'
        ]);
    }

    // Cek apakah sampel sudah diisi pada minggu tersebut (helper AJAX)
    public function checkFilled(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date',
            'master_koloni_id' => 'required|exists:wwtp_master_koloni,id',
        ]);

        $tanggal = Carbon::parse($request->tanggal);
        $startWeek = $tanggal->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endWeek   = $tanggal->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $header = WwtpKoloni::where('week_start', $startWeek)->first();

        if (!$header) {
            return response()->json([
                'success' => true,
                'is_filled' => false,
                'week_range' => "{$startWeek} s/d {$endWeek}"
            ]);
        }

        $isFilled = WwtpKoloniDetail::where('wwtp_koloni_id', $header->id)
            ->where('master_koloni_id', $request->master_koloni_id)
            ->exists();

        return response()->json([
            'success' => true,
            'is_filled' => $isFilled,
            'week_range' => "{$startWeek} s/d {$endWeek}"
        ]);
    }
}
