<?php

use App\Http\Controllers\Auth\ApiTokenController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/tokens/create', [ApiTokenController::class, 'store'])->name('api.tokens.create');
Route::post('/payments/midtrans/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');
