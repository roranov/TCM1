<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Transport Management System</title>

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- FONT AWESOME (for social icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<!-- ================= NAVBAR ================= -->
<div class="navbar">
    <div>TMS</div>
    <div>
        <a href="#home">Home</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>

        <?php if(isset($_SESSION['admin'])){ ?>
            <a href="tms.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php } else { ?>
            <a href="login.php">Admin Login</a>
        <?php } ?>
    </div>
</div>

<div class="content">

<!-- ================= HOME SECTION ================= -->
<section id="home">
    <div class="hero">

        <!-- LEFT CONTENT -->
        <div class="hero-text">
            <h1>Transport Management System</h1>
            <p>
                Manage drivers, vehicles, routes and transport assignments efficiently.
            </p>

            <?php if(isset($_SESSION['admin'])){ ?>
                <a href="tms.php" class="btn">Go to Dashboard</a>
            <?php } else { ?>
                <a href="login.php" class="btn">Admin Login</a>
            <?php } ?>
        </div>

        <!-- RIGHT IMAGE -->
        <div class="hero-image">
            <img src="hero.jpg" alt="Transport Illustration">
        </div>

    </div>
</section>

<!-- ================= ABOUT SECTION ================= -->
<section id="about">
    <h2>About</h2>
    <p>
       This application, developed using PHP and MySQL, offers a comprehensive Transport Management System with the following core functionalities: 
Secure Admin Authentication: Ensures only authorized personnel can access and manage system operations.
Full CRUD Operations: Supports the creation, reading, updating, and deletion of records across all major modules.
Module Management:
Drivers: Manage driver information and records.
Vehicles: Track vehicle details, status, and maintenance information.
Routes: Define and manage transportation routes.
Transport Assignments: Assign drivers and vehicles to specific routes or tasks. 
As this is a description of a software system, users interested in viewing, downloading, or contributing to the project may wish to find the source code on platforms like GitHub or GitLab. 
    </p>
</section>

<!-- ================= CONTACT / ADDRESS SECTION ================= -->
<section id="contact">
    <h2 style="color:#fff;">Contact & Address</h2>

    <div class="contact-box">
        <p>
            <strong>Transport Management System</strong><br>
            No: 12, Avinashi Road,<br>
            Peelamedu,<br>
            Coimbatore – 641004,<br>
            Tamil Nadu, India<br><br>

            <strong>Email:</strong> support@tms.com<br>
            <strong>Phone:</strong> +91 98765 43210
        </p>

        <hr>

        <div class="social-icons">
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-facebook"></i></a>
        </div>
    </div>
</section>

</div>

</body>
</html>
