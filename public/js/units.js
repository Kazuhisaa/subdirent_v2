document.addEventListener("DOMContentLoaded", () => {

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                const delay = entry.target.dataset.delay;
                if (delay) entry.target.style.transitionDelay = `${delay * 0.15}s`;
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    const staticAnimatedElements = document.querySelectorAll('.animate-on-scroll');
    staticAnimatedElements.forEach((el) => observer.observe(el));

    const reserveForm = document.getElementById("reserveForm");
    const applyForm = document.getElementById("applyForm");
    const container = document.getElementById("units-container");
    const searchInput = document.getElementById("searchInput");
    const filterButtons = document.querySelectorAll(".filter-btn");

    let allUnits = [];
    let currentPhase = "all";

    fetch("/units")
        .then(res => res.json())
        .then(units => {
            allUnits = units;
            renderUnits(allUnits);
        })
        .catch(err => {
            console.error("Error fetching units:", err);
            container.innerHTML = `<p class="text-danger text-center">Failed to load units.</p>`;
        });

    function renderUnits(units) {
        container.innerHTML = "";

        if (!units.length) {
            container.innerHTML = `<p class="text-muted text-center">No available units found.</p>`;
            return;
        }

        units.forEach((unit, index) => {
            const imageUrl = unit.files?.length ? `/${unit.files[0]}` : '/uploads/default-room.jpg';
            const unitPrice = unit.price ? unit.price.toLocaleString() : (unit.monthly_rent ? unit.monthly_rent.toLocaleString() : 'N/A');
            const code = unit.unit_code || 'N/A';
            const floorArea = unit.floor_area ? `${unit.floor_area} sqm` : 'N/A';
            const bedrooms = unit.bedroom || 'N/A';
            const bathrooms = unit.bathroom || 'N/A';
            container.innerHTML += `
                <div class="col-lg-4 col-md-6 animate-on-scroll" data-delay="${index + 1}">
                    <div class="property-card">
                        <img src="${imageUrl}" alt="${unit.title || 'Property Image'}">
                        <div class="info text-start">
                            <h6>${unit.title || 'Untitled Property'}</h6>
                            <p class="location mb-3">
                                <i class="fas fa-map-marker-alt me-1"></i>${unit.location || 'Location not specified'}
                            </p>
                            <ul class="property-details-list">
                                <li><strong>Code:</strong> ${code}</li>
                                <li><strong>Floor Area:</strong> ${floorArea}</li>
                                <li><strong>Bedroom:</strong> ${bedrooms}</li>
                                <li><strong>Bathroom:</strong> ${bathrooms}</li>
                            </ul>
                            <p class="unit-price">Unit Price: ₱${unitPrice}</p>
                            <div class="d-flex gap-2 mt-auto">
                                <button class="btn btn-details flex-fill" onclick="openModal('reserveModal','${unit.id}','${unit.title}')">
                                    <i class="fas fa-calendar-check me-1"></i> Reserve
                                </button>
                                <button class="btn btn-outline-details flex-fill" onclick="openModal('applyModal','${unit.id}','${unit.title}')">
                                    <i class="fas fa-file-alt me-1"></i> Apply
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
        });

        const dynamicAnimatedElements = container.querySelectorAll('.animate-on-scroll');
        dynamicAnimatedElements.forEach((el) => observer.observe(el));
    }

    searchInput.addEventListener("input", applyFilters);
    filterButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            filterButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            currentPhase = btn.dataset.phase;
            applyFilters();
        });
    });

    function applyFilters() {
        const term = searchInput.value.toLowerCase();
        const filtered = allUnits.filter(unit => {
            const matchesSearch = (unit.title || '').toLowerCase().includes(term);
            const normalize = str => str?.toLowerCase().replace(/phase\s+/i, "").replace(/\bi\b/g, "1").replace(/\bii\b/g, "2").replace(/\biii\b/g, "3").replace(/\biv\b/g, "4").replace(/\bv\b/g, "5").trim();
            const unitPhase = normalize(unit.location || "");
            const filterPhase = normalize(currentPhase);
            const matchesPhase = currentPhase === "all" || unitPhase === filterPhase || (unit.location || '').toLowerCase().includes(currentPhase.toLowerCase());
            return matchesSearch && matchesPhase;
        });
        renderUnits(filtered);
    }

    window.openModal = function(id, unitId = null, unitName = '') {
        document.getElementById('modalOverlay').classList.add('show');
        const modal = document.getElementById(id);
        modal.classList.add('show');

        if (id === 'reserveModal') {
            document.getElementById('reserveUnitId').value = unitId;
            document.getElementById('reserveUnitName').value = unitName;
        }
        if (id === 'applyModal') {
            document.getElementById('applyUnitId').value = unitId;
            document.getElementById('applyUnitName').value = unitName;
        }
    }

    window.closeModal = function(id) {
        document.getElementById(id).classList.remove('show');
        if (!document.querySelector('.custom-modal.show')) {
            document.getElementById('modalOverlay').classList.remove('show');
        }
    }

    window.closeAllModals = function() {
        document.querySelectorAll('.custom-modal').forEach(m => m.classList.remove('show'));
        document.getElementById('modalOverlay').classList.remove('show');
    }

    reserveForm.addEventListener("submit", async e => {
        e.preventDefault();
        const formData = new FormData(reserveForm);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const res = await fetch("/api/bookings", {
                method: "POST",
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (!res.ok) {
                let errorMessage = "❌ Failed to reserve unit.";
                if (data.errors) {
                    const errors = Object.values(data.errors).map(err => `• ${err[0]}`).join('\n');
                    errorMessage += "\n\n" + errors;
                }
                alert(errorMessage);
                return;
            }

            alert("✅ Reservation successful!");
            reserveForm.reset();
            closeAllModals();

        } catch (err) {
            console.error('Fetch Error:', err);
            alert("❌ Network or server error. Please try again.");
        }
    });

    applyForm.addEventListener("submit", async e => {
        e.preventDefault();
        const formData = new FormData(applyForm);

        try {
            const res = await fetch("/applications", {
                method: "POST",
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (!res.ok) {
                let errorMessage = "❌ Failed to submit application.";
                if (data.errors) {
                    const errors = Object.values(data.errors).map(err => `• ${err[0]}`).join('\n');
                    errorMessage += "\n\n" + errors;
                }
                alert(errorMessage);
                return;
            }

            alert("✅ Application submitted successfully!");
            applyForm.reset();
            closeAllModals();
            window.location.href = "/admin/applications";

        } catch (err) {
            console.error('Fetch Error:', err);
            alert("❌ Network or server error. Please try again.");
        }
    });

});
