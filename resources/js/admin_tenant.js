async function ensureSanctumSession() {
    await fetch("/sanctum/csrf-cookie", {
        method: "GET",
        credentials: "include",
    });
}

document.addEventListener("DOMContentLoaded", async () => {
    const tableBody = document.querySelector("#tenant-table-body");
    const loader = document.querySelector("#loading-row");
    loader.style.display = "table-row";
    tableBody.innerHTML = "";

    try {
        await ensureSanctumSession();

        const token = document.querySelector('meta[name="admin-api-token"]')?.getAttribute("content");

        const response = await fetch("/api/admin/api/tenants", {
            method: "GET",
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${token}`,
            },
        });

        if (!response.ok) {
            // Backend error response
            const errorData = await response.json().catch(() => null);
            console.error("Backend error response: ", errorData);
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const applications = await response.json();
        loader.style.display = "none";

        if (!applications || applications.length === 0) {
            tableBody.innerHTML = `
                <tr><td colspan="9" class="text-center py-4 text-muted">No tenants found.</td></tr>`;
            return;
        }

        applications.forEach((app, i) => {
            const fullName = [app.first_name, app.middle_name, app.last_name].filter(Boolean).join(" ");
            const row = `
                <tr>
                    <td>${i + 1}</td>
                    <td>${fullName}</td>
                    <td>${app.email}</td>
                    <td>${app.contact_num}</td>
                    <td>${app.unit_id || "N/A"}</td>
                    <td>
                        <button class="btn btn-sm btn-light border-0 text-primary me-2" title="Edit">
                            <i class="bi bi-pencil-square fs-5"></i>
                        </button>
                        <button class="btn btn-sm btn-light border-0 text-danger" title="Archive">
                            <i class="bi bi-archive fs-5"></i>
                        </button>
                    </td>
                </tr>`;
            tableBody.insertAdjacentHTML("beforeend", row);
        });

    } catch (err) {
        console.error("Error loading tenants:", err);
        loader.innerHTML = `<td colspan="9" class="text-center text-danger py-3">Failed to load tenant data.</td>`;
    }
});
