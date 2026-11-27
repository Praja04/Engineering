<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccessControlController extends Controller
{
    /**
     * Memastikan hanya admin atau Server yang dapat mengakses halaman ini
     */
    private function authorizeAdmin()
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $isAdmin = ($user->role === 'Server' || strtolower((string)$user->username) === 'server' || strtolower((string)$user->jabatan) === 'admin' || $user->hasRole('admin'));
        if (!$isAdmin) {
            abort(403, 'Akses Terbatas: Halaman ini hanya dapat diakses oleh Administrator.');
        }
    }

    /**
     * Endpoint API JSON Data Access Control (Client-Side Rendering)
     */
    public function getData(Request $request)
    {
        $this->authorizeAdmin();

        $permissions = Permission::orderBy('group', 'asc')->orderBy('name', 'asc')->get();
        $roles = Role::with('permissions')->orderBy('name', 'asc')->get();
        $users = User::with(['customRoles', 'directPermissions'])->orderBy('username', 'asc')->get();
        $groups = Permission::select('group')->distinct()->pluck('group')->filter()->values();

        return response()->json([
            'status' => 'success',
            'permissions' => $permissions,
            'roles' => $roles,
            'users' => $users,
            'groups' => $groups,
            'stats' => [
                'total_permissions' => $permissions->count(),
                'total_roles' => $roles->count(),
                'total_groups' => $groups->count(),
                'total_users' => $users->count(),
            ]
        ]);
    }

    /**
     * Halaman Utama Manajemen Role & Permission (Shell View)
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        if ($request->ajax() || $request->wantsJson()) {
            return $this->getData($request);
        }

        return view('access_control.index');
    }

    /**
     * Simpan Permission Baru
     */
    public function storePermission(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:permissions,name',
            'group'       => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $cleanName = trim(preg_replace('/[^a-zA-Z0-9]+/', '_', strtolower($validated['name'])), '_');
        $validated['name'] = $cleanName;
        $validated['display_name'] = null;
        $permission = Permission::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Permission berhasil dibuat.',
            'data'    => $permission
        ]);
    }

    /**
     * Update Permission
     */
    public function updatePermission(Request $request, $id)
    {
        $this->authorizeAdmin();

        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:permissions,name,' . $permission->id,
            'group'       => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $cleanName = trim(preg_replace('/[^a-zA-Z0-9]+/', '_', strtolower($validated['name'])), '_');
        $validated['name'] = $cleanName;
        $validated['display_name'] = null;
        $permission->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Permission berhasil diperbarui.',
            'data'    => $permission
        ]);
    }

    /**
     * Hapus Permission
     */
    public function destroyPermission($id)
    {
        $this->authorizeAdmin();

        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Permission berhasil dihapus.'
        ]);
    }

    /**
     * Simpan Role Baru
     */
    public function storeRole(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name'         => 'required|string|max:100|unique:roles,name',
            'display_name' => 'required|string|max:150',
            'description'  => 'nullable|string|max:255',
            'permissions'  => 'nullable|array',
            'permissions.*'=> 'exists:permissions,id',
        ]);

        $validated['name'] = Str::slug($validated['name'], '_');

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name'         => $validated['name'],
                'display_name' => $validated['display_name'],
                'description'  => $validated['description'] ?? null,
            ]);

            if (!empty($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Role berhasil dibuat.',
                'data'    => $role
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update Role
     */
    public function updateRole(Request $request, $id)
    {
        $this->authorizeAdmin();

        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|max:100|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:150',
            'description'  => 'nullable|string|max:255',
            'permissions'  => 'nullable|array',
            'permissions.*'=> 'exists:permissions,id',
        ]);

        $validated['name'] = Str::slug($validated['name'], '_');

        DB::beginTransaction();
        try {
            $role->update([
                'name'         => $validated['name'],
                'display_name' => $validated['display_name'],
                'description'  => $validated['description'] ?? null,
            ]);

            $role->syncPermissions($validated['permissions'] ?? []);

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Role berhasil diperbarui.',
                'data'    => $role
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Hapus Role
     */
    public function destroyRole($id)
    {
        $this->authorizeAdmin();

        $role = Role::findOrFail($id);
        if ($role->name === 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Role admin tidak dapat dihapus.'], 403);
        }

        $role->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Role berhasil dihapus.'
        ]);
    }

    /**
     * Ambil data permissions & roles milik user tertentu
     */
    public function getUserAccess($id)
    {
        $this->authorizeAdmin();

        $user = User::with(['customRoles', 'directPermissions'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'user_id'            => $user->id,
                'username'           => $user->username,
                'fullname'           => $user->fullname,
                'jabatan'            => $user->jabatan,
                'roles'              => $user->customRoles->pluck('id')->toArray(),
                'direct_permissions' => $user->directPermissions->pluck('id')->toArray(),
            ]
        ]);
    }

    /**
     * Simpan permissions & roles user
     */
    public function updateUserAccess(Request $request, $id)
    {
        $this->authorizeAdmin();

        $user = User::findOrFail($id);

        $request->validate([
            'roles'         => 'nullable|array',
            'roles.*'       => 'exists:roles,id',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $user->syncRoles($request->roles ?? []);
            $user->syncPermissions($request->permissions ?? []);

            $user->load(['customRoles', 'directPermissions']);

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Hak akses user ' . $user->username . ' berhasil diperbarui.',
                'data'    => [
                    'user_id' => $user->id,
                    'roles' => $user->customRoles->map(fn($r) => [
                        'id' => $r->id,
                        'name' => $r->name,
                        'display_name' => $r->display_name,
                    ]),
                    'direct_permissions_count' => $user->directPermissions->count(),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

