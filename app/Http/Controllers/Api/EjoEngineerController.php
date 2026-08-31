<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EjoEngineer\Drawing;
use App\Models\Ejo\EjoTicket as Ejo;
use App\Models\EjoEngineer\GeneralEjo;
use App\Models\EjoEngineer\Notification;
use App\Models\EjoEngineer\Project;
use App\Models\EjoEngineer\RepairPart;
use App\Models\EjoEngineer\Setting;
use App\Models\User;
use App\Models\EjoEngineer\WspMaterial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EjoEngineerController extends Controller
{
    private function isServerAdmin(?User $user, ?string $username = null): bool
    {
        if ($user && $user->role === 'Server') {
            return true;
        }
        if ($username) {
            $u = User::where('username', $username)->first();
            return $u && $u->role === 'Server';
        }
        return false;
    }

    private array $roleLevels = [
        'Server' => 100,
        'Manager Eng' => 80,
        'Plant Manager' => 80,
        'Admin Eng' => 40,
        'Drafter' => 20,
        'Sipil' => 20,
        'Mekanik' => 20,
        'Elektrik' => 20,
        'Program' => 20,
        'Kalibrasi' => 20,
        'Repair Part' => 20,
        'Manager' => 80,
        'Supervisor' => 60,
        'User' => 10,
        'user_PRD' => 10,
        'user_ENG' => 10,
        'user_EPR' => 10,
        'user_GA' => 10,
        'user_QC' => 10,
        'user_WRH' => 10,
        'user_TMB' => 10,
        'user_EUT' => 10,
        'Staff PRD' => 10,
        'Staff ENG' => 10,
        'Staff EPR' => 10,
        'Staff GA' => 10,
        'Staff QC' => 10,
        'Staff WRH' => 10,
        'Staff TMB' => 10,
        'Staff EUT' => 10,
        'Supervisor PRD' => 60,
        'Supervisor EPR' => 60,
        'Supervisor GA' => 60,
        'Supervisor QC' => 60,
        'Supervisor WRH' => 60,
        'Supervisor TMB' => 60,
        'Supervisor EUT' => 60,
        'Manager PRD' => 80,
        'Manager EPR' => 80,
        'Manager GA' => 80,
        'Manager QC' => 80,
        'Manager WRH' => 80,
        'Manager TMB' => 80,
        'Manager EUT' => 80,
    ];

    private function getRoleLevel(?string $role): int
    {
        if (! $role) return 0;
        if (isset($this->roleLevels[$role])) return $this->roleLevels[$role];
        if (str_starts_with($role, 'Manager ')) return 80;
        if (str_starts_with($role, 'Supervisor ')) return 60;
        if (str_starts_with($role, 'user_') || str_starts_with($role, 'Staff ') || str_starts_with($role, 'User ')) return 10;
        return 0;
    }

    private function normalizeDeptCode(?string $dept): string
    {
        if (! $dept) return '';
        $clean = trim((string) $dept);
        $upper = strtoupper($clean);
        $mapping = [
            'PRD' => 'PRD',
            'PRD (PRODUCTION)' => 'PRD',
            'PRODUCTION' => 'PRD',
            'ENG' => 'ENG',
            'ENG (ENGINEERING)' => 'ENG',
            'ENGINEERING' => 'ENG',
            'EUT' => 'EUT',
            'EUT (ENGINEER UTILITY)' => 'EUT',
            'EUT (ENGINEERING UTILITY)' => 'EUT',
            'ENGINEER UTILITY' => 'EUT',
            'ENGINEERING UTILITY' => 'EUT',
            'UTILITY' => 'EUT',
            'UTL' => 'EUT',
            'EPR' => 'EPR',
            'EPR (ENGINEERING PRODUKSI)' => 'EPR',
            'EPR (ENGINEERING PRODUCTION)' => 'EPR',
            'ENGINEERING PRODUKSI' => 'EPR',
            'ENGINEERING PRODUCTION' => 'EPR',
            'GA' => 'GA',
            'GA (GENERAL AFFAIR)' => 'GA',
            'GENERAL AFFAIR' => 'GA',
            'GENERAL AFFAIRS' => 'GA',
            'QC' => 'QC',
            'QC (QUALITY CONTROL)' => 'QC',
            'QUALITY CONTROL' => 'QC',
            'WRH' => 'WRH',
            'WRH (WAREHOUSE)' => 'WRH',
            'WAREHOUSE' => 'WRH',
            'MAINTENANCE' => 'WRH',
            'EKSPEDISI' => 'WRH',
            'TMB' => 'TMB',
            'TMB (TIMBANGAN)' => 'TMB',
            'TIMBANGAN' => 'TMB',
            'HSE' => 'HSE',
        ];
        return $mapping[$upper] ?? $clean;
    }

    private function insertNotification(string $targetUsername, string $ejoId, string $message): void
    {
        if (! $targetUsername) return;
        Notification::create([
            'id'              => date('YmdHis') . '_' . Str::random(6),
            'target_username' => $targetUsername,
            'ejo_id'          => $ejoId,
            'message'         => $message,
            'timestamp'       => now()->toIso8601String(),
            'is_read'         => 0,
        ]);
    }

    private function resolveUsername(string $fullname): ?string
    {
        if (! $fullname || $fullname === 'Unassigned') return null;
        $user = User::where('fullname', $fullname)->orWhere('username', $fullname)->first();
        return $user ? $user->username : null;
    }

    private function notifyDeptApprovers(string $dept, string $refId, string $message): void
    {
        $normDept = $this->normalizeDeptCode($dept);
        if (! $normDept) return;

        $approvers = User::where('is_active', 1)
            ->where(function ($q) use ($normDept) {
                $q->where('dept', $normDept)
                  ->orWhere('role', 'like', "%{$normDept}%");
            })
            ->where(function ($q) {
                $q->where('role', 'like', '%Supervisor%')
                  ->orWhere('role', 'like', '%Manager%')
                  ->orWhere('role', 'like', '%SPV%');
            })
            ->pluck('username');

        foreach ($approvers as $u) {
            $this->insertNotification($u, $refId, $message);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $rawUser = $request->input('username');
        $rawPass = $request->input('password');
        $rawDev  = $request->input('device_id');

        if (! is_string($rawUser) || ! is_string($rawPass)) {
            return response()->json(['status' => 'error', 'message' => 'Format input username atau password tidak valid!'], 400);
        }

        $username = trim($rawUser);
        $password = $rawPass;
        $deviceId = (is_string($rawDev) && trim($rawDev) !== '') ? trim($rawDev) : ('dev-fallback-' . Str::random(8));

        if (! $username || ! $password) {
            return response()->json(['status' => 'error', 'message' => 'Username dan Password wajib diisi.'], 400);
        }

        $maintenanceSetting = Setting::where('key', 'maintenance_mode')->first();
        $isMaintenance = $maintenanceSetting && $maintenanceSetting->value === '1';

        $user = User::whereRaw('LOWER(username) = LOWER(?)', [$username])->first();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'Username atau password salah'], 401);
        }

        $isValidPass = false;
        if (password_verify($password, $user->password) || $password === $user->password) {
            $isValidPass = true;
        }

        if (! $isValidPass) {
            return response()->json(['status' => 'error', 'message' => 'Username atau password salah'], 401);
        }

        $isServer = ($user->role === 'Server' || strtolower($user->username) === 'server');
        if ($user->is_active === 0 && ! $isServer) {
            return response()->json(['status' => 'error', 'message' => 'Akun Anda telah nonaktif/disuspend oleh Server Admin. Silakan hubungi Server Admin.'], 403);
        }

        if ($isMaintenance && ! $isServer) {
            return response()->json(['status' => 'error', 'message' => 'Server sedang dalam pemeliharaan (maintenance) / perbaikan. Akses ditutup sementara.'], 503);
        }

        $userPayload = $user->toArray();
        $userPayload['device_id'] = $deviceId;
        unset($userPayload['password']);

        return response()->json($userPayload, 200);
    }

    public function heartbeat(Request $request): JsonResponse
    {
        if (! Auth::check() && ! auth('web')->check()) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Sesi telah berakhir. Silakan login kembali.'
            ], 401);
        }
        return response()->json(['status' => 'success', 'timestamp' => now()->toIso8601String()]);
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json(['status' => 'success']);
    }

    public function forceLogoutUser(Request $request): JsonResponse
    {
        return response()->json(['status' => 'success', 'message' => 'Sesi berhasil di-logout.']);
    }

    public function getUsers(Request $request): JsonResponse
    {
        $users = User::where('role', '!=', 'Server')
            ->whereRaw('LOWER(COALESCE(dept, "")) != "server"')
            ->get();
        return response()->json($users);
    }

    public function createUser(Request $request): JsonResponse
    {
        $data = $request->all();
        if (User::where('username', $data['username'])->exists()) {
            return response()->json(['status' => 'error', 'message' => "Username '{$data['username']}' sudah digunakan oleh user lain!"], 400);
        }

        User::create($data);
        return response()->json(['status' => 'success', 'username' => $data['username']], 201);
    }

    public function updateUser(Request $request, string $username): JsonResponse
    {
        $user = User::where('username', $username)->first();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        $data = $request->all();
        $user->update($data);
        return response()->json(['status' => 'success', 'username' => $username]);
    }

    public function updateUserLayoutSettings(Request $request, string $username): JsonResponse
    {
        $user = User::where('username', $username)->first();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        $incomingLayout = $request->input('layout_settings');
        $layoutJson = is_array($incomingLayout) ? json_encode($incomingLayout) : (string) $incomingLayout;

        $user->update([
            'layout_settings' => $layoutJson,
        ]);

        return response()->json(['status' => 'success', 'username' => $username]);
    }

    public function updateUserAccess(Request $request, string $username): JsonResponse
    {
        $user = User::where('username', $username)->first();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        $permissions = $request->input('access_permissions', []);
        $isActive = $request->input('is_active', 1) ? 1 : 0;

        $user->update([
            'access_permissions' => is_array($permissions) ? json_encode($permissions) : $permissions,
            'is_active'          => $isActive,
        ]);

        return response()->json(['status' => 'success', 'message' => "Hak akses akun '{$username}' berhasil diperbarui."]);
    }

    public function updateRoleAccess(Request $request): JsonResponse
    {
        $targetDept = $request->input('dept', $request->input('target_dept', 'ENG'));
        $targetRole = $request->input('role', $request->input('target_role', ''));
        if (! $targetRole) {
            return response()->json(['status' => 'error', 'message' => 'Role target wajib diisi!'], 400);
        }

        $permissions = $request->input('access_permissions', []);
        $permJson = is_array($permissions) ? json_encode($permissions) : $permissions;

        User::whereRaw('LOWER(COALESCE(dept, "ENG")) = LOWER(?)', [$targetDept])
            ->whereRaw('LOWER(COALESCE(role, "User")) = LOWER(?)', [$targetRole])
            ->update(['access_permissions' => $permJson]);

        return response()->json(['status' => 'success', 'message' => "Hak akses per Role '{$targetRole}' Dept '{$targetDept}' berhasil diperbarui."]);
    }

    public function bulkResetUserAccess(Request $request): JsonResponse
    {
        User::query()->update(['access_permissions' => null]);
        return response()->json(['status' => 'success', 'message' => 'Seluruh hak akses kustom pengguna berhasil di-reset ke setelan role default!']);
    }

    public function deleteUser(Request $request, string $username): JsonResponse
    {
        $user = User::where('username', $username)->first();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'User berhasil dihapus']);
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $username = $request->input('username');
        $user = User::where('username', $username)->first();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        if (! $request->hasFile('avatar') && ! $request->hasFile('file')) {
            return response()->json(['status' => 'error', 'message' => 'File tidak ditemukan'], 400);
        }

        $file = $request->file('avatar') ?: $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $filename = 'avatar_' . strtolower($username) . '_' . Str::random(6) . '.' . $ext;
        $destPath = public_path('ejo-engineer-assets/uploads/avatars');
        if (! File::isDirectory($destPath)) {
            File::makeDirectory($destPath, 0777, true, true);
        }

        $file->move($destPath, $filename);
        $avatarUrl = '/ejo-engineer-assets/uploads/avatars/' . $filename;
        $user->update(['avatar' => $avatarUrl]);

        return response()->json([
            'status'  => 'success',
            'avatar'  => $avatarUrl,
            'message' => 'Foto profil berhasil diperbarui.',
        ]);
    }

    public function getEjos(): JsonResponse
    {
        $ejos = Ejo::all();
        return response()->json($ejos);
    }

    public function createEjo(Request $request): JsonResponse
    {
        $data = $request->all();
        $ejoId = $data['id'] ?? ('EJO-' . date('Y') . '-' . Str::random(3));
        $data['id'] = $ejoId;
        $status = $data['status'] ?? 'Requested';
        $data['status'] = $status;

        $ejo = Ejo::create($data);
        return response()->json(['status' => 'success', 'id' => $ejoId], 201);
    }

    public function updateEjo(Request $request, string $id): JsonResponse
    {
        $ejo = Ejo::find($id);
        if (! $ejo) {
            return response()->json(['status' => 'error', 'message' => 'EJO tidak ditemukan'], 404);
        }

        $data = $request->all();
        $ejo->update($data);
        return response()->json(['status' => 'success', 'id' => $id]);
    }

    public function deleteEjo(Request $request, string $id): JsonResponse
    {
        $ejo = Ejo::find($id);
        if (! $ejo) {
            return response()->json(['status' => 'error', 'message' => 'EJO tidak ditemukan'], 404);
        }

        $ejo->delete();
        return response()->json(['status' => 'success', 'message' => 'EJO berhasil dihapus']);
    }

    public function getGeneralEjos(): JsonResponse
    {
        $generalEjos = GeneralEjo::all();
        return response()->json($generalEjos);
    }

    public function createGeneralEjo(Request $request): JsonResponse
    {
        $data = $request->all();
        if (isset($data['items']) && is_array($data['items'])) {
            $inserted = 0;
            foreach ($data['items'] as $item) {
                GeneralEjo::create($item);
                $inserted++;
            }
            return response()->json(['status' => 'success', 'count' => $inserted], 201);
        }

        $gejoId = $data['id'] ?? ('EJO' . Str::random(8));
        $data['id'] = $gejoId;
        $data['createdDate'] = $data['createdDate'] ?? now()->toIso8601String();
        $data['status'] = $data['status'] ?? 'Requested';
        $gejo = GeneralEjo::create($data);

        return response()->json(['status' => 'success', 'id' => $gejoId], 201);
    }

    public function updateGeneralEjo(Request $request, string $id): JsonResponse
    {
        $gejo = GeneralEjo::find($id);
        if (! $gejo) {
            return response()->json(['status' => 'error', 'message' => 'General EJO tidak ditemukan'], 404);
        }

        $data = $request->all();
        $gejo->update($data);
        return response()->json(['status' => 'success', 'id' => $id]);
    }

    public function deleteGeneralEjo(Request $request, string $id): JsonResponse
    {
        $gejo = GeneralEjo::find($id);
        if (! $gejo) {
            return response()->json(['status' => 'error', 'message' => 'General EJO tidak ditemukan'], 404);
        }

        $gejo->delete();
        return response()->json(['status' => 'success', 'message' => 'General EJO berhasil dihapus']);
    }

    public function getProjects(): JsonResponse
    {
        $projects = Project::all();
        return response()->json($projects);
    }

    public function createProject(Request $request): JsonResponse
    {
        $data = $request->all();
        $proj = Project::create($data);
        return response()->json(['status' => 'success', 'id' => $proj->id], 201);
    }

    public function updateProject(Request $request, string $id): JsonResponse
    {
        $project = Project::find($id);
        if (! $project) {
            return response()->json(['status' => 'error', 'message' => 'Project tidak ditemukan'], 404);
        }

        $data = $request->all();
        $project->update($data);
        return response()->json(['status' => 'success', 'id' => $id]);
    }

    public function deleteProject(Request $request, string $id): JsonResponse
    {
        $project = Project::find($id);
        if (! $project) {
            return response()->json(['status' => 'error', 'message' => 'Project tidak ditemukan'], 404);
        }

        $project->delete();
        return response()->json(['status' => 'success', 'message' => 'Project berhasil dihapus']);
    }

    public function uploadProjectDoc(Request $request): JsonResponse
    {
        $projId = $request->input('id');
        $project = Project::find($projId);
        if (! $project) {
            return response()->json(['status' => 'error', 'message' => 'Project tidak ditemukan'], 404);
        }

        $file = $request->file('file');
        if (! $file) {
            return response()->json(['status' => 'error', 'message' => 'File tidak valid'], 400);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'pdf');
        $filename = 'proj_' . Str::random(8) . '.' . $ext;
        $destPath = public_path('ejo-engineer-assets/uploads/projects');
        if (! File::isDirectory($destPath)) {
            File::makeDirectory($destPath, 0777, true, true);
        }

        $file->move($destPath, $filename);
        $fileUrl = '/ejo-engineer-assets/uploads/projects/' . $filename;

        $colName = $request->input('category', 'docs');
        $currentDocs = $project->{$colName} ?: [];
        $currentDocs[] = $fileUrl;

        $project->{$colName} = $currentDocs;
        $project->save();

        return response()->json([
            'status'   => 'success',
            'file_url' => $fileUrl,
            $colName   => $currentDocs,
        ]);
    }

    public function deleteProjectHandoverDoc(Request $request, string $id): JsonResponse
    {
        $project = Project::find($id);
        if (! $project) {
            return response()->json(['status' => 'error', 'message' => 'Project tidak ditemukan'], 404);
        }

        $docUrl = $request->query('url', '');
        $currentDocs = is_array($project->handover_docs) ? $project->handover_docs : [];
        $targetClean = strtolower(basename(explode('?', $docUrl)[0]));

        $updatedDocs = [];
        foreach ($currentDocs as $d) {
            $dUrl = is_string($d) ? $d : ($d['path'] ?? $d['url'] ?? '');
            if ($dUrl && strtolower(basename(explode('?', $dUrl)[0])) === $targetClean) {
                continue;
            }
            $updatedDocs[] = $d;
        }

        $project->handover_docs = $updatedDocs;
        $project->save();

        return response()->json([
            'status'             => 'success',
            'handover_docs'      => $updatedDocs,
            'handover_approvals' => [],
        ]);
    }

    public function getDrawings(): JsonResponse
    {
        $drawings = Drawing::orderByDesc('uploaded_at')->get();
        return response()->json($drawings);
    }

    public function uploadDrawing(Request $request): JsonResponse
    {
        $data = $request->all();
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'pdf');
            $drawingId = $data['id'] ?? ('DRW' . Str::random(8));
            $filename = strtolower($drawingId) . '_' . Str::random(8) . '.' . $ext;
            $destPath = public_path('ejo-engineer-assets/uploads/drawings');
            if (! File::isDirectory($destPath)) {
                File::makeDirectory($destPath, 0777, true, true);
            }
            $file->move($destPath, $filename);
            $data['file_path'] = '/ejo-engineer-assets/uploads/drawings/' . $filename;
        }

        $drawingId = $data['id'] ?? ('DRW' . Str::random(8));
        $data['id'] = $drawingId;
        $data['uploaded_at'] = $data['uploaded_at'] ?? now()->toIso8601String();

        $drawing = Drawing::create($data);
        return response()->json(['status' => 'success', 'id' => $drawingId], 201);
    }

    public function updateDrawing(Request $request, string $id): JsonResponse
    {
        $drawing = Drawing::find($id);
        if (! $drawing) {
            return response()->json(['status' => 'error', 'message' => 'Drawing tidak ditemukan'], 404);
        }

        $data = $request->all();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'pdf');
            $filename = strtolower($id) . '_' . Str::random(8) . '.' . $ext;
            $destPath = public_path('ejo-engineer-assets/uploads/drawings');
            if (! File::isDirectory($destPath)) {
                File::makeDirectory($destPath, 0777, true, true);
            }
            $file->move($destPath, $filename);
            $data['file_path'] = '/ejo-engineer-assets/uploads/drawings/' . $filename;
        }

        $drawing->update($data);
        return response()->json(['status' => 'success', 'id' => $id]);
    }

    public function deleteDrawing(Request $request, string $id): JsonResponse
    {
        $drawing = Drawing::find($id);
        if (! $drawing) {
            return response()->json(['status' => 'error', 'message' => 'Drawing tidak ditemukan'], 404);
        }

        $drawing->delete();
        return response()->json(['status' => 'success', 'message' => 'Drawing berhasil dihapus']);
    }

    public function getRepairParts(): JsonResponse
    {
        $parts = RepairPart::all();
        return response()->json($parts);
    }

    public function createRepairPart(Request $request): JsonResponse
    {
        $data = $request->all();
        if (empty($data['id'])) {
            $data['id'] = 'RP' . date('ymdHis') . '_' . Str::random(4);
        }
        if (empty($data['name']) && ! empty($data['part_name'])) {
            $data['name'] = $data['part_name'];
        }
        if (empty($data['code']) && ! empty($data['part_number'])) {
            $data['code'] = $data['part_number'];
        }
        $part = RepairPart::create($data);
        return response()->json(['status' => 'success', 'id' => $part->id], 201);
    }

    public function deleteRepairPart(Request $request, string $id): JsonResponse
    {
        $part = RepairPart::find($id);
        if (! $part) {
            return response()->json(['status' => 'error', 'message' => 'Part tidak ditemukan'], 404);
        }

        $part->delete();
        return response()->json(['status' => 'success', 'message' => 'Part berhasil dihapus']);
    }

    public function getWspMaterials(): JsonResponse
    {
        $materials = WspMaterial::all();
        return response()->json($materials);
    }

    public function importWspMaterials(Request $request): JsonResponse
    {
        $items = $request->input('items', $request->all());
        if (! is_array($items)) {
            $items = [];
        }

        WspMaterial::truncate();
        $count = 0;
        foreach ($items as $item) {
            $mat = trim($item['material'] ?? '');
            if (! $mat) continue;
            WspMaterial::create([
                'material'    => $mat,
                'description' => trim($item['description'] ?? ''),
                'price'       => (float) ($item['price'] ?? 0.0),
            ]);
            $count++;
        }

        return response()->json(['status' => 'success', 'message' => "{$count} materials imported"]);
    }

    public function getSettings(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $data = $request->all();
        foreach ($data as $k => $v) {
            Setting::updateOrCreate(
                ['key' => $k],
                ['value' => (string) $v]
            );
        }
        return response()->json(['status' => 'success']);
    }

    public function getNotifications(Request $request): JsonResponse
    {
        $username = $request->query('username');
        if (! $username) {
            return response()->json(['message' => 'Parameter username diperlukan'], 400);
        }

        $notifs = Notification::where('target_username', $username)->orderByDesc('id')->get();
        return response()->json($notifs);
    }

    public function markAllNotificationsRead(Request $request): JsonResponse
    {
        $username = $request->query('username');
        if ($username) {
            Notification::where('target_username', $username)->update(['is_read' => 1]);
        }
        return response()->json(['status' => 'success']);
    }

    public function deleteNotifications(Request $request): JsonResponse
    {
        $username = $request->query('username');
        $notifId = $request->query('id');

        if ($notifId) {
            Notification::where('id', $notifId)->delete();
        } elseif ($username) {
            Notification::where('target_username', $username)->delete();
        }

        return response()->json(['status' => 'success']);
    }

    public function uploadFile(Request $request): JsonResponse
    {
        if (! $request->hasFile('file')) {
            return response()->json(['status' => 'error', 'message' => 'File tidak valid'], 400);
        }

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $filename = 'rev_' . Str::random(8) . '.' . $ext;

        $destPath = public_path('ejo-engineer-assets/uploads');
        if (! File::isDirectory($destPath)) {
            File::makeDirectory($destPath, 0777, true, true);
        }

        $file->move($destPath, $filename);
        $fileUrl = '/ejo-engineer-assets/uploads/' . $filename;

        return response()->json([
            'status'    => 'success',
            'file_url'  => $fileUrl,
            'file_name' => $file->getClientOriginalName(),
        ]);
    }
}
