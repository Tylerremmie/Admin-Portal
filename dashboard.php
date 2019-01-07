<?php
	require('database.php');
	include("authenticate.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin Portal</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--StyleSheet-->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/style.css" />
  <!--Scripts-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
	body {
	background-color: #F1F2F7;
	}
	th {
	  color: #E37B34;
	}
	table {
	  color: #000;
	  border: 1px solid #DEE2E6;
	}
	.whitetxt {
	  color: #fff;
	  width: 125px;
	}
	tbody tr:nth-child(odd){
	background-color: #F1F2F7;
	}
  </style>
</head>
<body>

<?php

	if (isset($_GET["msg"]) && $_GET["msg"] == 'failureadd') {
		$message = "Permission already existed.";
		echo "<script type='text/javascript'>alert('$message');</script>";
	} else if (isset($_GET["msg"]) && $_GET["msg"] == 'failuredelete') {
		$message = "User did not have that permission.";
		echo "<script type='text/javascript'>alert('$message');</script>";
	} else 	if (isset($_GET["msg"]) && $_GET["msg"] == 'successadd') {
		$message = "User permission added.";
		echo "<script type='text/javascript'>alert('$message');</script>";
	} else 	if (isset($_GET["msg"]) && $_GET["msg"] == 'successdelete') {
		$message = "User permission removed.";
		echo "<script type='text/javascript'>alert('$message');</script>";
	} else if (isset($_GET["msg"]) && $_GET["msg"] == 'emptysubmit'){
		$message = "Enter at least one field to update.";
		echo "<script type='text/javascript'>alert('$message');</script>";
	} else if (isset($_GET["msg"]) && $_GET["msg"] == 'failadminremove'){
		$message = "You Did Not Choose an Admin";
		echo "<script type='text/javascript'>alert('$message');</script>";
	} else if (isset($_GET["msg"]) && $_GET["msg"] == 'successadminremove'){
		$message = "Admin deleted from database";
		echo "<script type='text/javascript'>alert('$message');</script>";
	}
	
	
	// SQL query to get username, roles, and token id
    $query = "SELECT u.username, r.role, u.token FROM users u, user_roles ur, roles r WHERE u.id = ur.user_id AND ur.role_id = r.id AND u.username = '".$_SESSION['username']."'";
    
    if ($con->multi_query($query)) {
        do {
            // Create the records array
            $roles = array();
     
            // Lets work with the first result set
            if ($result = $con->use_result()) {
                // Loop the first result set, reading the records into an array
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    if($row['token'] != $_SESSION['token'])
                    {
                        // Incorrect token, disconnect the user
                        unset($_SESSION['username']);
                        unset($_SESSION['token']);
                        header("Location: logout.php");

                    } else {
                        // Token is the same still, continue with role assignment
                        $roles[] = $row['role'];
                    }
                }
                // Close the record set
                $result->close();
            }
    
        } while ($con->next_result());
    }

	//$con->close();
	
	// Function to check the users roles against the roles in the array of roles
    function check_role($checkrole, $arr) {
        foreach($arr as $role) { 
            if($role == $checkrole or $role == 'ADMIN') {
                return true;
            }
        }
        return false;
    }
?>

<!-- Navigation Bar -->
<div id="navigation" class="container-fluid">
    <nav class="navbar navbar-expand-sm fixed-top orange-background left-right-pad ">
        <h5><b>Admin Portal Dashboard</b></h5>
        <div id="navbarNavDropdown" class="navbar-collapse collapse">
            <ul class="navbar-nav mr-auto">
            </ul>
            <ul class="navbar-nav">
				<li class="nav-item">
					<i class="fa fa-user" style="font-size:20px; color:#000; padding-right: 8px;"></i>
				</li>
                <li class="nav-item">
				<h6><?php echo $_SESSION['username'];?> | <?php foreach($roles as $role) { echo $role . " "; } ?></h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./logout.php"><h6>Logout</h6></a>
                </li>
            </ul>
        </div>
    </nav>
