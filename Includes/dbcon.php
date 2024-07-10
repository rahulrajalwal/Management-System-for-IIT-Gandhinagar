<?php
	$host = "localhost";
	$user = "ivimvvzd_att";
	$pass = "ivimvvzd_att";
	$db = "ivimvvzd_att";
	
	$conn = new mysqli($host, $user, $pass, $db);
	if($conn->connect_error){
		echo "Seems like you have not configured the database. Failed To Connect to database:" . $conn->connect_error;
	}
?>