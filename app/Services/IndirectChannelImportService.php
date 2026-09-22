<?php

namespace App\Services;

use App\Models\IndirectChannelAllocation;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class IndirectChannelImportService
{
    private function cleanUtf8(string $str): string
    {
        $cleaned = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
        if ($cleaned === false) {
            $cleaned = mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1');
        }
        return trim($cleaned);
    }

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

    private function normalizeHeader(string $h): string
    {
        $h = strtolower(trim($h));
        $h = str_replace([' ', '_', '-', '/', '\\', '(', ')', '%', '.', ':'], '', $h);
        return $h;
    }

    public function import(UploadedFile $file, string $mode = 'replace'): array
    {
        return $this->importFile($file, $mode);
    }

    public function importFile(UploadedFile $file, string $mode = 'replace'): array
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

        return $this->processExtractedRows($rawRows, $mode);
    }

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

    public function processExtractedRows(array $rows, string $mode = 'replace'): array
    {
        if (empty($rows)) {
            throw new Exception("Data baris kosong.");
        }

        $headerRowIndex = 0;
        $headers = [];

        for ($r = 0; $r < min(10, count($rows)); $r++) {
            $candidate = $rows[$r];
            $normalizedCandidate = array_map([$this, 'normalizeHeader'], $candidate);

            foreach ($normalizedCandidate as $nh) {
                if (
                    str_contains($nh, 'cluster') || 
                    str_contains($nh, 'klaster') || 
                    str_contains($nh, 'total') || 
                    str_contains($nh, 'budget') || 
                    str_contains($nh, 'mitra') ||
                    str_contains($nh, 'digital') ||
                    str_contains($nh, 'cvm') ||
                    str_contains($nh, 'engagement') ||
                    str_contains($nh, 'branding') ||
                    str_contains($nh, 'sales') ||
                    str_contains($nh, 'porgram') ||
                    str_contains($nh, 'voucher')
                ) {
                    $headerRowIndex = $r;
                    $headers = $candidate;
                    break 2;
                }
            }
        }

        if (empty($headers)) {
            $headers = $rows[0];
            $headerRowIndex = 0;
        }

        $map = [
            'cluster' => -1,
            'mitra' => -1,
            'total_budget' => -1,
            'digital_marketing' => -1,
            'cvm_program' => -1,
            'engagement_outlet' => -1,
            'branding_outlet' => -1,
            'program_sales_outlet' => -1,
            'voucher_games_program' => -1,
        ];

        foreach ($headers as $idx => $rawHeader) {
            $nh = $this->normalizeHeader($rawHeader);

            if (str_contains($nh, 'cluster') || str_contains($nh, 'klaster')) {
                $map['cluster'] = $idx;
            } elseif (str_contains($nh, 'mitra') || str_contains($nh, 'partner')) {
                $map['mitra'] = $idx;
            } elseif (str_contains($nh, 'digital') || $nh === 'dm' || str_contains($nh, 'marketing')) {
                $map['digital_marketing'] = $idx;
            } elseif (str_contains($nh, 'cvm')) {
                $map['cvm_program'] = $idx;
            } elseif (str_contains($nh, 'engagement') || str_contains($nh, 'engag') || $nh === 'eo') {
                $map['engagement_outlet'] = $idx;
            } elseif (str_contains($nh, 'branding') || str_contains($nh, 'brand') || $nh === 'bo') {
                $map['branding_outlet'] = $idx;
            } elseif (str_contains($nh, 'sales') || str_contains($nh, 'pso') || str_contains($nh, 'porgram') || (str_contains($nh, 'program') && str_contains($nh, 'outlet'))) {
                $map['program_sales_outlet'] = $idx;
            } elseif (str_contains($nh, 'voucher') || str_contains($nh, 'game') || $nh === 'vgp') {
                $map['voucher_games_program'] = $idx;
            } elseif (str_contains($nh, 'total') || str_contains($nh, 'alokasi')) {
                $map['total_budget'] = $idx;
            }
        }

        // Positional fallback if headers were not detected by name
        if ($map['cluster'] === -1) $map['cluster'] = 0;
        if ($map['mitra'] === -1) $map['mitra'] = 1;
        if ($map['total_budget'] === -1) $map['total_budget'] = 2;
        if ($map['digital_marketing'] === -1) $map['digital_marketing'] = 3;
        if ($map['cvm_program'] === -1) $map['cvm_program'] = 4;
        if ($map['engagement_outlet'] === -1) $map['engagement_outlet'] = 5;
        if ($map['branding_outlet'] === -1) $map['branding_outlet'] = 6;
        if ($map['program_sales_outlet'] === -1) $map['program_sales_outlet'] = 7;
        if ($map['voucher_games_program'] === -1) $map['voucher_games_program'] = 8;

        $dataRows = array_slice($rows, $headerRowIndex + 1);
        $totalProcessed = 0;
        $now = now();
        $recordsToInsert = [];

        DB::beginTransaction();
        try {
            if ($mode === 'replace') {
                IndirectChannelAllocation::query()->delete();
            }

            foreach ($dataRows as $row) {
                $cluster = trim((string) ($row[$map['cluster']] ?? ''));
                $mitra = trim((string) ($row[$map['mitra']] ?? ''));

                $dm = $this->cleanNumeric((string) ($row[$map['digital_marketing']] ?? '0'));
                $cvm = $this->cleanNumeric((string) ($row[$map['cvm_program']] ?? '0'));
                $eo = $this->cleanNumeric((string) ($row[$map['engagement_outlet']] ?? '0'));
                $bo = $this->cleanNumeric((string) ($row[$map['branding_outlet']] ?? '0'));
                $pso = $this->cleanNumeric((string) ($row[$map['program_sales_outlet']] ?? '0'));
                $vgp = $this->cleanNumeric((string) ($row[$map['voucher_games_program']] ?? '0'));

                $total = 0.0;
                if ($map['total_budget'] !== -1 && isset($row[$map['total_budget']])) {
                    $total = $this->cleanNumeric((string) $row[$map['total_budget']]);
                }

                // If program columns were 0, try positional columns 3..8
                if (($dm + $cvm + $eo + $bo + $pso + $vgp) <= 0.0) {
                    $pDm = $this->cleanNumeric((string) ($row[3] ?? '0'));
                    $pCvm = $this->cleanNumeric((string) ($row[4] ?? '0'));
                    $pEo = $this->cleanNumeric((string) ($row[5] ?? '0'));
                    $pBo = $this->cleanNumeric((string) ($row[6] ?? '0'));
                    $pPso = $this->cleanNumeric((string) ($row[7] ?? '0'));
                    $pVgp = $this->cleanNumeric((string) ($row[8] ?? '0'));

                    if (($pDm + $pCvm + $pEo + $pBo + $pPso + $pVgp) > 0.0) {
                        $dm = $pDm; $cvm = $pCvm; $eo = $pEo; $bo = $pBo; $pso = $pPso; $vgp = $pVgp;
                    }
                }

                // If total is 0, sum the 6 programs
                if ($total <= 0.0) {
                    $total = $dm + $cvm + $eo + $bo + $pso + $vgp;
                }

                // Smart Fallback: If total > 0 but individual program columns are still 0, distribute total evenly across 6 programs
                if ($total > 0.0 && ($dm + $cvm + $eo + $bo + $pso + $vgp) <= 0.0) {
                    $evenShare = round($total / 6, 2);
                    $dm = $evenShare;
                    $cvm = $evenShare;
                    $eo = $evenShare;
                    $bo = $evenShare;
                    $pso = $evenShare;
                    $vgp = $total - ($evenShare * 5); // balance remainder
                }

                // Ignore completely empty rows
                if (empty($cluster) && empty($mitra) && $total == 0.0) {
                    continue;
                }

                $recordsToInsert[] = [
                    'cluster' => $cluster ?: 'General',
                    'mitra' => $mitra ?: 'All Mitra',
                    'total_budget' => $total,
                    'digital_marketing_budget' => $dm,
                    'cvm_program_budget' => $cvm,
                    'engagement_outlet_budget' => $eo,
                    'branding_outlet_budget' => $bo,
                    'program_sales_outlet_budget' => $pso,
                    'voucher_games_budget' => $vgp,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $totalProcessed++;
            }

            if (!empty($recordsToInsert)) {
                foreach (array_chunk($recordsToInsert, 500) as $chunk) {
                    DB::table('indirect_channel_allocations')->insert($chunk);
                }
            }

            DB::commit();

            return [
                'total_processed' => $totalProcessed,
            ];
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            throw new Exception("Gagal menyimpan data alokasi indirect channel: " . $e->getMessage());
        }
    }
}
