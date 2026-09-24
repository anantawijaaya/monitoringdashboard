<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Exception;

class DatabaseBackupService
{
    /**
     * Directory path where backup files are stored.
     */
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Generate a complete database SQL backup file.
     *
     * @return array Containing success status, message, and filepath
     */
    public function createBackup(): array
    {
        try {
            $tz = new \DateTimeZone(config('app.timezone', 'Asia/Makassar'));
            $now = new \DateTime('now', $tz);
            $timestamp = $now->format('Y-m-d_H-i-s');
            $fileName = "backup_{$timestamp}.sql";
            $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $fileName;

            $driver = config('database.default');

            if ($driver === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                if (File::exists($dbPath)) {
                    File::copy($dbPath, $filePath);
                } else {
                    $sqlContent = $this->generateGenericSqlDump();
                    File::put($filePath, $sqlContent);
                }
            } else {
                $sqlContent = $this->generateGenericSqlDump();
                File::put($filePath, $sqlContent);
            }

            return [
                'success' => true,
                'fileName' => $fileName,
                'filePath' => $filePath,
                'message' => "Backup database berhasil dibuat: {$fileName}"
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal membuat backup database: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate portable SQL dump statements for all database tables (MySQL and SQLite).
     */
    protected function generateGenericSqlDump(): string
    {
        $driver = config('database.default');

        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $tableKey = 'name';
        } else {
            $tables = DB::select('SHOW TABLES');
            $dbName = config('database.connections.mysql.database', 'monitoring_page');
            $tableKey = "Tables_in_{$dbName}";
        }

        $sql = "-- Database Snapshot Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- App: " . config('app.name') . "\n\n";

        foreach ($tables as $tableObj) {
            $tableName = $tableObj->$tableKey ?? current((array) $tableObj);
            if (empty($tableName) || $tableName === 'sqlite_sequence') continue;

            if ($driver === 'sqlite') {
                $createTableRes = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$tableName]);
                if (!empty($createTableRes)) {
                    $createSql = $createTableRes[0]->sql ?? null;
                    if ($createSql) {
                        $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                        $sql .= $createSql . ";\n\n";
                    }
                }
            } else {
                $createTableRes = DB::select("SHOW CREATE TABLE `{$tableName}`");
                if (!empty($createTableRes)) {
                    $createSql = $createTableRes[0]->{'Create Table'} ?? null;
                    if ($createSql) {
                        $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                        $sql .= $createSql . ";\n\n";
                    }
                }
            }

            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $columns = array_keys($rowArray);
                    $escapedCols = array_map(fn($c) => "`{$c}`", $columns);
                    
                    $values = array_map(function($v) {
                        if (is_null($v)) return 'NULL';
                        if (is_bool($v)) return $v ? 1 : 0;
                        if (is_numeric($v)) return $v;
                        return "'" . addslashes((string) $v) . "'";
                    }, array_values($rowArray));

                    $sql .= "INSERT INTO `{$tableName}` (" . implode(', ', $escapedCols) . ") VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }

        return $sql;
    }

    /**
     * Restore database from a given backup file path.
     *
     * @param string $filePath
     * @return array
     */
    public function restoreBackup(string $filePath): array
    {
        if (!File::exists($filePath)) {
            return ['success' => false, 'message' => 'File backup tidak ditemukan.'];
        }

        try {
            $driver = config('database.default');

            if ($driver === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                if (File::exists($dbPath) && str_ends_with($filePath, '.sqlite')) {
                    File::copy($filePath, $dbPath);
                    return ['success' => true, 'message' => 'Database SQLite berhasil dipulihkan.'];
                }
            }

            $sql = File::get($filePath);
            if (empty(trim($sql))) {
                return ['success' => false, 'message' => 'File backup kosong.'];
            }

            // Remove single line SQL comments (--)
            $lines = explode("\n", $sql);
            $cleanQueries = [];
            $currentQuery = '';

            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (empty($trimmed) || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*')) {
                    continue;
                }

                $currentQuery .= ' ' . $trimmed;

                if (str_ends_with($trimmed, ';')) {
                    $cleanQueries[] = trim($currentQuery);
                    $currentQuery = '';
                }
            }

            if (!empty(trim($currentQuery))) {
                $cleanQueries[] = trim($currentQuery);
            }

            // Disable foreign key checks for MySQL restore stability
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            try {
                foreach ($cleanQueries as $query) {
                    if (!empty($query)) {
                        DB::unprepared($query);
                    }
                }
            } finally {
                if ($driver === 'mysql') {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                }
            }

            return [
                'success' => true,
                'message' => 'Data aplikasi berhasil dipulihkan (Restore 100% Sukses)!'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal memulihkan database: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get list of all available backup files sorted by newest first.
     *
     * @return array
     */
    public function getBackupList(): array
    {
        if (!File::exists($this->backupDir)) {
            return [];
        }

        $files = File::files($this->backupDir);
        $backups = [];

        $tz = new \DateTimeZone(config('app.timezone', 'Asia/Makassar'));

        foreach ($files as $f) {
            $fileName = $f->getFilename();
            if (str_starts_with($fileName, 'backup_')) {
                $sizeBytes = $f->getSize();
                $sizeFormatted = $sizeBytes > 1048576 
                    ? number_format($sizeBytes / 1048576, 2, ',', '.') . ' MB'
                    : number_format($sizeBytes / 1024, 1, ',', '.') . ' KB';

                $dt = (new \DateTime())->setTimestamp($f->getMTime())->setTimezone($tz);

                $backups[] = [
                    'fileName' => $fileName,
                    'filePath' => $f->getPathname(),
                    'size' => $sizeFormatted,
                    'sizeBytes' => $sizeBytes,
                    'createdAt' => $dt->format('d M Y, H:i:s'),
                    'timestamp' => $f->getMTime(),
                ];
            }
        }

        usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
        return $backups;
    }

    /**
     * Delete a specific backup file.
     *
     * @param string $fileName
     * @return bool
     */
    public function deleteBackup(string $fileName): bool
    {
        $cleanPath = $this->backupDir . DIRECTORY_SEPARATOR . basename($fileName);

        if (File::exists($cleanPath)) {
            return File::delete($cleanPath);
        }
        return false;
    }
}
