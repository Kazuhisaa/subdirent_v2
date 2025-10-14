<?php
namespace App\Services;

use App\Models\RevenuePrediction;
use Phpml\Regression\LeastSquares;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Metric\Regression;
use Phpml\Dataset\ArrayDataset;
use Phpml\ModelManager;
use Carbon\Carbon;



class RevenuePredictionService
{
    protected $revenueprediction;

    public function __construct(RevenuePrediction $revenueprediction) {
        $this->revenueprediction = $revenueprediction;
    }

    public function predictmonthly() {
         $modelManager = new ModelManager();

         $model = $modelManager->restoreFromFile(storage_path('App/Models/revenue_prediction.model'));
         $retrievefeatureData = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get()->reverse()->values();
         $retrievedates = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get(['year_month', 'monthly_revenue'])->reverse() ->values();

        
          $length = count($retrievefeatureData)-1;
         $next_month = ($retrievefeatureData[$length]['month'] == 12) ? 1 : ($retrievefeatureData[$length]['month'] + 1);
         $next_year = ($retrievefeatureData[$length]['month'] == 12) ? ($retrievefeatureData[$length]['year']) : ($retrievefeatureData[$length]['year'] + 1);
          
         $active_contracts  = [
            $next_year,
            $next_month,
            $retrievefeatureData[$length]['active_contracts'],
            $retrievefeatureData[$length]['new_contracts'],
            $retrievefeatureData[$length]['expired_contracts'],
            $retrievefeatureData[$length]['prev_month_revenue']
         ];

         $lastDate = $retrievedates[count($retrievedates) - 1]['year_month'];

        $newDate = Carbon::parse($lastDate)  // parse string to Carbon
             ->addMonth(1)           // add 1 month
             ->format('Y-m-d');   
            
    $prediction = [ "prediction_date"=>$newDate,"revenue_prediction" =>$model->predict($active_contracts)];
     
       return  ["date" => $retrievedates,"prediction" => $prediction];
    }

  

    public function predictQuarterly(){
         $modelManager = new ModelManager();
         $model = $modelManager->restoreFromFile(storage_path('App/Models/revenue_prediction.model'));
         $retrievefeatureData = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get()->reverse()->values();
         $retrievedates = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get(['year_month', 'monthly_revenue'])->reverse()->values();
        $length = count($retrievefeatureData)-1;
        $next_year =  ($retrievefeatureData[$length]['month'] + 3 > 12) ?  ($retrievefeatureData[$length]['year']+1) : ($retrievefeatureData[$length]['year']);
        $next_month = ($retrievefeatureData[$length]['month'] + 3 > 12) ? ($retrievefeatureData[$length]['month'] + 3 - 12) : ($retrievefeatureData[$length]['month'] + 3);
         $active_contracts  = [
            $next_year,
            $next_month,
            $retrievefeatureData[$length]['active_contracts'],
            $retrievefeatureData[$length]['new_contracts'],
               $retrievefeatureData[$length]['expired_contracts'],
            $retrievefeatureData[$length]['prev_month_revenue']
         ];

         $lastDate = $retrievedates[count($retrievedates) - 1]['year_month'];

           $newDate = Carbon::parse($lastDate)  // parse string to Carbon
             ->addMonth(3)           // add 1 month
             ->format('Y-m-d');   
            
    $prediction = [ "prediction_date"=>$newDate,"revenue_prediction" =>$model->predict($active_contracts)];
     
       return  ["date" => $retrievedates,"prediction" => $prediction];
    }


        public function predictAnnualy(){
         $modelManager = new ModelManager();
         $model = $modelManager->restoreFromFile(storage_path('App/Models/revenue_prediction.model'));
         $retrievefeatureData = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get()->reverse()->values();
         $retrievedates = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get(['year_month', 'monthly_revenue'])->reverse()->values();
        $length = count($retrievefeatureData)-1;
        $next_year = ($retrievefeatureData[$length]['year'] + 1);
       
         $active_contracts  = [
            $next_year,
            $retrievefeatureData[$length]['month'],
            $retrievefeatureData[$length]['active_contracts'],
            $retrievefeatureData[$length]['new_contracts'],
            $dataset[$length]['expired_contracts'],
            $retrievefeatureData[$length]['prev_month_revenue']
         ];

         $lastDate = $retrievedates[count($retrievedates) - 1]['year_month'];

           $newDate = Carbon::parse($lastDate)  // parse string to Carbon
             ->addMonth(12)           // add 1 month
             ->format('Y-m-d');   
            
    $prediction = [ "prediction_date"=>$newDate,"revenue_prediction" =>$model->predict($active_contracts)];
     
       return  ["date" => $retrievedates,"prediction" => $prediction];
    }



       public function train() {
        $dataset= $this->revenueprediction::all();
        $targets =[];
        $features = [];
        $datasetlength = count($dataset)-1;
        for($i = 0; $i < $datasetlength; $i++)
        {
            $features[$i]=[ 
              $dataset[$i]['year'],
              $dataset[$i]['month'],
              $dataset[$i]['active_contracts'],
              $dataset[$i]['new_contracts'],
              $dataset[$i]['expired_contracts'],
              $dataset[$i]['prev_month_revenue']             
            ];
            
            $targets[$i] = $dataset[$i]['monthly_revenue'];
                

        }

         $regression = new LeastSquares();

         $regression->train($features,$targets);
         $modelManager = new ModelManager();

         $modelManager->saveToFile($regression,storage_path('App/Models/revenue_prediction.model'));

    }
}
