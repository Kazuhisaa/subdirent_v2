@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-blue-900">ANALYTICS</h3>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row text-center mb-4 g-3 justify-content-center">
        {{-- Total Revenue --}}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card shadow-sm border-0 bg-white h-100 rounded-3">
                <div class="card-body d-flex flex-column justify-content-center align-items-center px-4 py-3">
                    <h6 class="text-muted text-uppercase mb-2 small">Total Revenue</h6>
                    <h3 class="fw-bold text-blue-800 fs-2 text-nowrap" id="totalRevenue">₱0</h3>
                </div>
            </div>
        </div>
        {{-- Available Units --}}
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
            <div class="card shadow-sm border-0 bg-white h-100 rounded-3">
                <div class="card-body d-flex flex-column justify-content-center align-items-center p-3">
                    <h6 class="text-muted text-uppercase mb-2 small">Available Units</h6>
                    <h3 class="fw-bold text-blue-800 fs-3" id="availableUnits">0</h3>
                </div>
            </div>
        </div>
        {{-- Rented Units --}}
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
            <div class="card shadow-sm border-0 bg-white h-100 rounded-3">
                <div class="card-body d-flex flex-column justify-content-center align-items-center p-3">
                    <h6 class="text-muted text-uppercase mb-2 small">Rented Units</h6>
                    <h3 class="fw-bold text-blue-800 fs-3" id="rentedUnits">0</h3>
                </div>
            </div>
        </div>
        {{-- Completed Units --}}
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
            <div class="card shadow-sm border-0 bg-white h-100 rounded-3">
                <div class="card-body d-flex flex-column justify-content-center align-items-center p-3">
                    <h6 class="text-muted text-uppercase mb-2 small">Completed Units</h6>
                    <h3 class="fw-bold text-blue-800 fs-3" id="completedUnits">0</h3>
                </div>
            </div>
        </div>
        {{-- Peak Month --}}
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
            <div class="card shadow-sm border-0 bg-white h-100 rounded-3">
                <div class="card-body d-flex flex-column justify-content-center align-items-center p-3">
                    <h6 class="text-muted text-uppercase mb-2 small">Peak Month</h6>
                    <h3 class="fw-bold text-blue-800 fs-3 text-nowrap" id="peakMonth">-</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- REVENUE CHART --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                    <h5 class="mb-0">Revenue Overview</h5>
                    <div>
                        <button class="btn btn-light btn-sm me-2 fw-semibold chart-btn" data-type="month">Monthly</button>
                        <button class="btn btn-light btn-sm me-2 fw-semibold chart-btn" data-type="quarter">Quarterly</button>
                        <button class="btn btn-light btn-sm fw-semibold chart-btn" data-type="annual">Annual</button>
                    </div>
                </div>
                {{-- ADJUSTED CHART HEIGHT HERE --}}
                <div class="card-body" style="height:380px;"> 
                    <canvas id="revenueChart" height="330"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- OCCUPANCY CHART --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                    <h5 class="mb-0">Occupancy Overview</h5>
                    <div>
                        <button class="btn btn-light btn-sm me-1 fw-semibold occ-btn" data-type="all">All Status</button>
                        <button class="btn btn-light btn-sm me-1 fw-semibold occ-btn" data-type="location">Per Location</button>
                        <button class="btn btn-light btn-sm me-1 fw-semibold occ-btn" data-type="rate">Rate per Location</button>
                        <button class="btn btn-light btn-sm me-1 fw-semibold occ-btn" data-type="allrate">Overall Rate</button>
                        <select id="chartTypeSelect" class="form-select form-select-sm d-inline-block w-auto ms-2">
                            <option value="bar">Bar</option>
                            <option value="pie">Pie</option>
                        </select>
                    </div>
                </div>
                {{-- ADJUSTED CHART HEIGHT HERE --}}
                <div class="card-body d-flex align-items-center" style="height:380px;" id="occupancyChartContainerParent"> 
                    {{-- Container for the chart canvas --}}
                    <div id="occupancyChartContainer" style="flex: 1 1 70%; height: 100%; position: relative;">
                         <canvas id="occupancyChart"></canvas>
                    </div>
                     <div id="occupancyLegendContainer" style="flex: 0 0 30%; height: 100%; overflow-y: auto;">
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

