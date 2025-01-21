<?php 
    session_start();
    
    // Redirect if session already exists
    if(isset($_SESSION['user_id'])) {
        header("Location: userlogin.php");
        exit();
    }
    
    include("db_connect.php");
    
    $user_id = $pass = '';
    $errors = array('user_id' => '', 'pass' => '', 'login' => '');

    if(isset($_POST['submit'])){
        if(empty($_POST['user_id'])){
            $errors['user_id'] = "*Required";
        }else{
            $user_id = $_POST['user_id'];
        }
        if(empty($_POST["pass"])){
            $errors['pass'] = "*Required";
        }else{
            $pass = $_POST['pass'];
        }
        if(array_filter($errors)){
            //echo errors
        }else{
            $user_id = mysqli_real_escape_string($conn, $user_id);
            $pass = mysqli_real_escape_string($conn, $pass);

            $sql = "SELECT * FROM staff WHERE StaffID='$user_id' AND pass='$pass'";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0){
                $user = mysqli_fetch_assoc($result);
                session_start();
                $_SESSION['user_id'] = $user['StaffID'];
                header("Location: user_dashboard.php");
            }else{
                $sql = "SELECT * FROM credentials WHERE StaffID='$id'";
                $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) == 0){
                    $errors['login'] = 'Enter valid Staff ID';
                }else{
                    $user = mysqli_fetch_assoc($result);
                    if($pass != $user['pass']){
                        $errors['login'] = 'Incorrect Password';
                    }
                }
            }
        }
        
    }
    mysqli_close($conn);

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
                <a class="nav-item nav-link text-dark mr-5 " href="index.php">Home</a>
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
            <form class="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <h6 style="color:#fff; margin-bottom:20px;">Staff Login</h6>
            <div class="form-group text-left">
                <label style="font-size: 16px; color: #fff;">Staff ID : </label><br>
                <input type="text" class="form-control" style="border-radius: 8px; padding-left: 10px;" name="id" value="<?php echo htmlspecialchars($user_)?>">
                <label class="text-danger"><?php echo $errors['user_id'];?></label>
            </div>
            <div class="form-group text-left">
                <label style="font-size: 16px; color: #fff;">Password : </label><br>
                <input type="password" class="form-control" style="border-radius: 8px; padding-left: 10px;" name="pass" value="<?php echo htmlspecialchars($pass)?>">
                <label class="text-danger"><?php echo $errors['pass'];?></label>
            </div>
            <label class="text-danger"><?php echo $errors['login'];?></label>id
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
