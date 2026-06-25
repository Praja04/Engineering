<?php

namespace App\Services;

use App\Models\Utility\wwtp_analisa\WwtpAnalisa;
use App\Models\Utility\wwtp_analisa\WwtpParameter;
use App\Models\Utility\wwtp_analisa\WwtpPoint;
use App\Models\Utility\wwtp_analisa\WwtpStandard;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    /**
     * Synchronize WWTP Analisa data to Google Sheets.
     */
    public static function sync()
    {
        $json = env('GOOGLE_SERVICE_ACCOUNT_JSON');
        $spreadsheetId = env('GOOGLE_SHEETS_SPREADSHEET_ID');
        $sheetName = env('GOOGLE_SHEETS_SHEET_NAME', 'Sheet1');

        if (!$json || !$spreadsheetId) {
            Log::info('Google Sheets sync skipped: GOOGLE_SERVICE_ACCOUNT_JSON or GOOGLE_SHEETS_SPREADSHEET_ID is not configured in .env.');
            return;
        }

        try {
            // Resolve the JSON content if a file path is provided
            $jsonContent = self::resolveJsonContent($json);
            if (!$jsonContent) {
                throw new \Exception('Could not resolve Google Service Account JSON content from string or file path.');
            }

            // 1. Authenticate and retrieve OAuth2 Access Token
            $accessToken = self::getGoogleAccessToken($jsonContent, 'https://www.googleapis.com/auth/spreadsheets');

            // 2. Fetch and format dataset
            $data = self::buildSheetData();

            if (empty($data)) {
                Log::info('Google Sheets sync skipped: No data available to sync.');
                return;
            }

            // 3. Clear target sheet to remove any old/residual data across the entire sheet
            $clearUrl = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/" . rawurlencode($sheetName) . ":clear";
            $clearResponse = Http::withToken($accessToken)
                ->withBody('{}', 'application/json')
                ->post($clearUrl);

            if ($clearResponse->failed()) {
                throw new \Exception('Failed to clear spreadsheet: ' . $clearResponse->body());
            }

            // 4. Retrieve sheet properties to ensure the sheet has at least 26 columns (default Google Sheets size)
            $spreadsheetUrl = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}";
            $sheetResponse = Http::withToken($accessToken)->get($spreadsheetUrl);

            if ($sheetResponse->successful()) {
                $sheets = $sheetResponse->json()['sheets'] ?? [];
                $targetSheetId = 0;
                $currentColumnCount = 26; // fallback default
                foreach ($sheets as $s) {
                    if (isset($s['properties']) && $s['properties']['title'] === $sheetName) {
                        $targetSheetId = $s['properties']['sheetId'];
                        $currentColumnCount = $s['properties']['gridProperties']['columnCount'] ?? 26;
                        break;
                    }
                }

                // Determine the minimum required columns (at least 26, or more if data exceeds it)
                $dataColumnCount = 0;
                foreach ($data as $row) {
                    if (is_array($row)) {
                        $dataColumnCount = max($dataColumnCount, count($row));
                    }
                }
                $requiredColumnCount = max(26, $dataColumnCount);

                // Only expand columns if current sheet has fewer columns than required (never shrink to avoid deleting user custom columns)
                if ($currentColumnCount < $requiredColumnCount) {
                    $batchUpdateUrl = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}:batchUpdate";
                    Http::withToken($accessToken)->post($batchUpdateUrl, [
                        'requests' => [
                            [
                                'updateSheetProperties' => [
                                    'properties' => [
                                        'sheetId' => $targetSheetId,
                                        'gridProperties' => [
                                            'columnCount' => $requiredColumnCount
                                        ]
                                    ],
                                    'fields' => 'gridProperties.columnCount'
                                ]
                            ]
                        ]
                    ]);
                }
            }

            // 5. Write new dataset to spreadsheet starting from cell A1
            $updateUrl = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/" . rawurlencode($sheetName) . "!A1?valueInputOption=USER_ENTERED";
            $updateResponse = Http::withToken($accessToken)->put($updateUrl, [
                'range' => "{$sheetName}!A1",
                'majorDimension' => 'ROWS',
                'values' => $data
            ]);

            if ($updateResponse->failed()) {
                throw new \Exception('Failed to update spreadsheet values: ' . $updateResponse->body());
            }

            Log::info('Google Sheets sync completed successfully.');
        } catch (\Exception $e) {
            Log::error('Google Sheets sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate OAuth2 access token for Google API via Service Account JWT (RS256).
     */
    private static function getGoogleAccessToken($serviceAccountJson, $scope)
    {
        $creds = json_decode($serviceAccountJson, true);
        if (!$creds || !isset($creds['private_key']) || !isset($creds['client_email'])) {
            throw new \Exception('Invalid Service Account JSON credentials.');
        }

        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();
        $payload = json_encode([
            'iss' => $creds['client_email'],
            'scope' => $scope,
            'aud' => $creds['token_uri'] ?? 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;
        $signature = '';

        $privateKey = $creds['private_key'];
        if (!openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new \Exception('Failed to sign Google Auth JWT using openssl_sign.');
        }

        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        $jwt = $signatureInput . "." . $base64UrlSignature;

        $response = Http::asForm()->post($creds['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch OAuth2 token from Google: ' . $response->body());
        }

        $resData = $response->json();
        return $resData['access_token'];
    }

    /**
     * Build the 2D array representation of the WWTP Analisa dataset.
     */
    private static function buildSheetData()
    {
        // Get all analysis records with details, sorted chronologically by date
        $analisaRecords = WwtpAnalisa::with(['details.parameter', 'details.point'])
            ->orderBy('analisa_date', 'asc')
            ->get();

        if ($analisaRecords->isEmpty()) {
            return [];
        }

        // Get unique sorted dates from actual database records
        $dates = $analisaRecords->pluck('analisa_date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        // Build lookup table: lookup[date][parameter_id][point_id] = value
        $lookup = [];
        foreach ($analisaRecords as $record) {
            $dateStr = $record->analisa_date->format('Y-m-d');
            foreach ($record->details as $detail) {
                $lookup[$dateStr][$detail->parameter_id][$detail->point_id] = $detail->hasil_analisa;
            }
        }

        // Get all parameters and active standards configuration
        $parameters = WwtpParameter::orderBy('parameter_name')->get();
        $standards = WwtpStandard::with(['point'])
            ->join('wwtp_point', 'wwtp_standards.point_id', '=', 'wwtp_point.id')
            ->select('wwtp_standards.*')
            ->orderBy('wwtp_point.point_name')
            ->get()
            ->groupBy('parameter_id');

        $activeParameters = [];
        foreach ($parameters as $param) {
            if (isset($standards[$param->id]) && $standards[$param->id]->isNotEmpty()) {
                $activeParameters[] = [
                    'parameter' => $param,
                    'standards' => $standards[$param->id]
                ];
            }
        }

        $sheetData = [];

        // Header Rows
        $sheetData[] = ['LAPORAN ANALISA WWTP'];
        $sheetData[] = ['Terakhir Diperbarui: ' . now()->format('d/m/Y H:i:s')];
        $sheetData[] = []; // empty divider row

        // Row 4: Header Row 1 (Day names in Indonesian)
        $dayHeaders = ['', '', ''];
        foreach ($dates as $date) {
            $dayHeaders[] = Carbon::parse($date)->locale('id')->translatedFormat('l');
        }
        $sheetData[] = $dayHeaders;

        // Row 5: Header Row 2 (Dates)
        $dateHeaders = ['Parameter / Point Pengukuran', 'Standar', 'Satuan'];
        foreach ($dates as $date) {
            $dateHeaders[] = Carbon::parse($date)->format('d/m/Y');
        }
        $sheetData[] = $dateHeaders;

        // Build parameter rows and point sub-rows
        foreach ($activeParameters as $activeParam) {
            $param = $activeParam['parameter'];
            $paramStds = $activeParam['standards'];

            // Parameter Header row
            $paramRow = [$param->parameter_name, '-', $param->unit ?: '-'];
            foreach ($dates as $date) {
                $paramRow[] = ''; // Empty spaces under date columns for the parameter row
            }
            $sheetData[] = $paramRow;

            // Measurement point rows
            foreach ($paramStds as $std) {
                $point = $std->point;
                if (!$point) continue;

                // Indent point name for clean visual hierarchy
                $pointRow = ['   ' . $point->point_name, $std->standard_value !== null ? (float)$std->standard_value : '-', $param->unit ?: '-'];

                foreach ($dates as $date) {
                    $val = $lookup[$date][$param->id][$point->id] ?? null;
                    $pointRow[] = ($val !== null) ? (float)$val : '-';
                }
                $sheetData[] = $pointRow;
            }
        }

        return $sheetData;
    }

    /**
     * Resolve the JSON content from either a raw JSON string or a file path.
     */
    private static function resolveJsonContent($config)
    {
        if (!$config) {
            return null;
        }

        // Try decoding as raw JSON first
        json_decode($config, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $config;
        }

        // Clean up quotes from env
        $path = trim($config, "'\"");

        // If it's a file path, try to read it
        if (is_file($path)) {
            return file_get_contents($path);
        }

        if (is_file(base_path($path))) {
            return file_get_contents(base_path($path));
        }

        $storagePath = storage_path(str_replace('storage/', '', $path));
        if (is_file($storagePath)) {
            return file_get_contents($storagePath);
        }

        return $config; // fallback
    }
}
