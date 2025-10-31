<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Models\RevenuePrediction;




use Carbon\Carbon;

class RevenueService {

    protected $revenueprediction;
    public $historical_data;



    public function __construct(RevenuePrediction $revenueprediction) {
        $this->revenueprediction = $revenueprediction;
        $this->historical_data = RevenuePrediction::all();
    }

   
   public function createRevenue(array $data)
    {
        return RevenuePrediction::create($data);
    }
  
    public function getlatestRevenue(){
         $retrievefeatureData = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get(['year_month', 'monthly_revenue'])->reverse()->values();
        return $retrievefeatureData;
    }
    public function incrementRevenueFromDate($date,$amount){
         RevenuePrediction::where('year_month', '>=', $date)
            ->increment('monthly_revenue', $amount);
    }

    
     

public function addnewContract($date, $contract)
{
    return RevenuePrediction::where('year_month', '>=', $date)
        ->update([
            'active_contracts' => DB::raw("active_contracts + $contract"),
            'new_contracts' => DB::raw("new_contracts + $contract")
        ]);
}

public function subtractContract($date,$contract)
{

    return RevenuePrediction::where('year_month','>=',$date)->update([
        'active_contracts' => DB::raw("active_contracts - $contract"),
        'expired_contracts' =>  DB::raw("expired_contracts + $contract")
    ]);
}


    public function getAverage(){
        return $this->historical_data->avg('monthly_revenue');
    }
    
   
    public function getPeakmonth(){
        $highestMonthsPerYear = RevenuePrediction::select('year', 'month', 'monthly_revenue')
        ->orderBy('year')
    ->orderByDesc('monthly_revenue')
    ->get()
    ->groupBy('year')
    ->map(function($months) {
        // kunin yung month na may highest revenue sa year na iyon
        return $months->first();
    });
$frequency = $highestMonthsPerYear->countBy('month');

// kunin kung alin ang may pinakamataas na bilang
$maxCount = $frequency->max();
$topMonthNum = $frequency->flip()[$maxCount];

// i-convert yung month number to name (e.g. 12 -> December)
$monthName = date("F", mktime(0, 0, 0, $topMonthNum, 1));

// final readable output
return [
    'peak_month' => $monthName,
    ];

    }



    public function getTotalRevenue(){
        return $this->historical_data->sum('monthly_revenue');
    }


}
