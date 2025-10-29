    document.addEventListener("DOMContentLoaded", () => {

        // =============================================
        // === BAGONG SCRIPT PARA SA SCROLL ANIMATION ===
        // =============================================
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    
                    // Kukunin yung data-delay (kung meron) para sa staggered effect
                    const delay = entry.target.dataset.delay;
                    if (delay) {
                        entry.target.style.transitionDelay = `${delay * 0.15}s`;
                    }
                    
                    observer.unobserve(entry.target); // Para isang beses lang mag-animate
                }
            });
        }, {
            threshold: 0.1 // 10% ng item ay dapat makita bago mag-animate
        });

        // Kunin lahat ng static elements na may class na .animate-on-scroll
        const staticAnimatedElements = document.querySelectorAll('.animate-on-scroll');
        staticAnimatedElements.forEach((el) => observer.observe(el));
        // =============================================
        

        const container = document.getElementById("featured-container");

        fetch("/units") 
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
    })
    .then(units => {
        let allUnits = units;
        showRandomFeatured(allUnits); // unang render

        // ⏱️ Every 5 seconds magpapalit
        setInterval(() => {
            showRandomFeatured(allUnits);
        }, 7000);
    })
    .catch(err => {
        console.error("Error loading featured units:", err);
        container.innerHTML = `<p class="text-danger text-center">Failed to load featured units.</p>`;
    });

function showRandomFeatured(units) {
    // 🌀 Shuffle units (Fisher–Yates)
    const shuffled = [...units].sort(() => 0.5 - Math.random());
    const featured = shuffled.slice(0, 3); // 3 random units
    renderFeatured(featured);
}

        function renderFeatured(units) {
    container.style.opacity = 0; // fade out bago magpalit
    setTimeout(() => {
        container.innerHTML = ""; 

        if (!units.length) {
            container.innerHTML = `<p class="text-muted text-center">No featured units available at the moment.</p>`;
            container.style.opacity = 1;
            return;
        }

        units.forEach((unit, index) => {
            const imageUrl = unit.files?.length 
                ? `/${unit.files[0]}` 
                : 'https://via.placeholder.com/300x220.png?text=No+Image';

            const monthlyRent = unit.monthly_rent 
                ? `₱${parseFloat(unit.monthly_rent).toLocaleString()}` 
                : 'Price not available';

            const floorArea = unit.floor_area ? `${unit.floor_area} sqm` : 'N/A';
            const bedrooms = unit.bedroom ? unit.bedroom : 'N/A';
            const bathrooms = unit.bathroom ? unit.bathroom : 'N/A';

            container.innerHTML += `
                <div class="col-lg-4 col-md-6 animate-on-scroll" data-delay="${index + 1}">
                    <div class="property-card">
                        <img src="${imageUrl}" alt="${unit.title || 'Property Image'}">
                        <div class="info text-start">
                            <h6>${unit.title || 'Untitled Property'}</h6>
                            
                            <p class="location mb-2" style="color: var(--blue-700);"><i class="fas fa-map-marker-alt me-1"></i>${unit.location || 'Location not specified'}</p>
                            
                            <p class="details mb-1"><strong>Code:</strong> ${unit.unit_code || 'N/A'}</p>
                            <p class="details mb-1"><strong>Floor Area:</strong> ${floorArea}</p>
                            <p class="details mb-1"><strong>Bedroom:</strong> ${bedrooms}</p>
                            <p class="details mb-1"><strong>Bathroom:</strong> ${bathrooms}</p> 

                            <p class="price mt-3 mb-1" style="font-weight: 700; font-size: 1.25rem; color: var(--blue-800);">
                                <strong>Unit Price:</strong> ${monthlyRent}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        });

        // Observe new cards for animation
        const dynamicAnimatedElements = container.querySelectorAll('.animate-on-scroll');
        dynamicAnimatedElements.forEach((el) => observer.observe(el));

        container.style.opacity = 1; // fade in after load
    }, 400);
}
    });