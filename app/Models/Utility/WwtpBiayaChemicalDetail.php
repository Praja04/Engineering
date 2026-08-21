<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class WwtpBiayaChemicalDetail extends Model
{
    use HasFactory;

    protected $table = 'wwtp_biaya_chemical_details';

    protected $fillable = [
        'wwtp_biaya_chemical_record_id',
        'chemical_standard_id',
        'qty',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'qty' => 'double',
    ];

    public function record()
    {
        return $this->belongsTo(WwtpBiayaChemicalRecord::class, 'wwtp_biaya_chemical_record_id');
    }

    public function chemicalStandard()
    {
        return $this->belongsTo(WwtpChemicalStandard::class, 'chemical_standard_id');
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
