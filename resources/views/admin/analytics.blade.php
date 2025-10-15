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
                    <h3 class="fw-bold text-primary" id="totalRevenue">₱0</h3>
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
                <div class="card-body" style="height:450px;">
                    <canvas id="revenueChart" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    let chart;

    const buttonMap = {
        month: 'btnMonthly',
        quarter: 'btnQuarterly',
        annual: 'btnAnnual'
    };

    async function fetchAndRender(type) {
        const endpoints = {
            month: '/api/prediction/revenue/permonth',
            quarter: '/api/prediction/revenue/perQuarter',
            annual: '/api/prediction/revenue/perAnnual'
        };

        try {
            const res = await fetch(endpoints[type]);
            if(!res.ok) throw new Error('API returned '+res.status);
            const data = await res.json();
            console.log('API Data:', data);

            // Extract historical data
            const historical = data.date || [];
            const labels = historical.map(r => r.year_month?.slice(0,7) || '');
            const histData = historical.map(r => parseFloat(r.monthly_revenue) || 0);

            // Extract predicted data
            const pred = data.prediction?.revenue_prediction || 0;
            const predLabel = data.prediction?.prediction_date?.slice(0,7) || '';

            const allLabels = [...labels, predLabel];
            const predictedData = Array(histData.length).fill(null).concat(pred);

            // Example upper/lower confidence (if available)
            const upper = Array(histData.length).fill(null).concat(pred * 1.1); // 10% above
            const lower = Array(histData.length).fill(null).concat(pred * 0.9); // 10% below

            // Update total revenue card
            const total = histData.reduce((a,b)=>a+b,0);
            document.getElementById('totalRevenue').textContent = '₱' + total.toLocaleString();

            const chartData = {
                labels: allLabels,
                datasets: [
                    {
                        label:'Historical Revenue (₱)',
                        data:[...histData,null],
                        borderColor:'#007bff',
                        fill:false,
                        tension:0.3
                    },
                    {
                        label:'Predicted Revenue (₱)',
                        data:predictedData,
                        borderColor:'#ffc107',
                        fill:false,
                        pointRadius:6,
                        showLine:true
                    },
                    {
                        label:'Upper Confidence',
                        data:upper,
                        borderColor:'rgba(0,128,0,1)',
                        backgroundColor:'rgba(0,128,0,0.2)',
                        fill:'+1',
                        pointRadius:0,
                        borderWidth:1,
                        tension:0.3
                    },
                    {
                        label:'Lower Confidence',
                        data:lower,
                        borderColor:'rgba(255,0,0,1)',
                        backgroundColor:'rgba(255,0,0,0.2)',
                        fill:'-1',
                        pointRadius:0,
                        borderWidth:1,
                        tension:0.3
                    }
                ]
            };

            const options = {
                responsive:true,
                maintainAspectRatio:false,
                plugins:{
                    legend:{ position:'top' },
                    tooltip:{ callbacks:{ label: ctx => '₱'+(ctx.parsed.y||0).toLocaleString() } }
                },
                scales:{
                    x:{ ticks:{ color:'#6c757d' } },
                    y:{ beginAtZero:true, ticks:{ color:'#6c757d', callback: v=>'₱'+v.toLocaleString() } }
                }
            };

            if(!chart){
                chart = new Chart(ctx,{ type:'line', data:chartData, options });
            } else {
                chart.data = chartData;
                chart.update();
            }

            // Highlight active button
            Object.values(buttonMap).forEach(id=>{
                document.getElementById(id).classList.remove('btn-primary','text-white');
                document.getElementById(id).classList.add('btn-light');
            });
            document.getElementById(buttonMap[type]).classList.add('btn-primary','text-white');

        } catch(err){
            console.error('Failed to fetch/render chart:', err);
        }
    }

    // Button events
    Object.keys(buttonMap).forEach(type=>{
        const btn = document.getElementById(buttonMap[type]);
        if(btn) btn.addEventListener('click',()=>fetchAndRender(type));
    });

    // Load default
    fetchAndRender('month');
});
</script>
@endsection
