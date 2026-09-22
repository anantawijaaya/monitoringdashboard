<?php

namespace App\Services;

use App\Models\RegionalOutlet;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class RegionalOutletImportService
{
    /**
     * Clean and ensure string is valid UTF-8
     */
    private function cleanUtf8(string $str): string
    {
        $cleaned = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
        if ($cleaned === false) {
            $cleaned = mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1');
        }
        return trim($cleaned);
    }

    /**
     * Convert Excel column letter (A, B, ..., Z, AA, etc.) to 0-based column index
     */
    private function columnLetterToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $len = strlen($letters);
        $index = 0;
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - ord('A') + 1);
        }
        return $index - 1;
    }

    /**
     * Parse and import uploaded CSV or Excel file
     */
    public function importFile(UploadedFile $file, string $mode = 'replace'): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filePath = $file->getRealPath();

        // 1. Check if file is HTML table exported with .xls or .xlsx extension
        $fileHead = @file_get_contents($filePath, false, null, 0, 2048);
        if ($fileHead !== false && (
            str_contains(strtolower($fileHead), '<table') || 
            str_contains(strtolower($fileHead), '<html') || 
            str_contains(strtolower($fileHead), '<?xml')
        )) {
            $rawRows = $this->parseHtmlTable($filePath);
            return $this->processExtractedRows($rawRows, $mode);
        }

        $rawRows = [];

        if (in_array($extension, ['xlsx', 'xlsm'])) {
            try {
                $rawRows = $this->parseXlsx($filePath);
            } catch (Exception $e) {
                try {
                    $rawRows = $this->parseHtmlTable($filePath);
                } catch (Exception $e2) {
                    $rawRows = $this->parseCsv($filePath);
                }
            }
        } elseif (in_array($extension, ['csv', 'txt'])) {
            $rawRows = $this->parseCsv($filePath);
        } else {
            // Fallback for .xls or unknown extensions
            try {
                $rawRows = $this->parseXlsx($filePath);
            } catch (Exception $e) {
                try {
                    $rawRows = $this->parseHtmlTable($filePath);
                } catch (Exception $e2) {
                    $rawRows = $this->parseCsv($filePath);
                }
            }
        }

        if (empty($rawRows) || count($rawRows) < 2) {
            throw new Exception('File tidak berisi data atau format berkas tidak dapat dibaca.');
        }

        return $this->processExtractedRows($rawRows, $mode);
    }

    /**
     * Parse Native XLSX using PHP ZipArchive & XML (Multi-Sheet Support)
     */
    public function parseXlsx(string $filePath): array
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception("Ekstensi ZipArchive PHP belum aktif pada server.");
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception("Gagal membuka file Excel (.xlsx). Pastikan berkas tidak terenkripsi atau rusak.");
        }

        // 1. Extract shared strings
        $sharedStrings = [];
        $sharedStringsXML = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXML !== false) {
            $xml = @simplexml_load_string($sharedStringsXML);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Find All Worksheet Entries
        $sheetEntries = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (preg_match('#xl/worksheets/sheet\d+\.xml#i', $entry) || preg_match('#xl/worksheets/.*\.xml#i', $entry)) {
                $sheetEntries[] = $entry;
            }
        }

        if (empty($sheetEntries)) {
            $zip->close();
            throw new Exception("Lembar kerja (worksheet XML) tidak ditemukan di dalam berkas Excel.");
        }

        natsort($sheetEntries);
        $sheetEntries = array_values($sheetEntries);

        // 3. Extract Rows from worksheets
        $allRows = [];
        foreach ($sheetEntries as $sheetEntry) {
            $sheetXML = $zip->getFromName($sheetEntry);
            if ($sheetXML === false) continue;

            $xml = @simplexml_load_string($sheetXML);
            if (!$xml || !isset($xml->sheetData->row)) continue;

            foreach ($xml->sheetData->row as $row) {
                $rowData = [];
                $currentColIndex = 0;

                foreach ($row->c as $cell) {
                    $cellRef = isset($cell['r']) ? (string) $cell['r'] : '';
                    if (!empty($cellRef) && preg_match('/([A-Z]+)(\d+)/', $cellRef, $matches)) {
                        $colLetters = $matches[1];
                        $colIndex = $this->columnLetterToIndex($colLetters);
                    } else {
                        $colIndex = $currentColIndex;
                    }

                    $cellType = (string) $cell['t'];
                    $val = '';

                    if (isset($cell->v)) {
                        $rawVal = (string) $cell->v;
                        if ($cellType === 's') {
                            $stringIndex = (int) $rawVal;
                            $val = $sharedStrings[$stringIndex] ?? '';
                        } else {
                            $val = $rawVal;
                        }
                    } elseif (isset($cell->is)) {
                        if (isset($cell->is->t)) {
                            $val = (string) $cell->is->t;
                        } elseif (isset($cell->is->r)) {
                            $text = '';
                            foreach ($cell->is->r as $r) {
                                $text .= (string) ($r->t ?? '');
                            }
                            $val = $text;
                        }
                    }

                    $rowData[$colIndex] = $this->cleanUtf8($val);
                    $currentColIndex = $colIndex + 1;
                }

                if (!empty($rowData)) {
                    $maxIndex = max(array_keys($rowData));
                    $normalizedRow = [];
                    for ($c = 0; $c <= $maxIndex; $c++) {
                        $normalizedRow[$c] = $rowData[$c] ?? '';
                    }
                    $allRows[] = $normalizedRow;
                }
            }

            if (!empty($allRows)) {
                break; // Stop after first non-empty sheet
            }
        }

        $zip->close();

        if (empty($allRows)) {
            throw new Exception("Data lembar kerja Excel kosong atau tidak dapat diuraikan.");
        }

        return $allRows;
    }

    /**
     * Import HTML Table (.xls / .xlsx exported from Web Applications)
     */
    public function parseHtmlTable(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new Exception("Gagal membaca file HTML table.");
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $tables = $dom->getElementsByTagName('table');
        if ($tables->length === 0) {
            throw new Exception("Tidak ditemukan elemen tabel HTML dalam file.");
        }

        $rows = [];
        foreach ($tables as $table) {
            $trList = $table->getElementsByTagName('tr');
            foreach ($trList as $tr) {
                $rowData = [];
                $cells = $tr->getElementsByTagName('td');
                if ($cells->length === 0) {
                    $cells = $tr->getElementsByTagName('th');
                }
                foreach ($cells as $cell) {
                    $rowData[] = $this->cleanUtf8($cell->textContent);
                }

                if (!empty(array_filter($rowData, fn($v) => $v !== ''))) {
                    $rows[] = $rowData;
                }
            }
            if (!empty($rows)) break;
        }

        return $rows;
    }

    /**
     * Parse CSV file
     */
    public function parseCsv(string $filePath): array
    {
        $rows = [];
        $delimiters = [",", ";", "\t", "|"];
        $detectedDelimiter = ",";

        // Detect delimiter
        $sample = file_get_contents($filePath, false, null, 0, 4096);
        $maxCount = 0;
        foreach ($delimiters as $delim) {
            $count = substr_count($sample, $delim);
            if ($count > $maxCount) {
                $maxCount = $count;
                $detectedDelimiter = $delim;
            }
        }

        if (($handle = fopen($filePath, "r")) !== false) {
            $bom = "\xEF\xBB\xBF";
            $isFirst = true;

            while (($data = fgetcsv($handle, 4096, $detectedDelimiter)) !== false) {
                if ($isFirst && !empty($data)) {
                    $data[0] = preg_replace("/^$bom/", '', $data[0]);
                    $isFirst = false;
                }

                $cleanedRow = array_map(function ($val) {
                    return $this->cleanUtf8((string) $val);
                }, $data);

                if (!empty(array_filter($cleanedRow, fn($v) => $v !== ''))) {
                    $rows[] = $cleanedRow;
                }
            }
            fclose($handle);
        }

        return $rows;
    }

    /**
     * Normalize string for header comparison
     */
    private function normalizeHeader(string $h): string
    {
        $h = strtolower(trim($h));
        $h = str_replace([' ', '_', '-', '/', '\\', '(', ')', '%', '.', ':'], '', $h);
        return $h;
    }

    /**
     * Clean numeric/currency values (e.g. "Rp 120.000.000" -> 120000000)
     */
    private function cleanNumeric(string $val): float
    {
        $val = trim($val);
        $val = preg_replace('/[^0-9.,\-]/', '', $val);

        if (empty($val)) {
            return 0.0;
        }

        if (strpos($val, '.') !== false && strpos($val, ',') !== false) {
            if (strrpos($val, ',') > strrpos($val, '.')) {
                $val = str_replace('.', '', $val);
                $val = str_replace(',', '.', $val);
            } else {
                $val = str_replace(',', '', $val);
            }
        } elseif (strpos($val, ',') !== false) {
            $val = str_replace(',', '.', $val);
        }

        return (float) $val;
    }

    /**
     * Parse flag_omzet values intelligently
     */
    private function cleanFlagOmzet(string $val): float
    {
        $val = trim($val);
        if ($val === '') {
            return 0.0;
        }

        $lower = strtolower($val);

        if (str_contains($lower, 'putih') || str_contains($lower, 'penurunan') || str_contains($lower, 'turun')) {
            return -1.0;
        }
        if (str_contains($lower, 'merah') || str_contains($lower, 'stagnan')) {
            return 0.0;
        }
        if (str_contains($lower, 'orange') || str_contains($lower, 'oranye') || str_contains($lower, 'kuning') || str_contains($lower, 'moderat')) {
            return 2.0;
        }
        if (str_contains($lower, 'hijau') || str_contains($lower, 'tinggi') || str_contains($lower, 'tumbuh')) {
            return 4.0;
        }

        if (
            str_contains($lower, '<0') || 
            str_contains($lower, '< 0') || 
            str_contains($lower, '<=0') || 
            str_contains($lower, '<= 0') ||
            str_contains($lower, '<=0%') ||
            str_contains($lower, '<= 0%') ||
            str_contains($lower, '< 0%') ||
            str_contains($lower, '<0%')
        ) {
            return -1.0;
        }

        if (str_contains($lower, '>3') || str_contains($lower, '> 3')) {
            return 4.0;
        }

        if (str_contains($lower, '<=3') || str_contains($lower, '<= 3') || str_contains($lower, '0-3') || str_contains($lower, '0 - 3')) {
            return 2.0;
        }

        if ($lower === '=0%' || $lower === '=0' || $lower === '= 0%' || $lower === '= 0' || $lower === '0%' || $lower === '0' || $lower === '0.00' || $lower === '0,00') {
            return 0.0;
        }

        $hasNegative = (strpos($val, '-') !== false);
        $cleaned = str_replace(['%', ' ', 'Rp', 'rp', 'RP', '+'], '', $val);

        if (strpos($cleaned, '.') !== false && strpos($cleaned, ',') !== false) {
            if (strrpos($cleaned, ',') > strrpos($cleaned, '.')) {
                $cleaned = str_replace('.', '', $cleaned);
                $cleaned = str_replace(',', '.', $cleaned);
            } else {
                $cleaned = str_replace(',', '', $cleaned);
            }
        } elseif (strpos($cleaned, ',') !== false) {
            $cleaned = str_replace(',', '.', $cleaned);
        }

        if (preg_match('/-?\d+(\.\d+)?/', $cleaned, $matches)) {
            $floatVal = (float) $matches[0];
            if ($hasNegative && $floatVal > 0) {
                $floatVal = -$floatVal;
            }
            return $floatVal;
        }

        return 0.0;
    }

    /**
     * Process extracted rows into database records with high-speed chunk batching
     */
    public function processExtractedRows(array $rows, string $mode = 'replace'): array
    {
        if (empty($rows)) {
            throw new Exception("Data baris kosong.");
        }

        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        // Find header row
        $headerRowIndex = 0;
        $headers = [];

        for ($r = 0; $r < min(10, count($rows)); $r++) {
            $candidate = $rows[$r];
            $normalizedCandidate = array_map([$this, 'normalizeHeader'], $candidate);

            $hasIdOutlet = false;
            $hasCoords = false;
            $hasLocation = false;

            foreach ($normalizedCandidate as $nh) {
                if (in_array($nh, ['idoutlet', 'id', 'outletid', 'kodeoutlet', 'outlet', 'siteid', 'site', 'idoutlet/siteid', 'id_outlet', 'outlet_id', 'no', 'nomor', 'kode', 'site_id', 'nosite'])) {
                    $hasIdOutlet = true;
                }
                if (in_array($nh, ['longitude', 'long', 'lng', 'bujur', 'x', 'lon', 'longtitude', 'longi', 'latitude', 'lat', 'lintang', 'y', 'lati'])) {
                    $hasCoords = true;
                }
                if (in_array($nh, ['kabupaten', 'kab', 'kota', 'kabupatenkota', 'city', 'cluster', 'klaster', 'branch', 'cabang'])) {
                    $hasLocation = true;
                }
            }

            if ($hasIdOutlet || $hasCoords || $hasLocation) {
                $headerRowIndex = $r;
                $headers = $candidate;
                break;
            }
        }

        if (empty($headers)) {
            $headers = $rows[0];
            $headerRowIndex = 0;
        }

        // Map column indices
        $map = [
            'id_outlet' => -1,
            'longitude' => -1,
            'latitude' => -1,
            'kabupaten' => -1,
            'cluster' => -1,
            'branch' => -1,
            'total_omzet' => -1,
            'flag_omzet' => -1,
        ];

        foreach ($headers as $idx => $rawHeader) {
            $nh = $this->normalizeHeader($rawHeader);

            if ($map['id_outlet'] === -1 && in_array($nh, ['idoutlet', 'id', 'outletid', 'kodeoutlet', 'outlet', 'siteid', 'site', 'idoutlet/siteid', 'id_outlet', 'outlet_id', 'kode', 'site_id', 'nosite'])) {
                $map['id_outlet'] = $idx;
            } elseif ($map['longitude'] === -1 && in_array($nh, ['longitude', 'long', 'lng', 'bujur', 'x', 'lon', 'longtitude', 'longi'])) {
                $map['longitude'] = $idx;
            } elseif ($map['latitude'] === -1 && in_array($nh, ['latitude', 'lat', 'lintang', 'y', 'lati'])) {
                $map['latitude'] = $idx;
            } elseif ($map['kabupaten'] === -1 && in_array($nh, ['kabupaten', 'kab', 'kota', 'kabupatenkota', 'city', 'kab/kota', 'kabupaten_kota'])) {
                $map['kabupaten'] = $idx;
            } elseif ($map['cluster'] === -1 && in_array($nh, ['cluster', 'klaster', 'cluster_name', 'namacluster'])) {
                $map['cluster'] = $idx;
            } elseif ($map['branch'] === -1 && in_array($nh, ['branch', 'cabang', 'branch_name', 'namacabang'])) {
                $map['branch'] = $idx;
            } elseif ($map['total_omzet'] === -1 && in_array($nh, ['totalomzet', 'omzet', 'revenue', 'totalrevenue', 'omset', 'totalomset', 'sales', 'totalsales', 'rev'])) {
                $map['total_omzet'] = $idx;
            } elseif ($map['flag_omzet'] === -1 && in_array($nh, ['flagomzet', 'flag', 'growth', 'pertumbuhan', 'flagomset', 'growthomzet', 'persenomzet', 'mom'])) {
                $map['flag_omzet'] = $idx;
            }
        }

        // Fallback positioning if headers not matched
        if ($map['id_outlet'] === -1) $map['id_outlet'] = 0;
        if ($map['longitude'] === -1) $map['longitude'] = 1;
        if ($map['latitude'] === -1) $map['latitude'] = 2;
        if ($map['kabupaten'] === -1) $map['kabupaten'] = 3;
        if ($map['cluster'] === -1) $map['cluster'] = 4;
        if ($map['branch'] === -1) $map['branch'] = 5;
        if ($map['total_omzet'] === -1) $map['total_omzet'] = 6;
        if ($map['flag_omzet'] === -1) $map['flag_omzet'] = 7;

        $dataRows = array_slice($rows, $headerRowIndex + 1);
        $totalProcessed = 0;
        $insertedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        $now = now();
        $recordsToInsert = [];
        $seenIds = [];
        $existingOutletsMap = [];

        if ($mode === 'append') {
            $existingOutletsMap = RegionalOutlet::pluck('id', 'id_outlet')->toArray();
        }

        DB::beginTransaction();
        try {
            if ($mode === 'replace') {
                RegionalOutlet::query()->delete();
            }

            $autoIndex = 1;
            foreach ($dataRows as $row) {
                $rawId = trim((string) ($row[$map['id_outlet']] ?? ''));
                
                if (empty($rawId) || $rawId === '-') {
                    $idOutlet = 'OUT-AUTO-' . sprintf('%05d', $autoIndex++);
                } else {
                    $idOutlet = $rawId;
                }

                // Prevent duplicate id_outlet in the batch from failing unique key constraint
                $finalIdOutlet = $idOutlet;
                $dupCounter = 2;
                while (isset($seenIds[$finalIdOutlet])) {
                    $finalIdOutlet = $idOutlet . '-' . $dupCounter;
                    $dupCounter++;
                }
                $seenIds[$finalIdOutlet] = true;
                $idOutlet = $finalIdOutlet;

                $longitude = $this->cleanNumeric((string) ($row[$map['longitude']] ?? '0'));
                $latitude = $this->cleanNumeric((string) ($row[$map['latitude']] ?? '0'));
                $kabupaten = trim((string) ($row[$map['kabupaten']] ?? '-'));
                $cluster = trim((string) ($row[$map['cluster']] ?? '-'));
                $branch = trim((string) ($row[$map['branch']] ?? '-'));
                $totalOmzet = $this->cleanNumeric((string) ($row[$map['total_omzet']] ?? '0'));
                $flagOmzet = $this->cleanFlagOmzet((string) ($row[$map['flag_omzet']] ?? '0'));

                // Handle swapped coordinates (if latitude is positive >90 and longitude is negative)
                if ($latitude > 90.0 && $longitude < 0.0) {
                    $tmp = $longitude;
                    $longitude = $latitude;
                    $latitude = $tmp;
                }

                if ($longitude == 0.0 && $latitude == 0.0) {
                    $longitude = 115.2126;
                    $latitude = -8.6705;
                }

                $record = [
                    'id_outlet' => $idOutlet,
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'kabupaten' => $kabupaten ?: 'Kota Denpasar',
                    'cluster' => $cluster ?: 'Cluster Denpasar Kota',
                    'branch' => $branch ?: 'Branch Denpasar',
                    'total_omzet' => $totalOmzet,
                    'flag_omzet' => $flagOmzet,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($mode === 'append' && isset($existingOutletsMap[$idOutlet])) {
                    $outletId = $existingOutletsMap[$idOutlet];
                    RegionalOutlet::where('id', $outletId)->update([
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'kabupaten' => $kabupaten ?: 'Kota Denpasar',
                        'cluster' => $cluster ?: 'Cluster Denpasar Kota',
                        'branch' => $branch ?: 'Branch Denpasar',
                        'total_omzet' => $totalOmzet,
                        'flag_omzet' => $flagOmzet,
                        'updated_at' => $now,
                    ]);
                    $updatedCount++;
                } else {
                    $recordsToInsert[] = $record;
                    $insertedCount++;
                }

                $totalProcessed++;
            }

            // High-speed chunked batch insert (1000 items per chunk)
            if (!empty($recordsToInsert)) {
                $chunks = array_chunk($recordsToInsert, 1000);
                foreach ($chunks as $chunk) {
                    DB::table('regional_outlets')->insert($chunk);
                }
            }

            DB::commit();

            return [
                'total_processed' => $totalProcessed,
                'inserted' => $insertedCount,
                'updated' => $updatedCount,
                'skipped' => $skippedCount,
            ];
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            throw new Exception("Gagal menyimpan data ke database: " . $e->getMessage());
        }
    }
}
