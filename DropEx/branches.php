<?php
// Start session only once
session_start();

    include("db_connect.php");
    $sql = "SELECT * FROM branches";
    $result = mysqli_query($conn, $sql);
    $branches = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>DropEX</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body style="font-family: Arial, Helvetica, sans-serif;">
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
                <a href="tracking.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Tracking</a>
                <?php if(isset($_SESSION['id'])): ?>
                    <a href="staff.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Dashboard</a>
                    <?php else: ?>
                    <a href="user_dashboard.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Dashboard</a>
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
            <a href="index.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Home</a>
            <a href="tracking.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Tracking</a>
            <?php if(isset($_SESSION['id']) || isset($_SESSION['user_id'])): ?>
                <?php if(isset($_SESSION['id'])): ?>
                    <a href="staff.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Dashboard</a>
                <?php else: ?>
                    <a href="user_dashboard.php" class="text-white hover:text-blue-200 transition-colors duration-300 px-2 py-1 rounded-md text-sm font-medium">Dashboard</a>
                <?php endif; ?>
                <a href="logout.php" class="block text-red-200 hover:bg-red-400 px-3 py-2 rounded-md text-base font-medium">Logout</a>
            <?php else: ?>
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12"></div>
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Our Branches</h2>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach($branches as $branch) : ?>
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6">
                    <ul class="space-y-4">
                        <li class="flex items-center text-gray-700">
                            <i class="fa fa-map-marker text-blue-600 text-xl w-8"></i>
                            <span class="ml-2"><?php echo htmlspecialchars($branch['Address']); ?></span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fa fa-phone text-blue-600 text-xl w-8"></i>
                            <span class="ml-2"><?php echo htmlspecialchars($branch['Contact']); ?></span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fa fa-envelope text-blue-600 text-xl w-8"></i>
                            <span class="ml-2"><?php echo htmlspecialchars($branch['Email']); ?></span>
                        </li>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <footer class="bg-blue-600 text-white py-6 mt-12">
        <p class="text-center">&copy; 2025 DropEx. All Rights Reserved. | Delivering Beyond Borders</p>
    </footer>
</div>
