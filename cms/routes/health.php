<?php

use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health/ready', [HealthController::class, 'ready'])->name('health.ready');
Route::get('/health/legacy', [HealthController::class, 'legacy'])->name('health.legacy');
