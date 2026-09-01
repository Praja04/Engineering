<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\EjoRoleMapperService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'show_status_prop' => 'boolean',
        ];
    }

    /**
     * Model Boot Event: Auto-inject EJO Role, Dept & Section
     * setiap kali ada user baru yang dibuat atau user diupdate di sistem internal.
     */
    protected static function booted()
    {
        static::saving(function ($user) {
            // Jika role belum diset atau masih kosong, auto-inject sesuai jabatan & departemen
            if (empty($user->role) || empty($user->dept)) {
                $mapped = EjoRoleMapperService::resolveEjoAttributes(
                    $user->username,
                    $user->jabatan,
                    $user->departemen,
                    $user->bagian,
                    $user->role,
                    $user->dept
                );

                if (empty($user->role)) {
                    $user->role = $mapped['role'];
                }
                if (empty($user->dept)) {
                    $user->dept = $mapped['dept'];
                }
                if (empty($user->section) && !empty($mapped['section'])) {
                    $user->section = $mapped['section'];
                }
                if (empty($user->access_permissions) && !empty($mapped['access_permissions'])) {
                    $user->access_permissions = $mapped['access_permissions'];
                }
            }
        });
    }

    public function getImageUrlAttribute()
    {
        if ($this->avatar) {
            return $this->avatar;
        }
        return $this->image && url(Storage::disk('public')->exists($this->image))
            ? url(Storage::url($this->image))
            : asset('material/assets/images/users/user-dummy-img.jpg');
    }
}
