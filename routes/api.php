<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UnitsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/login',[AuthController::class,'login']);

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/tenant/dashboard',function (Request $request) {
        if(!$request->user()->tokenCan('tenant')){
                abort(403, 'Unauthorized');
        }
        return response()->json(['message'=>'Welcome Tenant']);
    });
});


Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/admin/dashboard',function (Request $request) {
        if(!$request->user()->tokenCan('admin')){
                abort(403, 'Unauthorized');
        }
        return response()->json(['message'=>'Welcome Admin']);
    });


    Route::get('/allUnits',[UnitsController::class, 'index']);
    Route::post('/addUnits',[UnitsController::class, 'store']);
    Route::get('/findUnits/{id}',[UnitsController::class, 'show']);
    Route::put('/editUnits/{unit}',[UnitsController::class, 'update']);    

});





Route::post('/logout',[AuthController::class,'logout'])
    ->middleware('auth:sanctum');



