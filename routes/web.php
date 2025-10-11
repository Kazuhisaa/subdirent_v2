<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UnitsController;


// PUBLIC ROUTES

Route::get('/', fn() => view('index'))->name('home');
Route::get('/welcome', fn() => view('welcome'))->name('welcome');

// Tenant dashboard
Route::get('/tenant', [TenantController::class, 'index'])->name('tenant.home');

// Units
Route::get('/units', fn() => view('units'))->name('units');


// AUTHENTICATION
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');

// Logout
Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('web');


// ADMIN ROUTES
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('home');

 

    // Static admin pages 
    Route::get('/rooms', [UnitsController::class, 'rooms'])
    ->name('rooms');
    Route::view('/addroom', 'admin.addroom')->name('addroom');
    Route::view('/tenants', 'admin.tenants')->name('tenants');
    Route::view('/bookings', 'admin.bookings')->name('bookings');
    Route::view('/maintenance', 'admin.maintenance')->name('maintenance');
    Route::view('/payments', 'admin.payments')->name('payments');
    Route::view('/contracts', 'admin.contracts')->name('contracts');
    Route::view('/analytics', 'admin.analytics')->name('analytics');
    Route::view('/reports', 'admin.reports')->name('reports');
    Route::view('/records', 'admin.records')->name('records');

    // Units Controller Routes
    Route::controller(UnitsController::class)->prefix('units')->name('units.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{unit}', 'update')->name('update');
        Route::post('/{id}/archive', 'archive')->name('archive');
        Route::post('/{id}/unarchive', 'unarchive')->name('unarchive');
    });
});
