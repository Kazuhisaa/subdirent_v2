// ----> BARONG DAGDAG: Helper function para i-encode ang data <----
// Para maiwasan ang error kung may single quote (') sa data ng unit mo
function escapeHTML(str) {
    // Check muna kung string talaga
    if (typeof str !== 'string') {
        // Kung hindi (e.g., null, undefined, number), ibalik lang
        return str;
    }
    // Pinalitan ko ng mas simpleng replace
    return str.replace(/'/g, '&apos;').replace(/"/g, '&quot;');
}


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
            const imageUrl = unit.files?.length && unit.files[0]
                ? unit.files[0].replace(/^uploads\/?/, 'uploads/') // keep relative to public
                : 'uploads/units/default.jpg';

            // console.log(unit.files, imageUrl); // Pwede mo 'to i-comment out kung 'di na kailangan

            const unitPrice = unit.price ? Number(unit.price).toLocaleString() : (unit.unit_price ? Number(unit.unit_price).toLocaleString() : 'N/A');
            const code = unit.unit_code || 'N/A';
            const floorArea = unit.floor_area ? `${unit.floor_area} sqm` : 'N/A';
            const bedrooms = unit.bedroom || 'N/A';
            const bathrooms = unit.bathroom || 'N/A';

            // ----> BARONG DAGDAG: I-convert ang unit data para sa data-attribute <----
            // Gagamitin natin ang helper function para safe i-encode
            const unitDataString = escapeHTML(JSON.stringify(unit));

            container.innerHTML += `
                <div class="col-lg-4 col-md-6 animate-on-scroll" data-delay="${index + 1}">
                    
            
                    <div class="property-card property-card-clickable" data-unit-details='${unitDataString}'>
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
            // Inayos ko 'yung logic dito para mas tama ang pag-filter sa phase
            const matchesPhase = currentPhase === "all" || unitPhase.includes(filterPhase);
            return matchesSearch && matchesPhase;
        });
        renderUnits(filtered);
    }


    // ----> BARONG DAGDAG: Event listener para sa "View Details" <----
    // Gagamit tayo ng 'event delegation' sa 'container'
    container.addEventListener('click', function(event) {
        
        // 1. Hanapin ang card na pinindot
        const card = event.target.closest('.property-card-clickable');

        // 2. Kung hindi card (o nasa labas ng card), huwag ituloy
        if (!card) {
            return;
        }

        // 3. (MAHALAGA) Tignan kung button ang pinindot
        if (event.target.closest('.btn-details') || event.target.closest('.btn-outline-details')) {
            // Button ito (Reserve or Apply), kaya hayaan ang inline 'onclick' na gumana
            return; 
        }

        // 4. Kung umabot dito, card ang pinindot (at hindi button)
        // Kunin ang data na tinago natin
        try {
            // Kailangan nating i-decode 'yung quotes na in-escape natin kanina
            const decodedString = card.dataset.unitDetails
                                     .replace(/&apos;/g, "'")
                                     .replace(/&quot;/g, '"');
            const unitData = JSON.parse(decodedString);
            
            // 5. Kunin ulit 'yung details gamit ang data
            const imageUrl = unitData.files?.length && unitData.files[0]
                ? unitData.files[0].replace(/^uploads\/?/, 'uploads/')
                : 'uploads/units/default.jpg';
            
            const unitPrice = unitData.price ? Number(unitData.price).toLocaleString() : (unitData.unit_price ? Number(unitData.unit_price).toLocaleString() : 'N/A');
            const code = unitData.unit_code || 'N/A';
            const floorArea = unitData.floor_area ? `${unitData.floor_area} sqm` : 'N/A';
            const bedrooms = unitData.bedroom || 'N/A';
            const bathrooms = unitData.bathroom || 'N/A';

            // 6. Ilagay ang data sa "View Details" modal
            // (Ito 'yung mga ID galing sa HTML na binigay ko sa'yo kanina)
            document.getElementById('modalUnitNameHeader').textContent = unitData.title || 'Unit Details';
            document.getElementById('modalUnitImage').src = imageUrl;
            document.getElementById('modalUnitPhase').textContent = unitData.location || 'N/A'; // Ginamit ko 'location' bilang Phase
            document.getElementById('modalUnitCode').textContent = code;
            document.getElementById('modalUnitFloorArea').textContent = floorArea;
            document.getElementById('modalUnitBedroom').textContent = bedrooms;
            document.getElementById('modalUnitBathroom').textContent = bathrooms;
            document.getElementById('modalUnitPrice').textContent = `₱${unitPrice}`;
            document.getElementById('modalUnitDescription').textContent = unitData.description || 'No description available.';

            // 7. Buksan ang "View Details" modal
            openModal('viewDetailsModal'); 

        } catch (e) {
            console.error('Error parsing unit data:', e, card.dataset.unitDetails);
            alert('Could not load unit details.');
        }
    });
    // ----> WAKAS NG BARONG DAGDAG <----


    window.openModal = function (id, unitId = null, unitName = '', event = null) {
    const overlay = document.getElementById('modalOverlay');
    const modal = document.getElementById(id);

    overlay.classList.add('show');
    modal.classList.add('show');

    // Optional: position the modal near where the button was clicked
    if (event && event.target) {
        const rect = event.target.getBoundingClientRect();
        const modalHeight = modal.offsetHeight || 300;
        const viewportHeight = window.innerHeight;

        let topPosition = rect.bottom + window.scrollY + 20;

        // If not enough space below, position above
        if (topPosition + modalHeight > window.scrollY + viewportHeight) {
            topPosition = rect.top + window.scrollY - modalHeight - 20;
        }

        modal.style.top = `${topPosition}px`;
        modal.style.left = `50%`;
        modal.style.transform = 'translateX(-50%)';
    } else {
        // fallback center if no event provided
        modal.style.top = `${window.scrollY + window.innerHeight / 2 - modal.offsetHeight / 2}px`;
    }

    // Fill in form data
    if (id === 'reserveModal') {
        document.getElementById('reserveUnitId').value = unitId;
        document.getElementById('reserveUnitName').value = unitName;
    } else if (id === 'applyModal') {
        document.getElementById('applyUnitId').value = unitId;
        document.getElementById('applyUnitName').value = unitName;
    }
};

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
                // --- GINAWANG SWEETALERT ---
                let errorTitle = '<b style="color: red;">Failed to reserve unit.</b>';
                let errorDetails = '';
                
                
if (data.errors) {
                // Tinanggal ang bullet (•) at ginawang red ang text
                errorDetails = Object.values(data.errors)
                                   .map(err => `<p style="color: #D32F2F; margin: 0; font-weight: 500;">${err[0]}</p>`)
                                   .join('');
            }
                Swal.fire({
                    icon: 'error',
                    title: errorTitle,
                    html: errorDetails,
                    background: 'transparent',
                    showConfirmButton: true,
                    confirmButtonText: '<b style="color: #D32F2F; " >OK</b>',
                     allowOutsideClick: false,
                    customClass: {
                        popup: 'swal2-no-backdrop'
                    }
                });
                return;
            }

            Swal.fire({
    icon: 'success',
    title: '<b>Success!</b>',
    text: 'Reservation Successful',
    background: 'transparent',
    showConfirmButton: true,
    confirmButtonText: '<b>OK</b>',
    allowOutsideClick: false,
    customClass: {
        popup: 'swal2-no-backdrop'
    }
});
            reserveForm.reset();
            closeAllModals();

        } catch (err) {
            console.error('Fetch Error:', err);
            // Inayos ko na rin 'to
            Swal.fire({
                icon: 'error',
                title: '<b>Network Error</b>',
                text: 'Please try again later.',
                background: 'transparent',
                showConfirmButton: true,
                confirmButtonText: '<b>OK</b>',
                allowOutsideClick: false,
                customClass: {
                    popup: 'swal2-no-backdrop'
                }
            });
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
                // --- GINAWANG SWEETALERT ---
                let errorTitle = '<b style="color: red;">Failed to reserve unit.</b>';
                let errorDetails = '';
                
                
if (data.errors) {
                // Tinanggal ang bullet (•) at ginawang red ang text
                errorDetails = Object.values(data.errors)
                                   .map(err => `<p style="color: #D32F2F; margin: 0; font-weight: 500;">${err[0]}</p>`)
                                   .join('');
            }
                Swal.fire({
                    icon: 'error',
                    title: errorTitle,
                    html: errorDetails,
                    background: 'transparent',
                    showConfirmButton: true,
                    confirmButtonText: '<b style="color: #D32F2F; " >OK</b>',
                     allowOutsideClick: false,
                    customClass: {
                        popup: 'swal2-no-backdrop'
                    }
                });
                return;
            }

            Swal.fire({
    icon: 'success',
    title: '<b>Success!</b>',
    text: 'Application Successful',
    background: 'transparent',
    showConfirmButton: true,
    confirmButtonText: '<b>OK</b>',
    allowOutsideClick: false,
    customClass: {
        popup: 'swal2-no-backdrop'
    }
});

        } catch (err) {
            console.error('Fetch Error:', err);
            alert("❌ Network or server error. Please try again.");
        }
    });

});