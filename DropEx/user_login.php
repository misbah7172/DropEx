<?php
session_start();
require_once 'config.php';

// Check if already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: user_dashboard.php");
    exit;
}

$error = '';
$success = '';

// Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['name'] = $row['name'];
            
            // Update last login
            $update_sql = "UPDATE users SET last_login = NOW() WHERE id = " . $row['id'];
            mysqli_query($conn, $update_sql);
            
            header("Location: user_dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Invalid email or password";
    }
}

// Handle Registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['reg_username']);
    $email = mysqli_real_escape_string($conn, $_POST['reg_email']);
    $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);
    $name = mysqli_real_escape_string($conn, $_POST['reg_name']);
    
    $check_sql = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Email or username already exists!";
    } else {
        $sql = "INSERT INTO users (username, email, password, name) VALUES ('$username', '$email', '$password', '$name')";
        if (mysqli_query($conn, $sql)) {
            $success = "Registration successful! Please login.";
        } else {
            $error = "Registration failed! Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DropEx Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('Images/DropExBack.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-form {
            max-width: 400px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-toggle {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <div class="text-center mb-4">
            <h4>DropEx User Portal</h4>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="form-toggle">
            <button class="btn btn-primary" onclick="showLogin()">Login</button>
            <button class="btn btn-secondary" onclick="showRegister()">Register</button>
        </div>
        
        <!-- Login Form -->
        <form method="POST" action="" id="loginForm">
            <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            <div class="mt-3">
            <a href="admin_login.php" class="btn btn-secondary w-100 mb-2">Admin Login</a>
            <a href="login.php" class="btn btn-secondary w-100">Staff Login</a>
            </div>
        </form>
        
        <!-- Registration Form -->
        <form method="POST" action="" id="registerForm" style="display: none;">
            <div class="mb-3">
                <label for="reg_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="reg_name" name="reg_name" required>
            </div>
            <div class="mb-3">
                <label for="reg_username" class="form-label">Username</label>
                <input type="text" class="form-control" id="reg_username" name="reg_username" required>
            </div>
            <div class="mb-3">
                <label for="reg_email" class="form-label">Email</label>
                <input type="email" class="form-control" id="reg_email" name="reg_email" required>
            </div>
            <div class="mb-3">
                <label for="reg_password" class="form-label">Password</label>
                <input type="password" class="form-control" id="reg_password" name="reg_password" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
        </form>
    </div>

    <script>
        function showLogin() {
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('registerForm').style.display = 'none';
        }
        
        function showRegister() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        }
    </script>
</body>
</html>