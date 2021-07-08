<?php
include("../config.php");

$serpsel = mysqli_query($conn, "SELECT * FROM `scraped_content_serp` WHERE `translated`='1' order by rand() limit 1");
	while($row = mysqli_fetch_object($serpsel)){
		echo $row->content_en;
	}
	
	?>