<?php
	include("../config.php");
	
function drip($current, $perday, $conn){
		
	$select = mysqli_query($conn, "SELECT * FROM `config_global` WHERE `name`='DRIP_FEED_NUMBERS'") or die(mysqli_error($conn));
	if(mysqli_num_rows($select) > 0){
		$row = mysqli_fetch_object($select);
				$look = explode("\r\n", $row->value);
				$i = 0;
				foreach($look as $pm){
					//if found current number -> set next value in drip array
					if($pm == $current){
						//if get to dripfeed end
						if(empty($look[$i+1])){
							$next = $perday;
						} elseif($look[$i+1] < $perday) {
							//if next value is smaller than perday absolute limiter
							$next = $look[$i+1];
						} else {
							//no dripfeed end but higher value than perday limiter
							$next = $perday;
						}
					}
					$i++;
				}
				//if current number is outside of drip array set perday absolute limiter
				if(empty($next)){
					$next = $perday;
				}
				return $next;
	} else {
		//no dripfeed settings found so return current number
		return $current;
	}
}
function drip_new($current, $perday, $conn){
		
	$select = mysqli_query($conn, "SELECT * FROM `config_global` WHERE `name`='DRIP_FEED_NUMBERS'") or die(mysqli_error($conn));
	if(mysqli_num_rows($select) > 0){
		$row = mysqli_fetch_object($select);
				$look = explode("\r\n", $row->value);
				
				//if dripfeed next is above perday limiter
				if($look[$current] > $perday){
					$next = $perday;
				}elseif(!empty($look[$current])){
					$next = $look[$current];
				}else {
					//if get to dripfeed end
					$next = $perday;
				}
				
				return $next;
	} else {
		//no dripfeed settings found so return current number
		return $perday;
	}
}	
	$get_templates = mysqli_query($conn, "SELECT * FROM `websites` where `skip`='0'");
	$templates = array();
	while($website = mysqli_fetch_object($get_templates)){
		//if($website->id !='1' AND $website->id !='2'){
		if($website->built < $website->dripfeed_current){
			$message = "Website ".$website->name." built only ".$website->built." from the ".$website->dripfeed_current." it was supposed to.\r\nPlease check.";
			mail('catalin_smecheru96@yahoo.com', 'website '.$website->name, $message);
		}
			
		//reset drip feed
		//$tomorrow = drip($website->dripfeed_current, $website->perday, $conn);
		$tomorrow = drip_new($website->dripfeed_order, $website->perday, $conn);
		
		echo $website->id." - ".$tomorrow."<br>";
		
		if($tomorrow == $website->perday){
			$order = "999";
		} else {
			$order = $website->dripfeed_order+1;
		}
		echo $order."<br><br>";
		//reset counter for posts/day
		//mysqli_query($conn, "UPDATE `websites` SET `dripfeed_current`='".$tomorrow."', `scraped`='0', `scraped_vid`='0', `scraped_img`='0', `built`='0' WHERE `id`='".$website->id."'");
		mysqli_query($conn, "UPDATE `websites` SET `dripfeed_current`='".$tomorrow."', `dripfeed_order`='".$order."', `scraped`='0', `scraped_vid`='0', `scraped_img`='0', `built`='0' WHERE `id`='".$website->id."'");
	//}
	}
		
	//reset counter for posts/day
	//mysqli_query($conn, "UPDATE `websites` SET `scraped`='0', `scraped_vid`='0', `scraped_img`='0', `built`='0'");
	
?>