</div>
	
<!-- Bootstrap Cards -->
<div class="container top-pad">

	<!-- Admin Control Panel - Super Admin Only -->
	<?php if (check_role('ADMIN', $roles)): ?>
	<div class="row pb-4">
	<div class="col-sm-12">
		<div class="card">
		  <div class="card-body">
			<h5 class="card-title">PERMISSION CONTROL PANEL</h5>
			<!-- TODO create the update permissions panel here -->
			<?php
			
				$myquery = 'SELECT admin_id, username, GROUP_CONCAT(access_level) AS permissions FROM `permissionlist` WHERE access_level = "LEVEL 1" OR access_level = "LEVEL 2" OR access_level = "LEVEL 3" GROUP BY admin_id';
				$danresult = mysqli_query($con, $myquery) or die (mysqli_error($con));
				$records = array();
				echo '
				<form action ="./update_roles.php" method ="post" >
					<table class="table" id="Permissions-Table">
						<thead>
							<tr>
								<th>ADMIN ID</th>
								<th>USERNAME</th>
								<th>PERMISSIONS</th>
								<th>ADD LEVEL</th>
								<th>REMOVE LEVEL</th>
							</tr>
						</thead>
						<tbody>';
				while($row=mysqli_fetch_array($danresult)){
					$records[] = $row['admin_id'];
					//values are based on role_id to insert/remove as necessary 3=USER, 4=USER2, 5=USER3
				echo '
						<tr>
							<td>'.$row['admin_id'].'</td>
							<td>'.$row['username'].'</td>
							<td>'.$row['permissions'].'</td>
							<td><select name="add[]">
									<option value="BLANK"> </option>
									<option value="3">1</option>
									<option value="4">2</option>
									<option value="5">3</option>
								</select>
							<td><select name="remove[]">
							<option value="BLANK"> </option>
							<option value="3">1</option>
							<option value="4">2</option>
							<option value="5">3</option>
							</select>
						</tr>';
				}
								
				echo ' </tbody></table>'
				?>
					<input class="btn whitetxt" style="float: right; margin-left: 5px;" type = "submit" value="REMOVE" name="removelevel" />
					<input class="btn whitetxt" style="float: right;" type = "submit" value="ADD" name="addlevel" />
					<input type='hidden' name='input_name' value="<?php echo htmlentities(serialize($records)); ?>" />
				

				
		
				<!--Table to remove users from system completely-->
				<table class= "table" id="Remove-Users">
							<tr>
								<th>ADMIN ID</th>
								<th>USERNAME</th>
								<th>DELETE USER</th>
							</tr>
				
				<?php
				$sql = 'SELECT admin_id, username, GROUP_CONCAT(access_level) AS permissions FROM `permissionlist` WHERE access_level = "LEVEL 1" OR access_level = "LEVEL 2" OR access_level = "LEVEL 3" GROUP BY admin_id';
				$result = mysqli_query($con, $sql) or die (mysqli_error($con));
				$i=0;
				while($row=mysqli_fetch_array($result)){
					//checkbox informs which index checked 'yes' as we cannot retrieve unchecked boxes
					echo'
						<tr>
							<td>'.$row['admin_id'].'</td>
							<td>'.$row['username'].'</td>
							<td>
								
								<input type="checkbox" name="listtodelete['.$i.']"  value="yes" >
							</td>';
							$i++;
				}
				
				?>
				</table>
				<input class="btn whitetxt" type= "submit" style="float: right;" value="DELETE" onclick="return confirm('THIS WILL REMOVE THE ADMIN AND ALL PERMISSIONS. ACTION CANNOT BE UNDONE. ARE YOU SURE YOU WANT TO DELETE?');"  name="deleteadmin" />
				</form>
		  		</div>
				  
			</div>
			
		</div>
	</div>
	<?php endif; ?>

	<!-- Admin Role based cards - Admin and Super Admin Only -->
	<?php if (check_role('ADMIN', $roles)): ?>
	<div class="row pb-4">
	<div class="col-sm-4">
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">Admin Links<hr></h5>
				<p class="card-text">
					<ul>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
					</ul>
				</p>
				</div>
			</div>
		</div>
	  <div class="col-sm-4">
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">Admin Links<hr></h5>
				<p class="card-text">
					<ul>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
					</ul>
				</p>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">Admin Links<hr></h5>
				<p class="card-text">
					<ul>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
					</ul>
				</p>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- User Role based cards - User & Super Admin Only -->
    <?php if (check_role('LEVEL 1', $roles)): ?>
	<div class="row pb-4">
	<div class="col-sm-4">
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">LEVEL 1 Links<hr></h5>
				<p class="card-text">
					<ul>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
					</ul>
				</p>
				</div>
			</div>
		</div>
	  <div class="col-sm-4">
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">LEVEL 1 Links<hr></h5>
				<p class="card-text">
					<ul>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
					</ul>
				</p>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">LEVEL 1 Links<hr></h5>
				<p class="card-text">
					<ul>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
					</ul>
				</p>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

    <!-- User2 Role based cards - User & Super Admin Only -->
    <?php if (check_role('LEVEL 2', $roles)): ?>
	<div class="row pb-4">
	<div class="col-sm-4">
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">LEVEL 2 Links<hr></h5>
				<p class="card-text">
					<ul>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
					</ul>
				</p>
				</div>
			</div>
		</div>
	  <div class="col-sm-4">
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">LEVEL 2 Links<hr></h5>
				<p class="card-text">
					<ul>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
					</ul>
				</p>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">LEVEL 2 Links<hr></h5>
				<p class="card-text">
					<ul>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
					</ul>
				</p>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	
	    <!-- User2 Role based cards - User & Super Admin Only -->
    <?php if (check_role('LEVEL 3', $roles)): ?>
	<div class="row pb-4">
	<div class="col-sm-4">
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">LEVEL 3 Links<hr></h5>
				<p class="card-text">
					<ul>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
					</ul>
				</p>
				</div>
			</div>
		</div>
	  <div class="col-sm-4">
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">LEVEL 3 Links<hr></h5>
				<p class="card-text">
					<ul>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
					</ul>
				</p>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">LEVEL 3 Links<hr></h5>
				<p class="card-text">
					<ul>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
						<li><a href="#">Lorem ipsum dolor sit amet.</a></li>
					</ul>
				</p>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Announcements - All roles can see -->
	<div class="row">
	<div class="col-sm-12">
		<div class="card">
		  <div class="card-body">
			<h5 class="card-title">Announcements: </h5>
			<p class="card-text"> 
			<hr> These announcements are viewable by all users. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean in convallis est. Sed placerat nisl nec elit pharetra, eu consectetur nulla vehicula. Cras cursus ullamcorper ante quis bibendum. Praesent eget rutrum purus, eget vulputate nunc. Pellentesque iaculis rutrum velit, vitae viverra lorem accumsan in.
			<hr> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean in convallis est. Sed placerat nisl nec elit pharetra, eu consectetur nulla vehicula. Cras cursus ullamcorper ante quis bibendum. Praesent eget rutrum purus, eget vulputate nunc. Pellentesque iaculis rutrum velit, vitae viverra lorem accumsan in. Praesent bibendum lorem sed fermentum condimentum. 
			<hr> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean in convallis est. Sed placerat nisl nec elit pharetra, eu consectetur nulla vehicula. Cras cursus ullamcorper ante quis bibendum. Praesent eget rutrum purus, eget vulputate nunc. Pellentesque iaculis rutrum velit, vitae viverra lorem accumsan in. Praesent bibendum lorem sed fermentum condimentum.</p>
		  </div>
		</div>
		</div>
	</div>

			</div>
		<?php $con->close();?>
	</body>
</html>
