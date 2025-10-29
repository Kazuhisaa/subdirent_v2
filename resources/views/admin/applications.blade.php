@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Page Title --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold text-blue-900">Applications</h3>

            <button class="btn btn-action rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#archivedModal">
                <i class="bi bi-archive-fill me-1"></i> Archived Applicants
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row text-center mb-4">
        {{-- ... summary card content ... --}}
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
    <div class="card border-0 shadow-sm">

        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            APPLICATIONS LIST
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- Ensure align-middle is here for overall row alignment --}}
                <table class="table mb-0 text-center booking-table align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Created At</th>
                            <th>Unit</th>
                            <th>Price</th>
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
                            <td>{{ $application->created_at?->format('M d, Y') }}</td>
                            <td>
                                @if($application->unit)
                                    <div class="d-flex flex-column align-items-center">
                                        <strong>{{ $application->unit->unit_name }}</strong>
                                        <small class="text-muted">{{ $application->unit->title }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            
                            <td>
                                @if($application->unit_price)
                                    ₱{{ number_format($application->unit_price, 2) }}
                                @elseif($application->unit)
                                    ₱{{ number_format($application->unit->unit_price, 2) }}
                                @else — @endif
                            </td>
                            <td>
                                @if($application->status === 'Approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($application->status === 'Rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>

                            {{-- Action buttons --}}
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    @if($application->status === 'Pending')
                                        {{-- Approve Button --}}
                                        <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST" class="mb-0">@csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>

                                        {{-- Deny Button --}}
                                        <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST" class="mb-0">@csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="Deny">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Archive Button (Always Visible) --}}
                                    <form action="{{ route('admin.applications.archive', $application->id) }}" method="POST" class="mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Archive">
                                            <i class="bi bi-archive-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-muted py-3">No applications found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Archived Applications Modal --}}
<div class="modal fade" id="archivedModal" tabindex="-1" aria-labelledby="archivedModalLabel" aria-hidden="true">
    {{-- ... modal content ... --}}
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="archivedModalLabel">Archived Applicants</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-striped text-center align-middle">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Unit</th>
                    <th>Archived At</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="archivedTableBody">
                  <tr><td colspan="7" class="text-muted">Loading...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
</div>

{{-- JS to load archived applicants --}}
<script>
    // ... modal javascript ...
    document.getElementById('archivedModal').addEventListener('show.bs.modal', async () => {
        const tbody = document.getElementById('archivedTableBody');
        tbody.innerHTML = '<tr><td colspan="7" class="text-muted">Loading...</td></tr>';

        const token = sessionStorage.getItem('admin_api_token');
        if (!token) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-danger">⚠ Authorization token not found.</td></tr>';
            return;
        }

        try {
            const res = await fetch('/api/archived-applications', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error('Failed to fetch archived data.');

            const data = await res.json();

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-muted">No archived applicants.</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(app => {
                const unitName = app.unit ? app.unit.unit_name : '—';
                const archivedDate = app.archived_at ? new Date(app.archived_at).toLocaleDateString() : '—';

                return `
                    <tr>
                        <td>${app.id}</td>
                        <td>${app.first_name} ${app.middle_name ?? ''} ${app.last_name}</td>
                        <td>${app.email}</td>
                        <td>${app.contact_num}</td>
                        <td>${unitName}</td>
                        <td>${archivedDate}</td>
                        <td>
                            <form method="POST" action="/admin/applications/${app.id}/restore">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                </button>
                            </form>
                        </td>
                    </tr>
                `
            }).join('');

        } catch (err) {
            console.error('Error loading archived applicants:', err);
            tbody.innerHTML = `<tr><td colspan="7" class="text-danger">⚠ ${err.message}</td></tr>`;
        }
    });
</script>
@endsection