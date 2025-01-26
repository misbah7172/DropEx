<?php
// ... PHP code remains the same ...
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DropEx Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[url('Images/admin.jpg')] bg-cover bg-center bg-no-repeat flex items-center justify-center">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md shadow-lg z-50">
if (mysqli_num_rows($result) == 1) {
 $row = mysqli_fetch_assoc($result);
 $_SESSION['admin_id'] = $row['admin_id'];
 $_SESSION['admin_name'] = $row['name'];
// Update last login
 $update_sql = "UPDATE admin_credentials SET last_login = NOW() WHERE admin_id = '$admin_id'";
mysqli_query($conn, $update_sql);
header("Location: adminDashboard.php");
exit;
 } else {
 $error = "Invalid admin ID or password";
 }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>DropEx Admin Login</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
 <style>
body {
background: url('Images/admin.jpg') no-repeat center center fixed;
background-size: cover;
height: 100vh;
display: flex;
justify-content: center;
align-items: center;
 }
.login-form {
max-width: 400px;
padding: 20px;
background:rgba(10, 33, 61, 0.61); /* Changed background color */
border-radius: 8px;
box-shadow: 0 0 20px rgba(0,0,0,0.1);
 }
.login-header {
text-align: center;
margin-bottom: 20px;
 }
.login-header h2 {
color: #333;
margin-bottom: 10px;
 }
.login-header p {
color: #777;
 }
.btn-primary {
background: #007bff;
border: none;
 }
.btn-primary:hover {
background: #0056b3;
 }
</style>
</head>
<body>
 <div class="login-form">
 <div class="login-header">
 <h6 style="color:rgb(255, 255, 255);">DropEx Admin Forum</h6>
 <p style="color:rgb(194, 194, 194);">Please login to continue</p>
 </div>
<?php if(isset($error)): ?>
 <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md shadow-lg z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="index.php" class="flex-shrink-0">
                        <img class="h-12 w-auto" src="Images/logo.png" alt="DropEx Logo">
                    </a>
                </div>
            </div>
        </div>
    </nav>
 <form method="POST" action="">
 <div class="mb-3">
 <label for="admin_id" class="form-label"style="color: white;">Admin ID</label>
 <input type="text" class="form-control" id="admin_id" name="admin_id" required>
 </div>
 <div class="mb-3">
 <label for="password" class="form-label"style="color: white;">Password</label>
 <input type="password" class="form-control" id="password" name="password" required>
 </div>
 <div class="mb-3">
 <button type="submit" class="btn btn-primary w-100">Login</button>
 </div>
 </form>
<!-- Admin Login Button -->
 <div class="mb-3">
 <a href="login.php" class="btn btn-danger w-100" style="font-size: 16px; border-radius: 8px;">
 Staff Login
 </a>
 </div>
<!-- User Login Button -->
 <div class="mb-3">
 <a href="user_login.php" class="btn btn-success w-100" style="font-size: 16px; border-radius: 8px;">
 User Login
 </a>
 </div>
 </div>
</body>
</html>
