<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }

    public function givePermissionTo($permission)
    {
        $permId = is_numeric($permission) ? $permission : (is_string($permission) ? Permission::where('name', $permission)->value('id') : $permission->id);
        if ($permId && !$this->permissions()->where('permissions.id', $permId)->exists()) {
            $this->permissions()->attach($permId);
        }
        return $this;
    }

    public function revokePermissionTo($permission)
    {
        $permId = is_numeric($permission) ? $permission : (is_string($permission) ? Permission::where('name', $permission)->value('id') : $permission->id);
        if ($permId) {
            $this->permissions()->detach($permId);
        }
        return $this;
    }

    public function syncPermissions(array $permissionIdsOrNames)
    {
        $ids = [];
        foreach ($permissionIdsOrNames as $item) {
            if (is_numeric($item)) {
                $ids[] = (int) $item;
            } elseif (is_string($item)) {
                $id = Permission::where('name', $item)->value('id');
                if ($id) $ids[] = $id;
            } elseif (isset($item->id)) {
                $ids[] = $item->id;
            }
        }
        $this->permissions()->sync(array_unique($ids));
        return $this;
    }
}
