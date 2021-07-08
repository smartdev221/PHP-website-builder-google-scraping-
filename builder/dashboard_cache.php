<?php

include("../config.php");



$select_websites = mysqli_query($conn, "SELECT * FROM `websites`");
	
	while($row = mysqli_fetch_object($select_websites)){
		
			$selwebsite = mysqli_query($conn, "SELECT count(`idkeywords`) as total FROM `keywords` WHERE `website`='".$row->id."'");
			$total = mysqli_fetch_object($selwebsite);
		//echo $row->id." ".$total->total."<br>";
			$selwebsite = mysqli_query($conn, "SELECT count(`idkeywords`) as built FROM `keywords` WHERE `website`='".$row->id."' and `built`='1'");
			$built = mysqli_fetch_object($selwebsite);
		//echo $row->id." ".$built->built."<br>";
			$selwebsite = mysqli_query($conn, "SELECT count(`idkeywords`) as failed FROM `keywords` WHERE `website`='".$row->id."' and `built`!='0' and `built`!='1'");
			$failed = mysqli_fetch_object($selwebsite);
		//echo $row->id." ".$failed->failed."<br>";
			
			$exists = mysqli_query($conn, "SELECT * FROM `cache` WHERE `website_id`='".$row->id."'");
			if(mysqli_num_rows($exists) > 0){
				mysqli_query($conn, "UPDATE `cache` set `total`='".$total->total."', `built`='".$built->built."', `failed`='".$failed->failed."' WHERE `website_id`='".$row->id."'");
			} else {
				mysqli_query($conn, "INSERT INTO `cache`(`website_id`, `total`, `built`,`failed`) VALUES('".$row->id."', '".$total->total."', '".$built->built."', '".$failed->failed."')");
			}
	}

?>