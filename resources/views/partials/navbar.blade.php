<nav class="navbar navbar-expand-lg py-3 shadow-sm bg-white">
    <div class="container d-flex align-items-center justify-content-between">
    
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('uploads/ddf63450-50d1-4fd2-9994-7a08dd496ac1-removebg-preview.png') }}" 
                 alt="SubdiRent Icon" 
                 class="brand-icon">
            <img src="{{ asset('uploads/1fc18e9c-b6b9-4f39-8462-6e4b7d594471-removebg-preview.png') }}" 
                 alt="SubdiRent Text" 
                 class="brand-text">
        </a>

        <div class="d-none d-lg-flex align-items-center flex-grow-1">
            <ul class="navbar-nav ms-3 gap-3"> <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="{{ route('public.units') }}">Units</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="#footer">Contact</a></li>
            </ul>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button 
                type="button" 
                class="btn btn-login-nav fw-semibold px-3 rounded-pill" /* <-- Pinalitan ang class */
                data-bs-toggle="modal" 
                data-bs-target="#loginModal">
                <i class="fas fa-user-circle"></i> <span>LOG IN</span> </button>
        </div>
        </div>
</nav>

<style>
/* Brand logo sizing */
.brand-icon {
    height: 34px;
    width: auto;
    object-fit: contain;
    transition: transform 0.3s ease;
}
.navbar {
    position: sticky;
    top: 0;
    z-index: 1030;
    transition: box-shadow 0.3s ease;
}
.navbar.scrolled {
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
.brand-text {
    height: 30px;
    width: auto;
    object-fit: contain;
    margin-left: 8px;
    transition: transform 0.3s ease;
}

/* Hover effect */
.navbar-brand:hover .brand-icon,
.navbar-brand:hover .brand-text {
    transform: scale(1.05);
}

/* Navbar link spacing and alignment */
.navbar-nav {
    margin-left: 10px !important; /* brings links closer to brand */
}

.nav-item .nav-link {
    color: #0A2540;
    transition: color 0.3s ease;
}

.nav-item .nav-link:hover {
    color: #08355fff;
}

/* Make navbar content centered nicely */
.navbar .container {
    max-width: 1200px;
}

/* ============================================= */
/* === ITO YUNG BAGONG CSS PARA SA BUTTON === */
/* ============================================= */
.btn-login-nav {
    background-color: transparent;
    border: 1px solid transparent; /* Para smooth ang transition */
    color: #0A2540; /* Kapareho ng nav links */
    
    /* Para mag-align ang icon at text */
    display: flex;
    align-items: center;
    gap: 0.5rem; /* Space sa pagitan ng icon at text */
    
    transition: all 0.3s ease; /* Smooth na transition */
}

.btn-login-nav:hover {
    background-color: #055981ff; /* Light blue background (blue-100) */
    border-color: #CDEEFF; /* Light blue border (blue-200) */
}
/* ============================================= */


/* Responsive adjustments */
@media (max-width: 576px) {
    .brand-icon {
        height: 28px;
    }
    .brand-text {
        height: 24px;
        margin-left: 6px;
    }
}

</style>
<script>
    window.addEventListener("scroll", () => {
        const navbar = document.querySelector(".navbar");
        navbar.classList.toggle("scrolled", window.scrollY > 20);
    });
</script>