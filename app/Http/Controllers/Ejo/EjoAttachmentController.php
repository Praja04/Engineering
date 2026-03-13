<?php

namespace App\Http\Controllers\Ejo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ejo\EjoAttachment;
use App\Models\Ejo\EjoTicket;
use Illuminate\Support\Facades\Storage;

class EjoAttachmentController extends Controller
{
    /**
     * List attachment
     */
    public function index($ejoId)
    {
        $attachments = EjoAttachment::where('ejo_id', $ejoId)->get();

        return response()->json($attachments);
    }

    /**
     * Upload attachment
     */
    public function store(Request $request, $ejoId)
    {
        $request->validate([
            'file' => 'required|file|max:10240'
        ]);

        $ticket = EjoTicket::findOrFail($ejoId);

        $file = $request->file('file');

        $path = $file->store('ejo_attachments', 'public');

        $attachment = EjoAttachment::create([
            'ejo_id' => $ticket->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path
        ]);

        return response()->json([
            'message' => 'File uploaded',
            'data' => $attachment
        ]);
    }

    /**
     * Delete attachment
     */
    public function destroy($id)
    {
        $attachment = EjoAttachment::findOrFail($id);

        Storage::disk('public')->delete($attachment->file_path);

        $attachment->delete();

        return response()->json([
            'message' => 'Attachment deleted'
        ]);
    }
}
