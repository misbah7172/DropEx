<?php 
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is staff
if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    // Store the intended destination in session if needed
    $_SESSION['redirect_url'] = $_SERVER['PHP_SELF'];
    header("Location: login.php");
    exit();
}

include("db_connect.php");
date_default_timezone_set('Asia/Dhaka');

// Verify staff exists in database
$id = $_SESSION['id'];
$sql = "SELECT * FROM staff WHERE StaffID=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    // Invalid staff ID - destroy session and redirect
    session_destroy();
    header("Location: login.php?error=invalid_staff");
    exit();
}

$staff = mysqli_fetch_assoc($result);
$name = $staff['Name'];

// Initialize variables
$sname = $sadd = $scity = $sstate = $scontact = $rname = $radd = $rcity = $rstate = $rcontact = $wgt = '';
$status = array('disp' => '', 'ship' => '', 'out' => '', 'del' => '');
$inp_tid = '';
$disable_del = $disable_out = $disable_ship = '';
$errors = array('req' => '');

if(isset($_POST['submit'])){
    if(empty($_POST['sname'])){
        $errors['req'] = '*Required Field';
    }else{
        $sname = $_POST['sname'];
    }
    if(empty($_POST['sadd'])){
        $errors['req'] = '*Required Field';
    }else{
        $sadd = $_POST['sadd'];
    }
    if(empty($_POST['scity'])){
        $errors['req'] = '*Required Field';
    }else{
        $scity = $_POST['scity'];
    }
    if(empty($_POST['sstate'])){
        $errors['req'] = '*Required Field';
    }else{
        $sstate = $_POST['sstate'];
    }
    if(empty($_POST['scontact'])){
        $errors['req'] = '*Required Field';
    }else{
        $scontact = $_POST['scontact'];
    }
    if(empty($_POST['rname'])){
        $errors['req'] = '*Required Field';
    }else{
        $rname = $_POST['rname'];
    }
    if(empty($_POST['radd'])){
        $errors['req'] = '*Required Field';
    }else{
        $radd = $_POST['radd'];
    }        
    if(empty($_POST['rcity'])){
        $errors['req'] = '*Required Field';
    }else{
        $rcity = $_POST['rcity'];
    }
    if(empty($_POST['rstate'])){
        $errors['req'] = '*Required Field';
    }else{
        $rstate = $_POST['rstate'];
    }
    if(empty($_POST['rcontact'])){
        $errors['req'] = '*Required Field';
    }else{
        $rcontact = $_POST['rcontact'];
    }
    if(empty($_POST['wgt'])){
        $errors['req'] = '*Required Field';
    }else{
        $wgt = $_POST['wgt'];
    }
    
    if(array_filter($errors)){
        //echo errors
    }else{
        $price = 0;
        $sql = "SELECT * FROM pricing WHERE State_1 = ? AND State_2 = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $sstate, $rstate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) > 0){
            $pricing = mysqli_fetch_assoc($result);
            $price = $pricing['Cost'] * $wgt;
            
            $sql = "INSERT INTO parcel (StaffID, S_Name, S_Add, S_City, S_State, S_Contact, 
                    R_Name, R_Add, R_City, R_State, R_Contact, Weight_Kg, Price) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssssssssdd", 
                $id, $sname, $sadd, $scity, $sstate, $scontact, 
                $rname, $radd, $rcity, $rstate, $rcontact, $wgt, $price);
            
            if(mysqli_stmt_execute($stmt)){
                $tid = mysqli_insert_id($conn);
                $_SESSION['tid'] = $tid;
                header("Location: receipt.php");
                exit();
            }else{
                echo "Error : " . mysqli_error($conn);
            }
        }else{
            echo '<script type="text/javascript">';
            echo "setTimeout(function () { swal('Service Not Available', 
                'We don't have an office in this area—our delivery team is too busy zooming around!', 'info');";
            echo '}, 1000);</script>';
        }
    }
}

