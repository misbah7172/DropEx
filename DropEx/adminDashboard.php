<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

//Handle feedback deletion
if(isset($_POST['delete_feedback'])) {
    $feedback_id = mysqli_real_escape_string($conn, $_POST['feedback_id']);
    
    // Delete from feedback table using prepared statement
    $delete_sql = "DELETE FROM feedback WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    if($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $feedback_id);
        if(mysqli_stmt_execute($stmt)) {
            $success_message = "Feedback deleted successfully";
        } else {
            $error_message = "Error deleting feedback: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Error preparing delete statement: " . mysqli_error($conn);
    }
}

// Handle staff deletion
if(isset($_POST['delete_staff'])) {
    $staff_id = mysqli_real_escape_string($conn, $_POST['staff_id']);
    
    // Delete from staff table using prepared statement
    $delete_sql = "DELETE FROM staff WHERE StaffID = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    if($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $staff_id);
        if(mysqli_stmt_execute($stmt)) {
            $success_message = "Staff member deleted successfully";
        } else {
            $error_message = "Error deleting staff member: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Error preparing delete statement: " . mysqli_error($conn);
    }
}

// Handle staff addition
if(isset($_POST['add_staff'])) {
    // Sanitize and validate input
    $staff_id = mysqli_real_escape_string($conn, $_POST['new_staff_id']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']); // Store password as is since no hashing requirement specified
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    $branch = mysqli_real_escape_string($conn, $_POST['branch']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $doj = mysqli_real_escape_string($conn, $_POST['doj']);
    $salary = filter_var($_POST['salary'], FILTER_VALIDATE_INT);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $credits = 0;

    // Validate required fields
    if(!$staff_id || !$name || !$designation || !$branch || !$gender || !$dob || !$doj || !$salary || !$mobile || !$email) {
        $error_message = "All fields are required and must be valid";
    } else {
        // Check if StaffID already exists
        $check_sql = "SELECT StaffID FROM staff WHERE StaffID = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $staff_id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if(mysqli_stmt_num_rows($check_stmt) > 0) {
            $error_message = "Staff ID already exists";
        } else {
            // Insert into staff table using prepared statement
            $insert_sql = "INSERT INTO staff (StaffID, Name, Designation, branch, Gender, DOB, DOJ, Salary, Mobile, Email, Credits, pass) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_sql);
            
            if($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssssissss", 
                    $staff_id, $name, $designation, $branch, $gender, $dob, $doj, $salary, $mobile, $email, $credits, $pass);
                
                if(mysqli_stmt_execute($stmt)) {
                    $success_message = "Staff member added successfully";
                } else {
                    $error_message = "Error adding staff member: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error_message = "Error preparing insert statement: " . mysqli_error($conn);
            }
        }
        mysqli_stmt_close($check_stmt);
    }
}
// Fetch staff members
$staff_sql = "SELECT * FROM staff WHERE Designation = 'Staff' ORDER BY StaffID";
$staff_result = mysqli_query($conn, $staff_sql);

// Fetch managers separately
$manager_sql = "SELECT * FROM staff WHERE Designation = 'Manager' ORDER BY StaffID";
$manager_result = mysqli_query($conn, $manager_sql);

// Fetch all feedback
$feedback_sql = "SELECT * FROM feedback";
$feedback_result = mysqli_query($conn, $feedback_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DropEx Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('Images/admindasb.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">DropEx Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="admin_logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if(isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if(isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12 mb-4">
                <h2 style="color:rgb(255, 255, 255);">Staff Management</h2>
                <!-- Add Staff Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Add New Staff</h4>
                    </div>
                    <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <input type="text" class="form-control" name="new_staff_id" placeholder="Staff ID" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <input type="text" class="form-control" name="name" placeholder="Name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <input type="password" class="form-control" name="pass" placeholder="Password" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <input type="text" class="form-control" name="designation" placeholder="Designation" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <input type="text" class="form-control" name="branch" placeholder="Branch" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select class="form-control" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <input type="date" class="form-control" name="dob" placeholder="Date of Birth" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <input type="date" class="form-control" name="doj" placeholder="Date of Joining" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <input type="number" class="form-control" name="salary" placeholder="Salary" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <input type="tel" class="form-control" name="mobile" placeholder="Mobile" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                        </div>
                        <button type="submit" name="add_staff" class="btn btn-primary">Add Staff</button>
                    </form>
                </div>
            </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Staff List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Staff ID</th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Branch</th>
                                        <th>Gender</th>
                                        <th>DOB</th>
                                        <th>DOJ</th>
                                        <th>Salary</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Credits</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($staff = mysqli_fetch_assoc($staff_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($staff['StaffID']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['Name']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['Designation']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['branch']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['Gender']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['DOB']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['DOJ']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['Salary']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['Mobile']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['Email']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['Credits']); ?></td>
                                        <td>
                                            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                                <input type="hidden" name="staff_id" value="<?php echo $staff['StaffID']; ?>">
                                                <button type="submit" name="delete_staff" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Manager List -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Manager List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Staff ID</th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Branch</th>
                                        <th>Gender</th>
                                        <th>DOB</th>
                                        <th>DOJ</th>
                                        <th>Salary</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Credits</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($manager = mysqli_fetch_assoc($manager_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($manager['StaffID']); ?></td>
                                        <td><?php echo htmlspecialchars($manager['Name']); ?></td>
                                        <td><?php echo htmlspecialchars($manager['Designation']); ?></td>
                                        <td><?php echo htmlspecialchars($manager['branch']); ?></td>
                                        <td><?php echo htmlspecialchars($manager['Gender']); ?></td>
                                        <td><?php echo htmlspecialchars($manager['DOB']); ?></td>
                                        <td><?php echo htmlspecialchars($manager['DOJ']); ?></td>
                                        <td><?php echo htmlspecialchars($manager['Salary']); ?></td>
                                        <td><?php echo htmlspecialchars($manager['Mobile']); ?></td>
                                        <td><?php echo htmlspecialchars($manager['Email']); ?></td>
                                        <td><?php echo htmlspecialchars($manager['Credits']); ?></td>
                                        <td>
                                            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this manager?');">
                                                <input type="hidden" name="staff_id" value="<?php echo $manager['StaffID']; ?>">
                                                <button type="submit" name="delete_staff" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback Section -->
            <div class="col-md-12">
                <h2 style="color:rgb(255, 255, 255);">Customer Feedback</h2>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Customer Name</th>
                                        <th>Email</th>
                                        <th>Message</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($feedback = mysqli_fetch_assoc($feedback_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($feedback['f_id']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['Cust_name']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['Cust_mail']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['Cust_msg']); ?></td>
                                        <td>
                                            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                                <input type="hidden" name="feedback_id" value="<?php echo $feedback['f_id']; ?>">
                                                <button type="submit" name="delete_feedback" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
