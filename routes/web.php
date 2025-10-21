<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\RevenuePredictionController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'index')->name('home');
Route::view('/welcome', 'welcome')->name('welcome');
Route::view('/units', 'units')->name('units');

Route::middleware(['auth:sanctum'])->prefix('tenant')->name('tenant.')->group(function () {  
    // Dashboard
    Route::get('/', [TenantController::class, 'home'])->name('home');
    // My Property
    Route::get('/property', [TenantController::class, 'property'])->name('property');
    // My Payments
    Route::get('/payments', [TenantController::class, 'payments'])->name('payments');
    Route::post('/payments/pay', [TenantController::class, 'makePayment'])->name('payments.pay');
    // Property Search
    Route::get('/propertysearch', [TenantController::class, 'propertysearch'])->name('propertysearch');
    // Account 
    Route::get('/account', [TenantController::class, 'account'])->name('account');
    Route::put('/account', [TenantController::class, 'accountupdate'])->name('update');

});


// Authentication
Route::middleware(['web'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {


    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('home');

    Route::get('/units/search', [UnitsController::class, 'search'])->name('units.search');

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
    
    // Application Controller
    Route::get('/applications', [ApplicationController::class, 'indexView'])->name('applications');
        Route::post('/applications/{id}/approve', [ApplicationController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{id}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');
        Route::post('/applications/{id}/archive', [ApplicationController::class, 'archive'])->name('applications.archive');

});

Route::get('/payments/success', function () {
    return "Payment successful!";
})->name('payments.success');

Route::get('/payments/failed', function () {
    return "Payment failed!";
})->name('payments.failed');



Route::middleware(['auth'])->group(function () {
        Route::get('/tenant/payments', [TenantController::class, 'payments'])->name('tenant.payments');
    Route::get('tenant/{tenant}/dashboard', [PaymentController::class, 'dashboard'])->name('tenant.dashboard');
    Route::get('tenant/{tenant}/payments', [PaymentController::class, 'payments'])->name('tenant.payments');
    Route::post('tenant/{tenant}/payments/create', [PaymentController::class, 'createPayment'])->name('payments.create');
    Route::get('tenant/{tenant}/payments/success', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('tenant/{tenant}/payments/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
});

Route::post('payments/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');