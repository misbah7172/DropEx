<?php
// Start output buffering
ob_start();
session_start();
include("db_connect.php");

// Check if staff is logged in
if(!isset($_SESSION['id'])) {
    header('Location: login.php');
    ob_end_flush();
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
    $_SESSION['error_message'] = 'Staff details not found';
    header('Location: login.php');
    ob_end_flush();
    exit();
}

$staff_name = $staff['Name'];
$staff_branch = $staff['branch'];

// Display messages from session if they exist
$success_message = '';
$error_message = '';
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
    
    // First check if the request is still pending and belongs to staff's branch
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
                
                // Insert into parcel table with request_id as serial
                $sql = "INSERT INTO parcel (TrackingID, request_id, StaffID, S_Name, S_Add, S_City, S_State, S_Contact, 
                        R_Name, R_Add, R_City, R_State, R_Contact, Weight_Kg, Price, Dispatched_Time) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iisssssssssssids", 
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
                $update_sql = "UPDATE online_request SET status = ? WHERE serial = ? AND status = 'pending' AND S_State = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "sis", $status, $serial, $staff_branch);
                mysqli_stmt_execute($update_stmt);
                
                mysqli_commit($conn);
                
                $_SESSION['tid'] = $tid;
                header("Location: receipt.php");
                ob_end_flush();
                exit();
                
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $_SESSION['error_message'] = 'Error processing request: ' . mysqli_error($conn);
                header("Location: " . $_SERVER['PHP_SELF']);
                ob_end_flush();
                exit();
            }
        } else if($status === 'rejected') {
            $sql = "UPDATE online_request SET status = ? WHERE serial = ? AND status = 'pending' AND S_State = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sis", $status, $serial, $staff_branch);
            if(mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = 'Request rejected successfully!';
            } else {
                $_SESSION['error_message'] = 'Error rejecting request: ' . mysqli_error($conn);
            }
            header("Location: " . $_SERVER['PHP_SELF']);
            ob_end_flush();
            exit();
        }
    } else {
        $_SESSION['error_message'] = 'This request has already been processed or does not belong to your branch!';
        header("Location: " . $_SERVER['PHP_SELF']);
        ob_end_flush();
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
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <a href="staff.php" class="inline-flex items-center px-4 py-2 mb-6 text-sm font-medium text-orange-500 bg-orange-100 rounded-lg hover:bg-orange-200 transition-colors">
            <i class="fa fa-arrow-left mr-2"></i> Go Back
        </a>
        
        <h3 class="text-2xl font-bold mb-6">Pending Shipping Requests</h3>
        
        <?php if($success_message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($success_message); ?></span>
                <button type="button" class="absolute top-0 right-0 px-4 py-3" data-dismiss="alert">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
                <button type="button" class="absolute top-0 right-0 px-4 py-3" data-dismiss="alert">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="font-medium">
                    <span class="text-gray-600">Staff ID:</span> 
                    <span class="ml-2"><?php echo htmlspecialchars($staff_id); ?></span>
                </div>
                <div class="font-medium">
                    <span class="text-gray-600">Staff Name:</span>
                    <span class="ml-2"><?php echo htmlspecialchars($staff_name); ?></span>
                </div>
                <div class="font-medium">
                    <span class="text-gray-600">Branch:</span>
                    <span class="ml-2"><?php echo htmlspecialchars($staff_branch); ?></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <?php foreach($pending_requests as $request): ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <!-- Mobile view - Card layout -->
                <div class="md:hidden">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Serial No.</p>
                                <p class="font-medium"><?php echo htmlspecialchars($request['serial']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Request ID</p>
                                <p class="font-medium"><?php echo htmlspecialchars($request['user_id']); ?></p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-600 mb-2">Sender Details</p>
                                <div class="pl-2 border-l-2 border-gray-200">
                                    <p class="font-medium"><?php echo htmlspecialchars($request['S_Name']); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($request['S_Add']); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($request['S_City']); ?>, <?php echo htmlspecialchars($request['S_State']); ?></p>
                                    <p class="text-sm text-gray-600">Contact: <?php echo htmlspecialchars($request['S_Contact']); ?></p>
                                </div>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-gray-600 mb-2">Receiver Details</p>
                                <div class="pl-2 border-l-2 border-gray-200">
                                    <p class="font-medium"><?php echo htmlspecialchars($request['R_Name']); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($request['R_Add']); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($request['R_City']); ?>, <?php echo htmlspecialchars($request['R_State']); ?></p>
                                    <p class="text-sm text-gray-600">Contact: <?php echo htmlspecialchars($request['R_Contact']); ?></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Weight</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($request['Weight_Kg']); ?> kg</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Price</p>
                                    <p class="font-medium">₹<?php echo htmlspecialchars($request['Price']); ?></p>
                                </div>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Created At</p>
                                <p class="font-medium"><?php echo htmlspecialchars($request['Dispatched_Time']); ?></p>
                            </div>

                            <div class="flex space-x-3">
                                <form method="POST" action="" class="flex-1">
                                    <input type="hidden" name="serial" value="<?php echo $request['serial']; ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" name="update_request" class="w-full px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="" class="flex-1">
                                    <input type="hidden" name="serial" value="<?php echo $request['serial']; ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" name="update_request" class="w-full px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop view - Table layout -->
                <div class="hidden md:block">
                    <table class="min-w-full">
                        <tr class="border-b">
                            <td class="py-2 w-1/6">
                                <span class="text-gray-600">Serial No:</span>
                                <span class="font-medium ml-2"><?php echo htmlspecialchars($request['serial']); ?></span>
                            </td>
                            <td class="py-2 w-1/6">
                                <span class="text-gray-600">Request ID:</span>
                                <span class="font-medium ml-2"><?php echo htmlspecialchars($request['user_id']); ?></span>
                            </td>
                            <td class="py-2 w-1/6">
                                <span class="text-gray-600">Weight:</span>
                                <span class="font-medium ml-2"><?php echo htmlspecialchars($request['Weight_Kg']); ?> kg</span>
                            </td>
                            <td class="py-2 w-1/6">
                                <span class="text-gray-600">Price:</span>
                                <span class="font-medium ml-2">₹<?php echo htmlspecialchars($request['Price']); ?></span>
                            </td>
                            <td class="py-2 w-2/6">
                                <div class="flex space-x-2">
                                    <form method="POST" action="">
                                        <input type="hidden" name="serial" value="<?php echo $request['serial']; ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" name="update_request" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700 transition-colors">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="">
                                        <input type="hidden" name="serial" value="<?php echo $request['serial']; ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" name="update_request" class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700 transition-colors">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="py-2">
                                <div class="text-sm">
                                    <p class="text-gray-600 mb-1">Sender Details:</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($request['S_Name']); ?></p>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($request['S_Add']); ?></p>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($request['S_City']); ?>, <?php echo htmlspecialchars($request['S_State']); ?></p>
                                    <p class="text-gray-600">Contact: <?php echo htmlspecialchars($request['S_Contact']); ?></p>
                                </div>
                            </td>
                            <td colspan="2" class="py-2">
                                <div class="text-sm">
                                    <p class="text-gray-600 mb-1">Receiver Details:</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($request['R_Name']); ?></p>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($request['R_Add']); ?></p>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($request['R_City']); ?>, <?php echo htmlspecialchars($request['R_State']); ?></p>
                                    <p class="text-gray-600">Contact: <?php echo htmlspecialchars($request['R_Contact']); ?></p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Close alert messages
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('[data-dismiss="alert"]');
            alerts.forEach(function(alert) {
                alert.addEventListener('click', function() {
                    this.parentElement.remove();
                });
            });
        });
    </script>
</body>
</html>