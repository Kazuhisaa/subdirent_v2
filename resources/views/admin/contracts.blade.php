{{-- resources/views/admin/contracts.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Page Title --}}
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-blue-900 mb-0">Contracts</h3>
        </div>
    </div>

    {{-- Contract Summary Cards --}}
    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card">
                <div class="card-body">
                    <h6 class="card-title">Active Contracts</h6>
                    <h3 id="activeContracts" class="fw-bold">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card approved">
                <div class="card-body">
                    <h6 class="card-title">Completed Contracts</h6>
                    <h3 id="completedContracts" class="fw-bold">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card rejected">
                <div class="card-body">
                    <h6 class="card-title">Terminated Contracts</h6>
                    <h3 id="terminatedContracts" class="fw-bold">0</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Contracts List Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            ONGOING CONTRACTS
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 text-center booking-table align-middle">
                    <thead>
                        <tr>
                            <th>Tenant Name</th>
                            <th>Unit</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="contractsTableBody">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Loading contracts...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript Fetch Logic --}}
<script>
document.addEventListener("DOMContentLoaded", async () => {
    const tableBody = document.getElementById('contractsTableBody');
    const activeEl = document.getElementById('activeContracts');
    const completedEl = document.getElementById('completedContracts');
    const terminatedEl = document.getElementById('terminatedContracts');
    const token = sessionStorage.getItem('admin_api_token'); 

    tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Loading...</td></tr>`;

    if (!token) {
        console.error("Authorization token not found.");
        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">⚠ Missing admin token. Please login again.</td></tr>`;
        return;
    }

    try {
        const response = await fetch('/api/contracts', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error(`HTTP error! ${response.status}`);

        const contracts = await response.json();

        // Filter and update summary
        const activeContracts = contracts.filter(c => c.status === 'ongoing');
        const completedContracts = contracts.filter(c => c.status === 'completed');
        const terminatedContracts = contracts.filter(c => c.status === 'Terminated');

        activeEl.textContent = activeContracts.length;
        completedEl.textContent = completedContracts.length;
        terminatedEl.textContent = terminatedContracts.length;

        tableBody.innerHTML = '';

        if (activeContracts.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No ongoing (active) contracts found.</td></tr>`;
            return;
        }

        // Display only Active Contracts
        activeContracts.forEach(c => {
            const tenantName = `${c.tenant?.first_name ?? ''} ${c.tenant?.last_name ?? ''}`.trim();
            const unitName = c.unit?.title ?? 'N/A';
            const start = c.contract_start ?? '—';
            const end = c.contract_end ?? '—';

            const row = `
                <tr>
                    <td>${tenantName || 'N/A'}</td>
                    <td>${unitName}</td>
                    <td>${start}</td>
                    <td>${end}</td>
                    <td><span class="badge bg-success">Active</span></td>
                    <td>
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <a href="/admin/contracts/${c.id}" class="btn btn-sm btn-outline-primary" title="View Contract">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="/admin/contracts/${c.id}/edit" class="btn btn-sm btn-outline-success" title="Edit Contract">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });

    } catch (error) {
        console.error('Error fetching contracts:', error);
        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">⚠ Failed to load contracts: ${error.message}</td></tr>`;
    }
});
</script>
@endsection