{{--Revenue Chart Script--}}
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        let chart;
        let activeButton = 'month';

        async function fetchTotalRevenue() {
            try {
                const res = await fetch('/api/revenue/totalrevenue');
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const text = await res.text();
                const total = parseFloat(text);
                document.getElementById('totalRevenue').textContent =
                    '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } catch {
                document.getElementById('totalRevenue').textContent = '₱0.00';
            }
        }

        async function fetchOccupancyCards() {
            try {
                const res = await fetch('/api/occupancy/all');
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();

                const available = data.find(d => d.status.toLowerCase() === 'available')?.total || 0;
                const rented = data.find(d => d.status.toLowerCase() === 'rented')?.total || 0;
                const completed = data.find(d => d.status.toLowerCase() === 'completed')?.total || 0;

                document.getElementById('availableUnits').textContent = available;
                document.getElementById('rentedUnits').textContent = rented;
                document.getElementById('completedUnits').textContent = completed;
            } catch (err) {
                console.error("Error fetching occupancy cards:", err);
            }
        }

        async function fetchPeakMonth() {
            try {
                const res = await fetch('/api/revenue/peakmonth');
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();
                document.getElementById('peakMonth').textContent = data.peak_month || 'N/A';
            } catch {
                document.getElementById('peakMonth').textContent = 'N/A';
            }
        }

        async function fetchAndRender(type) {
            const endpoints = {
                month: '/api/prediction/revenue/permonth',
                quarter: '/api/prediction/v2/revenue/perQuarter',
                annual: '/api/prediction/v2/revenue/perAnnual'
            };

            let labels = [], histData = [];
            let pred = 0, predLabel = '', ciLower = 0, ciUpper = 0, r2Score = '', mape = '';

            try {
                const res = await fetch(endpoints[type]);
                if (!res.ok) throw new Error(`Failed to fetch ${type} data`);
                const data = await res.json();

                if (type === 'month') {
                    const historical = data.date || [];
                    labels = historical.map(r => r.date || ''); 
                    histData = historical.map(r => parseFloat(r.monthly_revenue) || 0);

                    pred = data.prediction?.revenue_prediction || 0;
                    predLabel = data.prediction?.prediction_date || '';
                    ciLower = data.prediction?.confidence_interval?.lower || pred;
                    ciUpper = data.prediction?.confidence_interval?.upper || pred;
                    r2Score = data.prediction?.model_Accuracy?.Accuracy || 'N/A';
                    mape = data.prediction?.model_Accuracy?.MAPE || 'N/A';

                } else if (type === 'quarter') {
                    labels = data.quarters || [];
                    histData = Object.values(data.quarterly_revenues || {});

                    pred = data.predicted_revenue || 0;
                    predLabel = data.predicted_quarter || '';
                    ciLower = data.confidence_interval?.lower || pred;
                    ciUpper = data.confidence_interval?.upper || pred;
                    r2Score = data.Accuracy || 'N/A';
                    mape = data.MAPE || 'N/A';

                } else if (type === 'annual') {
                    labels = data.trained_years || [];
                    histData = data.annual_revenues || [];

                    pred = data.predicted_total_revenue || 0;
                    predLabel = data.predicted_year?.toString() || '';
                    ciLower = data.confidence_interval?.lower || pred;
                    ciUpper = data.confidence_interval?.upper || pred;
                    r2Score = data.Accuracy || 'N/A';
                    mape = data.MAPE || 'N/A';
                }

                const offset = (pred * 0.05) || 1000;

                const allLabels = [...labels, predLabel];
                const histDataExtended = [...histData, null];
                const predData = Array(histData.length).fill(null).concat(pred);
                const highData = Array(histData.length).fill(null).concat(ciUpper + offset);
                const lowData = Array(histData.length).fill(null).concat(ciLower - offset);

                const chartData = {
                    labels: allLabels,
                    datasets: [
                        {
                            label: 'Historical Revenue (₱)', data: histDataExtended, borderColor: '#0d6efd',
                            backgroundColor: '#0d6efd33', fill: false, tension: 0.3, pointRadius: 4
                        },
                        {
                            label: 'Predicted Revenue', data: predData, borderColor: '#0d6efd',
                            backgroundColor: '#0d6efd', pointStyle: 'circle', pointRadius: 10,
                            pointHoverRadius: 14, showLine: false
                        },
                        {
                            label: 'Higher Confidence interval (95%)', data: highData, borderColor: 'transparent',
                            backgroundColor: 'rgba(25,135,84,0.9)', pointStyle: 'triangle',
                            rotation: 0, pointRadius: 14, pointHoverRadius: 16, showLine: false
                        },
                        {
                            label: 'Lower Confidence interval (95%)', data: lowData, borderColor: 'transparent',
                            backgroundColor: 'rgba(220,53,69,0.9)', pointStyle: 'triangle',
                            rotation: 180, pointRadius: 14, pointHoverRadius: 16, showLine: false
                        }
                    ]
                };

                const options = {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    const y = ctx.parsed.y || 0;
                                    const label = ctx.dataset.label;
                                    
                                    if (label === 'Predicted Revenue')
                                        return [`Predicted: ₱${pred.toLocaleString()}`, `Accuracy: ${r2Score}`, `MAPE: ${mape}`];
                                    if (label === 'Higher Confidence interval (95%)') return `High CI: ₱${ciUpper.toLocaleString()}`;
                                    if (label === 'Lower Confidence interval (95%)') return `Low CI: ₱${ciLower.toLocaleString()}`;
                                    
                                    return '₱' + y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: { y: { beginAtZero: true, ticks: { callback: v => '₱' + v.toLocaleString() } } }
                };

                if (!chart) {
                    chart = new Chart(ctx, { type: 'line', data: chartData, options });
                } else {
                    chart.data = chartData; chart.update();
                }

            } catch (err) { console.error('Failed to fetch/render chart:', err); }
        }

        function setActiveButton(type){
            document.querySelectorAll('.chart-btn').forEach(btn=>{
                btn.classList.remove('btn-primary','text-white', 'active');
                btn.classList.add('btn-light','text-dark');
            });
            const activeBtn = document.querySelector(`.chart-btn[data-type="${type}"]`);
            if(activeBtn){
                activeBtn.classList.remove('btn-light','text-dark');
                activeBtn.classList.add('btn-primary','text-white', 'active');
            }
        }

        document.querySelectorAll('.chart-btn').forEach(btn=>{
            btn.addEventListener('click', e=>{
                const type = e.currentTarget.dataset.type;
                setActiveButton(type); fetchAndRender(type);
            });
        });

        fetchTotalRevenue(); fetchOccupancyCards(); fetchPeakMonth();
        setActiveButton(activeButton); fetchAndRender(activeButton);
    });
