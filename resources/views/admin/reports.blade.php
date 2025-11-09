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
                        {{-- BAGO: Pinalitan ang Analytics ng Maintenance --}}
                        <li><a class="dropdown-item" href="#" onclick="showReport('maintenance', 'Maintenance Report', this)">MAINTENANCE</a></li>
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
                                <th>Tenant Name</th> 
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>UnitPrice</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="applicationsTable">
                            <tr><td colspan="6" class="text-muted py-3">Loading...</td></tr> 
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
                                <th>Tenant Name</th> 
                                <th>Contract Start</th>
                                <th>Contract End</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="contractsTable">
                            <tr><td colspan="5" class="text-muted py-3">Loading...</td></tr>
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
                                <th>Tenant Name</th> 
                                <th>Payment Status</th>
                                <th>Payment Date</th>
                                <th>Payment Method</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="paymentsTable">
                            <tr><td colspan="6" class="text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- BAGO: Pinalitan ang Analytics ng Maintenance --}}
    <div id="maintenance-report" class="report-section" style="display: none;">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold text-white" style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                Maintenance Requests
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 small text-center">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tenant</th>
                                <th>Unit</th>
                                <th>Issue Type</th>
                                <th>Description</th>
                                <th>Date Submitted</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="maintenanceTable">
                            <tr><td colspan="7" class="text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
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
        
        const reportTitle = document.getElementById('reportTitle').textContent;
        const dataToSend = reportData[activeReportId];
        let headers = [];
        let dataKeys = [];

        switch (activeReportId) {
            case 'bookings':
                headers = ['ID', 'Tenant Name', 'Email', 'Contact', 'Date', 'Time', 'Status'];
                dataKeys = ['id', 'name', 'email', 'contact_num', 'date', 'booking_time', 'status'];
                break;
            case 'applications':
                headers = ['ID', 'Tenant Name', 'Email', 'Contact', 'UnitPrice', 'Status'];
                dataKeys = ['id', 'tenant_name', 'email', 'contact_num', 'unit_price', 'status'];
                break;
            case 'contracts':
                headers = ['Tenant ID', 'Tenant Name', 'Contract Start', 'Contract End', 'Status'];
                dataKeys = ['tenant_id', 'tenant_name', 'contract_start', 'contract_end', 'status'];
                break;
            case 'payments':
                headers = ['Tenant ID', 'Tenant Name', 'Status', 'Payment Date', 'Method', 'Remarks'];
                dataKeys = ['tenant_id', 'tenant_name', 'payment_status', 'payment_date', 'payment_method', 'remarks'];
                break;
            case 'maintenance':
                headers = ['ID', 'Tenant', 'Unit', 'Issue', 'Description', 'Date', 'Status'];
                dataKeys = ['id', 'tenant_name', 'unit_name', 'category', 'description', 'created_at', 'status'];
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
        const endpoints = ['bookings', 'contracts', 'applications', 'payments', 'maintenance'];
        const data = {};

       console.log("--- DEBUG: Simula ng Pag-fetch ng Data ---");
for (const key of endpoints) {
    try {
        const res = await fetch(`/api/${key}`, { headers: { 'Authorization': `Bearer ${token}` } });
        if (!res.ok) { 
            console.error(`Error fetching /api/${key}: ${res.statusText}`);
            data[key] = []; 
        } else {
            let responseData = await res.json();
            data[key] = responseData.data ? responseData.data : responseData;
        }
    } catch (error) {
        console.error(`Failed to fetch /api/${key}:`, error);
        data[key] = []; 
    }
    console.log(`Data for /api/${key}:`, data[key]);
}

console.log("--- DEBUG: Tapos na ang Pag-fetch. ---");
        
        // --- I-FILTER AT I-PROCESS ANG BOOKINGS ---
const filteredBookings = data.bookings
    .filter(item => item.status === 'Active' || item.status === 'Confirmed')
    .map(item => {
        const middleInitial = (item.middle_name && item.middle_name.length > 0) 
                            ? ` ${item.middle_name.charAt(0)}. ` 
                            : ' ';
        item.name = `${item.first_name ?? ''}${middleInitial}${item.last_name ?? ''}`;

        // 🗓 Format Date
        if (item.date) {
    const dateObj = new Date(item.date);
    item.date = dateObj.toLocaleDateString("en-US", { 
        month: 'long', day: 'numeric', year: 'numeric' 
    });
}
if (item.booking_time) {
    const timeObj = new Date(`1970-01-01T${item.booking_time}`);
    item.booking_time = timeObj.toLocaleTimeString("en-US", { 
        hour: 'numeric', minute: '2-digit', hour12: true 
    });
}
        return item;
    });

// --- I-FILTER ANG APPLICATIONS ---
const filteredApplications = data.applications
    .filter(item => item.status === 'Approved')
    .map(item => {
        item.tenant_name = `${item.first_name ?? ''} ${item.last_name ?? ''}`;

        // ✅ Format date
        if (item.created_at) {
    const dateObj = new Date(item.created_at);
    item.date_applied = dateObj.toLocaleDateString("en-US", { 
        month: 'long', 
        day: 'numeric', 
        year: 'numeric' 
    });
    item.time_applied = dateObj.toLocaleTimeString("en-US", { 
        hour: 'numeric', 
        minute: 'numeric', 
        hour12: true 
    });
} else {
    item.date_applied = 'N/A';
    item.time_applied = 'N/A';
}

        return item;
    });

// --- CONTRACTS ---
const filteredContracts = data.contracts
    .filter(item => item.status === 'ongoing')
    .map(item => {
        item.tenant_name = (item.tenant) 
            ? `${item.tenant.first_name ?? ''} ${item.tenant.last_name ?? ''}` 
            : 'N/A'; 
        
        // 🗓 Format contract dates
        if (item.contract_start) {
            item.contract_start = new Date(item.contract_start).toLocaleDateString("en-US", { 
                month: 'long', day: 'numeric', year: 'numeric' 
            });
        }
        if (item.contract_end) {
            item.contract_end = new Date(item.contract_end).toLocaleDateString("en-US", { 
                month: 'long', day: 'numeric', year: 'numeric' 
            });
        }
        return item;
    });

// --- PAYMENTS ---
const filteredPayments = data.payments
    .filter(item => item.payment_status === 'paid')
    .map(item => {
        item.tenant_name = (item.tenant) 
            ? `${item.tenant.first_name ?? ''} ${item.tenant.last_name ?? ''}` 
            : 'N/A';
        
        // 🗓 Format payment date
        if (item.payment_date) {
            item.payment_date = new Date(item.payment_date).toLocaleDateString("en-US", { 
                month: 'long', day: 'numeric', year: 'numeric' 
            });
        } else {
            item.payment_date = 'N/A';
        }
        return item;
    });

// --- MAINTENANCE ---
console.log("🔍 Maintenance raw response:", data.maintenance);

const filteredMaintenance = data.maintenance
    .filter(item => item.status === 'Completed')
    .map(item => {
        item.tenant_name = (item.tenant) 
            ? `${item.tenant.first_name ?? ''} ${item.tenant.last_name ?? ''}` 
            : 'N/A';
        item.unit_name = (item.tenant && item.tenant.unit) 
            ? item.tenant.unit.title 
            : 'N/A';
        
        // 🗓 Format submitted date
        item.created_at = new Date(item.created_at).toLocaleDateString("en-US", { 
            month: 'long', day: 'numeric', year: 'numeric' 
        });
        return item;
    });

console.log("🧩 Maintenance Sample:", filteredMaintenance[0]);
console.log("✅ Filtered & Mapped Maintenance:", filteredMaintenance);


        // I-store ang filtered data sa global variable
        reportData = {
            bookings: filteredBookings,
            applications: filteredApplications,
            contracts: filteredContracts,
            payments: filteredPayments, 
            maintenance: filteredMaintenance 
        };
        
        // Populate Tables
        const renderTable = (id, items, cols) => {
            const tbody = document.getElementById(id);
            if (!tbody) return;
            tbody.innerHTML = items.length
                ? items.map(r => `<tr>${cols.map(c => `<td>${r[c] ?? 'N/A'}</td>`).join('')}</tr>`).join('')
                : `<tr><td colspan="${cols.length}" class="text-muted py-3">No records found.</td></tr>`;
        };

        // I-render ang tables gamit ang TAMANG keys
        renderTable('bookingsTable', filteredBookings, ['id', 'name', 'email', 'contact_num', 'date', 'booking_time', 'status']);
        renderTable('contractsTable', filteredContracts, ['tenant_id', 'tenant_name', 'contract_start', 'contract_end', 'status']);
        
        renderTable('applicationsTable', filteredApplications, ['id', 'tenant_name', 'email', 'contact_num', 'date_applied', 'time_applied', 'unit_price', 'status']);

        renderTable('paymentsTable', filteredPayments, ['tenant_id', 'tenant_name', 'payment_status', 'payment_date', 'payment_method', 'remarks']);
        
        renderTable('maintenanceTable', filteredMaintenance, ['id', 'tenant_name', 'unit_name', 'category', 'description', 'created_at', 'status']);

        // Set the initial view to Bookings
        showReport('bookings', 'Bookings Report', document.querySelector('.dropdown-menu a'));
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