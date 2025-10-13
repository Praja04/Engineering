<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class AirArea extends Model
{
    //
    //
    protected $table = 'air_area_utility';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_area'];

    public function types()
    {
        return $this->hasMany(ChemicalType::class);
    }
}
