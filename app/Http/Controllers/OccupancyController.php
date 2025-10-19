<?php

namespace App\Http\Controllers;
use App\Services\OccupancyService;
use Illuminate\Http\Request;

class OccupancyController extends Controller
{
    //

    protected $occupancyservice;
       public function __construct(OccupancyService $occupancyservice) {
        $this->occupancyservice = $occupancyservice;
    }
  


    public function showAll(){
        $counts = $this->occupancyservice->getAll();
        return response()->json($counts);
    }

    public function showByLocation(){
        $units = $this->occupancyservice->getUnitsByLocation();
        return response()->json($units);
    }

    public function showOccupancyRateByLocation(){
        $occupancyRates = $this->occupancyservice->getOccupancyRateByLocation();
           return response()->json($occupancyRates);

    }


    public function showAllOccupancyRate(){
        $occupancyRates = $this->occupancyservice->getAllOccupancyRate();
        return response()->json($occupancyRates);
    }
}
