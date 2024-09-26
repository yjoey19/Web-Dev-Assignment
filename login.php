<?php

include 'connect.php';

if(isset($_POST['submit'])){

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING); 
    $pass = sha1($_POST['password']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING); 
 
    $select_admins = $conn->prepare("SELECT * FROM `admins` WHERE name = ? AND password = ? LIMIT 1");
    $select_admins->execute([$name, $pass]);
    $row = $select_admins->fetch(PDO::FETCH_ASSOC);
 
    if($select_admins->rowCount() > 0){
       setcookie('admin_id', $row['id'], time() + 60*60*24*30, '/');
       header('location:dashboard.php');
    }else{
       $warning_msg[] = 'Incorrect username or password!';
    }
 
 }

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <link rel="stylesheet" href="adminStyle.css">

        <title>LOGIN</title>
    </head>

    <body>
        <section class="formContainer" style="min-height:100vh;">
            <form action="" method="POST">
                <h3>ADMIN LOGIN</h3>
                <p>Default username:  <span>admin</span>&nbsp;&nbsp;&nbsp;Password:  <span>111</span></p>
                <input type="text" name="name" class="box" maxlength="20" placeholder="Enter username here" required oninput="this.value = this.value.replace(/\s+/g, '')">
                <input type="password" name="password" class="box" maxlength="20" placeholder="Enter password here" required oninput="this.value = this.value.replace(/\s+/g, '')"> 
                
                <input type="submit" value="Login" name="submit" class="button">
            </form>
        </section>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

        <?php include 'msg.php'; ?>

    </body>
</html>




