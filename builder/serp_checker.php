<?php
	include("../config.php");

	$getconf = mysqli_query($conn, "SELECT name,value_ FROM `config`");
			while($getconfig = mysqli_fetch_object($getconf)){
				if($getconfig->name == "MAX_THREADS"){
					$MAX_THREADS = $getconfig->value_;
				}elseif($getconfig->name == "PAUSED"){
					$PAUSED = $getconfig->value_;
				}elseif($getconfig->name == "MAX_KEYWORDS_X_THREAD"){
					$MAX_KEYWORDS_X_THREAD = $getconfig->value_;
				}elseif($getconfig->name == "SLEEP_THREAD_BETWEEN_KEYWORDS"){
					$SLEEP_THREAD_BETWEEN_KEYWORDS = $getconfig->value_;
				}elseif($getconfig->name == "PROXY"){
					$PROXY = $getconfig->value_;
				}elseif($getconfig->name == "PROXY_IP_PORT"){
					$PROXY_IP_PORT = $getconfig->value_;
				}elseif($getconfig->name == "PROXY_USER_PASSWORD"){
					$PROXY_USER_PASSWORD = $getconfig->value_;
				}elseif($getconfig->name == "RUN_CRONJOBS"){
					$RUN_CRONJOBS = $getconfig->value_;
				}
			}
			if($RUN_CRONJOBS == 0){
				if(getenv('cron') == 1) {
					  echo "The script was run from the crontab entry";
					  die();
				} else {
				   echo "The script was run from a webserver, or something else";
				}
			}
			
$select = mysqli_query($conn, "SELECT * FROM `config_global`") or die(mysqli_error($conn));
	if(mysqli_num_rows($select) > 0){
		while($row = mysqli_fetch_object($select)){
			if($row->name == "SERP_MATCH_POSITIVE"){
				$serp_positive = $row->value;
				
			}
			if($row->name == "SERP_MATCH_NEGATIVE"){
				$serp_negative = $row->value;
			}
		}
		
	}

	$sel = mysqli_query($conn, "SELECT * FROM `keywords` where `result1`!='' and `scraped`='1' and `serp_checked`='0' and `built`='0' LIMIT 50");
	//$sel = mysqli_query($conn, "SELECT * FROM `keywords` where `result1`!='' and `scraped`='1' and `idkeywords`='1'");
	
	while($row = mysqli_fetch_object($sel)){
		logger($row->idkeywords, $row->original, 'Serp filtering', $conn);
		echo "<hr>keyword id:".$row->idkeywords."<br>";
		$positive_match = 0;
		$negative_match = 0;
		if(empty($serp_positive) && empty($serp_negative)){
			mysqli_query($conn, "UPDATE `keywords` SET `serp_checked`='1' WHERE `idkeywords`='".$row->idkeywords."'");
			echo "No positive and negative serp matches added.<br>";
			logger($row->idkeywords, $row->original, 'Serp filtering: No positive or negative filters added -> pass.', $conn);
		} else {
			$text = $row->result1." ".$row->result2." ".$row->result3." ".$row->result4." ".$row->result5." ".$row->result6." ".$row->result7." ".$row->result8." ".$row->result9." ".$row->result10." ".$row->result1title." ".$row->result2title." ".$row->result3title." ".$row->result4title." ".$row->result5title." ".$row->result6title." ".$row->result7title." ".$row->result8title." ".$row->result9title." ".$row->result10title." ".$row->result1descr." ".$row->result2descr." ".$row->result3descr." ".$row->result4descr." ".$row->result5descr." ".$row->result6descr." ".$row->result7descr." ".$row->result8descr." ".$row->result9descr." ".$row->result10descr." ".$row->related1." ".$row->related2." ".$row->related3." ".$row->related4." ".$row->related5." ".$row->related6." ".$row->related7." ".$row->related8;
			//look for serp positive matches
			$look = explode("\r\n", $serp_positive);
				foreach($look as $pm){
					if($positive_match == 0){
						//echo $pm."<br>";
						$instances = explode(",", $pm);
						//print_r($instances);
						preg_match_all("/\b".$instances[0]."\b/i", $text, $matches);
						//print_r($matches);
						if(count($matches[0]) >= $instances[1]){
							$positive_match = 1;
							echo "Positive match for ".$instances[0]." with a required count of ".$instances[1].", actual match is ".count($matches[0])."<br>";
						}
					}
				}
			//look for serp negative matches
			$look_negative = explode("\r\n", $serp_negative);
				foreach($look_negative as $pm1){
					if($negative_match == 0){
						//echo $pm1."<br>";
						$instances1 = explode(",", $pm1);
						//print_r($instances1);
						preg_match_all("/\b".$instances1[0]."\b/i", $text, $matches1);
						//print_r($matches1);
						if(count($matches1[0]) >= $instances1[1]){
							$negative_match = 1;
							echo "Negative match for ".$instances1[0]." with a required count of ".$instances1[1].", actual match is ".count($matches1[0])."<br>";
						}
					}
				}
			echo "Final result for this keyword:";
			if($positive_match == 1 && $negative_match == 0){
				logger($row->idkeywords, $row->original, 'Serp filtering: pass.', $conn);
				mysqli_query($conn, "UPDATE `keywords` SET `serp_checked`='1',`last_action`='serp_filter', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
				$result = "passed";
			}elseif($negative_match == 1){
				logger($row->idkeywords, $row->original, 'Serp filtering: not passed (Negative serp match filter for '.$instances1[0].' with a required count of '.$instances1[1].', actual match is '.count($matches1[0]).').', $conn);
				mysqli_query($conn, "UPDATE `keywords` SET `serp_checked`='1',`built`='4',`reason`='Negative serp match filter for ".$instances1[0]." with a required count of ".$instances1[1].", actual match is ".count($matches1[0])."' WHERE `idkeywords`='".$row->idkeywords."'") or die(mysqli_error($conn));
				mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
				$result = "not passed";
			}elseif($positive_match == 0){
				logger($row->idkeywords, $row->original, 'Serp filtering: passed (No match for positive or negative serp filters)', $conn);
				//mysqli_query($conn, "UPDATE `keywords` SET `serp_checked`='1',`built`='4',`reason`='No match for positive or negative serp filters.' WHERE `idkeywords`='".$row->idkeywords."'");
				mysqli_query($conn, "UPDATE `keywords` SET `serp_checked`='1' WHERE `idkeywords`='".$row->idkeywords."'");
				//mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
				$result = "passed";
			}
			
			
			echo $result."<br>";
		}
				
	}
?>