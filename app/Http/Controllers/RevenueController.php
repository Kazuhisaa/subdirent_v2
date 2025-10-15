<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RevenueService;
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

}
