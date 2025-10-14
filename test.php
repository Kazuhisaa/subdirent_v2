<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Subdirent | Home</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
    <style>
        /* Base styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: "Inter", Arial, sans-serif;
  color: #1e293b;
  background-color: #f8fafc;
  line-height: 1.6;
}

a {
  text-decoration: none;
  color: inherit;
}

.container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Navbar */
.navbar {
  background: #ffffff;
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  z-index: 100;
}

.nav-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 70px;
}

.logo {
  font-weight: 700;
  font-size: 1.25rem;
  color: #0ea5a4;
}

.nav-links a {
  margin: 0 12px;
  color: #334155;
  font-weight: 500;
  transition: color 0.3s;
}

.nav-links a:hover,
.nav-links a.active {
  color: #0ea5a4;
}

.btn-login {
  background: #0ea5a4;
  color: white;
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 500;
  transition: background 0.3s;
}

.btn-login:hover {
  background: #08918f;
}

/* Hero Section */
.hero {
  background: linear-gradient(180deg, #e0f2fe 0%, #f8fafc 100%);
  text-align: center;
  padding: 80px 20px;
}

.hero h1 {
  font-size: 2.5rem;
  color: #0f172a;
}

.hero p {
  margin: 20px 0;
  font-size: 1.1rem;
  color: #475569;
}

.btn-primary {
  display: inline-block;
  background: #0ea5a4;
  color: white;
  padding: 12px 28px;
  border-radius: 8px;
  font-weight: 600;
  transition: background 0.3s;
}

.btn-primary:hover {
  background: #0b8887;
}

/* About Section */
.about {
  padding: 80px 20px;
  background: white;
  text-align: center;
}

.about h2 {
  font-size: 2rem;
  color: #0f172a;
  margin-bottom: 16px;
}

.about p {
  max-width: 700px;
  margin: 0 auto 40px auto;
  color: #475569;
}

.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 20px;
}

.card {
  background: #f1f5f9;
  padding: 24px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.card h3 {
  color: #0ea5a4;
  margin-bottom: 8px;
}

/* Footer */
.footer {
  background: #ffffff;
  border-top: 1px solid #e5e7eb;
  padding: 24px 20px;
  margin-top: 60px;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
}

.footer p {
  color: #64748b;
}

.footer-links a {
  margin-left: 16px;
  color: #0ea5a4;
}

.footer-links a:hover {
  text-decoration: underline;
}

    </style>
  <!-- HEADER -->
  <header class="navbar">
    <div class="container nav-container">
      <div class="logo">🏠 Subdirent</div>
      <nav class="nav-links">
        <a href="index.html" class="active">Home</a>
        <a href="units.html">Units</a>
        <a href="pricing.html">Pricing</a>
        <a href="contact.html">Contact</a>
      </nav>
      <a href="login.html" class="btn-login">Login</a>
    </div>
  </header>

  <!-- HERO -->
  <section class="hero">
    <div class="container hero-content">
      <h1>Find Your Perfect Rental Space</h1>
      <p>Discover, compare, and manage subdorm units easily with Subdirent.</p>
      <a href="units.html" class="btn-primary">Browse Units</a>
    </div>
  </section>

  <!-- ABOUT -->
  <section class="about">
    <div class="container">
      <h2>About Subdirent</h2>
      <p>Subdirent is a platform that connects renters and dorm owners for easier and smarter property management.</p>

      <div class="cards">
        <div class="card">
          <h3>Easy to Use</h3>
          <p>Search and filter units by price, location, and features in just a few clicks.</p>
        </div>
        <div class="card">
          <h3>Transparent Pricing</h3>
          <p>See all costs upfront — no hidden fees or surprise charges.</p>
        </div>
        <div class="card">
          <h3>Reliable Owners</h3>
          <p>All listings are verified for safety and accuracy.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container footer-content">
      <p>&copy; 2025 Subdirent. All rights reserved.</p>
      <div class="footer-links">
        <a href="contact.html">Contact</a>
        <a href="privacy.html">Privacy Policy</a>
      </div>
    </div>
  </footer>
</body>
</html>
