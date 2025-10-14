@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="row mb-3">
        <div class="col-12">
            <h3 class="fw-bold text-primary">ANALYTICS</h3>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase">Total Revenue</h6>
                    <h3 class="fw-bold text-primary">
                        ₱{{ number_format($historical->sum('monthly_revenue'), 0) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase">Total Units</h6>
                    <h3 class="fw-bold text-primary">{{ $totalUnits ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase">Total Tenants</h6>
                    <h3 class="fw-bold text-primary">{{ $totalTenants ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- REVENUE CHART --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0">Revenue Overview</h5>
                    <div>
                        <button class="btn btn-light btn-sm me-2 fw-semibold" id="btnMonthly">Monthly</button>
                        <button class="btn btn-light btn-sm me-2 fw-semibold" id="btnQuarterly">Quarterly</button>
                        <button class="btn btn-light btn-sm fw-semibold" id="btnAnnual">Annual</button>
                    </div>
                </div>
                <div class="card-body" style="height: 450px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const rawLabels = @json($historical->pluck('year_month')); // YYYY-MM
    const rawData = @json($historical->pluck('monthly_revenue'));

    let currentType = 'month'; // default

    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: formatLabels(rawLabels, currentType),
            datasets: [{
                label: 'Historical Revenue (₱)',
                data: rawData,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0,123,255,0.1)',
                borderWidth: 2,
                tension: 0.3,
                pointBackgroundColor: '#007bff',
                pointRadius: 4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => '₱' + ctx.parsed.y.toLocaleString()
                    }
                }
            },
            scales: {
                x: { ticks: { color: '#6c757d' } },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => '₱' + v.toLocaleString(),
                        color: '#6c757d'
                    }
                }
            }
        }
    });

    function formatLabels(labels, type) {
        switch(type) {
            case 'month':
                return labels.map(l => l); // YYYY-MM already
            case 'quarter':
                return labels.map(l => {
                    const month = parseInt(l.split('-')[1], 10);
                    const quarter = Math.ceil(month / 3);
                    return `${l.split('-')[0]}-Q${quarter}`;
                });
            case 'annual':
                return labels.map(l => l.split('-')[0]);
            default:
                return labels;
        }
    }

    async function loadPrediction(type) {
        currentType = type;
        let endpoint = {
            month: '/api/revenue/predictionMonth',
            quarter: '/api/revenue/predictionQuarter',
            annual: '/api/revenue/predictionAnnual'
        }[type];

        try {
            const res = await fetch(endpoint);
            if (!res.ok) throw new Error('API failed');
            const data = await res.json();

            const predictedRevenue = parseFloat(data.prediction.replace(/,/g, ''));
            const predictedDate = data.date; // Should match type e.g., YYYY-MM
            const lower = parseFloat(data.lower_confidence.replace(/,/g, ''));
            const upper = parseFloat(data.upper_confidence.replace(/,/g, ''));
            const confidenceLevel = data.confidence_level; // e.g., "95%"

            const allLabels = [...rawLabels, predictedDate];
            const historicalData = [...rawData, null];
            const predictedData = Array(rawData.length).fill(null).concat(predictedRevenue);
            const lowerData = Array(rawData.length).fill(null).concat(lower);
            const upperData = Array(rawData.length).fill(null).concat(upper);

            chart.data.labels = formatLabels(allLabels, type);
            chart.data.datasets = [
                {
                    label: 'Historical Revenue (₱)',
                    data: historicalData,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    tension: 0.3,
                    fill: false,
                    pointRadius: 3
                },
                {
                    label: `Predicted Revenue (₱, ${confidenceLevel})`,
                    data: predictedData,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255,193,7,0.2)',
                    pointBackgroundColor: '#ffc107',
                    borderWidth: 2,
                    pointRadius: 6,
                    showLine: false
                },
                {
                    label: 'Upper Confidence',
                    data: upperData,
                    borderColor: 'rgba(40,167,69,0.5)',
                    borderDash: [5,5],
                    fill: '+1'
                },
                {
                    label: 'Lower Confidence',
                    data: lowerData,
                    borderColor: 'rgba(40,167,69,0.5)',
                    borderDash: [5,5],
                    fill: '-1'
                }
            ];

            chart.update();
            highlightButton(type);
        } catch(err) {
            console.error('Prediction fetch error:', err);
        }
    }

    function highlightButton(type) {
        document.querySelectorAll('.card-header button').forEach(btn => {
            btn.classList.remove('btn-primary', 'text-white');
            btn.classList.add('btn-light');
        });

        const id = {
            month: '#btnMonthly',
            quarter: '#btnQuarterly',
            annual: '#btnAnnual'
        }[type];

        const activeBtn = document.querySelector(id);
        if(activeBtn){
            activeBtn.classList.remove('btn-light');
            activeBtn.classList.add('btn-primary', 'text-white');
        }
    }

    document.getElementById('btnMonthly').addEventListener('click', () => loadPrediction('month'));
    document.getElementById('btnQuarterly').addEventListener('click', () => loadPrediction('quarter'));
    document.getElementById('btnAnnual').addEventListener('click', () => loadPrediction('annual'));

    // Load default
    loadPrediction('month');
});
</script>
@endsection
