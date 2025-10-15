{{-- resources/views/admin/applications.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Page Title --}}
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-blue-900">Applications</h3>
        </div>
    </div>

    {{-- Application Summary Cards --}}
    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card">
                <div class="card-body">
                    <h6 class="card-title">Total Applications</h6>
                    <h3 class="fw-bold">{{ $applications->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card approved">
                <div class="card-body">
                    <h6 class="card-title">Approved Applications</h6>
                    <h3 class="fw-bold">{{ $applications->where('status', 'Approved')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card rejected">
                <div class="card-body">
                    <h6 class="card-title">Rejected Applications</h6>
                    <h3 class="fw-bold">{{ $applications->where('status', 'Rejected')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Applications List --}}
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center booking-header">
            <span class="fw-bold text-blue-900">Applications List</span>
            {{-- <a href="{{ route('admin.applications.create') }}" class="btn btn-sm btn-action">+ Add Application</a> --}}
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 text-center booking-table align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            <tr>
                                <td>{{ $application->id }}</td>
                                <td>{{ $application->first_name }} {{ $application->middle_name }} {{ $application->last_name }}</td>
                                <td>{{ $application->email }}</td>
                                <td>{{ $application->contact_num }}</td>
                                <td>{{ $application->unit ? $application->unit->unit_name ?? 'N/A' : 'N/A' }}</td>
                                <td>
                                    @if($application->status == 'Approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($application->status == 'Rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="d-flex justify-content-center gap-2">
                                    {{-- Approve --}}
                                    @if($application->status !== 'Approved')
                                        <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                    @endif

                                    {{-- Deny --}}
                                    @if($application->status !== 'Rejected')
                                        <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Deny</button>
                                        </form>
                                    @endif

                                    {{-- Archive --}}
                                    <form action="{{ route('admin.applications.archive', $application->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning text-dark fw-semibold">Archive</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">No applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
