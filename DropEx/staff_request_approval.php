<?php
// Start with error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session and include required files
session_start();
include("db_connect.php");

// Initialize messages
$success_message = '';
$error_message = '';
$pending_requests = [];
$staff = null;
$staff_id = '';
$staff_name = '';
$staff_branch = '';

// Process all logic that might require redirects first
if(!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Get staff details including branch
$staff_id = $_SESSION['id'];
$sql = "SELECT * FROM staff WHERE StaffID=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $staff_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$staff = mysqli_fetch_assoc($result);

if (!$staff) {
    header('Location: login.php');
    exit();
}

$staff_name = $staff['Name'];
$staff_branch = $staff['branch'];

// Handle messages from session
if(isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if(isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Handle request approval/rejection
if(isset($_POST['update_request'])) {
    $serial = mysqli_real_escape_string($conn, $_POST['serial']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Check if request is still pending and belongs to staff's branch
    $check_sql = "SELECT * FROM online_request WHERE serial = ? AND status = 'pending' AND S_State = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "is", $serial, $staff_branch);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if(mysqli_num_rows($check_result) > 0) {
        $request_data = mysqli_fetch_assoc($check_result);
        
        if($status === 'approved') {
            mysqli_begin_transaction($conn);
            
            try {
                $tracking_id = rand(100000, 999999);
                
                // Insert into parcel table
                $sql = "INSERT INTO parcel (TrackingID, request_id, StaffID, S_Name, S_Add, S_City, S_State, S_Contact, 
                        R_Name, R_Add, R_City, R_State, R_Contact, Weight_Kg, Price, Dispatched_Time) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iisssssissssidds", 
                    $tracking_id,
                    $serial,
                    $staff_id,
                    $request_data['S_Name'],
                    $request_data['S_Add'],
                    $request_data['S_City'],
                    $request_data['S_State'],
                    $request_data['S_Contact'],
                    $request_data['R_Name'],
                    $request_data['R_Add'],
                    $request_data['R_City'],
                    $request_data['R_State'],
                    $request_data['R_Contact'],
                    $request_data['Weight_Kg'],
                    $request_data['Price'],
                    $request_data['Dispatched_Time']
                );
                mysqli_stmt_execute($stmt);
                
                $tid = mysqli_insert_id($conn);
                
                // Update status in online_request table
                $update_sql = "UPDATE online_request SET status = ? WHERE serial = ? AND S_State = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "sis", $status, $serial, $staff_branch);
                mysqli_stmt_execute($update_stmt);
                
                mysqli_commit($conn);
                
                $_SESSION['tid'] = $tid;
                header("Location: receipt.php");
                exit();
                
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $_SESSION['error_message'] = 'Error processing request: ' . mysqli_error($conn);
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        } else if($status === 'rejected') {
            $sql = "UPDATE online_request SET status = ? WHERE serial = ? AND S_State = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sis", $status, $serial, $staff_branch);
            if(mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = 'Request rejected successfully!';
            } else {
                $_SESSION['error_message'] = 'Error rejecting request: ' . mysqli_error($conn);
            }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        $_SESSION['error_message'] = 'This request has already been processed or does not belong to your branch!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch pending requests only for staff's branch
$sql = "SELECT * FROM online_request WHERE status = 'pending' AND S_State = ? ORDER BY Dispatched_Time DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $staff_branch);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pending_requests = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Request Approval</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/bootstrap.css">
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
<body>
    <div class="container mt-4">
        <a href="staff.php" class="btn btn-warning" style="color: orange; margin-bottom: 20px;"><i class="fa fa-arrow-left"></i> Go Back</a>
        <h3>Pending Shipping Requests</h3>
        
        <?php if($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <strong>Staff ID: </strong><?php echo htmlspecialchars($staff_id); ?><br>
            <strong>Staff Name: </strong><?php echo htmlspecialchars($staff_name); ?><br>
            <strong>Branch: </strong><?php echo htmlspecialchars($staff_branch); ?>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="color: red;">Serial No.</th>
                    <th style="color: green;">Request ID</th>
                    <th style="color: red;">Sender Details</th>
                    <th style="color: green;">Receiver Details</th>
                    <th style="color: red;">Weight</th>
                    <th style="color: green;">Price</th>
                    <th style="color: red;">Created At</th>
                    <th style="color: red;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($pending_requests as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['serial']); ?></td>
                    <td><?php echo htmlspecialchars($request['user_id']); ?></td>
                    <td>
                        <?php echo htmlspecialchars($request['S_Name']); ?><br>
                        <?php echo htmlspecialchars($request['S_Add']); ?><br>
                        <?php echo htmlspecialchars($request['S_City']); ?>, <?php echo htmlspecialchars($request['S_State']); ?><br>
                        Contact: <?php echo htmlspecialchars($request['S_Contact']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($request['R_Name']); ?><br>
                        <?php echo htmlspecialchars($request['R_Add']); ?><br>
                        <?php echo htmlspecialchars($request['R_City']); ?>, <?php echo htmlspecialchars($request['R_State']); ?><br>
                        Contact: <?php echo htmlspecialchars($request['R_Contact']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($request['Weight_Kg']); ?> kg</td>
                    <td>₹<?php echo htmlspecialchars($request['Price']); ?></td>
                    <td><?php echo htmlspecialchars($request['Dispatched_Time']); ?></td>
                    <td>
                        <form method="POST" action="" class="d-inline">
                            <input type="hidden" name="serial" value="<?php echo $request['serial']; ?>">
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" name="update_request" class="btn btn-success btn-sm" style="background-color: green; color: white;">Approve</button>
                        </form>
                        <form method="POST" action="" class="d-inline">
                            <input type="hidden" name="serial" value="<?php echo $request['serial']; ?>">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" name="update_request" class="btn btn-danger btn-sm" style="background-color: red; color: white;">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
