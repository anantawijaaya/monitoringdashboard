<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an instant full database SQL backup snapshot';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseBackupService $backupService): int
    {
        $this->info('Memulai pembuatan backup database...');

        $res = $backupService->createBackup();

        if ($res['success']) {
            $this->info($res['message']);
            return Command::SUCCESS;
        }

        $this->error($res['message']);
        return Command::FAILURE;
    }
}
