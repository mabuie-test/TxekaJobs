<?php

namespace Tests\Unit\Admin;

use App\Services\Admin\DatabaseBackupService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseBackupServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('database.connections.mysql', [
            'database' => 'txeka',
            'host' => 'localhost',
            'port' => 3306,
            'username' => 'root',
            'password' => 'secret',
        ]);
    }

    public function test_it_stores_backup_file_using_runner_output(): void
    {
        Storage::fake('local');
        $service = new DatabaseBackupService(function () {
            return new class {
                public function isSuccessful(): bool
                {
                    return true;
                }

                public function getOutput(): string
                {
                    return '-- sql dump --';
                }

                public function getErrorOutput(): string
                {
                    return '';
                }
            };
        });

        $service->createBackup();

        $files = Storage::disk('local')->allFiles('backups');
        $this->assertCount(1, $files);
        $this->assertSame('-- sql dump --', Storage::disk('local')->get($files[0]));
    }

    public function test_it_passes_sql_to_restore_process(): void
    {
        $capturedInput = null;
        $service = new DatabaseBackupService(function (array $command, ?string $input) use (&$capturedInput) {
            $capturedInput = $input;
            return new class {
                public function isSuccessful(): bool
                {
                    return true;
                }

                public function getOutput(): string
                {
                    return '';
                }

                public function getErrorOutput(): string
                {
                    return '';
                }
            };
        });

        $service->restoreFromSql('CREATE TABLE demo (id INT);');

        $this->assertSame('CREATE TABLE demo (id INT);', $capturedInput);
    }
}
