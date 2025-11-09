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

function cssVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}

//  Brand palette from your :root
const COLOR_PRIMARY_DARK = cssVar("--blue-900") || "#1e3a8a"; // Added fallback
const COLOR_PRIMARY = cssVar("--blue-600") || "#2563eb"; // Added fallback
const COLOR_PRIMARY_LIGHT = cssVar("--blue-400") || "#60a5fa"; // Added fallback
const COLOR_SUCCESS = "#28a745";
const COLOR_WARNING = "#ffc107";
const COLOR_ERROR = "#dc3545";
const COLOR_INFO = "#17a2b8";

//  Base SweetAlert2 styling (Mula sa Code 1)
const swalBaseConfig = {
    background: "#ffffff",
    color: COLOR_PRIMARY_DARK,
    confirmButtonColor: COLOR_PRIMARY,
    customClass: {
        popup: "rounded-4 shadow-lg border-0",
        title: "fw-semibold text-blue-800",
        confirmButton: "rounded-pill px-4 py-2 fw-semibold",
        cancelButton: "rounded-pill px-4 py-2 fw-semibold",
    },
    buttonsStyling: false,
    showClass: {
        popup: "animate__animated animate__fadeInDown",
    },
    hideClass: {
        popup: "animate__animated animate__fadeOutUp",
    },
    confirmButtonText: "OK", // Dinagdag ko 'to para "OK" ang text
};

//  Success Alert
function showSuccess(message, title = "Success!") {
    Swal.fire({
        ...swalBaseConfig,
        icon: "success",
        title,
        text: message,
        confirmButtonColor: COLOR_PRIMARY, // Ito 'yung solid blue button
        iconColor: COLOR_PRIMARY_LIGHT, // Ito 'yung light blue icon galing sa image
    });
}

//  Error Alert
function showError(message, title = "Error!") {
    Swal.fire({
        ...swalBaseConfig,
        icon: "error",
        title,
        text: message, // Dito natin ilalagay 'yung listahan ng errors
        confirmButtonColor: COLOR_ERROR,
        iconColor: COLOR_ERROR,
    });
}

//  Warning Alert
function showWarning(message, title = "Warning!") {
    Swal.fire({
        ...swalBaseConfig,
        icon: "warning",
        title,
        text: message,
        confirmButtonColor: COLOR_WARNING,
        iconColor: COLOR_WARNING,
    });
}

//  Info Alert
function showInfo(message, title = "Notice") {
    Swal.fire({
        ...swalBaseConfig,
        icon: "info",
        title,
        text: message,
        confirmButtonColor: COLOR_INFO,
        iconColor: COLOR_INFO,
    });
}

//  Confirmation Dialog
function confirmAction(
    message = "Are you sure?",
    confirmText = "Yes, continue",
    cancelText = "Cancel",
    onConfirm = null
) {
    Swal.fire({
        ...swalBaseConfig,
        title: "Confirm Action",
        text: message,
        icon: "question",
        iconColor: COLOR_PRIMARY_LIGHT,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        confirmButtonColor: COLOR_PRIMARY,
        cancelButtonColor: COLOR_PRIMARY_DARK,
    }).then((result) => {
        if (result.isConfirmed && typeof onConfirm === "function") {
            onConfirm();
        }
    });
}

//  Expose to global scope
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;
window.confirmAction = confirmAction;

