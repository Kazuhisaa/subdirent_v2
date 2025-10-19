@extends('tenant.dashboard') 

@section('title', 'Tenant Payments')
@section('page-title', 'My Payments')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
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
                            {{-- Galing na 'to sa controller calculation --}}
                            <h2 class="text-success fw-bold mb-0">₱{{ number_format($outstanding ?? 0, 2) }}</h2>
                            <p class="text-secondary small mt-1">
                                {{-- Galing na rin sa controller --}}
                                Next bill due on <strong>{{ $nextMonth['date'] ?? 'N/A' }}</strong>
                            </p>
                        </div>
                        <div>
                            {{-- Che-check kung may next month na naka-calculate --}}
                            @if(isset($nextMonth['for_month']))
                                {{-- Ang $tenant dito ay ang Tenant model na (ID 98) --}}
                                <form method="POST" action="{{ route('tenant.payment.make') }}">
    @csrf
    <input type="hidden" name="for_month" value="{{ $nextMonth['for_month'] }}">
    <input type="hidden" name="amount" value="{{ $activeContract->monthly_payment ?? 0 }}">
    
    <select name="payment_method" class="form-select mb-2" required>
        <option value="" disabled selected>-- Choose Payment Method --</option>
        <option value="gcash">GCash</option>
        <option value="card">Credit/Debit Card</option>
    </select>

    <button type="submit" class="btn btn-success w-100">Pay Now</button>
</form>

                                <a href="#" class="btn btn-outline-secondary w-100 mt-2">
                                    Set Up Autopay
                                </a>
                            @endif
                        </div>
                    </div>

                    <h5 class="text-primary mt-4 mb-3 fw-semibold">This Month: {{ now()->format('F Y') }}</h5>
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr class="text-muted">
                                <th>Description</th>
                                <th class="text-end">Amount</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Rent</td>
                                {{-- BINAGO DITO: Kinukuha na sa $activeContract --}}
                                <td class="text-end">₱{{ number_format($activeContract->monthly_payment ?? 0, 2) }}</td>
                            </tr>
                            <tr class="border-top fw-bold">
                                <td>Total Balance</td>
                                <td class="text-end text-success">₱{{ number_format($outstanding ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light border-0">
                    <h6 class="fw-bold text-primary mb-0">Account Ledger</h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted small">Need help understanding your balance?</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">View full account ledger</a>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0">
                    <h6 class="fw-bold text-primary mb-0">Past Payments</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        {{-- BINAGO DITO: Ang $payment ay object na, hindi array --}}
                        @forelse($payments as $payment)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-success rounded-circle me-2" style="width:10px;height:10px;">&nbsp;</span>
                                {{-- Ginamit ang $payment->payment_date at $payment->remarks --}}
                                <strong>{{ $payment->remarks ?? 'Payment' }} on {{ $payment->payment_date->format('m/d/Y') }}</strong><br>
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