<?php

namespace App\Services;

use App\Models\GrowthRevenue;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Exception;
use ZipArchive;

class GrowthRevenueImportService
{
    /**
     * Parse and import CSV / Excel file into growth_revenues table
     */
    public function importFile(UploadedFile $file, bool $replaceExisting = true): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filePath = $file->getRealPath();

        if (in_array($extension, ['xlsx', 'xlsm'])) {
            return $this->importXlsx($filePath, $replaceExisting);
        } elseif (in_array($extension, ['csv', 'txt'])) {
            return $this->importCsv($filePath, $replaceExisting);
        } elseif (in_array($extension, ['xls'])) {
            try {
                return $this->importXlsx($filePath, $replaceExisting);
            } catch (Exception $e) {
                return $this->importCsv($filePath, $replaceExisting);
            }
        } else {
            try {
                return $this->importXlsx($filePath, $replaceExisting);
            } catch (Exception $e) {
                return $this->importCsv($filePath, $replaceExisting);
            }
        }
    }

    /**
     * Import Native XLSX for Growth Revenue
     */
    public function importXlsx(string $filePath, bool $replaceExisting = true): array
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception("Ekstensi ZipArchive PHP belum aktif.");
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception("Gagal membuka file Excel (.xlsx).");
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

        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXmlContent === false) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (preg_match('#xl/worksheets/sheet\d+\.xml#i', $entry)) {
                    $sheetXmlContent = $zip->getFromName($entry);
                    break;
                }
            }
        }
        $zip->close();

        if ($sheetXmlContent === false) {
            throw new Exception("Tidak dapat menemukan lembar kerja di dalam file Excel.");
        }

        $sheetXml = simplexml_load_string($sheetXmlContent);
        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            throw new Exception("Lembar kerja Excel kosong.");
        }

        $rows = [];
        foreach ($sheetXml->sheetData->row as $row) {
            $rowData = [];
            foreach ($row->c as $cell) {
                $cellRef = (string)$cell['r'];
                $colLetter = preg_replace('/[0-9]/', '', $cellRef);
                $colIndex = $this->colLetterToIndex($colLetter);

                $cellType = (string)$cell['t'];
                $cellValue = (string)$cell->v;

                if ($cellType === 's' && isset($sharedStrings[(int)$cellValue])) {
                    $val = $sharedStrings[(int)$cellValue];
                } elseif ($cellType === 'inlineStr' && isset($cell->is->t)) {
                    $val = (string)$cell->is->t;
                } else {
                    $val = $cellValue;
                }

                $rowData[$colIndex] = trim($val);
            }

            if (!empty($rowData)) {
                $maxIndex = max(array_keys($rowData));
                $fullRow = [];
                for ($i = 0; $i <= $maxIndex; $i++) {
                    $fullRow[$i] = $rowData[$i] ?? '';
                }
                $rows[] = $fullRow;
            }
        }

        return $this->processParsedRows($rows, $replaceExisting);
    }

    /**
     * Import CSV File
     */
    public function importCsv(string $filePath, bool $replaceExisting = true): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception("Gagal membaca file CSV.");
        }

        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = ',';
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
            $delimiter = "\t";
        }

        $rows = [];
        while (($data = fgetcsv($handle, 2000, $delimiter)) !== false) {
            $rows[] = array_map('trim', $data);
        }
        fclose($handle);

        return $this->processParsedRows($rows, $replaceExisting);
    }

    /**
     * Convert Excel column letter to 0-based index
     */
    private function colLetterToIndex(string $col): int
    {
        $col = strtoupper($col);
        $index = 0;
        $len = strlen($col);
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($col[$i]) - ord('A') + 1);
        }
        return $index - 1;
    }

    /**
     * Process extracted row data into growth_revenues table
     */
    private function processParsedRows(array $rows, bool $replaceExisting = true): array
    {
        if (empty($rows)) {
            throw new Exception("File kosong tidak memiliki data.");
        }

        $headerRowIndex = 0;
        $headers = [];

        // Locate header row containing cluster/kabupaten/bulan
        foreach ($rows as $idx => $r) {
            $rowStr = strtoupper(implode(' ', $r));
            if (str_contains($rowStr, 'CLUSTER') || str_contains($rowStr, 'KABUPATEN') || str_contains($rowStr, 'BULAN')) {
                $headerRowIndex = $idx;
                $headers = array_map(fn($h) => strtoupper(trim((string)$h)), $r);
                break;
            }
        }

        if (empty($headers)) {
            $headers = array_map(fn($h) => strtoupper(trim((string)$h)), $rows[0]);
            $headerRowIndex = 0;
        }

        // Map Column Positions
        $colCluster = null;
        $colKabupaten = null;
        $colLastMonth = null;
        $colCurrentMonth = null;
        $colPeriod = null;

        foreach ($headers as $idx => $colName) {
            if ($colCluster === null && (str_contains($colName, 'CLUSTER') || str_contains($colName, 'AREA'))) {
                $colCluster = $idx;
            } elseif ($colKabupaten === null && (str_contains($colName, 'KAB') || str_contains($colName, 'KOTA') || str_contains($colName, 'WILAYAH'))) {
                $colKabupaten = $idx;
            } elseif ($colLastMonth === null && (str_contains($colName, 'LALU') || str_contains($colName, 'SEBELUM') || str_contains($colName, 'LAST'))) {
                $colLastMonth = $idx;
            } elseif ($colCurrentMonth === null && (str_contains($colName, 'INI') || str_contains($colName, 'SEKARANG') || str_contains($colName, 'CURRENT') || str_contains($colName, 'MTD'))) {
                $colCurrentMonth = $idx;
            } elseif ($colPeriod === null && (str_contains($colName, 'PERIODE') || str_contains($colName, 'BULAN'))) {
                $colPeriod = $idx;
            }
        }

        // Default fallbacks if header names aren't matched exactly:
        if ($colCluster === null) $colCluster = 0;
        if ($colKabupaten === null) $colKabupaten = 1;
        if ($colLastMonth === null) $colLastMonth = 2;
        if ($colCurrentMonth === null) $colCurrentMonth = 3;

        $records = [];
        $importedCount = 0;

        for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty(array_filter($row))) continue;

            $clusterName = trim($row[$colCluster] ?? '');
            $kabupaten = trim($row[$colKabupaten] ?? '');

            if (empty($clusterName) && empty($kabupaten)) continue;

            if (empty($clusterName) && !empty($kabupaten)) {
                $clusterName = $this->autoClusterFromKabupaten($kabupaten);
            }

            if (empty($kabupaten) && !empty($clusterName)) {
                $kabupaten = $clusterName;
            }

            $rawLast = $row[$colLastMonth] ?? '0';
            $rawCurr = $row[$colCurrentMonth] ?? '0';

            $lastMonth = $this->cleanNumeric($rawLast);
            $currMonth = $this->cleanNumeric($rawCurr);

            $periodMonth = ($colPeriod !== null && isset($row[$colPeriod])) ? trim($row[$colPeriod]) : date('F Y');
            $periodYear = (int)date('Y');

            $growthMoM = ($lastMonth > 0) ? round((($currMonth - $lastMonth) / $lastMonth) * 100, 2) : 0.0;

            $records[] = [
                'cluster_name' => strtoupper($clusterName),
                'kabupaten' => strtoupper($kabupaten),
                'revenue_last_month' => $lastMonth,
                'revenue_current_month' => $currMonth,
                'growth_mom' => $growthMoM,
                'period_month' => $periodMonth,
                'period_year' => $periodYear,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($records)) {
            throw new Exception("Tidak ada baris data valid yang dapat diproses dari file.");
        }

        DB::transaction(function () use ($records, $replaceExisting, &$importedCount) {
            if ($replaceExisting) {
                GrowthRevenue::query()->delete();
            }

            foreach ($records as $rec) {
                GrowthRevenue::create($rec);
                $importedCount++;
            }
        });

        return [
            'success' => true,
            'count' => $importedCount,
            'message' => "Berhasil mengimpor {$importedCount} data grafik pertumbuhan revenue!",
        ];
    }

    /**
     * Clean numeric string (handles Indonesian format 1.000.000,50 and 1,000,000.50)
     */
    private function cleanNumeric($value): float
    {
        if (is_numeric($value)) {
            return (float)$value;
        }

        $str = trim((string)$value);
        $str = preg_replace('/[^\d,\.\-]/', '', $str);

        if (empty($str)) return 0.0;

        if (str_contains($str, '.') && str_contains($str, ',')) {
            if (strrpos($str, ',') > strrpos($str, '.')) {
                $str = str_replace('.', '', $str);
                $str = str_replace(',', '.', $str);
            } else {
                $str = str_replace(',', '', $str);
            }
        } elseif (str_contains($str, ',')) {
            if (strlen(substr($str, strrpos($str, ',') + 1)) <= 2) {
                $str = str_replace(',', '.', $str);
            } else {
                $str = str_replace(',', '', $str);
            }
        }

        return (float)$str;
    }

    /**
     * Auto Cluster from Kabupaten
     */
    private function autoClusterFromKabupaten(string $kab): string
    {
        $k = strtoupper($kab);
        if (str_contains($k, 'BULELENG') || str_contains($k, 'JEMBRANA') || str_contains($k, 'TABANAN')) return 'BALI BARAT';
        if (str_contains($k, 'BADUNG') || str_contains($k, 'DENPASAR')) return 'BALI SELATAN';
        if (str_contains($k, 'BANGLI') || str_contains($k, 'GIANYAR') || str_contains($k, 'KLUNGKUNG') || str_contains($k, 'KARANGASEM')) return 'BALI TIMUR';
        if (str_contains($k, 'MATARAM') || str_contains($k, 'LOMBOK')) return 'MATARAM';
        if (str_contains($k, 'SUMBAWA') || str_contains($k, 'BIMA') || str_contains($k, 'DOMPU')) return 'SUMBAWA';
        if (str_contains($k, 'KUPANG') || str_contains($k, 'ROTE') || str_contains($k, 'SABU') || str_contains($k, 'ALOR')) return 'KUPANG';
        if (str_contains($k, 'FLORES') || str_contains($k, 'ENDE') || str_contains($k, 'SIKKA') || str_contains($k, 'MANGGARAI') || str_contains($k, 'NGADA')) return 'FLORES';
        if (str_contains($k, 'SUMBA')) return 'SUMBA';
        return 'REGIONAL BALI NUSRA';
    }

    /**
     * Generate Template CSV with 4 simplified columns
     */
    public function generateTemplateCsv(): string
    {
        $headers = [
            'CLUSTER',
            'KABUPATEN',
            'PENDAPATAN BULAN LALU (RP)',
            'PENDAPATAN BULAN INI (RP)',
        ];

        $sampleRows = [
            ['BALI BARAT', 'BULELENG', '11500000000', '12200000000'],
            ['BALI BARAT', 'JEMBRANA', '6200000000', '6550000000'],
            ['BALI BARAT', 'TABANAN', '8900000000', '9400000000'],
            ['BALI SELATAN', 'BADUNG', '18500000000', '19800000000'],
            ['BALI SELATAN', 'KOTA DENPASAR', '24000000000', '25600000000'],
            ['BALI TIMUR', 'GIANYAR', '10200000000', '10850000000'],
            ['BALI TIMUR', 'KLUNGKUNG', '4800000000', '5100000000'],
            ['BALI TIMUR', 'BANGLI', '3900000000', '4120000000'],
            ['BALI TIMUR', 'KARANGASEM', '5600000000', '5950000000'],
            ['MATARAM', 'KOTA MATARAM', '14200000000', '15100000000'],
            ['MATARAM', 'LOMBOK BARAT', '7800000000', '8300000000'],
            ['MATARAM', 'LOMBOK TENGAH', '8100000000', '8650000000'],
            ['MATARAM', 'LOMBOK TIMUR', '9200000000', '9800000000'],
            ['MATARAM', 'LOMBOK UTARA', '3400000000', '3620000000'],
            ['SUMBAWA', 'SUMBAWA', '6700000000', '7100000000'],
            ['SUMBAWA', 'SUMBAWA BARAT', '4100000000', '4350000000'],
            ['SUMBAWA', 'DOMPU', '3900000000', '4150000000'],
            ['SUMBAWA', 'BIMA', '5400000000', '5750000000'],
            ['SUMBAWA', 'KOTA BIMA', '4600000000', '4900000000'],
            ['KUPANG', 'KOTA KUPANG', '16500000000', '17600000000'],
            ['KUPANG', 'KUPANG', '6200000000', '6580000000'],
            ['FLORES', 'MANGGARAI BARAT', '7200000000', '7800000000'],
            ['FLORES', 'ENDE', '4800000000', '5120000000'],
            ['FLORES', 'SIKKA', '5300000000', '5650000000'],
            ['SUMBA', 'SUMBA TIMUR', '4200000000', '4450000000'],
            ['SUMBA', 'SUMBA BARAT', '3100000000', '3300000000'],
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);

        foreach ($sampleRows as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
