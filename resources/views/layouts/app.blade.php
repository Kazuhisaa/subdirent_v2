<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/favicon-96x96.png') }}">
<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
<link rel="manifest" href="{{ asset('images/site.webmanifest') }}">

<link rel="manifest" href="public/images/site.webmanifest">  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'SubdiRent')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    :root {
      --blue-900: #0A2540;
      --blue-800: #0D3B66;
      --blue-700: #145DA0;
      --blue-600: #1E81CE;
      --blue-500: #2A9DF4;
      --blue-400: #5AB8F0;
      --blue-300: #9FD8F7;
      --blue-200: #CDEEFF;
      --blue-100: #EAF8FF;
      --gradient-diagonal: linear-gradient(135deg, var(--blue-700), var(--blue-600), var(--blue-500));
    }

    body {
      font-family: "Segoe UI", sans-serif;
      color: var(--blue-900);
      background-color: var(--blue-100);
    }

    html { scroll-behavior: smooth; }

    .btn-primary {
      background: var(--gradient-diagonal);
      border: none;
      transition: 0.3s;
    }
    .btn-primary:hover { filter: brightness(1.1); }

    footer {
      background-color: #fff;
      color: var(--blue-900);
    }
    footer a {
      color: var(--blue-700);
      text-decoration: none;
    }
    footer a:hover { color: var(--blue-500); }
    
    /* Slider */
    .slider img {
      position: absolute;
      top: 0;
      left: 0;
      opacity: 0;
      transition: opacity 1.5s ease-in-out;
    }

    .slider img.active {
      opacity: 1;
      z-index: 10;
    }

    .slider-btn {
      z-index: 20;
      opacity: 0.8;
      transition: opacity 0.3s;
    }
    .slider-btn:hover {
      opacity: 1;
    }

    @keyframes fadeInSlideUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    main {
      animation: fadeInSlideUp 1s ease-out forwards;
      opacity: 0;
    }
  </style>
  </head>
<body>

  @include('partials.navbar')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  @include('partials.login-modal')

  <main>
    @yield('content')
  </main>

  @include('partials.footer')
  
  {{-- ... (yung iba mong script tags) ... --}}
   <script>
    // Simple Image Slider
    const slides = document.querySelectorAll('.slider img');
    const prev = document.querySelector('.prev');
    const next = document.querySelector('.next');
    let index = 0;

    function showSlide(i) {
      slides.forEach(s => s.classList.remove('active'));
      slides[i].classList.add('active');
    }

    if (next && prev && slides.length > 0) {
      next.addEventListener('click', () => {
        index = (index + 1) % slides.length;
        showSlide(index);
      });
      prev.addEventListener('click', () => {
        index = (index - 1 + slides.length) % slides.length;
        showSlide(index);
      });
      setInterval(() => {
      index = (index + 1) % slides.length;
      showSlide(index);
      }, 5000); // <-- Ito ang nagpapa-auto-slide tuwing 5 segundo (5000 milliseconds)
    }
  </script>
@if ($errors->any())
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
      loginModal.show();
    });
  </script>
  @endif

</body>
</html>
 
