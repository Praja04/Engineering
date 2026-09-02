<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;

class PdfSignerService
{
    private function resolveImageTempFile(?string $sigData): ?string
    {
        if (empty($sigData)) return null;

        $bytes = null;
        if (str_starts_with($sigData, 'data:image/') || str_contains($sigData, ',')) {
            $parts = explode(',', $sigData, 2);
            $bytes = base64_decode($parts[1] ?? $parts[0]);
        } elseif (strlen($sigData) > 200 && !str_starts_with($sigData, '/') && !str_starts_with($sigData, 'http')) {
            $bytes = base64_decode($sigData);
        } else {
            $cleanPath = ltrim(explode('?', $sigData)[0], '/');
            $possiblePaths = [
                $sigData,
                base_path($cleanPath),
                storage_path('app/public/' . str_replace('storage/', '', $cleanPath)),
                public_path($cleanPath),
                storage_path('app/public/uploads/signatures/' . basename($cleanPath)),
            ];

            foreach ($possiblePaths as $p) {
                if (file_exists($p) && is_file($p)) {
                    $bytes = file_get_contents($p);
                    break;
                }
            }
        }

        if (!$bytes) return null;

        $tmpFile = tempnam(sys_get_temp_dir(), 'sig_img_') . '.png';
        file_put_contents($tmpFile, $bytes);
        return $tmpFile;
    }

    private function fitTextFont(Fpdi $pdf, string $name, float $boxW, float $maxFs = 5.7, float $minFs = 4.8): array
    {
        $name = trim($name);
        if (empty($name)) return ['', $maxFs];

        $parts = explode(' ', $name);
        $displayName = $name;

        if (count($parts) == 2) {
            $abbr = $parts[0] . ' ' . substr($parts[1], 0, 1) . '.';
            $pdf->SetFont('Helvetica', '', $maxFs);
            if ($pdf->GetStringWidth($abbr) <= ($boxW - 2)) {
                $displayName = $abbr;
            }
        } elseif (count($parts) >= 3) {
            $abbr = $parts[0] . ' ';
            for ($i = 1; $i < count($parts); $i++) {
                $abbr .= substr($parts[$i], 0, 1) . '. ';
            }
            $abbr = trim($abbr);
            $pdf->SetFont('Helvetica', '', $maxFs);
            if ($pdf->GetStringWidth($abbr) <= ($boxW - 2)) {
                $displayName = $abbr;
            }
        }

        $pdf->SetFont('Helvetica', '', $maxFs);
        $w = $pdf->GetStringWidth($displayName);
        $fs = $maxFs;
        if ($w > ($boxW - 2) && $w > 0) {
            $fs = max($minFs, min($maxFs, $maxFs * (($boxW - 2) / $w)));
        }

        return [$displayName, $fs];
    }

    private function getPythonBinary(): string
    {
        $custom = env('PYTHON_PATH') ?: env('PYTHON_BINARY');
        if (!empty($custom)) {
            return $custom;
        }

        $candidates = [
            '/opt/pymupdf-env/bin/python',
            '/opt/pymupdf-env/bin/python3',
            base_path('pymupdf-env/bin/python'),
            base_path('venv/bin/python'),
            base_path('env/bin/python'),
            base_path('venv/Scripts/python.exe'),
            '/usr/bin/python3',
            '/usr/local/bin/python3',
            '/usr/bin/python',
        ];

        foreach ($candidates as $bin) {
            if (file_exists($bin) && is_file($bin)) {
                return $bin;
            }
        }

        return 'python';
    }

