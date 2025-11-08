// admin_tenant.js
// Uses SweetAlert2 for all alerts

// --- Global state for client-side pagination and data ---
let allTenants = [];
let allArchivedTenants = [];
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

  // Updated listeners to use the new render functions
  safeAttach("#searchTenants", "input", () => renderTenantDisplay(1));
  safeAttach("#searchArchived", "input", () => renderArchivedDisplay(1));

  await loadTenants(token);
  await loadArchivedTenants(token);

  const editForm = document.querySelector("#editTenantForm");
  if (editForm) {
    editForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      await handleEditSubmit(token);
    });
  } else {
    console.warn("#editTenantForm not found");
  }
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

function showSuccess(message) {
  Swal.fire({
    icon: "success",
    title: "Success!",
    text: message,
    confirmButtonColor: "#198754",
  });
}

function showError(message) {
  Swal.fire({
    icon: "error",
    title: "Error!",
    text: message,
    confirmButtonColor: "#dc3545",
  });
}

function showConfirm(message) {
  return Swal.fire({
    icon: "warning",
    title: "Are you sure?",
    text: message,
    showCancelButton: true,
    confirmButtonText: "Yes",
    cancelButtonText: "Cancel",
    confirmButtonColor: "#0d6efd",
  });
}

/* ---------- Loaders ---------- */

async function loadTenants(token) {
  const body = document.querySelector("#tenant-table-body");
  if (!body) return console.warn("#tenant-table-body not found");
  body.innerHTML = `<tr><td colspan="5" class="py-3 text-muted">Loading tenants...</td></tr>`;

  try {
    const res = await fetch(TENANTS_BASE, { headers: apiHeaders(token) });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();

    if (!Array.isArray(data)) {
        throw new Error("Invalid data format from API");
    }

    // Sort by ID descending (latest first)
    data.sort((a, b) => b.id - a.id);
    allTenants = data; // Store all tenants
    
    renderTenantDisplay(1); // Render the first page

  } catch (err) {
    console.error("loadTenants error:", err);
    body.innerHTML = `<tr><td colspan="5" class="text-danger py-3">Error loading tenants.</td></tr>`;
    allTenants = [];
    renderTenantDisplay(1); // Render empty state
  }
}

async function loadArchivedTenants(token) {
  const body = document.querySelector("#archived-table-body");
  if (!body) return console.warn("#archived-table-body not found");
  body.innerHTML = `<tr><td colspan="5" class="py-3 text-muted">Loading archived tenants...</td></tr>`;

  try {
    const res = await fetch(`${TENANTS_BASE}/archived`, { headers: apiHeaders(token) });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    
    if (!Array.isArray(data)) {
        throw new Error("Invalid data format from API");
    }

    // Sort by ID descending (latest first)
    data.sort((a, b) => b.id - a.id);
    allArchivedTenants = data; // Store all archived tenants

    renderArchivedDisplay(1); // Render the first page

  } catch (err) {
    console.error("loadArchivedTenants error:", err);
    body.innerHTML = `<tr><td colspan="5" class="text-danger py-3">Error loading archived tenants.</td></tr>`;
    allArchivedTenants = [];
    renderArchivedDisplay(1); // Render empty state
  }
}

/* ---------- Render Functions (New) ---------- */

/**
 * Filters, paginates, and renders the active tenants table and pagination.
 */
function renderTenantDisplay(page = 1) {
  const body = document.querySelector("#tenant-table-body");
  const paginationContainer = document.querySelector("#tenant-pagination-container");
  if (!body || !paginationContainer) return;

  // 1. Filter Data
  const query = document.getElementById("searchTenants")?.value.trim().toLowerCase() || "";
  const filteredData = allTenants.filter(t => {
      const name = [t.first_name, t.middle_name, t.last_name].filter(Boolean).join(" ");
      const unitTitle = (t.unit && (t.unit.title || t.unit.unit_name)) ? (t.unit.title || t.unit.unit_name) : "";
      const searchableText = [name, t.email, t.contact_num, unitTitle].join(" ").toLowerCase();
      return searchableText.includes(query);
  });

  // 2. Paginate Data
  const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
  const start = (page - 1) * ROWS_PER_PAGE;
  const end = start + ROWS_PER_PAGE;
  const pageData = filteredData.slice(start, end);

  // 3. Render Table Rows
  body.innerHTML = ""; // Clear previous rows
  if (pageData.length === 0) {
    body.innerHTML = `<tr><td colspan="5" class="text-muted py-3">No tenants found.</td></tr>`;
  } else {
    pageData.forEach((t) => {
      const name = [t.first_name, t.middle_name, t.last_name].filter(Boolean).join(" ");
      const unitTitle = (t.unit && (t.unit.title || t.unit.unit_name)) ? (t.unit.title || t.unit.unit_name) : "N/A";
      body.insertAdjacentHTML("beforeend", `
        <tr data-id="${t.id}">
          <td>${escapeHtml(name)}</td>
          <td>${escapeHtml(t.email || "")}</td>
          <td>${escapeHtml(t.contact_num || "")}</td>
          <td>${escapeHtml(unitTitle)}</td>
          <td class="text-center">
            <button class="btn btn-sm btn-outline-blue edit-btn" title="Edit"><i class="bi bi-pencil-square"></i></button>
            <button class="btn btn-sm btn-outline-warning archive-btn" title="Archive"><i class="bi bi-archive"></i></button>
          </td>
        </tr>`);
    });
  }

  // 4. Render Pagination
  paginationContainer.innerHTML = buildPaginationUI(totalPages, page);

  // 5. Re-attach listeners for new buttons
  attachEditArchiveListeners(currentToken);
  
  // 6. Attach listeners for pagination links
  paginationContainer.querySelectorAll(".page-link").forEach(link => {
      link.addEventListener("click", e => {
          e.preventDefault();
          const newPage = parseInt(e.target.dataset.page, 10);
          if (newPage) {
              renderTenantDisplay(newPage);
          }
      });
  });
}

