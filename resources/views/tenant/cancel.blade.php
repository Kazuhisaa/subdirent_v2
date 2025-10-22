@extends('tenant.dashboard') 

@section('title', 'Payment Cancelled')

@section('content')
<div class="container py-5 text-center">
    <div class="card shadow-sm border-0 p-4 mx-auto" style="max-width: 480px; border-radius: 15px;">
        <div class="mb-4">
            <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
        </div>
        <h3 class="fw-bold text-danger mb-2">Payment Cancelled</h3>
        <p class="text-muted mb-4">Your payment has been cancelled. If this was a mistake, you can try again below.</p>
        
        <a href="{{ route('tenant.home', $tenant->id) }}" class="btn btn-outline-danger w-100">
            <i class="bi bi-arrow-left me-1"></i> Return to Payments
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
