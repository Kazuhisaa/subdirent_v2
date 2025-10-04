<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\RevenuePredictionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout'])
    ->middleware('auth:sanctum');

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




Route::prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']);               // GET /bookings
    Route::get('/find/{id}', [BookingController::class, 'show']);       // GET /bookings/find/{id}
    Route::get('/unit/{unit_id}', [BookingController::class, 'showByUnitId']); // GET /bookings/unit/{unit_id}
    Route::post('/', [BookingController::class, 'store']);              // POST /bookings
    Route::get('/getOccupiedTime/{unit_id}/{date}',[BookingController::class,'showAllOccupiedTime']);
    Route::post('/confirm/{id}',[BookingController::class, 'confirm']);
});
    

Route::prefix('applications')->group(function () {
    Route::get('/',[ApplicationController::class,'index']);
    Route::post('/addApplicants',[ApplicationController::class,'store']);
     Route::get('/find/{id}',[ApplicationController::class,'show']);
});



Route::prefix('prediction')->group(function(){
    Route::get('/revenue/permonth',[RevenuePredictionController::class,'showPrediction']);
    Route::post('/revenue/train',[RevenuePredictionController::class,'trainModel']);
});