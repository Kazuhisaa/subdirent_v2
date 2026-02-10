@extends('tenant.dashboard')

@section('title', 'My Payments')
@section('page-title', 'Billing & Payments')

@section('content')
<div class="container py-4">
    <div class="row g-4">

        {{-- LEFT COLUMN: Outstanding Balance + Payment (Preserving Original Look) --}}
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

                    
                        <button type="submit" class="btn btn-success w-100 fw-semibold mb-2 shadow-sm">
                            <i class="bi bi-cash-stack me-1"></i> Pay Now
                        </button>
                    </form>
                    @endif

                    {{-- Dynamic Payment Status Notification (Original content preserved) --}}
                    <div class="payment-status text-center mt-3">
                        @if($paymentStatus['currentMonthPaid'] && $paymentStatus['nextMonthPaid'])
                        <div class="alert alert-success py-2 small shadow-sm mb-0">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            You're fully paid for <strong>{{ $paymentStatus['currentMonth'] }}</strong> and <strong>{{ $paymentStatus['nextMonth'] }}</strong>! 🎉
                        </div>
                        @elseif($paymentStatus['currentMonthPaid'] && !$paymentStatus['nextMonthPaid'])
                        <div class="alert alert-info py-2 small shadow-sm mb-0">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            You've already paid for <strong>{{ $paymentStatus['currentMonth'] }}</strong>. Next rent is due soon.
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

        {{-- RIGHT COLUMN: History, Autopay, Ledger (Enhanced Structure) --}}
        <div class="col-lg-8">
            <div class="row g-4">
                
                {{-- Recent Payments Table --}}
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="fw-bold text-dark mb-0">Recent Rent Payments (Last 5)</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-borderless align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Description</th>
                                        <th>Date Paid</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-center pe-4">Invoice</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Limits the table to 5 transactions, as previously requested.
                                        $rentPayments = $payments->filter(function ($payment) {
                                            return str_starts_with($payment->remarks, 'Rent Payment');
                                        })->sortByDesc('payment_date')->take(5); 
                                    @endphp

                                    @forelse($rentPayments as $payment)
                                    <tr>
                                        <td class="ps-4">
                                            <strong class="text-primary">{{ $payment->remarks }}</strong><br>
                                            <small class="text-muted">For: {{ \Carbon\Carbon::parse($payment->for_month)->format('F Y') }}</small>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            ₱{{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td class="text-center pe-4">
                                            {{-- NEW / FIXED CODE --}}
                                            @if($payment->invoice_pdf)
                                            <a href="{{ route('tenant.payment.invoice.download', $payment->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                            @else
                                            <span class="text-muted small">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No recent rent payments found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Autopay Settings --}}
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">⚙️ Autopay Settings</h5>
                                @if ($tenant->autopay && $tenant->autopay->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success">ACTIVE</span>
                                @elseif($tenant->autopay && $tenant->autopay->status === 'paused')
                                    <span class="badge bg-warning-subtle text-warning border border-warning">PAUSED</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary">NOT SET UP</span>
                                @endif
                            </div>

                            @if ($tenant->autopay)
                                @if ($tenant->autopay->status === 'active')
                                    <p class="text-muted small">
                                        Your next automated payment is scheduled for <strong>{{ \Carbon\Carbon::parse($tenant->autopay->next_due_date)->format('F d, Y') }}</strong>.
                                    </p>
                                    {{-- Pause Autopay --}}
                                    <form action="{{ route('autopay.pause', $tenant->autopay->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-warning">
                                            ⏸ Pause Autopay
                                        </button>
                                    </form>

                                @elseif($tenant->autopay->status === 'paused')
                                    <p class="text-muted small">
                                        Autopay is temporarily paused. Activate it to ensure timely payments.
                                    </p>
                                    {{-- Activate Autopay --}}
                                    <form action="{{ route('autopay.activate', $tenant->autopay->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">
                                            ▶️ Activate Autopay
                                        </button>
                                    </form>
                                @endif
                            @else
                                {{-- No autopay exists, show card setup --}}
                                <p class="text-muted small">
                                    Set up Autopay using your card to automatically pay your monthly rent on the due date.
                                </p>

                                <form action="{{ route('autopay.setup', ['tenantId' => $tenant->id, 'contractId' => $activeContract->id]) }}" method="POST" id="autopayForm">
                                    @csrf

                                    {{-- Stripe Card Element --}}
                                    <div class="mb-3">
                                        <label for="card-element" class="form-label fw-semibold">Card Details</label>
                                        <div id="card-element" class="form-control p-2 border"></div>  
                                        <div id="card-errors" class="text-danger small mt-2"></div>
                                    </div>

                                    <input type="hidden" name="payment_method" id="payment_method">

                                    <button type="submit" class="btn btn-sm btn-primary">
                                        🟢 Set Up & Activate Autopay
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Ledger Button (Access to all transactions) --}}
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center py-3">
                            <p class="text-muted small mb-2">View every transaction, bill, and payment recorded on your account.</p>
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
/* Keeping and enhancing custom styles for professional look */
body {
  background-color: #f4f6f9; /* Lighter background */
  font-family: "Inter", sans-serif;
}
.card {
  border-radius: 12px; /* Slightly softer corners */
  transition: transform 0.1s ease, box-shadow 0.1s ease;
}
.card:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.card-summary {
    background-color: #ffffff;
    border: 1px solid #e9ecef;
}
.display-3 {
    font-size: 3.5rem;
}
.btn-success {
    background-color: #4CAF50; /* Original color */
    border-color: #4CAF50;
}
.btn-success:hover {
    background-color: #43a047;
}
.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}
.btn-primary:hover {
  background-color: #0069d9;
}
.alert-success {
  background-color: #eaf7ed; /* Light green background */
  color: #155724;
  border: 1px solid #c3e6cb;
}
.alert-info {
  background-color: #e8f5ff; /* Light blue background */
  color: #004085;
  border: 1px solid #b8daff;
}
.alert-warning {
  background-color: #fff8e6; /* Light yellow background */
  color: #856404;
  border: 1px solid #ffeeba;
}
.table thead th {
  border-bottom: 1px solid #dee2e6;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.8rem;
  color: #6c757d;
}
.table tbody tr:hover {
  background-color: #f7f7f7;
}
.table-borderless td, .table-borderless th {
    border: none;
}
</style>
@endpush


