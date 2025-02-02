<?php
// Start output buffering to prevent headers already sent error
ob_start();

// Start session only once
session_start();

// Include database connection
require_once "db_connect.php";

// Initialize variables
$tid = '';
$error = '';
$status = array('Dispatched' => '','Shipped' => '', 'Out_for_delivery' => '', 'Delivered' => '');
$hide = 'hidden';
$trackid = '';
$user_name = '';

// Get user name if logged in
if(isset($_SESSION['user_id'])) {
    // Assuming you have a users table with a username/name column
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT username FROM users WHERE id = ?"; // Use appropriate column name
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($row = $result->fetch_assoc()) {
        $user_name = $row['username'];
    }
    $stmt->close();
}

// Rest of your tracking logic
if(isset($_POST['track'])) {
    if(empty($_POST['tid'])) {
        $error = "*Required";
    } else {
        $tid = $_POST['tid'];
        $_SESSION['track_tid'] = $tid;
        if(empty($error)) {
            $hide = '';
            $trackid = $_SESSION['track_tid'];
            // Use prepared statement to prevent SQL injection
            $sql = "SELECT * FROM status WHERE TrackingID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $tid);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows > 0) {
                $status = $result->fetch_assoc();
                $active = array();
                if(!is_null($status['Delivered'])) {
                    $active['Delivered'] = $active['Out_for_delivery'] = $active['Shipped'] = 'active';
                } elseif(!is_null($status['Out_for_delivery'])) {
                    $active['Delivered'] = '';
                    $active['Out_for_delivery'] = $active['Shipped'] = 'active';
                } elseif(!is_null($status['Shipped'])) {
                    $active['Delivered'] = $active['Out_for_delivery'] = '';
                    $active['Shipped'] = 'active';
                }
            } else {
                $error = "Invalid Tracking ID";
            }
            $stmt->close();
        }
    }
}
    $hidden = 'hidden';
    if(isset($_POST['view'])){
        $trackid = $_SESSION['track_tid'];
        $hidden = $hide = '';
    } 
    $name = $add = $contact = '';
    $errors = array('name' => '', 'add' => '', 'cont' => '');
    if(isset($_POST['update'])){
        $hidden = $hide = '';
        $trackid = $_SESSION['track_tid'];
        if(empty($_POST['fname'])){
            $errors['name'] = "*Required";
        }else{
            $name = $_POST['fname'];
        }
        if(empty($_POST['fadd'])){
            $errors['add'] = "*Required";
        }else{
            $add = $_POST['fadd'];
        }
        if(empty($_POST['fcontact'])){
            $errors['cont'] = "*Required";
        }else{
            $contact = $_POST['fcontact'];
        }
        if(! array_filter($errors)){
            $trackid = $_SESSION['track_tid'];
            $sql = "UPDATE parcel SET R_Name = '$name', R_Add = '$add', R_Contact = $contact WHERE TrackingID = $trackid";
            if(mysqli_query($conn, $sql)){
                echo '<script type="text/javascript">';
                echo "setTimeout(function () { swal('Address Updated', 'Receiver address updated successfully !!', 'success');";
                echo '}, 1000);</script>';
                $hide  = $hidden =  'hidden';
                $trackid = '';
            }else{
                echo 'Update Error : '.mysqli_error($conn);
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Drop EX</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style/index_styles.css">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body class="font-sans bg-gray-100">



    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg fixed w-full z-50">
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
                    
                    <?php if(isset($_SESSION['id']) || isset($_SESSION['user_id'])): ?>
                        <?php if(isset($_SESSION['id'])): ?>
                            <a href="staff.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Dashboard</a>
                        <?php else: ?>
                            <a href="user_dashboard.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Dashboard</a>
                        <?php endif; ?>
                        <a href="logout.php" class="text-red-200 hover:text-red-400 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="bg-white text-blue-600 hover:bg-gray-100 hover:text-blue-700 transition-all duration-300 px-3 py-1.5 rounded-md text-sm font-medium shadow-md">Login</a>
                    <?php endif; ?>
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
                    <a href="logout.php" class="block text-red-200 hover:bg-red-400 px-3 py-2 rounded-md text-base font-medium">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="block bg-white text-blue-600 hover:bg-gray-200 px-4 py-2 rounded-md text-base font-medium shadow-md">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        // Toggle mobile menu
        mobileMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileMenu.classList.toggle('-translate-x-full');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target)) {
                mobileMenu.classList.add('-translate-x-full');
            }
        });

        // Close menu when a link is clicked
        const mobileMenuLinks = mobileMenu.querySelectorAll('a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('-translate-x-full');
            });
        });
    });
    </script>
