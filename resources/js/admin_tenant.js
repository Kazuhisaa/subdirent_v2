// admin_tenant.js
// Uses SweetAlert2 for all alerts

// --- Global state for client-side pagination, data, and view ---
let allTenants = [];
let allArchivedTenants = [];
let currentView = 'active'; // 'active' or 'archived'
const ROWS_PER_PAGE = 10;
let currentToken = ""; // Store token for paginated event listeners

async function ensureSanctumSession() {
    try {
        await fetch("/sanctum/csrf-cookie", { method: "GET", credentials: "include" });
    } catch (e) {
        console.warn("Failed to request sanctum csrf cookie:", e);
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    await ensureSanctumSession();

    const tokenEl = document.querySelector('meta[name="admin-api-token"]');
    const token = tokenEl ? tokenEl.getAttribute("content") : null;
    console.log("admin token present?", !!token);

    if (!token) {
        showError("Admin API token not found. App cannot function.");
        return;
    }

    currentToken = token; // Store token globally

    // --- Set initial loading message ---
    const initialBody = document.querySelector("#main-table-body");
    if (initialBody) {
        initialBody.innerHTML = `<tr><td colspan="5" class="py-3 text-muted">Loading data...</td></tr>`;
    }

    // --- Attach View Toggle Listeners ---
    const btnActive = document.querySelector("#btn-view-active");
    const btnArchived = document.querySelector("#btn-view-archived");

    if (btnActive && btnArchived) {
        btnActive.addEventListener("click", () => {
            if (currentView === 'active') return; // Already active
            currentView = 'active';
            btnActive.classList.add("active", "btn-action");
            btnActive.classList.remove("btn-outline-blue");
            btnArchived.classList.remove("active", "btn-action");
            btnArchived.classList.add("btn-outline-blue");
            renderDisplay(1);
        });
        btnArchived.addEventListener("click", () => {
            if (currentView === 'archived') return; // Already active
            currentView = 'archived';
            btnArchived.classList.add("active", "btn-action");
            btnArchived.classList.remove("btn-outline-blue");
            btnActive.classList.remove("active", "btn-action");
            btnActive.classList.add("btn-outline-blue");
            renderDisplay(1);
        });
    }

    // --- Attach Generic Search Listener ---
    safeAttach("#table-search", "input", () => renderDisplay(1));

    // --- Attach Edit Form Listener ---
    const editForm = document.querySelector("#editTenantForm");
    if (editForm) {
        editForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            await handleEditSubmit(token);
        });
    } else {
        console.warn("#editTenantForm not found");
    }

    // --- Load Data and Initial Render ---
    await loadTenants(token);
    await loadArchivedTenants(token);
    renderDisplay(1); // Initial render
});

/* ---------- Helpers ---------- */

function safeAttach(selector, evt, handler) {
    const el = document.querySelector(selector);
    if (el) el.addEventListener(evt, handler);
    else console.warn(`Selector ${selector} not found for safeAttach.`);
}

function apiHeaders(token) {
    return {
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
    };
}

const TENANTS_BASE = "/api/admin/api/tenants";

/* ---------- SweetAlert Helpers ---------- */
// ... (Your SweetAlert functions: showError, showSuccess, confirmAction)
/* ---------- Loaders ---------- */

async function loadTenants(token) {
    const body = document.querySelector("#main-table-body");
    if (!body) return console.warn("#main-table-body not found");
    // Loading message is now set in DOMContentLoaded

    try {
        const res = await fetch(TENANTS_BASE, { headers: apiHeaders(token) });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();

        if (!Array.isArray(data)) {
            throw new Error("Invalid data format from API");
        }

        data.sort((a, b) => b.id - a.id);
        allTenants = data; // Store all tenants

    } catch (err) {
        console.error("loadTenants error:", err);
        body.innerHTML = `<tr><td colspan="5" class="text-danger py-3">Error loading tenants.</td></tr>`;
        allTenants = [];
    }
}

