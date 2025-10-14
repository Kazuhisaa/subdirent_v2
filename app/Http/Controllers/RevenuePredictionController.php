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
    // Fetch all revenue history
    $rows = RevenuePrediction::select('year', 'month', 'monthly_revenue')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

    $historical = $rows->map(function ($r) {
        $month = str_pad((string) $r->month, 2, '0', STR_PAD_LEFT);
        $year_month = "{$r->year}-{$month}";
        $revenue = is_numeric($r->monthly_revenue) ? (float) $r->monthly_revenue : $r->monthly_revenue;

        return (object)[
            'year_month' => $year_month,
            'monthly_revenue' => $revenue,
            'year' => $r->year,
            'month' => (int) $r->month,
        ];
    });

    // Fetch counts
    $totalTenants = Tenant::count();
    $totalUnits = Unit::count();

    // Pass all variables to the Blade
    return view('admin.analytics', compact('historical', 'totalTenants', 'totalUnits'));
}


}
