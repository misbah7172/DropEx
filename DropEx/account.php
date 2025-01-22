<?php
    session_start();
    include("db_connect.php");
    
    // Check if user is logged in
    if (!isset($_SESSION['id'])) {
        header("Location: login.php");
        exit();
    }
    
    // Prevent SQL injection using prepared statements
    $id = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT * FROM staff WHERE StaffID = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $staff = $result->fetch_assoc();
    
    // If no staff found, redirect
    if (!$staff) {
        header("Location: login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>DropEx - Account Details</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="style/bootstrap.css">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="style/index_styles.css">
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
            body {
                font-family: 'Roboto', sans-serif;
                background: linear-gradient(to right, #6a11cb, #2575fc);
                color: #333;
            }
            .navbar {
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            .container {
                background-color: rgba(255, 255, 255, 0.9);
                border-radius: 15px;
                padding: 20px;
                margin-top: 20px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            table {
                margin-top: 20px;
            }
            table td {
                padding: 10px;
            }
            footer {
                text-align: center;
                padding: 20px;
                background: #0056b3;
                color: #fff;
                margin-top: 20px;
                border-radius: 0 0 15px 15px;
            }
            footer p {
                margin: 0;
                font-size: 0.9em;
            }
        </style>
    </head>
    <body>
        <div class="text-center">
            <img src="Images/logo.png" id="logo" alt="DropEx Logo" style="height: 100px; margin-top: 10px;">
        </div>
        <nav class="navbar navbar-toggleable-md navbar-expand-lg navbar-default navbar-light mb-10" style="background-color: rgba(255, 255, 255, 0.8);">
            <div class="container">
                <button class="navbar-toggler text-dark" data-toggle="collapse" data-target="#mainNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <div class="navbar-nav" style="margin: 0 auto; font-size: large;">
                        <a class="nav-item nav-link text-dark mr-5" href="staff.php">Back</a>
                        <a class="nav-item nav-link text-dark" href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="container text-center">
            <h3>Account Details</h3>
            <img src="Images/pp2.png" id="profile-pic" alt="Profile Picture" style="height: 90px; border-radius: 50%; object-fit: cover;">
            <table class="text-left table table-bordered table-striped">
                <tr><td style="font-weight:bold;">Name</td><td><?php echo htmlspecialchars($staff['Name']); ?></td></tr>
                <tr><td style="font-weight:bold;">Staff ID</td><td><?php echo htmlspecialchars($staff['StaffID']); ?></td></tr>
                <tr><td style="font-weight:bold;">Designation</td><td><?php echo htmlspecialchars($staff['Designation']); ?></td></tr>
                <tr><td style="font-weight:bold;">Branch</td><td><?php echo htmlspecialchars($staff['branch']); ?></td></tr>
                <tr><td style="font-weight:bold;">Gender</td><td><?php echo htmlspecialchars($staff['Gender']); ?></td></tr>
                <tr><td style="font-weight:bold;">DOB</td><td><?php echo htmlspecialchars($staff['DOB']); ?></td></tr>
                <tr><td style="font-weight:bold;">DOJ</td><td><?php echo htmlspecialchars($staff['DOJ']); ?></td></tr>
                <tr><td style="font-weight:bold;">Email</td><td><?php echo htmlspecialchars($staff['Email']); ?></td></tr>
                <tr><td style="font-weight:bold;">Mobile</td><td><?php echo htmlspecialchars($staff['Mobile']); ?></td></tr>
                <tr><td style="font-weight:bold;">Credits</td><td><?php echo htmlspecialchars($staff['Credits']); ?></td></tr>
            </table>
        </div>
        <footer>
            <p>&copy; 2025 DropEx. All Rights Reserved. | Delivering Beyond Borders</p>
        </footer>
    </body>
</html>
