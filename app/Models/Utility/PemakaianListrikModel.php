<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PemakaianListrikModel extends Model
{
    //
    //
    use HasFactory;

    protected $table = 'pemakaian_listrik_eng';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = ['waktu', 'operator', 'panel_type', 'volt', 'a', 'kw', 'mwh', 'cos'];

}