@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener("DOMContentLoaded", async () => {
  // Siguraduhin mong tama ang Stripe key mo sa .env file
  const stripe = Stripe("{{ config('services.stripe.key') }}");
  const elements = stripe.elements();

  const card = elements.create("card", {
    style: {
      base: {
        color: "#32325d",
        fontFamily: '"Inter", sans-serif',
        fontSize: "16px",
        "::placeholder": { color: "#aab7c4" }
      },
      invalid: { color: "#fa755a" }
    }
  });
  
  // Imamount lang ito kung may #card-element sa page
  if (document.getElementById("card-element")) {
      card.mount("#card-element");
  }

  const form = document.getElementById("autopayForm");
  
  // Mag-attach lang ng listener kung may form sa page
  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const { paymentMethod, error } = await stripe.createPaymentMethod({
        type: "card",
        card: card,
        billing_details: {
          name: "{{ $tenant->name }}",
          email: "{{ $tenant->email }}"
        }
      });

      if (error) {
        document.getElementById("card-errors").textContent = error.message;
        return;
      }

      // Ilalagay ang paymentMethod.id sa hidden input
      document.getElementById("payment_method").value = paymentMethod.id;
      
      // Isusubmit na ang form kasama ang (payment_method, tenant_id, contract_id)
      form.submit();
    });
  }
});
</script>
@endpush