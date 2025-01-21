<?php
    session_start();
    include("db_connect.php");
    $tid = '';
    $error = '';
    $status = array('Dispatched' => '','Shipped' => '', 'Out_for_delivery' => '', 'Delivered' => '', );
    $hide = 'hidden';
    session_start();
    $trackid = '';
    if(isset($_POST['track'])){
        if(empty($_POST['tid'])){
            $error = "*Required";
        }else{
            $tid = $_POST['tid'];
            $_SESSION['track_tid'] = $tid;
            if(empty($error)){
                $hide = '';
                $trackid = $_SESSION['track_tid'];
                $sql = "SELECT * FROM status WHERE TrackingID='$tid'";
                $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) > 0){
                    $status = mysqli_fetch_assoc($result);
                    $active = array();
                    if(! is_null($status['Delivered'])){
                        $active['Delivered'] = $active['Out_for_delivery'] = $active['Shipped'] = 'active';
                    }elseif(! is_null($status['Out_for_delivery'])){
                        $active['Delivered'] = '';
                        $active['Out_for_delivery'] = $active['Shipped'] = 'active';
                    }elseif(! is_null($status['Shipped'])){
                        $active['Delivered'] = $active['Out_for_delivery'] = '';
                        $active['Shipped'] = 'active';
                    }
                }else{
                    $error = "Invalid Tracking ID";
                }
            }
        }
    }
    $hidden = 'hidden';
    if(isset($_POST['view'])){
        $trackid = $_SESSION['track_tid'];
        $hidden = $hide = '';
    } 
    $name = $add = $contact = '';
    $errors = array('name' => '', 'add' => '', 'cont' => '');
    if(isset($_POST['update'])){
        $hidden = $hide = '';
        $trackid = $_SESSION['track_tid'];
        if(empty($_POST['fname'])){
            $errors['name'] = "*Required";
        }else{
            $name = $_POST['fname'];
        }
        if(empty($_POST['fadd'])){
            $errors['add'] = "*Required";
        }else{
            $add = $_POST['fadd'];
        }
        if(empty($_POST['fcontact'])){
            $errors['cont'] = "*Required";
        }else{
            $contact = $_POST['fcontact'];
        }
        if(! array_filter($errors)){
            $trackid = $_SESSION['track_tid'];
            $sql = "UPDATE parcel SET R_Name = '$name', R_Add = '$add', R_Contact = $contact WHERE TrackingID = $trackid";
            if(mysqli_query($conn, $sql)){
                echo '<script type="text/javascript">';
                echo "setTimeout(function () { swal('Address Updated', 'Receiver address updated successfully !!', 'success');";
                echo '}, 1000);</script>';
                $hide  = $hidden =  'hidden';
                $trackid = '';
            }else{
                echo 'Update Error : '.mysqli_error($conn);
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Drop EX</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="style/bootstrap.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="style/index_styles.css">
       <!-- <link rel="icon" type="image/png" sizes="32x32" href="Images/blank.png"> -->
       <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body style="font-family: Arial, Helvetica, sans-serif;">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <nav class="navbar navbar-toggleable-md navbar-expand-lg navbar-default navbar-light mb-10" style="background-color: rgba(255, 255, 255, 0.7); margin-bottom: 15px; margin-top:10px !important;">
                                <div class="container">
                                    <a class="navbar-brand" href="#">
                                        <img src="Images/logo.png" id="logo" style="height: 50px !important; margin-top: 10px !important;">
                                    </a>
                                    <button class="navbar-toggler text-dark" data-toggle="collapse" data-target="#mainNav">
                                        <span class="navbar-toggler-icon"></span>
                                    </button>
                                    <div class="collapse navbar-collapse" id="mainNav">
                                        <div class="navbar-nav ml-auto" style="font-size: large;">
                                            <a class="nav-item nav-link text-dark mr-5" href="user_dashboard.php">Dashboard</a>
                                            <span class="nav-item nav-link text-dark mr-3">
                                                Welcome, <?php echo htmlspecialchars($user_name); ?>
                                            </span>
                                            <a class="nav-item nav-link btn-logout" href="user_logout.php">Logout</a>
                                        </div>
                                    </div>
                                </div>
                            </nav>
                        <?php else: ?>
                            <nav class="navbar navbar-toggleable-md navbar-expand-lg navbar-default navbar-light mb-10" style="background-color: rgba(255, 255, 255, 0.7); margin-bottom: 15px; margin-top:10px !important;">
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
                                                <a class="nav-item nav-link text-dark mr-5 active" href="tracking.php">Tracking</a>
                                                <a class="nav-item nav-link text-dark mr-5" href="branches.php">Branches</a>
                                                <?php if(isset($_SESSION['id'])): ?>
                                                    <a class="nav-item nav-link text-dark mr-3" href="staff.php">Dashboard</a>
                                                    <a class="nav-item nav-link btn-logout" href="logout.php">Logout</a>
                                                <?php else: ?>
                                                    <a class="nav-item nav-link text-dark" href="login.php">DropEx Login</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </nav>
                        <?php endif; ?>
        <div class="container mt-10">
            <div class="row">
                <div class="col-md-4 p-4 text-center pt-0" style="background-color: rgba(255, 255, 255, 0.7); margin-top: 20px;">
                    <img src="Images/track.png" style="margin:0 auto; height: 250px; border-radius: 15px;">
                    <form action="" method="POST" class="form">
                        <div class="form-group">
                            <label style="font-size: 20px;">Tracking ID : </label>
                            <input type="text" class="form-control" style="border-radius: 8px;" name="tid" value="<?php echo $tid; ?>">
                            <label class="text-danger"><?php echo $error; ?></label>
                        </div>
                        <input type="submit" name="track" class="btn btn-light text-center" value="Track" style="font-size: 20px;">
                    </form>
                </div>
                <div class="col-md-8 p-4 " style="background-color: rgba(255, 255, 255, 0.7); margin-top: 20px;">
                    <h3 class="display-6 text-center pb-2 mb-3" style="border-bottom: 2px solid black;">Delivery Status</h3>
                    <label>Tracking ID : <?php echo $trackid; ?></label><br>
                    <div class="track bg-info">
                        <div class="step active"> <span class="icon"> <i class="fa fa-map-marker"></i> </span> <span class="text font-weight-bold"> Received </span><span><?php echo $status['Dispatched'];?></span> </div>
                        <div class="step <?php echo $active['Shipped']; ?>"> <span class="icon"> <i class="fa fa-truck"></i> </span> <span class="text font-weight-bold"> On the way </span><span><?php echo $status['Shipped'];?></span> </div>
                        <div class="step <?php echo $active['Out_for_delivery']; ?>"> <span class="icon"> <i class="fa fa-cubes"></i> </span> <span class="text font-weight-bold"> Out for delivery </span><span><?php echo $status['Out_for_delivery'];?></span> </div>
                        <div class="step <?php echo $active['Delivered']; ?>"> <span class="icon"> <i class="fa fa-check"></i> </span> <span class="text font-weight-bold">Delivered</span><span><?php echo $status['Delivered'];?></span> </div>
                    </div>
                    <div <?php echo $hide; ?>>
                        <br>
                        <label>Unable to receive on the expected date?</label>
                        <form action="tracking.php" method="POST">
                            <label>Drop to a friend nearby in the your city.</label>
                            <input type="submit" name="view" value="Update Delivery Address" class="btn btn-info">
                        </form>
                        <form action="tracking.php" method="POST" <?php echo $hidden; ?>>
                            <label>Friend's Details</label>
                            <div class="form-group text-left">
                                <label>Name : </label>
                                <input type="text" class="form-control" name="fname" style="border-radius: 8px;">
                                <label class="text-danger"><?php echo $errors['name'];?></label>
                            </div>
                            <div class="form-group text-left">
                                <label>Address : </label>
                                <input type="text" class="form-control" name="fadd" style="border-radius: 8px;">
                                <label class="text-danger"><?php echo $errors['add'];?></label>
                            </div>
                            <div class="form-group text-left">
                                <label>Contact : </label>
                                <input type="text" class="form-control" name="fcontact" style="border-radius: 8px;" >
                                <label class="text-danger"><?php echo $errors['cont'];?></label>
                            </div>
                            <input type="submit" name="update" value="Update" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <style>
         footer {
            text-align: center;
            padding: 20px;
            background: #0056b3;
            color: #fff;
            margin-top: 20px;
        }

        footer p {
            margin: 0;
            font-size: 0.9em;
        }
    </style>
    <footer>
        <p>&copy; 2025 DropEx. All Rights Reserved. | Delivering Beyond Borders</p>
    </footer>
    </body>
</html>