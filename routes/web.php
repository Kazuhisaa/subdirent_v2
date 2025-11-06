<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\AutopayController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\RevenuePredictionController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Stripe\Stripe;

// use App\Http\Controllers\ResetPasswordController;

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
    // Account Settings
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
    // ⬇️ IDAGDAG MO ANG MGA ITO ⬇️

// === ITO ANG MGA BINAGO ===

    // 1. Forgot Password Routes (Gamit ang bagong controller)
    // 1. Forgot Password Routes (handled inline to avoid missing controller)
    Route::get('forgot-password', function () {
        return view('forgot-password');
    })->name('password.request');

    Route::post('forgot-password', function (Illuminate\Http\Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Illuminate\Support\Facades\Password::sendResetLink(
            $request->only('email')
        );

        return $status === Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');
    // 2. Reset Password Routes (Gamit ang bagong controller)
    Route::get('reset-password/{token}', function ($token) {
        return view('reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('reset-password', function (Illuminate\Http\Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Illuminate\Support\Facades\Hash::make($password);
                $user->save();
            }
        );

        return $status === Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    })->name('password.update');
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
    Route::view('/contracts', 'admin.contracts')->name('contracts');
    Route::view('/reports', 'admin.reports')->name('reports');

    Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
    Route::put('/payments/{id}', [PaymentController::class, 'update'])->name('admin.payments.update');
    Route::post('/payments/{id}/archive', [PaymentController::class, 'archive'])->name('admin.payments.archive');
    Route::post('/payments/{id}/restore', [PaymentController::class, 'restore'])->name('admin.payments.restore');
    Route::get('/payments/{payment}/download', [PaymentController::class, 'downloadInvoice'])
         ->name('admin.payments.download'); 
         });  

  
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
    Route::get('/tenant/maintenance', [MaintenanceController::class, 'index'])->name('tenant.maintenance');
    Route::post('/tenant/maintenance', [MaintenanceController::class, 'store'])->name('tenant.maintenance.store');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/admin/contracts/{id}', [ApplicationController::class, 'showContract'])->name('admin.contracts.show');

Route::get('/tenant/contracts/{tenant_id}', [ContractController::class, 'showByTenant'])
    ->name('tenant.contract.show');

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
    
Route::get('tenant/payment/invoice/{payment}', [PaymentController::class,'downloadInvoice'])
    ->name('tenant.payment.invoice.download');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('payments/{payment}/download', [PaymentController::class,'downloadInvoice'])
         ->name('payments.download');
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


Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');

// ✅ STRIPE WEBHOOK — must be outside auth middleware
Route::post('stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

// ✅ Protected routes (with auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/tenant/{tenant}/autopay/dashboard', [AutopayController::class, 'dashboard'])->name('tenant.dashboard');
    Route::post('/autopay/cancel/{autopay_id}', [AutopayController::class, 'cancel']);
    Route::get('/autopay/invoice/{autopay_id}', [AutopayController::class, 'downloadInvoice']);
    Route::get('/admin/autopay', [AutopayController::class, 'index'])->name('admin.autopay');
    Route::post('/tenant/{tenantId}/contract/{contractId}/autopay', [AutopayController::class, 'setupAutopay']);
     Route::post('/tenant/{tenantId}/contract/{contractId}/autopay', [AutopayController::class, 'setupAutopay'])
        ->name('autopay.setup');
   Route::delete('/autopay/{autopayId}/cancel', [AutopayController::class, 'cancel'])->name('autopay.cancel');
Route::patch('autopay/{autopay}/pause', [AutopayController::class, 'pause'])->name('autopay.pause');
Route::patch('autopay/{autopay}/activate', [AutopayController::class, 'activate'])->name('autopay.activate');


});


Route::post('/tenant/autopay/setup', [TenantController::class, 'autopaySetup'])
    ->name('tenant.autopay.setup')
    ->middleware('auth');