/**
 * Filters, paginates, and renders the archived tenants table and pagination.
 */
function renderArchivedDisplay(page = 1) {
  const body = document.querySelector("#archived-table-body");
  const paginationContainer = document.querySelector("#archived-pagination-container");
  if (!body || !paginationContainer) return;

  // 1. Filter Data
  const query = document.getElementById("searchArchived")?.value.trim().toLowerCase() || "";
  const filteredData = allArchivedTenants.filter(t => {
      const name = [t.first_name, t.middle_name, t.last_name].filter(Boolean).join(" ");
      const unitTitle = (t.unit && (t.unit.title || t.unit.unit_name)) ? (t.unit.title || t.unit.unit_name) : "";
      const searchableText = [name, t.email, t.contact_num, unitTitle].join(" ").toLowerCase();
      return searchableText.includes(query);
  });

  // 2. Paginate Data
  const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
  const start = (page - 1) * ROWS_PER_PAGE;
  const end = start + ROWS_PER_PAGE;
  const pageData = filteredData.slice(start, end);

  // 3. Render Table Rows
  body.innerHTML = ""; // Clear previous rows
  if (pageData.length === 0) {
    body.innerHTML = `<tr><td colspan="5" class="text-muted py-3">No archived tenants found.</td></tr>`;
  } else {
    pageData.forEach((t) => {
      const name = [t.first_name, t.middle_name, t.last_name].filter(Boolean).join(" ");
      const unitTitle = (t.unit && (t.unit.title || t.unit.unit_name)) ? (t.unit.title || t.unit.unit_name) : "N/A";
      body.insertAdjacentHTML("beforeend", `
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
        </tr>`);
    });
  }

  // 4. Render Pagination
  paginationContainer.innerHTML = buildPaginationUI(totalPages, page);

  // 5. Re-attach listeners for new buttons
  attachRestoreListeners(currentToken);
  
  // 6. Attach listeners for pagination links
  paginationContainer.querySelectorAll(".page-link").forEach(link => {
      link.addEventListener("click", e => {
          e.preventDefault();
          const newPage = parseInt(e.target.dataset.page, 10);
          if (newPage) {
              renderArchivedDisplay(newPage);
          }
      });
  });
}

/**
 * Builds Bootstrap pagination HTML string.
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
  // Simple logic: show current page, +/- 2 pages, and first/last
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
  // Note: These listeners are now attached *after* each render
  document.querySelectorAll("#tenant-table-body .edit-btn").forEach((btn) => {
    // Prevent double-listening
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
    })
  });

  document.querySelectorAll("#tenant-table-body .archive-btn").forEach((btn) => {
    // Prevent double-listening
    if (btn.listenerAttached) return;
    btn.listenerAttached = true;

    btn.addEventListener("click", async (e) => {
      const id = e.currentTarget.closest("tr").dataset.id;
      const result = await showConfirm("Archive this tenant?");
      if (!result.isConfirmed) return;

      try {
        const res = await fetch(`${TENANTS_BASE}/${id}`, { method: "DELETE", headers: apiHeaders(token) });
        if (!res.ok) throw new Error();
        showSuccess("Tenant archived successfully.");
        // Reload all data and re-render
        await loadTenants(token);
        await loadArchivedTenants(token);
      } catch {
        showError("Failed to archive tenant.");
      }
    })
  });
}

function attachRestoreListeners(token) {
  // Note: These listeners are now attached *after* each render
  document.querySelectorAll("#archived-table-body .restore-btn").forEach((btn) => {
    // Prevent double-listening
    if (btn.listenerAttached) return;
    btn.listenerAttached = true;

    btn.addEventListener("click", async (e) => {
      const id = e.currentTarget.dataset.id;
      const result = await showConfirm("Restore this tenant?");
      if (!result.isConfirmed) return;

      try {
        const res = await fetch(`${TENANTS_BASE}/${id}/restore`, {
          method: "PUT",
          headers: apiHeaders(token),
        });
        if (!res.ok) throw new Error();
        showSuccess("Tenant restored successfully.");
        // Reload all data and re-render
        await loadTenants(token);
        await loadArchivedTenants(token);
      } catch {
        showError("Failed to restore tenant.");
      }
    })
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
    
    // Reload all data and re-render
    await loadTenants(token);
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

// **DEPRECATED** - These are now handled by the new render functions
// function filterTenants() {
//   const q = document.getElementById("searchTenants")?.value.trim().toLowerCase() || "";
//   document.querySelectorAll("#tenant-table-body tr").forEach((r) => {
//     if (r.querySelector('td[colspan="5"]')) return;
//     r.style.display = r.textContent.toLowerCase().includes(q) ? "" : "none";
//   });
// }
//
// function filterArchivedTenants() {
//   const q = document.getElementById("searchArchived")?.value.trim().toLowerCase() || "";
//   document.querySelectorAll("#archived-table-body tr").forEach((r) => {
//     if (r.querySelector('td[colspan="5"]')) return;
//     r.style.display = r.textContent.toLowerCase().includes(q) ? "" : "none";
//   });
// }

function escapeHtml(str) {
  return String(str || "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}