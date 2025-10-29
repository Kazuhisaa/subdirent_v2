@extends('layouts.app')

@section('title', 'Available Units | SubdiRent')

@section('content')

{{-- Google Fonts: Kinuha mula sa home.blade.php --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Salsa&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/units.css') }}">

<section class="py-5" style="padding-top: 5rem !important;">
    <div class="container">
        <h2 class="section-heading animate-on-scroll">Available Units</h2>

        <div class="mb-4 animate-on-scroll" data-delay="1">
            <div class="search-filter-container">
                <input type="text" id="searchInput" class="form-control search-box" placeholder="Search Unit Name">

                <div class="filter-buttons mt-3">
                    <button class="filter-btn active" data-phase="all">All Phase</button>
                    <button class="filter-btn" data-phase="Phase I">Phase I</button>
                    <button class="filter-btn" data-phase="Phase II">Phase II</button>
                    <button class="filter-btn" data-phase="Phase III">Phase III</button>
                    <button class="filter-btn" data-phase="Phase IV">Phase IV</button>
                    <button class="filter-btn" data-phase="Phase V">Phase V</button>
                </div>
            </div>
        </div>
        
        <div class="properties-section">
            <div id="units-container" class="row g-4">
                <p class="text-muted text-center">Loading available units...</p>
            </div>
        </div>
    </div>
</section>


{{-- ============================================= --}}
{{-- ==== MODAL HTML (KINUHA SA FIRST CODE) ==== --}}
{{-- ============================================= --}}
{{-- In-update ko ang classes para sa styling --}}

{{-- ==== Reserve Unit Modal ==== --}}
<div id="reserveModal" class="custom-modal">
    <div class="custom-modal-header">
        Reserve Unit
        <span style="float: right; cursor: pointer;" onclick="closeModal('reserveModal')">&times;</span>
    </div>
    <div class="custom-modal-body">
        <form id="reserveForm">
            <input type="hidden" id="reserveUnitId" name="unit_id">
            
            <label for="reserveUnitName" class="form-label">Unit Name</label>
            <input type="text" id="reserveUnitName" name="unit_name" readonly class="form-control mb-3">

            <label for="first_name" class="form-label">First Name</label>
            <input type="text" id="first_name" name="first_name" required class="form-control mb-3">

            <label for="first_name" class="form-label">Middle Name</label>
            <input type="text" name="middle_name" class="form-control mb-3 >

            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" id="last_name" name="last_name" required class="form-control mb-3">

            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" required class="form-control mb-3">

            <label for="contact_num" class="form-label">Contact Number</label>
            <input type="text" id="contact_num" name="contact_num" required class="form-control mb-3">

            {{-- BINAGO MULA "move_in_date" PATUNGONG "date" --}}
            <label for="move_in_date" class="form-label">Preferred Date</label>
            <input type="date" id="move_in_date" name="date" required class="form-control mb-3"> 

            {{-- ITO YUNG MAHALAGANG DAGDAG --}}
            <label for="booking_time" class="form-label">Preferred Time</label>
            <input type="time" id="booking_time" name="booking_time" required class="form-control mb-3">
        </form>
    </div>
    <div class="custom-modal-footer">
        <button class="btn btn-details" type="submit" form="reserveForm">Submit Reservation</button>
        <button type="button" class="btn btn-outline-danger" onclick="closeModal('reserveModal')">Cancel</button>
    </div>
</div>

{{-- ==== Apply Now Modal ==== --}}
<div id="applyModal" class="custom-modal">
    <div class="custom-modal-header">
        Apply Now
        <span style="float: right; cursor: pointer;" onclick="closeModal('applyModal')">&times;</span>
    </div>
    <div class="custom-modal-body">
        <form id="applyForm">
            <input type="hidden" id="applyUnitId" name="unit_id">
            
            <label for="applyUnitName" class="form-label">Unit Name</label>
            <input type="text" id="applyUnitName" name="unit_name" readonly class="form-control mb-3">

            <label for="apply_first_name" class="form-label">First Name</label>
            <input type="text" id="apply_first_name" name="first_name" required class="form-control mb-3">

            <input type="text" name="middle_name" placeholder="Middle Name" required>

            <label for="apply_last_name" class="form-label">Last Name</label>
            <input type="text" id="apply_last_name" name="last_name" required class="form-control mb-3">

            <label for="apply_email" class="form-label">Email</label>
            <input type="email" id="apply_email" name="email" required class="form-control mb-3">

            <label for="apply_contact_num" class="form-label">Contact Number</label>
            <input type="text" id="apply_contact_num" name="contact_num" required class="form-control mb-3">

            <label for="message" class="form-label">Message / Remarks</label>
            <textarea id="message" name="remarks" rows="3" class="form-control mb-3"></textarea>
        </form>
    </div>
    <div class="custom-modal-footer">
        <button class="btn btn-details" type="submit" form="applyForm">Submit Application</button>
        <button type="button" class="btn btn-outline-danger" onclick="closeModal('applyModal')">Cancel</button>
    </div>
</div>

{{-- ==== Overlay (KINUHA SA FIRST CODE) ==== --}}
<div id="modalOverlay" class="modal-overlay" onclick="closeAllModals()"></div>


<script>
document.addEventListener("DOMContentLoaded", () => {
    
    // === SCRIPT PARA SA SCROLL ANIMATION (Mula sa Code 2) ===
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                const delay = entry.target.dataset.delay;
                if (delay) {
                    entry.target.style.transitionDelay = `${delay * 0.15}s`;
                }
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });

    const staticAnimatedElements = document.querySelectorAll('.animate-on-scroll');
    staticAnimatedElements.forEach((el) => observer.observe(el));

    // === Kinuha sa First Code (para sa form submission)
    const reserveForm = document.getElementById("reserveForm");
    const applyForm = document.getElementById("applyForm");

    // === Kinuha sa Second Code (para sa fetching at filtering)
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
            const code = unit.code || 'N/A';
            const floorArea = unit.floor_area ? `${unit.floor_area} sqm` : 'N/A';
            const bedrooms = unit.bedrooms || 'N/A';
            const bathrooms = unit.bathrooms || 'N/A';

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
                                {{-- ============================================= --}}
                                {{-- ==== BUTTONS (STYLE: Code 2, FUNC: Code 1) ==== --}}
                                {{-- ============================================= --}}
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
        
        // I-observe ulit 'yung mga bagong gawang cards (Mula sa Code 2)
        const dynamicAnimatedElements = container.querySelectorAll('.animate-on-scroll');
        dynamicAnimatedElements.forEach((el) => observer.observe(el));
    }

    // Search and Filter logic (Mula sa Code 2)
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
            const normalize = str => str?.toLowerCase().replace(/phase\s+/i, "").replace(/\bii\b/g, "2").replace(/\biii\b/g, "3").replace(/\biv\b/g, "4").replace(/\bv\b/g, "5").replace(/\bvi\b/g, "6").replace(/\bvii\b/g, "7").replace(/\bviii\b/g, "8").replace(/\bix\b/g, "9").replace(/\bx\b/g, "10").trim();
            const unitPhase = normalize(unit.location || "");
            const filterPhase = normalize(currentPhase);
            const matchesPhase = currentPhase === "all" || unitPhase === filterPhase || (unit.location || '').toLowerCase().includes(currentPhase.toLowerCase());
            return matchesSearch && matchesPhase;
        });
        renderUnits(filtered);
    }

    // =============================================
    // ==== MODAL CONTROLS (KINUHA SA FIRST CODE) ====
    // =============================================
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
        // Check if other modals are open before closing overlay
        const anyModalOpen = document.querySelector('.custom-modal.show');
        if (!anyModalOpen) {
            document.getElementById('modalOverlay').classList.remove('show');
        }
    }

    window.closeAllModals = function() {
        document.querySelectorAll('.custom-modal').forEach(m => m.classList.remove('show'));
        document.getElementById('modalOverlay').classList.remove('show');
    }

    // ---------------------------------------------
