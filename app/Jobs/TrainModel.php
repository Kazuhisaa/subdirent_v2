<?php

namespace App\Jobs;

use App\Services\RevenuePredictionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Queueable;

class TrainModel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $service;

    public function __construct(RevenuePredictionService $service)
    {
        $this->service = $service; // Laravel DI automatic dito
    }

    public function handle()
    {
        $this->service->train();
    }
}
