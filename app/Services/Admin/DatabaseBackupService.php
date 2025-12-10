<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class DatabaseBackupService
{
    private const BACKUP_DISK = 'local';
    private const BACKUP_DIR = 'backups';

    /** @var callable */
    private $processRunner;

    public function __construct(?callable $processRunner = null)
    {
        $this->processRunner = $processRunner ?? function (array $command, ?string $input = null): Process {
            $process = new Process($command);
            $process->setTimeout(300);
            if ($input !== null) {
                $process->setInput($input);
            }
            $process->run();

            return $process;
        };
    }

    /**
     * Cria um backup completo da base de dados em formato SQL e devolve o nome do ficheiro armazenado.
     */
    public function createBackup(): string
    {
        $this->ensureBackupDirectory();

        $database = Config::get('database.connections.mysql.database');
        $host = Config::get('database.connections.mysql.host');
        $port = (string) Config::get('database.connections.mysql.port');
        $user = Config::get('database.connections.mysql.username');
        $password = (string) Config::get('database.connections.mysql.password');

        $filename = sprintf('backup_%s.sql', now()->format('Ymd_His'));
        $command = [
            'mysqldump',
            '--single-transaction',
            '--skip-lock-tables',
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $user,
            '--password=' . $password,
            $database,
        ];

        /** @var Process $process */
        $process = ($this->processRunner)($command, null);

        if (! $process->isSuccessful()) {
            throw new \RuntimeException('Falha ao gerar backup: ' . $process->getErrorOutput());
        }

        Storage::disk(self::BACKUP_DISK)->put(self::BACKUP_DIR . '/' . $filename, $process->getOutput());

        return $filename;
    }

    /**
     * Lista os backups existentes com metadados úteis para interface administrativa.
     */
    public function listBackups(): array
    {
        $this->ensureBackupDirectory();

        $files = Storage::disk(self::BACKUP_DISK)->files(self::BACKUP_DIR);

        return collect($files)
            ->filter(fn ($path) => str_ends_with($path, '.sql'))
            ->sortDesc()
            ->map(function ($path) {
                $meta = Storage::disk(self::BACKUP_DISK)->lastModified($path);

                return [
                    'path' => $path,
                    'name' => basename($path),
                    'size' => Storage::disk(self::BACKUP_DISK)->size($path),
                    'created_at' => $meta ? now()->createFromTimestamp($meta) : null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Devolve o conteúdo de um backup para download.
     */
    public function getBackupContents(string $filename): string
    {
        $sanitized = $this->sanitizeFilename($filename);
        $path = self::BACKUP_DIR . '/' . $sanitized;

        if (! Storage::disk(self::BACKUP_DISK)->exists($path)) {
            throw new \InvalidArgumentException('Backup não encontrado.');
        }

        return Storage::disk(self::BACKUP_DISK)->get($path);
    }

    /**
     * Restaura a base de dados a partir de um ficheiro SQL fornecido pelo administrador.
     */
    public function restoreFromSql(string $sqlContent): void
    {
        $database = Config::get('database.connections.mysql.database');
        $host = Config::get('database.connections.mysql.host');
        $port = (string) Config::get('database.connections.mysql.port');
        $user = Config::get('database.connections.mysql.username');
        $password = (string) Config::get('database.connections.mysql.password');

        $command = [
            'mysql',
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $user,
            '--password=' . $password,
            $database,
        ];

        /** @var Process $process */
        $process = ($this->processRunner)($command, $sqlContent);

        if (! $process->isSuccessful()) {
            throw new \RuntimeException('Falha ao restaurar backup: ' . $process->getErrorOutput());
        }
    }

    private function ensureBackupDirectory(): void
    {
        if (! Storage::disk(self::BACKUP_DISK)->exists(self::BACKUP_DIR)) {
            Storage::disk(self::BACKUP_DISK)->makeDirectory(self::BACKUP_DIR);
        }
    }

    private function sanitizeFilename(string $filename): string
    {
        if (! preg_match('/^backup_[0-9]{8}_[0-9]{6}\\.sql$/', $filename)) {
            throw new \InvalidArgumentException('Nome de ficheiro inválido.');
        }

        return $filename;
    }
}
