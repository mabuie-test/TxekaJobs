<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Cliente\ServicoController as ClienteServicoController;
use App\Http\Controllers\Prestador\PropostaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/otp', [OtpController::class, 'show'])->name('otp.show');
Route::post('/otp', [OtpController::class, 'verify'])->name('otp.verify');

Route::middleware('auth')->group(function () {
    Route::prefix('cliente')->name('cliente.')->group(function () {
        Route::get('servicos', [ClienteServicoController::class, 'index'])->name('servicos.index');
        Route::get('servicos/criar', [ClienteServicoController::class, 'create'])->name('servicos.create');
        Route::post('servicos', [ClienteServicoController::class, 'store'])->name('servicos.store');
        Route::get('servicos/{servico}', [ClienteServicoController::class, 'show'])->name('servicos.show');
    });

    Route::prefix('prestador')->name('prestador.')->group(function () {
        Route::get('servicos/{servico}/propostas/criar', [PropostaController::class, 'create'])->name('propostas.create');
        Route::post('servicos/{servico}/propostas', [PropostaController::class, 'store'])->name('propostas.store');
        Route::get('servicos/{servico}/propostas/{proposta}', function () {
            return view('prestador.propostas.show');
        })->name('propostas.show');
    });
});
