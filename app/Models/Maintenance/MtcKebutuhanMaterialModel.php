<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Maintenance\MtcMasterMaterialModel;
use App\Models\Maintenance\MtcMainModel;

class MtcKebutuhanMaterialModel extends Model
{
    protected $table = 'mtc_kebutuhan_material';

    protected $fillable = [
        'mtc_main_id',
        'mid',
        'deskripsi',
        'qty',
        'uom',
        'harga_satuan',
        'total_harga',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'qty'          => 'double',
        'harga_satuan' => 'double',
        'total_harga'  => 'double',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            // Auto lookup harga satuan dari Master Material jika belum diisi atau 0
            if (empty($model->harga_satuan) || floatval($model->harga_satuan) <= 0) {
                $master = null;
                if (!empty($model->mid)) {
                    $master = MtcMasterMaterialModel::where('mid', trim($model->mid))->first();
                }
                if (!$master && !empty($model->deskripsi)) {
                    $master = MtcMasterMaterialModel::where('deskripsi', trim($model->deskripsi))->first();
                }
                if ($master && floatval($master->harga) > 0) {
                    $model->harga_satuan = floatval($master->harga);
                }
            }

            // Hitung total_harga otomatis
            $qty = floatval($model->qty ?? 0);
            $hargaSatuan = floatval($model->harga_satuan ?? 0);
            $model->total_harga = round($qty * $hargaSatuan, 2);
        });
    }

    public function main()
    {
        return $this->belongsTo(MtcMainModel::class, 'mtc_main_id');
    }
}
