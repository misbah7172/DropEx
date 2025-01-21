<?php
session_start();
include("db_connect.php");

// Check if staff is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get staff details
$staff_id = $_SESSION['user_id'];
$sql = "SELECT * FROM staff WHERE StaffID=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $staff_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$staff = mysqli_fetch_assoc($result);
$staff_name = $staff['Name'];

// Fetch all pricing data
$sql = "SELECT * FROM pricing ORDER BY state_1, state_2";
$result = mysqli_query($conn, $sql);
$pricing_list = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle Add/Edit/Delete operations if needed
if(isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'add':
            if(isset($_POST['state_1'], $_POST['state_2'], $_POST['cost'])) {
                $state_1 = mysqli_real_escape_string($conn, $_POST['state_1']);
                $state_2 = mysqli_real_escape_string($conn, $_POST['state_2']);
                $cost = floatval($_POST['cost']);
                
                $sql = "INSERT INTO pricing (state_1, state_2, cost) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssd", $state_1, $state_2, $cost);
                
                if(mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Price added successfully!');</script>";
                } else {
                    echo "<script>alert('Error adding price!');</script>";
                }
            }
            break;
            
        case 'edit':
            if(isset($_POST['user_id'], $_POST['cost'])) {
                $user_id = intval($_POST['user_id']);
                $cost = floatval($_POST['cost']);
                
                $sql = "UPDATE pricing SET cost = ? WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "di", $cost, $user_id);
                
                if(mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Price updated successfully!');</script>";
                } else {
                    echo "<script>alert('Error updating price!');</script>";
                }
            }
            break;
            
        case 'delete':
            if(isset($_POST['id'])) {
                $id = intval($_POST['id']);
                
                $sql = "DELETE FROM pricing WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $id);
                
                if(mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Price deleted successfully!');</script>";
                } else {
                    echo "<script>alert('Error deleting price!');</script>";
                }
            }
            break;
    }
    
    // Refresh the page after operations
    header("Location: pricing.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing List</title>
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
        .pricing-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }
        .add-price-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Shipping Price List</h2>
        <div class="mb-3">
            <strong>Staff ID: </strong><?php echo htmlspecialchars($staff_id); ?><br>
            <strong>Staff Name: </strong><?php echo htmlspecialchars($staff_name); ?>
        </div>

        <!-- Add New Price Form -->
        <div class="add-price-form">
            <h4>Add New Price</h4>
            <form method="POST" action="" class="row">
                <input type="hidden" name="action" value="add">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From State</label>
                        <input type="text" name="state_1" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To State</label>
                        <input type="text" name="state_2" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Cost (₹)</label>
                        <input type="number" step="0.01" name="cost" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Add Price</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Pricing List Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>From State</th>
                        <th>To State</th>
                        <th>Cost (₹)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pricing_list as $price): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($price['state_1']); ?></td>
                        <td><?php echo htmlspecialchars($price['state_2']); ?></td>
                        <td>₹<?php echo htmlspecialchars(number_format($price['cost'], 2)); ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editPrice(<?php echo $price['id']; ?>)">
                                Edit
                            </button>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $price['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this price?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Price Modal -->
    <div class="modal fade" id="editPriceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Price</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_price_id">
                        <div class="form-group">
                            <label>Cost (₹)</label>
                            <input type="number" step="0.01" name="cost" id="edit_cost" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editPrice(id) {
            // You would typically fetch the current price here via AJAX
            // For now, we'll just open the modal
            $('#edit_price_id').val(id);
            $('#editPriceModal').modal('show');
        }
    </script>
</body>
</html>