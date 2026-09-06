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

        $rawRows = [];

        if (in_array($extension, ['xlsx', 'xlsm'])) {
            $rawRows = $this->parseXlsx($filePath);
        } elseif (in_array($extension, ['csv', 'txt'])) {
            $rawRows = $this->parseCsv($filePath);
        } elseif (in_array($extension, ['xls'])) {
            try {
                $rawRows = $this->parseXlsx($filePath);
            } catch (Exception $e) {
                $rawRows = $this->parseCsv($filePath);
            }
        } else {
            try {
                $rawRows = $this->parseXlsx($filePath);
            } catch (Exception $e) {
                $rawRows = $this->parseCsv($filePath);
            }
        }

        if (empty($rawRows) || count($rawRows) < 2) {
            throw new Exception('File tidak berisi data atau format berkas tidak valid.');
        }

        return $this->processExtractedRows($rawRows, $mode);
    }

    /**
     * Parse Native XLSX using PHP ZipArchive & XML
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

        // Extract shared strings
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

        // Read Sheet 1
        $sheetXML = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXML === false) {
            $zip->close();
            throw new Exception("Lembar kerja sheet1.xml tidak ditemukan di dalam berkas Excel.");
        }

        $xml = @simplexml_load_string($sheetXML);
        $zip->close();

        if (!$xml || !isset($xml->sheetData)) {
            throw new Exception("Data lembar kerja Excel kosong atau tidak dapat diuraikan.");
        }

        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $rowNum = (int) $row['r'];
            $rowData = [];

            foreach ($row->c as $cell) {
                $cellRef = (string) $cell['r'];
                preg_match('/([A-Z]+)(\d+)/', $cellRef, $matches);
                $colLetters = $matches[1] ?? 'A';
                $colIndex = $this->columnLetterToIndex($colLetters);

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
                } elseif (isset($cell->is->t)) {
                    $val = (string) $cell->is->t;
                }

                $rowData[$colIndex] = $this->cleanUtf8($val);
            }

            if (!empty($rowData)) {
                $maxIndex = max(array_keys($rowData));
                $normalizedRow = [];
                for ($i = 0; $i <= $maxIndex; $i++) {
                    $normalizedRow[$i] = $rowData[$i] ?? '';
                }
                $rows[] = $normalizedRow;
            }
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
            while (($data = fgetcsv($handle, 4096, $detectedDelimiter)) !== false) {
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
        $h = str_replace([' ', '_', '-', '/', '\\', '(', ')', '%', '.'], '', $h);
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

        // If string contains both comma and dot e.g. 1.250.000,50 or 1,250,000.50
        if (strpos($val, '.') !== false && strpos($val, ',') !== false) {
            if (strrpos($val, ',') > strrpos($val, '.')) {
                // European/Indonesian: 1.250.000,50
                $val = str_replace('.', '', $val);
                $val = str_replace(',', '.', $val);
            } else {
                // US: 1,250,000.50
                $val = str_replace(',', '', $val);
            }
        } elseif (strpos($val, ',') !== false) {
            // Only comma e.g. 3,5 or 100,00
            $val = str_replace(',', '.', $val);
        }

        return (float) $val;
    }

    /**
     * Parse flag_omzet values intelligently from Excel/CSV cells
     * Handles relational expressions (<0%, >3%, <=3%, =0%), text categories (putih, merah, orange, hijau),
     * percentage strings (-2.5%, 5.4%), and float decimals.
     */
    private function cleanFlagOmzet(string $val): float
    {
        $val = trim($val);
        if ($val === '') {
            return 0.0;
        }

        $lower = strtolower($val);

        // 1. Text labels/categories
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

        // 2. Relational symbol expressions
        // Less than or less than/equal to 0% (White Flag < 0%)
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

        // Greater than 3% (Green Flag > 3%)
        if (str_contains($lower, '>3') || str_contains($lower, '> 3')) {
            return 4.0;
        }

        // Less than or equal to 3% (Orange Flag <= 3%)
        if (str_contains($lower, '<=3') || str_contains($lower, '<= 3') || str_contains($lower, '0-3') || str_contains($lower, '0 - 3')) {
            return 2.0;
        }

        // Exactly 0% (Red Flag = 0%)
        if ($lower === '=0%' || $lower === '=0' || $lower === '= 0%' || $lower === '= 0' || $lower === '0%' || $lower === '0' || $lower === '0.00' || $lower === '0,00') {
            return 0.0;
        }

        // 3. Numeric float parsing (e.g. "-1.5%", "5.4%", "-2", "3.2")
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

        // Find header row
        $headerRowIndex = 0;
        $headers = [];

        for ($r = 0; $r < min(5, count($rows)); $r++) {
            $candidate = $rows[$r];
            $normalizedCandidate = array_map([$this, 'normalizeHeader'], $candidate);

            $hasIdOutlet = false;
            $hasCoords = false;
            foreach ($normalizedCandidate as $nh) {
                if (in_array($nh, ['idoutlet', 'id', 'outletid', 'kodeoutlet', 'outlet'])) {
                    $hasIdOutlet = true;
                }
                if (in_array($nh, ['longitude', 'long', 'lng', 'latitude', 'lat'])) {
                    $hasCoords = true;
                }
            }

            if ($hasIdOutlet || $hasCoords) {
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

            if (in_array($nh, ['idoutlet', 'id', 'outletid', 'kodeoutlet', 'outlet'])) {
                $map['id_outlet'] = $idx;
            } elseif (in_array($nh, ['longitude', 'long', 'lng', 'bujur', 'x'])) {
                $map['longitude'] = $idx;
            } elseif (in_array($nh, ['latitude', 'lat', 'lintang', 'y'])) {
                $map['latitude'] = $idx;
            } elseif (in_array($nh, ['kabupaten', 'kab', 'kota', 'kabupatenkota', 'city'])) {
                $map['kabupaten'] = $idx;
            } elseif (in_array($nh, ['cluster', 'klaster'])) {
                $map['cluster'] = $idx;
            } elseif (in_array($nh, ['branch', 'cabang'])) {
                $map['branch'] = $idx;
            } elseif (in_array($nh, ['totalomzet', 'omzet', 'revenue', 'totalrevenue', 'omset', 'totalomset'])) {
                $map['total_omzet'] = $idx;
            } elseif (in_array($nh, ['flagomzet', 'flag', 'growth', 'pertumbuhan', 'flagomset', 'growthomzet', 'persenomzet'])) {
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
        $existingOutletsMap = [];

        if ($mode === 'append') {
            $existingOutletsMap = RegionalOutlet::pluck('id', 'id_outlet')->toArray();
        }

        DB::beginTransaction();
        try {
            if ($mode === 'replace') {
                // Use DML delete() instead of DDL truncate() to avoid implicit MySQL transaction commits
                RegionalOutlet::query()->delete();
            }

            foreach ($dataRows as $row) {
                $idOutlet = trim((string) ($row[$map['id_outlet']] ?? ''));
                if (empty($idOutlet)) {
                    $skippedCount++;
                    continue;
                }

                $longitude = $this->cleanNumeric((string) ($row[$map['longitude']] ?? '0'));
                $latitude = $this->cleanNumeric((string) ($row[$map['latitude']] ?? '0'));
                $kabupaten = trim((string) ($row[$map['kabupaten']] ?? '-'));
                $cluster = trim((string) ($row[$map['cluster']] ?? '-'));
                $branch = trim((string) ($row[$map['branch']] ?? '-'));
                $totalOmzet = $this->cleanNumeric((string) ($row[$map['total_omzet']] ?? '0'));
                $flagOmzet = $this->cleanFlagOmzet((string) ($row[$map['flag_omzet']] ?? '0'));

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

            // High-speed chunked batch insert
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
