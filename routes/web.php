<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\UnitsController;

Route::get('/admin', [AdminController::class, 'index'])->name('admin.home');

// Temporary placeholder routes (so route() calls work)
Route::view('/admin/rooms', 'admin.rooms')->name('admin.rooms');
Route::view('/admin/addroom', 'admin.addroom')->name('admin.addroom');
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

// Tenant dashboard (protected)
Route::middleware('auth')->get('/tenant/dashboard', function () {
    return view('tenant.dashboard');
})->name('tenant.dashboard');

// Welcome / homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Generic login form
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Generic login submit
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Unified logout (POST is more secure)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/rooms', [UnitsController::class, 'rooms'])->name('rooms'); // <-- change here
    Route::get('/addroom', function () {
        return view('admin.addroom');
    })->name('addroom');
    Route::post('/units', [UnitsController::class, 'store'])->name('units.store');
    Route::get('/units/{unit}', [UnitsController::class, 'show'])->name('units.show');
    Route::put('/units/{unit}', [UnitsController::class, 'update'])->name('units.update');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/units/{id}/edit', [UnitsController::class, 'edit'])->name('units.edit');
});
Route::patch('/admin/units/{id}/archive', [UnitsController::class, 'archive'])->name('admin.units.archive');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/rooms', [UnitsController::class, 'rooms'])->name('rooms'); // <-- change here
    Route::get('/addroom', function () {
        return view('admin.addroom');
    })->name('addroom');
    Route::post('/units', [UnitsController::class, 'store'])->name('units.store');
    Route::get('/units/{unit}', [UnitsController::class, 'show'])->name('units.show');
    Route::put('/units/{unit}', [UnitsController::class, 'update'])->name('units.update');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/units/{id}/edit', [UnitsController::class, 'edit'])->name('units.edit');
});
Route::patch('/admin/units/{id}/archive', [UnitsController::class, 'archive'])->name('admin.units.archive');