@extends('admin.dashboard')

@section('title', 'Admin - Reports')
@section('page-title', 'Reports Summary')

@section('content')
<div class="container-fluid py-4">

    {{-- 🧾 Page Header with Dropdown --}}
    <div class="row mb-4 no-print">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 id="reportTitle" class="fw-bold text-dark mb-0">Bookings Report</h3>
            
            {{-- ✅ BINAGO: Dinagdag ang CSV button at inayos ang spacing --}}
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="reportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Bookings
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="reportDropdown">
                        <li><a class="dropdown-item" href="#" onclick="showReport('bookings', 'Bookings Report', this)">Confirmed Bookings</a></li>
                        <li><a class="dropdown-item" href="#" onclick="showReport('applications', 'Application Report', this)">Approved Applications</a></li>
                        <li><a class="dropdown-item" href="#" onclick="showReport('contracts', 'Contracts Report', this)">Ongoing Contracts</a></li>
                        <li><a class="dropdown-item" href="#" onclick="showReport('downpayments', 'Downpayments Report', this)">Downpayments</a></li>
                        {{-- ✅ BAGO: Rent Payments --}}
                        <li><a class="dropdown-item" href="#" onclick="showReport('rent_payments', 'Rent Payments Report', this)">Rent Payments</a></li>
                        <li><a class="dropdown-item" href="#" onclick="showReport('maintenance', 'Maintenance Report', this)">Completed Maintenance</a></li>
                    </ul>
                </div>
                
                {{-- ✅ BAGO: Generate CSV Button --}}
                <button class="btn btn-success" onclick="generateCSV()" id="generateCsvButton">
                    <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                </button>

                <button class="btn btn-primary" onclick="generatePDF()" id="generatePdfButton">
                    <i class="bi bi-printer"></i> PDF
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
            {{-- ✅ BAGO: Pagination Container --}}
            <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="bookings-pagination"></div>
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
            {{-- ✅ BAGO: Pagination Container --}}
            <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="applications-pagination"></div>
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
            {{-- ✅ BAGO: Pagination Container --}}
            <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="contracts-pagination"></div>
        </div>
    </div>

    {{-- ✅ BINAGO: Ito na ngayon ay Downpayments Report --}}
    <div id="downpayments-report" class="report-section" style="display: none;">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold text-white" style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                Downpayments
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 small text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Tenant Name</th> 
                                <th>Payment Status</th>
                                <th>Payment Date</th>
                                <th>Payment Method</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="downpaymentsTable">
                            <tr><td colspan="6" class="text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- ✅ BAGO: Pagination Container --}}
            <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="downpayments-pagination"></div>
        </div>
    </div>
    
    {{-- ✅ BAGO: Rent Payments Report --}}
    <div id="rent_payments-report" class="report-section" style="display: none;">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold text-white" style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                Rent Payments
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 small text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Tenant Name</th> 
                                <th>Payment Status</th>
                                <th>Payment Date</th>
                                <th>Payment Method</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="rentPaymentsTable">
                            <tr><td colspan="6" class="text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- ✅ BAGO: Pagination Container --}}
            <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="rent_payments-pagination"></div>
        </div>
    </div>


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
            {{-- ✅ BAGO: Pagination Container --}}
            <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="maintenance-pagination"></div>
        </div>
    </div>