// ▼▼▼ PALITAN MO ITONG BUONG BLOCK... ▼▼▼
// ---------------------------------------------
reserveForm.addEventListener("submit", async e => {
    e.preventDefault();
    const formData = new FormData(reserveForm);
    
    // Kunin ang CSRF token (Mahalaga 'to!)
    const csrfToken = document.querySelector('meta[name="csrf-token"]') 
                        ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        : null;

    if (!csrfToken) {
        // Siguraduhin mong may <meta name="csrf-token" ...> sa <head> ng layout mo
        alert('❌ CSRF Token not found. Please refresh the page.');
        return;
    }

    try {
        const res = await fetch("/api/bookings", { 
            method: "POST", 
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken, // <-- Idinagdag ang token
                'Accept': 'application/json' // <-- Para alam ng Laravel na JSON ang sagot
            }
        });

        const data = await res.json(); // Kunin ang JSON response kahit error

        if (!res.ok) {
            console.error('Error Response:', data);
            
            let errorMessage = "❌ Failed to reserve unit.";
            
            // Kung validation error (422), ipakita kung anong field ang mali
            if (data.errors) {
                const errors = Object.values(data.errors).map(err => `• ${err[0]}`).join('\n');
                errorMessage += "\n\nPlease check the following:\n" + errors;
            } else if (data.message) {
                // Kung ibang server error (gaya ng SQL error)
                errorMessage = "❌ " + data.message;
            }
            
            alert(errorMessage); // Ipakita ang specific error
            return;
        }

        // --- Dito ay Success na ---
        alert("✅ Reservation successful!");
        reserveForm.reset();
        closeAllModals();

    } catch(err) {
        // Kung network error mismo
        console.error('Fetch Error:', err);
        alert("❌ A network or server error occurred. Please try again.");
    }
});

    // =======================================================
    // ==== ITO YUNG BINAGO KO (MAY CSRF TOKEN AT ERROR HANDLING) ====
    // =======================================================
    applyForm.addEventListener("submit", async e => {
    e.preventDefault();
    const formData = new FormData(applyForm);

    try {
        const res = await fetch("/applications", {
            method: "POST",
            body: formData,
            headers: {
                // ITO YUNG MAHALAGANG DAGDAG:
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json' // Para alam ng Laravel na JSON response ang inaasahan mo
            }
        });

        // Kunin yung JSON data kahit error man o hindi
        const data = await res.json();

        if (!res.ok) {
            console.error('Error Response:', data);
            
            // Gumawa ng mas malinaw na error message
            let errorMessage = "❌ Failed to submit application.";
            
            // Kung validation error (422), ipakita yung mga error
            if (data.errors) {
                // Kunin lahat ng error messages at pagsamahin
                const errors = Object.values(data.errors).map(err => `• ${err[0]}`).join('\n');
                errorMessage += "\n\nPlease check the following:\n" + errors;
            } else if (data.message) {
                // Kung ibang error na may message
                errorMessage = "❌ " + data.message;
            }
            
            alert(errorMessage);
            return; // Itigil dito, huwag i-redirect
        }

        // Kung naging OKAY (res.ok === true)
        alert("✅ Application submitted successfully!");
        applyForm.reset();
        closeAllModals();

        // Ito na yung redirect na gusto mong mangyari
        window.location.href = "/admin/applications";

    } catch (err) {
        console.error('Fetch Error:', err);
        alert("❌ A network or server error occurred. Please try again.");
    }
});

});
</script>

@endsection