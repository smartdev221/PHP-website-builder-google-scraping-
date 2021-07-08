<?php
class wordtune{

function __construct($link){
	$this->link = $link;
	echo "inside wordtune constructor<br>\n";

$getconf = mysqli_query($this->link, "SELECT name,value_ FROM `config`");
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
			
	$sel = mysqli_query($this->link, "SELECT * FROM `rewrite_wordtune` WHERE `done`='0' LIMIT 1");
	if(mysqli_num_rows($sel) > 0){
	while($row = mysqli_fetch_object($sel)){
		//
		mysqli_query($this->link, "UPDATE `rewrite_wordtune` SET `done`='2' WHERE `id`='".$row->id."'");
		
		logger($row->idkeywords, $row->original, 'Rewrite wordtune', $this->link);
		
		echo "keyword id: #".$row->id."<br>";
		
		for($i = 1; $i<=5; $i++){
			usleep(rand(1000000, 2000000));
			$content = $this->newsession($row->content, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
			echo "Try ".$i."<br>";
			$json = json_decode($content, false);
			if(!empty($json->interactionId)){
				break;
			}
		}
		if(!empty($json->interactionId)){
			//print_r($content);
			//$json = json_decode($content, false);
			print_r($json);
			//retry
			if($json->error_msg == "429 Too Many Requests: 300 per 1 hour"){
				usleep(1500);
				$content = $this->newsession($row->content, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
				$json = json_decode($content, false);
			}
			if($json->error_msg == "429 Too Many Requests: 300 per 1 hour"){
				mysqli_query($this->link, "UPDATE `rewrite_wordtune` SET `done`='0' WHERE `id`='".$row->id."'");
				break;					
			}
	
			if($json->error_code == null){
					$unique_sentence = "";
					$current_difference = 0;
					//print_r($json->suggestions);
					foreach($json->suggestions as $suggestion){
						$suggestion = $suggestion[0];
						$differences = Diff::compare($sentence, $suggestion);
						$difference = 100-(Diff::toTable1($differences[0], $differences[1]));
						
						if($difference > $current_difference){
							//make sure rewrite version is at least half the length of original
							if(strlen($suggestion) >= (strlen($row->content)/2)){
								$current_difference = $difference;
								$unique_sentence = $suggestion;
							}
						}
						
					}
					mysqli_query($this->link, "UPDATE `rewrite_wordtune` SET `content_done`='".mysqli_real_escape_string($this->link, $this->punctuation($unique_sentence))."', `done`='1' WHERE `id`='".$row->id."'");
					logger($row->idkeywords, $row->original, 'Rewrite wordtune completed for sentence.', $this->link);
			} else {
				echo "There seems to be an error I never encountered, please let me know. ".$json->error_code;
					//
						$chk = mysqli_query($conn, "SELECT * FROM `config`");
						while($chkd = mysqli_fetch_object($chk)){
							if($chkd->name == "EMAIL_SENT"){
								$EMAIL_SENT = $chkd->value_;
							}elseif($chkd->name == "FAILS_BEFORE_CRON_STOP"){
								$FAILS_BEFORE_CRON_STOP = $chkd->value_;
							}elseif($chkd->name == "CURRENT_FAILS"){
								$CURRENT_FAILS = $chkd->value_;
							}
						}
						if($CURRENT_FAILS < $FAILS_BEFORE_CRON_STOP){
							mysqli_query($conn, "UPDATE `config` SET `value_`=`value_`+1 WHERE `name`='CURRENT_FAILS'");
						} elseif($CURRENT_FAILS >= $FAILS_BEFORE_CRON_STOP){
							if($EMAIL_SENT == 0){
								file_put_contents(__DIR__."/json_wordtune.html", $json);
								$message = date('m-d-Y H:i:s')."\r\nPossible wordtune changes. Got error code ".$json->error_code.". The cronjobs are stopped. \r\nPlease check. Returned json below:\r\nhttp://winhook.org/script_new/admin/json_wordtune.html";
								mail('catalin_smecheru96@yahoo.com, phaygarth@btinternet.com', 'wordtune failed', $message);
								mysqli_query($conn, "UPDATE `config` SET `value_`='0' WHERE `name`='RUN_CRONJOBS'");
								mysqli_query($conn, "UPDATE `config` SET `value_`='1' WHERE `name`='EMAIL_SENT'");
							}
						}
					//
			}
		} else {
			mysqli_query($this->link, "UPDATE `rewrite_wordtune` SET `done`='3' WHERE `id`='".$row->id."'");
		}
	}
	}
}

function punctuation($sentence){
	
	if(!preg_match('/([.?!:])\s?$/', $sentence)){
		$sentence = $sentence.". ";		
	}
	
	return $sentence;
}

function newsession($text, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT){
	$post = array("text"=>$text, "action"=>"REWRITE", "start"=> 0, "end"=>strlen($text), "selection"=>array("wholeText"=>$text, "start"=>(strlen($text)-1), "end"=>(strlen($text)-1)), "draftId"=>"DIV__notranslate _5rpu", "emailAccount"=>null, "emailMetadata"=>array());
	$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			//curl_setopt($ch, CURLOPT_POSTFIELDS, '{"text":"wordtune may be the best extension for Google Chrome for rewriting.","action":"REWRITE","start":0,"end":67,"selection":{"wholeText":"wordtune may be the best extension for Google Chrome for rewriting.","start":66,"end":66},"draftId":"DIV__notranslate _5rpu","emailAccount":null,"emailMetadata":{}}');
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post, JSON_FORCE_OBJECT));
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 35);
			curl_setopt($ch, CURLOPT_TIMEOUT, 35);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ .'/cookies.txt');
			curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ .'/cookies.txt');
			curl_setopt($ch, CURLOPT_HTTPHEADER,array ('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.141 Safari/537.36',
			'Accept: application/json, text/plain, */*',
			'Accept-Language: en-US,en;q=0.9',
			'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
			'Connection: keep-alive',
			'Content-Type:application/json',
			'x-wordtune: 1',
			'x-wordtune-origin: https://www.facebook.com/',
			'x-wordtune-version: 2.13.1',
			'token:eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2MjI0MjM3NTksInN1YiI6Ijg4YTQ4MzIzLTYwMGEtNGMxMy04NTFjLWE0NDkxOTBmZWVmMyIsInBsYW5fdHlwZSI6IlRSSUFMIiwicGxhbl9leHBpcmF0aW9uIjoxNjE0MTI2NjQ2LCJleHAiOjE2MjUwMTU3NTksImV4cGVyaW1lbnQiOiJlNmM1ZTk4OC03MTUyLTRlM2MtODI1My02MTc0MGU1NDYxZDMiLCJmbGFncyI6W10sInNjb3BlcyI6WyJyZWZpbmUuZnVsbCIsImNvcnJlY3Rpb25zLmZ1bGwiLCJ0cmF2ZXJzaW5nLmZ1bGwiLCJyZXdyaXRlLmdlbmVyaWMuZnVsbCJdLCJzY29wZXNDb25maWciOnsicmVmaW5lLmZ1bGwiOnsiaXNUcmlhbCI6ZmFsc2V9LCJjb3JyZWN0aW9ucy5mdWxsIjp7ImlzVHJpYWwiOnRydWV9LCJ0cmF2ZXJzaW5nLmZ1bGwiOnsiaXNUcmlhbCI6dHJ1ZX0sInJld3JpdGUuZ2VuZXJpYy5mdWxsIjp7ImlzVHJpYWwiOmZhbHNlfX0sInRyaWFsX3N0YXJ0X3RyaWdnZXIiOiJJTU1FRElBVEUiLCJ0cmlhbF9leHBpcmF0aW9uIjoxNjEzNTIxODQ2LCJ0cmFjZWxlc3MiOmZhbHNlfQ.XwYR8SxaZeVBrCH43hpCoI1oWMmZgGGoqU7zZXxG070'));
			curl_setopt($ch, CURLOPT_URL, "https://api.wordtune.com/rewrite");
			
			//
			if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
				curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			}
			if($PROXY==1){
				$proxyip = $PROXY_IP_PORT;
				//echo "SETTING ".$proxyip."\n<br>";
				curl_setopt($ch, CURLOPT_PROXY, $proxyip);
						
				if(!empty($PROXY_USER_PASSWORD)){
					curl_setopt($ch, CURLOPT_PROXYUSERPWD, $PROXY_USER_PASSWORD);
				}
						
				curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			}else{
				curl_setopt($ch, CURLOPT_PROXY, '');
			}
			//
			$translate = curl_exec($ch);
			//print_r(json_encode($post, JSON_FORCE_OBJECT));
			//print_r($translate);
			//$json = json_decode($translate);
			//$token = $json->idToken;
			curl_close($ch);
			
			return $translate;
			//newsession_new($token);
	
}

}
	
?>