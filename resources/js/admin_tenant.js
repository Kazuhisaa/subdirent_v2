// admin_tenant.js - robust version with safe guards + single restore handler

async function ensureSanctumSession() {
  try {
    await fetch("/sanctum/csrf-cookie", { method: "GET", credentials: "include" });
  } catch (e) {
    console.warn("Failed to request sanctum csrf cookie:", e);
  }
}

document.addEventListener("DOMContentLoaded", async () => {
  await ensureSanctumSession();

  const token = document.querySelector('meta[name="admin-api-token"]')?.getAttribute("content");
  console.log("admin token present?", !!token);

  // Attach search handlers safely later when inputs exist
  safeAttach("#searchTenants", "input", filterTenants);
  safeAttach("#searchArchived", "input", filterArchivedTenants);

  // Load tables
  await loadTenants(token);
  await loadArchivedTenants(token);

  // Edit form submit (guard)
  const editForm = document.querySelector("#editTenantForm");
  if (editForm) {
    editForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      await handleEditSubmit(token);
    });
  } else {
    console.warn("#editTenantForm not found");
  }

  // Ensure the restore handler is attached exactly once
  attachSingleRestoreHandler(token);
});

/* ---------- Helpers ---------- */

function safeAttach(selector, evt, handler) {
  const el = document.querySelector(selector);
  if (el) el.addEventListener(evt, handler);
  else {
    // fallback delegation: attach to document and check target id
    document.addEventListener(evt, (ev) => {
      if (!ev.target) return;
      if (ev.target.matches && ev.target.matches(selector)) handler(ev);
      if (ev.target.id === selector.replace("#", "")) handler(ev);
    });
  }
}

function apiHeaders(token) {
  const h = { Accept: "application/json" };
  if (token) h["Authorization"] = `Bearer ${token}`;
  h["Content-Type"] = "application/json";
  return h;
}

