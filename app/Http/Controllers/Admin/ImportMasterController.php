<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\MatriksCpmk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ImportMasterController extends Controller
{
    /**
     * Display the import form
     */
    public function index()
    {
        $previewData = Session::get('import_preview_data');
        $previewIssues = Session::get('import_preview_issues');
        $previewConflicts = Session::get('import_preview_conflicts');
        $filename = Session::get('import_filename');

        return view('admin.import-master.index', [
            'previewData' => $previewData,
            'previewIssues' => $previewIssues,
            'previewConflicts' => $previewConflicts,
            'filename' => $filename,
        ]);
    }

    /**
     * Upload and preview the Excel/CSV file
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ], [
            'file.required' => 'File harus diupload',
            'file.mimes' => 'Format file harus .xlsx, .xls, .csv, atau .txt',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);

        try {
            $filepath = $request->file('file')->getRealPath();
            $parsedData = $this->parseFile($filepath);

            if (empty($parsedData)) {
                return redirect()->back()->with('error', 'File tidak memiliki data yang valid atau format header tidak dikenali.');
            }

            // Store in session for preview
            Session::put('import_preview_data', $parsedData['data']);
            Session::put('import_preview_issues', $parsedData['issues']);
            Session::put('import_preview_conflicts', $parsedData['conflicts']);
            Session::put('import_filename', $request->file('file')->getClientOriginalName());

            return redirect()->route('admin.import-master.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    /**
     * Parse Excel/CSV file
     */
    protected function parseFile($filepath)
    {
        $parsedData = [];
        $issues = [];
        $conflicts = [];

        // Forward fill values
        $lastKodeCpl = null;
        $lastDeskripsiCpl = null;
        $lastKodeMk = null;
        $lastNamaMk = null;

        // Track kode_mk + nama_mk combinations
        $mkNameVariations = [];

        // Read file based on extension
        $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        if (in_array($extension, ['xlsx', 'xls'])) {
            $rows = $this->parseExcelNative($filepath);
        } else {
            $rows = $this->parseCsv($filepath);
        }

        if (empty($rows)) {
            return ['data' => [], 'issues' => [], 'conflicts' => []];
        }

        // Find header row
        $headerRow = 0;
        foreach ($rows as $idx => $row) {
            $firstCell = strtolower(trim($row[0] ?? ''));
            // Look for 'no' or 'no.' as first column
            if (in_array($firstCell, ['no', 'no.', 'no', 'no.']) || preg_match('/^no\.?\s*$/i', $firstCell)) {
                $headerRow = $idx;
                break;
            }
        }

        // Map headers
        $colMapping = $this->mapHeaders($rows[$headerRow] ?? []);

        // Process data rows
        for ($i = $headerRow + 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $rowNum = $i + 1;

            // Get values
            $kodeCplRaw = $this->getCellValue($row, $colMapping['kode_cpl']);
            $deskripsiCplRaw = $this->getCellValue($row, $colMapping['deskripsi_cpl']);
            $kodeMkRaw = $this->getCellValue($row, $colMapping['kode_mk']);
            $namaMkRaw = $this->getCellValue($row, $colMapping['nama_mk']);
            $kodeCpmkRaw = $this->getCellValue($row, $colMapping['kode_cpmk']);
            $deskripsiCpmkRaw = $this->getCellValue($row, $colMapping['deskripsi_cpmk']);

            if (empty($kodeCpmkRaw)) {
                continue;
            }

            // Forward fill
            if (!empty($kodeCplRaw)) {
                $lastKodeCpl = $kodeCplRaw;
                $lastDeskripsiCpl = $deskripsiCplRaw ?? '';
            }
            if (!empty($kodeMkRaw)) {
                $lastKodeMk = $kodeMkRaw;
                $lastNamaMk = $namaMkRaw ?? '';
            }

            $kodeCpl = $lastKodeCpl ?? $kodeCplRaw;
            $deskripsiCpl = $lastDeskripsiCpl ?? $deskripsiCplRaw ?? '';
            $kodeMk = $lastKodeMk ?? $kodeMkRaw ?? '';
            $namaMk = $lastNamaMk ?? $namaMkRaw ?? '';

            $wasForwardFilled = empty($kodeCplRaw) && !empty($lastKodeCpl);

            // Check conflicts
            if (!empty($kodeMk)) {
                $normalizedMk = $this->normalizeKode($kodeMk);
                if (isset($mkNameVariations[$normalizedMk])) {
                    if ($mkNameVariations[$normalizedMk] !== $namaMk && !empty($mkNameVariations[$normalizedMk]) && !empty($namaMk)) {
                        $conflicts[] = [
                            'row' => $rowNum,
                            'kode_mk' => $kodeMk,
                            'nama_1' => $mkNameVariations[$normalizedMk],
                            'nama_2' => $namaMk,
                        ];
                    }
                } else {
                    $mkNameVariations[$normalizedMk] = $namaMk;
                }
            }

            if ($wasForwardFilled) {
                $issues[] = "Baris {$rowNum}: Kode CPL forward-filled dari baris sebelumnya";
            }

            $parsedData[] = [
                'row_num' => $rowNum,
                'kode_cpl' => $kodeCpl,
                'deskripsi_cpl' => $deskripsiCpl,
                'kode_mk' => $kodeMk,
                'nama_mk' => $namaMk,
                'kode_cpmk' => $kodeCpmkRaw,
                'deskripsi_cpmk' => $deskripsiCpmkRaw,
                'bobot_partisipatif' => $this->parseNumeric($this->getCellValue($row, $colMapping['bobot_partisipatif'])),
                'bobot_tugas' => $this->parseNumeric($this->getCellValue($row, $colMapping['bobot_tugas'])),
                'bobot_uts' => $this->parseNumeric($this->getCellValue($row, $colMapping['bobot_uts'])),
                'bobot_uas' => $this->parseNumeric($this->getCellValue($row, $colMapping['bobot_uas'])),
                'skor_maksimal' => $this->parseNumeric($this->getCellValue($row, $colMapping['skor_maksimal'])),
                'forward_filled' => $wasForwardFilled,
                'cpl_variation_issue' => null,
                'is_valid' => !empty($kodeCpl) && !empty($kodeCpmkRaw),
            ];
        }

        return [
            'data' => $parsedData,
            'issues' => $issues,
            'conflicts' => $conflicts,
        ];
    }

    /**
     * Parse Excel using native XML (OOXML format)
     */
    protected function parseExcelNative($filepath)
    {
        $rows = [];

        // For .xlsx files, read the shared strings and sheet data
        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($filepath) === true) {
                // Read shared strings
                $sharedStrings = [];
                if ($zip->locateName('xl/sharedStrings.xml') !== false) {
                    $stringsXml = $zip->getFromName('xl/sharedStrings.xml');
                    $xml = simplexml_load_string($stringsXml);
                    foreach ($xml->si as $si) {
                        $sharedStrings[] = trim((string)$si->t);
                    }
                }

                // Read first sheet
                $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
                $xml = simplexml_load_string($sheetXml);

                $ns = $xml->getNamespaces(true);
                $xml->registerXPathNamespace('x', $ns['r'] ?? 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

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

                $zip->close();
            }
        }

        return $rows;
    }

    /**
     * Parse CSV file
     */
    protected function parseCsv($filepath)
    {
        $rows = [];
        $handle = fopen($filepath, 'r');

        if ($handle) {
            // Try semicolon first (European format), then comma
            $delimiter = ';';
            $firstRow = fgetcsv($handle, 0, $delimiter);

            if (count($firstRow) < 2) {
                // Fall back to comma
                rewind($handle);
                $delimiter = ',';
            } else {
                // Put back the first row
                array_unshift($firstRow, null);
                unset($firstRow[0]);
                $rows[] = array_values($firstRow);
            }

            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        return $rows;
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
     * Map headers - exact match dengan template
     */
    protected function mapHeaders($headers)
    {
        $mapping = [
            'kode_cpl' => null,
            'deskripsi_cpl' => null,
            'kode_mk' => null,
            'nama_mk' => null,
            'kode_cpmk' => null,
            'deskripsi_cpmk' => null,
            'bobot_partisipatif' => null,
            'bobot_tugas' => null,
            'bobot_uts' => null,
            'bobot_uas' => null,
            'skor_maksimal' => null,
        ];

        foreach ($headers as $idx => $header) {
            $headerStr = trim((string)($header ?? ''));
            $headerLower = strtolower($headerStr);

            // No (first column)
            if (in_array($headerLower, ['no', 'no.', 'no ', 'no'])) {
                $mapping['kode_cpl'] = $idx; // Actually this is the row number, not kode_cpl
                continue;
            }

            // Kode CPL
            if ($headerLower === 'kode cpl') {
                $mapping['kode_cpl'] = $idx;
            }

            // CPL (deskripsi)
            if ($headerLower === 'cpl') {
                $mapping['deskripsi_cpl'] = $idx;
            }

            // Kode (kode MK)
            if ($headerLower === 'kode') {
                $mapping['kode_mk'] = $idx;
            }

            // Mata Kuliah
            if ($headerLower === 'mata kuliah') {
                $mapping['nama_mk'] = $idx;
            }

            // Kode CPMK
            if ($headerLower === 'kode cpmk') {
                $mapping['kode_cpmk'] = $idx;
            }

            // Deskripsi CPMK
            if ($headerLower === 'deskripsi cpmk') {
                $mapping['deskripsi_cpmk'] = $idx;
            }

            // Aktivitas Partisipatif
            if ($headerLower === 'aktivitas partisipatif') {
                $mapping['bobot_partisipatif'] = $idx;
            }

            // Hasil Proyek
            if ($headerLower === 'hasil proyek') {
                $mapping['bobot_tugas'] = $idx;
            }

            // Quiz
            if ($headerLower === 'quiz') {
                $mapping['bobot_uts'] = $idx;
            }

            // Tugas
            if ($headerLower === 'tugas') {
                $mapping['bobot_tugas'] = $idx;
            }

            // UTS
            if ($headerLower === 'uts') {
                $mapping['bobot_uts'] = $idx;
            }

            // UAS
            if ($headerLower === 'uas') {
                $mapping['bobot_uas'] = $idx;
            }

            // Skor Maksimal
            if ($headerLower === 'skor maksimal') {
                $mapping['skor_maksimal'] = $idx;
            }
        }

        return $mapping;
    }

    /**
     * Get cell value
     */
    protected function getCellValue(array $row, ?int $col)
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
     * Parse numeric value
     */
    protected function parseNumeric($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = str_replace([',', ' '], ['.', ''], (string)$value);
        return is_numeric($value) ? (float)$value : null;
    }

    /**
     * Normalize kode
     */
    protected function normalizeKode(string $kode): string
    {
        return strtoupper(str_replace(['-', ' ', '.'], '', trim($kode)));
    }

    /**
     * Process and save the imported data
     */
    public function process(Request $request)
    {
        $parsedData = Session::get('import_preview_data');
        $conflicts = Session::get('import_preview_conflicts') ?? [];

        if (empty($parsedData)) {
            return redirect()->route('admin.import-master.index')->with('error', 'Data tidak ditemukan. Silakan upload file terlebih dahulu.');
        }

        $unresolvedConflicts = $request->input('conflict_resolutions', []);
        $skipConflicts = $request->boolean('skip_conflicts');

        if (!$skipConflicts && count($conflicts) > 0 && count($unresolvedConflicts) < count($conflicts)) {
            return redirect()->back()->with('error', 'Masih ada konflik yang belum diselesaikan.');
        }

        try {
            $stats = $this->processData($parsedData, $unresolvedConflicts);

            Session::forget(['import_preview_data', 'import_preview_issues', 'import_preview_conflicts', 'import_filename']);

            return redirect()->route('admin.import-master.index')->with('success',
                "Import berhasil! CPL baru: {$stats['cpl_created']}, CPL diupdate: {$stats['cpl_updated']}. " .
                "CPMK baru: {$stats['cpmk_created']}, CPMK diupdate: {$stats['cpmk_updated']}. " .
                "Matriks baru: {$stats['matriks_created']}, Matriks diupdate: {$stats['matriks_updated']}." .
                ($stats['skipped'] > 0 ? " Baris dilewati: {$stats['skipped']}." : "")
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Process data to database
     */
    protected function processData(array $parsedData, array $conflictResolutions = []): array
    {
        $stats = [
            'cpl_created' => 0,
            'cpl_updated' => 0,
            'cpmk_created' => 0,
            'cpmk_updated' => 0,
            'matriks_created' => 0,
            'matriks_updated' => 0,
            'skipped' => 0,
        ];

        $groupedByCpl = collect($parsedData)->groupBy('kode_cpl');

        foreach ($groupedByCpl as $kodeCpl => $cplRows) {
            $normalizedKodeCpl = $this->normalizeKode($kodeCpl);
            $cpl = Cpl::whereRaw("REPLACE(REPLACE(UPPER(kode_cpl), '-', ''), ' ', '') = ?", [$normalizedKodeCpl])->first();

            if (!$cpl) {
                $cpl = Cpl::create([
                    'kode_cpl' => $kodeCpl,
                    'deskripsi' => $cplRows[0]['deskripsi_cpl'] ?? 'Deskripsi tidak tersedia',
                    'ambang_minimum' => 70.00,
                ]);
                $stats['cpl_created']++;
            } else {
                $firstDesc = $cplRows[0]['deskripsi_cpl'] ?? '';
                if ($firstDesc && $cpl->deskripsi !== $firstDesc) {
                    $cpl->update(['deskripsi' => $firstDesc]);
                    $stats['cpl_updated']++;
                }
            }

            $groupedByCpmk = collect($cplRows)->groupBy('kode_cpmk');

            foreach ($groupedByCpmk as $kodeCpmk => $cpmkRows) {
                $cpmk = Cpmk::where('kode_cpmk', $kodeCpmk)->first();

                if (!$cpmk) {
                    $cpmk = Cpmk::create([
                        'cpl_id' => $cpl->id,
                        'kode_cpmk' => $kodeCpmk,
                        'deskripsi' => $cpmkRows[0]['deskripsi_cpmk'] ?? '',
                        'ambang_minimum' => 70.00,
                    ]);
                    $stats['cpmk_created']++;
                } else {
                    $firstDesc = $cpmkRows[0]['deskripsi_cpmk'] ?? '';
                    if ($firstDesc && $cpmk->deskripsi !== $firstDesc) {
                        $cpmk->update(['deskripsi' => $firstDesc]);
                        $stats['cpmk_updated']++;
                    }
                    if ($cpmk->cpl_id !== $cpl->id) {
                        $cpmk->update(['cpl_id' => $cpl->id]);
                    }
                }

                foreach ($cpmkRows as $row) {
                    if (empty($row['kode_mk'])) {
                        $stats['skipped']++;
                        continue;
                    }

                    $namaMk = $row['nama_mk'];
                    $conflictKey = "{$row['row_num']}_{$row['kode_mk']}";
                    if (isset($conflictResolutions[$conflictKey])) {
                        $namaMk = $conflictResolutions[$conflictKey];
                    }

                    $matriks = MatriksCpmk::where('cpmk_id', $cpmk->id)
                        ->where('kode_mk', $row['kode_mk'])
                        ->first();

                    if (!$matriks) {
                        MatriksCpmk::create([
                            'cpmk_id' => $cpmk->id,
                            'kode_mk' => $row['kode_mk'],
                            'nama_mk_matrix' => $namaMk,
                            'bobot_partisipatif' => $row['bobot_partisipatif'] ?? 0,
                            'bobot_tugas' => $row['bobot_tugas'] ?? 0,
                            'bobot_uts' => $row['bobot_uts'] ?? 0,
                            'bobot_uas' => $row['bobot_uas'] ?? 0,
                            'skor_maksimal' => $row['skor_maksimal'] ?? 0,
                        ]);
                        $stats['matriks_created']++;
                    } else {
                        $matriks->update([
                            'nama_mk_matrix' => $namaMk ?: $matriks->nama_mk_matrix,
                            'bobot_partisipatif' => $row['bobot_partisipatif'] ?? $matriks->bobot_partisipatif,
                            'bobot_tugas' => $row['bobot_tugas'] ?? $matriks->bobot_tugas,
                            'bobot_uts' => $row['bobot_uts'] ?? $matriks->bobot_uts,
                            'bobot_uas' => $row['bobot_uas'] ?? $matriks->bobot_uas,
                            'skor_maksimal' => $row['skor_maksimal'] ?? $matriks->skor_maksimal,
                        ]);
                        $stats['matriks_updated']++;
                    }
                }
            }
        }

        return $stats;
    }

    /**
     * Download template file
     */
    public function downloadTemplate()
    {
        // Headers sesuai format yang diharapkan
        $headers = [
            'No',
            'Kode CPL',
            'CPL',
            'Kode',
            'Mata Kuliah',
            'Kode CPMK',
            'Deskripsi CPMK',
            'Aktivitas Partisipatif',
            'Hasil Proyek',
            'Quiz',
            'Tugas',
            'UTS',
            'UAS',
            'Skor Maksimal',
        ];

        $sampleData = [
            1,
            'CPL-01',
            'Deskripsi CPL 01',
            'MK-01',
            'Nama Mata Kuliah 1',
            'CPMK.01.1',
            'Deskripsi CPMK 01.1',
            10,
            20,
            10,
            20,
            20,
            20,
            100,
        ];

        // Create CSV with proper formatting
        $output = fopen('php://temp', 'r+');

        // UTF-8 BOM for Excel compatibility
        fwrite($output, "\xEF\xBB\xBF");

        // Write headers
        fputcsv($output, $headers, ';');

        // Write sample data
        fputcsv($output, $sampleData, ';');

        // Write a few more sample rows with forward-fill
        fputcsv($output, [2, '', '', 'MK-02', 'Nama MK 2', 'CPMK.01.2', 'Deskripsi CPMK 01.2', 15, 25, 10, 25, 15, 10, 100], ';');
        fputcsv($output, [3, '', '', 'MK-03', 'Nama MK 3', 'CPMK.01.3', 'Deskripsi CPMK 01.3', 10, 30, 10, 30, 10, 10, 100], ';');

        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_cpl_cpmk.csv"',
        ]);
    }

    /**
     * Clear preview data
     */
    public function clear()
    {
        Session::forget(['import_preview_data', 'import_preview_issues', 'import_preview_conflicts', 'import_filename']);
        return redirect()->route('admin.import-master.index');
    }
}
