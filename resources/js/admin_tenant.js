// admin_tenant.js
// Uses SweetAlert2 for all alerts

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

  safeAttach("#searchTenants", "input", filterTenants);
  safeAttach("#searchArchived", "input", filterArchivedTenants);

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
    body.innerHTML = "";

    if (!Array.isArray(data) || data.length === 0) {
      body.innerHTML = `<tr><td colspan="5" class="text-muted py-3">No tenants found.</td></tr>`;
      return;
    }

    data.forEach((t) => {
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
    body.innerHTML = "";

    if (!Array.isArray(data) || data.length === 0) {
      body.innerHTML = `<tr><td colspan="5" class="text-muted py-3">No archived tenants found.</td></tr>`;
      return;
    }

    data.forEach((t) => {
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

    attachRestoreListeners(token);
  } catch (err) {
    console.error("loadArchivedTenants error:", err);
    body.innerHTML = `<tr><td colspan="5" class="text-danger py-3">Error loading archived tenants.</td></tr>`;
  }
}

/* ---------- Listeners ---------- */

function attachEditArchiveListeners(token) {
  document.querySelectorAll("#tenant-table-body .edit-btn").forEach((btn) =>
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
  );

  document.querySelectorAll("#tenant-table-body .archive-btn").forEach((btn) =>
    btn.addEventListener("click", async (e) => {
      const id = e.currentTarget.closest("tr").dataset.id;
      const result = await showConfirm("Archive this tenant?");
      if (!result.isConfirmed) return;

      try {
        const res = await fetch(`${TENANTS_BASE}/${id}`, { method: "DELETE", headers: apiHeaders(token) });
        if (!res.ok) throw new Error();
        showSuccess("Tenant archived successfully.");
        await loadTenants(token);
        await loadArchivedTenants(token);
      } catch {
        showError("Failed to archive tenant.");
      }
    })
  );
}

function attachRestoreListeners(token) {
  document.querySelectorAll("#archived-table-body .restore-btn").forEach((btn) =>
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
        await loadTenants(token);
        await loadArchivedTenants(token);
      } catch {
        showError("Failed to restore tenant.");
      }
    })
  );
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

function filterTenants() {
  const q = document.getElementById("searchTenants")?.value.trim().toLowerCase() || "";
  document.querySelectorAll("#tenant-table-body tr").forEach((r) => {
    if (r.querySelector('td[colspan="5"]')) return;
    r.style.display = r.textContent.toLowerCase().includes(q) ? "" : "none";
  });
}

function filterArchivedTenants() {
  const q = document.getElementById("searchArchived")?.value.trim().toLowerCase() || "";
  document.querySelectorAll("#archived-table-body tr").forEach((r) => {
    if (r.querySelector('td[colspan="5"]')) return;
    r.style.display = r.textContent.toLowerCase().includes(q) ? "" : "none";
  });
}

function escapeHtml(str) {
  return String(str || "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
