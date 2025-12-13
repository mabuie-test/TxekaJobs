<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Cliente\ServicoController as ClienteServicoController;
use App\Http\Controllers\Prestador\PropostaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/registar/cliente', [RegisterController::class, 'showClienteForm'])->name('register.cliente');
Route::post('/registar/cliente', [RegisterController::class, 'storeCliente'])->name('register.cliente.store');
Route::get('/registar/prestador', [RegisterController::class, 'showPrestadorForm'])->name('register.prestador');
Route::post('/registar/prestador', [RegisterController::class, 'storePrestador'])->name('register.prestador.store');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('home')->with('status', 'Email verificado com sucesso.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('cliente')->name('cliente.')->group(function () {
        Route::get('servicos', [ClienteServicoController::class, 'index'])->name('servicos.index');
        Route::get('servicos/criar', [ClienteServicoController::class, 'create'])->name('servicos.create');
        Route::post('servicos', [ClienteServicoController::class, 'store'])->name('servicos.store');
        Route::get('servicos/{servico}', [ClienteServicoController::class, 'show'])->name('servicos.show');
    });

    Route::prefix('prestador')->name('prestador.')->group(function () {
        Route::view('dashboard', 'prestador.dashboard')->name('dashboard');
        Route::get('servicos/{servico}/propostas/criar', [PropostaController::class, 'create'])->name('propostas.create');
        Route::post('servicos/{servico}/propostas', [PropostaController::class, 'store'])->name('propostas.store');
        Route::get('servicos/{servico}/propostas/{proposta}', function () {
            return view('prestador.propostas.show');
        })->name('propostas.show');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [BackupController::class, 'store'])->name('backups.store');
        Route::post('backups/restore', [BackupController::class, 'restore'])->name('backups.restore');
        Route::get('backups/{filename}', [BackupController::class, 'download'])
            ->where('filename', 'backup_[0-9]{8}_[0-9]{6}\.sql')
            ->name('backups.download');
    });
});
