<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RevenueService;
use Carbon\Carbon;

class RevenueController extends Controller
{
    //

    protected $revenueservice;

    public function __construct(RevenueService $revenueservice) {
        $this->revenueservice = $revenueservice;
    }

    public function showAverage(){
       $average = $this->revenueservice->getAverage();

       return response()->json($average);
    }
   
   public function showPeakMonth(){
      $peakMonth = $this->revenueservice->getPeakmonth();
      return response()->json($peakMonth);
    }
    
    public function showTotalRevenue(){
       $total_revenue = $this->revenueservice->getTotalRevenue();
       return response()->json($total_revenue);
    }
   
    public function store(Request $request, RevenuePredictionService $service)
    {


        // 1. Validate input
        $data = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'active_contracts' => 'required|integer|min:0',
            'new_contracts' => 'required|integer|min:0',
            'expired_contracts' => 'required|integer|min:0',
            'prev_month_revenue' => 'required|numeric|min:0',
            'monthly_revenue' => 'required|numeric|min:0',
        ]);

      
        $revenue = $service->createRevenue($data);
        return response()->json([
            'message' => 'Revenue stored successfully',
            'data' => $revenue
        ]);
    }


    public function addrevenue(Request $request){
        $data = $request->validate([
        'increment' => 'required|numeric'
    ]);

            $fromDate = Carbon::now()->startOfMonth()->toDateString(); 

          $incrementValue = $data['increment'];

        $updatedCount = $this->revenueservice->incrementRevenueFromDate($fromDate, $incrementValue);
            return response()->json([
            'message' => "Monthly revenue incremented successfully",
            'updated_records' => $updatedCount
        ]);
    }

    
public function addNewContract(Request $request)
{
    // 1️⃣ Validate request
    $data = $request->validate([
        'contract' => 'required|numeric'
    ]);

    // 2️⃣ Get current date (start of month para pareho sa year_month format mo)
    $fromDate = Carbon::now()->startOfMonth()->toDateString(); // e.g. "2025-10-01"

    // 3️⃣ Get value from input
    $contractCount = $data['contract'];

    // 4️⃣ Call service method
    $updatedCount = $this->revenueservice->addnewContract($fromDate, $contractCount);

    // 5️⃣ Return JSON response
    return response()->json([
        'message' => 'New contracts successfully added',
        'updated_records' => $updatedCount
    ]);
}
   

}
