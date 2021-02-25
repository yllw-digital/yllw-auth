<?php

use Illuminate\Support\Facades\Route;
use YllwDigital\YllwAuth\app\Http\Controllers\LoginController;

Route::prefix('auth')->group(function() {
    // Route::post('/login', [LoginController::class, 'login'])->name('login');
});