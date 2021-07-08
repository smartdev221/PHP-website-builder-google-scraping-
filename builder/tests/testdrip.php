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
				
		//reset drip feed
		$tomorrow = drip("8", "99", $conn);
		echo "Old drip:";
		echo $tomorrow;
		echo "<br><br>New drip:";
		$tomorrow1 = drip_new("7", "99", $conn);
		echo $tomorrow1;
	
?>