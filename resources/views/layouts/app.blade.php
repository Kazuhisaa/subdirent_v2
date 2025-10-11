<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'SubdiRent')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

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
  </style>
</head>

<body>
  @include('partials.navbar')

  <main>
    @yield('content')
  </main>

  @include('partials.footer')

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
      }, 5000);
    }
  </script>
</body>
</html>
