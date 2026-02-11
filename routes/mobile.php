<?php

use App\Http\Controllers\Mobile\V1\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
Route::get('');   


Route::prefix('')->group(function () {
 

});

});
