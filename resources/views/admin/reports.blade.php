@extends('admin.dashboard')

@section('title', 'Admin - Reports')
@section('page-title', 'Reports Summary')

@section('content')
<div class="container-fluid py-4">

    {{-- 🧾 Page Header with Dropdown --}}
    <div class="row mb-4 no-print">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 id="reportTitle" class="fw-bold text-dark mb-0">Bookings Report</h3>
            <div class="d-flex">
                <div class="dropdown me-2">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="reportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Bookings
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="reportDropdown">
                        <li><a class="dropdown-item" href="#" onclick="showReport('bookings', 'Bookings Report', this)">BOOKINGS</a></li>
                        <li><a class="dropdown-item" href="#" onclick="showReport('applications', 'Application Report', this)">APPLICATION</a></li>
                        <li><a class="dropdown-item" href="#" onclick="showReport('contracts', 'Contracts Report', this)">CONTRACTS</a></li>
                        <li><a class="dropdown-item" href="#" onclick="showReport('payments', 'Payments Report', this)">PAYMENTS</a></li>
                        <li><a class="dropdown-item" href="#" onclick="showReport('analytics', 'Analytics Summary', this)">ANALYTICS</a></li>
                    </ul>
                </div>
                {{-- ITO YUNG TAMANG BUTTON: --}}
                <button class="btn btn-primary" onclick="generatePDF()" id="generatePdfButton">
                    <i class="bi bi-printer"></i> Generate Report
                </button>
            </div>
        </div>
    </div>

    {{-- 📋 Reports Content (HTML tables) --}}
    
    <div id="bookings-report" class="report-section">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold text-white" style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                Bookings
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 small text-center">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tenant Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsTable">
                            <tr><td colspan="7" class="text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="applications-report" class="report-section" style="display: none;">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold text-white" style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                Applications
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 small text-center">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>UnitPrice</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="applicationsTable">
                            <tr><td colspan="7" class="text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="contracts-report" class="report-section" style="display: none;">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold text-white" style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                Contracts
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 small text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Tenant ID</th>
                                <th>Contract Start</th>
                                <th>Contract End</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="contractsTable">
                            <tr><td colspan="4" class="text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="payments-report" class="report-section" style="display: none;">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold text-white" style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                Payments
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 small text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Tenant ID</th>
                                <th>Payment Status</th>
                                <th>Payment Date</th>
                                <th>Payment Method</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="paymentsTable">
                            <tr><td colspan="5" class="text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="analytics-report" class="report-section" style="display: none;">
        <div class="row text-center mb-4">
            <div class="col-md-3 mb-3"><div class="card border-0 shadow-sm small-report"><div class="card-body"><h6 class="text-muted">Total Bookings</h6><h3 id="bookingsCount" class="fw-bold text-primary">0</h3></div></div></div>
            <div class="col-md-3 mb-3"><div class="card border-0 shadow-sm small-report"><div class="card-body"><h6 class="text-muted">Active Contracts</h6><h3 id="contractsCount" class="fw-bold text-success">0</h3></div></div></div>
            <div class="col-md-3 mb-3"><div class="card border-0 shadow-sm small-report"><div class="card-body"><h6 class="text-muted">Applications</h6><h3 id="applicationsCount" class="fw-bold text-info">0</h3></div></div></div>
            <div class="col-md-3 mb-3"><div class="card border-0 shadow-sm small-report"><div class="card-body"><h6 class="text-muted">Payments</h6><h3 id="paymentsCount" class="fw-bold text-warning">0</h3></div></div></div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold text-white" style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                Analytics Details
            </div>
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
@endsection


