<?php

namespace App\Services;

class EjoRoleMapperService
{
    /**
     * Otomatis memetakan (inject) role & dept EJO Engineer
     * berdasarkan kombinasi jabatan, departemen, bagian, dan username internal PT BAS.
     */
    public static function resolveEjoAttributes(
        ?string $username,
        ?string $jabatan,
        ?string $departemen,
        ?string $bagian = null,
        ?string $existingRole = null,
        ?string $existingDept = null
    ): array {
        $uLower = strtolower(trim($username ?? ''));
        $jLower = strtolower(trim($jabatan ?? ''));
        $dLower = strtolower(trim($departemen ?? ''));
        $bLower = strtolower(trim($bagian ?? ''));

        // 1. Akun Server Root
        if ($uLower === 'server' || $existingRole === 'Server') {
            return [
                'role' => 'Server',
                'dept' => 'ENG',
                'section' => 'System Root',
                'access_permissions' => null
            ];
        }

        // 2. Akun IT / Admin
        if ($uLower === 'admin' || $jLower === 'admin' || $dLower === 'it' || $bLower === 'it') {
            return [
                'role' => 'Admin Eng',
                'dept' => 'ENG',
                'section' => 'IT / Engineering Admin',
                'access_permissions' => null
            ];
        }

        // 3. Drafter Khusus (Sipil & Mekanikal)
        if ($uLower === 'diki' || $uLower === 'rifan' || str_contains($bLower, 'drafter') || str_contains($uLower, 'drafter')) {
            return [
                'role' => 'Drafter',
                'dept' => 'ENG',
                'section' => str_contains($uLower, 'rifan') ? 'Mekanikal' : 'Sipil',
                'access_permissions' => json_encode([
                    'can_create_ejo' => true,
                    'can_upload_drawing' => true,
                    'can_edit_ejo' => true
                ])
            ];
        }

        // 4. SPV / Manager Khusus Pemohon berdasarkan List Resmi User
        // SPV PRD
        if ($uLower === 'andi_y' || $uLower === 'andi yulianto' || ($jLower === 'supervisor' && $dLower === 'produksi')) {
            return [
                'role' => 'Supervisor PRD',
                'dept' => 'PRD',
                'section' => $bagian ?: 'Produksi',
                'access_permissions' => json_encode(['can_sign_approval' => true, 'can_create_ejo' => true])
            ];
        }
        // SPV WRH
        if ($uLower === 'endro' || $uLower === 'endro juniarto' || ($jLower === 'supervisor' && ($dLower === 'warehouse' || $dLower === 'gudang'))) {
            return [
                'role' => 'Supervisor WRH',
                'dept' => 'WRH',
                'section' => $bagian ?: 'Warehouse',
                'access_permissions' => json_encode(['can_sign_approval' => true, 'can_create_ejo' => true])
            ];
        }
        // SPV EUT
        if ($uLower === 'muhono_eut' || $uLower === 'reja' || ($jLower === 'supervisor' && $dLower === 'eut')) {
            return [
                'role' => 'Supervisor EUT',
                'dept' => 'EUT',
                'section' => $bagian ?: 'Engineer Utility',
                'access_permissions' => json_encode(['can_sign_approval' => true, 'can_create_ejo' => true])
            ];
        }
        // SPV EPR
        if ($uLower === 'usep' || $uLower === 'usep hermawan' || ($jLower === 'supervisor' && $dLower === 'epr')) {
            return [
                'role' => 'Supervisor EPR',
                'dept' => 'EPR',
                'section' => $bagian ?: 'Engineering Produksi',
                'access_permissions' => json_encode(['can_sign_approval' => true, 'can_create_ejo' => true])
            ];
        }
        // SPV QC
        if ($uLower === 'veronica' || $uLower === 'veronica ong' || ($jLower === 'supervisor' && ($dLower === 'quality control' || $dLower === 'qc'))) {
            return [
                'role' => 'Supervisor QC',
                'dept' => 'QC',
                'section' => $bagian ?: 'Quality Control',
                'access_permissions' => json_encode(['can_sign_approval' => true, 'can_create_ejo' => true])
            ];
        }
        // SPV GA
        if ($uLower === 'nancy' || $uLower === 'yongki' || ($jLower === 'supervisor' && ($dLower === 'hrga' || $dLower === 'ga'))) {
            return [
                'role' => 'Supervisor GA',
                'dept' => 'GA',
                'section' => $bagian ?: 'General Affairs / HR',
                'access_permissions' => json_encode(['can_sign_approval' => true, 'can_create_ejo' => true])
            ];
        }

        // 5. Staff Pemohon Khusus per Departemen
        // PRD Staff
        if (in_array($uLower, ['syawal', 'zikautsar', 'ahmad zikautsar']) || ($dLower === 'produksi' && $jLower !== 'supervisor' && $jLower !== 'dept_head')) {
            return [
                'role' => 'user_PRD',
                'dept' => 'PRD',
                'section' => $bagian ?: (str_contains($uLower, 'syawal') ? 'PRD Proses' : 'PRD Retail'),
                'access_permissions' => null
            ];
        }
        // WRH Staff
        if (in_array($uLower, ['alfian']) || (($dLower === 'warehouse' || $dLower === 'gudang') && $jLower !== 'supervisor' && $jLower !== 'dept_head')) {
            return [
                'role' => 'user_WRH',
                'dept' => 'WRH',
                'section' => $bagian ?: 'Gudang / Warehouse',
                'access_permissions' => null
            ];
        }
        // EUT Staff
        if (in_array($uLower, ['puput', 'puput susanto', 'parhan', 'ahmad parhan', 'miftah', 'miftah hasan fuadi']) || ($dLower === 'eut' && $jLower !== 'supervisor' && $jLower !== 'dept_head')) {
            $sec = 'Utility';
            if (str_contains($uLower, 'parhan') || str_contains($bLower, 'wwtp')) $sec = 'WWTP';
            if (str_contains($uLower, 'miftah') || str_contains($bLower, 'otomotif')) $sec = 'Otomotif & Maintenance';
            return [
                'role' => 'user_EUT',
                'dept' => 'EUT',
                'section' => $bagian ?: $sec,
                'access_permissions' => null
            ];
        }
        // EPR Staff
        if (in_array($uLower, ['feny', 'feny logina', 'dicky', 'dicky syaiful', 'dodi', 'dodi simanjuntak']) || ($dLower === 'epr' && $jLower !== 'supervisor' && $jLower !== 'dept_head')) {
            $sec = 'Part Keeper';
            if (str_contains($uLower, 'dicky')) $sec = 'PM Retail';
            if (str_contains($uLower, 'dodi')) $sec = 'PM Proses';
            return [
                'role' => 'user_EPR',
                'dept' => 'EPR',
                'section' => $bagian ?: $sec,
                'access_permissions' => null
            ];
        }
        // QC Staff
        if (in_array($uLower, ['intan', 'intan purnama', 'annisa', 'annisa nurfitriana', 'yessica', 'yessica tania', 'hesti', 'hesti kurniati', 'fina']) || (($dLower === 'quality control' || $dLower === 'qc') && $jLower !== 'supervisor' && $jLower !== 'dept_head')) {
            $sec = 'Quality Control';
            if (str_contains($uLower, 'intan')) $sec = 'Kimia & Mikro';
            if (str_contains($uLower, 'annisa')) $sec = 'Retail';
            if (str_contains($uLower, 'yessica')) $sec = 'RnD (Research)';
            if (str_contains($uLower, 'hesti')) $sec = 'RM (Raw Material)';
            return [
                'role' => 'user_QC',
                'dept' => 'QC',
                'section' => $bagian ?: $sec,
                'access_permissions' => null
            ];
        }
        // GA Staff
        if (in_array($uLower, ['tashya', 'tashya claudea']) || (($dLower === 'hrga' || $dLower === 'ga') && $jLower !== 'supervisor' && $jLower !== 'dept_head')) {
            return [
                'role' => 'user_GA',
                'dept' => 'GA',
                'section' => $bagian ?: 'General Affairs',
                'access_permissions' => null
            ];
        }
        // TMB Staff
        if (in_array($uLower, ['dedi_h', 'dedi hartono', 'dedi']) || ($dLower === 'tmb' && $jLower !== 'supervisor' && $jLower !== 'dept_head')) {
            return [
                'role' => 'user_TMB',
                'dept' => 'TMB',
                'section' => $bagian ?: 'Tambang',
                'access_permissions' => null
            ];
        }

        // 6. Engineering Management (Dept Head / Manager Eng)
        if ($jLower === 'dept_head') {
            if ($dLower === 'engineering' || $dLower === 'eng' || empty($dLower)) {
                return [
                    'role' => 'Manager Eng',
                    'dept' => 'ENG',
                    'section' => 'Engineering Management',
                    'access_permissions' => json_encode([
                        'can_sign_approval' => true,
                        'can_view_all_dept' => true,
                        'can_create_ejo' => true
                    ])
                ];
            } else {
                $deptCode = self::normalizeDeptCode($departemen ?: $bagian);
                return [
                    'role' => "Manager {$deptCode}",
                    'dept' => $deptCode,
                    'section' => $bagian ?: $departemen,
                    'access_permissions' => json_encode([
                        'can_sign_approval' => true,
                        'can_create_ejo' => true
                    ])
                ];
            }
        }

        // 7. Engineering Supervisor (Supervisor Eng)
        if ($jLower === 'supervisor') {
            if ($dLower === 'engineering' || $dLower === 'eng') {
                return [
                    'role' => 'Supervisor Eng',
                    'dept' => 'ENG',
                    'section' => $bagian ?: 'Engineering Supervisor',
                    'access_permissions' => json_encode([
                        'can_sign_approval' => true,
                        'can_view_all_dept' => true,
                        'can_create_ejo' => true
                    ])
                ];
            } else {
                $deptCode = self::normalizeDeptCode($departemen ?: $bagian);
                return [
                    'role' => "Supervisor {$deptCode}",
                    'dept' => $deptCode,
                    'section' => $bagian ?: $departemen,
                    'access_permissions' => json_encode([
                        'can_sign_approval' => true,
                        'can_create_ejo' => true
                    ])
                ];
            }
        }

        // 8. Engineering Foreman (Foreman Eng -> Admin Panel Access)
        if ($jLower === 'foreman' && ($dLower === 'engineering' || $dLower === 'eng')) {
            return [
                'role' => 'Foreman Eng',
                'dept' => 'ENG',
                'section' => $bagian ?: 'Engineering Maintenance',
                'access_permissions' => json_encode([
                    'can_manage_parts' => true,
                    'can_create_ejo' => true,
                    'can_edit_ejo' => true,
                    'can_view_all_dept' => true
                ])
            ];
        }

        // 9. Teknisi Lapangan / Operator Engineering (Mekanik, Elektrik, Sipil, Kalibrasi, Program, dsb.)
        if ($dLower === 'engineering' || $dLower === 'eng') {
            $role = 'Mekanik'; // default teknisi
            $section = $bagian ?: 'Maintenance';

            if (str_contains($bLower, 'kalibrasi') || str_contains($uLower, 'kalibrasi')) {
                $role = 'Kalibrasi';
                $section = 'Kalibrasi';
            } elseif (str_contains($bLower, 'wwtp') || str_contains($uLower, 'wwtp') || str_contains($uLower, 'spl') || str_contains($uLower, 'sipil')) {
                $role = 'Sipil';
                $section = 'WWTP / Sipil';
            } elseif (str_contains($bLower, 'listrik') || str_contains($bLower, 'elektrik') || str_contains($uLower, 'oto')) {
                $role = 'Elektrik';
                $section = 'Elektrik / Otomasi';
            } elseif (str_contains($bLower, 'workshop') || str_contains($bLower, 'project')) {
                $role = 'Mekanik';
                $section = 'Workshop & Project';
            }

            return [
                'role' => $role,
                'dept' => 'ENG',
                'section' => $section,
                'access_permissions' => null
            ];
        }

        // 10. Fallback Departemen Pemohon
        $deptCode = self::normalizeDeptCode($departemen ?: $bagian);
        return [
            'role' => "user_{$deptCode}",
            'dept' => $deptCode,
            'section' => $bagian ?: $departemen,
            'access_permissions' => null
        ];
    }

    private static function normalizeDeptCode(?string $dept): string
    {
        $d = strtoupper(trim($dept ?? ''));
        if (str_contains($d, 'PRODUKSI') || $d === 'PRD') return 'PRD';
        if (str_contains($d, 'QUALITY') || str_contains($d, 'QC')) return 'QC';
        if (str_contains($d, 'WAREHOUSE') || str_contains($d, 'WRH') || str_contains($d, 'GUDANG')) return 'WRH';
        if (str_contains($d, 'HRGA') || str_contains($d, 'HR') || str_contains($d, 'GA')) return 'GA';
        if (str_contains($d, 'EUT') || str_contains($d, 'UTILITY')) return 'EUT';
        if (str_contains($d, 'EPR')) return 'EPR';
        if (str_contains($d, 'TMB') || str_contains($d, 'TAMBANG')) return 'TMB';
        if (str_contains($d, 'IT')) return 'IT';
        if (str_contains($d, 'ENG')) return 'ENG';

        return !empty($d) ? substr($d, 0, 4) : 'PRD';
    }
}
