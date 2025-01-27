<?php
session_start();
include("db_connect.php");

// Check if staff is logged in
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
    $_SESSION['error_message'] = 'Staff details not found';
    header('Location: login.php');
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
                        R_Name, R_Add, R_City, R_State, R_Contact, Weight_Kg, Price, Dispatched_Time, image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iisssssissssiddss", 
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
                    $request_data['Dispatched_Time'],
                    $request_data['image']
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
                exit();
                
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $_SESSION['error_message'] = 'Error processing request: ' . mysqli_error($conn);
                header("Location: " . $_SERVER['PHP_SELF']);
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

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-red-600 font-semibold">Serial No.</th>
                        <th class="px-4 py-3 text-left text-green-600 font-semibold">Request ID</th>
                        <th class="px-4 py-3 text-left text-red-600 font-semibold">Sender Details</th>
                        <th class="px-4 py-3 text-left text-green-600 font-semibold">Receiver Details</th>
                        <th class="px-4 py-3 text-left text-red-600 font-semibold">Weight</th>
                        <th class="px-4 py-3 text-left text-green-600 font-semibold">Price</th>
                        <th class="px-4 py-3 text-left text-red-600 font-semibold">Created At</th>
                        <th class="px-4 py-3 text-left text-green-600 font-semibold">Image</th>
                        <th class="px-4 py-3 text-left text-red-600 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach($pending_requests as $request): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><?php echo htmlspecialchars($request['serial']); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($request['user_id']); ?></td>
                        <td class="px-4 py-3">
                            <div class="text-sm">
                                <p class="font-medium"><?php echo htmlspecialchars($request['S_Name']); ?></p>
                                <p class="text-gray-600"><?php echo htmlspecialchars($request['S_Add']); ?></p>
                                <p class="text-gray-600"><?php echo htmlspecialchars($request['S_City']); ?>, <?php echo htmlspecialchars($request['S_State']); ?></p>
                                <p class="text-gray-600">Contact: <?php echo htmlspecialchars($request['S_Contact']); ?></p>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm">
                                <p class="font-medium"><?php echo htmlspecialchars($request['R_Name']); ?></p>
                                <p class="text-gray-600"><?php echo htmlspecialchars($request['R_Add']); ?></p>
                                <p class="text-gray-600"><?php echo htmlspecialchars($request['R_City']); ?>, <?php echo htmlspecialchars($request['R_State']); ?></p>
                                <p class="text-gray-600">Contact: <?php echo htmlspecialchars($request['R_Contact']); ?></p>
                            </div>
                        </td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($request['Weight_Kg']); ?> kg</td>
                        <td class="px-4 py-3">₹<?php echo htmlspecialchars($request['Price']); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($request['Dispatched_Time']); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($request['image']); ?></td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col space-y-2">
                                <form method="POST" action="">
                                    <input type="hidden" name="serial" value="<?php echo $request['serial']; ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" name="update_request" class="w-full px-3 py-1 text-sm text-white bg-green-600 rounded hover:bg-green-700 transition-colors">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="">
                                    <input type="hidden" name="serial" value="<?php echo $request['serial']; ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" name="update_request" class="w-full px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700 transition-colors">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
