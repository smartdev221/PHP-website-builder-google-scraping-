<?php
	include("../config.php");

$getconf = mysqli_query($conn, "SELECT name,value_ FROM `config`");
			while($getconfig = mysqli_fetch_object($getconf)){
				if($getconfig->name == "PROXY"){
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
$select_global = mysqli_query($conn, "SELECT * FROM `config_global` where `name`='HEADLINE_SENTENCES'") or die(mysqli_error($conn));
	if(mysqli_num_rows($select_global) > 0){
		while($row = mysqli_fetch_object($select_global)){
				$HEADLINE_SENTENCES = $row->value;
		}
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
	$sel = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `translated_headline`='0' and `scraped`='1' and `built`='0' and `serp_checked`='1' LIMIT 1");
	if(mysqli_num_rows($sel) > 0){
	while($row = mysqli_fetch_object($sel)){
		logger($row->idkeywords, $row->original, 'Headline translating', $conn);
		$text = str_replace('%keyword%', $row->original, $HEADLINE_SENTENCES);
		$expl = explode("\r\n", $text);
		shuffle($expl);
		
		$selected = $expl[0];
		
		echo $selected;
		
		$langs = array('en|de', 'de|fr', 'fr|ru', 'ru|en');
		echo "keyword id: #".$row->idkeywords."<br>";
		$fortranslate = substr($selected, 0, 9000);//2000
		$content = scraper($langs, $fortranslate, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
		
		//print_r($content);
		foreach($content as $lang=>$source){
			${$lang} = $source;
		}
		if(!empty($content_en)){
			logger($row->idkeywords, $row->original, 'Headline translated', $conn);
			mysqli_query($conn, "INSERT INTO `headlines`(`idkeywords`, `content`, `content_de`, `content_fr`, `content_ru`, `content_en`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($conn, $selected)."', '".mysqli_real_escape_string($conn, $content_de)."', '".mysqli_real_escape_string($conn, $content_fr)."', '".mysqli_real_escape_string($conn, $content_ru)."', '".mysqli_real_escape_string($conn, $content_en)."')");
			mysqli_query($conn, "UPDATE `keywords` SET `translated_headline`='1',`last_action`='translator_headline', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
		} else {
			logger($row->idkeywords, $row->original, 'Headline couldn\'t be translated', $conn);
			mysqli_query($conn, "UPDATE `keywords` SET `last_action`='translator_headline', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
		}
		unset($langs);
		echo "<hr>";
	}
	}
	
	
?>