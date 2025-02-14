<?php
    session_start();
    include("db_connect.php");
    
    $sql = "SELECT * FROM staff WHERE credits = (SELECT MAX(credits) FROM staff)";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0){
        $empmonth = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }else{
        echo "Error : ". mysqli_error($conn);
    }

    $name = $email = $msg = '';
    $error = array('name' => '', 'email' => '', 'msg' => '');
    if(isset($_POST['submit'])){
        if(empty($_POST['name'])){
            $error['name'] = "*Required";
        }else{
            $name = mysqli_real_escape_string($conn, $_POST['name']);
        }
        if(empty($_POST['email'])){
            $error['email'] = "*Required";
        }else{
            if(email_validation($_POST['email'])){
                $email =  mysqli_real_escape_string($conn, $_POST['email']);
            }else{
                $error['email'] = "*Invalid email";
            }
        }
        if(empty($_POST['msg'])){
            $error['msg'] = "*Required";
        }else{
            $msg = mysqli_real_escape_string($conn, $_POST['msg']);
        }
        if(! array_filter($error)){
            $sql = "INSERT INTO feedback (Cust_name, Cust_mail, Cust_msg) VALUES ('$name', '$email', '$msg')";
            if(mysqli_query($conn, $sql)){
                echo '<script type="text/javascript">';
                echo "setTimeout(function () { swal('Thank You', 'Your response recorded successfully !!', 'success');";
                echo '}, 1000);</script>';
                $name = $email = $msg = '';
            }else{
                echo "Insert Error : " . mysqli_error($conn);
            }
        }
    }
    function email_validation($str) {
        return (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $str)) ? FALSE : TRUE;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DropEx - Global Logistics Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="min-h-screen">
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
                <a href="tracking.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Tracking</a>
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

    <!-- Hero Section -->
    <div class="pt-16 bg-gradient-to-r from-blue-50 to-indigo-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="lg:flex lg:items-center lg:space-x-8">
                <div class="lg:w-1/2 space-y-6">
                    <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl">Business Solutions for Global Logistics</h1>
                    <p class="text-lg text-gray-600">Discover shipping and logistics service options from <span class="font-semibold italic">Drop Ex</span> Global Forwarding.</p>
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-gray-900">Services Available</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center space-x-3">
                                <i class='bx bx-package text-blue-600 text-2xl'></i>
                                <span>Air Freight</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class='bx bx-package text-blue-600 text-2xl'></i>
                                <span>Road Freight</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class='bx bx-package text-blue-600 text-2xl'></i>
                                <span>Ocean Freight</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class='bx bx-package text-blue-600 text-2xl'></i>
                                <span>Rail Freight</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class='bx bx-time text-blue-600 text-2xl'></i>
                                <span>Express Delivery</span>
                            </div>
                        </div>
                        <a href="services.html" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            Explore DropEx
                        </a>
                    </div>
                </div>
                <div class="lg:w-1/2 mt-8 lg:mt-0">
                    <img src="Images/bigp.jpg" alt="Logistics" class="rounded-xl shadow-lg w-full h-auto object-cover">
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="lg:flex lg:items-start lg:space-x-8">
            <div class="lg:w-1/2 space-y-6">
                <h2 class="text-3xl font-bold text-gray-900">About Us</h2>
                <div class="prose prose-lg text-gray-600">
                    <p>Welcome to DropEx, your trusted partner in global shipping and logistics. At DropEx, we specialize in delivering reliable, fast, and seamless shipping solutions to meet the demands of an interconnected world.</p>
                    <h3 class="text-xl font-semibold text-gray-900 mt-6">Why Choose DropEx?</h3>
                    <ul class="space-y-3 list-none pl-0">
                        <li class="flex items-center space-x-3">
                            <i class='bx bx-check-circle text-green-500 text-xl'></i>
                            <span>Global Reach: Connecting major hubs and remote locations worldwide</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class='bx bx-check-circle text-green-500 text-xl'></i>
                            <span>Fast and Secure: Precision handling with guaranteed safety</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class='bx bx-check-circle text-green-500 text-xl'></i>
                            <span>24/7 Support: Dedicated team for continuous assistance</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class='bx bx-check-circle text-green-500 text-xl'></i>
                            <span>Eco-Friendly: Committed to sustainable logistics</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="lg:w-1/2 mt-8 lg:mt-0">
                <div class="space-y-4">
                    <img src="Images/aboutus.jpeg" alt="About DropEx" class="rounded-xl shadow-lg w-full h-64 object-cover">
                    <div class="grid grid-cols-3 gap-4">
                        <img src="Images/icon1.jpeg" alt="Service 1" class="rounded-lg w-full h-32 object-cover">
                        <img src="Images/icon2.jpeg" alt="Service 2" class="rounded-lg w-full h-32 object-cover">
                        <img src="Images/worker.jpeg" alt="Service 3" class="rounded-lg w-full h-32 object-cover">
                        <img src="Images/icon4.jpg" alt="Service 4" class="rounded-lg w-full h-32 object-cover">
                        <img src="Images/icon5.jpeg" alt="Service 5" class="rounded-lg w-full h-32 object-cover">
                        <img src="Images/aboutus.jpeg" alt="Service 6" class="rounded-lg w-full h-32 object-cover">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee of the Month -->

    <?php if(isset($_SESSION['id'])): ?>
        <div class="bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <img src="Images/ofthemonth.png" alt="Employee of the Month" class="mx-auto max-w-full h-auto mb-8">
                <?php foreach($empmonth as $emp) : ?>
                    <div class="bg-white rounded-xl shadow-sm p-6 max-w-2xl mx-auto">
                        <h3 class="text-2xl font-bold text-yellow-500 mb-2"><?php echo $emp['Name'] ?></h3>
                        <p class="text-gray-600">Staff ID: <?php echo $emp['StaffID'] ?></p>
                        <p class="text-gray-600">Credits: <?php echo $emp['Credits'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>                
    <?php endif; ?>

    <!-- Feedback Section -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">What Our Customers Say</h2>
                <p class="mt-4 text-lg text-gray-600">Recent feedback from our valued customers</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $sql = "SELECT * FROM feedback ORDER BY f_id DESC LIMIT 5";  // Changed LIMIT to 5
                $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) > 0):
                    while($feedback = mysqli_fetch_assoc($result)):
                ?>
                    <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex flex-col items-center text-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                                <i class='bx bxs-user-circle text-blue-600 text-2xl'></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1"><?php echo htmlspecialchars($feedback['Cust_name']); ?></h3>
                            </div>
                        </div>
                        <div class="relative mt-6 text-center">
                            <i class='bx bxs-quote-left absolute top-0 left-0 text-blue-200 text-3xl -translate-x-2 -translate-y-2'></i>
                            <p class="text-gray-700 leading-relaxed px-4">
                                "<?php echo htmlspecialchars($feedback['Cust_msg']); ?>"
                            </p>
                            <i class='bx bxs-quote-right absolute bottom-0 right-0 text-blue-200 text-3xl translate-x-2 translate-y-2'></i>
                        </div>
                    </div>
                <?php 
                    endwhile;
                endif;
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p class="text-sm">&copy; 2025 DropEx. All Rights Reserved. | Delivering Beyond Borders</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Logout confirmation
        document.querySelector('.btn-logout')?.addEventListener('click', function(e) {
            e.preventDefault();
            swal({
                title: "Logout",
                text: "Are you sure you want to logout?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willLogout) => {
                if (willLogout) {
                    window.location.href = 'logout.php';
                }
            });
        });
    </script>
</body>
</html>
