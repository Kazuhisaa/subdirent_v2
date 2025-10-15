<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RevenuePredictionService;
  use Illuminate\Support\Facades\DB;
  use App\Models\RevenuePrediction;
  use Illuminate\Support\Collection;
  use App\Models\Tenant;
  use App\Models\Unit;


class RevenuePredictionController extends Controller
{
    //
   
  protected $revenuepredictionservice;

   public function __construct(RevenuePredictionService $revenuepredictionservice) {
    $this->revenuepredictionservice = $revenuepredictionservice;
  }
    public function showPredictionMonth(){
      $predict = $this->revenuepredictionservice->predictmonthly();

      return response()->json($predict);
    }

     public function showPredictionQuarter(){
      $predict = $this->revenuepredictionservice->predictQuarterly();

      return response()->json($predict);
    }
      
    public function showPredictionAnnual()
    {
       $predict = $this->revenuepredictionservice->predictAnnualy();

      return response()->json($predict);
    }

    public function trainModel(){
      $this->revenuepredictionservice->train();

      return response()->json("your model succesfully trained");
    }

public function showAnalyticsPage()
{
    // Fetch counts
    $totalTenants = Tenant::count();
    $totalUnits = Unit::count();

    // Pass only counts to Blade. The chart will fetch data via API.
    return view('admin.analytics', compact('totalTenants', 'totalUnits'));
}



}
