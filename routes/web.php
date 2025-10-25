<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\RevenuePredictionController;
use App\Http\Controllers\BookingController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'index')->name('home');
Route::view('/welcome', 'welcome')->name('welcome');


// API endpoint (returns JSON)
Route::get('/units', [UnitsController::class, 'index'])->name('units.api');

// Public page (Blade view)
Route::view('/available-units', 'units')->name('public.units');

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
    Route::put('account/credentials', [TenantController::class, 'updatecredentials'])->name('credentials.update');
    // Maintenance Requests
    Route::get('/maintenance', [TenantController::class, 'maintenance'])->name('maintenance');

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

    Route::get('/applications/archived', [ApplicationController::class, 'getArchived'])->name('applications.archived');
Route::post('/applications/{id}/unarchive', [ApplicationController::class, 'unarchive'])->name('applications.unarchive');



    
    Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.home');

    
    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('home');

    Route::get('/units/search', [UnitsController::class, 'search'])->name('units.search');
    Route::get('/edit-unit/{id}', [UnitsController::class, 'edit'])->name('admin.edit-unit');

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
    Route::get('/admin/bookings', [BookingController::class, 'indexPage'])
    ->name('admin.bookings');
    // Application Controller
    Route::get('/applications', [ApplicationController::class, 'indexView'])->name('applications');
        Route::post('/applications/{id}/approve', [ApplicationController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{id}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');
        Route::post('/applications/{id}/archive', [ApplicationController::class, 'archive'])->name('applications.archive');

});


 Route::get('/tenant/{tenantId}/payment-success', [PaymentController::class, 'success'])
        ->name('tenant.payment.success');
    Route::get('/tenant/{tenantId}/payment-cancel', [PaymentController::class, 'cancel'])
        ->name('tenant.payment.cancel');


Route::middleware(['auth'])->group(function () {

    Route::get('/tenant/{tenant}/payments', [PaymentController::class, 'dashboard'])
        ->name('tenant.payments');
        Route::post('/tenant/{tenant}/payment/create', [PaymentController::class, 'createPayment'])
        ->name('tenant.payment.create');
    Route::get('/tenant/home', [TenantController::class, 'home'])
        ->name('tenant.home'); // Siguro ito 'yung home dashboard mo

    Route::get('/tenant/property', [TenantController::class, 'property'])
        ->name('tenant.property');

        
Route::get('/tenant/{tenant}/ledger', [TenantController::class, 'ledger'])
    ->name('tenant.ledger');

});


Route::post('payments/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');
Route::post('/paymongo/webhook', [PaymentController::class, 'handleWebhook'])
    ->withoutMiddleware([ValidateCsrfToken::class]);


 Route::get('/allUnits',[UnitsController::class, 'index']);
    Route::post('/addUnits',[UnitsController::class, 'store']);
    Route::get('/findUnits/{id}',[UnitsController::class, 'show']);
    Route::put('/editUnits/{unit}',[UnitsController::class, 'update']);  
    Route::delete('/deleteUnits/{unit}',[UnitsController::class, 'delete']);
    Route::get('/units/search', [UnitsController::class, 'search'])->name('units.search');



