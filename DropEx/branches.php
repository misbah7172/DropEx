<?php
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
        <link rel="stylesheet" href="style/bootstrap.css">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="style/index_styles.css">
       <!-- <link rel="icon" type="image/png" sizes="32x32" href="Images/blank.png"> -->
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body style="font-family: Arial, Helvetica, sans-serif;">
        <nav class="navbar navbar-toggleable-md navbar-expand-lg navbar-default navbar-light mb-10" style="background-color: rgba(255, 255, 255, 0.7); margin-bottom: 20px; margin-top:10px !important;">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="Images/logo.png" id="logo" style="height: 50px !important; margin-top: 10px !important;">
                </a>
                <button class="navbar-toggler text-dark" data-toggle="collapse" data-target="#mainNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <div class="navbar-nav ml-auto" style="font-size: large;">
                        <a class="nav-item nav-link text-dark mr-5" href="index.php">Home</a>
                        <a class="nav-item nav-link text-dark mr-5" href="tracking.php">Tracking</a>
                        <a class="nav-item nav-link text-dark mr-5 active" href="branches.php">Branches</a>
                        <?php if(isset($_SESSION['id'])): ?>
                            <a class="nav-item nav-link text-dark mr-3" href="staff.php">Dashboard</a>
                            <a class="nav-item nav-link btn-logout" href="logout.php">Logout</a>
                        <?php else: ?>
                            <a class="nav-item nav-link text-dark" href="login.php">DropEx Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
        <div class="container">
            <div class="row">
            <?php foreach($branches as $branch) : ?>
                
                <div class="p-3 col-12" style="background-color: rgba(255, 255, 255, 0.7); margin-top:5px !important;">
                    <ul style="list-style-type:none;">
                        <li><a href="#" class="fa fa-map-marker m-1" style="pointer-events: none;"></a><?php echo '  '.$branch['Address']; ?></li>
                        <li><a href="#" class="fa fa-phone m-1" style="pointer-events: none;"></a><?php echo '  '.$branch['Contact']; ?></li>
                        <li><a href="#" class="fa fa-envelope m-1" style="pointer-events: none;"></a><?php echo '  '.$branch['Email']; ?></li>
                    </ul>
                </div>
            <?php endforeach; ?>
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
    </body>
</html>