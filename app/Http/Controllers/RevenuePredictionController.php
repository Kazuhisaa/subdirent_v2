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
    public function showPrediction(){
      $predict = $this->revenuepredictionservice->predictmonthly();

      return response()->json($predict);
    }



    public function trainModel(){
      $this->revenuepredictionservice->train();

      return response()->json("your model succesfully trained");
    }

}
