@extends('tenant.dashboard')

@section('title', 'Tenant Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid tenant-dashboard">

  <!-- Welcome + Property Card -->
  <div class="row align-items-center mb-4">
    <div class="col-md-8">
      <div class="card tenant-card shadow-sm border-0 p-4 d-flex flex-row align-items-center">
        <img src="{{ asset('images/default-avatar.png') }}" class="rounded-circle me-3" width="60" height="60" alt="Profile">
        <div>
          <h5 class="mb-1">Hi, {{ $tenant->name ?? 'Tenant' }}</h5>
          <p class="text-muted mb-2">Welcome to your Tenant Portal. Here’s a quick overview of your account, payments, and tasks.</p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card p-3 border-0 shadow-sm">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block">PROPERTY DETAILS</small>
            <strong>123 Gawagawa Ave.</strong>
            <p class="small text-muted mb-0">
              Next payment of ₱19,220 is due on <strong>Oct 17, 2025</strong>.
            </p>
          </div>
          <i class="bi bi-house-door-fill text-tenant-accent fs-4"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Accounting Chart + Calendar -->
  <div class="row mb-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm p-4">
        <h6 class="fw-bold mb-3">Accounting Overview</h6>
        <canvas id="paymentChart" height="400"></canvas>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm p-4">
        <h6 class="fw-bold mb-3">Payment Schedule</h6>
        <div id="tenant-calendar" class="tenant-calendar"></div>
      </div>
    </div>
  </div>

  <!-- Maintenance + Alerts -->
  <div class="row mb-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm p-4">
        <h6 class="fw-bold mb-3">Maintenance Tasks</h6>
        <div class="d-flex justify-content-around text-center mb-3">
          <div><h2 class="text-tenant-accent mb-0">3</h2><small>New</small></div>
          <div><h2 class="text-tenant-dark mb-0">5</h2><small>Assigned</small></div>
          <div><h2 class="text-muted mb-0">10</h2><small>Closed</small></div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm p-4">
        <h6 class="fw-bold mb-3">Alerts</h6>
        <div class="alert alert-warning small">
          <i class="bi bi-exclamation-circle me-2"></i>
          Payment due on Oct 17.
        </div>
      </div>
    </div>
  </div>

  <!-- Invoices Table -->
  <div class="card border-0 shadow-sm p-4">
    <h6 class="fw-bold mb-3">Recent Invoices</h6>
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Description</th>
          <th>Amount</th>
          <th>Due Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>INV-001</td>
          <td>Monthly Rent (October)</td>
          <td>₱19,220</td>
          <td>Oct 17, 2025</td>
          <td><span class="badge bg-warning text-dark">Pending</span></td>
        </tr>
        <tr>
          <td>INV-002</td>
          <td>Plumbing Maintenance</td>
          <td>₱1,500</td>
          <td>Sept 22, 2025</td>
          <td><span class="badge bg-danger">Overdue</span></td>
        </tr>
        <tr>
          <td>INV-003</td>
          <td>Monthly Rent (September)</td>
          <td>₱19,220</td>
          <td>Sept 17, 2025</td>
          <td><span class="badge bg-success">Paid</span></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Chart + Calendar -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
  // Bar Chart
  const ctx = document.getElementById('paymentChart');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
      datasets: [{
        label: 'Total Rent',
        data: [15000, 15500, 16000, 16500, 17000, 17500, 18000, 18500, 19220, 19220],
        backgroundColor: '#009688'
      }]
    },
    options: {
      plugins: { legend: { display: false }},
      scales: { y: { beginAtZero: true }}
    }
  });

  // Calendar
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('tenant-calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        height: 400,
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
      events: [
        { title: 'Rent Due ₱19,220', start: '2025-10-17', color: '#009688' },
        { title: 'Maintenance - Water Leak', start: '2025-10-10', color: '#ff7043' },
        { title: 'Electric Bill ₱2,800', start: '2025-10-20', color: '#26a69a' }
      ]
    });
    calendar.render();
  });
</script>
@endsection
