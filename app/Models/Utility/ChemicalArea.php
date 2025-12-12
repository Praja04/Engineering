<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class ChemicalArea extends Model
{
    //
    //
    protected $table = 'chemical_areas';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_area'];

    public function types()
    {
        return $this->hasMany(ChemicalType::class);
    }
}
