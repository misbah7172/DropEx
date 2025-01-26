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
    <script src="https://cdn.tailwindcss.com"></script>
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

    <div class="min-h-screen pt-16 px-4">
        <div class="max-w-md mx-auto bg-white/90 backdrop-blur-md rounded-xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <h4 class="text-2xl font-bold text-gray-800">DropEx User Portal</h4>
            </div>
            
            <?php if($error): ?>
                <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-700"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="flex space-x-4 mb-6">
                <button onclick="showLogin()" class="flex-1 py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">Login</button>
                <button onclick="showRegister()" class="flex-1 py-2 px-4 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition duration-200">Register</button>
            </div>
            
            <!-- Login Form -->
            <form method="POST" action="" id="loginForm" class="space-y-4">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">Email</label>
                    <input type="email" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="email" name="email" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">Password</label>
                    <input type="password" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="password" name="password" required>
                </div>
                <button type="submit" name="login" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">Login</button>
                <div class="space-y-2">
                    <a href="admin_login.php" class="block w-full py-2 px-4 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-center transition duration-200">Admin Login</a>
                    <a href="login.php" class="block w-full py-2 px-4 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-center transition duration-200">Staff Login</a>
                </div>
            </form>
            
            <!-- Registration Form -->
            <form method="POST" action="" id="registerForm" class="space-y-4" style="display: none;">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="reg_name">Full Name</label>
                    <input type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="reg_name" name="reg_name" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="reg_username">Username</label>
                    <input type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="reg_username" name="reg_username" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="reg_email">Email</label>
                    <input type="email" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="reg_email" name="reg_email" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="reg_password">Password</label>
                    <input type="password" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="reg_password" name="reg_password" required>
                </div>
                <button type="submit" name="register" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">Register</button>
            </form>
        </div>
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
