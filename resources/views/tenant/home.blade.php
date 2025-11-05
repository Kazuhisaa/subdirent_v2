@extends('tenant.dashboard')

@section('title', 'Tenant Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid tenant-dashboard">

  <!-- Welcome + Property Card -->
  <div class="row align-items-stretch mb-4">
    <!-- Welcome Message -->
    <div class="col-md-8 d-flex">
      <div class="card tenant-card shadow-sm border-0 p-4 flex-fill d-flex flex-row align-items-center">
        <img src="{{ asset('images/default-avatar.png') }}" class="rounded-circle me-3" width="60" height="60" alt="Profile">
        <div>
          <h5 class="mb-1">Hi, {{ $tenant->first_name ?? $tenant->name ?? 'Tenant' }}</h5>
          <p class="text-muted mb-2">
            Welcome to your Tenant Portal. Here’s a quick overview of your property, payments, and maintenance updates.
          </p>
        </div>
      </div>
    </div>

    <!-- Dynamic Property Card -->
    <div class="col-md-4 d-flex">
      <div class="card p-3 border-0 shadow-sm flex-fill">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div>
            <small class="text-muted d-block">PROPERTY DETAILS</small>
               @if($tenant->tenant && $tenant->tenant->unit)
              <strong>{{ $tenant->tenant->unit->title }}</strong>
              <p class="small text-muted mb-0">
                {{ $tenant->tenant->unit->location }}
              </p>
            @else
              <p class="small text-muted mb-0">No assigned property yet.</p>
            @endif
          </div>
          <i class="bi bi-house-door-fill text-primary fs-4"></i>
        </div>

        @if($tenant && $tenant->unit)
          <a href="{{ route('tenant.property') }}" class="btn btn-outline-tenant w-100 mt-2">
            <i class="bi bi-building me-1"></i> View My Property
          </a>
        @endif
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

  <!-- Full Width Calendar -->
  <div class="row mb-5">
    <div class="col-12">
      <div class="card border-0 shadow-sm p-4 w-100">
        <h6 class="fw-bold mb-3">Payment Schedule</h6>
        <div id="tenant-calendar" class="tenant-calendar w-100"></div>
      </div>
    </div>
  </div>

  <!-- Recent Invoices Table -->
<div class="card border-0 shadow-sm p-4 mt-4">
    <h6 class="fw-bold mb-3">Recent Invoices</h6>
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th>Invoice<th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recentInvoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_no ?? 'INV-' . str_pad($invoice->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $invoice->remarks ?? 'No description' }}</td>
                    <td>₱{{ number_format($invoice->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('M d, Y') }}</td>

                    <td>
                        @if ($invoice->status === 'Pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif ($invoice->status === 'Paid')
                            <span class="badge bg-success">Paid</span>
                        @elseif ($invoice->status === 'Overdue')
                            <span class="badge bg-danger">Overdue</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($invoice->payment_status) }}</span>
                        @endif
                    </td>
<td>
                <a href="{{ route('tenant.payment.invoice.download', $invoice->id) }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-download"></i> Download
                </a>
            </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No recent invoices found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


<!-- Calendar Script -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('tenant-calendar');
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');

    // Create the calendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
      height: 'auto',
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: ''
      },
      events: [
        { title: 'Rent Due ₱19,220', start: '2025-10-17', color: '#2478C2' },
        { title: 'Maintenance - Water Leak', start: '2025-10-10', color: '#ff7043' },
        { title: 'Electric Bill ₱2,800', start: '2025-10-20', color: '#26a69a' }
      ]
    });
    calendar.render();

    // Animate calendar resize when sidebar toggles
    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener('click', () => {
        // Add a short opacity transition for smoothness
        calendarEl.style.transition = 'opacity 0.3s ease';
        calendarEl.style.opacity = '0.4';

        // Wait for sidebar animation to finish
        setTimeout(() => {
          calendar.updateSize();
          calendarEl.style.opacity = '1';
        }, 350);
      });
    }

    // Re-render on window resize
    window.addEventListener('resize', () => {
      calendar.updateSize();
    });
  });
</script>

@endsection
