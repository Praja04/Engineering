<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcSipilInspectionModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_sipil_inspections';

    protected $fillable = [
        'mtc_main_id',
        'plumbing',
        'plafon',
        'lantai',
        'dinding',
        'jendela',
        'pintu',
        'rooling_fast_door',
    ];

    protected $casts = [
        'plumbing' => 'boolean',
        'plafon' => 'boolean',
        'lantai' => 'boolean',
        'dinding' => 'boolean',
        'jendela' => 'boolean',
        'pintu' => 'boolean',
        'rooling_fast_door' => 'boolean',
    ];

    public function main()
    {
        return $this->belongsTo(MtcMainModel::class, 'mtc_main_id');
    }
}
