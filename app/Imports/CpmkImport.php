<?php

namespace App\Imports;

use App\Models\Cpl;
use App\Models\Cpmk;
use ZipArchive;

class CpmkImport
{
    public $parsedData = [];

    /**
     * Parse Excel/CSV file
     *
     * @param string $filepath Path to uploaded file
     * @param int $prodiId Prodi ID for CPL lookup
     * @return array
     */
    public function parse($filepath, $prodiId)
    {
        $this->parsedData = [];

        $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        if (in_array($extension, ['xlsx', 'xls'])) {
            $rows = $this->parseExcel($filepath);
        } else {
            $rows = $this->parseCsv($filepath);
        }

        if (empty($rows)) {
            return [];
        }

        // Find sheet with "Pemetaan CPL-CPMK-MK" (case-insensitive)
        $target = $this->findSheet($rows, 'pemetaan cpl-cpmk');
        if (!$target) {
            return [];
        }

        // Find header row
        $headerRow = $this->findHeaderRow($target, 'kode cpmk');
        $map = $this->mapHeaders($target[$headerRow] ?? []);

        // Validate required columns
        if ($map['kode_cpl'] === null || $map['kode_cpmk'] === null || $map['deskripsi'] === null) {
            return [];
        }

        $lastCplCode = null;
        $processed = [];

        for ($i = $headerRow + 1; $i < count($target); $i++) {
            $row = $target[$i];
            $rowNum = $i + 1;

            $kodeCplRaw = $this->getCellValue($row, $map['kode_cpl']);
            $kodeCpmk = $this->getCellValue($row, $map['kode_cpmk']);
            $deskripsi = $this->getCellValue($row, $map['deskripsi']);

            // Skip if kode_cpmk is empty
            if (empty(trim($kodeCpmk ?? ''))) {
                continue;
            }

            // Forward-fill untuk kolom CPL saja
            if (!empty(trim($kodeCplRaw ?? ''))) {
                $lastCplCode = trim($kodeCplRaw);
            }

            // Lookup CPL dengan filter prodi_id
            $cpl = null;
            if ($lastCplCode) {
                $cpl = Cpl::where('kode_cpl', $lastCplCode)
                    ->where('prodi_id', $prodiId)
                    ->first();
            }

            $cplFound = $cpl !== null;
            $cpmkExists = Cpmk::where('kode_cpmk', trim($kodeCpmk))->exists();

            $processed[] = [
                'row_num' => $rowNum,
                'kode_cpl' => $lastCplCode,
                'cpl_id' => $cpl ? $cpl->id : null,
                'kode_cpmk' => trim($kodeCpmk),
                'deskripsi' => $deskripsi ?? '',
                'is_new' => $cplFound && !$cpmkExists,
                'is_update' => $cpmkExists,
                'is_skip' => !$cplFound,
                'is_cpl_found' => $cplFound,
            ];
        }

        $this->parsedData = $processed;
        return $processed;
    }

    /**
     * Find sheet containing pattern
     */
    protected function findSheet($sheets, $pattern)
    {
        foreach ($sheets as $name => $rows) {
            if (stripos($name, $pattern) !== false) {
                return $rows;
            }
        }
        // Fallback: return first sheet
        if (is_array($sheets) && isset($sheets[0])) {
            return $sheets[0];
        }
        if (is_array($sheets) && !empty($sheets)) {
            $keys = array_keys($sheets);
            return $sheets[$keys[0]];
        }
        return null;
    }

    /**
     * Find header row containing pattern
     */
    protected function findHeaderRow($rows, $pattern)
    {
        foreach ($rows as $i => $row) {
            $str = implode(' ', array_filter($row, 'is_string'));
            if (stripos($str, $pattern) !== false) {
                return $i;
            }
        }
        return 0;
    }

    /**
     * Map column headers
     */
    protected function mapHeaders($headers)
    {
        $map = [
            'kode_cpl' => null,
            'kode_cpmk' => null,
            'deskripsi' => null,
        ];

        foreach ($headers as $i => $h) {
            if ($h === null) continue;

            $s = strtolower(trim((string)$h));

            // Kolom CPL (bukan "kode cpl", tapi kolom yang mengandung "CPL" saja)
            // Forward-fill diterapkan pada kolom ini
            if ($s === 'cpl' || stripos($s, 'cpl') !== false) {
                if (stripos($s, 'kode') === false) {
                    $map['kode_cpl'] = $i;
                }
            }

            // Kode CPMK
            if (stripos($s, 'kode cpmk') !== false) {
                $map['kode_cpmk'] = $i;
            }

            // Deskripsi CPMK (kolom bernama "CPMK" saja, bukan "Kode CPMK")
            if ($s === 'cpmk') {
                $map['deskripsi'] = $i;
            }
        }

        return $map;
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
     * Parse CSV file (semicolon delimiter for Indonesian format)
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

        return [0 => $rows];
    }

