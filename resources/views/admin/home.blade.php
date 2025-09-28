{{-- resources/views/admin/home.blade.php --}}
@extends('admin.dashboard')

@section('title','Admin Dashboard')
@section('page-title','Dashboard')

@section('content')
  <div class="row g-3">
    <!-- Top cards -->
    <div class="col-md-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Registered Users</h6>
          <h2 class="mb-0">{{ $registeredUsers ?? 0 }}</h2>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Rooms for Rent</h6>
          <h2 class="mb-0">{{ $roomsForRent ?? 0 }}</h2>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Unpaid Rent</h6>
          <h2 class="mb-0">{{ $unpaidRent ?? 0 }}</h2>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Monthly Income</h6>
          <h2 class="mb-0">₱{{ number_format($monthlyIncome ?? 0,2) }}</h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Monthly Income (placeholder for chart) -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Monthly Income Overview</h6>
          <p class="text-muted">(Chart placeholder :D)</p>
          <div style="height:260px; display:flex; align-items:center; justify-content:center; border:1px dashed #e9ecef;">
            <small class="text-muted">SANA MAY CHART NA DEJK</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Tables (Bookings snapshot example) -->
  <div class="row mt-4">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Latest Bookings</h6>
          <table class="table table-sm">
            <thead>
              <tr>
                <th>ID</th><th>Full Name</th><th>Room</th><th>Status</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {{-- Example loop; pass $latestBookings from controller --}}
              @forelse($latestBookings ?? [] as $b)
                <tr>
                  <td>{{ $b->id }}</td>
                  <td>{{ $b->full_name }}</td>
                  <td>{{ $b->room->title ?? '—' }}</td>
                  <td>{{ $b->status }}</td>
                  <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted">No bookings yet</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Quick Actions</h6>
          <a href="{{ route('admin.rooms') }}" class="btn btn-sm btn-primary w-100 mb-2">Add Room</a>
          <a href="{{ route('admin.tenants') }}" class="btn btn-sm btn-outline-secondary w-100 mb-2">Add Tenant</a>
          <a href="{{ route('admin.bookings','create') }}" class="btn btn-sm btn-outline-success w-100">Add Booking</a>
        </div>
      </div>
    </div>
  </div>
@endsection