async function loadArchivedTenants(token) {
    const body = document.querySelector("#main-table-body");
    if (!body) return console.warn("#main-table-body not found");
    // Loading message is now set in DOMContentLoaded

    try {
        const res = await fetch(`${TENANTS_BASE}/archived`, { headers: apiHeaders(token) });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();

        if (!Array.isArray(data)) {
            throw new Error("Invalid data format from API");
        }

        data.sort((a, b) => b.id - a.id);
        allArchivedTenants = data; // Store all archived tenants

    } catch (err) {
        console.error("loadArchivedTenants error:", err);
        // Don't overwrite the tenant error message if it already exists
        if (allTenants.length === 0) {
             body.innerHTML = `<tr><td colspan="5" class="text-danger py-3">Error loading archived tenants.</td></tr>`;
        }
        allArchivedTenants = [];
    }
}

/* ---------- Render Functions (NEW UNIFIED) ---------- */

/**
 * Filters, paginates, and renders the appropriate tenant list (active or archived)
 * based on the global 'currentView' state.
 */
function renderDisplay(page = 1) {
    const titleEl = document.querySelector("#table-title");
    const searchEl = document.querySelector("#table-search");
    const headEl = document.querySelector("#main-table-head");
    const bodyEl = document.querySelector("#main-table-body");
    const paginationEl = document.querySelector("#main-pagination-container");

    if (!titleEl || !searchEl || !headEl || !bodyEl || !paginationEl) {
        console.error("One or more critical table elements are missing.");
        return;
    }

    // 1. Define sources based on currentView
    let sourceData, tableHeadHTML, rowBuilderFn, listenerFn, emptyText;

    const sharedTableHead = `
        <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Unit</th>
            <th>Actions</th>
        </tr>`;

    if (currentView === 'active') {
        titleEl.textContent = 'ACTIVE TENANTS';
        searchEl.placeholder = 'Search active tenants...';
        sourceData = allTenants;
        emptyText = 'No active tenants found.';
        tableHeadHTML = sharedTableHead;

        rowBuilderFn = (t) => {
            const name = [t.first_name, t.middle_name, t.last_name].filter(Boolean).join(" ");
            const unitTitle = (t.unit && (t.unit.title || t.unit.unit_name)) ? (t.unit.title || t.unit.unit_name) : "N/A";
            return `
                <tr data-id="${t.id}">
                    <td>${escapeHtml(name)}</td>
                    <td>${escapeHtml(t.email || "")}</td>
                    <td>${escapeHtml(t.contact_num || "")}</td>
                    <td>${escapeHtml(unitTitle)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-blue edit-btn" title="Edit"><i class="bi bi-pencil-square"></i></button>
                        <button class="btn btn-sm btn-outline-warning archive-btn" title="Archive"><i class="bi bi-archive"></i></button>
                    </td>
                </tr>`;
        };

        listenerFn = attachEditArchiveListeners;

    } else { // 'archived'
        titleEl.textContent = 'ARCHIVED TENANTS';
        searchEl.placeholder = 'Search archived tenants...';
        sourceData = allArchivedTenants;
        emptyText = 'No archived tenants found.';
        tableHeadHTML = sharedTableHead;

        rowBuilderFn = (t) => {
            const name = [t.first_name, t.middle_name, t.last_name].filter(Boolean).join(" ");
            const unitTitle = (t.unit && (t.unit.title || t.unit.unit_name)) ? (t.unit.title || t.unit.unit_name) : "N/A";
            return `
                <tr data-id="${t.id}">
                    <td>${escapeHtml(name)}</td>
                    <td>${escapeHtml(t.email || "")}</td>
                    <td>${escapeHtml(t.contact_num || "")}</td>
                    <td>${escapeHtml(unitTitle)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-success restore-btn" data-id="${t.id}">
                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                        </button>
                    </td>
                </tr>`;
        };

        listenerFn = attachRestoreListeners;
    }

    // 2. Filter Data
    const query = searchEl.value.trim().toLowerCase();
    const filteredData = sourceData.filter(t => {
        const name = [t.first_name, t.middle_name, t.last_name].filter(Boolean).join(" ");
        const unitTitle = (t.unit && (t.unit.title || t.unit.unit_name)) ? (t.unit.title || t.unit.unit_name) : "";
        const searchableText = [name, t.email, t.contact_num, unitTitle].join(" ").toLowerCase();
        return searchableText.includes(query);
    });

    // 3. Paginate Data
    const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
    const start = (page - 1) * ROWS_PER_PAGE;
    const end = start + ROWS_PER_PAGE;
    const pageData = filteredData.slice(start, end);

    // 4. Render Table Head
    headEl.innerHTML = tableHeadHTML;

    // 5. Render Table Rows
    bodyEl.innerHTML = ""; // Clear previous rows
    if (pageData.length === 0) {
        bodyEl.innerHTML = `<tr><td colspan="5" class="text-muted py-3">${emptyText}</td></tr>`;
    } else {
        pageData.forEach((t) => {
            bodyEl.insertAdjacentHTML("beforeend", rowBuilderFn(t));
        });
    }

    // 6. Render Pagination
    paginationEl.innerHTML = buildPaginationUI(totalPages, page);

    // 7. Re-attach listeners for new buttons
    listenerFn(currentToken);

    // 8. Attach listeners for pagination links
    paginationEl.querySelectorAll(".page-link").forEach(link => {
        link.addEventListener("click", e => {
            e.preventDefault();
            const newPage = parseInt(e.target.dataset.page, 10);
            if (newPage) {
                renderDisplay(newPage); // Call the main render function
            }
        });
    });
}


