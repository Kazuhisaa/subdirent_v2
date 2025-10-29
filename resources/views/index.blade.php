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
                    <h5 class="fw-bold">Secure Platform</h5>
                    <p>Your data and payments are protected with advanced encryption.</p>
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


<script src="{{ asset('js/index.js') }}"></script>
@endsection