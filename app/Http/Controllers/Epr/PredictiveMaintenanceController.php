<?php

namespace App\Http\Controllers\Epr;

use App\Http\Controllers\Controller;
use App\Models\Epr\PredictiveMaintenance;
use App\Models\Epr\PredictiveMaintenancePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PredictiveMaintenanceController extends Controller
{
    public function form()
    {
        return view('epr.predictive-maintenance.form');
    }

    public function data()
    {
        return view('epr.predictive-maintenance.data');
    }

    public function getReports(Request $request)
    {
        $query = PredictiveMaintenance::with(['photos', 'workOrder'])
            ->orderBy('created_at', 'desc');

        if (Auth::user()->jabatan === 'operator') {
            $query->where('created_by', Auth::id());
        }

        $reports = $query->limit(500)->get();

        $mapped = $reports->map(function ($r) {
            return [
                'id' => (string) $r->id,
                'date' => $r->date,
                'tech' => $r->tech_name,
                'area' => $r->area,
                'woRef' => $r->wo_ref,
                'workOrderId' => $r->work_order_id ? (string) $r->work_order_id : null,
                'workOrderNo' => $r->workOrder->wo_number ?? null,
                'work' => $r->work_description,
                'timeStart' => substr($r->time_start, 0, 5),
                'timeEnd' => substr($r->time_end, 0, 5),
                'status' => $r->status,
                'notes' => $r->notes,
                'isAdhoc' => (bool) $r->is_adhoc,
                'adhocTitle' => $r->adhoc_title,
                'parentId' => $r->parent_id ? (string) $r->parent_id : null,
                'createdAt' => $r->created_at->toIso8601String(),
                'photos' => $r->photos->map(function ($p) {
                    $url = asset('storage/' . $p->photo_path);
                    return [
                        'url' => $url,
                        'thumb' => $url,
                        'path' => $p->photo_path
                    ];
                })
            ];
        });

        return response()->json($mapped);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'tech' => 'required|string',
            'area' => 'required|string',
            'work' => 'required|string',
            'timeStart' => 'required',
            'timeEnd' => 'required',
            'status' => 'required|in:open,progress,done,onhold',
            'isAdhoc' => 'required',
        ]);

        $id = $request->input('id');
        $isAdhoc = filter_var($request->input('isAdhoc'), FILTER_VALIDATE_BOOLEAN);
        $workOrderId = $request->input('workOrderId') ?: null;

        DB::beginTransaction();
        try {
            if ($id) {
                // Edit existing report
                $report = PredictiveMaintenance::findOrFail($id);
                $report->update([
                    'date' => $request->input('date'),
                    'tech_name' => $request->input('tech'),
                    'area' => $request->input('area'),
                    'wo_ref' => $request->input('woRef'),
                    'work_order_id' => $workOrderId,
                    'work_description' => $request->input('work'),
                    'time_start' => $request->input('timeStart'),
                    'time_end' => $request->input('timeEnd'),
                    'status' => $request->input('status'),
                    'notes' => $request->input('notes'),
                    'is_adhoc' => $isAdhoc,
                    'adhoc_title' => $isAdhoc ? $request->input('adhocTitle') : null,
                ]);
            } else {
                // Create new report
                $report = PredictiveMaintenance::create([
                    'date' => $request->input('date'),
                    'tech_name' => $request->input('tech'),
                    'area' => $request->input('area'),
                    'wo_ref' => $request->input('woRef'),
                    'work_order_id' => $workOrderId,
                    'work_description' => $request->input('work'),
                    'time_start' => $request->input('timeStart'),
                    'time_end' => $request->input('timeEnd'),
                    'status' => $request->input('status'),
                    'notes' => $request->input('notes'),
                    'is_adhoc' => $isAdhoc,
                    'adhoc_title' => $isAdhoc ? $request->input('adhocTitle') : null,
                    'parent_id' => $request->input('parentId'),
                    'created_by' => Auth::id(),
                ]);

                // If parent_id is set and status is done, update the parent report status to done
                if ($request->input('status') === 'done' && $request->input('parentId')) {
                    $parent = PredictiveMaintenance::find($request->input('parentId'));
                    if ($parent) {
                        $parent->update(['status' => 'done']);
                    }
                }
            }

            // Sync/update Work Order status
            $targetWoId = $workOrderId ?: ($report->work_order_id ?: ($report->parent?->work_order_id ?? null));
            if ($targetWoId) {
                $wo = \App\Models\Epr\WorkOrder::find($targetWoId);
                if ($wo) {
                    if ($request->input('status') === 'done') {
                        $wo->update(['status' => 'done']);
                    } else {
                        $wo->update(['status' => 'progress']);
                    }
                }
            }

            // Photo Management
            $existingPhotoPaths = $request->input('existingPhotos', []); // list of paths to keep
            if ($id) {
                // Delete photos that are no longer kept
                $currentPhotos = $report->photos;
                foreach ($currentPhotos as $photo) {
                    if (!in_array($photo->photo_path, $existingPhotoPaths)) {
                        Storage::disk('public')->delete($photo->photo_path);
                        $photo->delete();
                    }
                }
            }

            // Save new photos with server-side compression
            if ($request->hasFile('newPhotos')) {
                foreach ($request->file('newPhotos') as $file) {
                    $compressedPath = $this->compressAndStoreImage($file);
                    PredictiveMaintenancePhoto::create([
                        'predictive_maintenance_id' => $report->id,
                        'photo_path' => $compressedPath
                    ]);
                }
            }

            DB::commit();

            // Return fully mapped report data
            $report->load('photos');
            return response()->json([
                'success' => true,
                'report' => [
                    'id' => (string) $report->id,
                    'date' => $report->date,
                    'tech' => $report->tech_name,
                    'area' => $report->area,
                    'woRef' => $report->wo_ref,
                    'workOrderId' => $report->work_order_id ? (string) $report->work_order_id : null,
                    'work' => $report->work_description,
                    'timeStart' => substr($report->time_start, 0, 5),
                    'timeEnd' => substr($report->time_end, 0, 5),
                    'status' => $report->status,
                    'notes' => $report->notes,
                    'isAdhoc' => (bool) $report->is_adhoc,
                    'adhocTitle' => $report->adhoc_title,
                    'parentId' => $report->parent_id ? (string) $report->parent_id : null,
                    'createdAt' => $report->created_at->toIso8601String(),
                    'photos' => $report->photos->map(function ($p) {
                        $url = asset('storage/' . $p->photo_path);
                        return [
                            'url' => $url,
                            'thumb' => $url,
                            'path' => $p->photo_path
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function compressAndStoreImage($file)
    {
        // 1200px max, 75% quality using GD
        $tempPath = $file->getRealPath();
        list($width, $height, $type) = getimagesize($tempPath);
        
        $maxPx = 1200;
        if ($width > $maxPx || $height > $maxPx) {
            if ($width > $height) {
                $newHeight = round($height / $width * $maxPx);
                $newWidth = $maxPx;
            } else {
                $newWidth = round($width / $height * $maxPx);
                $newHeight = $maxPx;
            }
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $src = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($tempPath);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($tempPath);
                break;
            case IMAGETYPE_GIF:
                $src = imagecreatefromgif($tempPath);
                break;
            case IMAGETYPE_WEBP:
                $src = imagecreatefromwebp($tempPath);
                break;
        }

        $filename = 'epr-photos/' . uniqid() . '.jpg';
        $destinationPath = storage_path('app/public/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($destinationPath))) {
            mkdir(dirname($destinationPath), 0755, true);
        }

        if ($src) {
            $dst = imagecreatetruecolor($newWidth, $newHeight);
            if ($type == IMAGETYPE_PNG) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            }
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagejpeg($dst, $destinationPath, 75);
            imagedestroy($src);
            imagedestroy($dst);
        } else {
            // Fallback storage if GD fails
            return $file->store('epr-photos', 'public');
        }

        return $filename;
    }
}
