@extends('layouts.app')

@section('title', 'Welcome to SubdiRent')

@section('content')

{{-- Google Fonts: 'Salsa' --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Salsa&display=swap" rel="stylesheet">


<style>
    /* Gumagamit tayo ng font na kamukha ng nasa reference */
    body {
        font-family: 'Poppins', 'Segoe UI', sans-serif;
        background-image: url("{{ asset('uploads/bg1.jpg') }}");
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        background-attachment: fixed; 
        overflow-x: hidden; /* Para maiwasan ang horizontal scroll galing sa animations */
    }

    /* ============================================= */
    /* === BAGONG CSS PARA SA SCROLL ANIMATION === */
    /* ============================================= */
    .animate-on-scroll {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        will-change: opacity, transform; /* Para sa performance */
    }
    
    .animate-on-scroll.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
    /* ============================================= */


    /* --- Hero Section --- */
    .hero-section {
        min-height: 80vh; 
        display: flex;
        align-items: center;
        padding: 30px 0;
        overflow: hidden;
    }

    .hero-section .hero-text h1 {
        font-size: 3.5rem;
        font-weight: 700;
        color: var(--blue-800);
        line-height: 1.2;
    }

    .hero-section .hero-text p {
        font-size: 1.1rem;
        color: var(--blue-700);
        margin: 20px 0 30px;
    }

    .hero-section .btn-custom {
        padding: 12px 30px;
        border-radius: 90px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
    }
    .btn-explore {
        background-color: var(--blue-400);
        color: #fff;
        box-shadow: 0 4px 15px rgba(13, 59, 102, 0.2);
    }
    .btn-explore:hover {
        background-color: var(--blue-200);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(157, 170, 182, 0.3);
    }
    
    .hero-section .hero-image img {
        max-width: 100%;
        height: auto;
        animation: floatAnimation 10s ease-in-out infinite;
    }
    
    @keyframes floatAnimation {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    .section-heading {
        font-family: 'Salsa', 'Poppins', sans-serif; 
        font-size: 3.8rem; 
        font-weight: normal; 
        
        background-image: linear-gradient(135deg, var(--blue-800), var(--blue-600));
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;

        position: relative; /* Kailangan para sa ::after */
        padding-bottom: 20px; /* Space para sa underline */
        margin-bottom: 70px; /* Original margin */
    }

    .section-heading::after {
        content: '';
        position: absolute;
        display: block;
        width: 120px; /* Lapad ng underline */
        height: 4px; /* kapal */
        background-image: linear-gradient(135deg, var(--blue-800), var(--blue-600)); /* Kapareho ng text */
        border-radius: 2px;
        
        /* I-center sa baba */
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
    }

    .section-heading.mb-3 {
        margin-bottom: 1rem !important; /* Ino-override yung 70px */
    }

    /* --- Why Choose Us Section --- */
    .why-choose-section {
        padding: 100px 0;
        overflow: hidden; /* Para sa animation */
    }
    
    .why-choose-section .center-image {
        width: 100%; 
        height: auto;
    }

    /* --- CSS para sa Feature Items --- */
    .feature-item {
        text-align: center; 
        margin-bottom: 30px;
        position: relative; 
        z-index: 2; 
        padding: 10px; 
    }

    .feature-item::before {
        content: attr(data-number); 
        position: absolute;
        z-index: 1; 
        font-size: 8rem; 
        font-weight: 700;
        font-family: 'Poppins', sans-serif; 
        color: var(--blue-200); 
        opacity: 0.7; 
        top: 50%;
        user-select: none;
        pointer-events: none;
    }
    
    @media (min-width: 992px) {
        .feature-col-left .feature-item { text-align: left; }
        .feature-col-right .feature-item { text-align: right; }

        .feature-col-left .feature-item::before {
            left: -20px; 
            transform: translateY(-60%);
        }
        .feature-col-right .feature-item::before {
            right: -20px;
            transform: translateY(-60%);
        }
    }

    @media (max-width: 991.98px) {
        .feature-item::before {
            left: 50%;
            transform: translate(-50%, -60%);
        }
    }

    @media (max-width: 992px) {
        .hero-section .hero-text h1 { font-size: 2.8rem; }
        .why-choose-section .row { flex-direction: column; }
        .why-choose-section .center-image-col {
            order: 1; 
            margin-bottom: 40px;
        }
        .why-choose-section .feature-col-left,
        .why-choose-section .feature-col-right {
            order: 2; 
        }
    }

    @media (max-width: 768px) {
        .hero-section {
            text-align: center;
            min-height: auto;
            padding: 80px 0;
        }
    }
    
    /* --- Featured Properties Section Styling --- */
    .properties-section {
        padding: 80px 0;
        background-color: rgba(255, 255, 255, 0.5); 
        overflow: hidden; /* Para sa animation */
    }
    
    .property-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    .property-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }
    .property-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }
    .property-card .info { padding: 20px; }
    .property-card .info h6 {
        font-weight: 600;
        color: var(--blue-900);
        margin-bottom: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis; 
    }
     .property-card .info .price {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--blue-600);
     }
    .property-card .info .location {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .property-card .info .btn-details {
        background: var(--gradient-diagonal);
        border: none;
        color: white;
        border-radius: 50px;
        padding: 8px 20px;
        font-weight: 500;
        font-size: 0.9rem;
    }
    .property-card .info .btn-details:hover {
        filter: brightness(1.1);
    }
</style>


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