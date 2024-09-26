<?php


include 'connect.php';

if(isset($_COOKIE['admin_id'])){
    $admin_id = $_COOKIE['admin_id'];
 }else{
    $admin_id = '';
    header('location:login.php');
 }
 
 if(isset($_POST['submit'])){
 
    $id = create_unique_id();
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING); 
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING); 
    $c_pass = sha1($_POST['c_pass']);
    $c_pass = filter_var($c_pass, FILTER_SANITIZE_STRING);   
 
    $select_admins = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
    $select_admins->execute([$name]);
 
    if($select_admins->rowCount() > 0){
       $warning_msg[] = 'Username already taken!';
    }else{
       if($pass != $c_pass){
          $warning_msg[] = 'Password not matched!';
       }else{
          $insert_admin = $conn->prepare("INSERT INTO `admins`(id, name, password) VALUES(?,?,?)");
          $insert_admin->execute([$id, $name, $c_pass]);
          $success_msg[] = 'Registered successfully!';
       }
    }
 
 }


?>

<!DOCTYPE html>
<html >
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <link rel="stylesheet" href="adminStyle.css">
        <title>REGISTER</title>
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

        <section class="formContainer">

        <form action="" method="POST">
            <h3>New Registeration</h3>
            <input type="text" name="name" placeholder="Enter Username" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" placeholder="Enter Password" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="c_pass" placeholder="Enter Confirm Password" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="Register Now" name="submit" class="button">
        </form>

        </section>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
        <script src="adminScript.js"></script>

        <?php include 'msg.php'; ?>

    </body>
</html>