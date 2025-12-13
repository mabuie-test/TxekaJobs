@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4">Backups da Base de Dados</h1>
        <form method="POST" action="{{ route('admin.backups.store') }}">
            @csrf
            <button class="btn btn-primary btn-sm">Gerar backup agora</button>
        </form>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Importar backup</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.backups.restore') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Ficheiro SQL</label>
                    <input type="file" name="backup_file" class="form-control" required accept=".sql,text/plain">
                </div>
                <button class="btn btn-warning" type="submit">Restaurar base de dados</button>
                <p class="text-muted small mb-0 mt-2">A operação pode demorar alguns minutos e deve ser executada apenas em situações de recuperação.</p>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Backups disponíveis</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tamanho</th>
                        <th>Criado em</th>
                        <th class="text-end">Acções</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $backup)
                        <tr>
                            <td>{{ $backup['name'] }}</td>
                            <td>{{ number_format($backup['size'] / 1024, 2) }} KB</td>
                            <td>{{ optional($backup['created_at'])->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="text-end">
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.backups.download', $backup['name']) }}">Download</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-3">Nenhum backup disponível.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
