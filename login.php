<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Portal</title>
<!--StyleSheet-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="./css/style.css" />
<!--Scripts-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<?php
require('database.php');
session_start();

if (isset($_GET["msg"]) && $_GET["msg"] == 'failed') {
    $message = "Username and/or Password incorrect.\\nTry again.";
	echo "<script type='text/javascript'>alert('$message');</script>";
}

if (isset($_GET["msg"]) && $_GET["msg"] == 'registrationsuccessful') {
    $message = "Registration Successful.\\nPlease Login.";
	echo "<script type='text/javascript'>alert('$message');</script>";
}

// If form submitted, insert values into the database.
if (isset($_POST['username'])){

    // removes backslashes
    $username = stripslashes($_REQUEST['username']);

    //escapes special characters in a string
    $username = mysqli_real_escape_string($con,$username);
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($con,$password);

    //Checking is user existing in the database or not
    $query = "SELECT * FROM `users` WHERE username='$username' AND password='".md5($password)."' LIMIT 1";
    $result = mysqli_query($con,$query) or die(mysql_error());
    $rows = mysqli_num_rows($result);

    if($rows==1){
		// Initialize Session for username and token
        $_SESSION['username'] = $username;
		$token = md5(uniqid());
        $_SESSION['token'] = $token;
        $query  = "UPDATE users SET token='$token' WHERE username='$username'";
        $result = mysqli_query($con,$query);
		
        // Redirect user to dashboard.php
        header("Location: dashboard.php");
    }else{
		header("location:login.php?msg=failed");
    }
}else{
?>

<div class="login-form">
    <form action="" method="post" name="login">
        <h2 class="text-center">Admin Portal</h2>   
        <div class="form-group has-error">
        	<input type="text" class="form-control" name="username" placeholder="Username" required="required">
        </div>
		<div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password" required="required">
        </div>        
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>
        </div>
    </form>
</div>

<?php } ?>
</body>
</html>                            