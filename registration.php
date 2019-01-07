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

// If form submitted, insert values into the database.
if (isset($_REQUEST['username'])){
    
    // removes backslashes
    $username = stripslashes($_REQUEST['username']);

    //escapes special characters in a string
    $username = mysqli_real_escape_string($con,$username); 
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($con,$password);

    $query = "INSERT into `users` (username, password) VALUES ('$username', '".md5($password)."')";
    $result = mysqli_query($con,$query);

    if($result){
		header("location:login.php?msg=registrationsuccessful");
        //echo "<div class='form'><h3>You are registered successfully.</h3><br/>Click here to <a href='login.php'>Login</a></div>";
    }

}else{
?>
<div class="login-form">
    <form name="registration" action="" method="post">
        <h2 class="text-center">Create User</h2>   
        <div class="form-group has-error">
        	<input type="text" class="form-control" name="username" placeholder="Username" required="required">
        </div>
		<div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password" required="required">
        </div>             
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg btn-block">Create User</button>
        </div>
    </form>
</div>
<?php } ?>
</body>
</html>                            