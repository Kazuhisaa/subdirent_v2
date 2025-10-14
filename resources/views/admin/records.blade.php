@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-blue-900">RECORDS</h3>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background-color: var(--blue-100);">
        <div class="card-header border-0" style="background-color: var(--blue-200);">
            <span class="fw-bold text-blue-800">OVERALL RECORDS</span>
        </div>

        <div class="card-body">
            <table class="table table-bordered text-center booking-table mb-0">
                <thead>
                    <tr>
                        <th>FULL NAME</th>
                        <th>ROOM</th>
                        <th>PHASE</th>
                        <th>STATUS</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Dummy Data --}}
                    <tr>
                        <td>Juan Dela Cruz</td>
                        <td>Room 101</td>
                        <td>Application</td>
                        <td><span class="badge bg-success">Approved</span></td>
                        <td>2025-09-01</td>
                    </tr>
                    <tr>
                        <td>Maria Santos</td>
                        <td>Room 202</td>
                        <td>Contract</td>
                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                        <td>2025-09-05</td>
                    </tr>
                    <tr>
                        <td>Pedro Reyes</td>
                        <td>Room 303</td>
                        <td>Payment</td>
                        <td><span class="badge bg-danger">Overdue</span></td>
                        <td>2025-09-10</td>
                    </tr>
                    <tr>
                        <td>Ana Villanueva</td>
                        <td>Room 404</td>
                        <td>Maintenance</td>
                        <td><span class="badge bg-info text-dark">In Progress</span></td>
                        <td>2025-09-15</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="text-end mt-3">
                <button class="btn btn-action">Generate as PDF</button>
            </div>
        </div>
    </div>
</div>
@endsection
