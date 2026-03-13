<?php

namespace App\Http\Controllers\Ejo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Ejo\EjoTicket;
use App\Models\Ejo\EjoClassification;
use Carbon\Carbon;

class EjoImportController extends Controller
{

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');

        $spreadsheet = IOFactory::load($file->getRealPath());

        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray();

        $imported = 0;
        $skipped = 0;

        foreach ($rows as $index => $row) {

            // skip header
            if ($index == 0) {
                continue;
            }

            $ticketId = $row[4];

            if (!$ticketId) {
                continue;
            }

            // skip jika sudah ada
            if (EjoTicket::where('ticket_id', $ticketId)->exists()) {
                $skipped++;
                continue;
            }

            $classificationId = $this->detectClassification($row[6]);

            EjoTicket::create([
                'ticket_id' => $ticketId,
                'os_in' => $row[1],
                'department' => $row[2],
                'request_date' => $this->parseDate($row[5]),
                'category' => $row[6],
                'module' => $row[7],
                'subject' => $row[8],
                'description' => $row[9],
                'requestor' => $row[10],
                'status' => $row[11],
                'type' => $row[12],
                'schedule' => $this->parseDate($row[13]),
                'est_time' => $row[14],
                'date_done' => $this->parseDate($row[15]),
                'classification_id' => $classificationId
            ]);

            $imported++;
        }

        return response()->json([
            'message' => 'Import selesai',
            'imported' => $imported,
            'skipped' => $skipped
        ]);
    }


    /**
     * Auto klasifikasi berdasarkan category
     */
    private function detectClassification($category)
    {
        if (!$category) return null;

        $category = strtolower($category);

        if (str_contains($category, 'drawing')) {
            return EjoClassification::where('name', 'Mekanik')->first()?->id;
        }

        if (str_contains($category, 'project')) {
            return EjoClassification::where('name', 'Mekanik')->first()?->id;
        }

        if (str_contains($category, 'maintenance')) {
            return EjoClassification::where('name', 'Maintenance / Improvement')->first()?->id;
        }

        if (str_contains($category, 'repair')) {
            return EjoClassification::where('name', 'Repair Part')->first()?->id;
        }

        return null;
    }


    /**
     * Parse tanggal Excel
     */
    private function parseDate($value)
    {
        if (!$value) return null;

        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
