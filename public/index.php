<?php
session_start();
require_once '../config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Check credentials
        $stmt = $conn->prepare("SELECT id, username, password, full_name, role, status FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Check if account is active
            if ($user['status'] != 'Active') {
                $error = "Your account has been deactivated. Please contact the administrator.";
            } 
            // Verify password
            elseif (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                
                // Log activity
                logActivity($conn, $user['id'], 'User logged in', 'Authentication');
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BAC System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
        }

        /* ── Hero Header ── */
        .hero-header {
            position: relative;
            width: 100%;
            height: 450px;
            background-image: url('../img/BAC Hero.jpg');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }

        .hero-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(10, 40, 80, 0.80) 0%, rgba(0, 100, 60, 0.75) 100%);
        }

        .hero-header .hero-content {
            position: relative;
            z-index: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .hero-logo img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.6);
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
            margin-bottom: 12px;
        }

        .hero-content h2 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 4px;
            text-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }

        .hero-content h5 {
            font-weight: 500;
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 4px;
        }

        .hero-content p {
            font-size: 0.9rem;
            opacity: 0.7;
            margin: 0;
        }

        /* ── Login Card ── */
        .login-section {
            display: flex;
            justify-content: center;
            padding: 10px 20px 60px;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }

        .login-card h3 {
            font-weight: 700;
            color: #1a1a2e;
        }

        .btn-login {
            background: linear-gradient(135deg, #0a2850 0%, #006440 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
        }

        .btn-login:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <!-- Hero Header -->
    <div class="hero-header">
        <div class="hero-content">
            <div class="hero-logo">
                <img src="../img/Logo.jpg" alt="BFAR Logo">
            </div>
            <h2>Bureau of Fisheries and Aquatic Resources</h2>
            <h5>Bids and Awards Committee</h5>
            <p>Eligibilities Record Keeping System &nbsp;|&nbsp; Based on RA 9184</p>
        </div>
    </div>

    <!-- Login Form -->
    <div class="login-section">
        <div class="login-card">
            <h3 class="mb-1">Welcome Back!</h3>
            <p class="text-muted mb-4">Please login to your account</p>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-login w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
