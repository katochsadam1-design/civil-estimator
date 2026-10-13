<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}
include 'config/db.php';

$search = "";
$query = "SELECT * FROM bookings ORDER BY booking_date DESC";

if (isset($_GET['search']) && !empty($_GET['search_term'])) {
    $search = $conn->real_escape_string($_GET['search_term']);
    // Search records by Name or Phone Number
    $query = "SELECT * FROM bookings WHERE customer_name LIKE '%$search%' OR phone_number LIKE '%$search%' ORDER BY booking_date DESC";
}

$archive_result = $conn->query($query);

// Include the unified layout header
include 'header.php';
?>
    <style>
        /* --- Custom Styles for Admin Archive Integrated with Theme Variables --- */
        :root {
            --table-bg: #ffffff;
            --table-th-bg: #2c3e50;
            --table-th-text: #ffffff;
            --table-border: #dddddd;
            --input-bg: #fafafa;
            --input-text: #222222;
            --input-border: #cccccc;
            --btn-bg: #2c3e50;
            --btn-text: #ffffff;
            --sub-nav-bg: #f8f9fa;
        }

        [data-theme="dark"] {
            --table-bg: #1e1e1e;
            --table-th-bg: #111111;
            --table-th-text: #f5f5f5;
            --table-border: #333333;
            --input-bg: #2a2a2a;
            --input-text: #f5f5f5;
            --input-border: #444444;
            --btn-bg: #ff6b6b;
            --btn-text: #ffffff;
            --sub-nav-bg: #25262b;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* --- Stylish Admin Navigation Link Bar --- */
        .admin-sub-nav {
            background-color: var(--sub-nav-bg);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .admin-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-color);
            letter-spacing: 0.5px;
        }

        .admin-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        /* Stylish Buttons for Navigation */
        .btn-nav-back {
            display: inline-block;
            background-color: #2306c5;
            color: #ffffff !important;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(35, 6, 197, 0.1);
        }

        .btn-nav-back:hover {
            background-color: #ffb703;
            color: #130101 !important;
            transform: translateY(-1px);
        }

        .btn-nav-logout {
            display: inline-block;
            background-color: transparent;
            color: #e74c3c !important;
            text-decoration: none;
            padding: 8px 18px;
            border: 1.5px solid #e74c3c;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .btn-nav-logout:hover {
            background-color: #e74c3c;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.2);
        }

        .archive-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h2 {
            font-size: 1.8rem;
            color: var(--text-color);
            margin-bottom: 20px;
            transition: color 0.3s ease;
        }

        /* Search Form Layout */
        .search-wrapper {
            background-color: var(--table-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .search-form {
            display: flex; 
            gap: 10px;
        }

        .search-input {
            flex: 1; 
            padding: 12px; 
            background-color: var(--input-bg); 
            color: var(--input-text); 
            border: 1px solid var(--input-border);
            border-radius: 6px;
            font-size: 0.95rem;
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        .btn-search {
            background-color: #2306c5;
            color: #ffffff;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-search:hover {
            background-color: #ffb703;
            color: #130101;
        }

        /* Modern Table Layout */
        .archive-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--table-bg);
            border: 1px solid var(--table-border);
            border-radius: 8px;
            overflow: hidden;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .archive-table th, .archive-table td {
            padding: 14px 15px;
            text-align: left;
            border: 1px solid var(--table-border);
            transition: border-color 0.3s ease;
        }

        .archive-table th {
            background-color: var(--table-th-bg);
            color: var(--table-th-text);
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .archive-table td {
            color: var(--text-color);
            transition: color 0.3s ease;
        }

        .id-link {
            color: #2306c5;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.2s ease;
        }

        .id-link:hover {
            color: #ffb703;
            text-decoration: underline;
        }
    </style>

<div class="admin-sub-nav">
    <div class="admin-title">Lifetime Customer Archive</div>
    <div class="admin-links">
        <a href="admin_dashboard.php" class="btn-nav-back">Back to Dashboard</a>
        <a href="logout.php" class="btn-nav-logout">Logout</a>
    </div>
</div>

<div class="archive-container">
    <div class="search-wrapper">
        <form action="" method="GET" class="search-form">
            <input type="text" name="search_term" class="search-input" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by Name or Phone...">
            <button type="submit" name="search" class="btn-search">Search</button>
        </form>
    </div>

    <h2>Booking Records</h2>
    <table class="archive-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Phone Number</th>
                <th>Room Booked</th>
                <th>Booking Date & Time</th>
                <th>ID Proof</th>
            </tr>
        </thead>
        <tbody>
            <?php if($archive_result->num_rows > 0): ?>
                <?php while($row = $archive_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                    <td>Room <?php echo $row['room_number']; ?></td>
                    <td><?php echo $row['booking_date']; ?></td>
                    <td>
                        <?php 
                            $file_name_only = basename($row['id_proof_path']); 
                        ?>
                        <a href="http://localhost/al_rahat_homestay/js/uploads/<?php echo rawurlencode($file_name_only); ?>" target="_blank" class="id-link">View Uploaded ID</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; color: var(--text-muted);">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>