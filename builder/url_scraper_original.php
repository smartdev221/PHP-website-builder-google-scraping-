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
				}
			}
			

function get_source($url, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT){
	
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
	//echo $body;
	
	return $body;
}

function scraper($urls, $words, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT){
	$texttoreturn = "";
	
	foreach($urls as $url){
		if(!empty($url)){
		for($i = 1; $i < 4; $i++){
			echo "url:".$url."<br>TRY #".$i."<br>";
			$source = get_source($url, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
			if(!empty($source)){
				$doc = new DOMDocument();
				libxml_use_internal_errors(true);
				
				$doc->loadHTML($source);
				libxml_clear_errors();

				$xpath = new DOMXpath($doc);
				$elements = $xpath->query("//div");
				
				$collection = array();
				if (!is_null($elements)) {
				  foreach ($elements as $element) {
					//print_r($element);
				   //  echo "<br/>[". $element->nodeName. "]";

					$nodes = $element->childNodes;
					$noP = 0;
					$text = "";
					foreach ($nodes as $node) {
						if($node->nodeName == "p"){
							//echo "subnod nume:".$node->nodeName."\n";
							//echo $node->nodeValue. "\n";
							$noP++;
							$text.=$node->nodeValue."\n";
						}
						$collection[] = array("nop"=>$noP, "text"=>$text);
					}
				  }
				}
				$textfinal = "";
				$celmaimare = 0;
				foreach($collection as $celement){
					if($celement['nop'] > $celmaimare){
						$celmaimare = $celement['nop'];
						$textfinal = $celement['text'];
					}
				}
				$texttoreturn.="\n".$textfinal;
				unset($collection);
				break;
			}
		}
		$wordsnow = explode(" ", $texttoreturn);
		
		echo "words now: ".count($wordsnow)."<br><br>".$texttoreturn."<hr>";
		
			if(count($wordsnow) > $words){
				break;
			}
		}
		
	}
	return $texttoreturn;
}
	$websites = array();
	
	$getwebsites = mysqli_query($conn, "SELECT * FROM `websites`");
	while($website = mysqli_fetch_object($getwebsites)){
		$websites[$website->id] = $website->words;
	}
	
	$sel = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `scraped`='1' and `result1`!='' and `url_scraped`='0' LIMIT 2");
	
	while($row = mysqli_fetch_object($sel)){
		
		$urls = array($row->result1,$row->result2,$row->result3,$row->result4,$row->result5,$row->result6,$row->result7,$row->result8,$row->result9,$row->result10);
		$content = scraper($urls, $websites[$row->website], $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
		
		mysqli_query($conn, "INSERT INTO `scraped_content_serp`(`idkeywords`, `content`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($conn, $content)."')");
		mysqli_query($conn, "UPDATE `keywords` SET `url_scraped`='1' WHERE `idkeywords`='".$row->idkeywords."'");
		unset($urls);
	}
	
?>