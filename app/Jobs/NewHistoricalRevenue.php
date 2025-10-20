<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable; // ✅ Ito ang tamang Queueable trait path
use Illuminate\Support\Facades\Log;
use App\Models\RevenuePrediction;
use Carbon\Carbon;

class NewHistoricalRevenue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable; // ✅ Use the correct trait

    public function __construct()
    {
        //
    }

   public function handle(): void
{
    $latest = RevenuePrediction::latest()->first();

    if (!$latest) {
        Log::warning('No previous revenue record found.');
        return;
    }

    $nextmonth = $latest->month == 12 ? 1 : $latest->month + 1;
    $nextyear = $latest->month == 12 ? $latest->year + 1 : $latest->year;

    $prev_month_revenue = $latest->monthly_revenue; 
    $active_contracts = $latest->active_contracts;
    $new_contracts = 0;
    $expired_contracts = 0;
    $monthly_revenue = 0;

    $year_month = Carbon::create($nextyear, $nextmonth, 1)->format('Y-m-d');

  $newRow= RevenuePrediction::create([
        'year' => $nextyear,
        'month' => $nextmonth,
        'active_contracts' => $active_contracts,
        'new_contracts' => $new_contracts,
        'expired_contracts' => $expired_contracts,
        'prev_month_revenue' => $prev_month_revenue,
        'monthly_revenue' => $monthly_revenue,
        'year_month' => $year_month
    ]);

    Log::info('Created revenue:', $newRow->toArray());

}
}