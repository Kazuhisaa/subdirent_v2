<?php
namespace App\Services;

use App\Models\RevenuePrediction;
use Phpml\Regression\LeastSquares;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Metric\Regression;
use Phpml\Dataset\ArrayDataset;
use Phpml\ModelManager;


class RevenuePredictionService
{
    protected $revenueprediction;

    public function __construct(RevenuePrediction $revenueprediction) {
        $this->revenueprediction = $revenueprediction;
    }

    public function predictmonthly() {
         $modelManager = new ModelManager();

         $model = $modelManager->restoreFromFile(storage_path('App/Models/revenue_prediction.model'));
         $retrievefeatureData = RevenuePrediction::all();
          $length = count($retrievefeatureData)-1;
          $next_month =  $retrievefeatureData[$length]['month']+1;
          
         $active_contracts  = [
            $retrievefeatureData[$length]['year'],
            $next_month,
            $retrievefeatureData[$length]['active_contracts'],
            $retrievefeatureData[$length]['new_contracts'],
            $retrievefeatureData[$length]['default_rate'],
            $retrievefeatureData[$length]['installment_amount'],
            $retrievefeatureData[$length]['prev_month_revenue']
         ];
         

       return   $model->predict($active_contracts);


      
      
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
              $dataset[$i]['default_rate'],
              $dataset[$i]['installment_amount'],
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
