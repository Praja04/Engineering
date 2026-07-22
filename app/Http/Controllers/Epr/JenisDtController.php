<?php

namespace App\Http\Controllers\Epr;

use App\Http\Controllers\Controller;
use App\Models\Epr\JenisDt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JenisDtController extends Controller
{
    public function index()
    {
        return view('epr.master.jenis-dt');
    }

    public function json()
    {
        $data = JenisDt::with('creator')->orderBy('name', 'asc')->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:epr_jenis_dts,name,' . $request->input('id'),
            'aktif' => 'required|boolean'
        ]);

        $id = $request->input('id');
        if ($id) {
            $item = JenisDt::findOrFail($id);
            $item->update([
                'name' => $request->input('name'),
                'aktif' => $request->input('aktif')
            ]);
        } else {
            JenisDt::create([
                'name' => $request->input('name'),
                'aktif' => $request->input('aktif'),
                'created_by' => Auth::id()
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $item = JenisDt::findOrFail($id);
        $item->delete();
        return response()->json(['success' => true]);
    }
}
