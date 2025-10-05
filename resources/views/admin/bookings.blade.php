{{-- resources/views/admin/booking.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Page Title --}}
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-blue-900">Bookings</h3>
        </div>
    </div>

    {{-- Booking Summary Cards --}}
    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card">
                <div class="card-body">
                    <h6 class="card-title">Total Bookings</h6>
                    <h3 class="fw-bold">3</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card approved">
                <div class="card-body">
                    <h6 class="card-title">Approved Bookings</h6>
                    <h3 class="fw-bold">1</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card rejected">
                <div class="card-body">
                    <h6 class="card-title">Rejected Bookings</h6>
                    <h3 class="fw-bold">1</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Bookings List --}}
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center booking-header">
            <span class="fw-bold text-blue-900">Bookings List</span>
            <a href="#" class="btn btn-sm btn-action">+ Add Booking</a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 text-center booking-table align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contacts</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Dummy Data --}}
                        <tr>
                            <td>1</td>
                            <td>Juan Dela Cruz</td>
                            <td>juan@example.com</td>
                            <td>09123456789</td>
                            <td><span class="badge bg-success">Approved</span></td>
                            <td class="d-flex justify-content-center gap-2">
                                <a href="#" class="btn btn-sm btn-action">Edit</a>
                                <a href="#" class="btn btn-sm btn-outline-warning text-dark fw-semibold">Delete</a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Maria Santos</td>
                            <td>maria@example.com</td>
                            <td>09987654321</td>
                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                            <td class="d-flex justify-content-center gap-2">
                                <a href="#" class="btn btn-sm btn-action">Edit</a>
                                <a href="#" class="btn btn-sm btn-outline-warning text-dark fw-semibold">Delete</a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Carlos Reyes</td>
                            <td>carlos@example.com</td>
                            <td>09223334444</td>
                            <td><span class="badge bg-danger">Rejected</span></td>
                            <td class="d-flex justify-content-center gap-2">
                                <a href="#" class="btn btn-sm btn-action">Edit</a>
                                <a href="#" class="btn btn-sm btn-outline-warning text-dark fw-semibold">Delete</a>
                            </td>
                        </tr>
                        {{-- End Dummy Data --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
