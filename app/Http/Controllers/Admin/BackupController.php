<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RestoreBackupRequest;
use App\Services\Admin\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class BackupController extends Controller
{
    public function __construct(private readonly DatabaseBackupService $backupService)
    {
    }

    public function index(): View
    {
        $this->authorizeAdmin();

        return view('admin.backups.index', [
            'backups' => $this->backupService->listBackups(),
        ]);
    }

    public function store(): RedirectResponse
    {
        $this->authorizeAdmin();

        try {
            $filename = $this->backupService->createBackup();
            return back()->with('status', "Backup criado com sucesso: {$filename}");
        } catch (\Throwable $e) {
            Log::error('Falha ao criar backup', ['error' => $e->getMessage()]);
            return back()->withErrors('Falha ao criar backup: ' . $e->getMessage());
        }
    }

    public function download(string $filename)
    {
        $this->authorizeAdmin();

        $contents = $this->backupService->getBackupContents($filename);
        return Response::make($contents, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function restore(RestoreBackupRequest $request): RedirectResponse
    {
        try {
            $sql = file_get_contents($request->file('backup_file')->getRealPath());
            $this->backupService->restoreFromSql($sql);
            return back()->with('status', 'Base de dados restaurada com sucesso a partir do ficheiro enviado.');
        } catch (\Throwable $e) {
            Log::error('Falha na restauração de backup', ['error' => $e->getMessage()]);
            return back()->withErrors('Falha na restauração: ' . $e->getMessage());
        }
    }

    private function authorizeAdmin(): void
    {
        if (! auth()->check() || auth()->user()->tipo_perfil !== 'admin') {
            abort(403, 'Acesso restrito a administradores.');
        }
    }
}
