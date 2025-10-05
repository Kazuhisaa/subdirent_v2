<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subdivision Rental System</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f9f9f9; }
        header { background: #333; color: #fff; padding: 15px; }
        nav { display: flex; justify-content: space-between; align-items: center; }
        nav a { color: #fff; margin: 0 10px; text-decoration: none; }
        nav a:hover { text-decoration: underline; }
        .hero { text-align: center; padding: 80px 20px; background: #eee; }
        .section { padding: 40px 20px; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .card { background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .card img { max-width: 100%; height: auto; border-radius: 5px; }
        footer { background: #333; color: #fff; text-align: center; padding: 20px; margin-top: 40px; }
    </style>
</head>
<body>
    {{-- NAVBAR --}}
    <header>
        <nav>
            <div><strong>Subdivision Rentals</strong></div>
            <div>
                <a href="{{ url('/') }}">Home</a>
                <a href="#about">About</a>
                <a href="#rooms">Rooms</a>
                <a href="{{ route('login') }}">Login</a>
            </div>
        </nav>
    </header>

    {{-- HERO --}}
    <section class="hero">
        <h1>Find Your Perfect Unit</h1>
        <p>Safe, affordable, and convenient subdivision rentals.</p>
        <a href="#rooms">Rent Now</a>
    </section>

    {{-- ABOUT --}}
    <section id="about" class="section">
        <h2>About Us</h2>
        <p>We make renting subdivision units simple and transparent. Browse available properties and connect with trusted landlords.</p>
    </section>

    {{-- ROOMS --}}
    <section id="rooms" class="section">
        <h2>Available Units</h2>
        <div class="cards">
            <div class="card">
                <img src="/images/sample1.jpg" alt="Unit 1">
                <h3>Unit A</h3>
                <p>2 Bedroom, 1 Bath, near the clubhouse.</p>
            </div>
            <div class="card">
                <img src="/images/sample2.jpg" alt="Unit 2">
                <h3>Unit B</h3>
                <p>1 Bedroom, modern design, with balcony.</p>
            </div>
        </div>
    </section>

    {{-- FEATURED --}}
    <section class="section">
        <h2>Featured Properties</h2>
        <div class="cards">
            <div class="card">
                <img src="/images/sample3.jpg" alt="Featured Unit">
                <h3>Featured Unit</h3>
                <p>⭐ 5.0 Rating - Spacious 3BR family unit with garden.</p>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer>
        <p>&copy; {{ date('Y') }} Subdivision Rental System. All rights reserved.</p>
    </footer>
</body>
</html>