    /**
     * Parse Excel using native XML/ZipArchive
     */
    protected function parseExcel($filepath)
    {
        $sheets = [];

        if (!class_exists('ZipArchive')) {
            return $sheets;
        }

        $zip = new ZipArchive();
        if ($zip->open($filepath) !== true) {
            return $sheets;
        }

        // Load shared strings
        $sharedStrings = [];
        if ($zip->locateName('xl/sharedStrings.xml') !== false) {
            $xml = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
            foreach ($xml->si as $si) {
                $sharedStrings[] = trim((string)$si->t);
            }
        }

        // Get sheet names
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $workbook = simplexml_load_string($workbookXml);
        $sheetList = [];

        foreach ($workbook->sheets->sheet as $sheet) {
            $attributes = $sheet->attributes();
            $name = (string)$attributes['name'];
            $rId = (string)$attributes['{http://schemas.openxmlformats.org/officeDocument/2006/relationships}id'];
            $sheetList[$name] = $rId;
        }

        // Read each sheet
        foreach ($sheetList as $sheetName => $rId) {
            $sheetFile = "xl/worksheets/sheet" . substr($rId, 3) . ".xml";
            if ($zip->locateName($sheetFile) === false) {
                continue;
            }

            $sheetXml = $zip->getFromName($sheetFile);
            $xml = simplexml_load_string($sheetXml);

            $rows = [];
            foreach ($xml->sheetData->row as $excelRow) {
                $rowData = [];
                foreach ($excelRow->c as $cell) {
                    $ref = (string)$cell['r'];
                    preg_match('/[A-Z]+/', $ref, $matches);
                    $col = $this->colIndex($matches[0]);
                    $type = (string)$cell['t'];
                    $val = (string)$cell->v;

                    if ($type === 's' && isset($sharedStrings[(int)$val])) {
                        $rowData[$col] = $sharedStrings[(int)$val];
                    } else {
                        $rowData[$col] = $val;
                    }
                }
                ksort($rowData);
                $rows[] = array_values($rowData);
            }
            $sheets[$sheetName] = $rows;
        }

        $zip->close();
        return $sheets;
    }

    /**
     * Convert column letter to index
     */
    protected function colIndex($letter)
    {
        $n = 0;
        for ($i = 0; $i < strlen($letter); $i++) {
            $n = $n * 26 + ord($letter[$i]) - 64;
        }
        return $n - 1;
    }

    /**
     * Set parsed data (for process from session)
     */
    public function setData($data)
    {
        $this->parsedData = $data;
    }

    /**
     * Get parsed data
     */
    public function getData()
    {
        return $this->parsedData;
    }

    /**
     * Get preview summary
     */
    public function getSummary()
    {
        $total = count($this->parsedData);
        $new = count(array_filter($this->parsedData, fn($i) => $i['is_new'] ?? false));
        $upd = count(array_filter($this->parsedData, fn($i) => $i['is_update'] ?? false));
        $skip = count(array_filter($this->parsedData, fn($i) => $i['is_skip'] ?? false));

        return [
            'total' => $total,
            'new' => $new,
            'update' => $upd,
            'skip' => $skip,
        ];
    }

    /**
     * Process and save to database
     */
    public function save()
    {
        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        foreach ($this->parsedData as $row) {
            // Skip if CPL not found
            if ($row['is_skip'] ?? false) {
                $stats['skipped']++;
                continue;
            }

            $cpmk = Cpmk::where('kode_cpmk', $row['kode_cpmk'])->first();

            if ($cpmk) {
                // Update deskripsi saja (kode_cpmk tidak berubah)
                $cpmk->update([
                    'deskripsi' => $row['deskripsi'],
                    'cpl_id' => $row['cpl_id'], // Update relasi CPL jika berubah
                ]);
                $stats['updated']++;
            } else {
                // Create baru
                Cpmk::create([
                    'cpl_id' => $row['cpl_id'],
                    'kode_cpmk' => $row['kode_cpmk'],
                    'deskripsi' => $row['deskripsi'],
                    'ambang_minimum' => 70,
                ]);
                $stats['created']++;
            }
        }

        return $stats;
    }
}
