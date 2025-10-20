<?php

namespace App\Jobs;

use App\Services\RevenuePredictionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;

class TrainModel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function handle()
    {
        

        // Resolve service here instead of constructor
        app(RevenuePredictionService::class)->train();
    }
}
