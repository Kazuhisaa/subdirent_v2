<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\RevenuePredictionController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'index')->name('home');
Route::view('/welcome', 'welcome')->name('welcome');
Route::view('/units', 'units')->name('units');

// Tenant dashboard
Route::get('/tenant', [TenantController::class, 'index'])->name('tenant.home');

// Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('home');

    // Revenue Analytics
    Route::get('/admin/analytics', [RevenuePredictionController::class, 'showAnalyticsPage'])
        ->name('admin.analytics')
        ->middleware('auth');

    Route::get('/analytics', [RevenuePredictionController::class, 'showAnalyticsPage'])->name('analytics');
    Route::get('/predictionMonth', [RevenuePredictionController::class, 'showPredictionMonth']);
    Route::get('/predictionQuarter', [RevenuePredictionController::class, 'showPredictionQuarter']);
    Route::get('/predictionAnnual', [RevenuePredictionController::class, 'showPredictionAnnual']);
    Route::get('/train', [RevenuePredictionController::class, 'trainModel']);

    // Static admin pages
    Route::get('/rooms', [UnitsController::class, 'rooms'])->name('rooms');
    Route::view('/addroom', 'admin.addroom')->name('addroom');
    Route::view('/tenants', 'admin.tenants')->name('tenants');
    Route::view('/bookings', 'admin.bookings')->name('bookings');
    Route::view('/maintenance', 'admin.maintenance')->name('maintenance');
    Route::view('/payments', 'admin.payments')->name('payments');
    Route::view('/contracts', 'admin.contracts')->name('contracts');
    Route::view('/reports', 'admin.reports')->name('reports');
    Route::view('/records', 'admin.records')->name('records');

    // Units Controller
    Route::prefix('units')->name('units.')->controller(UnitsController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{unit}', 'update')->name('update');
        Route::post('/{id}/archive', 'archive')->name('archive');
        Route::post('/{id}/unarchive', 'unarchive')->name('unarchive');
    });
});
