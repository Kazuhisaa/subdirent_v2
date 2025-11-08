@extends('tenant.dashboard')

@section('title', 'Tenant Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid tenant-dashboard">

  <div class="row align-items-stretch mb-4">
    <div class="col-md-8 d-flex">
      <div class="card tenant-card shadow-sm border-0 p-4 flex-fill d-flex flex-row align-items-center">
        {{-- === START UPDATE (Simplified) === --}}
        <img src="{{ $tenant->profile_photo_url }}" 
             class="rounded-circle me-3" width="60" height="60" alt="Profile" style="object-fit: cover;">
        {{-- === END UPDATE === --}}
        
        <div>
          <h5 class="mb-1">Hi, {{ $tenant->first_name ?? $tenant->name ?? 'Tenant' }}</h5>
          <p class="text-muted mb-2">
            Welcome to your Tenant Portal. Here’s a quick overview of your property, payments, and maintenance updates.
          </p>
        </div>
      </div>
    </div>

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

        @if($tenant->tenant && $tenant->tenant->unit)
          <a href="{{ route('tenant.property') }}" class="btn btn-outline-tenant w-100 mt-2">
            <i class="bi bi-building me-1"></i> View My Property
          </a>
        @endif
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm p-4">
        <h6 class="fw-bold mb-3">Maintenance Tasks</h6>
        <div class="d-flex justify-content-around text-center mb-3">
          {{-- UPDATED: Use dynamic variables --}}
          <div><h2 class="text-tenant-accent mb-0">{{ $maintenanceCounts['pending'] }}</h2><small>Pending</small></div>
          <div><h2 class="text-tenant-dark mb-0">{{ $maintenanceCounts['inprogress'] }}</h2><small>In Progress</small></div>
          <div><h2 class="text-muted mb-0">{{ $maintenanceCounts['completed'] }}</h2><small></small>Completed</div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm p-4 h-100">
        <h6 class="fw-bold mb-3">Payment Alert</h6>
        <div class="alert {{ $nextUnpaidDueDate ? 'alert-danger' : 'alert-success' }} small mb-0">
          <i class="bi bi-{{ $nextUnpaidDueDate ? 'exclamation-triangle' : 'check-circle' }} me-2"></i>
          @if($nextUnpaidDueDate)
            **Next Rent Due:** **{{ $nextUnpaidDueDate }}**. Please settle immediately.
          @else
            **You are up to date!** No immediate payments pending.
          @endif
        </div>
        @if($activeContract)
        <small class="text-muted mt-2">Monthly Amount: ₱{{ number_format($activeContract->monthly_payment, 2) }}</small>
        @endif
      </div>
    </div>
  </div>

  <div class="row mb-5">
    <div class="col-12">
      <div class="card border-0 shadow-sm p-4 w-100">
        <h6 class="fw-bold mb-3">Payment Schedule (Current Contract)</h6>
        {{-- JSON data is passed here for the script --}}
        <div id="tenant-calendar" class="tenant-calendar w-100" data-events="{{ $calendarEvents }}"></div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm p-0">
    <div class="card-header bg-light border-0 py-3">
      <h6 class="fw-bold text-primary mb-0">Recent Payments & Invoices</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table home-table align-middle text-center mb-0">
                <thead class="text-uppercase small">
                    <tr>
                        <th class="text-start ps-4">Description</th>
                        <th>Amount</th>
                        <th>Date Paid</th>
                        <th>For Month</th>
                        <th>Status</th>
                        <th>Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $payment)
                    <tr>
                        <td class="text-start ps-4">
                            <strong class="text-blue-900">{{ $payment->remarks }}</strong><br>
                            <small class="text-muted">Ref No: {{ $payment->reference_no }}</small>
                        </td>
                        <td class="fw-bold text-success">₱{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                        <td>{{ $payment->for_month ? \Carbon\Carbon::parse($payment->for_month)->format('M Y') : 'N/A' }}</td>
                        <td>
                            @php
                                $statusClass = '';
                                switch ($payment->payment_status) {
                                    case 'paid': $statusClass = 'badge-paid'; break;
                                    case 'partial': $statusClass = 'badge-partial'; break;
                                    default: $statusClass = 'badge-due'; break;
                                }
                            @endphp
                            <span class="badge {{ $statusClass }} text-uppercase">
                                {{ $payment->payment_status }}
                            </span>
                        </td>
                        <td>
                            @if($payment->invoice_pdf)
                                <a href="{{ route('tenant.payment.invoice.download', $payment->id) }}" class="btn btn-sm btn-outline-tenant">
                                    <i class="bi bi-file-earmark-arrow-down"></i>
                                </a>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 text-muted text-center">No recent payments recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
  </div>

  <div class="row mb-5">
    <div class="col-12">
      <div class="card border-0 shadow-sm p-0">
        <div class="card-header bg-light border-0 py-3">
          <h6 class="fw-bold text-primary mb-0">My Maintenance Requests</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table home-table align-middle text-center mb-0">
                    <thead class="text-uppercase small">
                        <tr>
                            <th class="text-start ps-4">Category</th>
                            <th>Description</th>
                            <th>Notes</th>
                            <th>Date Submitted</th>
                            <th>Status</th>
                            <th class="pe-4">Scheduled Service</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($maintenanceRequests as $request)
                        <tr>
                            <td class="text-start ps-4 fw-bold text-blue-900">{{ $request->category }}</td>
                            <td class="text-start" style="max-width: 300px;">
                                <small class="d-block text-truncate" title="{{ $request->description }}">{{ $request->description }}</small>
                            </td>
                            {{-- === ADDED: This cell displays the admin's notes === --}}
                            <td class="text-start" style="max-width: 200px;">
                                <small class="d-block text-truncate" title="{{ $request->notes }}">{{ $request->notes ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                {{-- === MODIFIED LOGIC START: Explicitly ensure Completed is green === --}}
                                @php
                                    $badgeClass = 'bg-secondary'; // Default 
                                    if ($request->status == 'Completed') $badgeClass = 'bg-success'; // GREEN
                                    else if ($request->status == 'Pending') $badgeClass = 'bg-warning text-dark'; // YELLOW/ORANGE
                                    else if ($request->status == 'In Progress') $badgeClass = 'bg-danger'; // RED
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $request->status }}</span>
                                {{-- === MODIFIED LOGIC END === --}}
                            </td>
                            <td class="fw-bold pe-4">
                                {{ $request->scheduled_date ? \Carbon\Carbon::parse($request->scheduled_date)->format('M d, Y') : 'N/A' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            {{-- === CHANGED: Updated colspan from 5 to 6 === --}}
                            <td colspan="6" class="py-4 text-muted text-center">You have not submitted any maintenance requests.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection