<?php

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\OccupancyController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\RevenuePredictionController;
use App\Http\Controllers\ContractController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware(['auth:sanctum'])->prefix('admin/api')->group(function () {
    Route::get('/tenants', [TenantController::class, 'index']);
});

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

Route::middleware(['auth'])->group(function () {
    Route::get('/units', [UnitsController::class, 'index'])->name('units.index');
});
    Route::get('/allUnits',[UnitsController::class, 'index']);
    Route::post('/addUnits', [UnitsController::class, 'store']);
    Route::get('/findUnits/{id}',[UnitsController::class, 'show']);
    Route::post('/editUnits/{unit}',[UnitsController::class, 'update']);  
    Route::delete('/deleteUnits/{unit}',[UnitsController::class, 'delete']);
    Route::get('/units/search', [UnitsController::class, 'search'])->name('units.search');
Route::prefix('applications')->group(function () {
    Route::get('/find/{id}',[ApplicationController::class,'show']);
    Route::put('/editApplications/{id}',[ApplicationController::class,'update']);
    Route::patch('/archive/{id}', [ApplicationController::class, 'archive']);
    Route::post('/approve/{id}',[ApplicationController::class,'approve']);
});
Route::prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::get('/find/{id}', [BookingController::class, 'show']);
    Route::get('/unit/{unit_id}', [BookingController::class, 'showByUnitId']);
    Route::post('/confirm/{id}', [BookingController::class, 'confirm']);
});
});
Route::prefix('bookings')->group(function () {
    Route::post('/', [BookingController::class, 'store']);              // POST /bookings
    Route::get('/getOccupiedTime/{unit_id}/{date}',[BookingController::class,'showAllOccupiedTime']);
});
    
Route::prefix('applications')->group(function () {
    Route::post('/addApplicants',[ApplicationController::class,'store']);
});
Route::post('/applications', [ApplicationController::class, 'store']);
Route::prefix('prediction')->group(function(){
    Route::get('/revenue/permonth',[RevenuePredictionController::class,'showPredictionMonth']);
    Route::get('/revenue/perQuarter',[RevenuePredictionController::class,'showPredictionQuarter']);
    Route::get('/revenue/perAnnual',[RevenuePredictionController::class,'showPredictionAnnual']);
    Route::post('/revenue/train',[RevenuePredictionController::class,'trainModel']);
});
Route::prefix('revenue')->group(function(){
    Route::get('/average',[RevenueController::class,'showAverage']);
     Route::get('/peakmonth',[RevenueController::class,'showPeakMonth']);
     Route::get('/totalrevenue',[RevenueController::class,'showTotalRevenue']);
     Route::post('/addNewMonthRevenue',[RevenueController::class,'store']);
     Route::get('/latestRevenue',[RevenueController::class,'showLatestRevenue']);
     Route::put('/addRevenue',[RevenueController::class,'addrevenue']);
     /* 
     example put json
       {
         "increment": 1000 <- yung binayad ni tenant
        }
     
     */
      Route::put('/subtract-old-contract',[RevenueController::class,'completedContract']);

        /* 
     example put json
       {
         "contract": 1 <- mababawas na contact
        }
     
     */

     Route::put('/add-new-contract', [RevenueController::class, 'addNewContract']);
       /*
       
          example put json
           {
      "contract" : 1 <- kada new contract mag aadd ng one,fixed na yan automatic na mag aadd sa new_contract at active contract
           }
       
       */
});

Route::get('/analytics/data', [RevenuePredictionController::class, 'getAnalyticsData']);

Route::prefix('occupancy')->group(function(){
    Route::get('/all',[OccupancyController::class,'showAll']);  //total ng units
    Route::get('/perlocation',[OccupancyController::class,'showByLocation']);  //total ng units per location
    Route::get('/rate',[OccupancyController::class,'showOccupancyRateByLocation']); // occupany rate per location
    Route::get('/allrate',[OccupancyController::class,'showAllOccupancyRate']); //overall occupancy rate ng laaht ng units
  
});
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/payments/{id}', [PaymentController::class, 'show']);
// Create a source (POST)
Route::post('/payments/source', [PaymentController::class, 'createSource']);
// Create a payment (POST)
Route::post('/payments/pay', [PaymentController::class, 'createPayment']);

});


Route::post('/login', [AuthController::class, 'apiLogin']);



Route::post('/bookings', [BookingController::class, 'store']);

Route::get('/applications', [ApplicationController::class, 'index']);
Route::get('/applications/{id}', [ApplicationController::class, 'show']);
Route::post('/applications', [ApplicationController::class, 'store']);
Route::put('/applications/{id}', [ApplicationController::class, 'update']);
Route::post('/applications/{id}/approve', [ApplicationController::class, 'approve']);
Route::post('/applications/{id}/reject', [ApplicationController::class, 'reject']);
Route::post('/applications/{id}/archive', [ApplicationController::class, 'archive']);
Route::get('/applications/archived', [ApplicationController::class, 'viewArchive']);




Route::middleware('auth:sanctum')->group(function () {
    // 📄 Get all contracts (list)
    Route::get('/contracts', [ContractController::class, 'index']);
    // 🖊 Update contract
    Route::put('/contracts/{id}', [ContractController::class, 'update']);
    // 🗑 Soft delete contract
    Route::delete('/contracts/{id}', [ContractController::class, 'destroy']);
    // 🔁 Restore deleted contract
    Route::post('/contracts/restore/{id}', [ContractController::class, 'restore']);
});
