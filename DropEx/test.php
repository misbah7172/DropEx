<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];
$error = array('name' => '', 'email' => '', 'msg' => '');
$name = $email = $msg = '';

// Handle Feedback Submission
if(isset($_POST['submit_feedback'])) {
    $name = $_SESSION['name']; // Using logged in user's name
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $msg = mysqli_real_escape_string($conn, $_POST['msg']);
    
    $sql = "INSERT INTO feedback (Cust_name, Cust_mail, Cust_msg) VALUES ('$name', '$email', '$msg')";
    if(mysqli_query($conn, $sql)) {
        $feedback_success = "Feedback submitted successfully!";
    } else {
        $feedback_error = "Error submitting feedback.";
    }
}

// Handle Shipping Request
if(isset($_POST['submit_request'])) {
    $sender_name = mysqli_real_escape_string($conn, $_POST['sender_name']);
    $sender_address = mysqli_real_escape_string($conn, $_POST['sender_address']);
    $receiver_name = mysqli_real_escape_string($conn, $_POST['receiver_name']);
    $receiver_address = mysqli_real_escape_string($conn, $_POST['receiver_address']);
    $weight = mysqli_real_escape_string($conn, $_POST['weight']);
    
    // Check for duplicate request
    $check_sql = "SELECT id FROM shipping_requests WHERE 
                  user_id = '$user_id' AND 
                  sender_name = '$sender_name' AND 
                  sender_address = '$sender_address' AND 
                  receiver_name = '$receiver_name' AND 
                  receiver_address = '$receiver_address' AND 
                  weight = '$weight' AND 
                  created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
    
    $check_result = mysqli_query($conn, $check_sql);
    
    if(mysqli_num_rows($check_result) > 0) {
        $request_error = "Duplicate request detected. Please wait before submitting again.";
    } else {
        $sql = "INSERT INTO shipping_requests (user_id, sender_name, sender_address, receiver_name, receiver_address, weight, status) 
                VALUES ('$user_id', '$sender_name', '$sender_address', '$receiver_name', '$receiver_address', '$weight', 'pending')";
        if(mysqli_query($conn, $sql)) {
            $request_success = "Shipping request submitted successfully!";
        } else {
            $request_error = "Error submitting request.";
        }
    }
}

// Fetch user's shipping requests
$sql = "SELECT * FROM shipping_requests WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$shipping_requests = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - DropEx</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .feedback-form, .request-form {
            background: #f8f9fa;
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">DropEx</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#feedback">Feedback</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#requests">Shipping Requests</a>
                    </li>
                </ul>
                <span class="navbar-text me-3">
                    Welcome, <?php echo htmlspecialchars($user_name); ?>
                </span>
                <a href="user_logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Feedback Section -->
        <section id="feedback" class="mb-5">
            <h3>Submit Feedback</h3>
            <div class="feedback-form">
                <?php if(isset($feedback_success)): ?>
                    <div class="alert alert-success"><?php echo $feedback_success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_name); ?>" disabled>
                    </div>
            <div class="request-form">
                <?php if(isset($request_success)): ?>
                    <div class="alert alert-success"><?php echo $request_success; ?></div>
                <?php endif; ?>
                <?php if(isset($request_error)): ?>
                    <div class="alert alert-danger"><?php echo $request_error; ?></div>
                <?php endif; ?>
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
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Sender Name</label>
                        <input type="text" class="form-control" name="sender_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sender Address</label>
                        <input type="text" class="form-control" name="sender_address" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Receiver Name</label>
                        <input type="text" class="form-control" name="receiver_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Receiver Address</label>
                        <input type="text" class="form-control" name="receiver_address" required>
                    </div>
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
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($shipping_requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['id']); ?></td>
                            <td><?php echo htmlspecialchars($request['sender_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['receiver_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['weight']); ?> kg</td>
                            <td class="status-<?php echo htmlspecialchars($request['status']); ?>">
                                <?php echo htmlspecialchars(ucfirst($request['status'])); ?>
                            </td>
                            <td><?php echo htmlspecialchars($request['created_at']); ?></td>
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