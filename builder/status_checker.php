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
			
$related = 0;
if($related == 1){
	//select keyword google and serp scraped
	$sel = mysqli_query($conn, "SELECT * FROM `keywords` where `result1`!='' and `scraped`='1' and `url_scraped`='1' and `related_scraped`='0' LIMIT 50");
	
	while($row = mysqli_fetch_object($sel)){
		echo "keyword id:".$row->idkeywords."<br>";
		
		$look = mysqli_query($conn, "SELECT * FROM `keywordsqa` where `original1`='".mysqli_real_escape_string($conn, $row->original)."'");
			$scraped = array();
			while($qa = mysqli_fetch_object($look)){
				if(empty($qa->result1) && $qa->scraped == "1"){
					$scraped[] = 0;
					$show = 0;
				} else {
					$scraped[] = $qa->scraped;
					$show = $qa->scraped;
				}
				echo $qa->original." -> ".$show."<br>";
			}
			$related_scraped = 1;
			foreach($scraped as $elm){
				if($elm != 1){
					$related_scraped = 0;
				}
			}
		
		echo "final result:".$related_scraped."<br>";
		mysqli_query($conn, "UPDATE `keywords` SET `related_scraped`='".$related_scraped."' WHERE `idkeywords`='".$row->idkeywords."'");
		unset($scraped);
	}
}
////update translated q&a
	$sel = mysqli_query($conn, "SELECT * FROM `keywords` where `result1`!='' and `scraped`='1' and `url_scraped`='1' and `translated_qa`='0' LIMIT 50");
	
	while($row = mysqli_fetch_object($sel)){
		echo "keyword id:".$row->idkeywords."<br>";
		
		$look = mysqli_query($conn, "SELECT * FROM `qa` where `original`='".mysqli_real_escape_string($conn, $row->original)."'");
			$translated_q = array();
			while($qa = mysqli_fetch_object($look)){
					$translated_q[] = $qa->translated;
				echo $qa->original." -> ".$qa->translated."<br>";
			}
			$translated_qa = 1;
			foreach($translated_q as $elm){
				if($elm == 0){
					$translated_qa = 0;
				}
			}
		
		echo "final result:".$translated_qa."<br>";
		mysqli_query($conn, "UPDATE `keywords` SET `translated_qa`='".$translated_qa."',`last_action`='status_qa', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
		unset($translated_q);
	}
////
////update translated snippet
echo "Featured snippets:<br>";
	$sel1 = mysqli_query($conn, "SELECT * FROM `keywords` where `result1`!='' and `scraped`='1' and `url_scraped`='1' and `translated_snippet`='0' LIMIT 50");
	
	while($row1 = mysqli_fetch_object($sel1)){
		echo "keyword id:".$row1->idkeywords."<br>";
		
		$look1 = mysqli_query($conn, "SELECT * FROM `featuredsnippets` where `original`='".mysqli_real_escape_string($conn, $row1->original)."'");
			$translated_s = array();
			while($sn = mysqli_fetch_object($look1)){
					$translated_s[] = $sn->translated;
				echo $sn->original." -> ".$sn->translated."<br>";
			}
			$translated_sn = 1;
			foreach($translated_s as $elm){
				if($elm == 0){
					$translated_sn = 0;
				}
			}
		
		echo "final result:".$translated_sn."<br>";
		mysqli_query($conn, "UPDATE `keywords` SET `translated_snippet`='".$translated_sn."',`last_action`='status_snippet', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row1->idkeywords."'");
		unset($translated_s);
	}
////	
////update translated serp title
echo "serp titles:<br>";
	$sel2 = mysqli_query($conn, "SELECT * FROM `keywords` where `result1`!='' and `scraped`='1' and `url_scraped`='1' and `translated_serp_title`='0' LIMIT 50");
	
	while($row1 = mysqli_fetch_object($sel2)){
		echo "keyword id:".$row1->idkeywords."<br>";
		
		$look1 = mysqli_query($conn, "SELECT * FROM `serp_titles` where `idkeywords`='".mysqli_real_escape_string($conn, $row1->idkeywords)."'");
			$translated_s_t = array();
			while($sn = mysqli_fetch_object($look1)){
					$translated_s_t[] = $sn->translated;
				echo $sn->original." -> ".$sn->translated."<br>";
			}
			$translated_st = 1;
			foreach($translated_s_t as $elm){
				if($elm == 0){
					$translated_st = 0;
				}
			}
		
		echo "final result:".$translated_st."<br>";
		mysqli_query($conn, "UPDATE `keywords` SET `translated_serp_title`='".$translated_st."',`last_action`='status_serptitle', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row1->idkeywords."'");
		unset($translated_s_t);
	}
////	
?>