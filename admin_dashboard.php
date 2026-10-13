<?php
session_start();
// 1. Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// 2. Include database connection
include('config/db.php');

$message = "";

// --- Handle Live Updates directly from Dashboard ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Update Room Rent (Using your original form action)
    if (isset($_POST['update_rates']) && isset($_POST['room_rate'])) {
        if (isset($_POST['room_number'])) {
            $room_number = mysqli_real_escape_string($conn, $_POST['room_number']);
            $new_rate = (float)$_POST['room_rate'];
            
            $update_room = "UPDATE rooms SET rate = '$new_rate' WHERE room_number = '$room_number'";
            if (mysqli_query($conn, $update_room)) {
                $message = "Room " . htmlspecialchars($room_number) . " rent updated successfully!";
            }
        }
    }
    
    // Update Meal Prices (Using your original update_rates handle)
    if (isset($_POST['update_rates']) && isset($_POST['meal_type'])) {
        $meal_type = mysqli_real_escape_string($conn, $_POST['meal_type']);
        $new_meal_price = (float)$_POST['meal_price'];
        
        $update_meal = "UPDATE meal_rates SET price = '$new_meal_price' WHERE meal_name = '$meal_type'";
        if (mysqli_query($conn, $update_meal)) {
            $message = htmlspecialchars($meal_type) . " rates updated successfully!";
        }
    }

    // NEW FEATURE: Handle Manual Room Status Toggle Action
    if (isset($_POST['change_status'])) {
        $room_number = mysqli_real_escape_string($conn, $_POST['room_number']);
        $new_status = mysqli_real_escape_string($conn, $_POST['room_status']);

        $update_status_query = "UPDATE rooms SET status = '$new_status' WHERE room_number = '$room_number'";
        if (mysqli_query($conn, $update_status_query)) {
            $message = "Room " . htmlspecialchars($room_number) . " status manually set to " . htmlspecialchars($new_status) . "!";
            
            // If setting back to Available, automatically mark active pending bookings as Cancelled
            if ($new_status == 'Available') {
                $cancel_booking = "UPDATE bookings SET status = 'Cancelled' WHERE room_number = '$room_number' AND status = 'payment_pending'";
                mysqli_query($conn, $cancel_booking);
            }
        }
    }
}

// --- Live Database Fetch for Meal Rates ---
$meals_live = [];
$meal_query = "SELECT * FROM meal_rates";
$meal_res = mysqli_query($conn, $meal_query);
if ($meal_res && mysqli_num_rows($meal_res) > 0) {
    while ($m_row = mysqli_fetch_assoc($meal_res)) {
        $meals_live[$m_row['meal_name']] = $m_row['price'];
    }
}

// Default fallbacks if DB returns empty for meal rates
$veg_bf_price = isset($meals_live['Breakfast Veg']) ? $meals_live['Breakfast Veg'] : 100;
$non_veg_bf_price = isset($meals_live['Breakfast Non-Veg']) ? $meals_live['Breakfast Non-Veg'] : 150;
$veg_dn_price = isset($meals_live['Dinner Veg']) ? $meals_live['Dinner Veg'] : 200;
$non_veg_dn_price = isset($meals_live['Dinner Non-Veg']) ? $meals_live['Dinner Non-Veg'] : 250;


// 3. Fetch monthly bookings count for the Chart 1 (Monthly Progress)
$months = [];
$booking_counts = [];

$query = "SELECT MONTHNAME(booking_date) as month_name, COUNT(*) as total_bookings 
          FROM bookings 
          WHERE YEAR(booking_date) = YEAR(CURDATE()) 
          GROUP BY MONTH(booking_date) 
          ORDER BY MONTH(booking_date) ASC";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $months[] = $row['month_name'];
        $booking_counts[] = (int)$row['total_bookings'];
    }
} else {
    $months = ['January', 'February', 'March', 'April', 'May', 'June'];
    $booking_counts = [5, 12, 8, 15, 20, 25];
}