<div class="mt-20"></div>
        

        <div class="container mx-auto mt-10 py-4">
            <div class="flex flex-wrap">
                <div class="w-full md:w-1/3 p-4 bg-white rounded-lg shadow-md text-center mt-5">
                    <img src="Images/track.png" class="mx-auto h-64 rounded-lg">
                    <form action="" method="POST" class="mt-4">
                        <label class="text-lg">Tracking ID:</label>
                        <input type="text" class="mt-2 p-2 border border-gray-300 rounded-lg w-full" name="tid" value="<?php echo $tid; ?>">
                        <label class="text-red-500"><?php echo $error; ?></label>
                        <input type="submit" name="track" class="mt-4 bg-blue-500 text-white p-2 rounded-lg w-full" value="Track">
                    </form>
                </div>
                <div class="w-full md:w-2/3 p-4 bg-white rounded-lg shadow-md mt-5">
                    <h3 class="text-2xl text-center border-b-2 pb-2 mb-3">Delivery Status</h3>
                    <label>Tracking ID: <?php echo $trackid; ?></label><br>
                    <div class="track bg-info">
                        <div class="step active"> <span class="icon"> <i class="fa fa-map-marker"></i> </span> <span class="text font-weight-bold"> Received </span><span><?php echo $status['Dispatched'];?></span> </div>
                        <div class="step <?php echo $active['Shipped']; ?>"> <span class="icon"> <i class="fa fa-truck"></i> </span> <span class="text font-weight-bold"> On the way </span><span><?php echo $status['Shipped'];?></span> </div>
                        <div class="step <?php echo $active['Out_for_delivery']; ?>"> <span class="icon"> <i class="fa fa-cubes"></i> </span> <span class="text font-weight-bold"> Out for delivery </span><span><?php echo $status['Out_for_delivery'];?></span> </div>
                        <div class="step <?php echo $active['Delivered']; ?>"> <span class="icon"> <i class="fa fa-check"></i> </span> <span class="text font-weight-bold">Delivered</span><span><?php echo $status['Delivered'];?></span> </div>
                    </div>
                    <div <?php echo $hide; ?> class="mt-8 p-6 bg-white rounded-lg shadow-md">
                        <h4 class="text-xl mb-4 text-gray-700">Need to Change Delivery Details?</h4>
                        <form action="tracking.php" method="POST" class="mb-4">
                            <p class="text-gray-600 mb-3">Redirect your package to a friend or family member in your city.</p>
                            <input type="submit" name="view" value="Update Delivery Address" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        </form>
                        <form action="tracking.php" method="POST" <?php echo $hidden; ?> class="space-y-4">
                            <h5 class="text-lg font-medium text-gray-700 mb-3">New Recipient Details</h5>
                            <div class="space-y-2">
                                <label class="block text-gray-700">Full Name</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                    name="fname" placeholder="Enter recipient's name">
                                <span class="text-red-500 text-sm"><?php echo $errors['name'];?></span>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-gray-700">Delivery Address</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                    name="fadd" placeholder="Enter complete address">
                                <span class="text-red-500 text-sm"><?php echo $errors['add'];?></span>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-gray-700">Contact Number</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                    name="fcontact" placeholder="Enter contact number">
                                <span class="text-red-500 text-sm"><?php echo $errors['cont'];?></span>
                            </div>
                            <input type="submit" name="update" value="Update Details" 
                                class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <footer class="py-4 mt-8 text-center bg-gray-800 text-white">
            <p>&copy; 2025 DropEx. All Rights Reserved. | Delivering Beyond Borders</p>
        </footer>
    </body>
</html>
