<?php
session_start();
include('connection.php');

$loginMessage = "";

if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE EMAIL = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['PASSWORD'])) {
        $_SESSION['users'] = $user;
        header("Location: index.html");
        exit();
    } else {
        $loginMessage = "<p class='error'>❌ Invalid Email or Password</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | SafeGuard Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
/* ========== VARIABLES ========== */
:root {
  --dark-bg: #0a5347;
  --light-bg: #ffffff;
  --light-text: #ffffff;
  --dark-text: #000000;
  --primary-color: #dc3545;
  --text-light: #ffffff;
  --text-color: #ffffff;
  --text-muted: #cccccc;
  --error-red: #ff4d4d;
  --input-bg: rgba(0, 0, 0, 0.3);
  --form-bg: rgba(0, 0, 0, 0.5);
  --border-color: rgba(255, 255, 255, 0.2);
  --card-bg: #1f1f1f;
  --shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

/* ========== GLOBAL STYLES ========== */
body {
  background-image: url('images/234.jpg');
  background-size: cover;
  background-attachment: fixed;
  background-repeat: no-repeat;
  background-position: center;
  color: var(--light-text);
  padding-top: 70px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body.light-mode {
  background-color: var(--light-bg);
  color: var(--dark-text);
}

/* ========== NAVBAR ========== */
.navbar {
  background-color: #000;
  position: fixed;
  width: 100%;
  top: 0;
  z-index: 999;
}
.navbar-brand,
.nav-link {
  color: #fff !important;
}
.navbar-toggler {
  border-color: rgba(255, 255, 255, 0.5);
}

body.light-mode .navbar,
body.light-mode footer {
  background-color: #f8f9fa;
  color: #000;
}
body.light-mode .navbar-brand,
body.light-mode .nav-link {
  color: #000 !important;
}

/* ========== FORM ========== */
.form-container {
  background: var(--form-bg);
  padding: 40px;
  border-radius: 20px;
  max-width: 450px;
  margin: 0 auto;
  box-shadow: 0 0 30px rgba(220, 53, 69, 0.3), inset 0 0 30px rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(220, 53, 69, 0.2);
  animation: slideUp 0.8s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(50px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form-container h2 {
  text-align: center;
  margin-bottom: 30px;
  color: var(--primary-color);
  font-size: 1.8rem;
  font-weight: 700;
  font-family: 'Orbitron', sans-serif;
  text-shadow: 0 0 20px rgba(220, 53, 69, 0.5);
}

.form-container input[type="email"],
.form-container input[type="password"] {
  width: 100%;
  padding: 15px 20px;
  background: var(--input-bg);
  border: 2px solid rgba(220, 53, 69, 0.3);
  border-radius: 12px;
  color: var(--text-light);
  font-size: 16px;
  margin-bottom: 20px;
  font-family: 'Orbitron', sans-serif;
  transition: all 0.3s ease;
  outline: none;
}
.form-container input::placeholder {
  color: var(--text-muted);
}
.form-container input:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 20px rgba(220, 53, 69, 0.3);
  transform: translateY(-2px);
}

/* ========== BUTTONS ========== */
.form-container button[type="submit"] {
  width: 100%;
  padding: 15px;
  background: linear-gradient(135deg, var(--primary-color) 0%, #b91c2c 100%);
  border: none;
  border-radius: 12px;
  color: var(--text-light);
  font-size: 16px;
  font-weight: 700;
  font-family: 'Orbitron', sans-serif;
  cursor: pointer;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 1px;
}
.form-container button:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(220, 53, 69, 0.4);
}

/* ========== ERROR MESSAGE ========== */
.error {
  color: var(--error-red);
  margin-top: 15px;
  text-align: center;
}

/* ========== FOOTER ========== */
.footer {
  background: black;
  padding: 3rem 2rem 1rem;
  border-top: 1px solid var(--border-color);
  color: var(--text-color);
}
.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}
.footer-section h4 {
  color: var(--primary-color);
  margin-bottom: 1rem;
  font-size: 1.2rem;
}
.footer-section a {
  color: var(--text-color);
  text-decoration: none;
  margin-bottom: 0.5rem;
  display: block;
  opacity: 0.8;
}
.footer-section a:hover {
  opacity: 1;
  color: var(--primary-color);
}
.footer-bottom {
  text-align: center;
  padding-top: 2rem;
  border-top: 1px solid var(--border-color);
  margin-top: 2rem;
  opacity: 0.6;
}

/* ========== RESPONSIVE DESIGN ========== */
@media (max-width: 575.98px) {
  h1 {
    font-size: 1.8rem;
  }

  .form-container {
    padding: 30px 20px;
  }
}

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark px-3">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="images/logo-removebg-preview (4).png" alt="Logo" style="height: 40px; width: auto; margin-right: 10px;">
      <span>SAFEGUARD ONLINE</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="emergency.html">Emergency Help</a></li>
        <li class="nav-item"><a class="nav-link" href="hospital.html">Hospitals</a></li>
           <li class="nav-item"><a class="nav-link" href="feature.html">Features</a></li>
         <li class="nav-item"><a class="nav-link" href="doctor.html">Doctors</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="http://localhost/safeguard/register.php">Register/Login</a></li>
      </ul>
    </div>
  </div>
</nav>
<br><br><br><br><br><br>

   <div class="container py-5">
        <div class="form-container">
            <h2>Login</h2>
            <form method="post">
                <input type="email" name="email" placeholder="Enter your email" required>
                <input type="password" name="password" placeholder="Enter your password" required>
                <button type="submit" name="login">Login</button>
                <?php if ($loginMessage): ?>
                    <div class="error"><?= $loginMessage ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <br><br><br><br><br><br>
     <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>About YourCompany</h4>
                <p>We are a leading company dedicated to providing exceptional services and building lasting relationships with our clients.</p>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="index.html">Home</a>
                <a href="emergency.html">Emergency Help</a>
                <a href="hospital.html">Hospitals</a>
                <a href="do.html">Doctors</a>
                <a href="about.html">About</a>
                <a href="feature.html">Features</a>
                <a href="contact.html">Contact</a>
                <a href="http://localhost/safeguard/register.php">Login/Register</a>
                <a href="user.html">User Profile</a>
            </div>

            <div class="footer-section">
                <h4>Services</h4>
                <a href="#">Web Development</a>
                <a href="#">Mobile Apps</a>
                <a href="#">Digital Marketing</a>
                <a href="#">Consulting</a>
                <a href="#">Support</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 YourCompany. All rights reserved. | Privacy Policy | Terms of Service</p>
        </div>
    </footer>
  <!-- SCRIPT -->
</body>
</html>