$json_months = json_encode($months);
$json_bookings = json_encode($booking_counts);
$current_month_bookings = !empty($booking_counts) ? end($booking_counts) : 0;


// NEW DATA FEATURE: Fetch Room-wise Transactions Analytics for Chart 2
$room_labels = [];
$room_tx_counts = [];

$room_tx_query = "SELECT room_number, COUNT(*) as dynamic_tx FROM bookings GROUP BY room_number ORDER BY room_number ASC";
$room_tx_res = mysqli_query($conn, $room_tx_query);

if ($room_tx_res && mysqli_num_rows($room_tx_res) > 0) {
    while ($tx_row = mysqli_fetch_assoc($room_tx_res)) {
        $room_labels[] = "Room " . $tx_row['room_number'];
        $room_tx_counts[] = (int)$tx_row['dynamic_tx'];
    }
} else {
    $room_labels = ['Room 1', 'Room 2', 'Room 3', 'Room 4', 'Room 5'];
    $room_tx_counts = [10, 15, 7, 22, 14];
}

$json_rooms = json_encode($room_labels);
$json_room_tx = json_encode($room_tx_counts);

// Fetch all rooms for management section
$rooms_query = "SELECT * FROM rooms ORDER BY room_number ASC";
$rooms_result = mysqli_query($conn, $rooms_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Al Rahat Homestay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Premium Sidebar Design */
        .sidebar { background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%); min-height: 100vh; color: #fff; padding-top: 25px; box-shadow: 4px 0 10px rgba(0,0,0,0.1); }
        .sidebar h4 { font-size: 1.25rem; font-weight: 600; letter-spacing: 0.5px; }
        .sidebar-links { display: flex; flex-direction: column; gap: 4px; }
        .sidebar a { color: #b2bec3; text-decoration: none; padding: 14px 24px; display: block; font-weight: 500; transition: all 0.3s ease; border-left: 4px solid transparent; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background-color: rgba(255,255,255,0.05); border-left: 4px solid #2ecc71; text-shadow: 0 0 5px rgba(255,255,255,0.2); }
        .sidebar a.active { background-color: rgba(46, 204, 113, 0.15); color: #2ecc71; font-weight: 600; }
        
        /* Premium UI Elements Styling */
        .main-content { padding: 40px; }
        .welcome-badge { background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%); color: #ffffff; font-weight: 600; border-radius: 12px; padding: 10px 22px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid rgba(255,255,255,0.1); letter-spacing: 0.3px; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: all 0.3s ease; background: #ffffff; margin-bottom: 25px; }
        .card-custom:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .chart-container { background: #ffffff; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        
        /* Interactive Form Elements styling */
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.2); border-color: #2ecc71; }
        .btn-success { background-color: #2ecc71; border-color: #2ecc71; font-weight: 500; }
        .btn-success:hover { background-color: #27ae60; border-color: #27ae60; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 sidebar px-0">
            <div class="text-center mb-4 px-3">
                <h4 class="text-white"><i class="fa-solid fa-house-chimney text-success me-2"></i>Al Rahat Admin</h4>
                <hr class="opacity-25 mt-3">
            </div>
            <div class="sidebar-links">
                <a href="admin_dashboard.php" class="active"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
                <a href="#manage-rates-section"><i class="fa-solid fa-bed me-2"></i> Manage Rooms & Rent</a>
                <a href="admin_archive.php"><i class="fa-solid fa-box-archive me-2"></i> Admin Archive</a>
                <hr class="opacity-25 my-3 mx-3">
                <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
            </div>
        </div>

        <div class="col-md-9 col-lg-10 main-content">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-bold text-dark m-0">Overview & Analytics</h2>
                <span class="welcome-badge"><i class="fa-regular fa-user me-2 text-success"></i>Welcome Back, Admin</span>
            </div>

            <?php if(!empty($message)): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 border-0 shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-check me-2 text-success"></i> <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row mb-5">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="card card-custom bg-primary text-white h-100 border-0" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%) !important;">
                        <div class="card-body d-flex justify-content-between align-items-center p-4">
                            <div>
                                <h6 class="text-white-50 text-uppercase fw-bold mb-2 small">This Month Bookings</h6>
                                <h2 class="fw-bold mb-0"><?php echo $current_month_bookings; ?> Bookings</h2>
                            </div>
                            <div class="bg-white bg-opacity-10 p-3 rounded-circle">
                                <i class="fa-solid fa-calendar-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-custom text-white h-100 border-0" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%) !important;">
                        <div class="card-body d-flex justify-content-between align-items-center p-4">
                            <div>
                                <h6 class="text-white-50 text-uppercase fw-bold mb-2 small">System Status</h6>
                                <h2 class="fw-bold mb-0">Live / Active</h2>
                            </div>
                            <div class="bg-white bg-opacity-10 p-3 rounded-circle">
                                <i class="fa-solid fa-circle-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Graph Navigation -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="chart-container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="text-dark fw-bold m-0"><i class="fa-solid fa-chart-bar text-primary me-2"></i>Performance Charts</h5>
                            <ul class="nav nav-pills" id="chartTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="monthly-tab" data-bs-toggle="pill" data-bs-target="#monthly-panel" type="button" role="tab" aria-selected="true">Monthly Overview</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="room-tab" data-bs-toggle="pill" data-bs-target="#room-panel" type="button" role="tab" aria-selected="false">Room Analytics</button>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content" id="chartTabContent">
                            <div class="tab-pane fade show active" id="monthly-panel" role="tabpanel">
                                <canvas id="progressChart" style="max-height: 350px; width: 100%;"></canvas>
                            </div>
                            <div class="tab-pane fade" id="room-panel" role="tabpanel">
                                <canvas id="roomTxChart" style="max-height: 350px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="manage-rates-section">
                <!-- Room management column -->
                <div class="col-lg-7 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header bg-white py-3 fw-bold text-primary border-bottom-0 pt-4 px-4">
                            <i class="fa-solid fa-door-open me-2 text-primary"></i>Update Room Rates & Status (Live)
                        </div>
                        <div class="card-body p-0 pt-2">
                            <div class="table-responsive px-4 pb-3">
                                <table class="table align-middle table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Room No.</th>
                                            <th>Current Rent</th>
                                            <th>Live Control Toggle</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($rooms_result && mysqli_num_rows($rooms_result) > 0): ?>
                                            <?php while($room = mysqli_fetch_assoc($rooms_result)): ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge p-2 mb-1" style="background-color: #f1f5f9; color: #334155; border-radius: 6px;">Room <?php echo htmlspecialchars($room['room_number']); ?></span>
                                                        <br>
                                                        <?php if($room['status'] == 'Available'): ?>
                                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Available</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2">Booked</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <form action="" method="POST" class="d-flex align-items-center gap-1">
                                                            <div class="input-group input-group-sm" style="max-width: 140px;">
                                                                <span class="input-group-text">₹</span>
                                                                <input type="number" name="room_rate" class="form-control" value="<?php echo htmlspecialchars($room['rate']); ?>" required min="0">
                                                                <input type="hidden" name="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>">
                                                                <button class="btn btn-success" type="submit" name="update_rates">
                                                                    <i class="fa-solid fa-floppy-disk"></i>
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <form action="" method="POST" class="d-flex align-items-center gap-1">
                                                            <input type="hidden" name="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>">
                                                            <div class="input-group input-group-sm" style="max-width: 170px;">
                                                                <select name="room_status" class="form-select form-select-sm">
                                                                    <option value="Available" <?php echo ($room['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                                                                    <option value="Booked" <?php echo ($room['status'] == 'Booked') ? 'selected' : ''; ?>>Booked</option>
                                                                </select>
                                                                <button class="btn btn-success text-nowrap px-2 fw-semibold" type="submit" name="change_status">Update</button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">No rooms found in database.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meal Rates section with all 4 forms intact -->
                <div class="col-lg-5 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header bg-white py-3 fw-bold text-success border-bottom-0 pt-4 px-4">
                            <i class="fa-solid fa-utensils me-2 text-success"></i>Edit Meal Options Price
                        </div>
                        <div class="card-body p-4 pt-2">
                            
                            <!-- Form 1: Breakfast Veg -->
                            <form action="" method="POST" class="mb-4">
                                <div class="form-group">
                                    <label class="form-label fw-semibold text-secondary small">Veg Breakfast Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" name="meal_price" class="form-control" value="<?php echo htmlspecialchars($veg_bf_price); ?>" required min="0">
                                        <input type="hidden" name="meal_type" value="Breakfast Veg">
                                        <button class="btn btn-success" type="submit" name="update_rates">Update</button>
                                    </div>
                                </div>
                            </form>
                            <!-- Form 2: Breakfast Non-Veg -->
                            <form action="" method="POST" class="mb-4">
                                <div class="form-group">
                                    <label class="form-label fw-semibold text-secondary small">Non-Veg Breakfast Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" name="meal_price" class="form-control" value="<?php echo htmlspecialchars($non_veg_bf_price); ?>" required min="0">
                                        <input type="hidden" name="meal_type" value="Breakfast Non-Veg">
                                        <button class="btn btn-success" type="submit" name="update_rates">Update</button>
                                    </div>
                                </div>
                            </form>

                            <!-- Form 3: Dinner Veg -->
                            <form action="" method="POST" class="mb-4">
                                <div class="form-group">
                                    <label class="form-label fw-semibold text-secondary small">Dinner Veg Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" name="meal_price" class="form-control" value="<?php echo htmlspecialchars($veg_dn_price); ?>" required min="0">
                                        <input type="hidden" name="meal_type" value="Dinner Veg">
                                        <button class="btn btn-success" type="submit" name="update_rates">Update</button>
                                    </div>
                                </div>
                            </form>

                            <!-- Form 4: Dinner Non-Veg -->
                            <form action="" method="POST" class="mb-2">
                                <div class="form-group">
                                    <label class="form-label fw-semibold text-secondary small">Dinner Non-Veg Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" name="meal_price" class="form-control" value="<?php echo htmlspecialchars($non_veg_dn_price); ?>" required min="0">
                                        <input type="hidden" name="meal_type" value="Dinner Non-Veg">
                                        <button class="btn btn-success" type="submit" name="update_rates">Update</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Scripts section loaded at footer to ensure dashboard rendering stays fast -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Chart 1: Monthly Overview Dashboard Rendering
const chartLabels = <?php echo $json_months; ?>;
const chartData = <?php echo $json_bookings; ?>;

const ctx = document.getElementById('progressChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Total Bookings',
            data: chartData,
            backgroundColor: 'rgba(52, 152, 219, 0.7)', 
            borderColor: 'rgba(52, 152, 219, 1)',
            borderWidth: 1.5,
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true, ticks: { stepSize: 1, callback: function(value) { return value + ' Bookings'; } } }
        }
    }
});

// Chart 2: Dynamic Room Transaction Counter Performance
const roomLabels = <?php echo $json_rooms; ?>;
const roomTxData = <?php echo $json_room_tx; ?>;

const ctxRoom = document.getElementById('roomTxChart').getContext('2d');
new Chart(ctxRoom, {
    type: 'bar',
    data: {
        labels: roomLabels,
        datasets: [{
            label: 'Bookings Count',
            data: roomTxData,
            backgroundColor: 'rgba(46, 204, 113, 0.7)',
            borderColor: 'rgba(46, 204, 113, 1)',
            borderWidth: 1.5,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true, ticks: { stepSize: 1, callback: function(value) { return value + ' Bookings'; } } }
        }
    }
});
</script>
</body>
</html>