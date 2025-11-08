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
        {{-- ✅ MODIFIED: Card header now includes a search bar --}}
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center flex-wrap gy-2"
             style="background: linear-gradient(90deg, #007BFF, #0A2540);">
            <span>ONGOING CONTRACTS</span>
            <input type="text" id="searchContracts" class="form-control form-control-sm" 
                   style="flex-basis: 300px;" placeholder="Search active contracts...">
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

        {{-- ✅ ADDED: Pagination container --}}
        <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="contracts-pagination-container">
            </div>
    </div>
</div>

{{-- ✅ REPLACED: Entire JavaScript block is updated for pagination --}}
<script>
// --- Global State ---
let allActiveContracts = [];
const ROWS_PER_PAGE = 10;
let currentToken = "";

/**
 * Renders the paginated display for active contracts
 */
function renderContractsDisplay(page = 1) {
    const tableBody = document.getElementById('contractsTableBody');
    const paginationContainer = document.getElementById('contracts-pagination-container');
    const query = document.getElementById('searchContracts').value.toLowerCase();

    // 1. Filter data
    const filteredData = allActiveContracts.filter(c => {
        const tenantName = `${c.tenant?.first_name ?? ''} ${c.tenant?.last_name ?? ''}`.trim();
        const unitName = c.unit?.title ?? 'N/A';
        const start = c.contract_start ?? '—';
        const end = c.contract_end ?? '—';
        const searchableText = [tenantName, unitName, start, end].join(' ').toLowerCase();
        return searchableText.includes(query);
    });

    // 2. Paginate data
    const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
    const startIdx = (page - 1) * ROWS_PER_PAGE;
    const endIdx = startIdx + ROWS_PER_PAGE;
    const pageData = filteredData.slice(startIdx, endIdx);

    // 3. Render table
    tableBody.innerHTML = ''; // Clear old rows
    if (pageData.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No ongoing contracts found.</td></tr>`;
        paginationContainer.innerHTML = ''; // Clear pagination
        return;
    }

    pageData.forEach(c => {
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

    // 4. Render pagination UI
    paginationContainer.innerHTML = buildPaginationUI(totalPages, page, 'renderContractsDisplay');
}

/**
 * Builds the Bootstrap pagination HTML
 */
function buildPaginationUI(totalPages, currentPage, renderFunction) {
    if (totalPages <= 1) return "";
    let html = `<nav><ul class="pagination pagination-sm mb-0">`;
    
    // Previous
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="event.preventDefault(); ${renderFunction}(${currentPage - 1})">&laquo;</a>
    </li>`;

    // Numbers
    for (let i = 1; i <= totalPages; i++) {
        html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" onclick="event.preventDefault(); ${renderFunction}(${i})">${i}</a>
        </li>`;
    }

    // Next
    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="event.preventDefault(); ${renderFunction}(${currentPage + 1})">&raquo;</a>
    </li>`;
    
    html += `</ul></nav>`;
    return html;
}

// --- Main execution ---
document.addEventListener("DOMContentLoaded", async () => {
    const tableBody = document.getElementById('contractsTableBody');
    const activeEl = document.getElementById('activeContracts');
    const completedEl = document.getElementById('completedContracts');
    const terminatedEl = document.getElementById('terminatedContracts');
    
    currentToken = sessionStorage.getItem('admin_api_token'); 

    tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Loading...</td></tr>`;

    if (!currentToken) {
        console.error("Authorization token not found.");
        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">⚠ Missing admin token. Please login again.</td></tr>`;
        return;
    }

    try {
        const response = await fetch('/api/contracts', {
            headers: {
                'Authorization': `Bearer ${currentToken}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error(`HTTP error! ${response.status}`);

        const contracts = await response.json();

        // Filter and update summary (this logic was correct)
        const activeContracts = contracts.filter(c => c.status === 'ongoing');
        const completedContracts = contracts.filter(c => c.status === 'completed');
        const terminatedContracts = contracts.filter(c => c.status === 'Terminated');

        activeEl.textContent = activeContracts.length;
        completedEl.textContent = completedContracts.length;
        terminatedEl.textContent = terminatedContracts.length;

        // --- NEW PAGINATION LOGIC ---
        // 1. Sort latest first (by ID)
        activeContracts.sort((a, b) => b.id - a.id);

        // 2. Store in global state
        allActiveContracts = activeContracts;

        // 3. Initial Render
        renderContractsDisplay(1);

        // 4. Attach search listener
        document.getElementById('searchContracts').addEventListener('keyup', () => {
            renderContractsDisplay(1); // Reset to page 1 on search
        });

    } catch (error) {
        console.error('Error fetching contracts:', error);
        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">⚠ Failed to load contracts: ${error.message}</td></tr>`;
    }
});

// Make render function global for pagination links
window.renderContractsDisplay = renderContractsDisplay;
</script>
@endsection