/**
 * Builds Bootstrap pagination HTML string. (Unchanged)
 */
function buildPaginationUI(totalPages, currentPage) {
    if (totalPages <= 1) return "";

    let html = `<nav aria-label="Pagination"><ul class="pagination pagination-sm mb-0">`;

    // Previous Button
    html += `
    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
        </a>
    </li>`;

    // Page Numbers
    const pagesToShow = [];
    pagesToShow.push(1); // Always show first page

    let start = Math.max(2, currentPage - 2);
    let end = Math.min(totalPages - 1, currentPage + 2);

    if (currentPage - 2 > 2) { // Add ellipsis if gap after first page
        pagesToShow.push('...');
    }

    for (let i = start; i <= end; i++) {
        if (!pagesToShow.includes(i)) {
            pagesToShow.push(i);
        }
    }

    if (currentPage + 2 < totalPages - 1) { // Add ellipsis if gap before last page
        pagesToShow.push('...');
    }

    if (!pagesToShow.includes(totalPages)) { // Always show last page
        pagesToShow.push(totalPages);
    }

    pagesToShow.forEach(p => {
        if (p === '...') {
        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        } else {
        html += `
            <li class="page-item ${p === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${p}">${p}</a>
            </li>`;
        }
    });


    // Next Button
    html += `
    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
        </a>
    </li>`;

    html += `</ul></nav>`;
    return html;
}


/* ---------- Listeners ---------- */

