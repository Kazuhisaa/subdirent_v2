@extends('tenant.dashboard')

@section('title', 'Tenant Payments')
@section('page-title', 'My Payments')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    {{-- Main Section --}}
    <div class="col-lg-8">
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light border-0">
          <h4 class="mb-0 text-primary fw-bold">Current Balance</h4>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded"
               style="background-color: #f4f9f6; border-left: 5px solid #4caf50;">
            <div>
              <h5 class="text-muted mb-1">Outstanding Balance</h5>
              <h2 class="text-success fw-bold mb-0">₱{{ number_format($outstanding ?? 0, 2) }}</h2>
              <p class="text-secondary small mt-1">
                For Month: {{ $nextBilling['date'] ?? 'N/A' }}
              </p>
            </div>

            <div>
              @if($nextBilling)
              <form method="POST" action="{{ route('tenant.payment.create', $tenant->id) }}">
                @csrf
                <input type="hidden" name="for_month" value="{{ $nextBilling['for_month'] ?? '' }}">

                <div class="mb-3">
                  <label for="amount" class="form-label fw-bold">Enter Payment Amount (₱)</label>
                  <input 
                      type="number"
                      name="amount"
                      id="amount"
                      class="form-control"
                      min="1000"
                      max="{{ $activeContract->monthly_payment ?? 0 }}"
                      step="0.01"
                      placeholder="Enter amount (₱)"
                      required>
                  <small class="text-muted">
                    Minimum payment: ₱1,000 — Maximum: ₱{{ number_format($activeContract->monthly_payment ?? 0, 2) }}
                  </small>
                </div>

                <select name="payment_method" class="form-select mb-3" required>
                  <option value="" disabled selected>-- Choose Payment Method --</option>
                  <option value="gcash">GCash</option>
                  <option value="card">Credit/Debit Card</option>
                </select>

                <button type="submit" class="btn btn-success w-100">Pay Now</button>
              </form>

              <a href="#" class="btn btn-outline-secondary w-100 mt-2">Set Up Autopay</a>
              @endif
            </div>
          </div>

          {{-- Next Month Billing Section --}}
          <h5 class="text-primary mt-5 mb-3 fw-semibold">
            Next Month Billing: {{ $nextBilling['month_year'] ?? 'N/A' }}
          </h5>

          <table class="table table-borderless align-middle">
            <thead>
              <tr class="text-muted">
                <th>Description</th>
                <th class="text-end">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Rent for {{ $nextBilling['month_name'] ?? 'N/A' }}</td>
                <td class="text-end">₱{{ number_format($activeContract->monthly_payment ?? 0, 2) }}</td>
              </tr>
              <tr class="border-top fw-bold">
                <td>Total Next Bill</td>
                <td class="text-end text-danger">₱{{ number_format($activeContract->monthly_payment ?? 0, 2) }}</td>
              </tr>
            </tbody>
          </table>

          <div class="alert alert-info mt-3 mb-0" style="border-left: 4px solid #0d6efd;">
            <small>
              <i class="bi bi-info-circle"></i>
              Your next bill will be generated automatically on 
              <strong>{{ $nextBilling['date'] ?? 'N/A' }}</strong>.
            </small>
          </div>
        </div>
      </div>
    </div>

    {{-- Sidebar Section --}}
    <div class="col-lg-4">
      {{-- Ledger Trigger --}}
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light border-0">
          <h6 class="fw-bold text-primary mb-0">Account Ledger</h6>
        </div>
        <div class="card-body text-center">
          <p class="text-muted small">Need help understanding your balance?</p>
          <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ledgerModal">
            View Account Ledger
          </button>
        </div>
      </div>

      {{-- Past Payments --}}
      <div class="card shadow-sm border-0">
        <div class="card-header bg-light border-0">
          <h6 class="fw-bold text-primary mb-0">Past Payments</h6>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            @forelse($payments as $payment)
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <span class="badge bg-success rounded-circle me-2" style="width:10px;height:10px;">&nbsp;</span>
                  <strong>{{ $payment->remarks ?? 'Payment' }} on {{ \Carbon\Carbon::parse($payment->payment_date)->format('m/d/Y') }}</strong>
                </div>
                <div class="fw-semibold text-success">₱{{ number_format($payment->amount, 2) }}</div>
              </li>
            @empty
              <li class="list-group-item text-center text-muted">No past payments found.</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Include Ledger Modal Partial --}}
@include('partials.ledger-modal')
@endsection

@push('styles')
<style>
  body { background-color: #f8f9fb; }
  .card { border-radius: 12px; }
  .btn-primary { background-color: #4facfe; border-color: #4facfe; }
  .btn-primary:hover { background-color: #00c6ff; }
  .btn-success { background-color: #4caf50; border-color: #4caf50; }
  .btn-success:hover { background-color: #45a049; }
  .text-success { color: #4caf50 !important; }
</style>
@endpush
