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
 
    $verify_delete = $conn->prepare("SELECT * FROM `messages` WHERE id = ?");
    $verify_delete->execute([$delete_id]);
 
    if($verify_delete->rowCount() > 0){
       $delete_bookings = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
       $delete_bookings->execute([$delete_id]);
       $success_msg[] = 'Message deleted!';
    }else{
       $warning_msg[] = 'Message deleted already!';
    }
 
 }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <link rel="stylesheet" href="adminStyle.css">

        <title>MESSAGES</title>
    </head>

    <body>
    <?php include 'adminHeader.php'; ?>

    <section class="grid">
        <h1 class="heading">MESSAGES</h1>
        <div class="boxContainer">
            <?php
            
            $select_messages = $conn->prepare("SELECT * FROM `messages`");
      $select_messages->execute();
      if($select_messages->rowCount() > 0){
         while($fetch_messages = $select_messages->fetch(PDO::FETCH_ASSOC)){
            
            ?>
            <div class="box">
                <p>name : <span><?= $fetch_messages['name']; ?></span></p>
                <p>email : <span><?= $fetch_messages['email']; ?></span></p>
                <p>number : <span><?= $fetch_messages['number']; ?></span></p>
                <p>message : <span><?= $fetch_messages['message']; ?></span></p>
                <form action="" method="POST">
                    <input type="hidden" name="delete_id" value="<?= $fetch_messages['id']; ?>">
                    <input type="submit" value="delete message" onclick="return confirm('Confirm delete this message?');" name="delete" class="button">
                </form>
            </div>
            <?php
                }
            }else{
            ?>

            <div class="box" style="text-align: center;">
                <p>No messages found.</p>
                <a href="dashboard.php" class="button">Go to Home</a>
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