@push('scripts')
{{-- 🧠 JavaScript Logic --}}
<script>
    // Global variables
    let reportData = {};
    let activeReportId = 'bookings';

    // Function to switch between reports
    function showReport(reportId, title, element) {
        document.querySelectorAll('.report-section').forEach(section => {
            section.style.display = 'none';
            section.classList.remove('active');
        });
        const activeSection = document.getElementById(reportId + '-report');
        if (activeSection) {
            activeSection.style.display = 'block';
            activeSection.classList.add('active');
        }
        document.getElementById('reportTitle').textContent = title;
        document.getElementById('reportDropdown').textContent = element.textContent;
        activeReportId = reportId;
    }

    // Function para i-generate ang PDF
    async function generatePDF() {
        if (activeReportId === 'analytics') {
            alert('PDF generation is not available for Analytics Summary.');
            return;
        }

        const reportTitle = document.getElementById('reportTitle').textContent;
        const dataToSend = reportData[activeReportId];
        let headers = [];
        let dataKeys = [];

        switch (activeReportId) {
            case 'bookings':
                // BAGO: Pinalitan ang 'Tenant Name' -> 'name' para tumugma sa data
                headers = ['ID', 'Tenant Name', 'Email', 'Contact', 'Date', 'Time', 'Status'];
                dataKeys = ['id', 'name', 'email', 'contact_num', 'date', 'booking_time', 'status'];
                break;
            case 'applications':
                headers = ['ID', 'First Name', 'Last Name', 'Email', 'Contact', 'UnitPrice', 'Status'];
                dataKeys = ['id', 'first_name', 'last_name', 'email', 'contact_num', 'unit_price', 'status'];
                break;
            case 'contracts':
                headers = ['Tenant ID', 'Contract Start', 'Contract End', 'Status'];
                dataKeys = ['tenant_id', 'contract_start', 'contract_end', 'status'];
                break;
            case 'payments':
                headers = ['Tenant ID', 'Status', 'Payment Date', 'Method', 'Remarks'];
                dataKeys = ['tenant_id', 'status', 'payment_date', 'payment_method', 'remarks'];
                break;
            default:
                console.error('Unknown report type for PDF generation');
                return;
        }

        // I-format ang data
        const formattedData = dataToSend.map(row => {
            return dataKeys.map(key => row[key] ?? 'N/A');
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const pdfButton = document.getElementById('generatePdfButton');

        pdfButton.disabled = true;
        pdfButton.innerHTML = '<i class="bi bi-arrow-down-circle"></i> Generating...';

        try {
            const response = await fetch('/generate-report-pdf', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/pdf'
                },
                body: JSON.stringify({
                    title: reportTitle,
                    headers: headers,
                    data: formattedData
                })
            });

            if (!response.ok) {
                throw new Error('Server error: Could not generate PDF.');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `${activeReportId}_report.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

        } catch (error) {
            console.error('Error during PDF generation:', error);
            alert('Could not generate PDF. Please try again or check the console.');
        } finally {
            pdfButton.disabled = false;
            pdfButton.innerHTML = '<i class="bi bi-printer"></i> Generate Report';
        }
    }


    // --- DOMContentLoaded script ---
    document.addEventListener("DOMContentLoaded", async () => {
        const token = document.querySelector('meta[name="admin-api-token"]').getAttribute('content');
        const endpoints = ['bookings', 'contracts', 'applications', 'payments'];
        const data = {};

        for (const key of endpoints) {
            try {
                const res = await fetch(`/api/${key}`, { headers: { 'Authorization': `Bearer ${token}` } });
                data[key] = await res.json();
            } catch (error) {
                console.error(`Failed to fetch /api/${key}:`, error);
                data[key] = []; 
            }
        }
        
        // --- BAGO: I-FILTER AT I-PROCESS ANG BOOKINGS ---
        const filteredBookings = data.bookings
            .filter(item => 
                item.status === 'Active' || item.status === 'Confirmed'
            )
            .map(item => {
                // I-concatenate ang pangalan base sa image mo
                // Gagawa ito ng "First M. Last"
                const middleInitial = (item.middle_name && item.middle_name.length > 0) 
                                    ? ` ${item.middle_name.charAt(0)}. ` 
                                    : ' ';
                
                // I-o-overwrite natin ang 'name' property na gagamitin ng renderTable
                item.name = `${item.first_name ?? ''}${middleInitial}${item.last_name ?? ''}`;
                
                return item;
            });
        // --- WAKAS NG PAG-PROCESS NG BOOKINGS ---
        
        const filteredApplications = data.applications.filter(item => 
            item.status === 'Approved'
        );
        const filteredContracts = data.contracts.filter(item => 
            item.status === 'ongoing' 
        );
        const filteredPayments = data.payments.filter(item => 
            item.status === 'paid'
        );

        // I-store ang filtered data sa global variable
        reportData = {
            bookings: filteredBookings,
            applications: filteredApplications,
            contracts: filteredContracts,
            payments: filteredPayments
        };

        // Update Counts for Analytics
        document.getElementById('bookingsCount').textContent = filteredBookings.length;
        document.getElementById('contractsCount').textContent = filteredContracts.length;
        document.getElementById('applicationsCount').textContent = filteredApplications.length;
        document.getElementById('paymentsCount').textContent = filteredPayments.length;

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
        .no-print { display: none !important; }
        .report-section.active, .report-section.active * { visibility: visible; }
        .report-section.active { position: absolute; left: 0; top: 20px; width: 100%; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
        .card-header {
            background: linear-gradient(90deg, #007BFF, #0A2540) !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .table-light { background-color: #f8f9fa !important; }
    }
    .small-report { border-left: 4px solid #0A2540; }
    .table td, .table th { vertical-align: middle; }
</style>
@endpush