<?php
namespace App\Services;

use App\Models\RevenuePrediction;
use Phpml\Regression\LeastSquares;
use Phpml\ModelManager;
use Carbon\Carbon;

class RevenuePredictionService
{
    protected $revenueprediction;

    public function __construct(RevenuePrediction $revenueprediction) {
        $this->revenueprediction = $revenueprediction;
    }

    /** -----------------------------
     * 📅 MONTHLY PREDICTION
     * ----------------------------- */
    public function predictMonthly()
    {
        $modelManager = new ModelManager();
        $model = $modelManager->restoreFromFile(storage_path('App/Models/revenue_prediction.model'));
        $modelaccurancy = $this->evaluateAccuracy($model);
        $retrievefeatureData = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get()->reverse()->values();
        $retrievedates = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get(['year_month', 'monthly_revenue'])->reverse()->values();

        $length = count($retrievefeatureData) - 1;
        $next_month = ($retrievefeatureData[$length]['month'] == 12) ? 1 : ($retrievefeatureData[$length]['month'] + 1);
        $next_year = ($retrievefeatureData[$length]['month'] == 12) ? ($retrievefeatureData[$length]['year'] + 1) : ($retrievefeatureData[$length]['year']);

        $active_contracts = [
            $next_year,
            $next_month,
            $retrievefeatureData[$length]['active_contracts'],
            $retrievefeatureData[$length]['new_contracts'],
            $retrievefeatureData[$length]['expired_contracts'],
            $retrievefeatureData[$length]['prev_month_revenue']
        ];

        $predictionValue = $model->predict($active_contracts);

        // --- compute residuals for confidence ---
        $residuals = [];
        foreach ($retrievefeatureData as $data) {
            $features_i = [
                $data['year'],
                $data['month'],
                $data['active_contracts'],
                $data['new_contracts'],
                $data['expired_contracts'],
                $data['prev_month_revenue']
            ];
            $pred_i = $model->predict($features_i);
            $residuals[] = $data['monthly_revenue'] - $pred_i;
        }

        $n = count($residuals);
        $meanResidual = array_sum($residuals) / $n;
        $sumSquares = 0;
        foreach ($residuals as $r) {
            $sumSquares += pow($r - $meanResidual, 2);
        }
        $s = sqrt($sumSquares / ($n - 1));
        $SE = $s / sqrt($n);
        $df = $n - 1;
        $tValues = [
            5 => 2.571, 6 => 2.447, 7 => 2.365, 8 => 2.306,
            9 => 2.262, 10 => 2.228, 11 => 2.201, 12 => 2.179,
            15 => 2.131, 20 => 2.086, 30 => 2.042
        ];
        $t = $tValues[$df] ?? 1.96;
        $ME = $t * $SE;

        $lower = $predictionValue - $ME;
        $upper = $predictionValue + $ME;

        $newDate = Carbon::parse($retrievedates[$length]['year_month'])->addMonth(1)->format('Y-m-d');

        return [
            "date" => $retrievedates,
            "prediction" => [
                "prediction_date" => $newDate,
                "revenue_prediction" => round($predictionValue, 2),
                 "model Accurancy" => $modelaccurancy,
                "confidence_interval" => [
                    "lower" => round($lower, 2),
                    "upper" => round($upper, 2),
                    "confidence_level" => "95%"
                ],
                // Developer data (hidden in UI)
                "debug" => [
                    "margin_of_error" => $ME,
                    "standard_error" => $SE,
                    "sample_size" => $n,
                    "t_value" => $t
                ]
            ]
        ];
    }


    /** -----------------------------
     * 🕒 QUARTERLY PREDICTION
     * ----------------------------- */
    public function predictQuarterly()
    {
        $modelManager = new ModelManager();
        $model = $modelManager->restoreFromFile(storage_path('App/Models/revenue_prediction.model'));

        $data = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get()->reverse()->values();
        $dates = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get(['year_month', 'monthly_revenue'])->reverse()->values();

        $length = count($data) - 1;
        $next_year = ($data[$length]['month'] + 3 > 12)
            ? $data[$length]['year'] + 1
            : $data[$length]['year'];
        $next_month = ($data[$length]['month'] + 3 > 12)
            ? $data[$length]['month'] + 3 - 12
            : $data[$length]['month'] + 3;

        $active_contracts = [
            $next_year,
            $next_month,
            $data[$length]['active_contracts'],
            $data[$length]['new_contracts'],
            $data[$length]['expired_contracts'],
            $data[$length]['prev_month_revenue']
        ];

        $predictionValue = $model->predict($active_contracts);

        // reuse residual computation for confidence
        $residuals = [];
        foreach ($data as $d) {
            $features = [
                $d['year'],
                $d['month'],
                $d['active_contracts'],
                $d['new_contracts'],
                $d['expired_contracts'],
                $d['prev_month_revenue']
            ];
            $pred = $model->predict($features);
            $residuals[] = $d['monthly_revenue'] - $pred;
        }

        $n = count($residuals);
        $meanResidual = array_sum($residuals) / $n;
        $sumSquares = array_sum(array_map(fn($r) => pow($r - $meanResidual, 2), $residuals));
        $s = sqrt($sumSquares / ($n - 1));
        $SE = $s / sqrt($n);
        $df = $n - 1;
        $tValues = [
            5 => 2.571, 6 => 2.447, 7 => 2.365, 8 => 2.306,
            9 => 2.262, 10 => 2.228, 11 => 2.201, 12 => 2.179
        ];
        $t = $tValues[$df] ?? 1.96;
        $ME = $t * $SE;
        $lower = $predictionValue - $ME;
        $upper = $predictionValue + $ME;

        $newDate = Carbon::parse($dates[$length]['year_month'])->addMonths(3)->format('Y-m-d');

        return [
            "date" => $dates,
            "prediction" => [
                "prediction_date" => $newDate,
                "revenue_prediction" => round($predictionValue, 2),
                "confidence_interval" => [
                    "lower" => round($lower, 2),
                    "upper" => round($upper, 2),
                    "confidence_level" => "95%"
                ],
                "debug" => [
                    "margin_of_error" => $ME,
                    "standard_error" => $SE,
                    "sample_size" => $n,
                    "t_value" => $t
                ]
            ]
        ];
    }


