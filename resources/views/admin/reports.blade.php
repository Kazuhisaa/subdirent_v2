@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- 🧾 Page Header --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold text-primary mb-0">Reports Summary</h3>
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> Print Report
            </button>
        </div>
    </div>

    {{-- 📊 Overview Row --}}
    <div class="row text-center mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm small-report">
                <div class="card-body">
                    <h6 class="text-muted">Total Bookings</h6>
                    <h3 id="bookingsCount" class="fw-bold text-primary">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm small-report">
                <div class="card-body">
                    <h6 class="text-muted">Active Contracts</h6>
                    <h3 id="contractsCount" class="fw-bold text-success">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm small-report">
                <div class="card-body">
                    <h6 class="text-muted">Applications</h6>
                    <h3 id="applicationsCount" class="fw-bold text-info">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm small-report">
                <div class="card-body">
                    <h6 class="text-muted">Payments</h6>
                    <h3 id="paymentsCount" class="fw-bold text-warning">0</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- 📋 Reports Tables Section --}}
    <div class="row">
        {{-- Bookings Table --}}
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-bold bg-primary text-white">Bookings</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 small text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTable">
                                <tr><td colspan="3" class="text-muted py-3">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contracts Table --}}
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-bold bg-success text-white">Contracts</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 small text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tenant</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="contractsTable">
                                <tr><td colspan="3" class="text-muted py-3">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Applications Table --}}
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-bold bg-info text-white">Applications</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 small text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Applicant</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="applicationsTable">
                                <tr><td colspan="3" class="text-muted py-3">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payments Table --}}
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-bold bg-warning text-white">Payments</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 small text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tenant</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="paymentsTable">
                                <tr><td colspan="3" class="text-muted py-3">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Analytics --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-bold bg-dark text-white">Analytics</div>
                <div class="card-body">
                    <p class="text-muted small mb-2">Performance Summary:</p>
                    <ul class="list-unstyled small" id="analyticsList">
                        <li>Average Monthly Payment: ₱0.00</li>
                        <li>Occupancy Rate: 0%</li>
                        <li>Pending Applications: 0</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- 🧠 JS Example (Mock Data) --}}
<script>
document.addEventListener("DOMContentLoaded", async () => {
    const token = sessionStorage.getItem('admin_api_token');
    const endpoints = ['bookings', 'contracts', 'applications', 'payments'];
    const data = {};

    for (const key of endpoints) {
        try {
            const res = await fetch(`/api/${key}`, { headers: { 'Authorization': `Bearer ${token}` } });
            data[key] = await res.json();
        } catch {
            data[key] = [];
        }
    }

    // Update Counts
    document.getElementById('bookingsCount').textContent = data.bookings.length;
    document.getElementById('contractsCount').textContent = data.contracts.length;
    document.getElementById('applicationsCount').textContent = data.applications.length;
    document.getElementById('paymentsCount').textContent = data.payments.length;

    // Populate Tables (limit to 5 rows for cleanliness)
    const renderTable = (id, items, cols) => {
        const tbody = document.getElementById(id);
        tbody.innerHTML = items.length
            ? items.slice(0, 5).map(r => `<tr>${cols.map(c => `<td>${r[c] ?? 'N/A'}</td>`).join('')}</tr>`).join('')
            : `<tr><td colspan="${cols.length}" class="text-muted py-3">No records found.</td></tr>`;
    };
    renderTable('bookingsTable', data.bookings, ['id', '', 'status']);
    renderTable('contractsTable', data.contracts, ['id', 'tenant_name', 'status']);
    renderTable('applicationsTable', data.applications, ['id', 'applicant', 'status']);
    renderTable('paymentsTable', data.payments, ['id', 'tenant_name', 'amount']);
});
</script>

{{-- 🖨 Print-Friendly Styling --}}
<style>
@media print {
    body * { visibility: hidden; }
    .container-fluid, .container-fluid * { visibility: visible; }
    .container-fluid { position: absolute; left: 0; top: 0; width: 100%; }
    button, a.btn { display: none !important; }
}
.small-report { border-left: 4px solid #0A2540; }
.table td, .table th { vertical-align: middle; }
</style>
@endsection
