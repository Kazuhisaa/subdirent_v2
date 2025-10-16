document.addEventListener('DOMContentLoaded', async () => {
    const grid = document.getElementById('unitsGrid');
    const token = sessionStorage.getItem('admin_api_token');

    if (!token) {
        grid.innerHTML = `<div class="col-12 text-danger text-center">⚠ Unauthorized — please login first.</div>`;
        return;
    }

    try {
        const res = await fetch('/api/allUnits', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();

        // Filter only available units
        const availableUnits = data.filter(unit =>
            unit.status && unit.status.toLowerCase() === 'available'
        );

        grid.innerHTML = '';

        if (availableUnits.length === 0) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <img src="/images/empty-state.svg" alt="No data" width="120" class="mb-3">
                    <h6 class="text-blue-800">No available units found.</h6>
                </div>`;
            return;
        }

        availableUnits.forEach(unit => {
            const imagePath = (unit.files && unit.files.length > 0)
                ? `/uploads/units/${unit.files[0].split('/').pop()}`
                : `/images/no-image.png`;

            const card = `
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 room-card">
                        <div class="position-relative" style="height: 200px; background: #f5f9ff;">
                            <img src="${imagePath}" 
                                 alt="${unit.title}" 
                                 class="w-100 h-100 object-fit-cover">
                            <span class="badge position-absolute top-2 end-2 px-3 py-2 bg-success">
                                Available
                            </span>
                        </div>

                        <div class="card-body">
                            <h5 class="fw-bold text-blue-900 mb-1">${unit.title ?? 'Untitled'}</h5>
                            <p class="text-blue-700 small mb-2">
                                <i class="bi bi-geo-alt-fill"></i> ${unit.location ?? 'Unknown'}
                            </p>

                            <div class="small text-blue-800 lh-sm mb-3">
                                <p class="mb-1"><strong>Code:</strong> ${unit.unit_code ?? '-'}</p>
                                <p class="mb-1"><strong>Floor Area:</strong> ${unit.floor_area ?? 'N/A'} sqm</p>
                                <p class="mb-1"><strong>Bedroom:</strong> ${unit.bedroom ?? 'N/A'}</p>
                                <p class="mb-1"><strong>Bathroom:</strong> ${unit.bathroom ?? 'N/A'}</p>
                            </div>

                            <div class="border-top pt-2">
                                <p class="fw-bold text-blue-900 mb-1">
                                    Rent: ₱${Number(unit.monthly_rent || 0).toLocaleString()}
                                </p>
                                <p class="fw-semibold text-blue-800 mb-0">
                                    Price: ₱${Number(unit.unit_price || 0).toLocaleString()}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>`;
            grid.insertAdjacentHTML('beforeend', card);
        });
    } catch (err) {
        console.error('Error loading units:', err);
        grid.innerHTML = `<div class="col-12 text-danger text-center">⚠ Error loading units.</div>`;
    }
});
