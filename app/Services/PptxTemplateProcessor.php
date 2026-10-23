<?php

namespace App\Services;

use ZipArchive;
use Exception;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PptxTemplateProcessor
{
    protected string $templatePath;
    protected string $tempFile;
    protected ?ZipArchive $zip = null;
    protected array $slideContents = [];
    protected array $relsContents = [];
    protected array $modifiedEntries = [];

    /**
     * @param string $templatePath Path to the template .pptx file
     * @throws Exception
     */
    public function __construct(string $templatePath)
    {
        if (!file_exists($templatePath)) {
            // Also check common locations if relative or short name provided
            $candidates = [
                $templatePath,
                public_path($templatePath),
                public_path('assets/templates/pptx/' . basename($templatePath)),
                public_path('assets/pptx/' . basename($templatePath)),
                storage_path('app/templates/pptx/' . basename($templatePath)),
            ];

            $found = null;
            foreach ($candidates as $candidate) {
                if (file_exists($candidate)) {
                    $found = $candidate;
                    break;
                }
            }

            if (!$found) {
                throw new Exception("File template PPTX tidak ditemukan di: {$templatePath}");
            }
            $templatePath = $found;
        }

        $this->templatePath = $templatePath;

        // Copy template to a working temp file so original template remains intact
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $this->tempFile = $tempDir . '/pptx_' . uniqid() . '_' . basename($templatePath);
        copy($this->templatePath, $this->tempFile);

        $this->openZip();
        $this->loadSlides();
    }

    /**
     * Open working zip archive
     */
    protected function openZip(): void
    {
        $this->zip = new ZipArchive();
        $res = $this->zip->open($this->tempFile);
        if ($res !== true) {
            throw new Exception("Gagal membuka file arsip PPTX (Zip error code: {$res})");
        }
    }

    /**
     * Read all slide XML files into memory
     */
    protected function loadSlides(): void
    {
        $this->slideContents = [];

        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $entryName = $this->zip->getNameIndex($i);

            // Match slides, slide masters, slide layouts, or notes
            if (preg_match('#^ppt/slides/slide\d+\.xml$#', $entryName) ||
                preg_match('#^ppt/slideLayouts/slideLayout\d+\.xml$#', $entryName) ||
                preg_match('#^ppt/slideMasters/slideMaster\d+\.xml$#', $entryName)) {
                
                $xml = $this->zip->getFromIndex($i);
                // Pre-normalize broken split tags inside <a:t>
                $this->slideContents[$entryName] = $this->normalizeXmlRuns($xml);
            }
        }
    }

    /**
     * Normalize XML runs where PowerPoint splits {{placeholder}} across multiple <a:t> tags
     */
    protected function normalizeXmlRuns(string $xml): string
    {
        // Match each paragraph <a:p>...</a:p>
        return preg_replace_callback('#<a:p\b[^>]*>(.*?)</a:p>#s', function ($pMatch) {
            $pContent = $pMatch[1];

            // If the paragraph contains both {{ and }}
            if (strpos($pContent, '{{') !== false && strpos($pContent, '}}') !== false) {
                // If {{ is already closed within individual <a:t>, it is fine
                // But if {{ ... }} spans across multiple tags, we clean the internal XML tags between {{ and }}
                $cleaned = preg_replace_callback('#\{\{([^{}]*)\}\}#s', function ($phMatch) {
                    // Strip any XML tags that may have split the placeholder
                    return '{{' . strip_tags($phMatch[1]) . '}}';
                }, $pContent);

                // Re-stitch if <a:t> was closed in the middle
                // e.g. <a:t>{{var</a:t></a:r><a:r><a:t>.field}}</a:t>
                $repaired = preg_replace('#<a:t([^>]*)>([^<]*?)\{\{([^}]*?)</a:t>.*?<a:t[^>]*>([^<]*?)\}\}([^<]*?)</a:t>#s', '<a:t$1>$2{{$3$4}}$5</a:t>', $cleaned);

                return str_replace($pContent, $repaired, $pMatch[0]);
            }

            return $pMatch[0];
        }, $xml);
    }

    /**
     * Get list of all placeholders found in the presentation
     * @return array
     */
    public function getPlaceholders(): array
    {
        $placeholders = [];
        foreach ($this->slideContents as $content) {
            preg_match_all('#\{\{([^{}]+)\}\}#', $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $ph) {
                    $trimmed = trim(strip_tags($ph));
                    if (!in_array($trimmed, $placeholders)) {
                        $placeholders[] = $trimmed;
                    }
                }
            }
        }
        return $placeholders;
    }

    /**
     * Replace a single placeholder with a given value
     *
     * @param string $search e.g. 'compressor.rh1' or '{{compressor.rh1}}'
     * @param mixed $replace
     * @return self
     */
    public function setValue(string $search, mixed $replace): self
    {
        $placeholder = str_starts_with($search, '{{') && str_ends_with($search, '}}')
            ? $search
            : '{{' . $search . '}}';

        // Escape for XML safely (protect &, <, >)
        $replaceStr = htmlspecialchars((string)($replace ?? ''), ENT_XML1, 'UTF-8');

        foreach ($this->slideContents as $entryName => &$content) {
            if (strpos($content, $placeholder) !== false) {
                $content = str_replace($placeholder, $replaceStr, $content);
                $this->modifiedEntries[$entryName] = true;
            }
        }

        return $this;
    }

    /**
     * Replace multiple placeholders in batch
     *
     * @param array $values Associative array of ['key' => 'value']
     * @return self
     */
    public function setValues(array $values): self
    {
        foreach ($values as $key => $value) {
            $this->setValue((string)$key, $value);
        }
        return $this;
    }

    /**
     * Replace an image in the PPTX archive
     *
     * @param string $imageEntryName e.g. 'ppt/media/image1.png' or base filename 'image1.png'
     * @param string $sourceImagePathOrDataUri Local file path or base64 data URI (data:image/png;base64,...)
     * @return self
     */
    public function replaceImage(string $imageEntryName, string $sourceImagePathOrDataUri): self
    {
        $targetEntry = str_starts_with($imageEntryName, 'ppt/media/')
            ? $imageEntryName
            : 'ppt/media/' . $imageEntryName;

        $imageData = null;
        if (str_starts_with($sourceImagePathOrDataUri, 'data:image')) {
            // Base64 Data URI
            $parts = explode(',', $sourceImagePathOrDataUri);
            if (count($parts) === 2) {
                $imageData = base64_decode($parts[1]);
            }
        } elseif (file_exists($sourceImagePathOrDataUri)) {
            $imageData = file_get_contents($sourceImagePathOrDataUri);
        }

        if ($imageData !== null) {
            $this->zip->deleteName($targetEntry);
            $this->zip->addFromString($targetEntry, $imageData);
        }

        return $this;
    }

    /**
     * Replace a placeholder shape (or text box) with a chart image preserving exact dimensions & position.
     * Alternatively, if placeholder is in an image's Alt Text, replaces that image.
     *
     * @param string $placeholder e.g. 'chart.compressor' or '{{chart.compressor}}'
     * @param string $sourceImagePathOrDataUri Base64 data URI (data:image/png;base64,...) or file path
     * @return self
     */
    public function setChartImage(string $placeholder, string $sourceImagePathOrDataUri): self
    {
        $cleanKey = trim($placeholder, '{} ');
        $needle = '{{' . $cleanKey . '}}';

        // Extract binary image data
        $imageData = null;
        if (str_starts_with($sourceImagePathOrDataUri, 'data:image')) {
            $parts = explode(',', $sourceImagePathOrDataUri);
            if (count($parts) === 2) {
                $imageData = base64_decode($parts[1]);
            }
        } elseif (file_exists($sourceImagePathOrDataUri)) {
            $imageData = file_get_contents($sourceImagePathOrDataUri);
        } elseif (strlen($sourceImagePathOrDataUri) > 8 && substr($sourceImagePathOrDataUri, 1, 3) === 'PNG') {
            $imageData = $sourceImagePathOrDataUri;
        }

        if (!$imageData) {
            return $this;
        }

        // Generate unique image filename in media
        $imageFileName = 'chart_' . substr(md5($cleanKey . uniqid('', true)), 0, 12) . '.png';
        $imageEntryName = 'ppt/media/' . $imageFileName;
        $this->zip->addFromString($imageEntryName, $imageData);

        // Search through slides
        foreach ($this->slideContents as $slideEntry => &$slideXml) {
            if (strpos($slideXml, $needle) === false && strpos($slideXml, $cleanKey) === false) {
                continue;
            }

            // Extract slide number e.g. slide2
            if (!preg_match('#ppt/slides/slide(\d+)\.xml#', $slideEntry, $sm)) {
                continue;
            }
            $slideNum = $sm[1];
            $relsEntry = "ppt/slides/_rels/slide{$slideNum}.xml.rels";

            // Load rels
            if (!isset($this->relsContents[$relsEntry])) {
                $this->relsContents[$relsEntry] = $this->zip->getFromName($relsEntry) ?: '';
            }
            $relsXml = &$this->relsContents[$relsEntry];

            // Calculate next rId
            preg_match_all('/Id="rId(\d+)"/', $relsXml, $rMatches);
            $maxRId = !empty($rMatches[1]) ? max(array_map('intval', $rMatches[1])) : 10;
            $newRId = 'rId' . ($maxRId + 1);

            // Add relationship to rels
            $relTag = '<Relationship Id="' . $newRId . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/' . $imageFileName . '"/>';
            $relsXml = str_replace('</Relationships>', $relTag . '</Relationships>', $relsXml);
            $this->modifiedEntries[$relsEntry] = true;

            // 1. Check if placeholder is inside a Shape <p:sp>
            $spPattern = '#<p:sp\b.*?</p:sp>#s';
            if (preg_match_all($spPattern, $slideXml, $spMatches)) {
                foreach ($spMatches[0] as $sp) {
                    if (strpos($sp, $needle) !== false || strpos($sp, $cleanKey) !== false) {
                        // Extract <a:xfrm>
                        preg_match('#<a:xfrm\b[^>]*>.*?</a:xfrm>#s', $sp, $xfrmMatch);
                        $xfrmXml = $xfrmMatch[0] ?? '';

                        // Extract ID
                        preg_match('#<p:cNvPr\b[^>]*id="(\d+)"#', $sp, $idMatch);
                        $id = $idMatch[1] ?? rand(100, 9999);

                        $picXml = '<p:pic>'
                            . '<p:nvPicPr>'
                            . '<p:cNvPr id="' . $id . '" name="Chart_' . $cleanKey . '"/>'
                            . '<p:cNvPicPr><a:picLocks noChangeAspect="0"/></p:cNvPicPr>'
                            . '<p:nvPr/>'
                            . '</p:nvPicPr>'
                            . '<p:blipFill>'
                            . '<a:blip r:embed="' . $newRId . '"/>'
                            . '<a:stretch><a:fillRect/></a:stretch>'
                            . '</p:blipFill>'
                            . '<p:spPr>'
                            . $xfrmXml
                            . '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>'
                            . '</p:spPr>'
                            . '</p:pic>';

                        $slideXml = str_replace($sp, $picXml, $slideXml);
                        $this->modifiedEntries[$slideEntry] = true;
                        break;
                    }
                }
            }

            // 2. Check if placeholder is inside an existing Picture <p:pic> (e.g. in alt text / name)
            $picPattern = '#<p:pic\b.*?</p:pic>#s';
            if (preg_match_all($picPattern, $slideXml, $picMatches)) {
                foreach ($picMatches[0] as $pic) {
                    if (strpos($pic, $needle) !== false || strpos($pic, $cleanKey) !== false) {
                        // Replace embed rId
                        $updatedPic = preg_replace('/<a:blip\b[^>]*r:embed="[^"]*"/', '<a:blip r:embed="' . $newRId . '"', $pic);
                        $slideXml = str_replace($pic, $updatedPic, $slideXml);
                        $this->modifiedEntries[$slideEntry] = true;
                        break;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Batch set chart images
     *
     * @param array $chartImages Associative array of ['chart.name' => 'data:image/png;base64,...' | 'path/to/file.png']
     * @return self
     */
    public function setChartImages(array $chartImages): self
    {
        foreach ($chartImages as $placeholder => $image) {
            if (!empty($image)) {
                $this->setChartImage((string)$placeholder, (string)$image);
            }
        }
        return $this;
    }

    /**
     * Commit all XML changes to the zip archive
     */
    public function save(): string
    {
        foreach ($this->modifiedEntries as $entryName => $isModified) {
            if ($isModified) {
                if (isset($this->slideContents[$entryName])) {
                    $this->zip->deleteName($entryName);
                    $this->zip->addFromString($entryName, $this->slideContents[$entryName]);
                } elseif (isset($this->relsContents[$entryName])) {
                    $this->zip->deleteName($entryName);
                    $this->zip->addFromString($entryName, $this->relsContents[$entryName]);
                }
            }
        }

        $this->modifiedEntries = [];
        $this->zip->close();
        $this->zip = null;

        return $this->tempFile;
    }

    /**
     * Save the processed presentation to a specific destination path
     *
     * @param string $destinationPath
     * @return string
     */
    public function saveAs(string $destinationPath): string
    {
        $savedTemp = $this->save();
        $dir = dirname($destinationPath);
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        copy($savedTemp, $destinationPath);
        return $destinationPath;
    }

    /**
     * Create a Laravel download response and automatically clean up the temp file
     *
     * @param string $filename
     * @return BinaryFileResponse
     */
    public function download(string $filename = 'presentation.pptx'): BinaryFileResponse
    {
        $savedTemp = $this->save();

        return response()->download($savedTemp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Destructor to ensure open zip is closed
     */
    public function __destruct()
    {
        if ($this->zip instanceof ZipArchive) {
            try {
                $this->zip->close();
            } catch (Exception $e) {
                // Silently ignore if already closed
            }
        }
    }
}
