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

// Fetch arrived and delivered parcels
$sql = "SELECT * FROM arrived";
$result = mysqli_query($conn, $sql);
$arr = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql = "SELECT * FROM delivered";
$result = mysqli_query($conn, $sql);
$delivered = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
    <div class="container mx-auto">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <a href="index.php">
                    <img src="Images/logo.png" id="logo" class="h-24">
                </a>
            </div>
            <div class="relative">
                <button id="profileDropdown" class="flex items-center bg-white border border-gray-300 rounded-lg px-4 py-2">
                    <img src="Images/pp2.png" id="logo" class="h-16 rounded-full object-cover">
                    <span class="ml-2"><?php echo $name ?></span>
                </button>
                <ul id="dropdownMenu" class="absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                    <li><a href="account.php" class="block px-4 py-2 text-black hover:bg-gray-200">Account</a></li>
                    <li><a href="logout.php" class="block px-4 py-2 text-black hover:bg-gray-200">Logout</a></li>
                </ul>
                <script>
                    document.getElementById('profileDropdown').addEventListener('click', function() {
                        document.getElementById('dropdownMenu').classList.toggle('hidden');
                    });
                    
                    // Close dropdown when clicking outside
                    window.addEventListener('click', function(e) {
                        if (!e.target.closest('.relative')) {
                            document.getElementById('dropdownMenu').classList.add('hidden');
                        }
                    });
                </script>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md">
            <ul class="flex justify-center space-x-4">
                <li><a class="text-black font-semibold py-2 px-4 rounded-lg bg-gray-200" data-toggle="tab" href="#ins">New Order</a></li>
                <li><a class="text-black py-2 px-4 rounded-lg hover:bg-gray-200" href="staff_request_approval.php">Pending Request</a></li>
                <li><a class="text-black py-2 px-4 rounded-lg hover:bg-gray-200" data-toggle="tab" href="#update">Update Order</a></li>
                <li><a class="text-black py-2 px-4 rounded-lg hover:bg-gray-200" data-toggle="tab" href="#cons">Invoice</a></li>
                <li><a class="text-black py-2 px-4 rounded-lg hover:bg-gray-200" href="account.php">Profile</a></li>
            </ul>
            <div class="tab-content mt-4">
                <div class="tab-pane fade show active" id="ins">
                    <div class="container">
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" class="form" method="POST">
                            <div class="flex space-x-4">
                                <div class="w-1/2 p-4 bg-white rounded-lg shadow-md">
                                    <h3 class="mb-3 text-lg font-semibold">Sender's Details</h3>
                                    <div class="mb-4">
                                        <label>Name:</label>
                                        <input type="text" name="sname" class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                                        <label class="text-red-500"><?php echo $errors['req'];?></label>
                                    </div>
                                    <div class="mb-4">
                                        <label>Address:</label>
                                        <input type="text" name="sadd" class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                                        <label class="text-red-500"><?php echo $errors['req'];?></label>
                                    </div>
                                    <div class="mb-4">
                                        <label>City:</label>
                                        <input type="text" name="scity" class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                                        <label class="text-red-500"><?php echo $errors['req'];?></label>
                                    </div>
                                    <div class="mb-4">
                                        <label>State:</label>
                                        <input type="text" name="sstate" class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                                        <label class="text-red-500"><?php echo $errors['req'];?></label>
                                    </div>
                                    <div class="mb-4">
                                        <label>Contact:</label>
                                        <input type="text" name="scontact" class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                                        <label class="text-red-500"><?php echo $errors['req'];?></label>
                                    </div>
                                </div>
                                <div class="w-1/2 p-4 bg-white rounded-lg shadow-md">
                                    <h3 class="mb-3 text-lg font-semibold">Receiver's Details</h3>
                                    <div class="mb-4">
                                        <label>Name:</label>
                                        <input type="text" name="rname" class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                                        <label class="text-red-500"><?php echo $errors['req'];?></label>
                                    </div>
                                    <div class="mb-4">
                                        <label>Address:</label>
                                        <input type="text" name="radd" class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                                        <label class="text-red-500"><?php echo $errors['req'];?></label>
                                    </div>
                                    <div class="mb-4">
                                        <label>City:</label>
                                        <input type="text" name="rcity" class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                                        <label class="text-red-500"><?php echo $errors['req'];?></label>
                                    </div>
                                    <div class="mb-4">
                                        <label>State:</label>
                                        <input type="text" name="rstate" class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                                        <label class="text-red-500"><?php echo $errors['req'];?></label>
                                    </div>
                                    <div class="mb-4">
                                        <label>Contact:</label>
                                        <input type="text" name="rcontact" class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                                        <label class="text-red-500"><?php echo $errors['req'];?></label>
                                    </div>
                                    <div class="mb-4">
                                        <label>Weight:</label>
                                        <input type="text" name="wgt" class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                                        <label class="text-red-500"><?php echo $errors['req'];?></label>
                                    </div>
                                    <input type="submit" name="submit" value="Place order" class="bg-blue-500 text-white rounded-lg px-4 py-2">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade" id="update">
                    <div class="container mt-10">
                        <div class="flex space-x-4">
                            <div class="w-1/3 p-4 bg-white rounded-lg shadow-md">
                                <form action="" method="POST" class="form">
                                    <div class="mb-4">
                                        <label class="text-lg">Tracking ID:</label>
                                        <input type="text" name="inp_tid" value="<?php echo $_SESSION['up_tid'] ?? $status['TrackingID']??'' ; ?>" class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                                        <label class="text-red-500"><?php echo $errors['status']??'';?></label>
                                    </div>
                                    <input type="submit" name="sel_order" class="bg-blue-500 text-white rounded-lg px-4 py-2" value="Select">
                                </form>
                            </div>
                            <div class="w-2/3 p-4 bg-white rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold pb-2 mb-3 border-b">Order Details</h3>
                                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="form">
                                    <div class="mb-4">
                                        <label>Tracking ID:</label>
                                        <label><?php echo $_SESSION['up_tid'] ?? $status['TrackingID']??'' ; ?></label>
                                    </div>
                                    <div class="mb-4">
                                        <input type='checkbox' name='status_upd' value ="dispatched" disabled>  
                                        <label>Dispatched</label>
                                        <?php echo $status['disp']; ?>
                                    </div>
                                    <div class="mb-4">
                                        <input type='checkbox' name='status_upd' value ="shipped" <?php echo $disable_ship ?>>
                                        <label>Shipped</label>
                                        <?php echo $status['ship']; ?>
                                    </div>
                                    <div class="mb-4">
                                        <input type='checkbox' name='status_upd' value ="out_for_delivery" <?php echo $disable_out ?>>
                                        <label>Out for Delivery</label>
                                        <?php echo $status['out']; ?>
                                    </div>
                                    <div class="mb-4">
                                        <input type='checkbox' name='status_upd' value ="delivered" <?php echo $disable_del ?>>
                                        <label>Delivered</label>
                                        <?php echo $status['del']; ?>
                                    </div>
                                    <input type="submit" name="update" value="Update Details" class="bg-blue-500 text-white rounded-lg px-4 py-2">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="cons">
                    <ul class="flex space-x-4">
                        <li><a class="text-black font-semibold py-2 px-4 rounded-lg bg-gray-200" data-toggle="tab" href="#arr">Arrived</a></li>
                        <li><a class="text-black py-2 px-4 rounded-lg hover:bg-gray-200" data-toggle="tab" href="#del">Delivered</a></li>
                    </ul>
                    <div class="tab-content mt-4">
                        <div class="tab-pane fade show active" id="arr">
                            <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="border px-4 py-2">TrackingID</th>
                                        <th class="border px-4 py-2">StaffID</th>
                                        <th class="border px-4 py-2">Sender</th>
                                        <th class="border px-4 py-2">Receiver</th>
                                        <th class="border px-4 py-2">Weight</th>
                                        <th class="border px-4 py-2">Price</th>
                                        <th class="border px-4 py-2">Dispatched</th>
                                        <th class="border px-4 py-2">Shipped</th>
                                        <th class="border px-4 py-2">Out for delivery</th>
                                        <th class="border px-4 py-2">Delivered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($arr as $order): ?>
                                    <tr>
                                        <td class="border px-4 py-2"><?php echo $order['TrackingID'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['StaffID'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['S_Name'].', '.$order['S_Add'].', '.$order['S_City'].', '.$order['S_State'].' - '.$order['S_Contact'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['R_Name'].', '.$order['R_Add'].', '.$order['R_City'].', '.$order['R_State'].' - '.$order['R_Contact'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['Weight_Kg'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['Price'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['Dispatched_Time'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['Shipped'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['Out_for_delivery'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['Delivered'];?></td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="del">
                            <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="border px-4 py-2">TrackingID</th>
                                        <th class="border px-4 py-2">StaffID</th>
                                        <th class="border px-4 py-2">Sender</th>
                                        <th class="border px-4 py-2">Receiver</th>
                                        <th class="border px-4 py-2">Weight</th>
                                        <th class="border px-4 py-2">Price</th>
                                        <th class="border px-4 py-2">Dispatched</th>
                                        <th class="border px-4 py-2">Shipped</th>
                                        <th class="border px-4 py-2">Out for delivery</th>
                                        <th class="border px-4 py-2">Delivered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($delivered as $order): ?>
                                    <tr>
                                        <td class="border px-4 py-2"><?php echo $order['TrackingID'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['StaffID'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['S_Name'].', '.$order['S_Add'].', '.$order['S_City'].', '.$order['S_State'].' - '.$order['S_Contact'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['R_Name'].', '.$order['R_Add'].', '.$order['R_City'].', '.$order['R_State'].' - '.$order['R_Contact'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['Weight_Kg'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['Price'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['Dispatched_Time'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['Shipped'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['Out_for_delivery'];?></td>
                                        <td class="border px-4 py-2"><?php echo $order['Delivered'];?></td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="text-center py-4 bg-blue-600 text-white mt-4">
            <p>&copy; 2025 DropEx. All Rights Reserved. | Delivering Beyond Borders</p>
        </footer>
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
</body>
</html>
