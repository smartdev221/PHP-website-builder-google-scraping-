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
function links($content){
	//look for link with or without ending
	if(preg_match('/<a\s+href(?:\s+)?=(?:\s+)?"([A-z:\/.\-_&\s]+)"[A-z=_"\s]+>/', $content, $match)){
		//replace space in url
		$content = str_replace($match[1], str_replace(" ", "", $match[1]), $content);
		
		//match new a href with ok url
		preg_match('/<a\s+href(?:\s+)?=(?:\s+)?"([A-z:\/.\-_&\s]+)"[A-z=_"\s]+>/', $content, $match);
		//grab 
			//echo "found link a...".$match[0]."<br>\n";
			
			//explode link relative to rest of sentences
			$expl = explode($match[0], $content);
			//echo "primu explode:\n";
			//print_r($expl);
			
			//explode rest of words
			$expl1 = explode(" ", $expl[1]);
			//echo "al doilea explode:\n";
			//print_r($expl1);
			$found = false;
			//look for ending tag
			foreach($expl1 as $arrelement){
				if($arrelement == "</a>" or $arrelement == "< /a>" or $arrelement == "</a >" or $arrelement == "</ a>"){
					$found = true;
				}
			}
			//echo "found:\n".$found."\n<br>";
			if($found == false){
			//new content
			$content = $expl[0]." ".$match[0];
			for($i = 0; $i<count($expl1); $i++){
				if(count($expl1) > 7){
					//close after 8 words
					if($i == 7){
						$content.=$expl1[$i]."</a> ";
					} else {
						$content.=$expl1[$i]." ";
					}
				} elseif(count($expl1) < 7 && count($expl1) > 2) {
					//close link after 4 words
					if($i == 3){
						$content.=$expl1[$i]."</a> ";
					} else {
						$content.=$expl1[$i]." ";
					}
				} else {
					//close after 3 words
					if($i == 2){
						$content.=$expl1[$i]."</a> ";
					} else {
						$content.=$expl1[$i]." ";
					}
				}
				
			}
			}
		//}
		
		
	}

	return $content;
}

function translate($from, $to, $content, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT){
	$url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=".$from."&tl=".$to."&dt=t&q=".urlencode($content);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array ('User-Agent: Mozilla/5.0 (X11; Linux i686; rv:36.0) Gecko/20100101 Firefox/36.0',
	'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
	'Accept-Language: en-US,en;q=0.5',
	'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
	'Connection: keep-alive'
	));
	if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	}
	curl_setopt($ch, CURLOPT_URL, $url);
	if($PROXY==1){
		$proxyip = $PROXY_IP_PORT;
		echo "SETTING ".$proxyip."\n<br>";
		curl_setopt($ch, CURLOPT_PROXY, $proxyip);
				
		if(!empty($PROXY_USER_PASSWORD)){
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $PROXY_USER_PASSWORD);
		}
				
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
	}else{
		curl_setopt($ch, CURLOPT_PROXY, '');
	}
	$body = curl_exec($ch);
	
	//$translate = str_replace("<\/ ", "<\/", $body);
	
	$translated = json_decode($body);
	//print_r($translated);
	
	$newtext = "";
	foreach($translated[0] as $sentences){
		//call to function @links for link fixing
		$sentences[0] = links($sentences[0]);
		$newtext.=str_replace("</ ", "</",$sentences[0]);
		
	}
	
	//echo $body;
	
	return $newtext;
}

function scraper($langs, $content, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT){
	$texttoreturn = array();
	
	foreach($langs as $lang){
		$lang = explode("|", $lang);
		$from = $lang[0];
		$to = $lang[1];
		if($from == 'en'){
			$content = $content;
		} else {
			$content = ${'content_'.$from};
		}
		//do 3 tries per language
		if(!empty($content)){
			for($i = 1; $i < 4; $i++){
				echo "from:".$from." to ".$to."<br>TRY #".$i."<br>";
				if($to != "en"){
					$source = translate($from, $to, $content, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
					if(!empty($source)){
						${'content_'.$to} = $source;
						$texttoreturn['content_'.$to] = $source;
						break;
					}
				} else {
					//chunk for russian
					$chunks = str_split($source, 2000);
					$chunki = 1;
					foreach($chunks as $chunk){
						//5 tries
						for($i1 = 1; $i1 < 6; $i1++){
							echo "from:".$from." to ".$to."<br>Chunk #".$chunki." TRY #".$i1."<br>";
							$source1 = translate($from, $to, $chunk, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
							if(!empty($source1)){
								$content_en.= $source1;
								break;
							}
						}
						$chunki++;
					}
					$texttoreturn['content_en'] = $content_en;
					break;
					
				}
				
			}
		}
		
		
	}
	return $texttoreturn;
}
	
	//translate serp title
	$sel = mysqli_query($conn, "SELECT * FROM `serp_titles` WHERE `translated`='0' LIMIT 1");
	if(mysqli_num_rows($sel) > 0){
	while($row = mysqli_fetch_object($sel)){
		logger($row->idkeywords, $row->original, 'Serp title translating', $conn);
		$langs = array('en|de', 'de|fr', 'fr|ru', 'ru|en');
		echo "keyword id: #".$row->idkeywords."<br>";
		$fortranslate = substr($row->title, 0, 9000);//2000
		$content = scraper($langs, $fortranslate, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
		
		//print_r($content);
		foreach($content as $lang=>$source){
			${$lang} = $source;
		}
		logger($row->idkeywords, $row->original, 'Serp title translated', $conn);
		mysqli_query($conn, "UPDATE `serp_titles` SET `title_de`='".mysqli_real_escape_string($conn, $content_de)."', `title_fr`='".mysqli_real_escape_string($conn, $content_fr)."', `title_ru`='".mysqli_real_escape_string($conn, $content_ru)."', `title_en`='".mysqli_real_escape_string($conn, $content_en)."',`translated`='1' WHERE `idkeywords`='".$row->idkeywords."'");
		mysqli_query($conn, "UPDATE `keywords` SET `translated_serp_title`='1',`last_action`='translator_serptitle', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
		unset($langs);
		echo "<hr>";
	}
	}
	
	
?>