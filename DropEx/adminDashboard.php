<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle common functions
function sanitizeInput($conn, $input) {
    return mysqli_real_escape_string($conn, trim($input));
}

// Comprehensive validation function
function validateStaffInput($input) {
    $errors = [];

    // Required fields validation
    $required_fields = [
        'new_staff_id' => 'Staff ID',
        'name' => 'Name',
        'pass' => 'Password',
        'designation' => 'Designation', 
        'branch' => 'branch',
        'gender' => 'Gender',
        'dob' => 'DOB',
        'doj' => 'DOJ',
        'salary' => 'Salary',
        'mobile' => 'Mobile',
        'email' => 'Email',
        'credits' => 'Credits'
    ];

    foreach ($required_fields as $field => $label) {
        if (empty($input[$field])) {
            $errors[] = "$label is required";
        }
    }

    // Email validation
    if (!empty($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Mobile validation (11 digits)
    if (!empty($input['mobile']) && !preg_match("/^[0-9]{11}$/", $input['mobile'])) {
        $errors[] = "Mobile number must be 10 digits";
    }

    return $errors;
}

// Handle staff addition
if(isset($_POST['add_staff'])) {
    // Sanitize and validate input
    $staff_id = mysqli_real_escape_string($conn, $_POST['new_staff_id']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']); 
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $doj = mysqli_real_escape_string($conn, $_POST['doj']);
    $salary = filter_var($_POST['salary'], FILTER_VALIDATE_INT);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $credits = 0;
    $branch = mysqli_real_escape_string($conn, $_POST['branch']);

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
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_sql);
            
            if($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssssissss", 
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

// Handle staff deletion
if(isset($_POST['delete_staff'])) {
    $staff_id = sanitizeInput($conn, $_POST['staff_id']);
    
    $delete_sql = "DELETE FROM staff WHERE StaffID = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "s", $staff_id);
    
    $result = mysqli_stmt_execute($stmt) ? 
        ["success" => "Staff member deleted successfully"] : 
        ["error" => "Error deleting staff member: " . mysqli_error($conn)];
    
    mysqli_stmt_close($stmt);
}

// Search functionality by branch
if(isset($_POST['search_branch'])) {
    $branch = sanitizeInput($conn, $_POST['branch']);
    $search_sql = "SELECT * FROM staff WHERE branch = ?";
    $stmt = mysqli_prepare($conn, $search_sql);
    mysqli_stmt_bind_param($stmt, "s", $branch);
    mysqli_stmt_execute($stmt);
    $search_result = mysqli_stmt_get_result($stmt);
}

// Search functionality
$search_results = [];
if(isset($_GET['search_staffid'])) {
    $search_id = sanitizeInput($conn, $_GET['search_staffid']);
    $search_sql = "SELECT * FROM staff WHERE StaffID LIKE ?";
    $stmt = mysqli_prepare($conn, $search_sql);
    $search_param = "%{$search_id}%";
    mysqli_stmt_bind_param($stmt, "s", $search_param);
    mysqli_stmt_execute($stmt);
    $search_result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($search_result)) {
        $search_results[] = $row;
    }
}

// Fetch staff and managers for list view
$staff_sql = "SELECT * FROM staff WHERE Designation = 'Staff' ORDER BY StaffID";
$staff_result = mysqli_query($conn, $staff_sql);

$manager_sql = "SELECT * FROM staff WHERE Designation = 'Manager' ORDER BY StaffID";
$manager_result = mysqli_query($conn, $manager_sql);

// Fetch feedback
$feedback_sql = "SELECT * FROM feedback ORDER BY f_id DESC";
$feedback_result = mysqli_query($conn, $feedback_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DropEx Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .navbar {
            background-color: #2c3e50 !important;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.1);
        }
        .btn-custom {
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .modal-header {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">DropEx Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Staff Management
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#staffListModal">Staff List</a></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#managerListModal">Manager List</a></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addStaffModal">Add New Staff</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#feedbackModal">Feedback</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php 
        if(isset($result['success'])) {
            echo "<div class='alert alert-success'>{$result['success']}</div>";
        }
        if(isset($result['error'])) {
            echo "<div class='alert alert-danger'>{$result['error']}</div>";
        }
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card admin-card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Staff Search</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="input-group">
                                <input type="text" name="search_staffid" class="form-control" 
                                       placeholder="Search by Staff ID" 
                                       value="<?php echo $_GET['search_staffid'] ?? ''; ?>">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </form>

                        <?php if(!empty($search_results)): ?>
                        <div class="table-responsive mt-3">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Staff ID</th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Branch</th>
                                        <th>Joining Date</th>
                                        <th>Mobile</th>
                                        <th>Salary</th>
                                        <th>Credits</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($search_results as $staff): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($staff['StaffID']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['Name']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['Designation']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['branch']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['DOJ']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['Mobile']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['Salary']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['Credits']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search by branch -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card admin-card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Branch Search</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="input-group">
                                <input type="text" name="search_branch" class="form-control" 
                                       placeholder="Search by Branch" 
                                       value="<?php echo $_GET['search_branch'] ?? ''; ?>">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </form>

                        <?php if(!empty($search_branch_results)): ?>
                        <div class="table-responsive mt-3">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Branch ID</th>
                                        <th>Address</th>
                                        <th>Contact</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($search_branch_results as $branch): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($branch['BranchID']); ?></td>
                                        <td><?php echo htmlspecialchars($branch['Address']); ?></td>
                                        <td><?php echo htmlspecialchars($branch['Contact']); ?></td>
                                        <td><?php echo htmlspecialchars($branch['Email']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Add Staff Modal -->
    <div class="modal fade" id="addStaffModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
        </div>
    </div>

    <!-- Staff List Modal -->
    <div class="modal fade" id="staffListModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Staff List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Name</th>
                                    <th>Branch</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                mysqli_data_seek($staff_result, 0);
                                while($staff = mysqli_fetch_assoc($staff_result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($staff['StaffID']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['Name']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['branch']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['Email']); ?></td>
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
        </div>
    </div>

    <!-- Manager List Modal -->
    <div class="modal fade" id="managerListModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manager List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Name</th>
                                    <th>Branch</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                mysqli_data_seek($manager_result, 0);
                                while($manager = mysqli_fetch_assoc($manager_result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($manager['StaffID']); ?></td>
                                    <td><?php echo htmlspecialchars($manager['Name']); ?></td>
                                    <td><?php echo htmlspecialchars($manager['branch']); ?></td>
                                    <td><?php echo htmlspecialchars($manager['Email']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Detail Modal -->
    <div class="modal fade" id="staffDetailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Staff Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="staffDetailsContent">
                    <!-- Details will be dynamically populated -->
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Feedback ID</th>
                                    <th>Customer Name</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($feedback = mysqli_fetch_assoc($feedback_result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($feedback['f_id']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['Cust_name']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['Cust_mail']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['Cust_msg']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showStaffDetails(staff) {
            const detailsContent = document.getElementById('staffDetailsContent');
            detailsContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Staff ID:</strong> ${staff.StaffID}</p>
                        <p><strong>Name:</strong> ${staff.Name}</p>
                        <p><strong>Designation:</strong> ${staff.Designation}</p>
                        <p><strong>Branch:</strong> ${staff.branch}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Gender:</strong> ${staff.Gender === 'M' ? 'Male' : 'Female'}</p>
                        <p><strong>Date of Birth:</strong> ${staff.DOB}</p>
                        <p><strong>Date of Joining:</strong> ${staff.DOJ}</p>
                        <p><strong>Salary:</strong> ${staff.Salary}</p>
                    </div>
                    <div class="col-12">
                        <p><strong>Email:</strong> ${staff.Email}</p>
                        <p><strong>Mobile:</strong> ${staff.Mobile}</p>
                    </div>
                </div>
            `;
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Clear all form inputs
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.reset();
    });

    // Clear search input fields
    const searchInputs = document.querySelectorAll('input[name="search_staffid"], input[name="search_branch"]');
    searchInputs.forEach(input => {
        input.value = '';
    });

    // Clear URL parameters to prevent form resubmission
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.pathname);
    }

    // Optional: Remove success/error messages
    const alertMessages = document.querySelectorAll('.alert');
    alertMessages.forEach(alert => {
        alert.remove();
    });
});
    </script>
</body>
</html>
