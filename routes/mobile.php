<?php

use App\Http\Controllers\Mobile\V1\AuthController;
use App\Http\Controllers\Mobile\V1\AccountController;
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
Route::prefix('Account')->group(function () {
    Route::get('unit', [AccountController::class, 'show']);  
});

});
