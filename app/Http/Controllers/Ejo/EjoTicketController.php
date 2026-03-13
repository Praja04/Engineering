<?php

namespace App\Http\Controllers\Ejo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ejo\EjoTicket;
use App\Models\Ejo\EjoClassification;

class EjoTicketController extends Controller
{
    /**
     * List semua EJO
     */
    public function index(Request $request)
    {
        $query = EjoTicket::with([
            'classification.type',
            'teams.user',   // ganti dari teams.team
            'progress',
            'notes'
        ]);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->department) {
            $query->where('department', $request->department);
        }

        if ($request->classification) {
            $query->where('classification_id', $request->classification);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_id',  'like', '%' . $request->search . '%')
                    ->orWhere('subject',   'like', '%' . $request->search . '%')
                    ->orWhere('requestor', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->latest()->paginate(20);

        return response()->json($tickets);
    }

    /**
     * Detail EJO
     */
    public function show($id)
    {
        $ticket = EjoTicket::with([
            'classification.type',
            'progress.user',
            'notes.user',
            'attachments',
            'teams.user',   // ganti dari teams.team
        ])->findOrFail($id);

        return response()->json($ticket);
    }

    /**
     * Create EJO manual
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'ticket_id'         => 'required|unique:ejo_tickets',
            'subject'           => 'required',
            'description'       => 'nullable',
            'department'        => 'nullable',
            'category'          => 'nullable',
            'module'            => 'nullable',
            'requestor'         => 'nullable',
            'classification_id' => 'nullable'
        ]);

        $ticket = EjoTicket::create($data);

        return response()->json([
            'message' => 'EJO created',
            'data'    => $ticket
        ]);
    }

    /**
     * Update EJO
     */
    public function update(Request $request, $id)
    {
        $ticket = EjoTicket::findOrFail($id);

        $ticket->update($request->all());

        return response()->json([
            'message' => 'EJO updated',
            'data'    => $ticket
        ]);
    }

    /**
     * Delete EJO
     */
    public function destroy($id)
    {
        $ticket = EjoTicket::findOrFail($id);
        $ticket->delete();

        return response()->json([
            'message' => 'EJO deleted'
        ]);
    }

    /**
     * Assign user ke EJO
     */
    public function assignTeam(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $ticket = EjoTicket::findOrFail($id);

        // Cek duplikat
        $exists = $ticket->teams()
            ->where('user_id', $request->user_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'User sudah di-assign ke EJO ini.'
            ], 422);
        }

        $ticket->teams()->create([
            'user_id' => $request->user_id,
        ]);

        return response()->json([
            'message' => 'User berhasil di-assign.'
        ]);
    }
}
