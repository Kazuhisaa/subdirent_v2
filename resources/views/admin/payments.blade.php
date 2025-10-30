@extends('admin.dashboard')

@section('page-title', 'Payments')

@section('content')
<div class="container-fluid py-4">

    {{-- ✅ Page Header --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold text-blue-900">Payment Records</h3>

            <button class="btn btn-action rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#archivedPaymentsModal">
                <i class="bi bi-archive-fill me-1"></i> Archived Payments
            </button>
        </div>
    </div>

    {{-- ✅ Summary Cards (optional) --}}
    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card">
                <div class="card-body">
                    <h6 class="card-title">Total Payments</h6>
                    <h3 class="fw-bold">{{ $payments->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card approved">
                <div class="card-body">
                    <h6 class="card-title">Paid</h6>
                    <h3 class="fw-bold">{{ $payments->where('payment_status', 'paid')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card rejected">
                <div class="card-body">
                    <h6 class="card-title">Unpaid</h6>
                    <h3 class="fw-bold">{{ $payments->where('payment_status', 'unpaid')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Payments Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            PAYMENT RECORDS LIST
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 text-center booking-table align-middle">
                    <thead>
                        <tr>
                            <th>Tenant ID</th>
                            <th>Tenant Name</th>
                            <th>Payment Status</th>
                            <th>Payment Date</th>
                            <th>Payment Method</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->tenant_id }}</td>
                            <td>
                                @if($payment->tenant)
                                    {{ $payment->tenant->first_name ?? '' }} {{ $payment->tenant->last_name ?? '' }}
                                @else
                                    <span class="text-muted">Unknown Tenant</span>
                                @endif
                            </td>                                
                            <td>
                                @if($payment->payment_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($payment->payment_status === 'partial')
                                    <span class="badge bg-warning text-dark">Partial</span>
                                @else
                                    <span class="badge bg-danger">Unpaid</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                            <td>{{ ucfirst($payment->payment_method ?? '—') }}</td>
                            <td>{{ $payment->remarks ?? 'No remarks' }}</td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                        <a href="{{ route('admin.admin.payments.download', $payment->id) }}" 
                                        class="btn btn-sm btn-outline-primary" 
                                        title="Download Invoice">
                                            <i class="bi bi-file-earmark-arrow-down"></i>
                                        </a>
                                    <form action="{{ route('admin.admin.payments.archive', $payment->id) }}" 
                                          method="POST" class="mb-0">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Archive this payment record?')" 
                                                title="Archive">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No payment records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ✅ Archived Payments Modal --}}
<div class="modal fade" id="archivedPaymentsModal" tabindex="-1" aria-labelledby="archivedPaymentsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(90deg, #007BFF, #0A2540);">
                <h5 class="modal-title fw-bold">Archived Payments</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tenant ID</th>
                                <th>Tenant Name</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($archivedPayments as $archived)
                                <tr>
                                    <td>{{ $archived->tenant_id }}</td>
                                    <td>
                                        @if($archived->tenant)
                                            {{ $archived->tenant->first_name ?? '' }} {{ $archived->tenant->last_name ?? '' }}
                                        @else
                                            <span class="text-muted">Unknown Tenant</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($archived->payment_status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($archived->payment_status === 'partial')
                                            <span class="badge bg-warning text-dark">Partial</span>
                                        @else
                                            <span class="badge bg-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($archived->payment_date)->format('M d, Y') }}</td>
                                    <td>{{ ucfirst($archived->payment_method ?? '—') }}</td>
                                    <td>{{ $archived->remarks ?? 'No remarks' }}</td>
                                    <td>
                                    <form action="{{ route('admin.admin.payments.restore', $archived->id) }}" 
                                        method="POST" 
                                        class="restoreForm mb-0" 
                                        data-id="{{ $archived->id }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                        </button>
                                    </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">No archived payments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const restoreForms = document.querySelectorAll('.restoreForm');

    restoreForms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!confirm('Restore this archived payment?')) return;

            const formData = new FormData(form);
            const action = form.getAttribute('action');

            try {
                const response = await fetch(action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) throw new Error('Network error');

                const result = await response.json();

                alert(result.message || 'Payment restored successfully!');

                const modal = bootstrap.Modal.getInstance(document.getElementById('archivedPaymentsModal'));
                modal.hide();

                setTimeout(() => window.location.reload(), 500);

            } catch (error) {
                console.error(error);
                alert('An error occurred while restoring the payment.');
            }
        });
    });
});
</script>
@endpush


@endsection
