<?php

namespace App\Console\Commands;

use App\Services\Admin\DatabaseBackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'txeka:backup-diario';

    protected $description = 'Gera um backup SQL da base de dados e armazena em storage/app/backups';

    public function __construct(private readonly DatabaseBackupService $backupService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $filename = $this->backupService->createBackup();
            $this->info("Backup criado: {$filename}");
            Log::info('Backup diário criado', ['filename' => $filename]);
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Falha ao gerar backup: ' . $e->getMessage());
            Log::error('Falha no backup diário', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }
    }
}