</script>

{{--OCCUPANCY CHART SCRIPT--}}
<script>
let chartType = document.getElementById('chartTypeSelect').value;
const occCtx = document.getElementById('occupancyChart').getContext('2d');
let occChart;
let activeOccBtn = 'all';


const bluePalette = [
    '#EAF8FF', '#CDEEFF', '#9FD8F7', '#5AB8F0', '#2A9DF4',
    '#1E81CE', '#145DA0', '#0D3B66', '#0A2540'
];
const chartColors = bluePalette.slice().reverse();

const chartContainer = document.getElementById('occupancyChartContainer');
const legendContainer = document.getElementById('occupancyLegendContainer');
const chartTypeSelect = document.getElementById('chartTypeSelect');

chartTypeSelect.addEventListener('change', e => {
    chartType = e.target.value; 
    fetchOccupancyData(activeOccBtn);
});

async function fetchOccupancyData(type) {
    const endpoints = {
        all: '/api/occupancy/all', location: '/api/occupancy/perlocation',
        rate: '/api/occupancy/rate', allrate: '/api/occupancy/allrate'
    };

    chartContainer.innerHTML = '<canvas id="occupancyChart"></canvas>';
    legendContainer.innerHTML = ''; 
    const newCtx = document.getElementById('occupancyChart').getContext('2d'); 

    try {
        const res = await fetch(endpoints[type]);
        if (!res.ok) throw new Error(`Failed to fetch ${type} occupancy data`);
        const data = await res.json();

        let labels = [], values = [], labelText = '', isPercentage = false;

        if (type === 'all') {
            labels = data.map(d => d.status || 'N/A'); values = data.map(d => Number(d.total) || 0);
            labelText = 'Units by Status';
        }
        else if (type === 'allrate') {
            labels = ['Occupied (%)', 'Unoccupied (%)'];
            values = [parseFloat(data.occupancy_rate || 0), parseFloat(data.unoccupied_rate || 0)];
            labelText = 'Overall Occupancy Rate'; isPercentage = true;
        }
        else if (type === 'location') {
            labels = data.map(d => d.location || 'N/A'); values = data.map(d => Number(d.total) || 0);
            labelText = 'Units per Location';
        }
        else if (type === 'rate') {
            labels = data.map(d => d.location || 'N/A'); values = data.map(d => parseFloat(d.occupancy_rate) || 0);
            labelText = 'Occupancy Rate by Location (%)'; isPercentage = true;
        }

        // ✅✅✅ Y-AXIS FIX START ✅✅✅
        let yAxisMax = 10; // Default max if no data or all values are 0
        if (values.length > 0) {
            const maxVal = Math.max(...values);
            if (maxVal > 0) {
                yAxisMax = maxVal;
            }
        }
        // For percentage charts, the max should always be 100
        if (isPercentage) {
            yAxisMax = 100;
        }
        // ✅✅✅ Y-AXIS FIX END ✅✅✅

        const bgColors = labels.map((_, i) => chartColors[i % chartColors.length]);

        const chartData = {
            labels,
            datasets: [{
                label: labelText, data: values, backgroundColor: bgColors,
                borderColor: '#FFFFFF', borderWidth: chartType === 'pie' ? 2 : 1,
                borderRadius: chartType === 'bar' ? 5 : 0,
            }]
        };

        const isPieChart = chartType === 'pie';
        legendContainer.style.display = isPieChart ? 'block' : 'none';
        chartContainer.style.flex = isPieChart ? '1 1 70%' : '1 1 100%';

        const options = {
            responsive: true, maintainAspectRatio: false,
            layout: { padding: { top: 10, bottom: 10, left: 10, right: 10 } },
            plugins: {
                legend: {
                    display: !isPieChart, 
                    position: 'top',
                    align: 'end',
                    labels: { color: '#0D3B66', font: { weight: 'bold' }, boxWidth: 20, padding: 10 },
                },
                tooltip: {
                    backgroundColor: '#0A2540', titleFont: { weight: 'bold' }, bodyFont: { size: 14 }, padding: 10,
                    callbacks: {
                        label: ctx => {
                            let value;
                            if (chartType === 'bar') {
                                value = ctx.parsed.y;
                            } else { 
                                value = ctx.parsed;
                            }
                            
                            if (value === null || value === undefined) {
                                value = 0;
                            }

                            return isPercentage ? ` ${ctx.label}: ${value.toFixed(1)}%` : ` ${ctx.label}: ${value} units`;
                        }
                    }
                },
                datalabels: {
                    color: '#ffffff', font: { weight: 'bold', size: 12 }, anchor: 'center', align: 'center',
                    formatter: (value, ctx) => {
                        if (value === 0) {
                            return null;
                        }
                        
                        if (chartType === 'pie') {
                            const total = ctx.chart.getDatasetMeta(0).total;
                            const percentage = total > 0 ? (value / total) * 100 : 0;
                            if (percentage < 3) return null; 
                            return isPercentage ? `${value.toFixed(1)}%` : value;
                        }
                        return isPercentage ? `${value.toFixed(1)}%` : value;
                    }
                }
            },
            // ✅✅✅ Y-AXIS FIX APPLIED HERE ✅✅✅
            scales: chartType === 'bar' ? {
                x: { ticks: { color: '#0D3B66', font: { weight: '600' } }, grid: { display: false } },
                y: { 
                    beginAtZero: true, 
                    ticks: { 
                        callback: v => isPercentage ? v + '%' : v, 
                        color: '#0D3B66' 
                    }, 
                    grid: { color: '#CDEEFF' },
                    // Set the max value dynamically
                    max: yAxisMax, 
                }
            } : {}
        };

        if (occChart) occChart.destroy();

        occChart = new Chart(newCtx, {
            type: chartType, data: chartData, options, plugins: [ChartDataLabels]
        });

        if (isPieChart) {
            generateCustomLegend(occChart);
        }

    } catch (err) {
        console.error('Occupancy chart fetch/render error:', err);
        chartContainer.innerHTML = `<div class="text-danger text-center p-5 w-100">⚠ Failed to load occupancy chart: ${err.message}</div>`;
        legendContainer.innerHTML = '';
    }
}

