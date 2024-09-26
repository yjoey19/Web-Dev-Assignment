<?php

include 'connect.php';

if(isset($_COOKIE['admin_id'])){
    $admin_id = $_COOKIE['admin_id'];
 }else{
    $admin_id = '';
    header('location:login.php');
 }
 
 if(isset($_POST['delete'])){
 
    $delete_id = $_POST['delete_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
 
    $verify_delete = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
    $verify_delete->execute([$delete_id]);
 
    if($verify_delete->rowCount() > 0){
       $delete_admin = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
       $delete_admin->execute([$delete_id]);
       $success_msg[] = 'Admin deleted!';
    }else{
       $warning_msg[] = 'Admin deleted already!';
    }
 
 }

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <link rel="stylesheet" href="adminStyle.css">

        <title>ADMINS</title>
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

        <section class="grid">
            <h1 class="heading">ADMINS</h1>
            <div class="boxContainer">

            <div class="box" style="text-align: center;">
                <p>Create admin</p>
                <a href="registration.php" class="button">Register</a>
            </div>   
            
            <?php
            $select_admins = $conn->prepare("SELECT * FROM `admins`");
            $select_admins->execute();
            if($select_admins->rowCount() > 0){
               while($fetch_admins = $select_admins->fetch(PDO::FETCH_ASSOC)){
         ?>
         <div class="box" <?php if( $fetch_admins['name'] == 'admin'){ echo 'style="display:none;"'; } ?>>
            <p>name : <span><?= $fetch_admins['name']; ?></span></p>
            <form action="" method="POST">
               <input type="hidden" name="delete_id" value="<?= $fetch_admins['id']; ?>">
               <input type="submit" value="delete admins" onclick="return confirm('delete this admin?');" name="delete" class="button">
            </form>
         </div>
         <?php
            }
         }else{
         }
            ?>
            </div>
        </section>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
        <script src="adminScript.js"></script>

        <?php include 'msg.php'; ?>
        
    </body>
</html>
