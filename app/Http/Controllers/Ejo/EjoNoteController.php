<?php

namespace App\Http\Controllers\Ejo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ejo\EjoNote;
use App\Models\Ejo\EjoTicket;
use Illuminate\Support\Facades\Auth;

class EjoNoteController extends Controller
{
    /**
     * List notes
     */
    public function index($ejoId)
    {
        $notes = EjoNote::with('user')
            ->where('ejo_id', $ejoId)
            ->latest()
            ->get();

        return response()->json($notes);
    }

    /**
     * Tambah note
     */
    public function store(Request $request, $ejoId)
    {
        $request->validate([
            'note' => 'required|string'
        ]);

        $ticket = EjoTicket::findOrFail($ejoId);

        $note = EjoNote::create([
            'ejo_id' => $ticket->id,
            'note' => $request->note,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'message' => 'Note added',
            'data' => $note
        ]);
    }

    /**
     * Delete note
     */
    public function destroy($id)
    {
        $note = EjoNote::findOrFail($id);
        $note->delete();

        return response()->json([
            'message' => 'Note deleted'
        ]);
    }
}
