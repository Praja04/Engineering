<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwtpInfluentHarian extends Model
{
    //
    use HasFactory;

    protected $table = 'wwtp_influent_harian';

    protected $fillable = [
        'tanggal',
        'shift',
        'pit_sparta',
        'pit_sparta_awal',
        'pit_garam',
        'pit_garam_awal',
        'pit_domestik',
        'pit_domestik_awal',
        'pit_produksi_step3',
        'pit_produksi_step3_awal',
        'pit_storage',
        'pit_storage_awal',
        'pit_proses_wwtp2',
        'pit_proses_wwtp2_awal',
        'pit_outlet',
        'pit_outlet_awal',
        'pit_boiler',
        'pit_boiler_awal',
        'debit1',
        'running_wwtp1',
        'debit2',
        'running_wwtp2',
    ];

    /**
     * Recalculate _awal fields chronologically starting from a certain date
     * to keep data integrity intact when edits/inserts/deletions happen.
     */
    public static function recalculateAwalFieldsFrom($tanggal)
    {
        // Get all records starting from $tanggal
        $records = self::where('tanggal', '>=', $tanggal)
            ->orderBy('tanggal', 'asc')
            ->orderByRaw("CASE 
                WHEN shift = 'shift1' THEN 1 
                WHEN shift = 'shift2' THEN 2 
                WHEN shift = 'shift3' THEN 3 
                ELSE 4 
            END ASC")
            ->get();

        foreach ($records as $record) {
            $dirty = false;
            
            // Find preceding record using the new logic
            $tanggalRecord = $record->tanggal;
            $shiftRecord = $record->shift;
            
            if ($shiftRecord !== 'shift1') {
                $preceding = self::where('tanggal', $tanggalRecord)
                    ->where('shift', '<', $shiftRecord)
                    ->orderByRaw("CASE 
                        WHEN shift = 'shift3' THEN 3 
                        WHEN shift = 'shift2' THEN 2 
                        WHEN shift = 'shift1' THEN 1 
                        ELSE 0 
                    END DESC")
                    ->first();
                
                if (!$preceding) {
                    $preceding = self::where('tanggal', '<', $tanggalRecord)
                        ->orderBy('tanggal', 'desc')
                        ->orderByRaw("CASE 
                            WHEN shift = 'shift3' THEN 3 
                            WHEN shift = 'shift2' THEN 2 
                            WHEN shift = 'shift1' THEN 1 
                            ELSE 0 
                        END DESC")
                        ->first();
                }
            } else {
                $preceding = self::where('tanggal', '<', $tanggalRecord)
                    ->orderBy('tanggal', 'desc')
                    ->orderByRaw("CASE 
                        WHEN shift = 'shift3' THEN 3 
                        WHEN shift = 'shift2' THEN 2 
                        WHEN shift = 'shift1' THEN 1 
                        ELSE 0 
                    END DESC")
                    ->first();
            }

            $fields = [
                'pit_sparta', 'pit_garam', 'pit_domestik', 'pit_produksi_step3',
                'pit_storage', 'pit_proses_wwtp2', 'pit_outlet', 'pit_boiler'
            ];

            foreach ($fields as $field) {
                $awalField = $field . '_awal';
                $expectedVal = 0;
                if ($preceding) {
                    // jika di kolom utama nya ada (tidak nol), ambil kolom utama, jika tidak ada, ambil kolom _awal
                    $expectedVal = (float) $preceding->$field ?: (float) $preceding->$awalField ?: 0;
                } else {
                    // Jika data awal benar-benar kosong (tidak ada preceding record), set data awal = data sekarang (kolom utama) dari record itu sendiri
                    $expectedVal = (float) $record->$field;
                }
                
                if ($record->$awalField != $expectedVal) {
                    $record->$awalField = $expectedVal;
                    $dirty = true;
                }
            }

            if ($dirty) {
                $record->save();
            }
        }
    }
}

