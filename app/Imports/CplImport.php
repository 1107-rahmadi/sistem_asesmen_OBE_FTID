<?php

namespace App\Imports;

use App\Models\Cpl;
use App\Models\Prodi;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class CplImport
{
    protected $parsedData = [];

    /**
     * Parse Excel/CSV file
     */
    public function parse($filepath, $prodiId)
    {
        $this->parsedData = [];

        $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        if (in_array($extension, ['xlsx', 'xls'])) {
            $rows = $this->parseExcelNative($filepath);
        } else {
            $rows = $this->parseCsv($filepath);
        }

        if (empty($rows)) {
            return [];
        }

        // Find sheet with "CPL Prodi" (case-insensitive)
        $targetSheetRows = $this->findSheetWithPattern($rows, 'cpl prodi');

        if (!$targetSheetRows) {
            return [];
        }

        // Find header row
        $headerRow = 0;
        foreach ($targetSheetRows as $idx => $row) {
            $headerStr = implode(' ', array_map('strtolower', array_filter($row, 'is_string')));
            if (strpos($headerStr, 'kode cpl') !== false || strpos($headerStr, 'kodecpl') !== false) {
                $headerRow = $idx;
                break;
            }
        }

        // Map headers
        $colMapping = $this->mapHeaders($targetSheetRows[$headerRow] ?? []);

        // Process data rows
        $processedData = [];
        for ($i = $headerRow + 1; $i < count($targetSheetRows); $i++) {
            $row = $targetSheetRows[$i];
            $rowNum = $i + 1;

            $kodeCpl = $this->getCellValue($row, $colMapping['kode_cpl']);
            $deskripsi = $this->getCellValue($row, $colMapping['deskripsi']);

            // Skip if kode_cpl empty or invalid
            if (empty(trim($kodeCpl ?? ''))) {
                continue;
            }

            // Check if CPL already exists for this prodi
            $existingCpl = Cpl::where('kode_cpl', trim($kodeCpl))
                ->where('prodi_id', $prodiId)
                ->first();

            $processedData[] = [
                'row_num' => $rowNum,
                'kode_cpl' => trim($kodeCpl),
                'deskripsi' => $deskripsi ?? '',
                'deskripsi_lama' => $existingCpl ? $existingCpl->deskripsi : null,
                'is_new' => !$existingCpl,
                'is_update' => $existingCpl ? true : false,
                'is_valid' => !empty(trim($kodeCpl ?? '')),
            ];
        }

        $this->parsedData = $processedData;
        return $processedData;
    }

    /**
     * Find sheet that contains pattern
     */
    protected function findSheetWithPattern($sheetsData, $pattern)
    {
        // For multi-sheet Excel, check each sheet
        foreach ($sheetsData as $sheetName => $rows) {
            if (is_array($rows) && !empty($rows)) {
                $firstRowStr = implode(' ', array_map('strtolower', array_filter($rows[0] ?? [], 'is_string')));
                if (stripos($firstRowStr, $pattern) !== false) {
                    return $rows;
                }
            }
        }

        // If no multi-sheet, treat first element as the data
        if (is_array($sheetsData) && isset($sheetsData[0])) {
            return $sheetsData[0];
        }

        return null;
    }

    /**
     * Parse Excel using native XML/ZipArchive
     */
    protected function parseExcelNative($filepath)
    {
        $sheets = [];

        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($filepath) === true) {
                $sharedStrings = [];
                if ($zip->locateName('xl/sharedStrings.xml') !== false) {
                    $stringsXml = $zip->getFromName('xl/sharedStrings.xml');
                    $xml = simplexml_load_string($stringsXml);
                    foreach ($xml->si as $si) {
                        $sharedStrings[] = trim((string)$si->t);
                    }
                }

                // Get all sheet names
                $workbookXml = $zip->getFromName('xl/workbook.xml');
                $workbook = simplexml_load_string($workbookXml);
                $sheetNames = [];

                foreach ($workbook->sheets->sheet as $sheet) {
                    $attributes = $sheet->attributes();
                    $name = (string)$attributes['name'];
                    $sheetId = (string)$attributes['{http://schemas.openxmlformats.org/officeDocument/2006/relationships}id'];
                    $sheetNames[$name] = $sheetId;
                }

                // Read each sheet
                foreach ($sheetNames as $sheetName => $sheetId) {
                    $sheetFile = "xl/worksheets/sheet" . substr($sheetId, 3) . ".xml";
                    if ($zip->locateName($sheetFile) !== false) {
                        $sheetXml = $zip->getFromName($sheetFile);
                        $xml = simplexml_load_string($sheetXml);

                        $rows = [];
                        foreach ($xml->sheetData->row as $row) {
                            $rowData = [];
                            foreach ($row->c as $cell) {
                                $cellRef = (string)$cell['r'];
                                preg_match('/[A-Z]+/', $cellRef, $matches);
                                $colIndex = $this->colLetterToIndex($matches[0]);
                                $type = (string)$cell['t'];
                                $value = (string)$cell->v;

                                if ($type === 's' && isset($sharedStrings[(int)$value])) {
                                    $rowData[$colIndex] = $sharedStrings[(int)$value];
                                } else {
                                    $rowData[$colIndex] = $value;
                                }
                            }
                            ksort($rowData);
                            $rows[] = array_values($rowData);
                        }
                        $sheets[$sheetName] = $rows;
                    }
                }

                $zip->close();
            }
        }

        return $sheets;
    }

    /**
     * Parse CSV file
     */
    protected function parseCsv($filepath)
    {
        $rows = [];
        $handle = fopen($filepath, 'r');

        if ($handle) {
            // Try semicolon first (Indonesian format)
            rewind($handle);
            $firstRow = fgetcsv($handle, 0, ';');
            $delimiter = (count($firstRow) >= 2) ? ';' : ',';

            if ($delimiter === ';') {
                $rows[] = $firstRow;
            }

            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        return [0 => $rows]; // Wrap in sheet format
    }

    /**
     * Convert column letter to index
     */
    protected function colLetterToIndex($letter)
    {
        $index = 0;
        for ($i = 0; $i < strlen($letter); $i++) {
            $index = $index * 26 + (ord($letter[$i]) - ord('A') + 1);
        }
        return $index - 1;
    }

    /**
     * Map headers
     */
    protected function mapHeaders($headers)
    {
        $mapping = [
            'kode_cpl' => null,
            'deskripsi' => null,
        ];

        foreach ($headers as $idx => $header) {
            $headerStr = trim((string)($header ?? ''));
            $headerLower = strtolower($headerStr);

            // Kode CPL variations
            if (in_array($headerLower, ['kode cpl', 'kode_cpl', 'kodecpl', 'kode-cpl'])) {
                $mapping['kode_cpl'] = $idx;
            }

            // Deskripsi CPL variations
            if (in_array($headerLower, ['deskripsi cpl', 'deskripsi_cpl', 'deskripsi', 'deskripsi capain pembelajaran', 'cpl'])) {
                $mapping['deskripsi'] = $idx;
            }
        }

        return $mapping;
    }

    /**
     * Get cell value
     */
    protected function getCellValue($row, $col)
    {
        if ($col === null || !isset($row[$col])) {
            return null;
        }
        $value = $row[$col];
        if (is_numeric($value)) {
            return $value;
        }
        return trim((string)($value ?? ''));
    }

    /**
     * Get parsed data
     */
    public function getParsedData()
    {
        return $this->parsedData;
    }

    /**
     * Get preview summary
     */
    public function getPreviewSummary()
    {
        $total = count($this->parsedData);
        $newCount = count(array_filter($this->parsedData, fn($item) => $item['is_new']));
        $updateCount = count(array_filter($this->parsedData, fn($item) => $item['is_update']));
        $skipCount = count(array_filter($this->parsedData, fn($item) => !$item['is_valid']));

        return [
            'total' => $total,
            'new' => $newCount,
            'update' => $updateCount,
            'skip' => $skipCount,
        ];
    }

    /**
     * Process and save to database
     */
    public function processAndSave($prodiId)
    {
        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        foreach ($this->parsedData as $row) {
            if (!$row['is_valid']) {
                $stats['skipped']++;
                continue;
            }

            $existingCpl = Cpl::where('kode_cpl', $row['kode_cpl'])
                ->where('prodi_id', $prodiId)
                ->first();

            if ($existingCpl) {
                $existingCpl->update([
                    'deskripsi' => $row['deskripsi'],
                ]);
                $stats['updated']++;
            } else {
                Cpl::create([
                    'kode_cpl' => $row['kode_cpl'],
                    'deskripsi' => $row['deskripsi'],
                    'ambang_minimum' => 70.00,
                    'prodi_id' => $prodiId,
                ]);
                $stats['created']++;
            }
        }

        return $stats;
    }
}
