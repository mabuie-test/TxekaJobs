<?php

use App\Http\Controllers\Auth\AdminRegistrationController;
use App\Http\Controllers\Payments\MpesaCallbackController;
use Illuminate\Support\Facades\Route;

Route::post('/pagamentos/mpesa/callback', MpesaCallbackController::class)->name('pagamentos.mpesa.callback');
Route::post('/admin/register', [AdminRegistrationController::class, 'store'])->name('admin.register');
