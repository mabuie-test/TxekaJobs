@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">Meu perfil</h1>
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if (! auth()->user()->hasVerifiedEmail())
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <div>
                <strong>Email não verificado.</strong> Reenvie o link para validar o acesso completo.
            </div>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="btn btn-sm btn-outline-primary">Reenviar verificação</button>
            </form>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="card p-3 shadow-sm">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nome completo</label>
            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control" required>
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control">
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Telemóvel</label>
            <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="form-control" required>
            @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Foto de perfil</label>
            <input type="file" name="profile_photo" accept="image/*" class="form-control">
            @if(auth()->user()->profile_photo_url)
                <small class="d-block mt-1">Foto actual:</small>
                <img src="{{ auth()->user()->profile_photo_url }}" alt="Foto" class="img-thumbnail mt-1" style="max-width: 160px;">
            @endif
            @error('profile_photo')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Currículo (PDF/Word)</label>
            <input type="file" name="curriculum" accept="application/pdf,.doc,.docx" class="form-control">
            @if(auth()->user()->curriculum_path)
                <small class="d-block mt-1"><a href="{{ Storage::disk('public')->url(auth()->user()->curriculum_path) }}" target="_blank">Ver currículo actual</a></small>
            @endif
            @error('curriculum')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-primary w-100">Guardar</button>
    </form>
</div>
@endsection