/* ---------- API paths ---------- */
/* Use the same path you're using now; change if yours differs */
const TENANTS_BASE = "/api/admin/api/tenants"; // keep this if your routes use /api/admin/api/tenants

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
      const row = `
        <tr data-id="${t.id}">
          <td>${escapeHtml(name)}</td>
          <td>${escapeHtml(t.email || "")}</td>
          <td>${escapeHtml(t.contact_num || "")}</td>
          <td>${escapeHtml(unitTitle)}</td>
          <td>
            <button class="btn btn-sm btn-outline-primary me-2 edit-btn"><i class="bi bi-pencil-square"></i></button>
            <button class="btn btn-sm btn-outline-danger archive-btn"><i class="bi bi-archive"></i></button>
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
    body.innerHTML = "";

    if (!Array.isArray(data) || data.length === 0) {
      body.innerHTML = `<tr><td colspan="5" class="text-muted py-3">No archived tenants found.</td></tr>`;
      return;
    }

    data.forEach((t) => {
      const name = [t.first_name, t.middle_name, t.last_name].filter(Boolean).join(" ");
      const unitTitle = (t.unit && (t.unit.title || t.unit.unit_name)) ? (t.unit.title || t.unit.unit_name) : "N/A";
      const row = `
        <tr data-id="${t.id}"
            data-name="${escapeAttr(name)}"
            data-email="${escapeAttr(t.email || "")}"
            data-contact="${escapeAttr(t.contact_num || "")}"
            data-unit="${escapeAttr(unitTitle)}">
          <td>${escapeHtml(name)}</td>
          <td>${escapeHtml(t.email || "")}</td>
          <td>${escapeHtml(t.contact_num || "")}</td>
          <td>${escapeHtml(unitTitle)}</td>
          <td>
            <button class="btn btn-sm btn-outline-secondary view-archived-btn"><i class="bi bi-eye"></i> View</button>
          </td>
        </tr>`;
      body.insertAdjacentHTML("beforeend", row);
    });

    attachArchivedViewListeners();
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
      const tr = e.target.closest("tr");
      if (!tr) return;
      const id = tr.dataset.id;
      try {
        const res = await fetch(`${TENANTS_BASE}/find/${id}`, { headers: apiHeaders(token) });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = await res.json();
        // some endpoints return object or array - handle both
        const tenant = Array.isArray(json) ? json[0] : json;
        fillEditModal(tenant);
        new bootstrap.Modal(document.getElementById("editTenantModal")).show();
      } catch (err) {
        console.error("fetch tenant for edit failed:", err);
        alert("Failed to fetch tenant details.");
      }
    })
  );

  // archive
  document.querySelectorAll("#tenant-table-body .archive-btn").forEach((btn) =>
    btn.addEventListener("click", async (e) => {
      const tr = e.target.closest("tr");
      if (!tr) return;
      const id = tr.dataset.id;
      if (!confirm("Archive this tenant?")) return;
      try {
        const res = await fetch(`${TENANTS_BASE}/${id}`, {
          method: "DELETE",
          headers: apiHeaders(document.querySelector('meta[name="admin-api-token"]')?.getAttribute("content")),
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        alert("Tenant archived.");
        // reload both tables
        await loadTenants(document.querySelector('meta[name="admin-api-token"]')?.getAttribute("content"));
        await loadArchivedTenants(document.querySelector('meta[name="admin-api-token"]')?.getAttribute("content"));
      } catch (err) {
        console.error("archive failed:", err);
        alert("Failed to archive tenant.");
      }
    })
  );
}

function attachArchivedViewListeners() {
  document.querySelectorAll("#archived-table-body .view-archived-btn").forEach((btn) =>
    btn.addEventListener("click", (e) => {
      const tr = e.target.closest("tr");
      if (!tr) return;
      const id = tr.dataset.id;
      const name = tr.dataset.name || "";
      const email = tr.dataset.email || "";
      const contact = tr.dataset.contact || "";
      const unit = tr.dataset.unit || "";

      const modal = document.getElementById("viewArchivedModal");
      if (!modal) return console.warn("#viewArchivedModal missing");

      // populate fields
      document.getElementById("archivedFullName").value = name;
      document.getElementById("archivedEmail").value = email;
      document.getElementById("archivedContact").value = contact;
      document.getElementById("archivedUnit").value = unit;
      document.getElementById("restoreTenantBtn").dataset.id = id;

      new bootstrap.Modal(modal).show();
    })
  );
}

/* ---------- Single restore handler (no duplicates) ---------- */

let restoreHandlerAttached = false;
function attachSingleRestoreHandler(token) {
  if (restoreHandlerAttached) return;
  const btn = document.getElementById("restoreTenantBtn");
  if (!btn) {
    console.warn("#restoreTenantBtn not found");
    // watch for dynamic addition: use mutation observer as fallback (optional)
    return;
  }

  btn.addEventListener("click", async (e) => {
    const id = e.currentTarget.dataset.id;
    if (!id) {
      alert("No tenant selected to restore.");
      return;
    }
    if (!confirm("Do you want to restore this tenant?")) return;

    try {
      const res = await fetch(`${TENANTS_BASE}/${id}/restore`, {
        method: "PUT",
        headers: apiHeaders(document.querySelector('meta[name="admin-api-token"]')?.getAttribute("content")),
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      alert("Tenant restored.");
      // hide modal if open
      try { bootstrap.Modal.getInstance(document.getElementById("viewArchivedModal"))?.hide(); } catch {}
      // reload lists
      await loadArchivedTenants(document.querySelector('meta[name="admin-api-token"]')?.getAttribute("content"));
      await loadTenants(document.querySelector('meta[name="admin-api-token"]')?.getAttribute("content"));
    } catch (err) {
      console.error("restore failed:", err);
      alert("Failed to restore tenant.");
    }
  });

  restoreHandlerAttached = true;
}

/* ---------- Edit submit handler ---------- */

async function handleEditSubmit(token) {
  const id = document.querySelector("#tenantId").value;
  if (!id) { alert("Tenant id missing"); return; }
  const payload = {
    first_name: document.querySelector("#editFirstName").value,
    middle_name: document.querySelector("#editMiddleName").value || null,
    last_name: document.querySelector("#editLastName").value,
    email: document.querySelector("#editEmail").value,
    contact_num: document.querySelector("#editContact").value,
  };
  try {
    const res = await fetch(`${TENANTS_BASE}/${id}`, {
      method: "PUT",
      headers: apiHeaders(token),
      body: JSON.stringify(payload),
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    alert("Tenant updated.");
    bootstrap.Modal.getInstance(document.getElementById("editTenantModal")).hide();
    await loadTenants(token);
  } catch (err) {
    console.error("update tenant failed:", err);
    alert("Failed to update tenant.");
  }
}

/* ---------- Utilities ---------- */

function fillEditModal(tenant) {
  if (!tenant) return;
  document.getElementById("tenantId").value = tenant.id ?? "";
  document.getElementById("editFirstName").value = tenant.first_name ?? "";
  document.getElementById("editMiddleName").value = tenant.middle_name ?? "";
  document.getElementById("editLastName").value = tenant.last_name ?? "";
  document.getElementById("editEmail").value = tenant.email ?? "";
  document.getElementById("editContact").value = tenant.contact_num ?? "";
}

function filterTenants() {
  const inputEl = document.getElementById("searchTenants");
  if (!inputEl) return;
  const q = inputEl.value.trim().toLowerCase();
  document.querySelectorAll("#tenant-table-body tr").forEach((r) => {
    const text = r.textContent.toLowerCase();
    r.style.display = text.includes(q) ? "" : "none";
  });
}

function filterArchivedTenants() {
  const inputEl = document.getElementById("searchArchived");
  if (!inputEl) return;
  const q = inputEl.value.trim().toLowerCase();
  document.querySelectorAll("#archived-table-body tr").forEach((r) => {
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
