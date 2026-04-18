<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Agency</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.0">
    <style>
        .logo-animated {
            animation: logoPulse 4s ease-in-out infinite;
            filter: drop-shadow(0 0 8px rgba(37,99,235,0.3));
            transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .logo:hover .logo-animated { transform: rotate(360deg) scale(1.1); }
        @keyframes logoPulse {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 8px rgba(37,99,235,0.3)); }
            50% { transform: scale(1.05); filter: drop-shadow(0 0 15px rgba(37,99,235,0.6)); }
        }
    </style>
    <script>
        // Apply saved theme BEFORE page renders to prevent flash
        (function() {
            var t = 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="index.html" class="logo">
                <img src="assets/images/logo.png" alt="Travel Agency Logo" class="logo-animated" style="height: 52px; width: auto;">
                Travel Agency
            </a>
            <nav class="nav-links">
                <a href="packages.html">Packages</a>
                <a href="hotels.html">Hotels</a>
                <a href="buses.html">Buses</a>
                <a href="rentals.html">Rentals</a>
                <a href="ai_planner.html" style="color: #2563eb; font-weight: 700;">✨ AI Planner</a>
                <a href="help.html">Help Support</a>
                
                <?php if(isLoggedIn()): ?>
                    <a href="dashboard.html">Dashboard</a>
                    <a href="logout.html" class="btn-danger">Logout</a>
                <?php else: ?>
                    <a href="login.html" class="btn-secondary">Login</a>
                    <a href="register.html" class="btn-primary">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main>

