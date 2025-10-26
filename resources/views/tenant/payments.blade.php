@extends('tenant.dashboard')

@section('title', 'My Payments')
@section('page-title', 'My Payments')

@section('content')
<div class="container py-4">
  <div class="row g-4">

    {{-- LEFT COLUMN - Outstanding Balance + Payment --}}
    <div class="col-lg-4">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body text-center py-4 px-3">
          <h6 class="text-secondary mb-2">Outstanding Balance</h6>
          <h1 class="fw-bold text-success mb-1">
            ₱{{ number_format($outstanding ?? 0, 2) }}
          </h1>

          {{-- Dates --}}
          <p class="text-muted small mb-2">
            For Month: <strong>{{ $nextMonth['date'] ?? 'N/A' }}</strong>
          </p>

          @php
            $lastPayment = $payments->sortByDesc('payment_date')->first();
          @endphp
          <div class="small text-secondary mb-3">
            <i class="bi bi-calendar-check text-success me-1"></i>
            Last Payment:
            <strong>
              {{ $lastPayment ? \Carbon\Carbon::parse($lastPayment->payment_date)->format('M d, Y') : 'N/A' }}
            </strong>
            <br>
            <i class="bi bi-calendar-event text-primary me-1"></i>
            Next Due Date:
            <strong>{{ $nextMonth['date'] ?? 'N/A' }}</strong>
          </div>

          {{-- Payment Form --}}
          @if(isset($nextMonth) && $nextMonth['for_month'])
          <form method="POST" action="{{ route('tenant.payment.create', $tenant->id) }}">
            @csrf
            <input type="hidden" name="for_month" value="{{ $nextMonth['for_month'] }}">

            <div class="mb-2">
              <input type="number"
                     name="amount"
                     class="form-control text-center fw-semibold"
                     min="1000"
                     max="{{ $amountToPay ?? 0 }}"
                     step="0.01"
                     placeholder="Enter payment (₱)"
                     required>
            </div>

            <div class="mb-3">
              <select name="payment_method" class="form-select text-center" required>
                <option value="" disabled selected>-- Choose Payment Method --</option>
                <option value="gcash">GCash</option>
                <option value="card">Credit/Debit Card</option>
              </select>
            </div>

            <button type="submit" class="btn btn-success w-100 fw-semibold mb-2 shadow-sm">
              <i class="bi bi-cash-stack me-1"></i> Pay Now
            </button>
          </form>
          @endif

          {{-- Autopay --}}
          <a href="#" class="btn btn-outline-secondary w-100 fw-semibold mb-3">
            <i class="bi bi-clock-history me-1"></i> Set Up Autopay
          </a>

          {{-- Dynamic Payment Status Notification --}}
          <div class="payment-status text-center mt-3">
            @if($paymentStatus['currentMonthPaid'] && $paymentStatus['nextMonthPaid'])
              <div class="alert alert-success py-2 small shadow-sm mb-0">
                <i class="bi bi-check-circle-fill me-1"></i>
                You’re fully paid for <strong>{{ $paymentStatus['currentMonth'] }}</strong> and <strong>{{ $paymentStatus['nextMonth'] }}</strong>! 🎉
              </div>
            @elseif($paymentStatus['currentMonthPaid'] && !$paymentStatus['nextMonthPaid'])
              <div class="alert alert-info py-2 small shadow-sm mb-0">
                <i class="bi bi-check-circle-fill me-1"></i>
                You’ve already paid for <strong>{{ $paymentStatus['currentMonth'] }}</strong>. Next rent is due soon.
              </div>
            @elseif(!$paymentStatus['currentMonthPaid'])
              <div class="alert alert-warning py-2 small shadow-sm mb-0">
                <i class="bi bi-exclamation-circle-fill me-1"></i>
                You have an outstanding balance for <strong>{{ $paymentStatus['currentMonth'] }}</strong>.
              </div>
            @endif

            @if($paymentStatus['prepaidMonths'] > 1)
              <div class="alert alert-success py-2 small shadow-sm mt-2 mb-0">
                <i class="bi bi-cash-stack me-1"></i>
                You’re paid ahead for {{ $paymentStatus['prepaidMonths'] }} months in advance!
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- RIGHT COLUMN - Ledger / Payment History --}}
    <div class="col-lg-8">
      <div class="card shadow-sm border-0">
        <div class="card-body px-4 py-3">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-semibold text-dark">
              <i class="bi bi-journal-text me-2"></i> Payment Ledger
            </h5>
              <span class="text-muted small">
                Showing last 5 transactions
              </span>
            <span class="text-muted small">
              {{ $tenant->tenant->first_name ?? '' }} {{ $tenant->tenant->last_name ?? '' }}
            </span>
          </div>

          {{-- Table --}}
           <div class="card shadow-sm border-0">
    <div class="card-body px-4 py-3">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="bg-light text-secondary small">
            <tr>
              <th>Month</th>
              <th>Date Paid</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Remarks</th>
              <th>Invoice</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments->sortByDesc('for_month')->take(5) as $payment)
              <tr>
                <td>{{ \Carbon\Carbon::parse($payment->for_month)->format('F Y') }}</td>
                <td>
                  {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : '-' }}
                </td>
                <td>₱{{ number_format($payment->amount, 2) }}</td>
                <td>
                  @if($payment->payment_status === 'paid')
                    <span class="badge bg-success">Paid</span>
                  @elseif($payment->payment_status === 'partial')
                    <span class="badge bg-warning text-dark">Partial</span>
                  @else
                    <span class="badge bg-danger">Unpaid</span>
                  @endif
                </td>
                <td>{{ $payment->remarks ?? '-' }}</td>
                <td>
                   @if($payment->invoice_pdf)
                      <a href="{{ asset('storage/' . $payment->invoice_pdf) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                        Download PDF
                      </a>
                    @else
                      <span class="text-muted small">N/A</span>
                   @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">
                  <i class="bi bi-info-circle me-1"></i> No payments or invoices found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
          

          {{-- Ledger Modal Trigger --}}
          <div class="text-center mt-4">
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ledgerModal">
              <i class="bi bi-journal-bookmark me-1"></i> View Full Account Ledger
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Include Ledger Modal Partial --}}
@if(isset($tenant))
  @include('partials.ledger-modal')
@endif
@endsection


@push('styles')
<style>
body {
  background-color: #f8f9fb;
  font-family: "Inter", sans-serif;
}
.card {
  border-radius: 14px;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 18px rgba(0,0,0,0.05);
}
.btn-success {
  background-color: #4CAF50;
  border-color: #4CAF50;
}
.btn-success:hover {
  background-color: #43a047;
}
.alert-success {
  background-color: #e6f7ea;
  color: #2e7d32;
  border: none;
}
.alert-info {
  background-color: #e3f2fd;
  color: #0d47a1;
  border: none;
}
.alert-warning {
  background-color: #fff4e5;
  color: #a26b00;
  border: none;
}
.payment-status {
  margin-top: 0.75rem;
}
.table {
  border: none;
}
.table thead th {
  border-bottom: 2px solid #dee2e6;
}
.table tbody tr:hover {
  background-color: #fafafa;
}
</style>
@endpush
