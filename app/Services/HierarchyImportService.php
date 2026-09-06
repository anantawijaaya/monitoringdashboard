<?php

namespace App\Services;

use App\Models\HierarchyOutlet;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class HierarchyImportService
{
    /**
     * Helper to determine row color class based on cluster name
     */
    public function getGroupClass(string $cluster): array
    {
        return [
            'group_class' => 'bg-white hover:bg-gray-50/80',
            'badge_color' => 'bg-gray-100 text-gray-800',
        ];
    }

    /**
     * Clean and ensure string is valid UTF-8
     */
    private function cleanUtf8(string $str): string
    {
        // Convert encoding to UTF-8 if invalid characters exist
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

        // 1. Extract shared strings
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = @simplexml_load_string($sharedStringsXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = $this->cleanUtf8((string)$si->t);
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string)$r->t;
                        }
                        $sharedStrings[] = $this->cleanUtf8($text);
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Find worksheet
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
            throw new Exception("Tidak dapat menemukan lembar kerja (worksheet) di dalam file Excel.");
        }

        $sheetXml = @simplexml_load_string($sheetXmlContent);
        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            throw new Exception("Lembar kerja Excel kosong atau tidak terbaca.");
        }

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

                $rowData[$colIndex] = $this->cleanUtf8($cellValue);
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

        return $rows;
    }

    /**
     * Parse CSV file with auto-detect delimiter and UTF-8 handling
     */
    public function parseCsv(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new Exception("Gagal membaca file CSV.");
        }

        // Remove UTF-8 BOM if present
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) {
            return [];
        }

        // Detect delimiter
        $firstLine = $lines[0];
        $semicolonCount = substr_count($firstLine, ';');
        $commaCount = substr_count($firstLine, ',');
        $tabCount = substr_count($firstLine, "\t");

        $delimiter = ',';
        if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
            $delimiter = ';';
        } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
            $delimiter = "\t";
        }

        $rows = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;
            $cols = str_getcsv($trimmed, $delimiter);
            $cleanCols = array_map(function ($c) {
                return $this->cleanUtf8((string)$c);
            }, $cols);
            $rows[] = $cleanCols;
        }

        return $rows;
    }

    /**
     * Infer Branch from Cluster name
     */
    public function getBranchByCluster(string $cluster): string
    {
        $cl = strtoupper(trim($cluster));
        if (str_contains($cl, 'BALI')) {
            return 'DENPASAR';
        } elseif (str_contains($cl, 'SIKKA') || str_contains($cl, 'FLORES') || str_contains($cl, 'MANGGARAI') || str_contains($cl, 'ENDE')) {
            return 'FLORES';
        } elseif (str_contains($cl, 'KUPANG') || str_contains($cl, 'ROTE') || str_contains($cl, 'MALAKA') || str_contains($cl, 'TIMTIM') || str_contains($cl, 'SUMBA')) {
            return 'KUPANG';
        } elseif (str_contains($cl, 'LOMBOK') || str_contains($cl, 'SUMBAWA') || str_contains($cl, 'MATARAM') || str_contains($cl, 'BIMA') || str_contains($cl, 'DOMPU')) {
            return 'MATARAM';
        }
        return 'DENPASAR';
    }

    /**
     * Infer Manager from Branch name
     */
    public function getManagerByBranch(string $branch): string
    {
        $br = strtoupper(trim($branch));
        if (str_contains($br, 'DENPASAR') || str_contains($br, 'BALI')) {
            return 'HERBIANTO';
        } elseif (str_contains($br, 'FLORES')) {
            return 'WASKITHO ANJAR PRABOWO';
        } elseif (str_contains($br, 'KUPANG')) {
            return 'ADHY YANWAR';
        } elseif (str_contains($br, 'MATARAM') || str_contains($br, 'LOMBOK')) {
            return 'KURNIA BUDI SETIAWAN';
        }
        return 'MANAGER BRANCH';
    }

    /**
     * Process extracted row matrices and insert directly into hierarchy_outlets
     */
    public function processExtractedRows(array $rows, string $mode = 'replace'): array
    {
        if (empty($rows) || count($rows) < 2) {
            throw new Exception('Data tidak memadai untuk diimpor.');
        }

        // Locate Header Row (usually row 0, but check if row 0 has keywords)
        $headerRowIndex = 0;
        $headerMap = [];

        foreach ($rows as $rIdx => $row) {
            $rowString = strtolower(implode(' ', $row));
            if (str_contains($rowString, 'kabupaten') || str_contains($rowString, 'cluster') || str_contains($rowString, 'branch') || str_contains($rowString, 'outlet') || str_contains($rowString, 'mitra')) {
                $headerRowIndex = $rIdx;
                break;
            }
        }

        $headerRow = $rows[$headerRowIndex];
        $hasNoCol = false;

        foreach ($headerRow as $idx => $headerText) {
            $hClean = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$headerText)));

            if ($hClean === 'no' || $hClean === 'nomor' || $hClean === 'num' || $hClean === '#' || $hClean === 'number') {
                $hasNoCol = true;
            } elseif (str_contains($hClean, 'manager') || str_contains($hClean, 'mgr') || str_contains($hClean, 'pic') || str_contains($hClean, 'pimpinan') || str_contains($hClean, 'kepala') || $hClean === 'mb') {
                // Must check MANAGER BRANCH first so it doesn't match 'branch'
                $headerMap['manager_branch'] = $idx;
            } elseif (str_contains($hClean, 'branch') || str_contains($hClean, 'cabang')) {
                // Pure branch column
                $headerMap['branch'] = $idx;
            } elseif (str_contains($hClean, 'outlet') || str_contains($hClean, 'jumlah') || str_contains($hClean, 'totaloutlet') || str_contains($hClean, 'qty') || str_contains($hClean, 'jml')) {
                $headerMap['jumlah_outlet'] = $idx;
            } elseif (str_contains($hClean, 'mitra') || str_contains($hClean, 'partner') || str_contains($hClean, 'dealer') || str_contains($hClean, 'distributor')) {
                $headerMap['mitra'] = $idx;
            } elseif (str_contains($hClean, 'cluster') || str_contains($hClean, 'kluster')) {
                $headerMap['cluster'] = $idx;
            } elseif (str_contains($hClean, 'kabupaten') || str_contains($hClean, 'kota') || str_contains($hClean, 'wilayah') || str_contains($hClean, 'regency') || str_contains($hClean, 'city') || $hClean === 'kab') {
                $headerMap['kabupaten'] = $idx;
            }
        }

        // Fallback default index map if headers couldn't be matched by keywords
        $offset = $hasNoCol ? 1 : 0;
        if (!isset($headerMap['kabupaten'])) $headerMap['kabupaten'] = $offset;
        if (!isset($headerMap['cluster'])) $headerMap['cluster'] = $offset + 1;
        if (!isset($headerMap['mitra'])) $headerMap['mitra'] = $offset + 2;
        if (!isset($headerMap['branch'])) $headerMap['branch'] = $offset + 3;
        if (!isset($headerMap['jumlah_outlet'])) $headerMap['jumlah_outlet'] = $offset + 4;
        if (!isset($headerMap['manager_branch'])) $headerMap['manager_branch'] = $offset + 5;

        $rowsToInsert = [];
        $importedCount = 0;

        for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
            $cols = $rows[$i];
            if (empty($cols)) continue;

            $kabupaten = strtoupper(trim($cols[$headerMap['kabupaten']] ?? ''));
            $cluster = strtoupper(trim($cols[$headerMap['cluster']] ?? ''));
            $mitra = strtoupper(trim($cols[$headerMap['mitra']] ?? ''));
            $branch = strtoupper(trim($cols[$headerMap['branch']] ?? ''));
            $rawJumlah = trim($cols[$headerMap['jumlah_outlet']] ?? '0');
            $manager = strtoupper(trim($cols[$headerMap['manager_branch']] ?? ''));

            // Ignore empty rows or repeated headers
            if (empty($kabupaten) || empty($cluster) || str_contains(strtolower($kabupaten), 'kabupaten') || str_contains(strtolower($kabupaten), 'cluster')) {
                continue;
            }

            // Inferred fallback if branch or manager is empty or erroneously set
            $inferredBranch = $this->getBranchByCluster($cluster);
            $inferredManager = $this->getManagerByBranch($inferredBranch);

            if (empty($branch) || $branch === $manager || in_array($branch, ['HERBIANTO', 'WASKITHO ANJAR PRABOWO', 'ADHY YANWAR', 'KURNIA BUDI SETIAWAN'])) {
                $branch = $inferredBranch;
            }

            if (empty($manager)) {
                $manager = $inferredManager;
            }

            $cleanNumber = intval(preg_replace('/[^0-9]/', '', $rawJumlah));
            $formattedJumlah = number_format($cleanNumber, 0, ',', '.');
            $groupData = $this->getGroupClass($cluster);

            $rowsToInsert[] = [
                'kabupaten' => $kabupaten,
                'cluster' => $cluster,
                'mitra' => $mitra ?: 'PT AKAR DAYA',
                'branch' => $branch,
                'jumlah_outlet' => $formattedJumlah ?: '1.000',
                'manager_branch' => $manager,
                'group_class' => $groupData['group_class'],
                'badge_color' => $groupData['badge_color'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $importedCount++;
        }

        if (empty($rowsToInsert)) {
            throw new Exception('Tidak ada baris data yang valid ditemukan dalam berkas Excel/CSV.');
        }

        DB::transaction(function () use ($mode, $rowsToInsert) {
            if ($mode === 'replace') {
                HierarchyOutlet::query()->delete();
                foreach (array_chunk($rowsToInsert, 50) as $chunk) {
                    HierarchyOutlet::insert($chunk);
                }
            } else {
                // Mode: append (preserve old data, update matching or insert new)
                foreach ($rowsToInsert as $row) {
                    HierarchyOutlet::updateOrCreate(
                        [
                            'kabupaten' => $row['kabupaten'],
                            'cluster' => $row['cluster'],
                        ],
                        [
                            'mitra' => $row['mitra'],
                            'branch' => $row['branch'],
                            'jumlah_outlet' => $row['jumlah_outlet'],
                            'manager_branch' => $row['manager_branch'],
                            'group_class' => $row['group_class'],
                            'badge_color' => $row['badge_color'],
                        ]
                    );
                }
            }
        });

        return [
            'success' => true,
            'count' => $importedCount,
            'message' => "Berhasil mengimpor {$importedCount} data Cluster & Outlet langsung ke database!",
        ];
    }
}
