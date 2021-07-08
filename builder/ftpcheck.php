<?php
session_start();
include("../config.php");

if(empty($_SESSION['loggedin'])){
	echo '<meta http-equiv="refresh" content="0; url=login.php">';
} else {
	if(!empty($_GET['user']) && !empty($_GET['password']) && !empty($_GET['ip'])){
		preg_match("/[A-z0-9]+/", $_GET['user'], $user);
		$user = $user[0];
		if($user == $_GET['user']){
			//$ftp_server = $_SERVER['SERVER_ADDR'];
			$ftp_server = $_GET['ip'];
			$conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 
			$ftpuser = $_GET['user'];
			$ftppassword = $_GET['password'];
			// try to login
			if (@ftp_login($conn_id, $_GET['user'], $_GET['password'])) {
				//echo "Connected as $ftpuser@$ftp_server\n";
				echo "0";
			} else {
				echo "Couldn't connect as $ftpuser@$ftp_server\n";
			}
			// close the connection
			ftp_close($conn_id);  
		} else {
			echo "Only a-z, 0-9 for user!";
		}
	} else {
		echo "Please complete FTP data.";
	}
	
	/*if(!empty($_GET['user'])){
		preg_match("/[A-z0-9]+/", $_GET['user'], $user);
		$user = $user[0];
		if($user == $_GET['user']){
			if(file_exists("/home/".$_GET['user']."/public_html")){
				echo "0";
			} else {
				echo "User non existant.";
			}
		} else {
			echo "Only a-z, 0-9!";
		}

	} else {
		
		echo "Please complete FTP User.";
	}*/
	
}
?>