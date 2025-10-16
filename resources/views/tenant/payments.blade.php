@extends('tenant.dashboard')

@section('title', 'Tenant Payments')
@section('page-title', 'My Payments')


@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Current Balance Card -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0">
                    <h4 class="mb-0 text-primary fw-bold">Payments</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded" 
                         style="background-color: #f4f9f6; border-left: 5px solid #4caf50;">
                        <div>
                            <h5 class="text-muted mb-1">Your Current Balance</h5>
                            <h2 class="text-success fw-bold mb-0">₱200.00</h2>
                            <p class="text-secondary small mt-1">Next bill due on <strong>December 11, 2025</strong></p>
                        </div>
                        <div>
                            <button class="btn btn-primary btn-lg px-4 me-2">Pay Now</button>
                            <button class="btn btn-outline-secondary btn-lg px-4">Set Up Autopay</button>
                        </div>
                    </div>

                    <h5 class="text-primary mt-4 mb-3 fw-semibold">December (This Month)</h5>
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
                                <td class="text-end">₱2000.00</td>
                            </tr>
                            <tr class="border-top fw-bold">
                                <td>Total Balance</td>
                                <td class="text-end text-success">₱2000.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Account Ledger + Past Payments -->
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
                        @foreach([
                            ['date' => '10/10/2025', 'confirmation' => 'A1B2-C3D4', 'amount' => 2000.00],
                            ['date' => '09/09/2025', 'confirmation' => 'F5G6-H7I8', 'amount' => 2000.00],
                            ['date' => '08/08/2025', 'confirmation' => 'J9K1-L2M3', 'amount' => 2000.00],
                        ] as $payment)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-success rounded-circle me-2" style="width:10px;height:10px;">&nbsp;</span>
                                <strong>Paid on {{ $payment['date'] }}</strong><br>
                                <small class="text-muted">Confirmation #: {{ $payment['confirmation'] }}</small>
                            </div>
                            <div class="fw-semibold text-success">₱{{ number_format($payment['amount'], 2) }}</div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    body {
        background-color: #f8f9fb;
    }
    .card {
        border-radius: 12px;
    }
    .btn-primary {
        background-color: #4facfe;
        border-color: #4facfe;
    }
    .btn-primary:hover {
        background-color: #00c6ff;
    }
    .text-success {
        color: #4caf50 !important;
    }
</style>
@endpush
