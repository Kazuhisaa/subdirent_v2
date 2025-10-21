<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RevenuePredictionController; // Assuming this is for admin

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'index')->name('home');
Route::view('/welcome', 'welcome')->name('welcome'); // Or redirect authenticated users?
Route::view('/units', 'units')->name('units');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Tenant Routes (Protected by standard web auth)
|--------------------------------------------------------------------------
| Gamitin ang standard 'auth' middleware for web routes, hindi 'auth:sanctum'
*/
Route::middleware(['auth'])->prefix('tenant')->name('tenant.')->group(function () {
    // Tenant Dashboard/Info
    Route::get('/', [TenantController::class, 'home'])->name('home');
    Route::get('/property', [TenantController::class, 'property'])->name('property');
    Route::get('/payments', [TenantController::class, 'payments'])->name('payments'); // Page to view payments
    Route::get('/ledger', [TenantController::class, 'ledger'])->name('ledger'); // Optional ledger page

    // Payment Processing Routes (using PaymentController)
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected by standard web auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin']) // Magdagdag ka ng 'admin' middleware kung kailangan
    ->prefix('admin')->name('admin.')->group(function () {

    // Admin Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('home');

    // Units Management
    Route::get('/units/search', [UnitsController::class, 'search'])->name('units.search'); // Moved inside admin prefix
    Route::get('/rooms', [UnitsController::class, 'rooms'])->name('rooms'); // Assuming this is admin view
    Route::view('/addroom', 'admin.addroom')->name('addroom');
    Route::prefix('units')->name('units.')->controller(UnitsController::class)->group(function () {
        Route::get('/', 'index')->name('index'); // Admin list of units
        Route::post('/store', 'store')->name('store');
        Route::get('/{unit}/edit', 'edit')->name('edit'); // Use route model binding
        Route::put('/{unit}', 'update')->name('update'); // Use route model binding
        Route::post('/{id}/archive', 'archive')->name('archive');
        Route::post('/{id}/unarchive', 'unarchive')->name('unarchive');
        // Removed show route '{id}', maybe use an API route for that?
    });

    // Applications Management
    Route::get('/applications', [ApplicationController::class, 'indexView'])->name('applications');
    Route::post('/applications/{id}/approve', [ApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{id}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');
    Route::post('/applications/{id}/archive', [ApplicationController::class, 'archive'])->name('applications.archive');

    // Revenue Analytics & Prediction
    Route::get('/analytics', [RevenuePredictionController::class, 'showAnalyticsPage'])->name('analytics');
    Route::get('/predictionMonth', [RevenuePredictionController::class, 'showPredictionMonth']); // Consider naming these routes
    Route::get('/predictionQuarter', [RevenuePredictionController::class, 'showPredictionQuarter']);
    Route::get('/predictionAnnual', [RevenuePredictionController::class, 'showPredictionAnnual']);
    Route::get('/train', [RevenuePredictionController::class, 'trainModel']); // Consider naming and maybe POST/PUT method?

    // Other Admin Pages (Using Views directly)
    Route::view('/tenants', 'admin.tenants')->name('tenants'); // Consider using a controller if data is needed
    Route::view('/bookings', 'admin.bookings')->name('bookings');
    Route::view('/maintenance', 'admin.maintenance')->name('maintenance');
    Route::view('/payments', 'admin.payments')->name('payments');
    Route::view('/contracts', 'admin.contracts')->name('contracts');
    Route::view('/reports', 'admin.reports')->name('reports');
    Route::view('/records', 'admin.records')->name('records');

});

/*
|--------------------------------------------------------------------------
| Webhook Route (Publicly accessible, CSRF excluded)
|--------------------------------------------------------------------------
| Note: Exclude this route from CSRF protection in App/Http/Middleware/VerifyCsrfToken.php
*/


// ... iba pang routes



Route::middleware(['auth'])->group(function () {
Route::post('/tenant/make-payment', [PaymentController::class, 'makePayment'])->name('tenant.payment.make');
    Route::post('/tenant/payment/webhook', [PaymentController::class, 'handleWebhook'])->name('tenant.payment.webhook');
    
});

/*
|--------------------------------------------------------------------------
| Removed/Consolidated Routes
|--------------------------------------------------------------------------
| - Tinanggal ko 'yung mga duplicate na tenant payment groups.
| - Tinanggal ko 'yung mga simple function routes para sa success/failed na nasa labas ng controller.
| - Inayos ko 'yung paggamit ng auth:sanctum sa web tenant routes.
| - Inayos ko 'yung route na may tenant parameter ({tenant}) na hindi kailangan dahil Auth::user() ang ginagamit.
*/