<?php

namespace App\Http\Controllers\Kalibrasi\Master;

use App\Http\Controllers\Controller;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\Master\MasterKetidakpastianPressureModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KalibrasiMasterKetidakpastianPressureController extends Controller
{
    /**
     * Tampilkan halaman master ketidakpastian pressure.
     */
    public function viewKetidakpastian()
    {
        $alatList = AlatKalibrasiModel::where('jenis_kalibrasi', 'pressure')
            ->orderBy('kode_alat')
            ->get(['id', 'kode_alat', 'nama_alat', 'kapasitas', 'resolusi']);

        return view('kalibrasi.master.ketidakpastian_pressure', compact('alatList'));
    }

    /**
     * Ambil data JSON untuk tabel / datatable.
     */
    public function index(Request $request)
    {
        $query = MasterKetidakpastianPressureModel::with('alat')
            ->orderBy('kode_alat')
            ->orderBy('titik_kalibrasi');

        if ($request->filled('alat_id')) {
            $query->where('alat_id', $request->alat_id);
        }

        if ($request->filled('kode_alat')) {
            $query->where('kode_alat', $request->kode_alat);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_alat', 'like', "%{$search}%")
                  ->orWhere('nama_alat', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('titik_kalibrasi', 'like', "%{$search}%");
            });
        }

        $data = $query->get();

        if ($request->ajax()) {
            return response()->json($data);
        }

        return response()->json($data);
    }

    /**
     * Simpan data master baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'alat_id'              => 'nullable|exists:alat_kalibrasi,id',
            'kode_alat'            => 'required|string|max:100',
            'titik_kalibrasi'      => 'required|numeric',
            'nilai_ketidakpastian' => 'required|numeric|min:0',
            'keterangan'           => 'nullable|string|max:255',
        ], [
            'kode_alat.required'            => 'Kode alat wajib dipilih atau diisi.',
            'titik_kalibrasi.required'      => 'Titik kalibrasi wajib diisi.',
            'titik_kalibrasi.numeric'       => 'Titik kalibrasi harus berupa angka.',
            'nilai_ketidakpastian.required' => 'Nilai ketidakpastian wajib diisi.',
            'nilai_ketidakpastian.numeric'  => 'Nilai ketidakpastian harus berupa angka.',
        ]);

        // Cek duplikasi kode_alat + titik_kalibrasi
        $exists = MasterKetidakpastianPressureModel::where('kode_alat', $validated['kode_alat'])
            ->where('titik_kalibrasi', $validated['titik_kalibrasi'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => "Ketidakpastian untuk alat {$validated['kode_alat']} pada titik {$validated['titik_kalibrasi']} sudah ada.",
            ], 422);
        }

        // Auto-resolve nama_alat & alat_id jika salah satunya kosong
        if (!empty($validated['alat_id'])) {
            $alat = AlatKalibrasiModel::find($validated['alat_id']);
            if ($alat) {
                $validated['nama_alat'] = $alat->nama_alat;
                $validated['kode_alat'] = $alat->kode_alat;
            }
        } elseif (!empty($validated['kode_alat'])) {
            $alat = AlatKalibrasiModel::where('kode_alat', $validated['kode_alat'])->first();
            if ($alat) {
                $validated['alat_id'] = $alat->id;
                $validated['nama_alat'] = $alat->nama_alat;
            }
        }

        // Estimasi u_naik dan u_turun jika tidak disediakan
        $uGabungan = (float) $validated['nilai_ketidakpastian'];
        $validated['u_naik'] = $uGabungan / sqrt(2);
        $validated['u_turun'] = $uGabungan / sqrt(2);

        $record = MasterKetidakpastianPressureModel::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data ketidakpastian berhasil disimpan.',
            'data'    => $record,
        ]);
    }

    /**
     * Ambil data per id.
     */
    public function show($id)
    {
        $data = MasterKetidakpastianPressureModel::with('alat')->findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update data master.
     */
    public function update(Request $request, $id)
    {
        $data = MasterKetidakpastianPressureModel::findOrFail($id);

        $validated = $request->validate([
            'alat_id'              => 'nullable|exists:alat_kalibrasi,id',
            'kode_alat'            => 'required|string|max:100',
            'titik_kalibrasi'      => 'required|numeric',
            'nilai_ketidakpastian' => 'required|numeric|min:0',
            'keterangan'           => 'nullable|string|max:255',
        ], [
            'kode_alat.required'            => 'Kode alat wajib dipilih atau diisi.',
            'titik_kalibrasi.required'      => 'Titik kalibrasi wajib diisi.',
            'nilai_ketidakpastian.required' => 'Nilai ketidakpastian wajib diisi.',
        ]);

        // Cek duplikasi kecuali id ini
        $exists = MasterKetidakpastianPressureModel::where('kode_alat', $validated['kode_alat'])
            ->where('titik_kalibrasi', $validated['titik_kalibrasi'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => "Ketidakpastian untuk alat {$validated['kode_alat']} pada titik {$validated['titik_kalibrasi']} sudah terdaftar.",
            ], 422);
        }

        if (!empty($validated['alat_id'])) {
            $alat = AlatKalibrasiModel::find($validated['alat_id']);
            if ($alat) {
                $validated['nama_alat'] = $alat->nama_alat;
                $validated['kode_alat'] = $alat->kode_alat;
            }
        } elseif (!empty($validated['kode_alat'])) {
            $alat = AlatKalibrasiModel::where('kode_alat', $validated['kode_alat'])->first();
            if ($alat) {
                $validated['alat_id'] = $alat->id;
                $validated['nama_alat'] = $alat->nama_alat;
            }
        }

        $uGabungan = (float) $validated['nilai_ketidakpastian'];
        $validated['u_naik'] = $uGabungan / sqrt(2);
        $validated['u_turun'] = $uGabungan / sqrt(2);

        $data->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data ketidakpastian berhasil diperbarui.',
            'data'    => $data,
        ]);
    }

    /**
     * Hapus data master.
     */
    public function destroy($id)
    {
        $data = MasterKetidakpastianPressureModel::findOrFail($id);
        $data->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data ketidakpastian berhasil dihapus.',
        ]);
    }

    /**
     * Ambil list titik & ketidakpastian berdasarkan alat.
     */
    public function getByAlat($alatIdentifier)
    {
        $data = MasterKetidakpastianPressureModel::where(function ($q) use ($alatIdentifier) {
                $q->where('alat_id', $alatIdentifier)
                  ->orWhere('kode_alat', $alatIdentifier);
            })
            ->orderBy('titik_kalibrasi')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }
}
