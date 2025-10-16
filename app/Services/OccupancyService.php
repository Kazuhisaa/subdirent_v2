<?php
namespace App\Services;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class OccupancyService{

    protected $units;

    public function __construct(Unit $units) {
        $this->units = $units;
    }
   


    public function getAll(){
       $counts = Unit::select('status', DB::raw('COUNT(*) as total'))
              ->groupBy('status')
              ->get();
  
        return $counts;
    }
   

public function getUnitsByLocation() {
    $units = Unit::select('location', DB::raw('COUNT(*) as total'))
                 ->where('status', '!=', 'completed')
                 ->groupBy('location')
                 ->get();

    return $units;
}



public function getOccupancyRateByLocation()
{
    $rates = Unit::select(
                'location',
                DB::raw('
                    SUM(CASE WHEN status = "Rented" THEN 1 ELSE 0 END) AS rented_count,
                    SUM(CASE WHEN status NOT IN ("Owned", "Completed") THEN 1 ELSE 0 END) AS total_units,
                    ROUND(
                        (SUM(CASE WHEN status = "Rented" THEN 1 ELSE 0 END) /
                         NULLIF(SUM(CASE WHEN status NOT IN ("Owned", "Completed") THEN 1 ELSE 0 END), 0)) * 100,
                        2
                    ) AS occupancy_rate
                ')
            )
            ->groupBy('location')
            ->get();

    return $rates;
}


public function getAllOccupancyRate(){
     $rates = Unit::select(
                DB::raw('
                    SUM(CASE WHEN status = "Rented" THEN 1 ELSE 0 END) AS rented_count,
                    SUM(CASE WHEN status NOT IN ("Owned", "Completed") THEN 1 ELSE 0 END) AS total_units,
                    ROUND(
                        (SUM(CASE WHEN status = "Rented" THEN 1 ELSE 0 END) /
                         NULLIF(SUM(CASE WHEN status NOT IN ("Owned", "Completed") THEN 1 ELSE 0 END), 0)) * 100,
                        2
                    ) AS occupancy_rate
                ')
            )
   ->get();

    return $rates;
}

}