function attachEditArchiveListeners(token) {
    // UPDATED selector to generic table body
    document.querySelectorAll("#main-table-body .edit-btn").forEach((btn) => {
        if (btn.listenerAttached) return;
        btn.listenerAttached = true;

        btn.addEventListener("click", async (e) => {
            const id = e.currentTarget.closest("tr").dataset.id;
            try {
                const res = await fetch(`${TENANTS_BASE}/${id}`, { headers: apiHeaders(token) });
                const json = await res.json();
                fillEditModal(Array.isArray(json) ? json[0] : json);
                new bootstrap.Modal(document.getElementById("editTenantModal")).show();
            } catch {
                showError("Failed to fetch tenant details.");
            }
        });
    });

    // UPDATED selector to generic table body
    document.querySelectorAll("#main-table-body .archive-btn").forEach((btn) => {
        if (btn.listenerAttached) return;
        btn.listenerAttached = true;

        btn.addEventListener("click", (e) => {
            const id = e.currentTarget.closest("tr").dataset.id;

            confirmAction(
                "Archive this tenant?",
                "Yes, archive it",
                "Cancel",
                async () => {
                    try {
                        const res = await fetch(`${TENANTS_BASE}/${id}`, { method: "DELETE", headers: apiHeaders(token) });
                        if (!res.ok) throw new Error();
                        showSuccess("Tenant archived successfully.");
                        // Reload all data
                        await loadTenants(token);
                        await loadArchivedTenants(token);
                        // Re-render the current view
                        renderDisplay(1); // Go back to page 1 of active tenants
                    } catch {
                        showError("Failed to archive tenant.");
                    }
                }
            );
        });
    });
}
function attachRestoreListeners(token) {
    // UPDATED selector to generic table body
    document.querySelectorAll("#main-table-body .restore-btn").forEach((btn) => {
        if (btn.listenerAttached) return;
        btn.listenerAttached = true;

        btn.addEventListener("click", (e) => {
            const id = e.currentTarget.dataset.id;

            confirmAction(
                "Restore this tenant?",
                "Yes, restore it",
                "Cancel",
                async () => {
                    try {
                        const res = await fetch(`${TENANTS_BASE}/${id}/restore`, {
                            method: "PUT",
                            headers: apiHeaders(token),
                        });
                        if (!res.ok) throw new Error();
                        showSuccess("Tenant restored successfully.");
                        // Reload all data
                        await loadTenants(token);
                        await loadArchivedTenants(token);
                        // Re-render the current view
                        renderDisplay(1); // Go back to page 1 of archived tenants
                    } catch {
                        showError("Failed to restore tenant.");
                    }
                }
            );
        });
    });
}
/* ---------- Edit Form ---------- */

async function handleEditSubmit(token) {
    const id = document.querySelector("#tenantId").value;
    if (!id) return showError("Tenant ID is missing.");

    const payload = {
        first_name: document.querySelector("#editFirstName").value,
        middle_name: document.querySelector("#editMiddleName").value || null,
        last_name: document.querySelector("#editLastName").value,
        email: document.querySelector("#editEmail").value,
        contact_num: document.querySelector("#editContact").value,
        unit_id: document.querySelector("#editUnitId").value,
    };

    const submitBtn = document.querySelector("#editTenantSubmitButton");
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Saving...';

    try {
        const res = await fetch(`${TENANTS_BASE}/${id}`, {
            method: "PUT",
            headers: apiHeaders(token),
            body: JSON.stringify(payload),
        });
        if (!res.ok) throw new Error();
        showSuccess("Tenant updated successfully.");
        bootstrap.Modal.getInstance(document.getElementById("editTenantModal")).hide();

        // Reload tenants data and re-render current view
        await loadTenants(token);
        renderDisplay(); // Re-render current page
    } catch {
        showError("Failed to update tenant.");
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Save Changes';
    }
}

/* ---------- Utilities ---------- */

function fillEditModal(tenant) {
    document.getElementById("tenantId").value = tenant.id ?? "";
    document.getElementById("editFirstName").value = tenant.first_name ?? "";
    document.getElementById("editMiddleName").value = tenant.middle_name ?? "";
    document.getElementById("editLastName").value = tenant.last_name ?? "";
    document.getElementById("editEmail").value = tenant.email ?? "";
    document.getElementById("editContact").value = tenant.contact_num ?? "";
    document.getElementById("editUnitId").value = tenant.unit_id ?? "";
}

function escapeHtml(str) {
    return String(str || "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}