    /** -----------------------------
     * 📆 ANNUAL PREDICTION
     * ----------------------------- */
    public function predictAnnualy()
    {
        $modelManager = new ModelManager();
        $model = $modelManager->restoreFromFile(storage_path('App/Models/revenue_prediction.model'));

        $data = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get()->reverse()->values();
        $dates = RevenuePrediction::orderBy('year_month', 'desc')->take(12)->get(['year_month', 'monthly_revenue'])->reverse()->values();

        $length = count($data) - 1;
        $next_year = $data[$length]['year'] + 1;

        $active_contracts = [
            $next_year,
            $data[$length]['month'],
            $data[$length]['active_contracts'],
            $data[$length]['new_contracts'],
            $data[$length]['expired_contracts'],
            $data[$length]['prev_month_revenue']
        ];

        $predictionValue = $model->predict($active_contracts);

        // reuse residual-based confidence logic
        $residuals = [];
        foreach ($data as $d) {
            $features = [
                $d['year'],
                $d['month'],
                $d['active_contracts'],
                $d['new_contracts'],
                $d['expired_contracts'],
                $d['prev_month_revenue']
            ];
            $pred = $model->predict($features);
            $residuals[] = $d['monthly_revenue'] - $pred;
        }

        $n = count($residuals);
        $meanResidual = array_sum($residuals) / $n;
        $sumSquares = array_sum(array_map(fn($r) => pow($r - $meanResidual, 2), $residuals));
        $s = sqrt($sumSquares / ($n - 1));
        $SE = $s / sqrt($n);
        $df = $n - 1;
        $t = 2.201;
        $ME = $t * $SE;
        $lower = $predictionValue - $ME;
        $upper = $predictionValue + $ME;

        $newDate = Carbon::parse($dates[$length]['year_month'])->addMonths(12)->format('Y-m-d');

        return [
            "date" => $dates,
            "prediction" => [
                "prediction_date" => $newDate,
                "revenue_prediction" => round($predictionValue, 2),
                "confidence_interval" => [
                    "lower" => round($lower, 2),
                    "upper" => round($upper, 2),
                    "confidence_level" => "95%"
                ],
                "debug" => [
                    "margin_of_error" => $ME,
                    "standard_error" => $SE,
                    "sample_size" => $n,
                    "t_value" => $t
                ]
            ]
        ];
    }


    /** -----------------------------
     * 🧠 TRAIN MODEL
     * ----------------------------- */
    public function train()
    {
        $dataset = $this->revenueprediction::all();
        $targets = [];
        $features = [];

        foreach ($dataset as $data) {
            $features[] = [
                $data['year'],
                $data['month'],
                $data['active_contracts'],
                $data['new_contracts'],
                $data['expired_contracts'],
                $data['prev_month_revenue']
            ];
            $targets[] = $data['monthly_revenue'];
        }

        $regression = new LeastSquares();
        $regression->train($features, $targets);
        $modelManager = new ModelManager();
        $modelManager->saveToFile($regression, storage_path('app/Models/revenue_prediction.model'));
    }



    public function evaluateAccuracy($model)
{
   

    // Prepare dataset again (same structure as training)
    $dataset = $this->revenueprediction::all();
    $targets = [];
    $features = [];

    foreach ($dataset as $data) {
        $features[] = [
            $data['year'],
            $data['month'],
            $data['active_contracts'],
            $data['new_contracts'],
            $data['expired_contracts'],
            $data['prev_month_revenue']
        ];
        $targets[] = $data['monthly_revenue'];
    }

    // Evaluate
    $predictions = [];
    foreach ($features as $f) {
        $predictions[] = $model->predict($f);
    }

    // Compute R²
    $meanTarget = array_sum($targets) / count($targets);
    $ss_total = array_sum(array_map(fn($y) => pow($y - $meanTarget, 2), $targets));
    $ss_res = 0;
    for ($i = 0; $i < count($targets); $i++) {
        $ss_res += pow($targets[$i] - $predictions[$i], 2);
    }
    $r2 = 1 - ($ss_res / $ss_total);

    // Compute MAPE
    $abs_percentage_errors = [];
    for ($i = 0; $i < count($targets); $i++) {
        if ($targets[$i] != 0) {
            $abs_percentage_errors[] = abs(($targets[$i] - $predictions[$i]) / $targets[$i]);
        }
    }
    $mape = array_sum($abs_percentage_errors) / count($abs_percentage_errors) * 100;
     $r2_percent = $r2 * 100;
    return [
       'Accuracy(r2Score)' => number_format($r2_percent, 2) . '%',
        'mape' => round($mape, 2) . '%'
    ];
}

}


