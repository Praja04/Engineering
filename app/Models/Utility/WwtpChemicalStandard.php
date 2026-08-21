<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class WwtpChemicalStandard extends Model
{
    use HasFactory;

    protected $table = 'wwtp_chemical_standards';

    protected $fillable = [
        'chemical_name',
        'harga_standar',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'harga_standar' => 'double',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
