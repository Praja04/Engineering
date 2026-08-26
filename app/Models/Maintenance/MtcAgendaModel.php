<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcAgendaModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_agenda';

    protected $fillable = [
        'mesin_id',
        'tahun',
        'bulan',
        'minggu_ke',
        'paket',
        'tanggal',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'minggu_ke' => 'integer',
        'tanggal' => 'date',
    ];

    public function mesin()
    {
        return $this->belongsTo(MtcMasterMesinModel::class, 'mesin_id');
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
