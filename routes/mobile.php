<?php

use App\Http\Controllers\Mobile\V1\AuthController;

Route::get('/login', [AuthController::class, 'login']);