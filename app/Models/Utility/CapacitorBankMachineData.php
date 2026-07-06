<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapacitorBankMachineData extends Model
{
    use HasFactory;

    protected $table = 'capacitor_bank_machine_data';

    protected $fillable = [
        'tanggal',
        'cap_type',
        'current',

        // Voltage LL
        'voltage_ll_Vab',
        'voltage_ll_Vbc',
        'voltage_ll_Vca',

        // Voltage LN
        'voltage_ln_Van',
        'voltage_ln_Vbn',
        'voltage_ln_Vcn',

        // Power (total only)
        'power_Ptot',
        'power_Qtot',
        'power_Stot',

        // Power factor per phase
        'pf_PFa',
        'pf_PFb',
        'pf_PFc',

        // Cos phi per phase
        'cosphi_dPFa',
        'cosphi_dPFb',
        'cosphi_dPFc',

        // Frequency
        'freq',

        // THD Current per phase
        'thd_i_Ia',
        'thd_i_Ib',
        'thd_i_Ic',

        // THD Voltage per phase
        'thd_v_Van',
        'thd_v_Vbn',
        'thd_v_Vcn',
    ];

    protected $casts = [
        'current' => 'decimal:3',

        'voltage_ll_Vab' => 'decimal:3',
        'voltage_ll_Vbc' => 'decimal:3',
        'voltage_ll_Vca' => 'decimal:3',

        'voltage_ln_Van' => 'decimal:3',
        'voltage_ln_Vbn' => 'decimal:3',
        'voltage_ln_Vcn' => 'decimal:3',

        'power_Ptot' => 'decimal:3',
        'power_Qtot' => 'decimal:3',
        'power_Stot' => 'decimal:3',

        'pf_PFa' => 'decimal:4',
        'pf_PFb' => 'decimal:4',
        'pf_PFc' => 'decimal:4',

        'cosphi_dPFa' => 'decimal:4',
        'cosphi_dPFb' => 'decimal:4',
        'cosphi_dPFc' => 'decimal:4',

        'freq' => 'decimal:3',

        'thd_i_Ia' => 'decimal:3',
        'thd_i_Ib' => 'decimal:3',
        'thd_i_Ic' => 'decimal:3',

        'thd_v_Van' => 'decimal:3',
        'thd_v_Vbn' => 'decimal:3',
        'thd_v_Vcn' => 'decimal:3',
    ];
}
