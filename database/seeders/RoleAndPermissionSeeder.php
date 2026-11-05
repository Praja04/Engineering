<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Project Utility
            ['name' => 'project_view',   'description' => 'Lihat Data Project',      'group' => 'Project Utility'],
            ['name' => 'project_create', 'description' => 'Buat Project Baru',       'group' => 'Project Utility'],
            ['name' => 'project_edit',   'description' => 'Edit & Tracking Project', 'group' => 'Project Utility'],
            ['name' => 'project_delete', 'description' => 'Hapus Project',           'group' => 'Project Utility'],
            ['name' => 'project_export', 'description' => 'Export Data Project',     'group' => 'Project Utility'],

            // Compressor
            ['name' => 'compressor_view',    'description' => 'Lihat Log Compressor',    'group' => 'Compressor'],
            ['name' => 'compressor_create',  'description' => 'Input Log Compressor',    'group' => 'Compressor'],
            ['name' => 'compressor_edit',    'description' => 'Edit Log Compressor',     'group' => 'Compressor'],
            ['name' => 'compressor_delete',  'description' => 'Hapus Log Compressor',    'group' => 'Compressor'],
            ['name' => 'compressor_approve', 'description' => 'Approval Log Compressor', 'group' => 'Compressor'],
            ['name' => 'compressor_export',  'description' => 'Export Log Compressor',   'group' => 'Compressor'],

            // WWTP
            ['name' => 'wwtp_view',    'description' => 'Lihat Data WWTP',       'group' => 'WWTP'],
            ['name' => 'wwtp_create',  'description' => 'Input Operasional WWTP', 'group' => 'WWTP'],
            ['name' => 'wwtp_edit',    'description' => 'Edit Operasional WWTP',  'group' => 'WWTP'],
            ['name' => 'wwtp_delete',  'description' => 'Hapus Data WWTP',        'group' => 'WWTP'],
            ['name' => 'wwtp_approve', 'description' => 'Approval WWTP',         'group' => 'WWTP'],
            ['name' => 'wwtp_export',  'description' => 'Export Data WWTP',       'group' => 'WWTP'],

            // Boiler
            ['name' => 'boiler_view',   'description' => 'Lihat Log Boiler',   'group' => 'Boiler'],
            ['name' => 'boiler_create', 'description' => 'Input Log Boiler',   'group' => 'Boiler'],
            ['name' => 'boiler_edit',   'description' => 'Edit Log Boiler',    'group' => 'Boiler'],
            ['name' => 'boiler_delete', 'description' => 'Hapus Log Boiler',   'group' => 'Boiler'],
            ['name' => 'boiler_export', 'description' => 'Export Log Boiler',  'group' => 'Boiler'],

            // Maintenance
            ['name' => 'maintenance_view',   'description' => 'Lihat Data Maintenance',   'group' => 'Maintenance'],
            ['name' => 'maintenance_create', 'description' => 'Buat WO Maintenance',      'group' => 'Maintenance'],
            ['name' => 'maintenance_edit',   'description' => 'Update WO Maintenance',    'group' => 'Maintenance'],
            ['name' => 'maintenance_delete', 'description' => 'Hapus Data Maintenance',   'group' => 'Maintenance'],
            ['name' => 'maintenance_export', 'description' => 'Export Data Maintenance',  'group' => 'Maintenance'],

            // Kalibrasi
            ['name' => 'kalibrasi_view',   'description' => 'Lihat Data Kalibrasi',  'group' => 'Kalibrasi'],
            ['name' => 'kalibrasi_create', 'description' => 'Input Data Kalibrasi',  'group' => 'Kalibrasi'],
            ['name' => 'kalibrasi_edit',   'description' => 'Edit Data Kalibrasi',   'group' => 'Kalibrasi'],
            ['name' => 'kalibrasi_delete', 'description' => 'Hapus Data Kalibrasi',  'group' => 'Kalibrasi'],

            // Master Data
            ['name' => 'master_view',   'description' => 'Lihat Data Master',    'group' => 'Master Data'],
            ['name' => 'master_manage', 'description' => 'Kelola Data Master',   'group' => 'Master Data'],

            // User & Access Management
            ['name' => 'user_manage',   'description' => 'Kelola Data User',        'group' => 'User Management'],
            ['name' => 'access_manage', 'description' => 'Kelola Role & Permission', 'group' => 'User Management'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }

        // Roles
        $roles = [
            [
                'name'         => 'admin',
                'display_name' => 'Administrator',
                'description'  => 'Akses penuh ke seluruh modul dan manajemen sistem'
            ],
            [
                'name'         => 'supervisor',
                'display_name' => 'Supervisor',
                'description'  => 'Akses operasional, monitoring dashboard, dan approval'
            ],
            [
                'name'         => 'foreman',
                'display_name' => 'Foreman',
                'description'  => 'Akses operasional lapangan, input, dan review berkala'
            ],
            [
                'name'         => 'operator_utility',
                'display_name' => 'Operator Utility',
                'description'  => 'Akses input dan pemantauan harian mesin utility'
            ],
            [
                'name'         => 'operator_maintenance',
                'display_name' => 'Operator Maintenance',
                'description'  => 'Akses input dan pemantauan pekerjaan maintenance'
            ],
        ];

        foreach ($roles as $r) {
            $roleModel = Role::updateOrCreate(['name' => $r['name']], $r);

            // Assign permissions per role
            if ($r['name'] === 'admin') {
                $roleModel->syncPermissions(Permission::pluck('id')->toArray());
            } elseif ($r['name'] === 'supervisor') {
                $roleModel->syncPermissions(Permission::whereNotIn('name', ['user_manage', 'access_manage'])->pluck('id')->toArray());
            } elseif ($r['name'] === 'foreman') {
                $roleModel->syncPermissions(Permission::whereIn('group', ['Project Utility', 'Compressor', 'WWTP', 'Boiler', 'Maintenance'])
                    ->whereNotIn('name', ['project_delete', 'compressor_delete', 'wwtp_delete', 'boiler_delete', 'maintenance_delete'])
                    ->pluck('id')->toArray());
            } elseif ($r['name'] === 'operator_utility') {
                $roleModel->syncPermissions(Permission::whereIn('group', ['Compressor', 'WWTP', 'Boiler'])
                    ->whereIn('name', ['compressor_view', 'compressor_create', 'compressor_edit', 'wwtp_view', 'wwtp_create', 'wwtp_edit', 'boiler_view', 'boiler_create', 'boiler_edit'])
                    ->pluck('id')->toArray());
            } elseif ($r['name'] === 'operator_maintenance') {
                $roleModel->syncPermissions(Permission::whereIn('group', ['Maintenance'])
                    ->whereIn('name', ['maintenance_view', 'maintenance_create', 'maintenance_edit'])
                    ->pluck('id')->toArray());
            }
        }

        // Hubungkan akun Admin / Server ke role admin
        $adminUsers = User::where('jabatan', 'admin')
            ->orWhere('role', 'Server')
            ->orWhere('username', 'server')
            ->get();

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($adminUsers as $adminUser) {
                $adminUser->assignRole($adminRole);
            }
        }
    }
}