</div>
@endsection
@push('scripts')
{{-- 🧠 JavaScript Logic --}}
<script>
    // --- Global State ---
    let reportData = {};
    let activeReportId = 'bookings';
    const ROWS_PER_PAGE = 10;

    // ========================================================== //
    // ============ HELPER FUNCTIONS (Date/Time/Pagination) ===== //
    // ========================================================== //

    /**
     * Formats date to "Month Day, Year"
     */
    function formatAppDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            const localDate = new Date(date.getTime() + date.getTimezoneOffset() * 60000);
            return localDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        } catch (e) { return dateString; }
    }

    /**
     * Formats 24-hr time to 12-hr time with AM/PM
     */
    function formatAppTime(timeString) {
        if (!timeString) return 'N/A';
        try {
            const [hours, minutes] = timeString.split(':');
            const date = new Date();
            date.setHours(hours);
            date.setMinutes(minutes);
            return date.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        } catch (e) { return timeString; }
    }

    /**
     * Builds the Bootstrap pagination HTML string.
     */
    function buildPaginationUI(totalPages, currentPage, renderFunctionName) {
        if (totalPages <= 1) return "";
        let html = `<nav><ul class="pagination pagination-sm mb-0">`;
        
        // Previous button
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="event.preventDefault(); ${renderFunctionName}(${currentPage - 1})">&laquo;</a>
                 </li>`;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                         <a class="page-link" href="#" onclick="event.preventDefault(); ${renderFunctionName}(${i})">${i}</a>
                     </li>`;
        }

        // Next button
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="event.preventDefault(); ${renderFunctionName}(${currentPage + 1})">&raquo;</a>
                 </li>`;
        
        html += `</ul></nav>`;
        return html;
    }

    // ========================================================== //
    // ============ CORE FUNCTIONS (Show, PDF, CSV) ============= //
    // ========================================================== //

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

        // ✅ BINAGO: Tawagin ang specific render function para sa page 1
        switch (reportId) {
            case 'bookings':      renderBookingsReport(1); break;
            case 'applications':  renderApplicationsReport(1); break;
            case 'contracts':     renderContractsReport(1); break;
            case 'downpayments':  renderDownpaymentsReport(1); break;
            case 'rent_payments': renderRentPaymentsReport(1); break;
            case 'maintenance':   renderMaintenanceReport(1); break;
        }
    }

    /**
     * Helper function to get headers and keys for exports
     */
    function getExportConfig(reportId) {
        let headers = [];
        let dataKeys = [];

        switch (reportId) {
            case 'bookings':
                headers = ['Tenant Name', 'Email', 'Contact', 'Date', 'Time', 'Status'];
                dataKeys = ['name', 'email', 'contact_num', 'date', 'booking_time', 'status'];
                break;
            case 'applications':
                headers = ['Tenant Name', 'Email', 'Contact', 'UnitPrice', 'Status'];
                dataKeys = ['tenant_name', 'email', 'contact_num', 'unit_price', 'status'];
                break;
            case 'contracts':
                headers = ['Tenant Name', 'Contract Start', 'Contract End', 'Status'];
                dataKeys = ['tenant_name', 'contract_start', 'contract_end', 'status'];
                break;
            case 'downpayments': // ✅ BINAGO
                headers = ['Tenant Name', 'Status', 'Payment Date', 'Method', 'Remarks'];
                dataKeys = ['tenant_name', 'payment_status', 'payment_date', 'payment_method', 'remarks'];
                break;
            case 'rent_payments': // ✅ BAGO
                headers = ['Tenant Name', 'Status', 'Payment Date', 'Method', 'Remarks'];
                dataKeys = ['tenant_name', 'payment_status', 'payment_date', 'payment_method', 'remarks'];
                break;
            case 'maintenance':
                headers = ['Tenant', 'Unit', 'Issue', 'Description', 'Date', 'Status'];
                dataKeys = ['tenant_name', 'unit_name', 'category', 'description', 'created_at', 'status'];
                break;
        }
        return { headers, dataKeys };
    }

    // Function para i-generate ang PDF
    async function generatePDF() {
        const reportTitle = document.getElementById('reportTitle').textContent;
        const dataToSend = reportData[activeReportId];
        const { headers, dataKeys } = getExportConfig(activeReportId);

        if (!dataKeys.length) {
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

            if (!response.ok) throw new Error('Server error: Could not generate PDF.');

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

    // ✅ BAGO: Generate CSV Function
    function generateCSV() {
        const { headers, dataKeys } = getExportConfig(activeReportId);
        const data = reportData[activeReportId];

        if (!dataKeys.length) {
            console.error('Unknown report type for CSV generation');
            return;
        }

        let csvContent = "data:text/csv;charset=utf-8,";
        
        // Add headers
        csvContent += headers.join(",") + "\r\n";

        // Add rows
        data.forEach(row => {
            const rowData = dataKeys.map(key => {
                let cellData = row[key] ?? 'N/A';
                // Linisin ang data para sa CSV (tanggalin ang commas)
                return `"${String(cellData).replace(/"/g, '""')}"`;
            });
            csvContent += rowData.join(",") + "\r\n";
        });

        // Trigger download
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `${activeReportId}_report.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
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
    

    // ========================================================== //
    // ============ BAGONG RENDER FUNCTIONS (PAGINATION) ======== //
    // ========================================================== //
    // Bawat isa nito ay gumagawa ng table rows para sa 1 page

    function renderBookingsReport(page = 1) {
        const tbody = document.getElementById('bookingsTable');
        const pagination = document.getElementById('bookings-pagination');
        const data = reportData.bookings || [];
        
        const totalPages = Math.ceil(data.length / ROWS_PER_PAGE);
        const start = (page - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = data.slice(start, end);

        tbody.innerHTML = pageData.length
            ? pageData.map(r => `<tr>
                <td>${r.name ?? 'N/A'}</td>
                <td>${r.email ?? 'N/A'}</td>
                <td>${r.contact_num ?? 'N/A'}</td>
                <td>${r.date ?? 'N/A'}</td>
                <td>${r.booking_time ?? 'N/A'}</td>
                <td>${r.status ?? 'N/A'}</td>
            </tr>`).join('')
            : `<tr><td colspan="6" class="text-muted py-3">No records found.</td></tr>`;
        
        pagination.innerHTML = buildPaginationUI(totalPages, page, 'renderBookingsReport');
    }

    function renderApplicationsReport(page = 1) {
        const tbody = document.getElementById('applicationsTable');
        const pagination = document.getElementById('applications-pagination');
        const data = reportData.applications || [];
        
        const totalPages = Math.ceil(data.length / ROWS_PER_PAGE);
        const start = (page - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = data.slice(start, end);

        tbody.innerHTML = pageData.length
            ? pageData.map(r => `<tr>
                <td>${r.tenant_name ?? 'N/A'}</td>
                <td>${r.email ?? 'N/A'}</td>
                <td>${r.contact_num ?? 'N/A'}</td>
                <td>${r.unit_price ? '₱' + parseFloat(r.unit_price).toLocaleString('en-US') : 'N/A'}</td>
                <td>${r.status ?? 'N/A'}</td>
            </tr>`).join('')
            : `<tr><td colspan="5" class="text-muted py-3">No records found.</td></tr>`;
        
        pagination.innerHTML = buildPaginationUI(totalPages, page, 'renderApplicationsReport');
    }

    function renderContractsReport(page = 1) {
        const tbody = document.getElementById('contractsTable');
        const pagination = document.getElementById('contracts-pagination');
        const data = reportData.contracts || [];
        
        const totalPages = Math.ceil(data.length / ROWS_PER_PAGE);
        const start = (page - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = data.slice(start, end);

        tbody.innerHTML = pageData.length
            ? pageData.map(r => `<tr>
                <td>${r.tenant_name ?? 'N/A'}</td>
                <td>${r.contract_start ?? 'N/A'}</td>
                <td>${r.contract_end ?? 'N/A'}</td>
                <td>${r.status ?? 'N/A'}</td>
            </tr>`).join('')
            : `<tr><td colspan="4" class="text-muted py-3">No records found.</td></tr>`;
        
        pagination.innerHTML = buildPaginationUI(totalPages, page, 'renderContractsReport');
    }

    function renderDownpaymentsReport(page = 1) {
        const tbody = document.getElementById('downpaymentsTable');
        const pagination = document.getElementById('downpayments-pagination');
        const data = reportData.downpayments || [];
        
        const totalPages = Math.ceil(data.length / ROWS_PER_PAGE);
        const start = (page - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = data.slice(start, end);

        tbody.innerHTML = pageData.length
            ? pageData.map(r => `<tr>
                <td>${r.tenant_name ?? 'N/A'}</td>
                <td>${r.payment_status ?? 'N/A'}</td>
                <td>${r.payment_date ?? 'N/A'}</td>
                <td>${r.payment_method ?? 'N/A'}</td>
                <td>${r.remarks ?? 'N/A'}</td>
            </tr>`).join('')
            : `<tr><td colspan="5" class="text-muted py-3">No records found.</td></tr>`;
        
        pagination.innerHTML = buildPaginationUI(totalPages, page, 'renderDownpaymentsReport');
    }

    function renderRentPaymentsReport(page = 1) {
        const tbody = document.getElementById('rentPaymentsTable');
        const pagination = document.getElementById('rent_payments-pagination');
        const data = reportData.rent_payments || [];
        
        const totalPages = Math.ceil(data.length / ROWS_PER_PAGE);
        const start = (page - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = data.slice(start, end);

        tbody.innerHTML = pageData.length
            ? pageData.map(r => `<tr>
                <td>${r.tenant_name ?? 'N/A'}</td>
                <td>${r.payment_status ?? 'N/A'}</td>
                <td>${r.payment_date ?? 'N/A'}</td>
                <td>${r.payment_method ?? 'N/A'}</td>
                <td>${r.remarks ?? 'N/A'}</td>
            </tr>`).join('')
            : `<tr><td colspan="5" class="text-muted py-3">No records found.</td></tr>`;
        
        pagination.innerHTML = buildPaginationUI(totalPages, page, 'renderRentPaymentsReport');
    }

    function renderMaintenanceReport(page = 1) {
        const tbody = document.getElementById('maintenanceTable');
        const pagination = document.getElementById('maintenance-pagination');
        const data = reportData.maintenance || [];
        
        const totalPages = Math.ceil(data.length / ROWS_PER_PAGE);
        const start = (page - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = data.slice(start, end);

        tbody.innerHTML = pageData.length
            ? pageData.map(r => `<tr>
                <td>${r.tenant_name ?? 'N/A'}</td>
                <td>${r.unit_name ?? 'N/A'}</td>
                <td>${r.category ?? 'N/A'}</td>
                <td>${r.description ?? 'N/A'}</td>
                <td>${r.created_at ?? 'N/A'}</td>
                <td>${r.status ?? 'N/A'}</td>
            </tr>`).join('')
            : `<tr><td colspan="6" class="text-muted py-3">No records found.</td></tr>`;
        
        pagination.innerHTML = buildPaginationUI(totalPages, page, 'renderMaintenanceReport');
    }

    // --- Ilagay sa window para magamit ng pagination links ---
    window.renderBookingsReport = renderBookingsReport;
    window.renderApplicationsReport = renderApplicationsReport;
    window.renderContractsReport = renderContractsReport;
    window.renderDownpaymentsReport = renderDownpaymentsReport;
    window.renderRentPaymentsReport = renderRentPaymentsReport;
    window.renderMaintenanceReport = renderMaintenanceReport;

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