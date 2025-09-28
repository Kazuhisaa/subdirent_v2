<?php

use Illuminate\Support\Facades\Route;

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



Route::get('/', function () {
    return view('welcome');
});
