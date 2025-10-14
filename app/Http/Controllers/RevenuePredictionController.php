<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RevenuePredictionService;
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

}