if(isset($_POST['sel_order'])){
    if(empty($_POST['inp_tid'])){
        $errors['status'] = '*Required Field';
    }else{
        $inp_tid = htmlspecialchars($_POST['inp_tid']);
    }
    
    if (!empty($inp_tid)) {
        $sql = "SELECT * FROM status WHERE TrackingID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $inp_tid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result)){
            $del_status = mysqli_fetch_assoc($result);
            $status['disp'] = $del_status['Dispatched'];
            $status['ship'] = $del_status['Shipped'];
            $status['out'] = $del_status['Out_for_delivery'];
            $status['del'] = $del_status['Delivered'];
            $inp_tid = $del_status['TrackingID'];
            $_SESSION['up_tid'] = $inp_tid;
            
            // Set disable flags based on status
            if(!is_null($status['del'])){
                $disable_del = $disable_out = $disable_ship = "disabled";
            }elseif(!is_null($status['out'])){
                $disable_out = $disable_ship = "disabled";
            }elseif(!is_null($status['ship'])){
                $disable_ship = "disabled";
            }
            if(is_null($status['ship'])){
                $disable_del = $disable_out = "disabled";
            }elseif(is_null($status['out'])){
                $disable_del = "disabled";
            }
        }else{
            $errors['status'] = 'Enter a valid tracking ID';
        }
    }
}

if(isset($_POST['update']) && isset($_SESSION['up_tid'])){
    $checked = $_POST['status_upd'];
    $inp_tid = $_SESSION['up_tid'];
    
    $sql = "";
    switch($checked) {
        case 'delivered':
            $sql = "UPDATE status SET Delivered=CURRENT_TIMESTAMP WHERE TrackingID=?";
            break;
        case 'out_for_delivery':
            $sql = "UPDATE status SET Out_for_delivery=CURRENT_TIMESTAMP WHERE TrackingID=?";
            break;
        case 'shipped':
            $sql = "UPDATE status SET Shipped=CURRENT_TIMESTAMP WHERE TrackingID=?";
            break;
    }
    
    if(!empty($sql)) {
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $inp_tid);
        if(!mysqli_stmt_execute($stmt)){
            echo 'Error : '. mysqli_error($conn);
        }
    }
}

$sql = "SELECT * FROM arrived";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "Error with arrived view: " . mysqli_error($conn);
    die();
}
$arr = mysqli_fetch_all($result, MYSQLI_ASSOC);
echo "<!-- Number of arrived parcels: " . count($arr) . " -->";

// After the delivered view query
$sql = "SELECT * FROM delivered";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "Error with delivered view: " . mysqli_error($conn);
    die();
}
$delivered = mysqli_fetch_all($result, MYSQLI_ASSOC);
echo "<!-- Number of delivered parcels: " . count($delivered) . " -->";

// Let's also check the actual data
echo "<!-- Delivered data: " . print_r($delivered, true) . " -->";

