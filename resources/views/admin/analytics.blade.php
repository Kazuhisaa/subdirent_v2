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
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase">Total Revenue</h6>
                    <h3 class="fw-bold text-primary" id="totalRevenue">₱0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase">Total Units</h6>
                    <h3 class="fw-bold text-primary" id="totalUnits">{{ $totalUnits }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase">Total Tenants</h6>
                    <h3 class="fw-bold text-primary" id="totalTenants">{{ $totalTenants }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3" id="peakMonthCard">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase">Peak Month</h6>
                    <h3 class="fw-bold text-primary" id="peakMonth">-</h3>
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
                        <button class="btn btn-light btn-sm me-2 fw-semibold chart-btn" data-type="month">Monthly</button>
                        <button class="btn btn-light btn-sm me-2 fw-semibold chart-btn" data-type="quarter">Quarterly</button>
                        <button class="btn btn-light btn-sm fw-semibold chart-btn" data-type="annual">Annual</button>
                    </div>
                </div>
                <div class="card-body" style="height:450px;">
                    <canvas id="revenueChart" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CHART SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    let chart;
    let activeButton = 'month';

    // --- Fetch Total Revenue from API ---
    async function fetchTotalRevenue() {
        try {
            const res = await fetch('/api/revenue/totalrevenue');
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const text = await res.text(); // raw number
            const total = parseFloat(text);
            document.getElementById('totalRevenue').textContent = '₱' + total.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        } catch (err) {
            console.error("Error fetching total revenue:", err);
            document.getElementById('totalRevenue').textContent = '₱0.00';
        }
    }

    // --- Fetch only peak month dynamically ---
    async function fetchPeakMonth() {
        try {
            const res = await fetch('/api/revenue/peakmonth');
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            document.getElementById('peakMonth').textContent = data.peak_month || 'N/A';
        } catch (err) {
            console.error("Error fetching peak month:", err);
            document.getElementById('peakMonth').textContent = 'N/A';
        }
    }

    // --- Fetch and render revenue chart ---
    async function fetchAndRender(type) {
        const endpoints = {
            month: '/api/prediction/revenue/permonth',
            quarter: '/api/prediction/revenue/perQuarter',
            annual: '/api/prediction/revenue/perAnnual'
        };

        try {
            const res = await fetch(endpoints[type]);
            if (!res.ok) throw new Error(`Failed to fetch ${type} data`);
            const data = await res.json();

            const historical = data.date || [];
            const labels = historical.map(r => r.year_month?.slice(0,7) || '');
            const histData = historical.map(r => parseFloat(r.monthly_revenue) || 0);

            const pred = data.prediction?.revenue_prediction || 0;
            const predLabel = data.prediction?.prediction_date?.slice(0,7) || '';
            const ciLower = data.prediction?.confidence_interval?.lower || pred;
            const ciUpper = data.prediction?.confidence_interval?.upper || pred;
            const confidenceLevel = data.prediction?.confidence_interval?.confidence_level || 'N/A';

            const allLabels = [...labels, predLabel];
            const histDataExtended = [...histData, null];
            const predData = Array(histData.length).fill(null).concat(pred);
            const lowerData = Array(histData.length).fill(null).concat(ciLower);
            const upperData = Array(histData.length).fill(null).concat(ciUpper);

            const chartData = {
                labels: allLabels,
                datasets: [
                    { label:'Historical Revenue (₱)', data: histDataExtended, borderColor:'#0d6efd', backgroundColor:'#0d6efd33', fill:false, tension:0.3 },
                    { label:'Predicted Revenue (₱)', data: predData, borderColor:'#0d6efd', backgroundColor:'#0d6efd88', pointRadius:10, pointHoverRadius:15, showLine:false },
                    { label:'Confidence Interval (High)', data: upperData, borderColor:'rgba(0,0,0,0)', backgroundColor:'rgba(0,200,0,0.2)', fill:'-1', pointRadius:0 },
                    { label:'Confidence Interval (Low)', data: lowerData, borderColor:'rgba(0,0,0,0)', backgroundColor:'rgba(255,0,0,0.2)', fill:'1', pointRadius:0 }
                ]
            };

            const options = {
                responsive:true,
                maintainAspectRatio:false,
                plugins:{
                    legend:{ position:'top' },
                    tooltip:{
                        callbacks:{
                            label: function(ctx){
                                const y = ctx.parsed.y || 0;
                                if(ctx.dataset.label.includes('Predicted')) {
                                    return [
                                        `Predicted: ₱${y.toLocaleString()}`,
                                        `Low CI: ₱${ciLower.toLocaleString()}`,
                                        `High CI: ₱${ciUpper.toLocaleString()}`,
                                        `Confidence: ${confidenceLevel}`
                                    ];
                                }
                                return '₱' + y.toLocaleString();
                            }
                        }
                    }
                },
                scales:{
                    y:{ beginAtZero:true, ticks:{ callback:v=>'₱'+v.toLocaleString() } }
                }
            };

            if(!chart){
                chart = new Chart(ctx, { type:'line', data:chartData, options });
            } else {
                chart.data = chartData;
                chart.update();
            }

        } catch(err) {
            console.error('Failed to fetch/render chart:', err);
        }
    }

    // --- Button handling ---
    function setActiveButton(type){
        document.querySelectorAll('.chart-btn').forEach(btn=>{
            btn.classList.remove('btn-primary','text-white');
            btn.classList.add('btn-light','text-dark');
        });
        const activeBtn = document.querySelector(`.chart-btn[data-type="${type}"]`);
        if(activeBtn){
            activeBtn.classList.remove('btn-light','text-dark');
            activeBtn.classList.add('btn-primary','text-white');
        }
    }

    document.querySelectorAll('.chart-btn').forEach(btn=>{
        btn.addEventListener('click', e=>{
            const type = e.currentTarget.dataset.type;
            setActiveButton(type);
            fetchAndRender(type);
        });
    });

    // --- Initial load ---
    fetchTotalRevenue();   // ✅ new API call for correct total
    fetchPeakMonth();      // ✅ separate API call
    setActiveButton(activeButton);
    fetchAndRender(activeButton);
});
</script>
@endsection
