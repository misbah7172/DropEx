<?php
session_start();
include("db_connect.php");

// Check if staff is logged in
if(!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

date_default_timezone_set('Asia/Kolkata');
$date = date('d-m-Y');
$time = date('h:i:s a');

// Get the parcel details using the tid from session
if(isset($_SESSION['tid'])) {
    $tid = $_SESSION['tid'];
    $sql = "SELECT * FROM parcel WHERE TrackingID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $tid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
        // Clear the tid from session after fetching
        unset($_SESSION['tid']);
    } else {
        $_SESSION['error_message'] = 'Receipt data not found';
        header("Location: staff_request_approval.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = 'No receipt to generate';
    header("Location: staff_request_approval.php");
    exit();
}

if(isset($_POST['back'])) {
    header("Location: staff_request_approval.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>DropEx - Receipt</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="style/bootstrap.css">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="style/index_styles.css">
        <link rel="icon" type="image/png" sizes="32x32" href="Images/blank.png">
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
            @media print {
                .no-print {
                    display: none;
                }
                .container {
                    margin: 0 !important;
                    padding: 20px !important;
                }
                .background {
                    display: none;
                }
            }
        </style>
    </head>
    <body style="font-family: Arial, Helvetica, sans-serif;">
        <div class="no-print"><img src="Images/logo.png" id="logo" style="height: 100px !important; margin-top: 10px !important;"></div>
        <div class="background"></div>  
        <div class="container p-5" style="background-color: rgba(255, 255, 255, 0.7); margin-top: 10px !important">
            <h2 class="display-4 text-center" style="border-bottom: 2px solid black; margin-bottom:15px !important;">Receipt</h2>
            
            <div class="row">
                <div class="col-md-6">
                    <p><span class="font-weight-bold">Date : </span><?php echo $date; ?></p>
                    <p><span class="font-weight-bold">Time : </span><?php echo $time; ?></p>
                </div>
                <div class="col-md-6">
                    <p><span class="font-weight-bold">Tracking ID : </span><?php echo $order['TrackingID']; ?></p>
                    <p><span class="font-weight-bold">Staff ID : </span><?php echo $order['StaffID']; ?></p>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Sender Details:</h5>
                    <p><?php echo $order['S_Name']; ?><br>
                    <?php echo $order['S_Add']; ?><br>
                    <?php echo $order['S_City'] . ', ' . $order['S_State']; ?><br>
                    Contact: <?php echo $order['S_Contact']; ?></p>
                </div>
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Receiver Details:</h5>
                    <p><?php echo $order['R_Name']; ?><br>
                    <?php echo $order['R_Add']; ?><br>
                    <?php echo $order['R_City'] . ', ' . $order['R_State']; ?><br>
                    Contact: <?php echo $order['R_Contact']; ?></p>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <p><span class="font-weight-bold">Weight : </span><?php echo $order['Weight_Kg']; ?> KG</p>
                </div>
                <div class="col-md-6">
                    <p><span class="font-weight-bold">Price : </span>Rs <?php echo $order['Price']; ?></p>
                </div>
            </div>

            <div class="row mt-4 no-print">
                <div class="col-12 text-center">
                    <form method="POST" action="">        
                        <input type="submit" name="back" value="Back" class="btn btn-dark mr-2">
                        <button type="button" onclick="window.print()" class="btn btn-info">Print</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="container-fluid text-center mt-5 no-print" style="background-color: rgba(255, 255, 255, 0.7); padding: 20px; position: relative;">
            <div class="i-bar" style="display: flex; flex-direction: row; flex-wrap: wrap; justify-content:center; margin-bottom: 2em;">
                <a class="fa fa-facebook" href="#" style="border: none; text-decoration: none; margin: 0em 1em; color:black; font-size: xx-large;"></a>
                <a class="fa fa-instagram" href="#" style="border: none; text-decoration: none; margin: 0em 1em; color:black; font-size: xx-large;"></a>
                <a class="fa fa-envelope" href="#" style="border: none; text-decoration: none; margin: 0em 1em; color:black; font-size: xx-large;"></a>
            </div>
            <p class="credit" style="font-size: 20px; font-stretch: 3px; text-align: center; color: black;">©2025 DropEx.| Delivering Beyond Borders</p>
        </div>
    </body>
</html>