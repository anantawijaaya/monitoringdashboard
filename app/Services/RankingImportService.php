<?php

namespace App\Services;

use App\Models\ClusterRevenue;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Exception;
use ZipArchive;

class RankingImportService
{
    /**
     * Import CSV / Excel file for Peringkat Revenue
     */
    public function importFile(UploadedFile $file, string $mode = 'append'): array
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
     * Import Native XLSX via ZipArchive + SimpleXML
     */
    public function importXlsx(string $filePath, string $mode = 'append'): array
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception("Ekstensi ZipArchive PHP belum aktif.");
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return $this->importHtmlTable($filePath, $mode);
        }

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

        $sheetEntries = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (preg_match('#xl/worksheets/sheet\d+\.xml#i', $entry)) {
                $sheetEntries[] = $entry;
            }
        }

        if (empty($sheetEntries)) {
            $zip->close();
            throw new Exception("Tidak dapat menemukan worksheet di dalam file Excel.");
        }

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
    public function importHtmlTable(string $filePath, string $mode = 'append'): array
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
     * Import CSV File
     */
    public function importCsv(string $filePath, string $mode = 'append'): array
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
     * Process Extracted Rows into Database Table: revenue_data
     */
    private function processExtractedRows(array $rows, string $mode = 'append'): array
    {
        if (empty($rows)) {
            throw new Exception("Tidak ada baris data yang ditemukan dalam berkas.");
        }

        // 1. Detect Header Row Index
        $headerIndex = $this->findHeaderRowIndex($rows);
        if ($headerIndex === -1) {
            $headerIndex = 0;
        }

        // 2. Build Combined Header
        $header = $this->buildHeaderArray($rows, $headerIndex);

        $insertedCount = 0;
        $updatedCount = 0;

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
                \App\Models\PeringkatData::query()->delete();
            }

            for ($i = $startIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                $nonEmpty = array_filter($row, function ($v) { return trim($v) !== ''; });
                if (empty($nonEmpty)) continue;

                $data = $this->mapRowData($header, $row);

                if ($this->isSummaryRow($row)) continue;

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

                $normalizedKab = \App\Services\RevenueImportService::normalizeKabupatenName($data['kabupaten']);
                if (empty($normalizedKab) && !empty($data['cluster_name'])) {
                    $normalizedKab = \App\Http\Controllers\RevenueDataController::resolveDefaultKabupaten($data['cluster_name']);
                }

                $normalizedCluster = strtoupper(trim($data['cluster_name']));
                if (empty($normalizedCluster) && !empty($normalizedKab)) {
                    $normalizedCluster = \App\Http\Controllers\RevenueDataController::resolveClusterFromKabupaten($normalizedKab);
                }

                if (empty($normalizedKab) && empty($normalizedCluster)) {
                    continue;
                }

                $city = $normalizedKab ?: 'BULELENG';
                $cluster = $normalizedCluster ?: 'BALI BARAT';

                $payload = [
                    'cluster' => $cluster,
                    'city' => $city,
                    'period_month' => $data['period_month'],
                    'period_year' => $data['period_year'],
                    'target_rev_all' => $data['target_revenue_all'],
                    'actual_rev_all' => $data['mtd_revenue_all'],
                    'target_rev_bb' => $data['target_broadband'],
                    'actual_rev_bb' => $data['mtd_broadband'],
                    'target_rev_pv' => $data['target_redeem'],
                    'actual_rev_pv' => $data['mtd_redeem'],
                    'target_rgb' => $data['target_rgb'],
                    'actual_rgb' => $data['mtd_rgb'],
                    'omzet_rev_m1' => $data['omzet_rev_m1'],
                    'mtd_m1' => $data['mtd_m1'],
                    'mtd' => $data['mtd'],
                    'outlet_pjp' => $data['outlet_pjp'],
                    'outlet_pjp_growth' => $data['outlet_pjp_growth'],
                    'notes' => $data['notes'],
                ];

                $periodMonth = $data['period_month'] ?: 'Agustus 2026';
                $periodYear = intval($data['period_year']) ?: 2026;

                if ($mode === 'replace') {
                    \App\Models\PeringkatData::create($payload);
                    $insertedCount++;
                } else {
                    $existingRecord = \App\Models\PeringkatData::whereRaw('UPPER(TRIM(city)) = ?', [$city])
                        ->where('period_month', $periodMonth)
                        ->where('period_year', $periodYear)
                        ->first();

                    if (!$existingRecord && in_array($city, ['KOTA MATARAM', 'KOTA DENPASAR'])) {
                        $stripped = trim(substr($city, 5));
                        $existingRecord = \App\Models\PeringkatData::whereRaw('UPPER(TRIM(city)) = ?', [$stripped])
                            ->where('period_month', $periodMonth)
                            ->where('period_year', $periodYear)
                            ->first();
                    }

                    if ($existingRecord) {
                        $existingRecord->update($payload);
                        $updatedCount++;
                    } else {
                        \App\Models\PeringkatData::create($payload);
                        $insertedCount++;
                    }
                }
            }

            DB::commit();

            if (($insertedCount + $updatedCount) === 0) {
                throw new Exception("Tidak ada data peringkat yang berhasil diimpor. Mohon periksa format berkas Excel/CSV.");
            }

            return [
                'success' => true,
                'inserted' => $insertedCount,
                'updated' => $updatedCount,
                'total_processed' => $insertedCount + $updatedCount,
                'message' => "Berhasil mengimpor " . ($insertedCount + $updatedCount) . " data peringkat revenue."
            ];

        } catch (Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            throw $e;
        }
    }

    private function findHeaderRowIndex(array $rows): int
    {
        $keywords = [
            'city', 'kabupaten', 'kota', 'cluster', 'target rev all', 'actual rev all',
            'target rev bb', 'actual rev bb', 'target rev pv', 'actual rev pv',
            'target rgb', 'actual rgb', 'omzet rev m1', 'mtd m1', 'mtd', 'outlet pjp'
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

    private function buildHeaderArray(array $rows, int $headerIndex): array
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

    private function isSubHeaderRow(array $row): bool
    {
        if (empty($row)) return false;
        $matchCount = 0;
        foreach ($row as $cell) {
            $clean = strtolower(trim((string)$cell));
            if ($clean !== '' && preg_match('/^(target|mtd|actual|ach|growth|score|pjp)%?$/i', $clean)) {
                $matchCount++;
            }
        }
        return $matchCount >= 2;
    }

    private function isSummaryRow(array $row): bool
    {
        $rowStr = strtoupper(implode(' ', $row));
        return (bool) preg_match('/\b(TOTAL|GRAND TOTAL|JUMLAH|SUBTOTAL|RATA-RATA|AVERAGE)\b/', $rowStr);
    }

    private function mapRowData(array $header, array $row): array
    {
        $cluster = '';
        $city = '';
        $periodMonth = 'Agustus 2026';
        $periodYear = intval(date('Y'));

        $targetAll = 0;
        $actualAll = 0;
        $targetBb = 0;
        $actualBb = 0;
        $targetPv = 0;
        $actualPv = 0;
        $targetRgb = 0;
        $actualRgb = 0;
        $omzetRevM1 = 0;
        $mtdM1 = 0;
        $mtd = 0;
        $outletPjp = 0;
        $outletPjpGrowth = 0;
        $notes = '';

        foreach ($row as $index => $value) {
            $col = isset($header[$index]) ? strtolower(trim($header[$index])) : '';
            $val = trim((string)$value);

            if (empty($val) && $val !== '0') continue;
            if (empty($col)) continue;

            // City / Kabupaten
            if (preg_match('/(city|kabupaten|kota|regency|kab\b)/i', $col)) {
                $city = \App\Services\RevenueImportService::normalizeKabupatenName($val);
            }
            // Cluster Name
            elseif (preg_match('/(cluster|wilayah|area|nama_cluster)/i', $col) && !preg_match('/(target|mtd|actual|rev|bb|pv|rgb)/i', $col)) {
                $cluster = $val;
            }
            // Periode / Month
            elseif (preg_match('/(periode|bulan|month|period)/i', $col)) {
                $periodMonth = $val ?: 'Agustus 2026';
            }
            // Rev Broadband
            elseif (preg_match('/(broadband|\bbb\b)/i', $col)) {
                if (preg_match('/(target|tgt)/i', $col)) {
                    $targetBb = $this->cleanNumber($val);
                } else {
                    $actualBb = $this->cleanNumber($val);
                }
            }
            // Rev PV
            elseif (preg_match('/(pv|redeem|reedem)/i', $col)) {
                if (preg_match('/(target|tgt)/i', $col)) {
                    $targetPv = $this->cleanNumber($val);
                } else {
                    $actualPv = $this->cleanNumber($val);
                }
            }
            // Rev RGB
            elseif (preg_match('/(rgb)/i', $col)) {
                if (preg_match('/(target|tgt)/i', $col)) {
                    $targetRgb = $this->cleanNumber($val);
                } else {
                    $actualRgb = $this->cleanNumber($val);
                }
            }
            // Omzet Rev M1 / MTD M1
            elseif (preg_match('/(omzet.*m1|revenue.*m1|m1)/i', $col)) {
                $omzetRevM1 = $this->cleanNumber($val);
            } elseif (preg_match('/(mtd.*m1)/i', $col)) {
                $mtdM1 = $this->cleanNumber($val);
            }
            // MTD (Current Month)
            elseif (preg_match('/^\bmtd\b$/i', $col) || preg_match('/(mtd_current|mtd.*sekarang)/i', $col)) {
                $mtd = $this->cleanNumber($val);
            }
            // Outlet PJP Growth
            elseif (preg_match('/(outlet.*growth|pjp.*growth)/i', $col)) {
                $outletPjpGrowth = $this->cleanNumber($val);
            }
            // Outlet PJP
            elseif (preg_match('/(outlet.*pjp|pjp)/i', $col)) {
                $outletPjp = $this->cleanNumber($val);
            }
            // Rev All (Target & Actual)
            elseif (preg_match('/(rev.*all|revenue.*all|all)/i', $col)) {
                if (preg_match('/(target|tgt)/i', $col)) {
                    $targetAll = $this->cleanNumber($val);
                } else {
                    $actualAll = $this->cleanNumber($val);
                }
            } elseif (preg_match('/(target|tgt)/i', $col)) {
                $targetAll = $this->cleanNumber($val);
            } elseif (preg_match('/(actual|mtd|realisasi)/i', $col)) {
                $actualAll = $this->cleanNumber($val);
            }
        }

        if (empty($actualAll) && !empty($mtd)) {
            $actualAll = $mtd;
        }
        if (empty($mtd) && !empty($actualAll)) {
            $mtd = $actualAll;
        }
        $revLastMonth = $omzetRevM1 ?: $mtdM1;

        return [
            'cluster_name' => $cluster,
            'kabupaten' => $city,
            'period_month' => $periodMonth,
            'period_year' => $periodYear,
            'target_revenue_all' => $targetAll,
            'mtd_revenue_all' => $actualAll,
            'target_broadband' => $targetBb,
            'mtd_broadband' => $actualBb,
            'target_redeem' => $targetPv,
            'mtd_redeem' => $actualPv,
            'target_rgb' => $targetRgb,
            'mtd_rgb' => $actualRgb,
            'revenue_last_month' => $revLastMonth,
            'revenue_current_month' => $actualAll,
            'omzet_rev_m1' => $omzetRevM1,
            'mtd_m1' => $mtdM1,
            'mtd' => $mtd,
            'outlet_pjp' => $outletPjp,
            'outlet_pjp_growth' => $outletPjpGrowth,
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

    private function cleanNumber($val): float
    {
        if ($val === null || $val === '' || $val === false) return 0.0;
        if (is_numeric($val)) return floatval($val);

        $val = trim((string)$val);
        $val = str_ireplace(['Rp', 'IDR', 'Rp.', '$', '€', ' '], '', $val);

        $sciTest = str_replace(',', '.', $val);
        if (preg_match('/^-?[0-9.]+[eE][+-]?[0-9]+$/', $sciTest)) {
            return floatval($sciTest);
        }

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

        if (substr_count($val, '.') > 1) {
            $val = str_replace('.', '', $val);
            $val = str_replace(',', '.', $val);
        } elseif (substr_count($val, ',') > 1) {
            $val = str_replace(',', '', $val);
        } elseif (substr_count($val, '.') === 1 && substr_count($val, ',') === 1) {
            $dotPos = strpos($val, '.');
            $commaPos = strpos($val, ',');
            if ($dotPos < $commaPos) {
                $val = str_replace('.', '', $val);
                $val = str_replace(',', '.', $val);
            } else {
                $val = str_replace(',', '', $val);
            }
        } elseif (substr_count($val, ',') === 1 && substr_count($val, '.') === 0) {
            $val = str_replace(',', '.', $val);
        } elseif (substr_count($val, '.') === 1 && substr_count($val, ',') === 0) {
            $parts = explode('.', $val);
            if (strlen(end($parts)) === 3 && strlen($parts[0]) <= 3) {
                $val = str_replace('.', '', $val);
            }
        }

        return floatval(preg_replace('/[^0-9.-]/', '', $val));
    }
}
