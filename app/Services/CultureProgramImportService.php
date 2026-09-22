<?php

namespace App\Services;

use App\Models\CultureProgramAllocation;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class CultureProgramImportService
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
        $ext = strtolower($file->getClientOriginalExtension());
        $filePath = $file->getRealPath();

        if ($ext === 'csv' || $ext === 'txt') {
            $rows = $this->parseCsv($filePath);
        } elseif (in_array($ext, ['xlsx', 'xls'])) {
            $rows = $this->parseXLSXNative($filePath);
        } else {
            throw new Exception("Format berkas tidak didukung: .{$ext}. Harap gunakan XLSX, XLS, atau CSV.");
        }

        return $this->processExtractedRows($rows, $mode);
    }

    public function parseXLSXNative(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception("Gagal membuka file Excel.");
        }

        $sharedStrings = [];
        if (($sharedStringsData = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            $xml = @simplexml_load_string($sharedStringsData);
            if ($xml) {
                foreach ($xml->si as $val) {
                    if (isset($val->t)) {
                        $sharedStrings[] = (string) $val->t;
                    } elseif (isset($val->r)) {
                        $tStr = '';
                        foreach ($val->r as $rVal) {
                            $tStr .= (string) $rVal->t;
                        }
                        $sharedStrings[] = $tStr;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        $sheetData = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetData) {
            $sheetFiles = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if (preg_match('#xl/worksheets/sheet\d+\.xml#i', $stat['name'])) {
                    $sheetFiles[] = $stat['name'];
                }
            }
            if (!empty($sheetFiles)) {
                $sheetData = $zip->getFromName($sheetFiles[0]);
            }
        }

        $zip->close();

        if (!$sheetData) {
            throw new Exception("Sheet Excel tidak ditemukan.");
        }

        $xml = @simplexml_load_string($sheetData);
        if (!$xml || !isset($xml->sheetData)) {
            throw new Exception("Gagal membaca struktur XML sheet Excel.");
        }

        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $rowData = [];
            foreach ($row->c as $cell) {
                $attr = $cell->attributes();
                $cellRef = (string) $attr['r'];
                preg_match('/^([A-Z]+)/', $cellRef, $matches);
                $colLetters = $matches[1] ?? 'A';
                $colIndex = $this->columnLetterToIndex($colLetters);

                $cellType = (string) $attr['t'];
                $val = '';

                if (isset($cell->v)) {
                    $rawVal = (string) $cell->v;
                    if ($cellType === 's' && isset($sharedStrings[(int) $rawVal])) {
                        $val = $sharedStrings[(int) $rawVal];
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
                    str_contains($nh, 'culture')
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
            'culture_program' => -1,
        ];

        foreach ($headers as $idx => $hStr) {
            $nh = $this->normalizeHeader((string) $hStr);
            if (str_contains($nh, 'cluster') || str_contains($nh, 'klaster')) $map['cluster'] = $idx;
            elseif (str_contains($nh, 'mitra') || str_contains($nh, 'tap') || str_contains($nh, 'partner')) $map['mitra'] = $idx;
            elseif (str_contains($nh, 'total')) $map['total_budget'] = $idx;
            elseif (str_contains($nh, 'culture')) $map['culture_program'] = $idx;
        }

        if ($map['cluster'] === -1) $map['cluster'] = 0;
        if ($map['mitra'] === -1) $map['mitra'] = 1;
        if ($map['total_budget'] === -1) $map['total_budget'] = 2;
        if ($map['culture_program'] === -1) $map['culture_program'] = 3;

        $dataRows = array_slice($rows, $headerRowIndex + 1);
        $totalProcessed = 0;
        $now = now();
        $recordsToInsert = [];

        DB::beginTransaction();
        try {
            if ($mode === 'replace') {
                CultureProgramAllocation::query()->delete();
            }

            foreach ($dataRows as $row) {
                $cluster = trim((string) ($row[$map['cluster']] ?? ''));
                $mitra = trim((string) ($row[$map['mitra']] ?? ''));

                $cp = $this->cleanNumeric((string) ($row[$map['culture_program']] ?? '0'));

                $total = 0.0;
                if ($map['total_budget'] !== -1 && isset($row[$map['total_budget']])) {
                    $total = $this->cleanNumeric((string) $row[$map['total_budget']]);
                }

                if ($cp <= 0.0) {
                    $cp = $this->cleanNumeric((string) ($row[3] ?? '0'));
                }

                if ($total <= 0.0) {
                    $total = $cp;
                }

                if ($total > 0.0 && $cp <= 0.0) {
                    $cp = $total;
                }

                if (empty($cluster) && empty($mitra) && $total == 0.0) {
                    continue;
                }

                $recordsToInsert[] = [
                    'cluster' => $cluster ?: 'General',
                    'mitra' => $mitra ?: 'All Mitra',
                    'total_budget' => $total,
                    'culture_program_budget' => $cp,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $totalProcessed++;
            }

            if (!empty($recordsToInsert)) {
                foreach (array_chunk($recordsToInsert, 500) as $chunk) {
                    DB::table('culture_program_allocations')->insert($chunk);
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
            throw new Exception("Gagal menyimpan data alokasi culture program: " . $e->getMessage());
        }
    }
}
