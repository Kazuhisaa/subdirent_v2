document.addEventListener('DOMContentLoaded', async () => {
    const grid = document.getElementById('unitsGrid');
    const searchInput = document.getElementById('unitSearch');
    const token = sessionStorage.getItem('admin_api_token');

    if (!token) {
        grid.innerHTML = `<div class="col-12 text-danger text-center">⚠ Unauthorized — please login first.</div>`;
        return;
    }

    let allUnits = [];

    // 🟢 Function to render unit cards
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
   const imagePath = (unit.files && unit.files.length > 0)
    ? unit.files[0].startsWith('http') 
        ? unit.files[0] 
        : `/${unit.files[0].replace(/\\/g, '/')}` // siguraduhin forward slash
    : '/uploads/units/default.jpg';


            const cleanPrice = Number((unit.unit_price || '0').toString().replace(/[^0-9.]/g, ''));

            const card = `
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 room-card">
                        
                        <div class="position-relative" style="height: 200px; background: #f5f9ff;">
                            <img src="${imagePath}" 
                                 alt="${unit.title}" 
                                 class="w-100 h-100 clickable-image"
                                 style="object-fit: cover;" 
                                 data-full="${imagePath}">
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
    
    // ... (rest of your file is identical) ...

    // 🟢 Fetch all available units
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

    // 🟢 Search filter
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase().trim();
        const filtered = allUnits.filter(unit => (
            (unit.title && unit.title.toLowerCase().includes(searchTerm)) ||
            (unit.unit_code && unit.unit_code.toLowerCase().includes(searchTerm)) ||
            (unit.location && unit.location.toLowerCase().includes(searchTerm))
        ));
        renderUnits(filtered);
    });

    // 🟢 Edit button
    document.addEventListener('click', (e) => {
        if (e.target.closest('.edit-unit-btn')) {
            const id = e.target.closest('.edit-unit-btn').dataset.id;
            window.location.href = `/admin/edit-unit/${id}`;
        }
    });

    // 🟢 Image click viewer (modal)
    document.addEventListener('click', (e) => {
        const img = e.target.closest('.clickable-image');
        if (img) {
            const fullImg = document.getElementById('imageModalImg');
            fullImg.src = img.dataset.full;
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        }
    });
});