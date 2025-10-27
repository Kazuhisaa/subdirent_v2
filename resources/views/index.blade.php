@extends('layouts.app')

@section('title', 'Welcome to SubdiRent')

@section('content')

{{-- Google Fonts: 'Salsa' --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Salsa&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/index.css') }}">

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-text animate-on-scroll">
                <h1>Find Your Perfect Home</h1>
                <p>
                    SubdiRent makes renting easier — discover, book, and manage units all in one place.
                </p>
                <a href="#properties" class="btn btn-custom btn-explore">
                    Explore Units
                </a>
            </div>
            <div class="col-lg-6 hero-image text-center animate-on-scroll" data-delay="1">
                <img src="{{ asset('uploads/websubdi3.png') }}" alt="Cozy house illustration">
            </div>
        </div>
    </div>
</section>

<section class="why-choose-section">
    <div class="container text-center">
        <h2 class="section-heading animate-on-scroll">Why Choose SubdiRent?</h2>
        
        <div class="row align-items-center justify-content-center gy-5 gy-lg-0">
            
            <div class="col-lg-3 feature-col-left animate-on-scroll">
                <div class="feature-item mb-5" data-number="1"> 
                    <h5 class="fw-bold">Fast Approvals</h5>
                    <p>Streamlined tenant screening and digital forms for quick processing.</p>
                </div>
                <div class="feature-item" data-number="2">
                    <h5 class="fw-bold">Reliable Records</h5>
                    <p>Keep every transaction and unit activity organized in one place.</p>
                </div>
            </div>

            <div class="col-lg-6 text-center center-image-col animate-on-scroll" data-delay="1">
                <img src="{{ asset('uploads/websubdi2.png') }}" class="img-fluid center-image" alt="Modern family home">
            </div>

            <div class="col-lg-3 feature-col-right animate-on-scroll" data-delay="2">
                <div class="feature-item mb-5" data-number="3">
                    <h5 class="fw-bold">Transparent Payments</h5>
                    <p>Automated reminders and unified receipts — no hidden fees.</p>
                </div>
                <div class="feature-item" data-number="4">
                    <h5 class="fw-bold">24/7 Support</h5>
                    <p>Our dedicated team is always here to help you with any questions.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<section id="properties" class="properties-section text-center">
    <div class="container">
        <h2 class="section-heading mb-3 animate-on-scroll">Featured Properties</h2>
        <p class="text-muted mb-5 animate-on-scroll" data-delay="1">Explore our most popular rental listings available for you.</p>

        <div id="featured-container" class="row g-4 justify-content-center">
            <p class="text-muted text-center">Loading featured properties...</p>
        </div>
    </div>
</section>


<script>
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
                const featured = units.slice(0, 3);
                renderFeatured(featured);
            })
            .catch(err => {
                console.error("Error loading featured units:", err);
                container.innerHTML = `<p class="text-danger text-center">Failed to load featured units.</p>`;
            });

        function renderFeatured(units) {
            container.innerHTML = ""; 

            if (!units.length) {
                container.innerHTML = `<p class="text-muted text-center">No featured units available at the moment.</p>`;
                return;
            }

            units.forEach((unit, index) => { // Dinagdag ko 'index'
                const imageUrl = unit.files?.length ? `/${unit.files[0]}` : 'https://via.placeholder.com/300x220.png?text=No+Image';
                const monthlyRent = unit.monthly_rent ? `₱${parseFloat(unit.monthly_rent).toLocaleString()}` : 'Price not available';

                // Nilagyan ko ng 'animate-on-scroll' class at 'data-delay'
                container.innerHTML += `
                    <div class="col-lg-4 col-md-6 animate-on-scroll" data-delay="${index + 1}">
                        <div class="property-card">
                            <img src="${imageUrl}" alt="${unit.title || 'Property Image'}">
                            <div class="info text-start">
                                <h6>${unit.title || 'Untitled Property'}</h6>
                                <p class="price mb-1">${monthlyRent}/month</p>
                                <p class="location mb-3"><i class="fas fa-map-marker-alt me-1"></i>${unit.location || 'Location not specified'}</p>
                                <a href="/units/${unit.id}" class="btn btn-details">View Details</a>
                            </div>
                        </div>
                    </div>
                `;
            });

            // =============================================
            // === I-OBSERVE YUNG MGA BAGONG GAWANG CARDS ===
            // =============================================
            const dynamicAnimatedElements = container.querySelectorAll('.animate-on-scroll');
            dynamicAnimatedElements.forEach((el) => observer.observe(el));
            // =============================================
        }
    });
</script>
@endsection