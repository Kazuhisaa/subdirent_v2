{{-- resources/views/admin/booking.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold">BOOKINGS</h3>
        </div>
    </div>

    {{-- Booking Summary Cards --}}
    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">TOTAL BOOKINGS</h6>
                    <h3 class="fw-bold">3</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h6 class="text-success">APPROVED BOOKINGS</h6>
                    <h3 class="fw-bold text-success">1</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <h6 class="text-danger">REJECTED BOOKINGS</h6>
                    <h3 class="fw-bold text-danger">1</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Bookings List --}}
    <div class="card shadow-sm border-0 ">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <span class="fw-bold">BOOKINGS LIST</span>
            <a href="#" class="btn btn-sm btn-dark">Add Bookings</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>FULL NAME</th>
                            <th>EMAIL</th>
                            <th>CONTACTS</th>
                            <th>STATUS</th>
                            <th>ACTIONS</th>
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
                            <td>
                                <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                <a href="#" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Maria Santos</td>
                            <td>maria@example.com</td>
                            <td>09987654321</td>
                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                <a href="#" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Carlos Reyes</td>
                            <td>carlos@example.com</td>
                            <td>09223334444</td>
                            <td><span class="badge bg-danger">Rejected</span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                <a href="#" class="btn btn-sm btn-danger">Delete</a>
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
