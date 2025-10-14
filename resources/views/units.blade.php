@extends('layouts.app')

@section('title', 'Available Units | SubdiRent')

@section('content')
<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 section-header">
      <div>
        <h2>Available Units</h2>
        <p>Browse a curated list of ready-to-rent properties.</p>
      </div>
      <button class="btn view-all">View all</button>
    </div>

    <div class="row g-4">
      @foreach($units as $unit)
        <div class="col-md-6 col-lg-4">
          <div class="card unit-card">
            <img src="{{ asset('storage/' . $unit->image) }}" class="unit-img" alt="{{ $unit->name }}">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <h5 class="card-title">{{ $unit->name }}</h5>
                  <p class="text-muted small mb-0">{{ $unit->location }}</p>
                </div>
                <span class="badge badge-status">
                  {{ $unit->status ?? 'For Rent' }}
                </span>
              </div>
              <p class="price mb-2">₱{{ number_format($unit->price, 2) }}/month</p>
              <p class="text-muted small mb-3">{{ $unit->description }}</p>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-primary w-50">Reserve Unit</button>
                <button class="btn btn-primary w-50">Apply</button>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endsection
