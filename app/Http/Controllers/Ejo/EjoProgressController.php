<?php

namespace App\Http\Controllers\Ejo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ejo\EjoProgress;
use App\Models\Ejo\EjoTicket;
use Illuminate\Support\Facades\Auth;

class EjoProgressController extends Controller
{
    /**
     * Tambah progress
     */
    public function store(Request $request, $ejoId)
    {
        $request->validate([
            'progress_percent' => 'required|integer|min:0|max:100',
            'progress_note' => 'nullable|string'
        ]);

        $ticket = EjoTicket::findOrFail($ejoId);

        $progress = EjoProgress::create([
            'ejo_id' => $ticket->id,
            'progress_percent' => $request->progress_percent,
            'progress_note' => $request->progress_note,
            'updated_by' => Auth::id()
        ]);

        // jika progress 100 maka update status
        if ($request->progress_percent == 100) {
            $ticket->update([
                'status' => 'Done',
                'date_done' => now()
            ]);
        }

        return response()->json([
            'message' => 'Progress updated',
            'data' => $progress
        ]);
    }

    /**
     * List progress dari ticket
     */
    public function index($ejoId)
    {
        $progress = EjoProgress::with('user')
            ->where('ejo_id', $ejoId)
            ->latest()
            ->get();

        return response()->json($progress);
    }

    /**
     * Delete progress
     */
    public function destroy($id)
    {
        $progress = EjoProgress::findOrFail($id);
        $progress->delete();

        return response()->json([
            'message' => 'Progress deleted'
        ]);
    }
}
