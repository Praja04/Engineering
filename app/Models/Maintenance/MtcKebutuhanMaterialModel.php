<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Model;

class MtcKebutuhanMaterialModel extends Model
{
    protected $table = 'mtc_kebutuhan_material';

    protected $fillable = [
        'mtc_main_id',
        'mid',
        'deskripsi',
        'qty',
        'created_by',
        'updated_by',
    ];

    public function main()
    {
        return $this->belongsTo(MtcMainModel::class, 'mtc_main_id');
    }
}
