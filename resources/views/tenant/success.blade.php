@extends('tenant.dashboard') 

@section('title', 'Payment Successful')

@section('content')
<div class="container py-5 text-center">
    <div class="card shadow-sm border-0 p-4 mx-auto" style="max-width: 480px; border-radius: 15px;">
        <div class="mb-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
        </div>
        <h3 class="fw-bold text-success mb-2">Payment Successful!</h3>
        <p class="text-muted mb-4">Thank you for your payment. Your transaction has been successfully processed.</p>
        
        <a href="{{ route('tenant.payments', $tenant->id) }}" class="btn btn-success w-100">
            <i class="bi bi-house-door me-1"></i> Back to Payments
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
body { background-color: #f8f9fb; }
.card { border-radius: 12px; }
</style>
@endpush
