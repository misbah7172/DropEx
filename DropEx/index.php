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
<html>
    <head>
        <title>Drop Ex</title>
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
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <style>
            .carousel-inner img {
              width: 100%;
              height: 100%;
            }
            .btn-logout {
                color: #dc3545 !important;
            }
            .btn-logout:hover {
                color: #bd2130 !important;
            }
            .user-welcome {
                margin-right: 15px;
                color: #0056b3;
                font-weight: 500;
            }
        </style>
    </head>
    <body style="font-family: Arial, Helvetica, sans-serif;">
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
                                            <a class="nav-item nav-link text-dark mr-5 active" href="index.php">Home</a>
                                            <a class="nav-item nav-link text-dark mr-5" href="tracking.php">Tracking</a>
                                            <a class="nav-item nav-link text-dark mr-5" href="branches.php">Branches</a>
                                            <?php if(isset($_SESSION['id']) || isset($_SESSION['user_id'])): ?>
                                                <?php if(isset($_SESSION['id'])): ?>
                                                    <a class="nav-item nav-link text-dark mr-3" href="staff.php">Dashboard</a>
                                                <?php else: ?>
                                                    <a class="nav-item nav-link text-dark mr-3" href="user_dashboard.php">Dashboard</a>
                                                <?php endif; ?>
                                                <a class="nav-item nav-link btn-logout" href="logout.php">Logout</a>
                                            <?php else: ?>
                                                <a class="nav-item nav-link text-dark" href="login.php">DropEx Login</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </nav>

        <div class="container-fluid" style="width: 100%; padding: 0; margin: 0;">
            <div class="container-fluid mt-10" style="width: 85%; height: 100%; border-radius: 15px;">
                <div class="row">
                    <div class="col-md-6 p-5" style="background-color: rgba(255, 255, 255, 0.7); color: black; border-radius: 15px;">
                        <h2 class="display-5 text-center mb-3 pb-2" style="border-bottom: 2px solid white;">Business Only</h2>
                        <p>Discover shipping and logistics service options from <span style="font-weight: bold; font-style: italic;">Drop Ex</span> Global Forwarding.</p>
                        <h4>Services Available</h4>
                        <ul style="list-style-type: none; padding-left: 0;">
                            <li><i class='bx bx-package' style="font-size: 20px; margin-right: 10px"></i> Air Freight</li>
                            <li><i class='bx bx-package' style="font-size: 20px; margin-right: 10px"></i> Road Freight</li>
                            <li><i class='bx bx-package' style="font-size: 20px; margin-right: 10px"></i> Ocean Freight</li>
                            <li><i class='bx bx-package' style="font-size: 20px; margin-right: 10px"></i> Rail Freight</li>
                            <li><i class='bx bx-time' style="font-size: 20px; margin-right: 10px"></i> Express Delivery</li>
                        </ul>
                        <a href="services.html" class="btn btn-info mt-3">Explore DropEX</a>
                    </div>
                    <div class="col-md-6">
                        <img src="Images/bigp.jpg" style="height: 500px; width: 100%; border-radius: 15px;">
                    </div>
                </div>
            </div>
        </div>
         </div>
         <div class="container" id="about" style="margin-top: 20px; width: 85%;">
             <div class="row">
                <div class="col-md-6 p-5" style="background-color: rgba(255, 255, 255, 0.7); color: black; border-radius: 15px; ">
                    <h2 class="display-5 text-center mb-3 pb-2" style="border-bottom: 2px solid white;">About Us</h2>
                    <p>Welcome to DropEx, your trusted partner in global shipping and logistics. At DropEx, we specialize in delivering reliable, fast, and seamless shipping solutions to meet the demands of an interconnected world. With a strong focus on innovation, efficiency, and customer satisfaction, we bridge the gap between businesses and their customers across borders.</p>
                    <p>Our mission is to simplify international shipping by providing end-to-end solutions that cater to businesses of all sizes. Whether you’re shipping packages across the globe or managing large-scale freight logistics, DropEx is committed to ensuring that every delivery reaches its destination on time, every time.</p>
                    <h4>Why Choose DropEx?</h4>
                    <ul>
                        <li>Global Reach: We operate an extensive network that connects major hubs and remote locations worldwide.</li>
                        <li>Fast and Secure: Your shipments are handled with precision, speed, and care, ensuring safe delivery.</li>
                        <li>Customer-Centric Approach: Our dedicated team is available 24/7 to provide support and tailored solutions.</li>
                        <li>Sustainability Focus: We are committed to eco-friendly practices, leveraging sustainable logistics technologies to reduce our environmental footprint.</li>
                    </ul>
                    <p>At DropEx, we go beyond delivering parcels; we deliver trust, reliability, and peace of mind. Join us as we redefine the future of shipping and make global trade more accessible than ever.</p>
                </div>
                <div class="col-md-6">
                    <img src="Images/aboutus.jpeg" style="height: 500px; width: 100%; border-radius: 15px;" >
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <img src="Images/icon1.jpeg" style="width: 100%; height: 150px; border-radius: 15px;">
                        </div>
                        <div class="col-md-4">
                            <img src="Images/icon2.jpeg" style="width: 100%; height: 150px;border-radius: 15px;">
                        </div>
                        <div class="col-md-4">
                            <img src="Images/worker.jpeg" style="width: 100%; height: 150px;border-radius: 15px;">
                        </div>
                        <div class="col-md-4">
                            <img src="Images/icon4.jpg" style="width: 100%; height: 150px;border-radius: 15px;">
                        </div>
                        <div class="col-md-4">
                            <img src="Images/icon5.jpeg" style="width: 100%; height: 150px;border-radius: 15px;">
                        </div>
                        <div class="col-md-4">
                            <img src="Images/aboutus.jpeg" style="width: 100%; height: 150px;border-radius: 15px;">
                        </div>
                    </div>
                </div>
             </div>
         </div>
         <div class="container" style="margin-top: 20px; width: 85%;">
            <div class="row">
                <div class="col-md-12 text-center p-5" style="background-color: rgba(36, 35, 35, 0.3); color: black; ">
                    <img src="Images/ofthemonth.png" style="width: 100%; border-top:2px solid white;" >
                    <?php foreach($empmonth as $emp) : ?>
                        <div style="margin:auto !important; border-bottom:2px solid white;">
                            <p class="text-center pt-2" style="font-family: 'Times New Roman', Times, serif !important; font-size:x-large; color:gold;"><?php echo $emp['Name'] ?></p>
                            <p>Staff ID : <?php echo $emp['StaffID'] ?> </p>
                            <p>Credits : <?php echo $emp['Credits'] ?> </p>
                        </div>
                    <?php endforeach; ?>              
                </div>
             </div>
            
         </div>
        <style>
         footer {
            text-align: center;
            padding: 20px;
            background: #0056b3;
            color: #fff;
            margin-top: 20px;
        }

        footer p {
            margin: 0;
            font-size: 0.9em;
        }
    </style>
    <footer>
        <p>&copy; 2025 DropEx. All Rights Reserved. | Delivering Beyond Borders</p>
    </footer>
        <script>
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