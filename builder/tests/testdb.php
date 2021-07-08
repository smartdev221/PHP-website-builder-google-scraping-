<?php
ini_set('mysql.connect_timeout', 300);
ini_set('mysql.allow_persistent', 1);
ini_set('default_socket_timeout', 300); 
	include("../config.php");

	
	//$sel = mysqli_query($conn, "SHOW SESSION VARIABLES LIKE \"%wait%\";");
	$sel = mysqli_query($conn, "SELECT SLEEP(120);") or die(mysqli_error($conn));
	
	while($row = mysqli_fetch_object($sel)){
		print_r($row);
	}
	
?>