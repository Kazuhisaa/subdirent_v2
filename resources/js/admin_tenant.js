async function ensureSanctumSession() {
    await fetch("/sanctum/csrf-cookie", { method: "GET", credentials: "include" });
}

document.addEventListener("DOMContentLoaded", async () => {
    await ensureSanctumSession();
    const token = document.querySelector('meta[name="admin-api-token"]')?.getAttribute("content");

    await loadTenants(token);
    await loadArchivedTenants(token);

    // Handle edit form submission
    document.querySelector("#editTenantForm").addEventListener("submit", async (e) => {
        e.preventDefault();
        const id = document.querySelector("#tenantId").value;
        const data = {
            first_name: document.querySelector("#editFirstName").value,
            middle_name: document.querySelector("#editMiddleName").value || null,
            last_name: document.querySelector("#editLastName").value,
            email: document.querySelector("#editEmail").value,
            contact_num: document.querySelector("#editContact").value,
            unit_id: 1, // keep or replace dynamically
        };

        const res = await fetch(`/api/admin/api/tenants/${id}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${token}`,
            },
            body: JSON.stringify(data),
        });

        if (res.ok) {
            alert("✅ Tenant updated successfully!");
            bootstrap.Modal.getInstance(document.getElementById("editTenantModal")).hide();
            await loadTenants(token);
        } else {
            alert("❌ Failed to update tenant.");
        }
    });
});

// ==========================
// LOAD ACTIVE TENANTS
// ==========================
async function loadTenants(token) {
    const body = document.querySelector("#tenant-table-body");
    body.innerHTML = `<tr><td colspan="5" class="py-3 text-muted">Loading...</td></tr>`;

    const res = await fetch("/api/admin/api/tenants", {
        headers: { "Authorization": `Bearer ${token}` },
    });

    const data = await res.json();
    body.innerHTML = "";

    if (data.length === 0) {
        body.innerHTML = `<tr><td colspan="5" class="py-3 text-muted">No tenants found.</td></tr>`;
        return;
    }

    data.forEach((t) => {
        const name = [t.first_name, t.middle_name, t.last_name].filter(Boolean).join(" ");
        const row = `
            <tr data-id="${t.id}">
                <td>${name}</td>
                <td>${t.email}</td>
                <td>${t.contact_num}</td>
                <td>${t.unit?.title ?? "N/A"}</td>
                <td>
                    <button class="btn btn-sm btn-light border-0 text-primary me-2 edit-btn"><i class="bi bi-pencil-square"></i></button>
                    <button class="btn btn-sm btn-light border-0 text-danger archive-btn"><i class="bi bi-archive"></i></button>
                </td>
            </tr>`;
        body.insertAdjacentHTML("beforeend", row);
    });

    attachEditAndArchive(token);
}

// ==========================
// LOAD ARCHIVED TENANTS
// ==========================
async function loadArchivedTenants(token) {
    const body = document.querySelector("#archived-table-body");
    body.innerHTML = `<tr><td colspan="5" class="py-3 text-muted">Loading...</td></tr>`;

    const res = await fetch("/api/admin/api/tenants/archived", {
        headers: { "Authorization": `Bearer ${token}` },
    });

    const data = await res.json();
    body.innerHTML = "";

    if (data.length === 0) {
        body.innerHTML = `<tr><td colspan="5" class="py-3 text-muted">No archived tenants.</td></tr>`;
        return;
    }

    data.forEach((t) => {
        const name = [t.first_name, t.middle_name, t.last_name].filter(Boolean).join(" ");
        const row = `
            <tr data-id="${t.id}">
                <td>${name}</td>
                <td>${t.email}</td>
                <td>${t.contact_num}</td>
                <td>${t.unit?.title ?? "N/A"}</td>
                <td>
                    <button class="btn btn-sm btn-success restore-btn"><i class="bi bi-arrow-clockwise"></i> Restore</button>
                </td>
            </tr>`;
        body.insertAdjacentHTML("beforeend", row);
    });

    attachRestore(token);
}

// ==========================
// ATTACH BUTTON ACTIONS
// ==========================
function attachEditAndArchive(token) {
    document.querySelectorAll(".edit-btn").forEach((btn) =>
        btn.addEventListener("click", async (e) => {
            const row = e.target.closest("tr");
            const id = row.dataset.id;

            const res = await fetch(`/api/admin/api/tenants/${id}`, {
                headers: { "Authorization": `Bearer ${token}` },
            });
            const tenant = (await res.json())[0];

            document.querySelector("#tenantId").value = tenant.id;
            document.querySelector("#editFirstName").value = tenant.first_name;
            document.querySelector("#editMiddleName").value = tenant.middle_name || "";
            document.querySelector("#editLastName").value = tenant.last_name;
            document.querySelector("#editEmail").value = tenant.email;
            document.querySelector("#editContact").value = tenant.contact_num;

            const modal = new bootstrap.Modal(document.getElementById("editTenantModal"));
            modal.show();
        })
    );

    document.querySelectorAll(".archive-btn").forEach((btn) =>
        btn.addEventListener("click", async (e) => {
            const row = e.target.closest("tr");
            const id = row.dataset.id;
            if (!confirm("Archive this tenant?")) return;

            const res = await fetch(`/api/admin/api/tenants/${id}`, {
                method: "DELETE",
                headers: { "Authorization": `Bearer ${token}` },
            });

            if (res.ok) {
                alert("🗂️ Tenant archived successfully!");
                await loadTenants(token);
                await loadArchivedTenants(token);
            } else alert("❌ Failed to archive tenant.");
        })
    );
}

// ==========================
// RESTORE ARCHIVED TENANT
// ==========================
function attachRestore(token) {
    document.querySelectorAll(".restore-btn").forEach((btn) =>
        btn.addEventListener("click", async (e) => {
            const id = e.target.closest("tr").dataset.id;
            if (!confirm("Restore this tenant?")) return;

            const res = await fetch(`/api/admin/api/tenants/${id}/restore`, {
                method: "PUT",
                headers: { "Authorization": `Bearer ${token}` },
            });

            if (res.ok) {
                alert("✅ Tenant restored!");
                await loadTenants(token);
                await loadArchivedTenants(token);
            } else alert("❌ Failed to restore tenant.");
        })
    );
}
