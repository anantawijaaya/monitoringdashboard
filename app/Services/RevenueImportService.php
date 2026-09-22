<?php

namespace App\Services;

use App\Models\ClusterRevenue;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Exception;
use ZipArchive;

class RevenueImportService
{
    /**
     * Parse and import CSV / Excel file into cluster_revenues table
     */
    public function importFile(UploadedFile $file, string $mode = 'replace'): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filePath = $file->getRealPath();

        // Check if file is HTML table exported with .xls or .xlsx extension
        $fileHead = file_get_contents($filePath, false, null, 0, 2048);
        if (str_contains(strtolower($fileHead), '<table') || str_contains(strtolower($fileHead), '<html') || str_contains(strtolower($fileHead), '<?xml')) {
            return $this->importHtmlTable($filePath, $mode);
        }

        if (in_array($extension, ['xlsx', 'xlsm'])) {
            return $this->importXlsx($filePath, $mode);
        } elseif (in_array($extension, ['csv', 'txt'])) {
            return $this->importCsv($filePath, $mode);
        } else {
            // Fallback strategy: try XLSX -> HTML Table -> CSV
            try {
                return $this->importXlsx($filePath, $mode);
            } catch (Exception $e) {
                try {
                    return $this->importHtmlTable($filePath, $mode);
                } catch (Exception $e2) {
                    return $this->importCsv($filePath, $mode);
                }
            }
        }
    }

    /**
     * Import Native XLSX via ZipArchive + SimpleXML (Multi-Sheet Support)
     */
    public function importXlsx(string $filePath, string $mode = 'replace'): array
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception("Ekstensi ZipArchive PHP belum aktif.");
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return $this->importHtmlTable($filePath);
        }

        // 1. Extract Shared Strings
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = simplexml_load_string($sharedStringsXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string)$r->t;
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
            if (preg_match('#xl/worksheets/sheet\d+\.xml#i', $entry)) {
                $sheetEntries[] = $entry;
            }
        }

        if (empty($sheetEntries)) {
            $zip->close();
            throw new Exception("Tidak dapat menemukan lembar kerja (worksheet) di dalam file Excel.");
        }

        // 3. Extract Rows across all sheets
        $allSheetRows = [];
        foreach ($sheetEntries as $sheetEntry) {
            $sheetXmlContent = $zip->getFromName($sheetEntry);
            if ($sheetXmlContent === false) continue;

            $sheetXml = simplexml_load_string($sheetXmlContent);
            if (!$sheetXml || !isset($sheetXml->sheetData->row)) continue;

            $rows = [];
            foreach ($sheetXml->sheetData->row as $row) {
                $rowData = [];
                foreach ($row->c as $cell) {
                    $cellRef = (string)$cell['r'];
                    $colLetters = preg_replace('/[0-9]/', '', $cellRef);
                    $colIndex = $this->columnLetterToIndex($colLetters);

                    $cellType = (string)$cell['t'];
                    $cellValue = '';

                    if ($cellType === 's') {
                        $sIndex = intval((string)$cell->v);
                        $cellValue = isset($sharedStrings[$sIndex]) ? $sharedStrings[$sIndex] : '';
                    } elseif ($cellType === 'inlineStr' && isset($cell->is->t)) {
                        $cellValue = (string)$cell->is->t;
                    } elseif (isset($cell->v)) {
                        $cellValue = (string)$cell->v;
                    }

                    $rowData[$colIndex] = trim($cellValue);
                }

                if (!empty($rowData)) {
                    $maxCol = max(array_keys($rowData));
                    $fullRow = [];
                    for ($c = 0; $c <= $maxCol; $c++) {
                        $fullRow[$c] = isset($rowData[$c]) ? $rowData[$c] : '';
                    }
                    $rows[] = $fullRow;
                }
            }

            if (!empty($rows)) {
                $allSheetRows[] = $rows;
            }
        }
        $zip->close();

        if (empty($allSheetRows)) {
            throw new Exception("Lembar kerja Excel kosong atau tidak memiliki data.");
        }

        $lastException = null;
        foreach ($allSheetRows as $sheetRows) {
            try {
                return $this->processExtractedRows($sheetRows, $mode);
            } catch (Exception $e) {
                $lastException = $e;
            }
        }

        throw $lastException ?: new Exception("Gagal memproses data dari lembar kerja Excel.");
    }

    /**
     * Import HTML Table (.xls / .xlsx exported from Web Applications)
     */
    public function importHtmlTable(string $filePath, string $mode = 'replace'): array
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
                    $rowData[] = trim($cell->textContent);
                }
                if (!empty($rowData)) {
                    $rows[] = $rowData;
                }
            }
        }

        return $this->processExtractedRows($rows, $mode);
    }

    /**
     * Import CSV File with auto-delimiter detection across multiple sample lines
     */
    public function importCsv(string $filePath, string $mode = 'replace'): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception("Gagal membaca file CSV.");
        }

        $sampleContent = '';
        for ($k = 0; $k < 10 && !feof($handle); $k++) {
            $sampleContent .= fgets($handle);
        }
        rewind($handle);

        $semicolonCount = substr_count($sampleContent, ';');
        $commaCount = substr_count($sampleContent, ',');
        $tabCount = substr_count($sampleContent, "\t");

        $delimiter = ',';
        if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
            $delimiter = ';';
        } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
            $delimiter = "\t";
        }

        $rows = [];
        $bom = "\xEF\xBB\xBF";
        $isFirst = true;

        while (($row = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
            if ($isFirst && !empty($row)) {
                $row[0] = preg_replace("/^$bom/", '', $row[0]);
                $isFirst = false;
            }
            if (!empty(array_filter($row, function ($v) { return trim($v) !== ''; }))) {
                $rows[] = $row;
            }
        }
        fclose($handle);

        return $this->processExtractedRows($rows, $mode);
    }

    /**
     * Process Extracted Rows into Database Table: cluster_revenues
     */
    private function processExtractedRows(array $rows, string $mode = 'replace'): array
    {
        if (empty($rows)) {
            throw new Exception("Tidak ada baris data yang ditemukan dalam berkas.");
        }

        // 1. Detect Header Row Dynamically
        $headerIndex = $this->findHeaderRowIndex($rows);
        if ($headerIndex === -1) {
            $headerIndex = 0;
        }

        // 2. Build Combined Header (Handling 2-Row Stacked Headers)
        $header = $this->buildCombinedHeader($rows, $headerIndex);

        $insertedCount = 0;
        $updatedCount = 0;

        // Persistent variables for forward filling merged Excel cells
        $lastCluster = '';
        $lastPeriodMonth = 'Agustus 2026';
        $lastPeriodYear = intval(date('Y'));

        $startIndex = $headerIndex + 1;
        if (isset($rows[$headerIndex + 1]) && $this->isSubHeaderRow($rows[$headerIndex + 1])) {
            $startIndex = $headerIndex + 2;
        }

        DB::beginTransaction();
        try {
            if ($mode === 'replace') {
                \App\Models\ClusterRevenue::query()->delete();
            }

            for ($i = $startIndex; $i < count($rows); $i++) {
                $row = $rows[$i];

                $nonEmpty = array_filter($row, function ($v) { return trim($v) !== ''; });
                if (empty($nonEmpty)) {
                    continue;
                }

                $data = $this->mapRowData($header, $row);

                if ($this->isSummaryRow($row, $data)) {
                    continue;
                }

                // Forward fill merged Excel cells
                if (!empty($data['cluster_name'])) {
                    $lastCluster = $data['cluster_name'];
                } else {
                    $data['cluster_name'] = $lastCluster;
                }

                if (!empty($data['period_month']) && $data['period_month'] !== 'Agustus 2026') {
                    $lastPeriodMonth = $data['period_month'];
                } else {
                    $data['period_month'] = $lastPeriodMonth;
                }

                if (empty($data['cluster_name']) && !empty($data['kabupaten'])) {
                    $data['cluster_name'] = \App\Http\Controllers\RevenueDataController::resolveClusterFromKabupaten($data['kabupaten']);
                }

                $normalizedKab = self::normalizeKabupatenName($data['kabupaten']);
                if (empty($normalizedKab) && !empty($data['cluster_name'])) {
                    $normalizedKab = \App\Http\Controllers\RevenueDataController::resolveDefaultKabupaten($data['cluster_name']);
                }

                $normalizedCluster = strtoupper(trim($data['cluster_name']));
                if (empty($normalizedCluster) && !empty($normalizedKab)) {
                    $normalizedCluster = \App\Http\Controllers\RevenueDataController::resolveClusterFromKabupaten($normalizedKab);
                }

                if (empty($normalizedKab)) {
                    continue;
                }

                $data['kabupaten'] = $normalizedKab;
                $data['cluster_name'] = $normalizedCluster;

                $payload = [
                    'cluster_name' => $data['cluster_name'],
                    'kabupaten' => $data['kabupaten'],
                    'period_month' => $data['period_month'],
                    'period_year' => $data['period_year'],
                    'target_revenue_all' => $data['target_revenue_all'],
                    'mtd_revenue_all' => $data['mtd_revenue_all'],
                    'target_broadband' => $data['target_broadband'],
                    'mtd_broadband' => $data['mtd_broadband'],
                    'target_redeem' => $data['target_redeem'],
                    'mtd_redeem' => $data['mtd_redeem'],
                    'target_rgb' => $data['target_rgb'],
                    'mtd_rgb' => $data['mtd_rgb'],
                    'revenue_last_month' => $data['revenue_last_month'],
                    'revenue_current_month' => $data['revenue_current_month'],
                    'notes' => $data['notes'],
                ];

                $periodMonth = $data['period_month'] ?: 'Agustus 2026';
                $periodYear = intval($data['period_year']) ?: 2026;

                // Persist to ClusterRevenue table (revenue_data)
                if ($mode === 'replace') {
                    $existingRecord = \App\Models\ClusterRevenue::whereRaw('UPPER(TRIM(kabupaten)) = ?', [$normalizedKab])
                        ->where('period_month', $periodMonth)
                        ->where('period_year', $periodYear)
                        ->first();

                    if ($existingRecord) {
                        $existingRecord->update($payload);
                        $updatedCount++;
                    } else {
                        \App\Models\ClusterRevenue::create($payload);
                        $insertedCount++;
                    }
                } else {
                    $existingRecord = \App\Models\ClusterRevenue::whereRaw('UPPER(TRIM(kabupaten)) = ?', [$normalizedKab])
                        ->where('period_month', $periodMonth)
                        ->where('period_year', $periodYear)
                        ->first();

                    if (!$existingRecord && in_array($normalizedKab, ['KOTA MATARAM', 'KOTA DENPASAR'])) {
                        $stripped = trim(substr($normalizedKab, 5));
                        $existingRecord = \App\Models\ClusterRevenue::whereRaw('UPPER(TRIM(kabupaten)) = ?', [$stripped])
                            ->where('period_month', $periodMonth)
                            ->where('period_year', $periodYear)
                            ->first();
                    }

                    if (!$existingRecord && in_array($normalizedKab, ['MATARAM', 'DENPASAR'])) {
                        $kotaVersion = 'KOTA ' . $normalizedKab;
                        $existingRecord = \App\Models\ClusterRevenue::whereRaw('UPPER(TRIM(kabupaten)) = ?', [$kotaVersion])
                            ->where('period_month', $periodMonth)
                            ->where('period_year', $periodYear)
                            ->first();
                    }

                    if ($existingRecord) {
                        $existingRecord->update($payload);
                        $updatedCount++;
                    } else {
                        \App\Models\ClusterRevenue::create($payload);
                        $insertedCount++;
                    }
                }
            }

            DB::commit();

            if (($insertedCount + $updatedCount) === 0) {
                throw new Exception("Tidak ada data cluster yang dapat dibaca dari file Excel. Pastikan nama kolom di file Excel berisi nama cluster / kabupaten dan angka revenue.");
            }

            return [
                'success' => true,
                'inserted' => $insertedCount,
                'updated' => $updatedCount,
                'total' => $insertedCount + $updatedCount,
                'message' => "Berhasil mengimpor " . ($insertedCount + $updatedCount) . " data cluster_revenues ({$insertedCount} baru, {$updatedCount} diperbarui)."
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Build merged combined header handling stacked Excel header rows (Row 1 + Row 2)
     */
    private function buildCombinedHeader(array $rows, int $headerIndex): array
    {
        $rawHeaderRow = $rows[$headerIndex];
        $nextRow = isset($rows[$headerIndex + 1]) ? $rows[$headerIndex + 1] : [];

        $isNextRowSubHeader = $this->isSubHeaderRow($nextRow);

        $combined = [];
        $maxCols = max(count($rawHeaderRow), count($nextRow));

        $lastCategory = '';

        for ($c = 0; $c < $maxCols; $c++) {
            $topCell = isset($rawHeaderRow[$c]) ? trim($rawHeaderRow[$c]) : '';
            $subCell = ($isNextRowSubHeader && isset($nextRow[$c])) ? trim($nextRow[$c]) : '';

            if (!empty($topCell)) {
                $lastCategory = $topCell;
            } else {
                $topCell = $lastCategory;
            }

            $fullStr = trim($topCell . ' ' . $subCell);
            $clean = strtolower($fullStr);
            $clean = preg_replace('/[^\x20-\x7E]/', '', $clean);

            $combined[$c] = $clean;
        }

        return $combined;
    }

    /**
     * Check if a row is a sub-header row
     */
    private function isSubHeaderRow(array $row): bool
    {
        if (empty($row)) return false;

        $matchCount = 0;
        foreach ($row as $cell) {
            $clean = strtolower(trim((string)$cell));
            if ($clean !== '' && preg_match('/^(target|mtd|achieved|ach|realisasi|actual|status|notes|keterangan|%)%?$/i', $clean)) {
                $matchCount++;
            }
        }

        return $matchCount >= 2;
    }

    /**
     * Check if a row is a summary / total row
     */
    private function isSummaryRow(array $row, array $mappedData): bool
    {
        $rowStr = strtoupper(implode(' ', $row));
        if (preg_match('/\b(TOTAL|GRAND TOTAL|JUMLAH|SUBTOTAL|RATA-RATA|AVERAGE)\b/', $rowStr)) {
            return true;
        }

        return false;
    }

    /**
     * Dynamically locate the row index containing table column headers
     */
    private function findHeaderRowIndex(array $rows): int
    {
        $keywords = [
            'cluster', 'kabupaten', 'kota', 'target', 'achieved', 'realisasi',
            'actual', 'mtd', 'broadband', 'redeem', 'rgb', 'revenue',
            'periode', 'bulan', 'tahun', 'catatan', 'notes', 'status'
        ];

        $bestScore = 0;
        $bestIndex = -1;

        $maxScan = min(15, count($rows));
        for ($r = 0; $r < $maxScan; $r++) {
            $score = 0;
            foreach ($rows[$r] as $cell) {
                $clean = strtolower(trim((string)$cell));
                foreach ($keywords as $kw) {
                    if (str_contains($clean, $kw)) {
                        $score++;
                    }
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestIndex = $r;
            }
        }

        return $bestIndex;
    }

    /**
     * Map header and row values dynamically matching all Excel variations
     */
    private function mapRowData(array $header, array $row): array
    {
        $cluster = '';
        $kabupaten = '';
        $periodMonth = 'Agustus 2026';
        $periodYear = intval(date('Y'));

        $targetAll = 0;
        $mtdAll = 0;
        $targetBb = 0;
        $mtdBb = 0;
        $targetRedeem = 0;
        $mtdRedeem = 0;
        $targetRgb = 0;
        $mtdRgb = 0;
        $lastMonth = 0;
        $currentMonth = 0;
        $notes = '';

        foreach ($row as $index => $value) {
            $col = isset($header[$index]) ? strtolower(trim($header[$index])) : '';
            $val = trim((string)$value);

            if (empty($val) && $val !== '0') {
                continue;
            }

            if (empty($col)) {
                continue;
            }

            // 1. Kabupaten / Kota
            if (preg_match('/(kabupaten|kota|regency|city|cakupan|kab\b)/i', $col)) {
                $kabupaten = self::normalizeKabupatenName($val);
            }
            // 2. Cluster Name / Region
            elseif (preg_match('/(cluster|wilayah|area|nama|new_cluster|region)/i', $col) && !preg_match('/(target|mtd|ach|realisasi|actual)/i', $col)) {
                $cluster = $val;
            }
            // 3. Periode / Month / Year
            elseif (preg_match('/(periode|bulan|month|period)/i', $col) && !preg_match('/(sebelum|sekarang|last|curr|lalu|ini)/i', $col)) {
                $periodMonth = $val ?: 'Agustus 2026';
            } elseif (preg_match('/(tahun|year)/i', $col)) {
                $periodYear = intval($this->cleanNumber($val)) ?: intval(date('Y'));
            }
            // 4. Broadband Target vs MTD (TARGET BB, MTD BB)
            elseif (preg_match('/(broadband|\bbb\b)/i', $col)) {
                if (preg_match('/(target|tgt)/i', $col)) {
                    $targetBb = $this->cleanNumber($val);
                } else {
                    $mtdBb = $this->cleanNumber($val);
                }
            }
            // 5. Redeem PV Target vs MTD (TARGET PV, MTD PV, TARGET REDEEM, MTD REDEEM)
            elseif (preg_match('/(redeem|\bpv\b|reedem|voucher)/i', $col)) {
                if (preg_match('/(target|tgt)/i', $col)) {
                    $targetRedeem = $this->cleanNumber($val);
                } else {
                    $mtdRedeem = $this->cleanNumber($val);
                }
            }
            // 6. RGB Target vs MTD (Target RGB All, MTD RGB All)
            elseif (preg_match('/(rgb)/i', $col)) {
                if (preg_match('/(target|tgt)/i', $col)) {
                    $targetRgb = $this->cleanNumber($val);
                } else {
                    $mtdRgb = $this->cleanNumber($val);
                }
            }
            // 7. Growth Revenue (Data Bln Sebelumnya vs Data Bln Sekarang)
            elseif (preg_match('/(sebelum|last|prev|lalu)/i', $col)) {
                $lastMonth = $this->cleanNumber($val);
            } elseif (preg_match('/(sekarang|curr|bulan_ini|current)/i', $col)) {
                $currentMonth = $this->cleanNumber($val);
            }
            // 8. Revenue ALL Target vs MTD (TARGET REV ALL, MTD REV ALL)
            elseif (preg_match('/(rev.*all|revenue.*all|all)/i', $col)) {
                if (preg_match('/(target|tgt)/i', $col)) {
                    $targetAll = $this->cleanNumber($val);
                } else {
                    $mtdAll = $this->cleanNumber($val);
                }
            } elseif (preg_match('/(target|tgt)/i', $col)) {
                $targetAll = $this->cleanNumber($val);
            } elseif (preg_match('/(mtd|achieved|ach|realisasi|actual|omzet)/i', $col)) {
                $mtdAll = $this->cleanNumber($val);
            }
            // 9. Notes / Status / Keterangan
            elseif (preg_match('/(note|catatan|keterangan|status)/i', $col)) {
                $notes = $val;
            }
        }

        // Two-way auto-mapping between Cluster and Kabupaten
        if (!empty($kabupaten) && empty($cluster)) {
            $cluster = \App\Http\Controllers\RevenueDataController::resolveClusterFromKabupaten($kabupaten);
        } elseif (!empty($cluster) && empty($kabupaten)) {
            $kabupaten = \App\Http\Controllers\RevenueDataController::resolveDefaultKabupaten($cluster);
        }

        // Auto sum if needed
        if ($targetAll <= 0 && ($targetBb > 0 || $targetRedeem > 0)) {
            $targetAll = $targetBb + $targetRedeem;
        }
        if ($mtdAll <= 0 && ($mtdBb > 0 || $mtdRedeem > 0)) {
            $mtdAll = $mtdBb + $mtdRedeem;
        }
        if ($currentMonth <= 0 && $mtdAll > 0) {
            $currentMonth = $mtdAll;
        }

        return [
            'cluster_name' => $cluster ?: 'BALI BARAT',
            'kabupaten' => $kabupaten ?: 'BULELENG',
            'period_month' => $periodMonth,
            'period_year' => $periodYear,
            'target_revenue_all' => $targetAll,
            'mtd_revenue_all' => $mtdAll,
            'target_broadband' => $targetBb,
            'mtd_broadband' => $mtdBb,
            'target_redeem' => $targetRedeem,
            'mtd_redeem' => $mtdRedeem,
            'target_rgb' => $targetRgb,
            'mtd_rgb' => $mtdRgb,
            'revenue_last_month' => $lastMonth,
            'revenue_current_month' => $currentMonth,
            'notes' => $notes,
        ];
    }

    private function columnLetterToIndex(string $letters): int
    {
        $letters = strtoupper(trim($letters));
        $index = 0;
        $len = strlen($letters);
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }
        return max(0, $index - 1);
    }

    /**
     * Normalize Kabupaten / Kota name into standard canonical form
     */
    public static function normalizeKabupatenName(string $name): string
    {
        if (empty($name)) return '';

        // Replace non-breaking spaces (\xC2\xA0 / &nbsp;) & multiple whitespace with single space
        $clean = preg_replace('/[\s\x{00a0}]+/u', ' ', $name);
        $clean = strtoupper(trim($clean));
        if (empty($clean)) return '';

        // Standardize punctuation like dots, dashes, parentheses
        $clean = str_replace(['KAB.', 'KABUPATEN', 'KOTA.', 'KOTA '], ['KAB ', 'KABUPATEN ', 'KOTA ', 'KOTA '], $clean);
        $clean = preg_replace('/[\(\)]/', ' ', $clean);
        $clean = preg_replace('/\s+/', ' ', $clean);
        $clean = trim($clean);

        // Match Kota Bima vs Kabupaten Bima variations
        if (in_array($clean, ['KOTA BIMA', 'BIMA KOTA', 'KOTA BIMA NTP', 'BIMA KOTA NTP'])) {
            return 'KOTA BIMA';
        }
        if (in_array($clean, ['BIMA', 'KAB BIMA', 'KABUPATEN BIMA', 'BIMA KAB', 'BIMA KABUPATEN', 'BIMA NTP', 'KABUPATEN BIMA NTP'])) {
            return 'BIMA';
        }

        // Match Kota Mataram, Kota Kupang, Kota Denpasar
        if (in_array($clean, ['MATARAM', 'KOTA MATARAM', 'KOTA MATARAM NTP', 'MATARAM KOTA'])) return 'KOTA MATARAM';
        if (in_array($clean, ['KUPANG KOTA', 'KOTA KUPANG', 'KOTA KUPANG NTT'])) return 'KOTA KUPANG';
        if (in_array($clean, ['DENPASAR', 'KOTA DENPASAR', 'DENPASAR KOTA'])) return 'KOTA DENPASAR';
        if (in_array($clean, ['KUPANG', 'KAB KUPANG', 'KABUPATEN KUPANG', 'KUPANG KAB'])) return 'KUPANG';

        // General fallback: strip prefixes KAB, KABUPATEN
        $stripped = trim(preg_replace('/^(KAB|KABUPATEN)\s+/i', '', $clean));
        if ($stripped === 'MATARAM') return 'KOTA MATARAM';
        if ($stripped === 'DENPASAR') return 'KOTA DENPASAR';
        if ($stripped === 'BIMA') return 'BIMA';
        if (in_array($stripped, ['KARANGASEM', 'KARANG ASEM'])) return 'KARANG ASEM';

        return $stripped;
    }

    /**
     * Clean numeric value supporting all currency symbols and number formats
     */
    public function cleanNumber($val): float
    {
        if ($val === null || $val === '' || $val === false) return 0.0;
        if (is_numeric($val)) return floatval($val);

        $val = trim((string)$val);
        $val = str_ireplace(['Rp', 'IDR', 'Rp.', '$', '€', ' '], '', $val);

        // Scientific Notation check (e.g. 4.25E+09 or 4,25E+09)
        $sciTest = str_replace(',', '.', $val);
        if (preg_match('/^-?[0-9.]+[eE][+-]?[0-9]+$/', $sciTest)) {
            return floatval($sciTest);
        }

        // Text multiplier units (Miliar, Juta, Ribu)
        if (preg_match('/([0-9.,]+)\s*(miliar|m\b)/i', $val, $matches)) {
            $num = floatval(str_replace(',', '.', str_replace('.', '', $matches[1])));
            return $num * 1000000000;
        }
        if (preg_match('/([0-9.,]+)\s*(juta|jt\b)/i', $val, $matches)) {
            $num = floatval(str_replace(',', '.', str_replace('.', '', $matches[1])));
            return $num * 1000000;
        }
        if (preg_match('/([0-9.,]+)\s*(ribu|k\b)/i', $val, $matches)) {
            $num = floatval(str_replace(',', '.', str_replace('.', '', $matches[1])));
            return $num * 1000;
        }

        // Format: 1.000.000,50 (Indonesian format with dots as thousands separator)
        if (substr_count($val, '.') > 1) {
            $val = str_replace('.', '', $val);
            $val = str_replace(',', '.', $val);
        } 
        // Format: 1,000,000.50 (English format with commas as thousands separator)
        elseif (substr_count($val, ',') > 1) {
            $val = str_replace(',', '', $val);
        }
        // Mixed: 1.000.000,50 vs 1,000,000.50
        elseif (substr_count($val, '.') === 1 && substr_count($val, ',') === 1) {
            $dotPos = strpos($val, '.');
            $commaPos = strpos($val, ',');
            if ($dotPos < $commaPos) {
                $val = str_replace('.', '', $val);
                $val = str_replace(',', '.', $val);
            } else {
                $val = str_replace(',', '', $val);
            }
        } 
        // Single comma: 1000,50 -> 1000.50
        elseif (substr_count($val, ',') === 1 && substr_count($val, '.') === 0) {
            $val = str_replace(',', '.', $val);
        }
        // Single dot: 1000.50 or 1.000
        elseif (substr_count($val, '.') === 1 && substr_count($val, ',') === 0) {
            $parts = explode('.', $val);
            if (strlen(end($parts)) === 3 && strlen($parts[0]) <= 3) {
                $val = str_replace('.', '', $val);
            }
        }

        return floatval(preg_replace('/[^0-9.-]/', '', $val));
    }

    public function cleanPercent($val): float
    {
        $val = str_replace(['%', '+', ' '], '', (string)$val);
        $val = str_replace(',', '.', $val);
        return floatval($val);
    }

    /**
     * Generate template CSV content with separate Cluster and Kabupaten columns
     */
    public function generateTemplateCsv(): string
    {
        $fp = fopen('php://memory', 'r+');
        fputs($fp, "\xEF\xBB\xBF");

        $data = [
            ['Cluster', 'Kabupaten / Kota', 'Periode Bulan', 'Tahun', 'Target Revenue All', 'MTD Revenue All', 'Target Broadband', 'MTD Broadband', 'Target Redeem', 'MTD Redeem', 'Target RGB', 'MTD RGB', 'Data Bln Sebelumnya', 'Data Bulan Sekarang', 'Catatan'],
            // BALI BARAT
            ['BALI BARAT', 'BULELENG', 'Agustus 2026', 2026, 4250000000, 4180000000, 3870000000, 3810000000, 245000000, 228000000, 135000000, 142000000, 4100000000, 4180000000, 'Optimal'],
            ['BALI BARAT', 'JEMBRANA', 'Agustus 2026', 2026, 2150000000, 2110000000, 1960000000, 1930000000, 125000000, 116000000, 65000000, 64000000, 2080000000, 2110000000, 'Optimal'],
            ['BALI BARAT', 'TABANAN', 'Agustus 2026', 2026, 3659687985, 3562472574, 3346966637, 3257005991, 213918822, 194592500, 98700000, 110884083, 3500000000, 3562472574, 'Perlu Perhatian'],
            // BALI TENGAH
            ['BALI TENGAH', 'BADUNG', 'Agustus 2026', 2026, 22500000000, 22350000000, 20000000000, 19950000000, 1270000000, 1200000000, 1230000000, 1200000000, 21800000000, 22350000000, 'Melampaui Target'],
            ['BALI TENGAH', 'KOTA DENPASAR', 'Agustus 2026', 2026, 22395936544, 22168450756, 19961726301, 19883265180, 1267080024, 1196144504, 1167080024, 1089035072, 21500000000, 22168450756, 'Melampaui Target'],
        ];

        foreach ($data as $row) {
            fputcsv($fp, $row, ';');
        }

        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);

        return $csv;
    }
}
