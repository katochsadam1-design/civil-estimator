<?php
session_start();

// 1. Include database connection
include 'config/db.php'; 

if (isset($_POST['login'])) {
    // Sanitize username to prevent SQL Injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Do not escape password as it will be verified against hash

    // 2. Fetch admin data from database based on username
    $query = "SELECT * FROM admin WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $admin_data = mysqli_fetch_assoc($result);
        
        // 3. Use password_verify() to check if the submitted password matches the secure hash
        if (password_verify($password, $admin_data['password'])) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Invalid username or password!";
    }
}

// Include the unified layout header
include 'header.php';
?>
    <style>
        /* --- Custom Styles for Admin Login Card Integrated with Theme Variables --- */
        :root {
            --login-card-bg: #ffffff;
            --input-bg: #fafafa;
            --input-border: #cccccc;
            --label-color: #444444;
            --btn-login-bg: #2c3e50;
            --btn-login-hover: #1a252f;
            --link-color: #3498db;
        }

        [data-theme="dark"] {
            --login-card-bg: #1e1e1e;
            --input-bg: #2a2a2a;
            --input-border: #444444;
            --label-color: #dddddd;
            --btn-login-bg: #ff6b6b;
            --btn-login-hover: #ff5252;
            --link-color: #ff6b6b;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .login-wrapper {
            max-width: 400px; 
            margin: 100px auto 0 auto;
            padding: 0 20px;
        }

        .login-card {
            background: var(--login-card-bg);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .login-card h2 {
            font-size: 1.8rem;
            color: var(--text-color);
            margin-bottom: 20px;
            text-align: center;
            transition: color 0.3s ease;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: var(--label-color);
            margin-bottom: 6px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--input-border);
            border-radius: 6px;
            font-size: 0.95rem;
            background-color: var(--input-bg);
            color: var(--text-color);
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        .submit-btn {
            display: block;
            width: 100%;
            background-color: var(--btn-login-bg);
            color: #ffffff;
            padding: 14px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 25px;
            transition: background-color 0.3s ease;
            text-align: center;
        }

        .submit-btn:hover {
            background-color: var(--btn-login-hover);
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.95rem;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }

        /* --- Style for Forgot Password Link --- */
        .forgot-link-wrapper {
            text-align: center;
            margin-top: 15px;
        }

        .forgot-link-wrapper a {
            color: var(--link-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease, text-decoration 0.3s ease;
        }

        .forgot-link-wrapper a:hover {
            text-decoration: underline;
        }
    </style>

<div class="login-wrapper">
    <div class="login-card">
        <h2>Admin Login</h2>
        
        <?php if(isset($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <form action="" method="POST" autocomplete="off">
            <div class="form-group">
                <label for="username">Username / Email</label>
                <input type="text" id="username" name="username" required placeholder="Enter admin username or email" autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter admin password" autocomplete="new-password">
            </div>
            
            <button type="submit" name="login" class="submit-btn">Login</button>
            
            <div class="forgot-link-wrapper">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>