<?php 
include("db_connect.php");
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Get staff details
$id = $_SESSION['id'];
$sql = "SELECT * FROM staff WHERE StaffID=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
$name = $staff['Name'];

// Initialize variables
$sname = $sadd = $scity = $sstate = $scontact = $rname = $radd = $rcity = $rstate = $rcontact = $wgt = '';
$errors = array();

// Handle form submission
if(isset($_POST['submit'])){
    // Validate sender details
    $sname = trim($_POST['sname']);
    $sadd = trim($_POST['sadd']);
    $scity = trim($_POST['scity']);
    $sstate = trim($_POST['sstate']);
    $scontact = trim($_POST['scontact']);
    
    // Validate receiver details
    $rname = trim($_POST['rname']);
    $radd = trim($_POST['radd']);
    $rcity = trim($_POST['rcity']);
    $rstate = trim($_POST['rstate']);
    $rcontact = trim($_POST['rcontact']);
    $wgt = trim($_POST['wgt']);
    
    // Validation checks
    if(empty($sname)) $errors['sname'] = 'Sender name is required';
    if(empty($sadd)) $errors['sadd'] = 'Sender address is required';
    if(empty($scity)) $errors['scity'] = 'Sender city is required';
    if(empty($sstate)) $errors['sstate'] = 'Sender state is required';
    if(empty($scontact)) $errors['scontact'] = 'Sender contact is required';
    if(!preg_match("/^[0-9]{10}$/", $scontact)) $errors['scontact'] = 'Invalid contact number';
    
    if(empty($rname)) $errors['rname'] = 'Receiver name is required';
    if(empty($radd)) $errors['radd'] = 'Receiver address is required';
    if(empty($rcity)) $errors['rcity'] = 'Receiver city is required';
    if(empty($rstate)) $errors['rstate'] = 'Receiver state is required';
    if(empty($rcontact)) $errors['rcontact'] = 'Receiver contact is required';
    if(!preg_match("/^[0-9]{10}$/", $rcontact)) $errors['rcontact'] = 'Invalid contact number';
    
    if(empty($wgt)) $errors['wgt'] = 'Weight is required';
    if(!is_numeric($wgt) || $wgt <= 0) $errors['wgt'] = 'Invalid weight';

    // Process if no errors
    if(empty($errors)) {
        // Check if service is available for the route
        $sql = "SELECT * FROM pricing WHERE State_1 = ? AND State_2 = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $sstate, $rstate);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $pricing = $result->fetch_assoc();
            $price = $pricing['Cost'] * $wgt;
            
            // Insert order into database using prepared statement
            $sql = "INSERT INTO parcel (StaffID, S_Name, S_Add, S_City, S_State, S_Contact, 
                    R_Name, R_Add, R_City, R_State, R_Contact, Weight_Kg, Price) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssssssssdl", 
                $id, 
                $sname, $sadd, $scity, $sstate, $scontact,
                $rname, $radd, $rcity, $rstate, $rcontact,
                $wgt, $price
            );
            
            if($stmt->execute()) {
                $_SESSION['tid'] = $stmt->insert_id;
                header("Location: receipt.php");
                exit();
            } else {
                echo '<script>swal("Error", "Failed to create order: ' . $stmt->error . '", "error");</script>';
            }
        } else {
            echo '<script>swal("Service Not Available", "We don\'t have service available between these states", "info");</script>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>DropEx - New Order</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
            body {
                font-family: 'Roboto', sans-serif;
                background-color: #f5f5f5;
            }
            .header {
                background: white;
                padding: 15px 0;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .form-container {
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                margin-top: 30px;
            }
            .form-group {
                margin-bottom: 20px;
            }
            .error-text {
                color: #dc3545;
                font-size: 0.875rem;
                margin-top: 5px;
            }
            .submit-btn {
                background-color: #0056b3;
                color: white;
                padding: 12px 25px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                width: 100%;
                font-size: 16px;
                margin-top: 20px;
            }
            .submit-btn:hover {
                background-color: #004494;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-10">
                        <img src="Images/logo.png" alt="DropEx Logo" style="height: 60px;">
                    </div>
                    <div class="col-2 text-right">
                        <span class="font-weight-bold"><?php echo htmlspecialchars($name); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container form-container">
            <h2 class="text-center mb-4">New Shipping Order</h2>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" novalidate>
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mb-3">Sender's Details</h4>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="sname" class="form-control <?php echo isset($errors['sname']) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($sname); ?>">
                            <?php if(isset($errors['sname'])): ?>
                                <div class="error-text"><?php echo $errors['sname']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="sadd" class="form-control <?php echo isset($errors['sadd']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo htmlspecialchars($sadd); ?>">
                            <?php if(isset($errors['sadd'])): ?>
                                <div class="error-text"><?php echo $errors['sadd']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="scity" class="form-control <?php echo isset($errors['scity']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo htmlspecialchars($scity); ?>">
                            <?php if(isset($errors['scity'])): ?>
                                <div class="error-text"><?php echo $errors['scity']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="sstate" class="form-control <?php echo isset($errors['sstate']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo htmlspecialchars($sstate); ?>">
                            <?php if(isset($errors['sstate'])): ?>
                                <div class="error-text"><?php echo $errors['sstate']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Contact</label>
                            <input type="text" name="scontact" class="form-control <?php echo isset($errors['scontact']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo htmlspecialchars($scontact); ?>" maxlength="10">
                            <?php if(isset($errors['scontact'])): ?>
                                <div class="error-text"><?php echo $errors['scontact']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4 class="mb-3">Receiver's Details</h4>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="rname" class="form-control <?php echo isset($errors['rname']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo htmlspecialchars($rname); ?>">
                            <?php if(isset($errors['rname'])): ?>
                                <div class="error-text"><?php echo $errors['rname']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="radd" class="form-control <?php echo isset($errors['radd']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo htmlspecialchars($radd); ?>">
                            <?php if(isset($errors['radd'])): ?>
                                <div class="error-text"><?php echo $errors['radd']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="rcity" class="form-control <?php echo isset($errors['rcity']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo htmlspecialchars($rcity); ?>">
                            <?php if(isset($errors['rcity'])): ?>
                                <div class="error-text"><?php echo $errors['rcity']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="rstate" class="form-control <?php echo isset($errors['rstate']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo htmlspecialchars($rstate); ?>">
                            <?php if(isset($errors['rstate'])): ?>
                                <div class="error-text"><?php echo $errors['rstate']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Contact</label>
                            <input type="text" name="rcontact" class="form-control <?php echo isset($errors['rcontact']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo htmlspecialchars($rcontact); ?>" maxlength="10">
                            <?php if(isset($errors['rcontact'])): ?>
                                <div class="error-text"><?php echo $errors['rcontact']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Weight (kg)</label>
                            <input type="number" step="0.1" name="wgt" class="form-control <?php echo isset($errors['wgt']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo htmlspecialchars($wgt); ?>">
                            <?php if(isset($errors['wgt'])): ?>
                                <div class="error-text"><?php echo $errors['wgt']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <button type="submit" name="submit" class="submit-btn">Place Order</button>
            </form>
        </div>

        <footer class="container-fluid text-center mt-5" style="background-color: rgba(255, 255, 255, 0.7); padding: 20px;">
            <p class="mb-0">&copy; 2025 DropEx | Delivering Beyond Borders</p>
        </footer>
    </body>
</html>