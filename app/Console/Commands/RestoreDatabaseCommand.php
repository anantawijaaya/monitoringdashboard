<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class RestoreDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore {file? : The filename of the backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database from a backup file';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseBackupService $backupService): int
    {
        $fileName = $this->argument('file');
        $backups = $backupService->getBackupList();

        if (empty($backups)) {
            $this->error('Tidak ada file backup yang ditemukan di storage/app/backups.');
            return Command::FAILURE;
        }

        if (empty($fileName)) {
            $fileName = $backups[0]['fileName'];
            $this->info("Menggunakan backup terbaru: {$fileName}");
        }

        $targetPath = storage_path('app/backups/' . basename($fileName));

        if (!file_exists($targetPath)) {
            $this->error("File backup {$fileName} tidak ditemukan.");
            return Command::FAILURE;
        }

        $this->info("Memulai pemulihan (restore) dari {$fileName}...");
        $res = $backupService->restoreBackup($targetPath);

        if ($res['success']) {
            $this->info($res['message']);
            return Command::SUCCESS;
        }

        $this->error($res['message']);
        return Command::FAILURE;
    }
}
