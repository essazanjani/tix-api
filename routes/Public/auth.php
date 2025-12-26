<?php

use App\Http\Controllers\Public\AuthController;

Route::group([], function ($router) {
    $router->post('register', [AuthController::class, 'register'])->name('register');
    $router->post('login', [AuthController::class, 'login'])->name('login');
    $router->post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
    $router->post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    $router->post('verify', [AuthController::class, 'verify'])->name('verify');
    $router->post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
});
