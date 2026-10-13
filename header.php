<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* --- Premium & Stylish Brand Logo Styling --- */
        .logo a {
            font-family: 'Poppins', 'Montserrat', 'Segoe UI', sans-serif;
            font-weight: 700 !important; 
            font-size: 1.8rem !important; 
            text-decoration: none;
            color: #2b2b2b !important; 
            letter-spacing: 1px; /* Increased letter spacing for premium look */
            text-transform: uppercase; /* Convert text to uppercase for brand identity */
            transition: all 0.3s ease; /* Smooth transition effect for hover */
            display: inline-block;
        }

        /* Golden accent color applied specifically to the 'Homestay' brand name */
        .logo a span {
            color: #ffb703 !important; 
            font-weight: 800;
            position: relative;
        }

        /* Hover Animation: Lift logo slightly on mouse over */
        .logo a:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        /* Flexbox spacing container layout for navigation items */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px; /* Enhanced padding for a cleaner layout */
            background-color: #ffffff; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); /* Modern subtle shadow under header */
        }

        /* --- Navigation Actions Container --- */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 25px; /* Spacing between Policy link and Admin button */
        }

        /* --- Custom Policy Link Styling --- */
        .policy-link {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            color: #2b2b2b;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .policy-link:hover {
            color: #ffb703; /* Turns gold on hover */
        }

        /* --- Admin Button Styling --- */
        .admin-login-btn {
            display: inline-block;
            background-color: #2306c5; /* Match with rooms page primary blue */
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(35, 6, 197, 0.15);
        }

        .admin-login-btn:hover {
            background-color: #ffb703; /* Match with logo golden yellow */
            color: #130101;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(255, 183, 3, 0.2);
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <a href="index.php">Al Raahat <span>Homestay</span></a>
        </div>
        
        <div class="nav-actions">
            <!-- Added Policy Link here -->
            <a href="policy.php" class="policy-link">Policies</a>
            <a href="admin_login.php" class="admin-login-btn">Admin Portal</a>
        </div>
    </header>

</body>
</html>