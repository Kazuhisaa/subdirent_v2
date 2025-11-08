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

    // Kunin latest 12 months data
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
    $sumSquares = array_sum(array_map(fn($r) => pow($r - $meanResidual, 2), $residuals));
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

    // Convert retrievedates to human-readable format
    $humanReadableDates = [];
    foreach ($retrievedates as $d) {
        $humanReadableDates[] = [
            'date' => Carbon::parse($d['year_month'])->format('F Y'),
            'monthly_revenue' => $d['monthly_revenue']
        ];
    }

    return [
        "date" => $humanReadableDates,
        "prediction" => [
            "prediction_date" => Carbon::parse($retrievedates[$length]['year_month'])->addMonth(1)->format('F Y'),
            "revenue_prediction" => round($predictionValue, 2),
            "model_Accuracy" => $modelaccurancy,
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
       'Accuracy' => number_format($r2_percent, 2) . '%',
        'MAPE' => round($mape, 2) . '%'
    ];
}
public function predictAnnualyV2()
{
    $dataset = $this->revenueprediction::select('year', 'monthly_revenue')->get();

    // 🧮 Group by year to get total revenue per year
    $annualData = [];
    foreach ($dataset as $data) {
        if (!isset($annualData[$data['year']])) {
            $annualData[$data['year']] = 0;
        }
        $annualData[$data['year']] += $data['monthly_revenue'];
    }

    // 🧠 Prepare training data (completed years only)
    $features = [];
    $targets = [];
    $currentYear = Carbon::now()->year;

    foreach ($annualData as $year => $totalRevenue) {
        if ($year < $currentYear) {
            $features[] = [$year];
            $targets[] = $totalRevenue;
        }
    }

    // ✅ Train linear regression
    $regression = new LeastSquares();
    $regression->train($features, $targets);

    // 📅 Predict current year's total revenue
    $predictedRevenue = $regression->predict([$currentYear]);

    // --- compute residuals for confidence interval ---
    $residuals = [];
    foreach ($features as $i => $f) {
        $pred_i = $regression->predict($f);
        $residuals[] = $targets[$i] - $pred_i;
    }

    $n = count($residuals);
    $meanResidual = array_sum($residuals) / $n;
    $sumSquares = array_sum(array_map(fn($r) => pow($r - $meanResidual, 2), $residuals));
    $s = sqrt($sumSquares / ($n - 1));
    $SE = $s / sqrt($n);
    $df = $n - 1;

    // t-value for 95% CI (approximate for small sample)
    $tValues = [
        5 => 2.571, 6 => 2.447, 7 => 2.365, 8 => 2.306,
        9 => 2.262, 10 => 2.228, 11 => 2.201, 12 => 2.179
    ];
    $t = $tValues[$df] ?? 1.96;
    $ME = $t * $SE;

    $lower = $predictedRevenue - $ME;
    $upper = $predictedRevenue + $ME;

    // --- Compute R² for accuracy ---
    $meanTarget = array_sum($targets) / $n;
    $ssTotal = array_sum(array_map(fn($y) => pow($y - $meanTarget, 2), $targets));
    $ssRes = array_sum(array_map(fn($i) => pow($targets[$i] - $regression->predict($features[$i]), 2), array_keys($targets)));
    $r2 = 1 - ($ssRes / $ssTotal);
    $r2Percent = round($r2 * 100, 2);

    // --- Compute MAPE ---
    $absPercentageErrors = [];
    foreach ($targets as $i => $actual) {
        if ($actual != 0) {
            $absPercentageErrors[] = abs(($actual - $regression->predict($features[$i])) / $actual);
        }
    }
    $mape = round((array_sum($absPercentageErrors) / count($absPercentageErrors)) * 100, 2);

    // 🔹 Filter annual revenues to exclude current year
    $filteredAnnualRevenues = [];
    foreach ($annualData as $year => $total) {
        if ($year < $currentYear) {
            $filteredAnnualRevenues[$year] = $total;
        }
    }

    return [
        "trained_years" => array_keys($filteredAnnualRevenues),
        "annual_revenues" => array_values($filteredAnnualRevenues),
        "predicted_year" => $currentYear,
        "predicted_total_revenue" => round($predictedRevenue, 2),
        "confidence_interval" => [
            "lower" => round($lower, 2),
            "upper" => round($upper, 2),
            "confidence_level" => "95%"
        ],
        "Accuracy" => $r2Percent . "%",
        "MAPE" => $mape . "%"
    ];
}
public function predictQuarterlyV2()
{
    $dataset = $this->revenueprediction::orderBy('year_month', 'asc')->get();

    $quarterlyData = [];
    foreach ($dataset as $data) {
        $quarter = ceil($data['month'] / 3);
        $key = $data['year'] . '-Q' . $quarter;

        if (!isset($quarterlyData[$key])) {
            $quarterlyData[$key] = [
                'year' => $data['year'],
                'quarter' => $quarter,
                'total_revenue' => 0,
                'features' => [
                    'active_contracts' => 0,
                    'new_contracts' => 0,
                    'expired_contracts' => 0,
                    'prev_month_revenue' => 0
                ]
            ];
        }

        $quarterlyData[$key]['total_revenue'] += $data['monthly_revenue'];
        $quarterlyData[$key]['features']['active_contracts'] += $data['active_contracts'];
        $quarterlyData[$key]['features']['new_contracts'] += $data['new_contracts'];
        $quarterlyData[$key]['features']['expired_contracts'] += $data['expired_contracts'];
        $quarterlyData[$key]['features']['prev_month_revenue'] = $data['prev_month_revenue'];
    }

    // Convert associative array to sequential array
    $quarterlyData = array_values($quarterlyData);

    $currentYear = Carbon::now()->year;
    $currentQuarter = ceil(Carbon::now()->month / 3);

    // Keep only last 12 completed quarters
    $completedQuarters = array_filter($quarterlyData, function($q) use ($currentYear, $currentQuarter) {
        return ($q['year'] < $currentYear || ($q['year'] == $currentYear && $q['quarter'] < $currentQuarter));
    });
    $last12Quarters = array_slice($completedQuarters, -12, 12, true);

    // Prepare training data
    $features = [];
    $targets = [];
    foreach ($last12Quarters as $q) {
        $features[] = [
            $q['year'],
            $q['quarter'],
            $q['features']['active_contracts'],
            $q['features']['new_contracts'],
            $q['features']['expired_contracts'],
            $q['features']['prev_month_revenue']
        ];
        $targets[] = $q['total_revenue'];
    }

    // Train model
    $regression = new LeastSquares();
    $regression->train($features, $targets);

    // Predict next quarter
    $lastQuarter = end($last12Quarters);
    $nextQuarter = ($lastQuarter['quarter'] == 4) ? 1 : $lastQuarter['quarter'] + 1;
    $nextYear = ($nextQuarter == 1) ? $lastQuarter['year'] + 1 : $lastQuarter['year'];

    $predictedRevenue = $regression->predict([
        $nextYear,
        $nextQuarter,
        $lastQuarter['features']['active_contracts'],
        $lastQuarter['features']['new_contracts'],
        $lastQuarter['features']['expired_contracts'],
        $lastQuarter['features']['prev_month_revenue']
    ]);

    // Compute residuals for confidence interval
    $residuals = [];
    foreach ($last12Quarters as $q) {
        $pred = $regression->predict([
            $q['year'],
            $q['quarter'],
            $q['features']['active_contracts'],
            $q['features']['new_contracts'],
            $q['features']['expired_contracts'],
            $q['features']['prev_month_revenue']
        ]);
        $residuals[] = $q['total_revenue'] - $pred;
    }

    $n = count($residuals);
    $meanResidual = array_sum($residuals) / $n;
    $sumSquares = array_sum(array_map(fn($r) => pow($r - $meanResidual, 2), $residuals));
    $s = sqrt($sumSquares / ($n - 1));
    $SE = $s / sqrt($n);
    $t = 2.201; // conservative t-value
    $ME = $t * $SE;
    $lower = $predictedRevenue - $ME;
    $upper = $predictedRevenue + $ME;

    // Human-readable month ranges for last 12 quarters
    $quartersWithDates = [];
    foreach ($last12Quarters as $q) {
        $startMonth = ($q['quarter'] - 1) * 3 + 1;
        $endMonth = $q['quarter'] * 3;
        $startDate = Carbon::create($q['year'], $startMonth, 1)->format('F Y');
        $endDate = Carbon::create($q['year'], $endMonth, 1)->format('F Y');
        $quartersWithDates[] = $startDate . ' – ' . $endDate;
    }

    // Human-readable month range for predicted quarter
    $predStartMonth = ($nextQuarter - 1) * 3 + 1;
    $predEndMonth = $nextQuarter * 3;
    $predictedQuarterDate = Carbon::create($nextYear, $predStartMonth, 1)->format('F Y') .
                            ' – ' . Carbon::create($nextYear, $predEndMonth, 1)->format('F Y');

    // Compute R² and MAPE
    $meanTarget = array_sum($targets) / $n;
    $ssTotal = array_sum(array_map(fn($y) => pow($y - $meanTarget, 2), $targets));
    $ssRes = array_sum(array_map(fn($y, $pred) => pow($y - $pred, 2), $targets, array_map(function($f) use ($regression){
        return $regression->predict($f);
    }, $features)));
    $r2 = 1 - ($ssRes / $ssTotal);
    $r2Percent = round($r2 * 100, 2);

    $mape = round(array_sum(array_map(fn($y, $pred) => abs(($y - $pred)/$y), $targets, array_map(function($f) use ($regression){
        return $regression->predict($f);
    }, $features))) / $n * 100, 2);

    return [
        "quarters" => $quartersWithDates,
        "quarterly_revenues" => array_map(fn($q) => $q['total_revenue'], $last12Quarters),
        "predicted_quarter" => $predictedQuarterDate,
        "predicted_revenue" => round($predictedRevenue, 2),
        "confidence_interval" => [
            "lower" => round($lower, 2),
            "upper" => round($upper, 2),
            "confidence_level" => "95%"
        ],
        "Accuracy" => $r2Percent . "%",
        "MAPE" => $mape . "%"
    ];
}



}


