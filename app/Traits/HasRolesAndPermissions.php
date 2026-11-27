<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;

trait HasRolesAndPermissions
{
    /**
     * Relasi ke Roles (M-to-M via user_roles)
     */
    public function customRoles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * Relasi ke Direct Permissions (M-to-M via user_permissions)
     */
    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }

    /**
     * Cek apakah user adalah Administrator / Superadmin (Bypass All Permissions & Roles)
     */
    public function isAdministrator(): bool
    {
        // 1. Akun server root
        if ($this->role === 'Server' || strtolower((string)$this->username) === 'server') {
            return true;
        }

        // 2. Akun username 'admin'
        if (strtolower((string)$this->username) === 'admin') {
            return true;
        }

        // 3. Jabatan admin
        if (strtolower((string)$this->jabatan) === 'admin') {
            return true;
        }

        // 4. Role 'admin', 'administrator', atau 'superadmin' dari customRoles
        if ($this->customRoles && $this->customRoles->contains(function ($r) {
            return in_array(strtolower($r->name), ['admin', 'administrator', 'superadmin']);
        })) {
            return true;
        }

        // 5. String role lama mengandung 'admin' (misal: 'Admin Eng', 'Admin Mtc')
        if (str_contains(strtolower((string)$this->role), 'admin')) {
            return true;
        }

        return false;
    }

    /**
     * Cek apakah user memiliki Role tertentu
     */
    public function hasRole(string $roleName): bool
    {
        // Administrator otomatis bypass semua role
        if ($this->isAdministrator()) {
            return true;
        }

        return $this->customRoles->contains('name', $roleName);
    }

    /**
     * Cek apakah user memiliki salah satu Role dalam list
     */
    public function hasAnyRole(array $roles): bool
    {
        // Administrator otomatis bypass semua role
        if ($this->isAdministrator()) {
            return true;
        }

        return $this->customRoles->whereIn('name', $roles)->isNotEmpty();
    }

    /**
     * Cek apakah user memiliki Permission tertentu
     */
    public function hasPermission(string $permissionName): bool
    {
        // Administrator otomatis bypass SEMUA permission
        if ($this->isAdministrator()) {
            return true;
        }

        $normalized = str_replace('.', '_', $permissionName);

        // 1. Cek direct permissions
        if ($this->directPermissions->contains(function ($p) use ($permissionName, $normalized) {
            return $p->name === $permissionName || $p->name === $normalized;
        })) {
            return true;
        }

        // 2. Cek permissions via roles
        foreach ($this->customRoles as $role) {
            if ($role->permissions->contains(function ($p) use ($permissionName, $normalized) {
                return $p->name === $permissionName || $p->name === $normalized;
            })) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cek apakah user memiliki salah satu permission dalam list
     */
    public function hasAnyPermission(array $permissions): bool
    {
        // Administrator otomatis bypass SEMUA permission
        if ($this->isAdministrator()) {
            return true;
        }

        foreach ($permissions as $perm) {
            if ($this->hasPermission($perm)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dapatkan semua permissions (direct + via roles)
     */
    public function getAllPermissions()
    {
        if ($this->isAdministrator()) {
            return Permission::all();
        }

        $rolePermissions = $this->customRoles->loadMissing('permissions')->pluck('permissions')->flatten();
        return $this->directPermissions->merge($rolePermissions)->unique('id');
    }

    /**
     * Assign Role ke User
     */
    public function assignRole($role)
    {
        $roleId = is_numeric($role) ? $role : (is_string($role) ? Role::where('name', $role)->value('id') : $role->id);
        if ($roleId && !$this->customRoles()->where('roles.id', $roleId)->exists()) {
            $this->customRoles()->attach($roleId);
            $this->unsetRelation('customRoles');
        }
        return $this;
    }

    /**
     * Hapus Role dari User
     */
    public function removeRole($role)
    {
        $roleId = is_numeric($role) ? $role : (is_string($role) ? Role::where('name', $role)->value('id') : $role->id);
        if ($roleId) {
            $this->customRoles()->detach($roleId);
            $this->unsetRelation('customRoles');
        }
        return $this;
    }

    /**
     * Sync Roles User
     */
    public function syncRoles(array $roles)
    {
        $ids = [];
        foreach ($roles as $item) {
            if (is_numeric($item)) {
                $ids[] = (int) $item;
            } elseif (is_string($item)) {
                $id = Role::where('name', $item)->value('id');
                if ($id) $ids[] = $id;
            } elseif (isset($item->id)) {
                $ids[] = $item->id;
            }
        }
        $this->customRoles()->sync(array_unique($ids));
        $this->unsetRelation('customRoles');
        return $this;
    }

    /**
     * Berikan Direct Permission ke User
     */
    public function givePermissionTo($permission)
    {
        $permId = is_numeric($permission) ? $permission : (is_string($permission) ? Permission::where('name', $permission)->value('id') : $permission->id);
        if ($permId && !$this->directPermissions()->where('permissions.id', $permId)->exists()) {
            $this->directPermissions()->attach($permId);
            $this->unsetRelation('directPermissions');
        }
        return $this;
    }

    /**
     * Cabut Direct Permission dari User
     */
    public function revokePermissionTo($permission)
    {
        $permId = is_numeric($permission) ? $permission : (is_string($permission) ? Permission::where('name', $permission)->value('id') : $permission->id);
        if ($permId) {
            $this->directPermissions()->detach($permId);
            $this->unsetRelation('directPermissions');
        }
        return $this;
    }

    /**
     * Sync Direct Permissions User
     */
    public function syncPermissions(array $permissions)
    {
        $ids = [];
        foreach ($permissions as $item) {
            if (is_numeric($item)) {
                $ids[] = (int) $item;
            } elseif (is_string($item)) {
                $id = Permission::where('name', $item)->value('id');
                if ($id) $ids[] = $id;
            } elseif (isset($item->id)) {
                $ids[] = $item->id;
            }
        }
        $this->directPermissions()->sync(array_unique($ids));
        $this->unsetRelation('directPermissions');
        return $this;
    }
}
