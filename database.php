<?php
	// Database connection (Password is empty on XAMPP)
	$con = mysqli_connect("localhost","root","","adminportal");
	
	// Check connection
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
?>