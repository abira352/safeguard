<?php
include('connection.php');

$registerMessage = "";

if (isset($_POST['register'])) {
    // Sanitize and validate inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $registerMessage = "<p class='error'>❌ All fields are required</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registerMessage = "<p class='error'>❌ Invalid email format</p>";
    } elseif (strlen($password) < 8) {
        $registerMessage = "<p class='error'>❌ Password must be at least 8 characters long</p>";
    } elseif ($password !== $confirm_password) {
        $registerMessage = "<p class='error'>❌ Passwords do not match</p>";
    } else {
        // Use prepared statements to prevent SQL injection
        $checkQuery = "SELECT id FROM users WHERE email = ?";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        
        if ($checkStmt) {
            mysqli_stmt_bind_param($checkStmt, "s", $email);
            mysqli_stmt_execute($checkStmt);
            $result = mysqli_stmt_get_result($checkStmt);
            
            if (mysqli_num_rows($result) > 0) {
                $registerMessage = "<p class='error'>❌ Email already exists</p>";
            } else {
                // Hash password securely
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user with prepared statement
                $insertQuery = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
                $insertStmt = mysqli_prepare($conn, $insertQuery);
                
                if ($insertStmt) {
                    mysqli_stmt_bind_param($insertStmt, "sss", $name, $email, $hashedPassword);
                    
                    if (mysqli_stmt_execute($insertStmt)) {
                        $registerMessage = "<p class='success'>✅ Registration Successful</p>";
                        // Clear form data on success
                        $name = $email = "";
                    } else {
                        $registerMessage = "<p class='error'>❌ Registration Failed</p>";
                    }
                    
                    mysqli_stmt_close($insertStmt);
                } else {
                    $registerMessage = "<p class='error'>❌ Database error</p>";
                }
            }
            
            mysqli_stmt_close($checkStmt);
        } else {
            $registerMessage = "<p class='error'>❌ Database error</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeGuard Online Registration</title>
      <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    <style>
        :root {
            --primary-color: #dc3545;
            --dark-bg: #0a0a0a;
            --form-bg: rgba(0, 0, 0, 0.85);
            --input-bg: rgba(30, 30, 30, 0.9);
            --text-light: #ffffff;
            --text-muted: #aaa;
            --success-color: #00ff99;
            --error-color: #ff4d4d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
        background-image: url('images/233.jpg');
      background-size: cover;
      background-attachment: fixed;
      background-repeat: no-repeat;
      background-position: center;
      color: var(--light-text);
      padding-top: 70px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Navbar Styles */
        .navbar {
    background-color: #0a0a0a;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(220, 53, 69, 0.3);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            color: color: white; !important;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .navbar-brand img {
            filter: drop-shadow(0 0 5px var(--primary-color));
        }

        .nav-link {
            color: color: white; !important;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: color: white; !important;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 20px 50px;
            background: 
                radial-gradient(circle at 20% 50%, rgba(220, 53, 69, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(220, 53, 69, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(220, 53, 69, 0.1) 0%, transparent 50%);
        }

        /* Form Container */
        .form-container {
            background: var(--form-bg);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 450px;
            box-shadow: 
                0 0 30px rgba(220, 53, 69, 0.3),
                inset  0 30px rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(141, 19, 32, 0.2);
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

        .form-title {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
            font-size: 1.8rem;
            font-weight: 700;
            text-shadow: 0 0 20px rgba(220, 53, 69, 0.5);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            background: var(--input-bg);
            border: 2px solid rgba(220, 53, 69, 0.3);
            border-radius: 12px;
            color: var(--text-light);
            font-size: 16px;
            font-family: 'Orbitron', sans-serif;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .form-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 20px rgba(220, 53, 69, 0.3);
            transform: translateY(-2px);
        }

        .submit-btn {
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
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(220, 53, 69, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 2px solid var(--primary-color);
            border-radius: 12px;
            color: var(--primary-color);
            font-size: 14px;
            font-weight: 600;
            font-family: 'Orbitron', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .login-btn:hover {
            background: var(--primary-color);
            color: var(--text-light);
            transform: translateY(-2px);
        }

        .message {
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
        }

        .success {
            color: var(--success-color);
            background: rgba(0, 255, 153, 0.1);
            border: 1px solid rgba(0, 255, 153, 0.3);
        }

        .error {
            color: var(--error-color);
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid rgba(255, 77, 77, 0.3);
        }

        .theme-toggle {
            background: none;
            border: none;
            font-size: 1.4rem;
            color: var(--text-light);
            cursor: pointer;
            margin-left: 15px;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            color: var(--primary-color);
            transform: rotate(180deg);
        }

            /* Footer */
        .footer {
            background:black;
            padding: 3rem 2rem 1rem;
            border-top: 1px solid var(--border-color);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h4 {
            color: white;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .footer-section p,
        .footer-section a {
           color: white;;
            text-decoration: none;
            margin-bottom: 0.5rem;
            display: block;
            opacity: 0.8;
        }

        .footer-section a:hover {
color: red;
            opacity: 1;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
            margin-top: 2rem;
   color: white;
            opacity: 0.6;
        }

        /* Social Icons */
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .social-links a:hover {
            transform: scale(1.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background: var(--card-bg);
                flex-direction: column;
                padding: 1rem;
                box-shadow: var(--shadow);
            }

            .nav-links.active {
                display: flex;
            }

            .mobile-menu {
                display: block;
            }

            .contact-section {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .page-title {
                font-size: 2rem;
            }

            .nav-container {
                padding: 0 1rem;
            }

            .main-content {
                padding: 2rem 1rem;
            }

            .footer {
                padding: 2rem 1rem 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="form-container">
            <h2 class="form-title">🛡️ Join SafeGuard Online</h2>
            
            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="name" class="form-input" placeholder="Full Name" 
                           value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="Email Address" 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Password (min 8 characters)" 
                           required minlength="8">
                </div>
                
                <div class="form-group">
                    <input type="password" name="confirm-password" class="form-input" placeholder="Confirm Password" 
                           required minlength="8">
                </div>
                
                <button type="submit" name="register" class="submit-btn">Create Account</button>
            </form>

            <?php if (!empty($registerMessage)): ?>
                <div class="message">
                    <?php echo $registerMessage; ?>
                </div>
            <?php endif; ?>

            <a href="login.php" class="login-btn">🔐 Already have an account? Login</a>
        </div>
    </main>

     <!-- Footer -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
  

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm-password"]').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
</body>
</html>