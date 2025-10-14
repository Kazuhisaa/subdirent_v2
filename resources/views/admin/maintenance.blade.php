{{-- resources/views/admin/maintenance.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-dark" style="color:#0A2540;">MAINTENANCE</h3>
        </div>
    </div>

    {{-- Maintenance Summary Cards --}}
    <div class="row text-center mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="background:#EAF8FF;">
                <div class="card-body">
                    <h6 class="text-muted">TOTAL REQUESTS</h6>
                    <h3 class="fw-bold text-dark">3</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h6 class="text-success">COMPLETE</h6>
                    <h3 class="fw-bold text-success">1</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-info">
                <div class="card-body">
                    <h6 class="text-info">PENDING</h6>
                    <h3 class="fw-bold text-info">1</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <h6 class="text-primary">IN PROGRESS</h6>
                    <h3 class="fw-bold text-primary">1</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Maintenance Requests Table --}}
    <div class="card shadow-sm border-0" style="background-color:#EAF8FF;">
        <div class="card-header d-flex justify-content-between align-items-center text-white fw-bold"
             style="background: linear-gradient(90deg, #2A9DF4, #0A2540);">
            <span>MAINTENANCE REQUESTS</span>
            <a href="#" class="btn btn-sm text-white"
               style="background: linear-gradient(90deg, #2A9DF4, #0A2540); border:none;">
               + Add Request
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 text-center align-middle">
                    <thead style="background-color:#D8EEFF; color:#0A2540;">
                        <tr>
                            <th>ID</th>
                            <th>FULL NAME</th>
                            <th>ROOM</th>
                            <th>ISSUE TYPE</th>
                            <th>DESCRIPTION</th>
                            <th>DATE</th>
                            <th>STATUS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Dummy Data --}}
                        <tr>
                            <td>1</td>
                            <td>Juan Dela Cruz</td>
                            <td>Room 101</td>
                            <td>Plumbing</td>
                            <td>Leaking faucet in bathroom</td>
                            <td>2025-10-01</td>
                            <td><span class="badge bg-success">Complete</span></td>
                            <td>
                                <a href="#" class="btn btn-sm text-white" 
                                   style="background-color:#2A9DF4;">Edit</a>
                                <a href="#" class="btn btn-sm text-white" 
                                   style="background-color:#0A2540;">Archive</a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Maria Santos</td>
                            <td>Room 203</td>
                            <td>Electrical</td>
                            <td>No power in outlets</td>
                            <td>2025-10-02</td>
                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                            <td>
                                <a href="#" class="btn btn-sm text-white" 
                                   style="background-color:#2A9DF4;">Edit</a>
                                <a href="#" class="btn btn-sm text-white" 
                                   style="background-color:#0A2540;">Archive</a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Carlos Reyes</td>
                            <td>Room 305</td>
                            <td>Aircon</td>
                            <td>Not cooling properly</td>
                            <td>2025-10-03</td>
                            <td><span class="badge bg-primary">In Progress</span></td>
                            <td>
                                <a href="#" class="btn btn-sm text-white" 
                                   style="background-color:#2A9DF4;">Edit</a>
                                <a href="#" class="btn btn-sm text-white" 
                                   style="background-color:#0A2540;">Archive</a>
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
