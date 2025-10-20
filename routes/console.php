<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\TrainModel;
use App\Jobs\NewHistoricalRevenue;

// Sample command
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the job
Schedule::call(function () {
    dispatch(new TrainModel());
})->monthlyOn(1, '00:00');

Schedule::call(function () {
    dispatch(new NewHistoricalRevenue());
    Log::info('Dispatched NewHistoricalRevenue job successfully at ' . now());
})->everyMinute();