@extends('tenant.dashboard')

@section('title', 'Tenant Payments')
@section('page-title', 'My Payments')

@section('content')
<div class="container-fluid py-4">
  <div class="row">

    {{-- Left: Current Balance --}}
    <div class="col-lg-4">
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body text-center">
          <h5 class="text-muted mb-1">Outstanding Balance</h5>
          <h2 class="text-success fw-bold mb-1">₱{{ number_format($outstanding ?? 0, 2) }}</h2>
          <p class="text-secondary small mb-2">For Month: {{ $nextMonth['date'] ?? 'N/A' }}</p>

          @if(isset($nextMonth) && $nextMonth['for_month'])
          <form method="POST" action="{{ route('tenant.payment.create', $tenant->id) }}">
            @csrf
            <input type="hidden" name="for_month" value="{{ $nextMonth['for_month'] }}">
            <input type="number" name="amount" class="form-control mb-2"
                   min="1000" max="{{ $amountToPay ?? 0 }}" step="0.01"
                   placeholder="Enter payment (₱)" required>
            <select name="payment_method" class="form-select mb-2" required>
              <option value="" disabled selected>-- Choose Payment Method --</option>
              <option value="gcash">GCash</option>
              <option value="card">Credit/Debit Card</option>
            </select>
            <button type="submit" class="btn btn-success w-100 mb-2">Pay Now</button>
          </form>
          @endif
          <a href="#" class="btn btn-outline-secondary w-100">Set Up Autopay</a>
        </div>
      </div>
    </div>

    {{-- Right: Past Payments + Ledger --}}
    <div class="col-lg-8">
      <div class="row">
        {{-- Past Payments Table --}}
        <div class="col-12 mb-3">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-light border-0">
              <h6 class="fw-bold text-primary mb-0">Recent Payments</h6>
            </div>
            <div class="card-body p-0">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Payment Details</th>
                    <th>Invoice</th>
                  </tr>
                </thead>
                <tbody>
@php
    $rentPayments = $payments->filter(function ($payment) {
        return str_starts_with($payment->remarks, 'Rent Payment');
    })->sortByDesc('for_month');
@endphp

    @forelse($rentPayments as $payment)
    <tr>
        <td>
            <strong>{{ $payment->remarks }}</strong><br>
            <small>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</small><br>
            <span class="text-success fw-bold">₱{{ number_format($payment->amount, 2) }}</span>
        </td>
        <td>
            @if($payment->invoice_pdf)
            <a href="{{ asset('storage/' . $payment->invoice_pdf) }}" target="_blank" class="btn btn-sm btn-primary">
                Download Invoice
            </a>
            @else
            <span class="text-muted">N/A</span>
            @endif
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="2" class="text-center text-muted">No rent payments yet.</td>
    </tr>
    @endforelse
</tbody>

              </table>
            </div>
          </div>
        </div>

        {{-- Ledger Button --}}
        <div class="col-12">
          <div class="card shadow-sm border-0">
            <div class="card-body text-center">
              <p class="text-muted small mb-2">Need help understanding your balance?</p>
              <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ledgerModal">
                View Full Account Ledger
              </button>
            </div>
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
  body { background-color: #f8f9fb; }
  .card { border-radius: 12px; }
  .btn-success { background-color: #4caf50; border-color: #4caf50; }
  .btn-success:hover { background-color: #45a049; }
  .text-success { color: #4caf50 !important; }
</style>
@endpush
