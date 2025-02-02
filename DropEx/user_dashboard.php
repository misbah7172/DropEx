<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];

// Handle Feedback Submission
if(isset($_POST['submit_feedback'])) {
    $name = $_SESSION['name'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $msg = mysqli_real_escape_string($conn, $_POST['msg']);
    $f_id = $_SESSION['user_id'];
    
    $sql = "INSERT INTO feedback (f_id, Cust_name, Cust_mail, Cust_msg) 
            VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isss", $f_id, $name, $email, $msg);
    
    if(mysqli_stmt_execute($stmt)) {
        $_SESSION['success_popup'] = [
            'title' => 'Success!',
            'message' => 'Feedback submitted successfully!',
            'type' => 'success'
        ];
    } else {
        $_SESSION['success_popup'] = [
            'title' => 'Error',
            'message' => 'Error submitting feedback.',
            'type' => 'error'
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "#feedback");
    exit;
}

// Handle Shipping Request
if(isset($_POST['submit_request'])) {
    $s_name = mysqli_real_escape_string($conn, $_POST['sender_name']);
    $s_add = mysqli_real_escape_string($conn, $_POST['sender_address']);
    $s_city = mysqli_real_escape_string($conn, $_POST['sender_city']);
    $s_state = mysqli_real_escape_string($conn, $_POST['sender_state']);
    $s_contact = mysqli_real_escape_string($conn, $_POST['sender_contact']);
    $r_name = mysqli_real_escape_string($conn, $_POST['receiver_name']);
    $r_add = mysqli_real_escape_string($conn, $_POST['receiver_address']);
    $r_city = mysqli_real_escape_string($conn, $_POST['receiver_city']);
    $r_state = mysqli_real_escape_string($conn, $_POST['receiver_state']);
    $r_contact = mysqli_real_escape_string($conn, $_POST['receiver_contact']);
    $weight = mysqli_real_escape_string($conn, $_POST['weight']);
    $o_id = $_SESSION['user_id'];

    $sql_check = "SELECT Cost FROM pricing WHERE 
                 (State_1 = ? AND State_2 = ?) OR 
                 (State_1 = ? AND State_2 = ?)";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ssss", $s_state, $r_state, $r_state, $s_state);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if(mysqli_num_rows($result_check) > 0) {
        $row = mysqli_fetch_assoc($result_check);
        $price = $row['Cost'] * $weight;

        $sql = "INSERT INTO online_request (user_id, S_Name, S_Add, S_City, S_State, S_Contact, 
                R_Name, R_Add, R_City, R_State, R_Contact, Weight_Kg, Price) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issssissssids", 
            $o_id, $s_name, $s_add, $s_city, $s_state, $s_contact,
            $r_name, $r_add, $r_city, $r_state, $r_contact, $weight, $price);

        if(mysqli_stmt_execute($stmt)) {
            $_SESSION['success_popup'] = [
                'title' => 'Success!',
                'message' => "Shipping request submitted successfully!\nEstimated cost: ₹$price",
                'type' => 'success'
            ];
        } else {
            $_SESSION['success_popup'] = [
                'title' => 'Error',
                'message' => "Error submitting request: " . mysqli_error($conn),
                'type' => 'error'
            ];
        }
    } else {
        $_SESSION['success_popup'] = [
            'title' => 'Warning',
            'message' => "Sorry, delivery is not available between $s_state and $r_state",
            'type' => 'warning'
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "#requests");
    exit;
}

