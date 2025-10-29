<link rel="stylesheet" href="{{ asset('css/footer.css') }}">

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
            © 2025 SubdiRent. All rights reserved. Project by <span class="text-primary fw-semibold">SubdiRent Team</span>.
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