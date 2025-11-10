document.addEventListener('DOMContentLoaded', async () => {
    const grid = document.getElementById('unitsGrid');
    const searchInput = document.getElementById('unitSearch');
    const token = sessionStorage.getItem('admin_api_token');

    if (!token) {
        grid.innerHTML = `<div class="col-12 text-danger text-center">⚠ Unauthorized — please login first.</div>`;
        return;
    }

    let allUnits = [];

    const renderUnits = (units) => {
        grid.innerHTML = '';

        if (!units.length) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <img src="/images/empty-state.svg" alt="No data" width="120" class="mb-3">
                    <h6 class="text-blue-800">No available units found.</h6>
                </div>`;
            return;
        }

        units.forEach(unit => {
            const cleanPrice = Number((unit.unit_price || '0').toString().replace(/[^0-9.]/g, ''));

            // --- 1. Get all image paths ---
            let allImages = [];
            if (unit.files && Array.isArray(unit.files) && unit.files.length > 0) {
                allImages = unit.files.map(file =>
                    file.startsWith('http') ? file : `/${file.replace(/\\/g, '/')}`
                );
            } else {
                allImages = ['/uploads/units/default.jpg']; // Default if empty
            }

            // --- 2. Build the carousel items (the images) ---
            const carouselItemsHTML = allImages.map((imagePath, index) => {
                const activeClass = index === 0 ? 'active' : '';
                return `
                    <div class="carousel-item ${activeClass}">
                        <img src="${imagePath}"
                             class="d-block w-100"
                             alt="${unit.title || 'Unit Photo'} ${index + 1}"
                             style="height: 200px; object-fit: cover;">
                    </div>
                `;
            }).join('');

            // --- 3. Build the carousel controls (prev/next buttons) ---
            const carouselId = `unitCarousel-${unit.id}`; // Create a UNIQUE ID for each carousel
            let carouselControlsHTML = '';

            if (allImages.length > 1) { // Only show controls if there's more than 1 image
                carouselControlsHTML = `
                    <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                `;
            }

            // --- 4. Build the final card HTML ---
            const card = `
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 room-card">

                        <div id="${carouselId}" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner">
                                ${carouselItemsHTML}
                            </div>
                            ${carouselControlsHTML}
                        </div>

                        <div class="card-body">
                            <span class="badge bg-success mb-2">
                                Available
                            </span>

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
                                <p class="fw-semibold text-blue-800 mb-0">
                                    <strong>Unit Price:</strong> ₱${cleanPrice.toLocaleString()}
                                </p>
                            </div>

                            <div class="text-end">
                                <button class="btn btn-outline-primary btn-sm edit-unit-btn" data-id="${unit.id}">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;

            grid.insertAdjacentHTML('beforeend', card);
        });
    };

    // --- FETCH and SEARCH LOGIC (No changes needed) ---
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
        allUnits = (data.data ?? data).filter(unit =>
            unit.status && unit.status.toLowerCase() === 'available'
        );

        renderUnits(allUnits);
    } catch (err) {
        console.error('Error loading units:', err);
        grid.innerHTML = `<div class="col-12 text-danger text-center">⚠ Error loading units.</div>`;
    }

    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase().trim();
        const filtered = allUnits.filter(unit => (
            (unit.title && unit.title.toLowerCase().includes(searchTerm)) ||
            (unit.unit_code && unit.unit_code.toLowerCase().includes(searchTerm)) ||
            (unit.location && unit.location.toLowerCase().includes(searchTerm))
        ));
        renderUnits(filtered);
    });

    // --- EDIT BUTTON LOGIC (No changes needed) ---
    document.addEventListener('click', (e) => {
        if (e.target.closest('.edit-unit-btn')) {
            const id = e.target.closest('.edit-unit-btn').dataset.id;
            window.location.href = `/admin/edit-unit/${id}`;
        }
    });

    // --- NO MODAL CLICK HANDLER IS NEEDED ---
});