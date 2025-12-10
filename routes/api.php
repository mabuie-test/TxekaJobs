<?php

use App\Http\Controllers\Payments\MpesaCallbackController;
use Illuminate\Support\Facades\Route;

Route::post('/pagamentos/mpesa/callback', MpesaCallbackController::class)->name('pagamentos.mpesa.callback');
