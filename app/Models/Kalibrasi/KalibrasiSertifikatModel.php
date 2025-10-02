<?php

namespace App\Models\Kalibrasi;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiSertifikatModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_sertifikat';

    protected $fillable = [
        'kalibrasi_id',
        'user_id',
        'certificate_number',
        'status',
        'notes',
        'issued_at',
    ];

    // relasi ke transaksi kalibrasi
    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }

    // relasi ke user yang request sertifikat
    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // relasi ke approvals
    public function approvals()
    {
        return $this->hasMany(KalibrasiSertifikatApprovalModel::class, 'sertifikat_id');
    }

    // helper: cek apakah semua sudah approve
    public function isFullyApproved()
    {
        return $this->approvals()->where('status', 'pending')->count() === 0
            && $this->approvals()->where('status', 'rejected')->count() === 0;
    }
}