// Fetch user's shipping requests
$sql = "SELECT * FROM online_request WHERE user_id = ? ORDER BY Dispatched_Time DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$shipping_requests = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>DropEx ID</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: inherit;
            filter: brightness(0.9);
            z-index: -1;
        }
        .feedback-form, .request-form {
            background: rgba(248, 249, 250, 0.9);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .status-pending { color: #ffc107; }
        .status-approved { color: #28a745; }
        .status-rejected { color: #dc3545; }
    </style>
</head>
<body>
        <!-- Navbar -->
        <nav class="bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg fixed top-0 left-0 right-0 w-full z-50">
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
                        <a href="branches.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Branches</a>
                            <a href="#feedback" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Feedback</a>
                            <a href="#requests" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Shipping Requests</a>
                            <a href="notifications.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Notifications</a>
                            <a href="tracking.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Tracking</a>
                            <a href="logout.php" class="text-red-200 hover:text-red-400 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Logout</a>
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
                    
                    <?php if(isset($_SESSION['id']) || isset($_SESSION['user_id'])): ?>
                        <?php if(isset($_SESSION['id'])): ?>
                            <a href="staff.php" class="block text-white hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                        <?php else: ?>
                            <a href="user_dashboard.php" class="block text-white hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                        <?php endif; ?>
                        <span class="nav-link text-dark">
                                    Welcome, <?php echo htmlspecialchars($user_name); ?>
                                </span>
                        <a href="user_logout.php" class="block text-red-200 hover:bg-red-400 px-3 py-2 rounded-md text-base font-medium">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="block bg-white text-blue-600 hover:bg-gray-200 px-4 py-2 rounded-md text-base font-medium shadow-md">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

    <div style="margin-top: 80px;"></div>

    <div class="mt-20">  <!-- Added margin top -->
        <div class="container mt-4">
            <!-- Feedback Section -->
            <section id="feedback" class="mb-5">
                <h3><strong style="color:rgb(239, 230, 230);">Submit Feedback</strong></h3>
                <h2 style="color: rgb(239, 230, 230);">If You Have Any Issues Mention Tracking Number</h2>
                <div class="feedback-form">
                    <?php if(isset($feedback_success)): ?>
                        <div class="alert alert-success"><?php echo $feedback_success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_name); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="msg" rows="4" required></textarea>
                        </div>
                        <button type="submit" name="submit_feedback" class="btn btn-primary">Submit Feedback</button>
                    </form>
                </div>
            </section>

        <!-- Shipping Request Section -->
        <section id="requests" class="mb-5">
            <h3>New Shipping Request</h3>
            <div class="request-form">
                <?php if(isset($request_success)): ?>
                    <div class="alert alert-success"><?php echo $request_success; ?></div>
                <?php endif; ?>
                <?php if(isset($request_error)): ?>
                    <div class="alert alert-danger"><?php echo $request_error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <!-- Sender Information -->
                    <h4>Sender Details</h4>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="sender_name" value="<?php echo htmlspecialchars($user_name); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State</label>
                        <input type="text" class="form-control" name="sender_state" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="sender_city" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" name="sender_address" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" name="sender_contact" required>
                    </div>

                    <!-- Receiver Information -->
                    <h4>Receiver Details</h4>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="receiver_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State</label>
                        <input type="text" class="form-control" name="receiver_state" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="receiver_city" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" name="receiver_address" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" name="receiver_contact" required>
                    </div>

                    <!-- Package Information -->
                    <div class="mb-3">
                        <label class="form-label">Package Weight (kg)</label>
                        <input type="number" step="0.01" class="form-control" name="weight" required>
                    </div>
         
                    <button type="submit" name="submit_request" class="btn btn-primary">Submit Request</button>
                </form>
            </div>

            <h3>Your Shipping Requests</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Sender</th>
                            <th>Receiver</th>
                            <th>Weight</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($shipping_requests as $request): ?>
                        <tr>
                            <td><?php echo $request['serial']; ?></td>
                            <td><?php echo htmlspecialchars($request['S_Name']); ?></td>
                            <td><?php echo htmlspecialchars($request['R_Name']); ?></td>
                            <td><?php echo $request['Weight_Kg']; ?> kg</td>
                            <td>₹<?php echo $request['Price']; ?></td>
                            <td class="status-<?php echo $request['status']; ?>">
                                <?php echo ucfirst($request['status']); ?>
                            </td>
                            <td><?php echo $request['Dispatched_Time']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('-translate-x-full');
            });
        });
        </script>
</body>
</html>