    /**
     * Stamping signatures on Technical Drawing PDF using PyMuPDF (with FPDI fallback).
     */
    public function signDrawing(string $pdfPath, array $approvals, string $category = 'Sipil', string $orientation = 'landscape'): bool
    {
        if (! file_exists($pdfPath)) {
            return false;
        }

        // 1. Try Python PyMuPDF script (supports compressed PDF 1.5+ streams & object streams)
        $scriptPath = base_path('scripts/pdf_signer.py');
        if (File::exists($scriptPath)) {
            $tmpJson = tempnam(sys_get_temp_dir(), 'sig_app_') . '.json';
            File::put($tmpJson, json_encode($approvals));

            $pyBin = $this->getPythonBinary();
            $cmd = sprintf(
                '%s %s drawing %s %s %s %s 2>&1',
                escapeshellcmd($pyBin),
                escapeshellarg($scriptPath),
                escapeshellarg($pdfPath),
                escapeshellarg($tmpJson),
                escapeshellarg($category),
                escapeshellarg($orientation)
            );

            exec($cmd, $output, $returnCode);
            @unlink($tmpJson);

            if ($returnCode === 0) {
                return true;
            }
            Log::warning("signDrawing python signer warning (code $returnCode): " . implode("\n", $output));
        }

        try {
            $catLower = strtolower($category);
            $orientLower = strtolower($orientation);
            $isSipil = str_contains($catLower, 'sipil');
            $isPortrait = str_contains($orientLower, 'portrait') || str_contains($orientLower, 'potrait');

            if ($isSipil) {
                $mapKey = $isPortrait ? 'diki_portrait' : 'diki_landscape';
            } else {
                $mapKey = $isPortrait ? 'rifan_portrait' : 'rifan_landscape';
            }

            $coordMaps = [
                'diki_landscape' => [
                    'drafter'         => ['img' => [802.0, 628.0, 52.0, 65.0], 'text' => [800.0, 695.0, 56.0, 13.0]],
                    'foreman'         => ['img' => [858.0, 628.0, 52.0, 65.0], 'text' => [856.0, 695.0, 56.0, 13.0]],
                    'requester'       => ['img' => [915.0, 628.0, 52.0, 65.0], 'text' => [913.0, 695.0, 56.0, 13.0]],
                    'staff_user'      => ['img' => [915.0, 628.0, 52.0, 65.0], 'text' => [913.0, 695.0, 56.0, 13.0]],
                    'staff_epr'       => ['img' => [915.0, 628.0, 52.0, 65.0], 'text' => [913.0, 695.0, 56.0, 13.0]],
                    'dept'            => ['img' => [971.0, 628.0, 52.0, 65.0], 'text' => [969.0, 695.0, 56.0, 13.0]],
                    'dept_approval'   => ['img' => [971.0, 628.0, 52.0, 65.0], 'text' => [969.0, 695.0, 56.0, 13.0]],
                    'spv_user'        => ['img' => [971.0, 628.0, 52.0, 65.0], 'text' => [969.0, 695.0, 56.0, 13.0]],
                    'spv_dept'        => ['img' => [971.0, 628.0, 52.0, 65.0], 'text' => [969.0, 695.0, 56.0, 13.0]],
                    'supervisor_user' => ['img' => [971.0, 628.0, 52.0, 65.0], 'text' => [969.0, 695.0, 56.0, 13.0]],
                    'supervisor'      => ['img' => [1028.0, 628.0, 52.0, 65.0], 'text' => [1026.0, 695.0, 56.0, 13.0]],
                    'spv_eng'         => ['img' => [1028.0, 628.0, 52.0, 65.0], 'text' => [1026.0, 695.0, 56.0, 13.0]],
                    'supervisor_eng'  => ['img' => [1028.0, 628.0, 52.0, 65.0], 'text' => [1026.0, 695.0, 56.0, 13.0]],
                    'manager'         => ['img' => [1083.0, 628.0, 52.0, 65.0], 'text' => [1082.0, 695.0, 55.0, 13.0]],
                    'manager_eng'     => ['img' => [1083.0, 628.0, 52.0, 65.0], 'text' => [1082.0, 695.0, 55.0, 13.0]],
                    'engineer'        => ['img' => [1083.0, 628.0, 52.0, 65.0], 'text' => [1082.0, 695.0, 55.0, 13.0]],
                    'factory_manager' => ['img' => [1028.0, 526.0, 107.0, 66.0], 'text' => [1026.0, 594.0, 111.0, 13.0]],
                ],
                'diki_portrait' => [
                    'drafter'         => ['img' => [25.0, 1057.0, 95.0, 91.0], 'text' => [22.0, 1152.0, 100.0, 13.0]],
                    'foreman'         => ['img' => [124.0, 1057.0, 95.0, 91.0], 'text' => [122.0, 1152.0, 98.0, 13.0]],
                    'requester'       => ['img' => [224.0, 1057.0, 95.0, 91.0], 'text' => [222.0, 1152.0, 98.0, 13.0]],
                    'staff_user'      => ['img' => [224.0, 1057.0, 95.0, 91.0], 'text' => [222.0, 1152.0, 98.0, 13.0]],
                    'staff_epr'       => ['img' => [224.0, 1057.0, 95.0, 91.0], 'text' => [222.0, 1152.0, 98.0, 13.0]],
                    'dept'            => ['img' => [323.0, 1057.0, 95.0, 91.0], 'text' => [321.0, 1152.0, 99.0, 13.0]],
                    'dept_approval'   => ['img' => [323.0, 1057.0, 95.0, 91.0], 'text' => [321.0, 1152.0, 99.0, 13.0]],
                    'spv_user'        => ['img' => [323.0, 1057.0, 95.0, 91.0], 'text' => [321.0, 1152.0, 99.0, 13.0]],
                    'spv_dept'        => ['img' => [323.0, 1057.0, 95.0, 91.0], 'text' => [321.0, 1152.0, 99.0, 13.0]],
                    'supervisor_user' => ['img' => [323.0, 1057.0, 95.0, 91.0], 'text' => [321.0, 1152.0, 99.0, 13.0]],
                    'supervisor'      => ['img' => [423.0, 1057.0, 95.0, 91.0], 'text' => [421.0, 1152.0, 99.0, 13.0]],
                    'spv_eng'         => ['img' => [423.0, 1057.0, 95.0, 91.0], 'text' => [421.0, 1152.0, 99.0, 13.0]],
                    'supervisor_eng'  => ['img' => [423.0, 1057.0, 95.0, 91.0], 'text' => [421.0, 1152.0, 99.0, 13.0]],
                    'manager'         => ['img' => [522.0, 1057.0, 96.0, 91.0], 'text' => [520.0, 1152.0, 99.0, 13.0]],
                    'manager_eng'     => ['img' => [522.0, 1057.0, 96.0, 91.0], 'text' => [520.0, 1152.0, 99.0, 13.0]],
                    'engineer'        => ['img' => [522.0, 1057.0, 96.0, 91.0], 'text' => [520.0, 1152.0, 99.0, 13.0]],
                    'factory_manager' => ['img' => [622.0, 1057.0, 195.0, 91.0], 'text' => [620.0, 1152.0, 199.0, 13.0]],
                ],
                'rifan_landscape' => [
                    'drafter'         => ['img' => [755.0, 517.0, 31.0, 31.0], 'text' => [753.5, 549.0, 33.0, 11.0]],
                    'foreman'         => ['img' => [789.0, 517.0, 31.0, 31.0], 'text' => [787.5, 549.0, 33.0, 11.0]],
                    'requester'       => ['img' => [685.0, 517.0, 32.0, 31.0], 'text' => [684.5, 549.0, 33.0, 11.0]],
                    'staff_user'      => ['img' => [685.0, 517.0, 32.0, 31.0], 'text' => [684.5, 549.0, 33.0, 11.0]],
                    'staff_epr'       => ['img' => [685.0, 517.0, 32.0, 31.0], 'text' => [684.5, 549.0, 33.0, 11.0]],
                    'dept'            => ['img' => [719.0, 517.0, 32.0, 31.0], 'text' => [718.5, 549.0, 33.0, 11.0]],
                    'dept_approval'   => ['img' => [719.0, 517.0, 32.0, 31.0], 'text' => [718.5, 549.0, 33.0, 11.0]],
                    'spv_user'        => ['img' => [719.0, 517.0, 32.0, 31.0], 'text' => [718.5, 549.0, 33.0, 11.0]],
                    'spv_dept'        => ['img' => [719.0, 517.0, 32.0, 31.0], 'text' => [718.5, 549.0, 33.0, 11.0]],
                    'supervisor_user' => ['img' => [719.0, 517.0, 32.0, 31.0], 'text' => [718.5, 549.0, 33.0, 11.0]],
                    'supervisor'      => ['img' => [624.0, 517.0, 52.0, 31.0], 'text' => [616.0, 549.0, 67.0, 11.0]],
                    'spv_eng'         => ['img' => [624.0, 517.0, 52.0, 31.0], 'text' => [616.0, 549.0, 67.0, 11.0]],
                    'supervisor_eng'  => ['img' => [624.0, 517.0, 52.0, 31.0], 'text' => [616.0, 549.0, 67.0, 11.0]],
                    'manager'         => ['img' => [555.0, 517.0, 52.0, 31.0], 'text' => [547.0, 549.0, 67.0, 11.0]],
                    'manager_eng'     => ['img' => [555.0, 517.0, 52.0, 31.0], 'text' => [547.0, 549.0, 67.0, 11.0]],
                    'engineer'        => ['img' => [555.0, 517.0, 52.0, 31.0], 'text' => [547.0, 549.0, 67.0, 11.0]],
                    'factory_manager' => ['img' => [555.0, 517.0, 52.0, 31.0], 'text' => [547.0, 549.0, 67.0, 11.0]],
                ],
                'rifan_portrait' => [
                    'drafter'         => ['img' => [518.0, 673.5, 26.3, 33.0], 'text' => [517.0, 707.0, 28.3, 10.5]],
                    'foreman'         => ['img' => [547.1, 673.5, 26.3, 33.0], 'text' => [546.1, 707.0, 28.3, 10.5]],
                    'requester'       => ['img' => [459.8, 673.5, 26.3, 33.0], 'text' => [458.8, 707.0, 28.3, 10.5]],
                    'staff_user'      => ['img' => [459.8, 673.5, 26.3, 33.0], 'text' => [458.8, 707.0, 28.3, 10.5]],
                    'staff_epr'       => ['img' => [459.8, 673.5, 26.3, 33.0], 'text' => [458.8, 707.0, 28.3, 10.5]],
                    'dept'            => ['img' => [488.9, 673.5, 26.3, 33.0], 'text' => [487.9, 707.0, 28.3, 10.5]],
                    'dept_approval'   => ['img' => [488.9, 673.5, 26.3, 33.0], 'text' => [487.9, 707.0, 28.3, 10.5]],
                    'spv_user'        => ['img' => [488.9, 673.5, 26.3, 33.0], 'text' => [487.9, 707.0, 28.3, 10.5]],
                    'spv_dept'        => ['img' => [488.9, 673.5, 26.3, 33.0], 'text' => [487.9, 707.0, 28.3, 10.5]],
                    'supervisor_user' => ['img' => [488.9, 673.5, 26.3, 33.0], 'text' => [487.9, 707.0, 28.3, 10.5]],
                    'supervisor'      => ['img' => [402.0, 673.5, 54.5, 33.0], 'text' => [401.0, 707.0, 56.5, 10.5]],
                    'spv_eng'         => ['img' => [402.0, 673.5, 54.5, 33.0], 'text' => [401.0, 707.0, 56.5, 10.5]],
                    'supervisor_eng'  => ['img' => [402.0, 673.5, 54.5, 33.0], 'text' => [401.0, 707.0, 56.5, 10.5]],
                    'manager'         => ['img' => [330.5, 673.5, 67.5, 33.0], 'text' => [329.5, 707.0, 69.5, 10.5]],
                    'manager_eng'     => ['img' => [330.5, 673.5, 67.5, 33.0], 'text' => [329.5, 707.0, 69.5, 10.5]],
                    'engineer'        => ['img' => [330.5, 673.5, 67.5, 33.0], 'text' => [329.5, 707.0, 69.5, 10.5]],
                    'factory_manager' => ['img' => [330.5, 673.5, 67.5, 33.0], 'text' => [329.5, 707.0, 69.5, 10.5]],
                ],
            ];

            $selectedMap = $coordMaps[$mapKey] ?? $coordMaps['diki_landscape'];

            $pdf = new Fpdi('P', 'pt');
            $pageCount = $pdf->setSourceFile($pdfPath);

            $tempFilesCreated = [];

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $orient = $size['width'] > $size['height'] ? 'L' : 'P';

                $pdf->AddPage($orient, [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                if ($pageNo === 1 && !empty($approvals)) {
                    foreach ($approvals as $roleKey => $appInfo) {
                        if (str_ends_with($roleKey, '_reject') || !is_array($appInfo)) continue;

                        $sigInput = $appInfo['signature'] ?? null;
                        $signerName = $appInfo['signer'] ?? ($appInfo['username'] ?? '');

                        $target = $selectedMap[$roleKey] ?? null;
                        if (!$target) continue;

                        $imgRect = $target['img'] ?? null;
                        $textRect = $target['text'] ?? null;

                        if ($imgRect && $sigInput) {
                            $tmpImg = $this->resolveImageTempFile($sigInput);
                            if ($tmpImg) {
                                $tempFilesCreated[] = $tmpImg;
                                list($ix, $iy, $iw, $ih) = $imgRect;
                                $pdf->Image($tmpImg, $ix, $iy, $iw, $ih);
                            }
                        }

                        if ($textRect && $signerName) {
                            list($tx, $ty, $tw, $th) = $textRect;
                            list($displayName, $calcFs) = $this->fitTextFont($pdf, $signerName, $tw, 5.7, 4.8);

                            $pdf->SetFont('Helvetica', '', $calcFs);
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->SetXY($tx, $ty);
                            $pdf->Cell($tw, $th, $displayName, 0, 0, 'C');
                        }
                    }
                }
            }

            $tempPdfPath = $pdfPath . '.tmp.pdf';
            $pdf->Output('F', $tempPdfPath);

            foreach ($tempFilesCreated as $tf) {
                @unlink($tf);
            }

            if (file_exists($tempPdfPath)) {
                @unlink($pdfPath);
                rename($tempPdfPath, $pdfPath);
                return true;
            }

            return false;
        } catch (\Throwable $e) {
            Log::error('signDrawing FPDI failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Stamping signatures on Project Handover (Berita Acara) PDF using 100% Native PHP FPDI.
     */
    public function signHandover(string $pdfPath, array $approvals): bool
    {
        if (! file_exists($pdfPath)) {
            return false;
        }

        // 1. Try Python PyMuPDF script
        $scriptPath = base_path('scripts/pdf_signer.py');
        if (File::exists($scriptPath)) {
            $tmpJson = tempnam(sys_get_temp_dir(), 'sig_ho_') . '.json';
            File::put($tmpJson, json_encode($approvals));

            $pyBin = $this->getPythonBinary();
            $cmd = sprintf(
                '%s %s handover %s %s 2>&1',
                escapeshellcmd($pyBin),
                escapeshellarg($scriptPath),
                escapeshellarg($pdfPath),
                escapeshellarg($tmpJson)
            );

            exec($cmd, $output, $returnCode);
            @unlink($tmpJson);

            if ($returnCode === 0) {
                return true;
            }
            Log::warning("signHandover python signer warning (code $returnCode): " . implode("\n", $output));
        }

        try {
            $roleCols = [
                'staff_eng'   => ['x' => [55, 132],  'header' => 'Dibuat oleh,',  'role' => 'Staff ENG'],
                'spv_eng'     => ['x' => [135, 212], 'header' => 'Diketahui oleh,', 'role' => 'SPV ENG'],
                'manager_eng' => ['x' => [215, 292], 'header' => 'Disetujui oleh,', 'role' => 'Manager ENG'],
                'manager_user'=> ['x' => [295, 372], 'header' => 'Disetujui oleh,', 'role' => 'Manager User'],
                'spv_user'    => ['x' => [375, 452], 'header' => 'Diketahui oleh,', 'role' => 'SPV User'],
                'staff_user'  => ['x' => [455, 532], 'header' => 'Diterima oleh,',  'role' => 'Staff User'],
            ];

            $pdf = new Fpdi('P', 'pt');
            $pageCount = $pdf->setSourceFile($pdfPath);
            $tempFilesCreated = [];

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $orient = $size['width'] > $size['height'] ? 'L' : 'P';

                $pdf->AddPage($orient, [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Stamp on last page or page 1
                if ($pageNo === $pageCount) {
                    $pdf->SetFillColor(255, 255, 255);
                    $pdf->Rect(50, 345, 495, 110, 'F');

                    foreach ($roleCols as $roleKey => $colInfo) {
                        list($x0, $x1) = $colInfo['x'];
                        $w = $x1 - $x0;

                        $pdf->SetFont('Helvetica', '', 8);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetXY($x0, 350);
                        $pdf->Cell($w, 12, $colInfo['header'], 0, 0, 'C');

                        $pdf->SetXY($x0, 435);
                        $pdf->Cell($w, 12, $colInfo['role'], 0, 0, 'C');

                        $appInfo = $approvals[$roleKey] ?? null;
                        if (!$appInfo || !is_array($appInfo)) continue;

                        $sigInput = $appInfo['signature'] ?? null;
                        $signerName = $appInfo['signer'] ?? ($appInfo['username'] ?? '');

                        if ($sigInput) {
                            $tmpImg = $this->resolveImageTempFile($sigInput);
                            if ($tmpImg) {
                                $tempFilesCreated[] = $tmpImg;
                                $pdf->Image($tmpImg, $x0, 362, $w, 52);
                            }
                        }

                        if ($signerName) {
                            list($displayName, $calcFs) = $this->fitTextFont($pdf, $signerName, $w, 7.5, 5.0);
                            $pdf->SetFont('Helvetica', '', $calcFs);
                            $pdf->SetXY($x0, 420);
                            $pdf->Cell($w, 12, $displayName, 0, 0, 'C');
                        }
                    }
                }
            }

            $tempPdfPath = $pdfPath . '.tmp.pdf';
            $pdf->Output('F', $tempPdfPath);

            foreach ($tempFilesCreated as $tf) {
                @unlink($tf);
            }

            if (file_exists($tempPdfPath)) {
                @unlink($pdfPath);
                rename($tempPdfPath, $pdfPath);
                return true;
            }

            return false;
        } catch (\Throwable $e) {
            Log::error('signHandover FPDI failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return false;
        }
    }
}
