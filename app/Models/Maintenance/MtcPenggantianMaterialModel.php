<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Model;

class MtcPenggantianMaterialModel extends Model
{
    protected $table = 'mtc_penggantian_material';

    protected $fillable = [
        'mtc_main_id',
        'mid',
        'deskripsi',
        'qty',
        'uom',
        'created_by',
        'updated_by',
    ];

    public function main()
    {
        return $this->belongsTo(MtcMainModel::class, 'mtc_main_id');
    }
}
