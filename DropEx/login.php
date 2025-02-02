<?php 
    // Start output buffering to prevent headers already sent error
    ob_start();
    
    // Start session first
    session_start();
    
    // Include database connection
    require_once "db_connect.php";
    
    // Redirect if session already exists
    if(isset($_SESSION['id'])) {
        header("Location: staff.php");
        exit();
    }
    
    $id = $pass = '';
    $errors = array('id' => '', 'pass' => '', 'login' => '');

    if(isset($_POST['submit'])){
        // Validate Staff ID
        if(empty($_POST['id'])){
            $errors['id'] = "*Required";
        } else {
            $id = trim($_POST['id']);
        }

        // Validate Password
        if(empty($_POST['pass'])){
            $errors['pass'] = "*Required";
        } else {
            $pass = trim($_POST['pass']);
        }

        // If no empty field errors, proceed with login attempt
        if(!$errors['id'] && !$errors['pass']) {
            $id = mysqli_real_escape_string($conn, $id);
            $pass = mysqli_real_escape_string($conn, $pass);

            // Check staff table
            $sql = "SELECT * FROM staff WHERE StaffID=? AND pass=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $id, $pass);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) > 0){
                $user = mysqli_fetch_assoc($result);
                $_SESSION['id'] = $user['StaffID'];
                header("Location: staff.php");
                exit();
            } else {
                // Check if StaffID exists
                $sql = "SELECT * FROM staff WHERE StaffID=?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if(mysqli_num_rows($result) == 0){
                    $errors['login'] = 'Invalid Staff ID';
                } else {
                    $errors['login'] = 'Incorrect Password';
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DropEx Staff Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-white/70 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="index.php" class="flex-shrink-0">
                        <img class="h-12" src="Images/logo.png" alt="DropEx Logo">
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Home</a>
                    <a href="tracking.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Tracking</a>
                    <a href="branches.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Branches</a>
                    <a href="login.php" class="text-blue-600 font-semibold px-3 py-2">DropEx Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login Container -->
    <div class="flex grow items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-gray-800/70 p-8 rounded-xl shadow-2xl">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-white">DropEx Login</h2>
                <p class="mt-2 text-gray-300">Please login to continue</p>
            </div>

            <form class="mt-8 space-y-6" method="POST">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="mb-4">
                        <label for="id" class="text-white block mb-2">Staff ID ( Try This: DE8888 )</label>
                        <input 
                            id="id" 
                            name="id" 
                            type="text" 
                            value="<?php echo htmlspecialchars($id)?>"
                            class="appearance-none rounded-md relative block w-full px-3 py-2 bg-gray-700 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                        <?php if($errors['id']): ?>
                            <p class="text-red-400 text-sm mt-1"><?php echo $errors['id']; ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="pass" class="text-white block mb-2">Password ( Try this : 1234 )</label>
                        <input 
                            id="pass" 
                            name="pass" 
                            type="password" 
                            class="appearance-none rounded-md relative block w-full px-3 py-2 bg-gray-700 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                        <?php if($errors['pass']): ?>
                            <p class="text-red-400 text-sm mt-1"><?php echo $errors['pass']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($errors['login']): ?>
                    <div class="bg-red-500 text-white p-3 rounded">
                        <?php echo $errors['login']; ?>
                    </div>
                <?php endif; ?>

                <div>
                    <button 
                        type="submit" 
                        name="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Staff Sign In
                    </button>
                </div>
            </form>

            <div class="space-y-3">
                <a 
                    href="admin_login.php" 
                    class="w-full inline-flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-500 hover:bg-red-600"
                >
                    Admin Login
                </a>
                <a 
                    href="user_login.php" 
                    class="w-full inline-flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-500 hover:bg-green-600"
                >
                    User Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
<?php 
mysqli_close($conn);
ob_end_flush(); 
?>
