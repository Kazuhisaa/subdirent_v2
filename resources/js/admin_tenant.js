// admin_tenant.js
// This version replaces the "View" button in the archive modal with a direct "Restore" button.

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
    console.error("Admin API token not found. App cannot function.");
    return;
  }

  // Attach search handlers
  safeAttach("#searchTenants", "input", filterTenants);
  safeAttach("#searchArchived", "input", filterArchivedTenants);

  // Load tables
  await loadTenants(token);
  await loadArchivedTenants(token);

  // Edit form submit
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
  if (el) {
    el.addEventListener(evt, handler);
  } else {
    console.warn(`Selector ${selector} not found for safeAttach.`);
  }
}

function apiHeaders(token) {
  return {
    "Accept": "application/json",
    "Authorization": `Bearer ${token}`,
    "Content-Type": "application/json",
  };
}

/* ---------- API paths ---------- */
const TENANTS_BASE = "/api/admin/api/tenants";

/* ---------- Loaders ---------- */

async function loadTenants(token) {
  const body = document.querySelector("#tenant-table-body");
  if (!body) return console.warn("#tenant-table-body not found");
  body.innerHTML = `<tr><td colspan="5" class="py-3 text-muted">Loading tenants...</td></tr>`;

  try {
    const res = await fetch(TENANTS_BASE, { headers: apiHeaders(token) });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    body.innerHTML = ""; // Clear loader

    if (!Array.isArray(data) || data.length === 0) {
      body.innerHTML = `<tr><td colspan="5" class="text-muted py-3">No tenants found.</td></tr>`;
      return;
    }

    data.forEach((t) => {
      const name = [t.first_name, t.middle_name, t.last_name].filter(Boolean).join(" ");
      const unitTitle = (t.unit && (t.unit.title || t.unit.unit_name)) ? (t.unit.title || t.unit.unit_name) : "N/A";
      const row = `
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
      body.insertAdjacentHTML("beforeend", row);
    });

    attachEditArchiveListeners(token);
  } catch (err) {
    console.error("loadTenants error:", err);
    body.innerHTML = `<tr><td colspan="5" class="text-danger py-3">Error loading tenants.</td></tr>`;
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
    body.innerHTML = ""; // Clear loader

    if (!Array.isArray(data) || data.length === 0) {
      body.innerHTML = `<tr><td colspan="5" class="text-muted py-3">No archived tenants found.</td></tr>`;
      return;
    }

    data.forEach((t) => {
      const name = [t.first_name, t.middle_name, t.last_name].filter(Boolean).join(" ");
      const unitTitle = (t.unit && (t.unit.title || t.unit.unit_name)) ? (t.unit.title || t.unit.unit_name) : "N/A";
      const row = `
        <tr data-id="${t.id}">
          <td>${escapeHtml(name)}</td>
          <td>${escapeHtml(t.email || "")}</td>
          <td>${escapeHtml(t.contact_num || "")}</td>
          <td>${escapeHtml(unitTitle)}</td>
          <td class="text-center">
            <button class="btn btn-sm btn-outline-success restore-btn" data-id="${t.id}" title="Restore">
              <i class="bi bi-arrow-counterclockwise"></i> Restore
            </button>
          </td>
        </tr>`;
      body.insertAdjacentHTML("beforeend", row);
    });

    attachRestoreListeners(token); // Attaches restore listeners
  } catch (err) {
    console.error("loadArchivedTenants error:", err);
    body.innerHTML = `<tr><td colspan="5" class="text-danger py-3">Error loading archived tenants.</td></tr>`;
  }
}

/* ---------- Attach listeners ---------- */

function attachEditArchiveListeners(token) {
  // edit
  document.querySelectorAll("#tenant-table-body .edit-btn").forEach((btn) =>
    btn.addEventListener("click", async (e) => {
      const button = e.currentTarget;
      const tr = button.closest("tr");
      if (!tr) return;
      const id = tr.dataset.id;
      button.disabled = true; // Prevent double clicks
      try {
        const res = await fetch(`${TENANTS_BASE}/${id}`, { headers: apiHeaders(token) });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = await res.json();
        const tenant = Array.isArray(json) ? json[0] : json;
        fillEditModal(tenant);
        new bootstrap.Modal(document.getElementById("editTenantModal")).show();
      } catch (err) {
        console.error("fetch tenant for edit failed:", err);
        alert("Failed to fetch tenant details.");
      } finally {
        button.disabled = false;
      }
    })
  );

  // archive
  document.querySelectorAll("#tenant-table-body .archive-btn").forEach((btn) =>
    btn.addEventListener("click", async (e) => {
      const button = e.currentTarget;
      const tr = button.closest("tr");
      if (!tr) return;
      const id = tr.dataset.id;
      if (!confirm("Are you sure you want to archive this tenant?")) return;

      button.disabled = true;
      try {
        const res = await fetch(`${TENANTS_BASE}/${id}`, {
          method: "DELETE",
          headers: apiHeaders(token),
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        alert("Tenant archived successfully.");
        // reload both tables
        await loadTenants(token);
        await loadArchivedTenants(token);
      } catch (err) {
        console.error("archive failed:", err);
        alert("Failed to archive tenant.");
        button.disabled = false;
      }
    })
  );
}

function attachRestoreListeners(token) {
  document.querySelectorAll("#archived-table-body .restore-btn").forEach((btn) =>
    btn.addEventListener("click", async (e) => {
      const button = e.currentTarget;
      const id = button.dataset.id;

      if (!id) return;
      if (!confirm("Do you want to restore this tenant?")) return;

      button.disabled = true;
      button.innerHTML = '<i class="bi bi-arrow-repeat"></i> Restoring...';

      try {
        const res = await fetch(`${TENANTS_BASE}/${id}/restore`, {
          method: "PUT",
          headers: apiHeaders(token),
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        
        alert("Tenant restored successfully.");

        // reload both tables
        await loadTenants(token);
        await loadArchivedTenants(token);

      } catch (err) {
        console.error("restore failed:", err);
        alert("Failed to restore tenant.");
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i> Restore';
      }
    })
  );
}

/* ---------- Edit submit handler ---------- */

async function handleEditSubmit(token) {
  const id = document.querySelector("#tenantId").value;
  if (!id) {
    alert("Tenant ID is missing. Cannot update.");
    return;
  }

  const payload = {
    first_name: document.querySelector("#editFirstName").value,
    middle_name: document.querySelector("#editMiddleName").value || null,
    last_name: document.querySelector("#editLastName").value,
    email: document.querySelector("#editEmail").value,
    contact_num: document.querySelector("#editContact").value,
    unit_id: document.querySelector("#editUnitId").value
  };

  if (!payload.first_name || !payload.last_name || !payload.email) {
    alert("First Name, Last Name, and Email are required.");
    return;
  }

  const submitButton = document.querySelector("#editTenantSubmitButton");
  submitButton.disabled = true;
  submitButton.innerHTML = '<i class="bi bi-arrow-repeat"></i> Saving...';

  try {
    const res = await fetch(`${TENANTS_BASE}/${id}`, {
      method: "PUT",
      headers: apiHeaders(token),
      body: JSON.stringify(payload),
    });
    if (!res.ok) {
      const errorData = await res.json();
      throw new Error(`HTTP ${res.status}: ${errorData.message || 'Update failed'}`);
    }

    alert("Tenant updated successfully.");
    bootstrap.Modal.getInstance(document.getElementById("editTenantModal")).hide();
    await loadTenants(token);

  } catch (err) {
    console.error("update tenant failed:", err);
    alert(`Failed to update tenant. ${err.message}`);
  } finally {
    submitButton.disabled = false;
    submitButton.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Save Changes';
  }
}

/* ---------- Utilities ---------- */

function fillEditModal(tenant) {
  if (!tenant) {
    console.error("fillEditModal received null or undefined tenant");
    return;
  }
  document.getElementById("tenantId").value = tenant.id ?? "";
  document.getElementById("editFirstName").value = tenant.first_name ?? "";
  document.getElementById("editMiddleName").value = tenant.middle_name ?? "";
  document.getElementById("editLastName").value = tenant.last_name ?? "";
  document.getElementById("editEmail").value = tenant.email ?? "";
  document.getElementById("editContact").value = tenant.contact_num ?? "";
  document.getElementById("editUnitId").value = tenant.unit_id ?? "";
}

function filterTenants() {
  const inputEl = document.getElementById("searchTenants");
  if (!inputEl) return;
  const q = inputEl.value.trim().toLowerCase();
  document.querySelectorAll("#tenant-table-body tr").forEach((r) => {
    if (r.querySelector('td[colspan="5"]')) return;
    const text = r.textContent.toLowerCase();
    r.style.display = text.includes(q) ? "" : "none";
  });
}

function filterArchivedTenants() {
  const inputEl = document.getElementById("searchArchived");
  if (!inputEl) return;
  const q = inputEl.value.trim().toLowerCase();
  document.querySelectorAll("#archived-table-body tr").forEach((r) => {
    if (r.querySelector('td[colspan="5"]')) return;
    const text = r.textContent.toLowerCase();
    r.style.display = text.includes(q) ? "" : "none";
  });
}

function escapeHtml(str) {
  if (!str) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
function escapeAttr(s) { return escapeHtml(s).replace(/"/g, "&quot;"); }