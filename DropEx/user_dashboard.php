<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];
$error = array('name' => '', 'email' => '', 'msg' => '');
$name = $email = $msg = '';

// Handle Feedback Submission
if(isset($_POST['submit_feedback']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_SESSION['name'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $msg = mysqli_real_escape_string($conn, $_POST['msg']);
    $f_id = $_SESSION['user_id']; // Get user_id from session
    
    if (!isset($_SESSION['last_feedback_time']) || 
        (time() - $_SESSION['last_feedback_time']) > 2) {
        
        $sql = "INSERT INTO feedback (f_id, Cust_name, Cust_mail, Cust_msg) 
                VALUES ('$f_id', '$name', '$email', '$msg')";
        if(mysqli_query($conn, $sql)) {
            $feedback_success = "Feedback submitted successfully!";
            $_SESSION['last_feedback_time'] = time();
        } else {
            $feedback_error = "Error submitting feedback.";
        }
    }
}

// Handle Shipping Request
if(isset($_POST['submit_request']) && $_SERVER["REQUEST_METHOD"] == "POST") {
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
    $o_id = $_SESSION['user_id']; // Get user_id from session
    
    // Check if delivery is available between these states
    $sql_check = "SELECT Cost FROM pricing WHERE 
                 (State_1 = '$s_state' AND State_2 = '$r_state') OR 
                 (State_1 = '$r_state' AND State_2 = '$s_state')";
    $result_check = mysqli_query($conn, $sql_check);
    
    if(mysqli_num_rows($result_check) > 0) {
        $row = mysqli_fetch_assoc($result_check);
        $base_cost = $row['Cost'];
        $price = $base_cost * $weight;
        
        if (!isset($_SESSION['last_request_time']) || 
            (time() - $_SESSION['last_request_time']) > 2) {
            
            $sql = "INSERT INTO online_request (user_id, S_Name, S_Add, S_City, S_State, S_Contact, 
                    R_Name, R_Add, R_City, R_State, R_Contact, Weight_Kg, Price) 
                    VALUES ('$o_id', '$s_name', '$s_add', '$s_city', '$s_state', '$s_contact',
                    '$r_name', '$r_add', '$r_city', '$r_state', '$r_contact', '$weight', '$price')";
            
            if(mysqli_query($conn, $sql)) {
                $request_success = "Shipping request submitted successfully! Estimated cost: ₹$price";
                $_SESSION['last_request_time'] = time();
                header("Location: " . $_SERVER['PHP_SELF'] . "#requests");
                exit;
            } else {
                $request_error = "Error submitting request: " . mysqli_error($conn);
            }
        }
    } else {
        $request_error = "Sorry, delivery is not available between $s_state and $r_state";
    }
}

// Fetch user's shipping requests
$sql = "SELECT * FROM online_request WHERE user_id = '$user_id' ORDER BY Dispatched_Time DESC";
$result = mysqli_query($conn, $sql);
$shipping_requests = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DropEx ID</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('Images/DropExBack.jpg');
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
    <!-- Navigation Bar -->
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
                    <a class="nav-item nav-link text-dark mr-5" href="#feedback">Feedback</a>
                    <a class="nav-item nav-link text-dark mr-5" href="#requests">Shipping Requests</a>
                    <a class="nav-item nav-link text-dark mr-5" href="tracking.php">Tracking</a>
                    <span class="nav-item nav-link text-dark mr-3">
                        Welcome, <?php echo htmlspecialchars($user_name); ?>
                    </span>
                    <a class="nav-item nav-link btn-logout" href="user_logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

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
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" name="sender_address" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="sender_city" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State</label>
                        <input type="text" class="form-control" name="sender_state" required>
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
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" name="receiver_address" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="receiver_city" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State</label>
                        <input type="text" class="form-control" name="receiver_state" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" name="receiver_contact" required>
                    </div>

                    <!-- Package Information -->
                    <h4>Package Details</h4>
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
                            <td><?php echo $request['user_id']; ?></td>
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
</body>
</html>