{{-- resources/views/admin/maintenance.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900">Maintenance</h2>
    </div>

    {{-- ✅ CHANGED: Wrapped the table in the same card style as the tenants page --}}
    <div class="card border-0 shadow-sm">

        {{-- ✅ ADDED: A gradient card-header to match --}}
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            <span>MAINTENANCE REQUESTS</span>

            {{-- ✅ MOVED: The "Add Request" button is now here --}}
            <a href="#" class="btn btn-sm text-white fw-semibold" 
                    style="background: linear-gradient(90deg, #2A9DF4, #0A2540); border:none; border-radius: 6px;">
                + Add Request
            </a>
        </div>

        {{-- Maintenance Requests Table --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- ✅ ADDED: The .tenant-table class for consistent styling --}}
                <table class="table align-middle mb-0 tenant-table text-center">
                    <thead class="table-light small text-uppercase text-secondary">
                        <tr>
                            <th class="fw-semibold py-3 ps-4">ID</th>
                            <th class="fw-semibold">Full Name</th>
                            <th class="fw-semibold">Room</th>
                            <th class="fw-semibold">Issue Type</th>
                            <th class="fw-semibold">Description</th>
                            <th class="fw-semibold">Date</th>
                            <th class="fw-semibold">Status</th>
                            <th class="fw-semibold pe-4">Actions</th>
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
                            {{-- This 'bg-success' is styled by your CSS --}}
                            <td><span class="badge bg-success">Complete</span></td>
                            <td>
                                {{-- ✅ CHANGED: Buttons now use icons and theme classes --}}
                                <a href="#" class="btn btn-sm btn-outline-blue">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-archive-fill"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Maria Santos</td>
                            <td>Room 203</td>
                            <td>Electrical</td>
                            <td>No power in outlets</td>
                            <td>2025-10-02</td>
                            {{-- This 'bg-secondary' is styled by your CSS --}}
                            <td><span class="badge bg-secondary">Pending</span></td>
                            <td>
                                {{-- ✅ CHANGED: Buttons now use icons and theme classes --}}
                                <a href="#" class="btn btn-sm btn-outline-blue">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-archive-fill"></i>
                                </a>
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
                                {{-- ✅ CHANGED: Buttons now use icons and theme classes --}}
                                <a href="#" class="btn btn-sm btn-outline-blue">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-archive-fill"></i>
                                </a>
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