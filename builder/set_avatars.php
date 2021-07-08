<?php
include("../config.php");

$sel = mysqli_query($conn, "SELECT * FROM `authors` WHERE `image`=''");

while($row = mysqli_fetch_object($sel)){
	
		$get = mysqli_query($conn, "SELECT * FROM `avatars` order by RAND() limit 1");
		$info = mysqli_fetch_object($get);
		
		
		mysqli_query($conn, "UPDATE `authors` set `image`='".$info->filename."', `image_extension`='".$info->extension."' WHERE `id`='".$row->id."'");	
	
	
}

?>