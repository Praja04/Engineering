<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\WwtpChemicalStandard;
use App\Models\Utility\WwtpBiayaChemicalRecord;
use App\Models\Utility\WwtpBiayaChemicalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WWTPControllerBiayaChemical extends Controller
{
    public function form_biaya_chemical()
    {
        $standards = WwtpChemicalStandard::orderBy('chemical_name', 'asc')->get();
        return view('utility.wwtp.form_biaya_chemical', compact('standards'));
    }

    public function data_biaya_chemical()
    {
        $standards = WwtpChemicalStandard::orderBy('chemical_name', 'asc')->get();
        return view('utility.wwtp.data_biaya_chemical', compact('standards'));
    }

    public function master_biaya_chemical()
    {
        return view('utility.wwtp.master_biaya_chemical');
    }

    // --- API FOR HISTORICAL RECORDS ---

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $page    = $request->input('page', 1);
        $tahun   = $request->input('tahun');
        $bulan   = $request->input('bulan'); // 1-12
        $search  = $request->input('search');

        $query = WwtpBiayaChemicalRecord::with(['details.chemicalStandard', 'createdBy:id,username', 'updatedBy:id,username'])
            ->orderBy('tanggal', 'desc');

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereYear('tanggal', 'like', "%{$search}%")
                  ->orWhere('limbah_di_olah', 'like', "%{$search}%");
            });
        }

        $records = $query->paginate($perPage, ['*'], 'page', $page);

        // Fetch standard prices
        $standards = WwtpChemicalStandard::orderBy('chemical_name', 'asc')->get();

        // Transform results to append calculated values dynamically
        $records->getCollection()->transform(function($record) use ($standards) {
            $limbah = $record->limbah_di_olah;
            
            $chemicalData = [];
            $totalCost = 0;

            // Map standard entries
            foreach ($standards as $std) {
                $detail = $record->details->firstWhere('chemical_standard_id', $std->id);
                $qty = $detail ? $detail->qty : 0;
                $cost = $qty * $std->harga_standar;
                $costM3 = $limbah > 0 ? $cost / $limbah : 0;

                $totalCost += $cost;

                $chemicalData[$std->chemical_name] = [
                    'qty'     => $qty,
                    'cost'    => $cost,
                    'cost_m3' => $costM3,
                    'price'   => $std->harga_standar
                ];
            }

            $totalCostM3 = $limbah > 0 ? $totalCost / $limbah : 0;

            $record->chemicals = $chemicalData;
            $record->total_cost = $totalCost;
            $record->total_cost_m3 = $totalCostM3;

            return $record;
        });

        // Also pass active standards alongside records so UI can draw tables dynamically
        return response()->json([
            'records'   => $records,
            'standards' => $standards
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun'          => 'required|integer|min:2000|max:2099',
            'bulan'          => 'required|integer|between:1,12',
            'limbah_di_olah' => 'required|numeric|min:0',
            'qty'            => 'required|array',
            'qty.*'          => 'required|numeric|min:0',
        ]);

        $tanggal = Carbon::create($request->tahun, $request->bulan, 1)->toDateString();

        // Check uniqueness
        $existing = WwtpBiayaChemicalRecord::where('tanggal', $tanggal)->first();
        if ($existing) {
            $monthName = Carbon::parse($tanggal)->translatedFormat('F');
            return response()->json([
                'status'  => 'error',
                'message' => "Data biaya chemical untuk bulan {$monthName} {$request->tahun} sudah diinput."
            ], 409);
        }

        DB::beginTransaction();
        try {
            // 1. Create header
            $record = WwtpBiayaChemicalRecord::create([
                'tanggal'        => $tanggal,
                'limbah_di_olah' => $request->limbah_di_olah,
                'created_by'     => Auth::id() ?? 1,
            ]);

            // 2. Create details
            foreach ($request->qty as $stdId => $qtyValue) {
                // Ensure the standard exists
                if (WwtpChemicalStandard::where('id', $stdId)->exists()) {
                    WwtpBiayaChemicalDetail::create([
                        'wwtp_biaya_chemical_record_id' => $record->id,
                        'chemical_standard_id'          => $stdId,
                        'qty'                            => $qtyValue,
                        'created_by'                     => Auth::id() ?? 1,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data biaya chemical berhasil disimpan.',
                'data'    => $record->load('details')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $record = WwtpBiayaChemicalRecord::findOrFail($id);

        $request->validate([
            'limbah_di_olah' => 'required|numeric|min:0',
            'qty'            => 'required|array',
            'qty.*'          => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update header
            $record->update([
                'limbah_di_olah' => $request->limbah_di_olah,
                'updated_by'     => Auth::id() ?? 1,
            ]);

            // Sync details
            foreach ($request->qty as $stdId => $qtyValue) {
                WwtpBiayaChemicalDetail::updateOrCreate(
                    [
                        'wwtp_biaya_chemical_record_id' => $record->id,
                        'chemical_standard_id'          => $stdId
                    ],
                    [
                        'qty'        => $qtyValue,
                        'updated_by' => Auth::id() ?? 1,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data biaya chemical berhasil diperbarui.',
                'data'    => $record->load('details')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $record = WwtpBiayaChemicalRecord::findOrFail($id);
        $record->delete(); // Details are deleted automatically due to cascade on migration foreign key

        return response()->json([
            'status'  => 'success',
            'message' => 'Data biaya chemical berhasil dihapus.'
        ]);
    }

    public function checkFilled(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|between:1,12',
        ]);

        $tanggal = Carbon::create($request->tahun, $request->bulan, 1)->toDateString();
        $isFilled = WwtpBiayaChemicalRecord::where('tanggal', $tanggal)->exists();

        return response()->json([
            'success'   => true,
            'is_filled' => $isFilled
        ]);
    }

    // --- API FOR MASTER PRICE STANDARDS ---

    public function indexStandards()
    {
        $standards = WwtpChemicalStandard::with('createdBy:id,username', 'updatedBy:id,username')
            ->orderBy('chemical_name', 'asc')
            ->get();
        return response()->json($standards);
    }

    public function storeStandard(Request $request)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $request->validate([
            'chemical_name' => 'required|string|max:255|unique:wwtp_chemical_standards,chemical_name',
            'harga_standar' => 'required|numeric|min:0',
        ]);

        $standard = WwtpChemicalStandard::create([
            'chemical_name' => strtoupper($request->chemical_name),
            'harga_standar' => $request->harga_standar,
            'created_by'    => Auth::id() ?? 1,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => "Master chemical standar {$standard->chemical_name} berhasil ditambahkan.",
            'data'    => $standard
        ]);
    }

    public function updateStandard(Request $request, $id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);
        $standard = WwtpChemicalStandard::findOrFail($id);

        $request->validate([
            'harga_standar' => 'required|numeric|min:0',
        ]);

        $standard->update([
            'harga_standar' => $request->harga_standar,
            'updated_by'    => Auth::id() ?? 1,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => "Harga standar chemical {$standard->chemical_name} berhasil diperbarui.",
            'data'    => $standard
        ]);
    }

    public function destroyStandard($id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);
        $standard = WwtpChemicalStandard::findOrFail($id);
        $standard->delete(); // Details are deleted automatically due to cascade on delete constraint

        return response()->json([
            'status'  => 'success',
            'message' => "Chemical {$standard->chemical_name} berhasil dihapus dari master."
        ]);
    }
}