?>
<!DOCTYPE html>
<html>
<head>
    <title>Drop Ex</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="style/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="style/index_styles.css">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        input[type="text"] {
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="font-sans bg-gray-100">    
        <!-- Navbar -->
        <nav class="bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg fixed w-full z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <!-- Logo Section -->
                    <div class="flex items-center space-x-3">
                        <a href="index.php" class="flex items-center group">
                            <img src="Images/logo.png" class="h-8 w-8 md:h-10 md:w-10 rounded-full transition-all duration-300 group-hover:scale-110" alt="DropEx Logo">
                            <span class="ml-2 md:ml-3 text-xl md:text-2xl font-bold text-white tracking-wider">DropEx</span>
                        </a>
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-4 lg:space-x-6">
                        <a href="index.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Home</a>
                        <a href="tracking.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Tracking</a>
                        <a href="branches.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Branches</a>
                        <?php
                        // Query to count pending requests specific to staff's state
                        $pendingCount = 0;
                        $countsql = "SELECT COUNT(*) as count 
                                    FROM online_request o
                                    JOIN staff s ON o.S_State = s.Branch 
                                    WHERE o.status='pending' AND s.StaffID = ?";
                        $stmt = mysqli_prepare($conn, $countsql);
                        mysqli_stmt_bind_param($stmt, "s", $_SESSION['id']);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        if($result) {
                            $row = mysqli_fetch_assoc($result);
                            $pendingCount = $row['count'];
                        }
                        ?>
                        <a class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium" href="staff_request_approval.php" style="color: black; position: relative;">
                            Pending Request
                            <?php if($pendingCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.7em; margin-left: -10px;">
                            <?php echo $pendingCount; ?>
                            </span>
                            <?php endif; ?>
                        </a>
                        <a href="account.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Account</a>
                        <a href="logout.php" class="text-red-200 hover:text-red-400 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Logout</a>
                    </div>

                    <!-- Staff Greeting -->
                    <div class="md:flex items-center">
                        <span class="text-white text-sm">
                            Welcome, <?php echo htmlspecialchars($name); ?>
                        </span>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <button id="mobile-menu-button" type="button" class="text-white focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden fixed inset-x-0 top-16 bg-blue-600 transform -translate-x-full transition-transform duration-300 ease-in-out">
                <div class="px-4 pt-4 pb-6 space-y-2">
                    <a href="index.php" class="block text-white hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium">Home</a>
                    <a href="tracking.php" class="block text-white hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium">Tracking</a>
                    <a href="branches.php" class="block text-white hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium">Branches</a>
                    <a class="block text-white hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium" href="staff_request_approval.php" style="color: black; position: relative;">
                            Pending Request
                            <?php if($pendingCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.7em; margin-left: -10px;">
                            <?php echo $pendingCount; ?>
                            </span>
                            <?php endif; ?>
                    </a>
                    <a href="account.php" class="block text-white hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium">Account</a>
                    <a href="logout.php" class="block text-red-200 hover:bg-red-700 px-3 py-2 rounded-md text-base font-medium">Logout</a>
                </div>
            </div>
        </nav>
        <div class="pt-20"></div>


        <div class="container mt-10">
            <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <a class="nav-link active" id="ins-tab" data-toggle="tab" href="#ins" role="tab" aria-controls="ins" aria-selected="true" style="color: black;">New Order</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="update-tab" data-toggle="tab" href="#update" role="tab" aria-controls="update" aria-selected="false" style="color: black;">Update Order</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="cons-tab" data-toggle="tab" href="#cons" role="tab" aria-controls="cons" aria-selected="false" style="color: black;">Invoice</a>
                </li>
                

            </ul>
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active pt-3" id="ins" role="tabpanel" aria-labelledby="ins-tab">
                    <div class="container">
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" class="form" method="POST">
                            <div class="row text-center">
                            <div class="col-md-6 p-3" style="background-color: rgba(255, 255, 255, 0.7);">
                                <h3 class="mb-3">Sender's Details</h3>
                                <div class="form-group text-left pl-5">
                                    <label>Name    : </label>
                                    <input type="text" name="sname">
                                    <label class="text-danger"><?php echo $errors['req'];?></label>
                                </div>
                                <div class="form-group text-left pl-5">
                                    <label>Address : </label>
                                    <input type="text" name="sadd">
                                    <label class="text-danger"><?php echo $errors['req'];?></label>
                                </div>
                                <div class="form-group text-left pl-5">
                                    <label>City    : </label>
                                    <input type="text" name="scity"> 
                                    <label class="text-danger"><?php echo $errors['req'];?></label>
                                </div>
                                <div class="form-group text-left pl-5">
                                    <label>State : </label>
                                    <input type="text" name="sstate">
                                    <label class="text-danger"><?php echo $errors['req'];?></label>
                                </div>
                                <div class="form-group text-left pl-5">
                                    <label>Contact : </label>
                                    <input type="text" name="scontact">
                                    <label class="text-danger"><?php echo $errors['req'];?></label>
                                </div>
                            </div>
                            <div class="col-md-6 p-3" style="background-color: rgba(255, 255, 255, 0.7);">
                                <h3 class="mb-3">Receiver's Details</h3>
                                <div class="form-group text-left pl-5">
                                    <label>Name : </label>
                                    <input type="text" name="rname">
                                    <label class="text-danger"><?php echo $errors['req'];?></label>
                                </div>
                                <div class="form-group text-left pl-5">
                                    <label>Address : </label>
                                    <input type="text" name="radd">
                                    <label class="text-danger"><?php echo $errors['req'];?></label>
                                </div>
                                <div class="form-group text-left pl-5">
                                    <label>City : </label>
                                    <input type="text" name="rcity">
                                    <label class="text-danger"><?php echo $errors['req'];?></label>
                                </div>
                                <div class="form-group text-left pl-5">
                                    <label>State : </label>
                                    <input type="text" name="rstate">
                                    <label class="text-danger"><?php echo $errors['req'];?></label>
                                </div>
                                <div class="form-group text-left pl-5">
                                    <label>Contact : </label>
                                    <input type="text" name="rcontact">
                                    <label class="text-danger"><?php echo $errors['req'];?></label>
                                </div>
                                <div class="form-group text-left pl-5">
                                    <label>Weight : </label>
                                    <input type="text" name="wgt">
                                    <label class="text-danger"><?php echo $errors['req'];?></label>
                                </div>
                                <input type="submit" name="submit" value="Place order" class="bt bt-primary">
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade" id="update" role="tabpanel" aria-labelledby="update-tab">
                    <div class="container mt-10">
                        <div class="row">
                            <div class="col-4 p-4 text-center pt-0" style="background-color: rgba(255, 255, 255, 0.7); margin-top: 20px;">
                                 <form action="" method="POST" class="form">
                                    <div class="form-group">
                                        <label style="font-size: 20px;">Tracking ID : </label>
                                        <input type="text" name="inp_tid" value="<?php echo $_SESSION['up_tid'] ?? $status['TrackingID']??'' ; ?>">
                                        <label class="text-danger"><?php echo $errors['status']??'';?></label>
                                    </div>
                                    <input type="submit" name="sel_order" class="btn btn-light text-center" value="Select" style="font-size: 20px;">
                                </form>
                            </div>
                            <div class="col-8 p-4 " style="background-color: rgba(255, 255, 255, 0.7); margin-top: 20px; ">
                                <h3 class="display-6 text-center pb-2 mb-3" style="border-bottom: 2px solid black;">Order Details</h3>
                                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="form">
                                    <div class="form-group">
                                        <label>Tracking ID : </label>
                                        <label><?php echo $_SESSION['up_tid'] ?? $status['TrackingID']??'' ; ?></label>
                                    </div>
                                    <div class="form-group">
                                        <input type='checkbox' name='status_upd' value ="dispatched" disabled>  
                                        <label>Dispatched  </label>
                                        <?php echo $status['disp']; ?>
                                    </div>
                                    <div class="form-group">
                                        <input type='checkbox' name='status_upd' value ="shipped" <?php echo $disable_ship ?>>
                                        <label>Shipped </label>
                                        <?php echo $status['ship']; ?>
                                    </div>
                                    <div class="form-group">
                                        <input type='checkbox' name='status_upd' value ="out_for_delivery" <?php echo $disable_out ?>>
                                        <label>Out for Delivery  </label>
                                        <?php echo $status['out']; ?>
                                    </div>
                                    <div class="form-group">
                                        <input type='checkbox' name='status_upd' value ="delivered" <?php echo $disable_del ?>>
                                        <label>Delivered  </label>
                                        <?php echo $status['del']; ?>
                                    </div>
                                    <input type="submit" name="update" value="Update Details" class="btn btn-light">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- HTML Modifications -->
<div class="tab-pane fade" id="cons" role="tabpanel" aria-labelledby="cons-tab">
    <!-- Add search input fields -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="staffIdSearch" class="form-control" placeholder="Search by Staff ID">
        </div>
        <div class="col-md-6">
            <input type="text" id="senderNameSearch" class="form-control" placeholder="Search by Sender Name">
        </div>
    </div>

    <ul class="nav nav-tabs" id="myTab2" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="arr-tab" data-toggle="tab" href="#arr" role="tab" aria-controls="arr" aria-selected="true" style="color: black;">Arrived</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="del-tab" data-toggle="tab" href="#del" role="tab" aria-controls="del" aria-selected="false" style="color: black;">Delivered</a>
        </li>
    </ul>
    <div class="tab-content b-0" id="myTabContent">
        <div class="tab-pane fade show active" id="arr" role="tabpanel" aria-labelledby="arr-tab">
            <table class="table table-hover table-bordered table-striped" id="arrivedTable" style="background-color: rgba(255, 255, 255, 0.8);">
                <thead class="thead-dark">
                    <tr class="table-info">
                        <td>TrackingID</td><td>StaffID</td><td>Sender</td><td>Receiver</td>
                        <td>Weight</td><td>Price</td><td>Dispatched</td><td>Shipped</td>
                        <td>Out for delivery</td><td>Delivered</td>
                    </tr>                    
                </thead>
                <tbody>
                    <?php foreach($arr as $order): ?>
                    <tr>
                        <td><?php echo $order['TrackingID'];?></td>
                        <td><?php echo $order['StaffID'];?></td>
                        <td><?php echo $order['S_Name'].', '.$order['S_Add'].', '.$order['S_City'].', '.$order['S_State'].' - '.$order['S_Contact'];?></td>
                        <td><?php echo $order['R_Name'].', '.$order['R_Add'].', '.$order['R_City'].', '.$order['R_State'].' - '.$order['R_Contact'];?></td>
                        <td><?php echo $order['Weight_Kg'];?></td>
                        <td><?php echo $order['Price'];?></td>
                        <td><?php echo $order['Dispatched_Time'];?></td>
                        <td><?php echo $order['Shipped'];?></td>
                        <td><?php echo $order['Out_for_delivery'];?></td>
                        <td><?php echo $order['Delivered'];?></td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="del" role="tabpanel" aria-labelledby="del-tab">
    <?php 
    // Debug information
    echo "<!-- Number of delivered parcels: " . count($delivered) . " -->";
    if (empty($delivered)) {
        echo '<div class="alert alert-info">No delivered parcels found</div>';
    } 
    ?>
    <table class="table table-hover table-bordered table-striped" id="deliveredTable" style="background-color: rgba(255, 255, 255, 0.8);">
        <thead class="thead-dark">
            <tr class="table-info">
                <td>TrackingID</td><td>StaffID</td><td>Sender</td><td>Receiver</td>
                <td>Weight</td><td>Price</td><td>Dispatched</td><td>Shipped</td>
                <td>Out for delivery</td><td>Delivered</td>
            </tr>                    
        </thead>
        <tbody>
            <?php 
            if (!empty($delivered)) {
                foreach($delivered as $order): 
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['TrackingID']); ?></td>
                    <td><?php echo htmlspecialchars($order['StaffID']); ?></td>
                    <td><?php echo htmlspecialchars($order['S_Name'].', '.$order['S_Add'].', '.$order['S_City'].', '.$order['S_State'].' - '.$order['S_Contact']); ?></td>
                    <td><?php echo htmlspecialchars($order['R_Name'].', '.$order['R_Add'].', '.$order['R_City'].', '.$order['R_State'].' - '.$order['R_Contact']); ?></td>
                    <td><?php echo htmlspecialchars($order['Weight_Kg']); ?></td>
                    <td><?php echo htmlspecialchars($order['Price']); ?></td>
                    <td><?php echo htmlspecialchars($order['Dispatched_Time']); ?></td>
                    <td><?php echo htmlspecialchars($order['Shipped']); ?></td>
                    <td><?php echo htmlspecialchars($order['Out_for_delivery']); ?></td>
                    <td><?php echo htmlspecialchars($order['Delivered']); ?></td>
                </tr>
            <?php 
                endforeach;
            }
            ?>
        </tbody>
    </table>
</div>
    </div>
</div>

<!-- JavaScript for Search Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to filter tables
    function filterTable(tableId, staffIdInput, senderNameInput) {
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        const staffIdSearchValue = staffIdInput.value.toLowerCase();
        const senderNameSearchValue = senderNameInput.value.toLowerCase();

        for (let i = 0; i < rows.length; i++) {
            const staffIdCell = rows[i].getElementsByTagName('td')[1];
            const senderCell = rows[i].getElementsByTagName('td')[2];
            
            const staffIdMatch = staffIdCell.textContent.toLowerCase().includes(staffIdSearchValue);
            const senderMatch = senderCell.textContent.toLowerCase().includes(senderNameSearchValue);

            // Show row if both search criteria are met (or if search input is empty)
            rows[i].style.display = (staffIdMatch && senderMatch) ? '' : 'none';
        }
    }

    // Get search input elements
    const staffIdSearchInput = document.getElementById('staffIdSearch');
    const senderNameSearchInput = document.getElementById('senderNameSearch');

    // Add event listeners for both arrived and delivered tables
    ['arrivedTable', 'deliveredTable'].forEach(tableId => {
        const table = document.getElementById(tableId);
        if (table) {
            staffIdSearchInput.addEventListener('keyup', () => filterTable(tableId, staffIdSearchInput, senderNameSearchInput));
            senderNameSearchInput.addEventListener('keyup', () => filterTable(tableId, staffIdSearchInput, senderNameSearchInput));
        }
    });
});
</script>
              </div>
              <script>
                $(function() { 
                    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                        localStorage.setItem('lastTab', $(this).attr('href'));
                    });
                    var lastTab = localStorage.getItem('lastTab');
                    if (lastTab) {
                        $('[href="' + lastTab + '"]').tab('show');
                    }   
                });
              </script>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            // Toggle mobile menu
            mobileMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                mobileMenu.classList.toggle('-translate-x-full');
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target)) {
                    mobileMenu.classList.add('-translate-x-full');
                }
            });

            // Close menu when a link is clicked
            const mobileMenuLinks = mobileMenu.querySelectorAll('a');
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.add('-translate-x-full');
                });
            });
        });
        </script>

        <script>
            const profileDropdown = document.getElementById('profileDropdown');
            const dropdownMenu = document.getElementById('dropdownMenu');
            
            profileDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
            if (!profileDropdown.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
            });
        </script>
        <script>
            window.onload = function() {
                // Store original form values to check against
                const originalFormValues = {};
                document.querySelectorAll('form input[type="text"]').forEach(input => {
                    originalFormValues[input.name] = input.value;
                });

                // Clear form on reload
                if(performance.navigation.type === 1) {
                    document.querySelectorAll('form input[type="text"]').forEach(input => {
                        input.value = '';
                    });
                }

                // Prevent form resubmission
                if(window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.href);
                }
            }
        </script>
</body>
</html>
