<?php

namespace App\Services;

use App\Models\KelasKpi;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class KelasKpiImportService
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
     * Clean numeric string (handles Indonesian 1.000.000,50 and US 1,000,000.50)
     */
    private function cleanNumeric($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $str = trim((string) $value);
        $str = preg_replace('/[^\d,\.\-]/', '', $str);

        if (empty($str)) {
            return 0.0;
        }

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

        return (float) $str;
    }

    /**
     * Normalize header string for matching
     */
    private function normalizeHeader(string $h): string
    {
        $h = strtolower(trim($h));
        $h = preg_replace('/[^a-z0-9]/', '', $h);
        return $h;
    }

    /**
     * Parse and import uploaded CSV or Excel file
     */
    public function importFile(UploadedFile $file, string $mode = 'append'): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filePath = $file->getRealPath();
        $rawRows = [];

        if (in_array($extension, ['xlsx', 'xlsm'])) {
            $rawRows = $this->parseXlsx($filePath);
        } elseif (in_array($extension, ['csv', 'txt'])) {
            $rawRows = $this->parseCsv($filePath);
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

        return $this->processParsedRows($rawRows, $mode);
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

        $rows = [];
        foreach ($sheetEntries as $sheetEntry) {
            $sheetXML = $zip->getFromName($sheetEntry);
            if ($sheetXML === false) continue;

            $xml = @simplexml_load_string($sheetXML);
            if (!$xml || !isset($xml->sheetData->row)) continue;

            foreach ($xml->sheetData->row as $r) {
                $rowData = [];
                $maxCol = 0;
                foreach ($r->c as $c) {
                    $rRef = (string) $c['r'];
                    preg_match('/([A-Z]+)(\d+)/', $rRef, $matches);
                    $colLetters = $matches[1] ?? 'A';
                    
                    $colIdx = 0;
                    $len = strlen($colLetters);
                    for ($k = 0; $k < $len; $k++) {
                        $colIdx = $colIdx * 26 + (ord($colLetters[$k]) - ord('A') + 1);
                    }
                    $colIdx -= 1;
                    if ($colIdx > $maxCol) $maxCol = $colIdx;

                    $cellType = (string) $c['t'];
                    $val = (string) $c->v;

                    if ($cellType === 's' && isset($sharedStrings[(int) $val])) {
                        $cellVal = $sharedStrings[(int) $val];
                    } else {
                        $cellVal = $val;
                    }

                    $rowData[$colIdx] = $this->cleanUtf8((string) $cellVal);
                }

                $fullRow = [];
                for ($col = 0; $col <= $maxCol; $col++) {
                    $fullRow[$col] = $rowData[$col] ?? '';
                }

                if (!empty(array_filter($fullRow, fn($v) => trim($v) !== ''))) {
                    $rows[] = $fullRow;
                }
            }
        }

        $zip->close();
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
                $cleanedRow = array_map(fn($v) => $this->cleanUtf8((string) $v), $data);
                if (!empty(array_filter($cleanedRow, fn($v) => trim($v) !== ''))) {
                    $rows[] = $cleanedRow;
                }
            }
            fclose($handle);
        }

        return $rows;
    }

    /**
     * Process parsed row matrix into kelas_kpis database records
     */
    private function processParsedRows(array $rows, string $mode = 'append'): array
    {
        $headerRowIndex = 0;
        $headerMap = [];

        // Find header row containing 'cluster'
        foreach ($rows as $idx => $r) {
            $rowStr = strtolower(implode(' ', $r));
            if (str_contains($rowStr, 'cluster') || str_contains($rowStr, 'target') || str_contains($rowStr, 'omzet')) {
                $headerRowIndex = $idx;
                break;
            }
        }

        $headerRow = $rows[$headerRowIndex];
        foreach ($headerRow as $colIdx => $colText) {
            $norm = $this->normalizeHeader((string) $colText);
            
            if ($norm === 'cluster' || $norm === 'newcluster' || $norm === 'namacluster') {
                $headerMap['cluster'] = $colIdx;
            } elseif ($norm === 'targetrevall' || $norm === 'targetrevenueall') {
                $headerMap['target_rev_all'] = $colIdx;
            } elseif ($norm === 'actualrevall' || $norm === 'actualrevenueall' || $norm === 'achieved') {
                $headerMap['actual_rev_all'] = $colIdx;
            } elseif ($norm === 'targetrevbb' || $norm === 'targetbroadband') {
                $headerMap['target_rev_bb'] = $colIdx;
            } elseif ($norm === 'actualrevbb' || $norm === 'actualbroadband') {
                $headerMap['actual_rev_bb'] = $colIdx;
            } elseif ($norm === 'targetrevpv' || $norm === 'targetpv') {
                $headerMap['target_rev_pv'] = $colIdx;
            } elseif ($norm === 'actualrevpv' || $norm === 'actualpv') {
                $headerMap['actual_rev_pv'] = $colIdx;
            } elseif ($norm === 'targetrgb') {
                $headerMap['target_rgb'] = $colIdx;
            } elseif ($norm === 'actualrgb') {
                $headerMap['actual_rgb'] = $colIdx;
            } elseif ($norm === 'omzetrevm1' || $norm === 'omzetm1') {
                $headerMap['omzet_rev_m1'] = $colIdx;
            } elseif ($norm === 'mtdm1') {
                $headerMap['mtd_m1'] = $colIdx;
            } elseif ($norm === 'tgt3' || $norm === 'tgt3percent' || $norm === 'tgt3pct') {
                $headerMap['tgt_3_percent'] = $colIdx;
            } elseif ($norm === 'mtd') {
                $headerMap['mtd'] = $colIdx;
            } elseif ($norm === 'growth') {
                $headerMap['growth'] = $colIdx;
            } elseif ($norm === 'growthtgt' || $norm === 'growthtarget') {
                $headerMap['growth_tgt'] = $colIdx;
            } elseif ($norm === 'revgrowthach' || $norm === 'revgrowthachpercent' || $norm === 'revgrowthachpct') {
                $headerMap['rev_growth_ach_percent'] = $colIdx;
            } elseif ($norm === 'outletpjp') {
                $headerMap['outlet_pjp'] = $colIdx;
            } elseif ($norm === 'outletpjpgrowth') {
                $headerMap['outlet_pjp_growth'] = $colIdx;
            } elseif ($norm === 'ratiooutletpjp') {
                $headerMap['ratio_outlet_pjp'] = $colIdx;
            } elseif ($norm === 'omzetoutletach') {
                if (!isset($headerMap['omzet_outlet_ach'])) {
                    $headerMap['omzet_outlet_ach'] = $colIdx;
                } else {
                    $headerMap['omzet_outlet_ach_score'] = $colIdx;
                }
            } elseif ($norm === 'weight' || $norm === 'bobot') {
                $headerMap['weight'] = $colIdx;
            } elseif ($norm === 'finalscore' || $norm === 'totalscore' || $norm === 'score') {
                $headerMap['final_score'] = $colIdx;
            } elseif ($norm === 'class' || $norm === 'kelas') {
                $headerMap['class'] = $colIdx;
            } elseif ($norm === 'type' || $norm === 'tipe') {
                $headerMap['type'] = $colIdx;
            } elseif ($norm === 'region' || $norm === 'wilayah') {
                $headerMap['region'] = $colIdx;
            } elseif ($norm === 'periode' || $norm === 'period' || $norm === 'bulan' || $norm === 'month') {
                $headerMap['periode'] = $colIdx;
            }
        }

        // Fallback default index positioning if headers are not matched by names
        if (!isset($headerMap['cluster'])) $headerMap['cluster'] = 0;

        $dataRows = array_slice($rows, $headerRowIndex + 1);
        $totalProcessed = 0;
        $insertedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        $existingClusterMap = [];
        if ($mode === 'append') {
            $existingClusterMap = KelasKpi::all()->keyBy(function ($r) {
                $c = strtoupper(trim($r->cluster ?: $r->new_cluster));
                $p = trim($r->periode ?: '2026-08');
                return $c . '|' . $p;
            })->toArray();
        }

        DB::beginTransaction();
        try {
            if ($mode === 'replace') {
                KelasKpi::query()->delete();
            }

            foreach ($dataRows as $row) {
                $clusterName = strtoupper(trim((string) ($row[$headerMap['cluster']] ?? '')));
                if (empty($clusterName) || $clusterName === 'CLUSTER' || $clusterName === 'NEW CLUSTER') {
                    $skippedCount++;
                    continue;
                }

                $targetRevAll = isset($headerMap['target_rev_all']) ? $this->cleanNumeric((string)$row[$headerMap['target_rev_all']]) : 0;
                $actualRevAll = isset($headerMap['actual_rev_all']) ? $this->cleanNumeric((string)$row[$headerMap['actual_rev_all']]) : 0;
                $targetRevBb = isset($headerMap['target_rev_bb']) ? $this->cleanNumeric((string)$row[$headerMap['target_rev_bb']]) : 0;
                $actualRevBb = isset($headerMap['actual_rev_bb']) ? $this->cleanNumeric((string)$row[$headerMap['actual_rev_bb']]) : 0;
                $targetRevPv = isset($headerMap['target_rev_pv']) ? $this->cleanNumeric((string)$row[$headerMap['target_rev_pv']]) : 0;
                $actualRevPv = isset($headerMap['actual_rev_pv']) ? $this->cleanNumeric((string)$row[$headerMap['actual_rev_pv']]) : 0;
                $targetRgb = isset($headerMap['target_rgb']) ? $this->cleanNumeric((string)$row[$headerMap['target_rgb']]) : 0;
                $actualRgb = isset($headerMap['actual_rgb']) ? $this->cleanNumeric((string)$row[$headerMap['actual_rgb']]) : 0;
                $omzetRevM1 = isset($headerMap['omzet_rev_m1']) ? $this->cleanNumeric((string)$row[$headerMap['omzet_rev_m1']]) : 0;
                $mtdM1 = isset($headerMap['mtd_m1']) ? $this->cleanNumeric((string)$row[$headerMap['mtd_m1']]) : 0;
                $tgt3Percent = isset($headerMap['tgt_3_percent']) ? $this->cleanNumeric((string)$row[$headerMap['tgt_3_percent']]) : 0;
                $mtd = isset($headerMap['mtd']) ? $this->cleanNumeric((string)$row[$headerMap['mtd']]) : $actualRevAll;
                $growth = isset($headerMap['growth']) ? $this->cleanNumeric((string)$row[$headerMap['growth']]) : 0;
                $growthTgt = isset($headerMap['growth_tgt']) ? $this->cleanNumeric((string)$row[$headerMap['growth_tgt']]) : 3.0;
                $revGrowthAchPercent = isset($headerMap['rev_growth_ach_percent']) ? $this->cleanNumeric((string)$row[$headerMap['rev_growth_ach_percent']]) : ($targetRevAll > 0 ? round(($actualRevAll / $targetRevAll) * 100, 1) : 0);
                $outletPjp = isset($headerMap['outlet_pjp']) ? $this->cleanNumeric((string)$row[$headerMap['outlet_pjp']]) : 0;
                $outletPjpGrowth = isset($headerMap['outlet_pjp_growth']) ? $this->cleanNumeric((string)$row[$headerMap['outlet_pjp_growth']]) : 0;
                $ratioOutletPjp = isset($headerMap['ratio_outlet_pjp']) ? $this->cleanNumeric((string)$row[$headerMap['ratio_outlet_pjp']]) : 0;
                $omzetOutletAch = isset($headerMap['omzet_outlet_ach']) ? $this->cleanNumeric((string)$row[$headerMap['omzet_outlet_ach']]) : 0;
                $weight = isset($headerMap['weight']) ? $this->cleanNumeric((string)$row[$headerMap['weight']]) : 15.0;
                $omzetOutletAchScore = isset($headerMap['omzet_outlet_ach_score']) ? $this->cleanNumeric((string)$row[$headerMap['omzet_outlet_ach_score']]) : 0;
                $finalScore = isset($headerMap['final_score']) ? $this->cleanNumeric((string)$row[$headerMap['final_score']]) : 2.0;

                $periode = isset($headerMap['periode']) && !empty($row[$headerMap['periode']]) ? trim((string)$row[$headerMap['periode']]) : '2026-08';

                $payload = [
                    'region' => isset($headerMap['region']) ? strtoupper(trim((string)$row[$headerMap['region']])) : 'BALI NUSRA',
                    'cluster' => $clusterName,
                    'new_cluster' => $clusterName,
                    'periode' => $periode ?: '2026-08',
                    'target_rev_all' => $targetRevAll,
                    'actual_rev_all' => $actualRevAll,
                    'target_rev_bb' => $targetRevBb,
                    'actual_rev_bb' => $actualRevBb,
                    'target_rev_pv' => $targetRevPv,
                    'actual_rev_pv' => $actualRevPv,
                    'target_rgb' => $targetRgb,
                    'actual_rgb' => $actualRgb,
                    'omzet_rev_m1' => $omzetRevM1,
                    'mtd_m1' => $mtdM1,
                    'mtd' => $mtd,
                    'outlet_pjp' => $outletPjp,
                    'outlet_pjp_growth' => $outletPjpGrowth,
                ];

                if (isset($headerMap['tgt_3_percent'])) $payload['tgt_3_percent'] = $tgt3Percent;
                if (isset($headerMap['growth'])) $payload['growth'] = $growth;
                if (isset($headerMap['growth_tgt'])) $payload['growth_tgt'] = $growthTgt;
                if (isset($headerMap['rev_growth_ach_percent'])) $payload['rev_growth_ach_percent'] = $revGrowthAchPercent;
                if (isset($headerMap['ratio_outlet_pjp'])) $payload['ratio_outlet_pjp'] = $ratioOutletPjp;
                if (isset($headerMap['omzet_outlet_ach'])) $payload['omzet_outlet_ach'] = $omzetOutletAch;
                if (isset($headerMap['weight'])) $payload['weight'] = $weight;
                if (isset($headerMap['omzet_outlet_ach_score'])) $payload['omzet_outlet_ach_score'] = $omzetOutletAchScore;
                if (isset($headerMap['final_score'])) $payload['final_score'] = $finalScore;
                if (isset($headerMap['class']) && !empty($row[$headerMap['class']])) $payload['class'] = strtoupper(trim((string)$row[$headerMap['class']]));
                if (isset($headerMap['type']) && !empty($row[$headerMap['type']])) $payload['type'] = strtoupper(trim((string)$row[$headerMap['type']]));

                $matchKey = $clusterName . '|' . ($periode ?: '2026-08');

                if ($mode === 'append' && isset($existingClusterMap[$matchKey])) {
                    $existingId = $existingClusterMap[$matchKey]['id'];
                    $kpiModel = KelasKpi::find($existingId);
                    if ($kpiModel) {
                        $kpiModel->fill($payload);
                        $kpiModel->save();
                    } else {
                        KelasKpi::create($payload);
                    }
                    $updatedCount++;
                } else {
                    KelasKpi::create($payload);
                    $insertedCount++;
                }

                $totalProcessed++;
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
            throw new Exception("Gagal menyimpan data KPI ke database: " . $e->getMessage());
        }
    }
}
