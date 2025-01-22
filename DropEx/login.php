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
<html>
    <head>
        <title>Drop Ex</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="style/bootstrap.css">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="style/index_styles.css">
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body style="font-family: Arial, Helvetica, sans-serif;">
        <nav class="navbar navbar-toggleable-md navbar-expand-lg navbar-default navbar-light mb-10" style="background-color: rgba(255, 255, 255, 0.7); margin-bottom: 20px; margin-top:10px !important;">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="Images/logo.png" id="logo" style="height: 50px !important; margin-top: 10px !important;">
                </a>
                <button class="navbar-toggler text-dark" data-toggle="collapse" data-target="#mainNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <div class="navbar-nav ml-auto" style="font-size: large;">
                        <a class="nav-item nav-link text-dark mr-5" href="index.php">Home</a>
                        <a class="nav-item nav-link text-dark mr-5" href="tracking.php">Tracking</a>
                        <a class="nav-item nav-link text-dark mr-5" href="branches.php">Branches</a>
                        <a class="nav-item nav-link text-dark active" href="login.php">DropEx Login</a>                        
                    </div>
                </div>
            </div>
        </nav>
        
        <div class="container text-center p-3" style="background-color: rgba(10, 33, 61, 0.61); padding: 20px; border-radius: 8px; max-width: 300px;">
            <div class="login-header">
                <h6 style="color:rgb(255, 255, 255);">DropEx Login</h6>
                <p style="color:rgb(194, 194, 194);">Please login to continue</p>
            </div>
            
            <!-- Staff Login Form -->
            <form class="form" method="POST">
                <h6 style="color:#fff; margin-bottom:20px;">Staff Login</h6>
                <div class="form-group text-left">
                    <label style="font-size: 16px; color: #fff;">Staff ID : </label><br>
                    <input type="text" class="form-control" style="border-radius: 8px; padding-left: 10px;" name="id" value="<?php echo htmlspecialchars($id)?>">
                    <label class="text-danger"><?php echo $errors['id'];?></label>
                </div>
                <div class="form-group text-left">
                    <label style="font-size: 16px; color: #fff;">Password : </label><br>
                    <input type="password" class="form-control" style="border-radius: 8px; padding-left: 10px;" name="pass">
                    <label class="text-danger"><?php echo $errors['pass'];?></label>
                </div>
                <?php if($errors['login']): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $errors['login']; ?>
                    </div>
                <?php endif; ?>
                <input type="submit" name="submit" class="btn btn-primary text-center" value="Staff Sign In" style="font-size: 16px; width: 100%; border-radius: 8px; margin-bottom:20px;">
            </form>

            <!-- Admin Login Button -->
            <a href="admin_login.php" class="btn btn-danger text-center" style="font-size: 16px; width: 100%; border-radius: 8px; margin-bottom:10px;">
                Admin Login
            </a>

            <!-- User Login Button -->
            <a href="user_login.php" class="btn btn-success text-center" style="font-size: 16px; width: 100%; border-radius: 8px;">
                User Login
            </a>
        </div>
    </body>
</html>
<?php 
mysqli_close($conn);
ob_end_flush(); 
?>