function generateCustomLegend(chart) {
    legendContainer.innerHTML = ''; // Clear old legend
    const legendItems = chart.data.labels.map((label, i) => {
        const meta = chart.getDatasetMeta(0);
        const style = meta.controller.getStyle(i);
        const value = chart.data.datasets[0].data[i];
        const total = meta.total;
        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';

        return {
            text: `${label} (${percentage})`,
            fillStyle: style.backgroundColor,
            strokeStyle: style.borderColor,
            lineWidth: style.borderWidth,
        };
    });

    const ul = document.createElement('ul');
    ul.style.listStyle = 'none';
    ul.style.padding = '0';
    ul.style.margin = '0';
    ul.style.display = 'flex';
    ul.style.flexDirection = 'column';
    ul.style.justifyContent = 'center';
    ul.style.height = '100%';
    ul.style.paddingLeft = '20px';

    legendItems.forEach(item => {
        const li = document.createElement('li');
        li.style.display = 'flex';
        li.style.alignItems = 'center';
        li.style.marginBottom = '15px'; 
        li.style.cursor = 'pointer';

        const box = document.createElement('span');
        box.style.display = 'inline-block';
        box.style.width = '30px'; 
        box.style.height = '30px'; 
        box.style.backgroundColor = item.fillStyle;
        box.style.borderColor = item.strokeStyle;
        box.style.borderWidth = item.lineWidth + 'px';
        box.style.borderStyle = 'solid';
        box.style.marginRight = '15px'; 
        box.style.borderRadius = '5px'; 

        const text = document.createElement('span');
        text.style.color = '#0D3B66';
        text.style.fontWeight = 'bold'; 
        text.style.fontSize = '1.1rem'; 
        text.textContent = item.text;

        li.appendChild(box);
        li.appendChild(text);
        ul.appendChild(li);
    });

    legendContainer.appendChild(ul);
}


function setActiveOccButton(type) {
    activeOccBtn = type;
    document.querySelectorAll('.occ-btn').forEach(btn => {
        btn.classList.remove('btn-primary', 'text-white', 'active'); btn.classList.add('btn-light', 'text-dark');
    });
    const activeBtn = document.querySelector(`.occ-btn[data-type="${type}"]`);
    if (activeBtn) {
        activeBtn.classList.remove('btn-light', 'text-dark'); activeBtn.classList.add('btn-primary', 'text-white', 'active');
    }
}

document.querySelectorAll('.occ-btn').forEach(btn => {
    btn.addEventListener('click', e => {
        const type = e.currentTarget.dataset.type;
        setActiveOccButton(type);
        fetchOccupancyData(type); 
    });
});

// Initial Load
setActiveOccButton(activeOccBtn); 
fetchOccupancyData(activeOccBtn);

</script>
@endsection