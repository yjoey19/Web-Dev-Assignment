<?php

include 'connect.php';

if(isset($_COOKIE['admin_id'])){
    $admin_id = $_COOKIE['admin_id'];
 }else{
    $admin_id = '';
    header('location:login.php');
 }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <link rel="stylesheet" href="adminStyle.css">

        <title>DASHBOARD</title>
    </head>
    <body>
        <header class="header">
            <section class="flex">
                <a href="dashboard.php" class="logo">Admin panel</a>
                <nav class="navbar">
                    <a href="dashboard.php">Home</a>
                    <a href="bookings.php">Bookings</a>
                    <a href="admins.php">Admins</a>
                    <a href="messages.php">Messages</a>
                    <a href="registration.php">Register</a>
                    <a href="login.php">Login</a>
                    <a href="adminLogout.php" onclick="return confirm('Confirm logout?');">Logout</a>
                </nav>

                <div id="menuButton" class="fas faBars"></div>
            </section>
        </header>

        <section class="dashboard">
            <h1 class="heading">DASHBOARD</h1>
        
            <div class="boxContainer">

            <div class="box">
                <?php
                    $select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ? LIMIT 1");
                    $select_profile->execute([$admin_id]);
                    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>

                <h3>Welcome!</h3>
                
                <p><?= $fetch_profile['name']; ?></p>
                <a href="update.php" class="button">Update profile</a>
            </div>

            <div class="box">
                <?php
                    $select_bookings = $conn->prepare("SELECT * FROM `bookings`");
                    $select_bookings->execute();
                    $count_bookings = $select_bookings->rowCount();
                ?>
                <h3><?= $count_bookings; ?></h3>
                <p>Total Bookings</p>
                <a href="bookings.php" class="button">View Bookings</a>
            </div>

            <div class="box">
                <?php
                    $select_admins = $conn->prepare("SELECT * FROM `admins`");
                    $select_admins->execute();
                    $count_admins = $select_admins->rowCount();
                ?>
                <h3><?= $count_admins; ?></h3>
                <p>Total Admins</p>
                <a href="admins.php" class="button">View Admins</a>
            </div>

            <div class="box">
                <?php
                    $select_messages = $conn->prepare("SELECT * FROM `messages`");
                    $select_messages->execute();
                    $count_messages = $select_messages->rowCount();
                ?>
                <h3><?= $count_messages; ?></h3>
                <p>Total Messages</p>
                <a href="messages.php" class="button">View Messages</a>
            </div>

            <div class="box">
                <h3>Please Select</h3>
                <p>Login or Register</p>
                <a href="login.php" class="button" style="margin-right: 1rem;">Login</a>
                <a href="registration.php" class="button" style="margin-left: 1rem;">Register</a>
            </div>

            </div>

            </section>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
            <script src="adminScript.js"></script>

            <?php include 'msg.php'; ?>
    </body>
</html>