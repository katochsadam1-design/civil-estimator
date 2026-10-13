<?php
// Include database connection
include('config/db.php'); 

$message = "";
$message_class = "";
$valid_token = false;

// 1. Check if token exists in the URL
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $current_time = date("Y-m-d H:i:s");

    // Verify token and check if it has not expired
    $query = "SELECT * FROM admin WHERE reset_token = '$token' AND token_expire > '$current_time'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $valid_token = true;
        $admin_data = mysqli_fetch_assoc($result);
    } else {
        $message = "The reset link is invalid or has expired! Please request a new one.";
        $message_class = "alert-danger";
    }
} else {
    $message = "No token provided. Cannot reset password.";
    $message_class = "alert-danger";
}

// 2. Handle the new password submission
if (isset($_POST['reset_submit']) && $valid_token) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_username = $admin_data['username'];

    if ($new_password === $confirm_password) {
        
        // आपके hash.php के मुताबिक PASSWORD_BCRYPT का उपयोग किया है
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $hashed_password_escaped = mysqli_real_escape_string($conn, $hashed_password);

        // Update password in database
        $update_query = "UPDATE admin SET password = '$hashed_password_escaped', reset_token = NULL, token_expire = NULL WHERE username = '$admin_username'";
        
        if (mysqli_query($conn, $update_query)) {
            $message = "Password updated successfully! You can now <a href='admin_login.php' class='alert-link'>Login</a>.";
            $message_class = "alert-success";
            $valid_token = false; 
        } else {
            $message = "Something went wrong while updating the password.";
            $message_class = "alert-danger";
        }
    } else {
        $message = "Passwords do not match!";
        $message_class = "alert-danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Alrath Homestay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .reset-card { max-width: 450px; margin-top: 100px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="card reset-card w-100 p-4 rounded-3 bg-white">
        <h3 class="text-center mb-4 text-dark">Reset Password</h3>

        <?php if(!empty($message)): ?>
            <div class="alert <?php echo $message_class; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if($valid_token): ?>
            <form action="reset_password.php?token=<?php echo htmlspecialchars($_GET['token']); ?>" method="POST">
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Enter new password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm new password" required>
                </div>
                <button type="submit" name="reset_submit" class="btn btn-success w-100 py-2">Update Password</button>
            </form>
        <?php endif; ?>
        
        <div class="text-center mt-3">
            <a href="admin_login.php" class="text-decoration-none small">Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>