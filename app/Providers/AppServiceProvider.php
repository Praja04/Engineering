<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Gate::before untuk penanganan global:
        // - Abaikan ability khusus 'role' dan 'permission' agar didelegasikan ke Gate::define masing-masing
        // - Akun Administrator otomatis bypass semua permission
        // - Mendukung pengecekan langsung: @can('project.create') atau auth()->user()->can('project.create')
        Gate::before(function ($user, $ability, $args = []) {
            if (in_array($ability, ['role', 'permission'])) {
                return null;
            }

            if (method_exists($user, 'isAdministrator') && $user->isAdministrator()) {
                return true;
            }

            if (method_exists($user, 'hasPermission') && $user->hasPermission($ability)) {
                return true;
            }
        });

        // 2. Gate 'role': mendukung @can('role', 'administrator') atau @can('role', ['admin', 'supervisor'])
        Gate::define('role', function ($user, $role) {
            if (method_exists($user, 'isAdministrator') && $user->isAdministrator()) {
                return true;
            }

            if (is_array($role)) {
                return method_exists($user, 'hasAnyRole') ? $user->hasAnyRole($role) : false;
            }

            return method_exists($user, 'hasRole') ? $user->hasRole($role) : false;
        });

        // 3. Gate 'permission': mendukung @can('permission', 'project.create') atau @can('permission', ['project.create', 'project.edit'])
        Gate::define('permission', function ($user, $permission) {
            if (method_exists($user, 'isAdministrator') && $user->isAdministrator()) {
                return true;
            }

            if (is_array($permission)) {
                return method_exists($user, 'hasAnyPermission') ? $user->hasAnyPermission($permission) : false;
            }

            return method_exists($user, 'hasPermission') ? $user->hasPermission($permission) : false;
        });

        // 4. Custom Blade Directives alternatif (opsional untuk kenyamanan)
        Blade::if('role', function ($role) {
            return auth()->check() && (
                (method_exists(auth()->user(), 'isAdministrator') && auth()->user()->isAdministrator()) ||
                (is_array($role) ? auth()->user()->hasAnyRole($role) : auth()->user()->hasRole($role))
            );
        });

        Blade::if('hasrole', function ($role) {
            return auth()->check() && (
                (method_exists(auth()->user(), 'isAdministrator') && auth()->user()->isAdministrator()) ||
                (is_array($role) ? auth()->user()->hasAnyRole($role) : auth()->user()->hasRole($role))
            );
        });

        Blade::if('permission', function ($permission) {
            return auth()->check() && (
                (method_exists(auth()->user(), 'isAdministrator') && auth()->user()->isAdministrator()) ||
                (is_array($permission) ? auth()->user()->hasAnyPermission($permission) : auth()->user()->hasPermission($permission))
            );
        });

        Blade::if('haspermission', function ($permission) {
            return auth()->check() && (
                (method_exists(auth()->user(), 'isAdministrator') && auth()->user()->isAdministrator()) ||
                (is_array($permission) ? auth()->user()->hasAnyPermission($permission) : auth()->user()->hasPermission($permission))
            );
        });
    }
}
