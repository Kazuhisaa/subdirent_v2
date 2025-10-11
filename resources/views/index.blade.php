@extends('layouts.app')

@section('title', 'SubdiRent | Automated Parking')

@section('content')
<section class="hero d-flex justify-content-between align-items-center py-5" style="padding:100px 10%;min-height:80vh;">
  <div class="hero-text" style="max-width:520px;">
    <h1 class="fw-bold display-5 text-dark">SubdiRent</h1>
    <p class="text-secondary fs-5">Subdirent is a property management platform designed to make renting easier for tenants and property owners. Manage tenants, payments, and units — all in one place.</p>
    <a href="#" class="btn btn-primary btn-lg rounded-pill px-4 py-2">Get Started</a>
  </div>

  <div class="slider position-relative rounded overflow-hidden shadow-lg" style="width:420px;height:300px;">
    <img src="https://picsum.photos/id/1005/600/400" class="active w-100 h-100" alt="Slide 1">
    <img src="https://picsum.photos/id/1011/600/400" class="w-100 h-100" alt="Slide 2">
    <img src="https://picsum.photos/id/1025/600/400" class="w-100 h-100" alt="Slide 3">
    <button class="slider-btn prev position-absolute top-50 start-0 translate-middle-y border-0 bg-light px-2 py-1"><i class="fas fa-chevron-left"></i></button>
    <button class="slider-btn next position-absolute top-50 end-0 translate-middle-y border-0 bg-light px-2 py-1"><i class="fas fa-chevron-right"></i></button>
  </div>
</section>

<section class="features py-5 text-center">
  <div class="container">
    <div class="row gy-4">
      <div class="col-md-4">
        <h5 class="fw-bold">Faster approvals</h5>
        <p>Streamlined tenant screening and digital forms.</p>
      </div>
      <div class="col-md-4">
        <h5 class="fw-bold">Clear payments</h5>
        <p>Automated reminders and unified receipts.</p>
      </div>
      <div class="col-md-4">
        <h5 class="fw-bold">Reliable records</h5>
        <p>All unit activity in a single timeline.</p>
      </div>
    </div>
  </div>
</section>
@endsection
