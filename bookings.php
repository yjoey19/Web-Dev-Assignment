<?php

include 'connect.php';

if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];
 }else{
    setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
    header('location:index.php');
 }
 
 if(isset($_POST['delete'])){

   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_delete = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ?");
   $verify_delete->execute([$delete_id]);

   if($verify_delete->rowCount() > 0){
      $delete_bookings = $conn->prepare("DELETE FROM `bookings` WHERE booking_id = ?");
      $delete_bookings->execute([$delete_id]);
      $success_msg[] = 'Booking deleted!';
   }else{
      $warning_msg[] = 'Booking deleted already!';
   }

}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <link rel="stylesheet" href="adminStyle.css">

        <title>BOOKINGS</title>
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
            <h1 class="heading">BOOKINGS</h1>
            <div class="boxContainer">

            <?php
               $select_bookings = $conn->prepare("SELECT * FROM `bookings`");
                $select_bookings->execute();
                if($select_bookings->rowCount() > 0){
                    while($fetch_bookings = $select_bookings->fetch(PDO::FETCH_ASSOC)){
            ?>
   <div class="box">
      <p>booking id : <span><?= $fetch_bookings['booking_id']; ?></span></p>
      <p>name : <span><?= $fetch_bookings['name']; ?></span></p>
      <p>email : <span><?= $fetch_bookings['email']; ?></span></p>
      <p>number : <span><?= $fetch_bookings['number']; ?></span></p>
      <p>check in : <span><?= $fetch_bookings['check_in']; ?></span></p>
      <p>check out : <span><?= $fetch_bookings['check_out']; ?></span></p>
      <p>room type : <span><?= $fetch_bookings['room_type']; ?></span></p>
      <p>rooms : <span><?= $fetch_bookings['rooms']; ?></span></p>
      <p>adults : <span><?= $fetch_bookings['adults']; ?></span></p>
      <p>childs : <span><?= $fetch_bookings['childs']; ?></span></p>
      <form action="" method="POST">
         <input type="hidden" name="delete_id" value="<?= $fetch_bookings['booking_id']; ?>">
         <input type="submit" value="delete booking" onclick="return confirm('delete this booking?');" name="delete" class="button">
      </form>
   </div>
   <?php
      }
   }else{
   ?>
   <div class="box" style="text-align: center;">
      <p>no bookings found!</p>
      <a href="dashboard.php" class="button">go to home</a>
   </div>
   <?php
   }
            ?>
            </div>
        </section>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
        <script src="adminScript.js"></script>

        <?php include 'msg.php'; ?>
    </body>
</html>