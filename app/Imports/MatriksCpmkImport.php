<?php

namespace App\Imports;

use App\Models\Cpmk;
use App\Models\MatriksCpmk;
use ZipArchive;

class MatriksCpmkImport
{
    public $parsedData = [];

    /**
     * Parse Excel/CSV file
     *
     * @param string $filepath Path to uploaded file
     * @return array
     */
    public function parse($filepath)
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

        // Get first sheet
        $target = $this->getFirstSheet($rows);

        // Find header row
        $headerRow = $this->findHeaderRow($target, 'kode mk');
        $map = $this->mapHeaders($target[$headerRow] ?? []);

        // Validate required columns
        if ($map['kode_mk'] === null || $map['kode_cpmk'] === null || $map['skor'] === null) {
            return [];
        }

        $processed = [];

        for ($i = $headerRow + 1; $i < count($target); $i++) {
            $row = $target[$i];
            $rowNum = $i + 1;

            $kodeMk = $this->getCellValue($row, $map['kode_mk']);
            $namaMk = $this->getCellValue($row, $map['nama_mk']);
            $kodeCpmk = $this->getCellValue($row, $map['kode_cpmk']);
            $skor = $this->getCellValue($row, $map['skor']);
            $bobotPart = $this->getCellValue($row, $map['bobot_part']);
            $bobotTugas = $this->getCellValue($row, $map['bobot_tugas']);
            $bobotUts = $this->getCellValue($row, $map['bobot_uts']);
            $bobotUas = $this->getCellValue($row, $map['bobot_uas']);

            // Skip if kode_mk or kode_cpmk is empty
            if (empty(trim($kodeMk ?? '')) || empty(trim($kodeCpmk ?? ''))) {
                continue;
            }

            $kodeMk = trim($kodeMk);
            $kodeCpmk = trim($kodeCpmk);

            // Lookup CPMK by kode_cpmk
            $cpmk = Cpmk::where('kode_cpmk', $kodeCpmk)->first();

            // Check if matriks already exists (by kode_mk + cpmk_id)
            $existingMatriks = null;
            if ($cpmk) {
                $existingMatriks = MatriksCpmk::where('kode_mk', $kodeMk)
                    ->where('cpmk_id', $cpmk->id)
                    ->first();
            }

            $processed[] = [
                'row_num' => $rowNum,
                'kode_cpl' => $this->getCellValue($row, $map['cpl']) ?? '',
                'kode_mk' => $kodeMk,
                'nama_mk' => $namaMk ?? '',
                'kode_cpmk' => $kodeCpmk,
                'skor' => $skor ?? 0,
                'bobot_part' => $bobotPart ?? 0,
                'bobot_tugas' => $bobotTugas ?? 0,
                'bobot_uts' => $bobotUts ?? 0,
                'bobot_uas' => $bobotUas ?? 0,
                'cpmk_id' => $cpmk ? $cpmk->id : null,
                'cpmk_cpl_kode' => $cpmk && $cpmk->cpl ? $cpmk->cpl->kode_cpl : null,
                'is_new' => $cpmk !== null && $existingMatriks === null,
                'is_update' => $existingMatriks !== null,
                'is_skip' => $cpmk === null,
                'is_cpmk_found' => $cpmk !== null,
            ];
        }

        $this->parsedData = $processed;
        return $processed;
    }

    /**
     * Get first sheet from sheets data
     */
    protected function getFirstSheet($sheets)
    {
        if (is_array($sheets) && isset($sheets[0])) {
            return $sheets[0];
        }
        if (is_array($sheets) && !empty($sheets)) {
            $keys = array_keys($sheets);
            return $sheets[$keys[0]];
        }
        return [];
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
            'cpl' => null,
            'kode_mk' => null,
            'nama_mk' => null,
            'kode_cpmk' => null,
            'skor' => null,
            'bobot_part' => null,
            'bobot_tugas' => null,
            'bobot_uts' => null,
            'bobot_uas' => null,
        ];

        foreach ($headers as $i => $h) {
            if ($h === null) continue;

            $s = strtolower(trim((string)$h));

            // CPL (konteks info)
            if ($s === 'cpl') {
                $map['cpl'] = $i;
            }

            // Kode MK
            if (stripos($s, 'kode mk') !== false || $s === 'kode_mk' || $s === 'kdmk' || $s === 'kd mk') {
                $map['kode_mk'] = $i;
            }

            // Nama MK
            if (stripos($s, 'nama mk') !== false || $s === 'nama_mk' || $s === 'matakuliah' || $s === 'mk') {
                if ($map['nama_mk'] === null) {
                    $map['nama_mk'] = $i;
                }
            }

            // Kode CPMK
            if (stripos($s, 'kode cpmk') !== false || $s === 'kode_cpmk') {
                $map['kode_cpmk'] = $i;
            }

            // Skor / Skor Maksimal
            if (stripos($s, 'skor') !== false && stripos($s, 'bobot') === false) {
                $map['skor'] = $i;
            }

            // Bobot Partisipatif
            if (stripos($s, 'partisipatif') !== false || $s === 'part' || $s === 'bobot part') {
                $map['bobot_part'] = $i;
            }

            // Bobot Tugas
            if (stripos($s, 'tugas') !== false && stripos($s, 'bobot') !== false || $s === 'bobot_tugas' || $s === 'tugas') {
                $map['bobot_tugas'] = $i;
            }

            // Bobot UTS
            if (stripos($s, 'uts') !== false && stripos($s, 'bobot') !== false || $s === 'bobot_uts') {
                $map['bobot_uts'] = $i;
            }

            // Bobot UAS
            if (stripos($s, 'uas') !== false && stripos($s, 'bobot') !== false || $s === 'bobot_uas') {
                $map['bobot_uas'] = $i;
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

        // Read first sheet
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

            // Only process first sheet
            break;
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
            // Skip if CPMK not found
            if ($row['is_skip'] ?? false) {
                $stats['skipped']++;
                continue;
            }

            // Update or create based on kode_mk + cpmk_id
            $matriks = MatriksCpmk::where('kode_mk', $row['kode_mk'])
                ->where('cpmk_id', $row['cpmk_id'])
                ->first();

            if ($matriks) {
                // Update existing
                $matriks->update([
                    'nama_mk_matrix' => $row['nama_mk'] ?: $matriks->nama_mk_matrix,
                    'skor_maksimal' => $row['skor'] ?: $matriks->skor_maksimal,
                    'bobot_partisipatif' => $row['bobot_part'] ?: $matriks->bobot_partisipatif,
                    'bobot_tugas' => $row['bobot_tugas'] ?: $matriks->bobot_tugas,
                    'bobot_uts' => $row['bobot_uts'] ?: $matriks->bobot_uts,
                    'bobot_uas' => $row['bobot_uas'] ?: $matriks->bobot_uas,
                ]);
                $stats['updated']++;
            } else {
                // Create baru
                MatriksCpmk::create([
                    'cpmk_id' => $row['cpmk_id'],
                    'kode_mk' => $row['kode_mk'],
                    'nama_mk_matrix' => $row['nama_mk'] ?? null,
                    'bobot_partisipatif' => $row['bobot_part'] ?? 0,
                    'bobot_tugas' => $row['bobot_tugas'] ?? 0,
                    'bobot_uts' => $row['bobot_uts'] ?? 0,
                    'bobot_uas' => $row['bobot_uas'] ?? 0,
                    'skor_maksimal' => $row['skor'] ?? 0,
                ]);
                $stats['created']++;
            }
        }

        return $stats;
    }
}
