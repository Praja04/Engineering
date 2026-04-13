<?php

namespace App\Models\Maintenance;

use App\Models\Maintenance\MtcElectricalModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maintenance\MtcUtilityModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Maintenance\MtcMotorPumpModel;

class MtcMainModel extends Model
{
    protected $table = 'mtc_main';

    protected $fillable = [
        'jenis_mtc',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'paket',
        'status',
        'keterangan',
        'korektif',
        'departemen',
        'area',
        'rekomendasi',
        'lokasi',
        'running_hour',
        'created_by',
        'updated_by',
    ];

    // ================= RELATIONS =================

    // Motor Pump
    public function motorPump()
    {
        return $this->hasOne(
            MtcMotorPumpModel::class,
            'mtc_main_id'
        );
    }

    public function utility()
    {
        return $this->hasOne(
            MtcUtilityModel::class,
            'mtc_main_id'
        );
    }

    public function electrical()
    {
        return $this->hasOne(
            MtcElectricalModel::class,
            'mtc_main_id'
        );
    }

    public function refrigerasi()
    {
        return $this->hasOne(
            MtcRefrigerasiModel::class,
            'mtc_main_id'
        );
    }

    public function electricEngine()
    {
        return $this->hasOne(
            MtcElectricEngineModel::class,
            'mtc_main_id'
        );
    }

    public function dieselEngine()
    {
        return $this->hasOne(
            MtcDieselEngineModel::class,
            'mtc_main_id'
        );
    }

    public function sipil()
    {
        return $this->hasOne(
            MtcSipilInspectionModel::class,
            'mtc_main_id'
        );
    }

    public function battery()
    {
        return $this->hasOne(
            MtcBatteryMainModel::class,
            'mtc_main_id'
        );
    }

    public function electricP2h()
    {
        return $this->hasOne(
            MtcElectricP2hInspectionModel::class,
            'mtc_main_id'
        );
    }

    public function dieselP2h()
    {
        return $this->hasOne(
            MtcDieselP2hInspectionModel::class,
            'mtc_main_id'
        );
    }

    public function kebutuhanMaterial()
    {
        return $this->hasMany(
            MtcKebutuhanMaterialModel::class,
            'mtc_main_id'
        );
    }

    // approval generic
    public function approvals()
    {
        return $this->hasMany(MtcApprovalModel::class, 'mtc_main_id')
            ->orderBy('level');
    }

    // helper
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
