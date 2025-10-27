<style>
/* --- BAGONG FOOTER STYLES --- */
.footer-section {
    background-color: #EAF8FF; /* Ginamit ko yung light blue (blue-100) mo */
    color: var(--blue-800);
    padding-top: 5rem;
    position: relative; /* Kailangan para sa background image */
    overflow: hidden; /* Para hindi lumagpas yung image */
}

/* ============================================= */
/* === CSS PARA SA SCROLL ANIMATION (FOOTER) === */
/* ============================================= */
.footer-section .animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    will-change: opacity, transform;
}

.footer-section .animate-on-scroll.is-visible {
    opacity: 1;
    transform: translateY(0);
}
/* ============================================= */


/* Ito yung modern house image sa background */
.footer-section::after {
    content: '';
    background-image: url("{{ asset('uploads/websubdi4-removebg-preview.png') }}");
    background-size: contain;
    background-repeat: no-repeat;
    background-position: bottom right;
    position: absolute;
    bottom: 0px; 
    right: -150px; 
    width: 500px; 
    height: 500px; 
    opacity: 0.2; 
    z-index: 1;
    pointer-events: none; 
}


/* Tinitiyak natin na yung content ay nasa ibabaw ng background image */
.footer-section .container {
    position: relative;
    z-index: 2;
}

/* Brand Logo */
.footer-section .brand-icon { height: 40px; }
.footer-section .brand-text { height: 34px; }
.footer-section .brand-description {
    font-size: 0.9rem;
    color: var(--blue-700);
    line-height: 1.7;
}

/* Navigation Links */
.footer-nav-links {
    list-style: none;
    padding-left: 0;
}
.footer-nav-links li {
    margin-bottom: 0.75rem;
}
.footer-nav-links a {
    color: var(--blue-800);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}
.footer-nav-links a:hover {
    color: var(--blue-500);
}

/* Social Icons (Gaya sa reference) */
.footer-social-icons {
    display: flex;
    gap: 1.25rem;
    justify-content: flex-end; /* Papunta sa kanan */
}
.footer-social-icons a {
    color: var(--blue-600);
    font-size: 1.25rem;
    transition: color 0.3s ease, transform 0.3s ease;
}
.footer-social-icons a:hover {
    color: var(--blue-800);
    transform: translateY(-2px);
}

/* --- BAGONG "NEED HELP" BAR --- */
.help-bar-wrapper {
    padding: 1.5rem 2rem;
    margin-top: 4rem;
    /* DINAGDAG KO 'TO PABALIK (Galing sa reference mo) */
}
.help-bar-wrapper h6 {
    color: var(--blue-900);
}
.help-bar-item {
    /* DINAGDAG KO 'TO PABALIK (Galing sa reference mo) */
    border-radius: 12px; 
    
    padding: 0.75rem 1.25rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
}
.help-bar-item:hover {
    transform: translateY(-3px);
}
.help-bar-item i {
    font-size: 1.1rem;
}
.help-bar-item span {
    color: var(--blue-900);
    font-weight: 600;
    font-size: 0.9rem;
}

/* Copyright */
.footer-copyright {
    padding: 1.5rem 0;
    margin-top: 2rem;
    border-top: 1px solid var(--blue-300);
    font-size: 0.85rem;
    color: var(--blue-700);
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .footer-social-icons {
        justify-content: flex-start; /* Sa kaliwa na sa mobile */
        margin-top: 2rem;
    }
}
@media (max-width: 768px) {
    .footer-section::after {
        opacity: 0.1; /* Mas transparent sa mobile */
        right: -200px; /* Mas i-usog pa sa mobile */
    }
}

</style>


<footer id="footer" class="footer-section">
    <div class="container pb-4">
        <div class="row gy-4 align-items-start">

            <div class="col-lg-5 col-md-12 animate-on-scroll">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('uploads/ddf63450-50d1-4fd2-9994-7a08dd496ac1-removebg-preview.png') }}" 
                         alt="SubdiRent Icon" 
                         class="brand-icon">
                    <img src="{{ asset('uploads/1fc18e9c-b6b9-4f39-8462-6e4b7d594471-removebg-preview.png') }}" 
                         alt="SubdiRent Text" 
                         class="brand-text">
                </div>
                <p class="brand-description">
                    SubdiRent helps you find, book, and manage rental units easily and securely, 
                    connecting tenants and property owners with transparent and reliable management tools.
                </p>
            </div>

            <div class="col-lg-3 offset-lg-1 col-md-6 animate-on-scroll" data-delay="1">
                <h6 class="fw-bold text-dark mb-3">Navigation</h6>
                <ul class="footer-nav-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="/available-units">Properties Listing</a></li>
                    <li><a href="#footer">Contact</a></li>
                </ul>
            </div>

        <div class="help-bar-wrapper d-flex flex-wrap justify-content-center align-items-center gap-3 animate-on-scroll" data-delay="3">
            <h6 class="fw-bold mb-0 text-dark me-2">Need Help?</h6>
            
            <div class="help-bar-item d-flex align-items-center gap-2">
                <i class="fas fa-phone-alt text-primary"></i>
                <span class="small text-dark fw-semibold">1-800-555-4321</span>
            </div>
            
            <div class="help-bar-item d-flex align-items-center gap-2">
                <i class="fab fa-whatsapp text-success"></i>
                <span class="small text-dark fw-semibold">1-800-555-4321</span>
            </div>
            
            <div class="help-bar-item d-flex align-items-center gap-2">
                <i class="fas fa-envelope text-primary"></i>
                <span class="small text-dark fw-semibold">hello@subdirent.com</span>
            </div>
        </div>

        <div class="footer-copyright text-center">
            © 2025 SubdiRent. All rights reserved. Designed by <span class="text-primary fw-semibold">SubdiRent Team</span>.
        </div>
    </div>
</footer>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Tinitingnan muna natin kung may 'IntersectionObserver' 
    if ('IntersectionObserver' in window) {
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
            threshold: 0.1 // 10% ng item ay dapat makita bago mag-animate
        });

        // Kunin lahat ng elements sa footer na may .animate-on-scroll
        const footerAnimatedElements = document.querySelectorAll('.footer-section .animate-on-scroll');
        footerAnimatedElements.forEach((el) => observer.observe(el));
    }
});
</script>