document.addEventListener("DOMContentLoaded", () => {

    const moveInDateInput = document.getElementById('move_in_date');
    if (moveInDateInput) {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0'); // month ay 0-indexed
        const dd = String(today.getDate()).padStart(2, '0');

        const minDate = `${yyyy}-${mm}-${dd}`;
        moveInDateInput.min = minDate;
    }

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

            const unitPrice = unit.price ? Number(unit.price).toLocaleString() : (unit.unit_price ? Number(unit.unit_price).toLocaleString() : 'N/A');
            const code = unit.unit_code || 'N/A';
            const floorArea = unit.floor_area ? `${unit.floor_area} sqm` : 'N/A';
            const bedrooms = unit.bedroom || 'N/A';
            const bathrooms = unit.bathroom || 'N/A';
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
            const matchesPhase = currentPhase === "all" || unitPhase.includes(filterPhase);
            return matchesSearch && matchesPhase;
        });
        renderUnits(filtered);
    }

    container.addEventListener('click', function (event) {
        const card = event.target.closest('.property-card-clickable');
        if (!card) return;
        if (event.target.closest('.btn-details') || event.target.closest('.btn-outline-details')) return;

        try {
            const decodedString = card.dataset.unitDetails
                .replace(/&apos;/g, "'")
                .replace(/&quot;/g, '"');
            const unitData = JSON.parse(decodedString);
            
            const imageUrl = unitData.files?.length && unitData.files[0]
                ? unitData.files[0].replace(/^uploads\/?/, 'uploads/')
                : 'uploads/units/default.jpg';
            
            const unitPrice = unitData.price ? Number(unitData.price).toLocaleString() : (unitData.unit_price ? Number(unitData.unit_price).toLocaleString() : 'N/A');
            const code = unitData.unit_code || 'N/A';
            const floorArea = unitData.floor_area ? `${unitData.floor_area} sqm` : 'N/A';
            const bedrooms = unitData.bedroom || 'N/A';
            const bathrooms = unitData.bathroom || 'N/A';

            document.getElementById('modalUnitNameHeader').textContent = unitData.title || 'Unit Details';
            document.getElementById('modalUnitImage').src = imageUrl;
            document.getElementById('modalUnitPhase').textContent = unitData.location || 'N/A';
            document.getElementById('modalUnitCode').textContent = code;
            document.getElementById('modalUnitFloorArea').textContent = floorArea;
            document.getElementById('modalUnitBedroom').textContent = bedrooms;
            document.getElementById('modalUnitBathroom').textContent = bathrooms;
            document.getElementById('modalUnitPrice').textContent = `₱${unitPrice}`;
            document.getElementById('modalUnitDescription').textContent = unitData.description || 'No description available.';

            openModal('viewDetailsModal');
        } catch (e) {
            console.error('Error parsing unit data:', e, card.dataset.unitDetails);
            // === INAYOS KO RIN ITO: Pinalitan ang alert() ===
            showError('Could not load unit details.', 'Error');
        }
    });

    window.openModal = function (id, unitId = null, unitName = '', event = null) {
        const overlay = document.getElementById('modalOverlay');
        const modal = document.getElementById(id);
        overlay.classList.add('show');
        modal.classList.add('show');

        if (event && event.target) {
            const rect = event.target.getBoundingClientRect();
            const modalHeight = modal.offsetHeight || 300;
            const viewportHeight = window.innerHeight;
            let topPosition = rect.bottom + window.scrollY + 20;
            if (topPosition + modalHeight > window.scrollY + viewportHeight) {
                topPosition = rect.top + window.scrollY - modalHeight - 20;
            }
            modal.style.top = `${topPosition}px`;
            modal.style.left = `50%`;
            modal.style.transform = 'translateX(-50%)';
        } else {
            modal.style.top = `${window.scrollY + window.innerHeight / 2 - modal.offsetHeight / 2}px`;
        }

        if (id === 'reserveModal') {
            document.getElementById('reserveUnitId').value = unitId;
            document.getElementById('reserveUnitName').value = unitName;
        } else if (id === 'applyModal') {
            document.getElementById('applyUnitId').value = unitId;
            document.getElementById('applyUnitName').value = unitName;
        }
    };

    window.closeModal = function (id) {
        document.getElementById(id).classList.remove('show');
        if (!document.querySelector('.custom-modal.show')) {
            document.getElementById('modalOverlay').classList.remove('show');
        }
    }

    window.closeAllModals = function () {
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
                // === INAYOS: Ginamit na ang showError ===
                let errorMessages = 'Please check your inputs.';
                if (data.errors) {
                    // Ginawang plain text na may newlines para sa 'text' property
                    errorMessages = Object.values(data.errors)
                        .map(err => err[0])
                        .join('\n');
                }
                showError(errorMessages, "Failed to reserve unit.");
                // === END NG AYOS ===
                return;
            }

            // === INAYOS: Ginamit na ang showSuccess ===
            showSuccess('Reservation Successful');
            // === END NG AYOS ===

            reserveForm.reset();
            closeAllModals();

        } catch (err) {
            console.error('Fetch Error:', err);
            // === INAYOS: Ginamit na ang showError ===
            showError('Please try again later.', 'Network Error');
            // === END NG AYOS ===
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
                // === INAYOS: Ginamit na ang showError ===
                let errorMessages = 'Please check your inputs.';
                if (data.errors) {
                    errorMessages = Object.values(data.errors)
                        .map(err => err[0])
                        .join('\n');
                }
                showError(errorMessages, "Failed to submit application.");
                // === END NG AYOS ===
                return;
            }

            // === INAYOS: Ginamit na ang showSuccess ===
            showSuccess('Application Successful');
            // === END NG AYOS ===

        } catch (err) {
            console.error('Fetch Error:', err);
            // === INAYOS: Ginamit na ang showError ===
            showError('Network or server error. Please try again.', 'Error');
            // === END NG AYOS ===
        }
    });

});