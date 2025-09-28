<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AdminController;

Route::get('/admin', [AdminController::class, 'index'])->name('admin.home');

// Temporary placeholder routes (so route() calls work)
Route::view('/admin/rooms', 'admin.rooms')->name('admin.rooms');
Route::view('/admin/tenants', 'admin.tenants')->name('admin.tenants');
Route::view('/admin/bookings', 'admin.bookings')->name('admin.bookings');
Route::view('/admin/maintenance', 'admin.maintenance')->name('admin.maintenance');
Route::view('/admin/payments', 'admin.payments')->name('admin.payments');
Route::view('/admin/contracts', 'admin.contracts')->name('admin.contracts');
Route::view('/admin/analytics', 'admin.analytics')->name('admin.analytics');
Route::view('/admin/reports', 'admin.reports')->name('admin.reports');
Route::view('/admin/records', 'admin.records')->name('admin.records');
Route::get('/logout', function () {
    // Later this will destroy session/auth.
    return redirect('/admin')->with('status', 'Logged out (placeholder)');
})->name('logout');

// Show tenant login form
Route::get('/tenant/login', function () {
    return view('partials.tenantLogin');
})->name('tenant.login');

// Handle tenant login submission
Route::post('/tenant/login', [AuthController::class, 'login'])->name('tenant.login.submit');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Tenant dashboard
    Route::get('/tenant/dashboard', function () {
        return view('tenant.dashboard');
    })->name('tenant.dashboard');

    // Admin dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Logout (better as POST)
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});

// Home / welcome
Route::get('/', function () {
    return view('welcome');
})->name('home');

// (Optional) generic login view if needed
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');


