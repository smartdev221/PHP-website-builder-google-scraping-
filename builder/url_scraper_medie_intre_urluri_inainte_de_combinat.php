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
					  break;
				} else {
				   echo "The script was run from a webserver, or something else";
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
function remove($content){
	$content = str_replace('"></a>', '"> </a>', $content);
	$content = preg_replace('/<i class="[A-z-_+\s]+"><\/i>/', "", $content);
	$content = preg_replace('/ class(?:\s)?=(?:\s)?"[A-z0-9-_+\s]+"/', "", $content);
	$content = preg_replace('/ style(?:\s)?=(?:\s)?"[A-z0-9-_+\s:;#.,\(\)]+"/sU', "", $content);
	$content = preg_replace('/ align="[A-z]+"/', "", $content);
	$content = preg_replace('/ id="[A-z0-9-_+\s]+"/sU', "", $content);
	$content = preg_replace('/ lang="[A-z0-9-_+\s]+"/sU', "", $content);
	$content = preg_replace('/ width="[A-z0-9-_+\s]+"/sU', "", $content);
	$content = preg_replace('/ height="[A-z0-9-_+\s]+"/sU', "", $content);
	$content = preg_replace('/ alt="[A-z0-9-_+\s.]+"/sU', "", $content);
	$content = preg_replace('/ title=".*"/sU', "", $content);
	$content = preg_replace('/ onclick(?:\s)?=(?:\s)?"[A-z0-9-_+\s:;.\(\)=\',]+"/', "", $content);
	$content = preg_replace('/ data-[a-z-]+=[A-z0-9-_+\{\}\(\)\"\:\;,\.\/\?\'\=\s]+/', "", $content);	
	$content = preg_replace('/<textarea.*>(.*)<\/textarea>/sU', "$1", $content);
	$content = preg_replace('/<time.*<\/time>/sU', "", $content);
	$content = preg_replace('/<script>[A-z0-9-_+\{\}\(\)\"\:\;,\.\/\?\'\=\s]+<\/script>/', "", $content);	
	$content = preg_replace('/<svg[A-z0-9-_+\{\}\(\)\"\:\;,\.\/\?\'\=\s\>\<]+<\/svg>/', "", $content);
		//bad stopping point
	//$content = preg_replace('/<svg[A-z0-9-_+\{\}\(\)\"\:\;,\.\/\?\'\=\s\>\<\#%]+<\/svg>/', "", $content);	
	$content = preg_replace('/<p\s+>/', "<p>", $content);		
	$content = preg_replace('/\sdir="\w+"/', "", $content);
	$content = preg_replace('/<span(?:\s+)?>/', "", $content);	
	$content = preg_replace('/<\/span>/', "", $content);
	//remove bold, italic, links and images
	$content = preg_replace('/<b(?:\s+)?>/', "", $content);	
	$content = preg_replace('/<\/b>/', "", $content);
	$content = preg_replace('/<i(?:\s+)?>/', "", $content);	
	$content = preg_replace('/<\/i>/', "", $content);
	$content = preg_replace('/<em(?:\s+)?>/', "", $content);	
	$content = preg_replace('/<\/em>/', "", $content);
	$content = preg_replace('/<strong(?:\s+)?>/', "", $content);	
	$content = preg_replace('/<\/strong>/', "", $content);
	$content = preg_replace('/<figure(?:\s+)?>/', "", $content);	
	$content = preg_replace('/<\/figure>/', "", $content);
	$content = preg_replace('/\sdir="\w+"/', "", $content);
	$content = preg_replace('/\starget(?:\s)?=(?:\s)?"_\w+"/', "", $content);
	$content = preg_replace('/\srel(?:\s)?=(?:\s)?"[\w\s]+"/', "", $content);
	$content = preg_replace('/<a\s+href(?:\s)?=(?:\s)?"([A-z0-9:\/.,\-_&;\?=!#()%]+)"(?:.+)?>(.+)<\/a>/U', "$2", $content);	
	$content = preg_replace('/<img\s+src(?:\s)?=(?:\s)?"([A-z0-9:\/.,\-_&;\?=!#()%]+)"(?:.+)?>/U', "", $content);
	
	return $content;
}
function relative($content){
	//look for relative links
	preg_match_all('/<a\s+href="([A-z:\/.\-_&]+)"(?:.+)?>(.+)<\/a>/', $content, $matches);
	//if found
	if(count($matches[0]) > 0){
			for($i =0; $i<count($matches[0]); $i++){
			//if it doesn't start with http
			if(!preg_match("/^http/", $matches[1][$i])){
				//replace html with <a> text 
				$content = str_replace($matches[0][$i], $matches[2][$i], $content);
			}
		}
	}
	
	return $content;
}

function getClosest($search, $arr) {
   $closest = null;
   foreach ($arr as $item) {
      if ($closest === null || abs($search - $closest) > abs($item['words'] - $search)) {
         $closest = $item['words'];
      }
   }
   return $closest;
}

function scraper($urls, $words, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT){
	unset($texttoreturn);
	
	$content_urls = array();
	$count = array();
	
	$iurl = 0;
	foreach($urls as $url){
		
		if(!empty($url)){
			//ignore youtube videos
		if(!preg_match('/http(?:s)?:\/\/(?:www\.)?youtube/', $url)){
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
					$html = "";
					foreach ($nodes as $node) {
						//if($node->nodeName == "p"){
						if($node->nodeName == "p" or $node->nodeName == "h1" or $node->nodeName == "h2" or $node->nodeName == "h3"){
							//echo "subnod nume:".$node->nodeName."\n";
							//echo $node->nodeValue. "\n";
							
							//only paragraphs with words > 20
							if($node->nodeName == "p"){
							if(count(explode(" ",$node->nodeValue)) > 20){
								//print_r($node);
								//echo remove($doc->saveHTML($node));
								$noP++;
								$text.=$node->nodeValue."\n";
								$html.=remove($doc->saveHTML($node))."\n";
							}
							} else {
								$html.=remove($doc->saveHTML($node))."\n";
							}
						}
						$collection[] = array("nop"=>$noP, "text"=>$text, "html"=>$html);
					}
				  }
				}
				$textfinal = "";
				$celmaimare = 0;
				$wordcount = 0;
				foreach($collection as $celement){
					if($celement['nop'] > $celmaimare){
						$celmaimare = $celement['nop'];
						$textfinal = $celement['html'];
						$wordcount = count(explode(" ",$celement['text']));
					}
				}
				//$texttoreturn.="\n".$textfinal;
				unset($collection);
				$content_urls[] = array("text"=>$textfinal, "words"=>$wordcount, "id"=>$iurl);
				$count[] = $wordcount;
				
				break;
			}
		}
				
		//echo "words now: ".count($wordsnow)."<br><br>".$texttoreturn."<hr>";
		
			
			
		}
		}
		
		
		
		$iurl++;
	}
	//print_r($content_urls);
	//
	$a=$count;
	if(count($a)) {
		$a = array_filter($a);
		$average = array_sum($a)/count($a);
		//echo "word average:".$average."<br>";
	}

	$contentbywords = getClosest($average, $content_urls);
	//echo "\n".$contentbywords." < content by words<br>";
	foreach($content_urls as $content){
		if($content['words'] == $contentbywords){
			$texttoreturn = array("content"=>$content['text'], "id"=>$content["id"]);
			//echo "\nmatch words de la closest ".$contentbywords."<br>";
		}
	}
	//print_r($texttoreturn);
	
	return $texttoreturn;
}
function domainmatch($url){
	$info = parse_url($url);
	$host = $info['host'];
	$host_names = explode(".", $host);
	$bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
	
	return $bottom_host_name;
}
function ignore_url($url, $conn){
	
	$blacklist_found = 0;
	
	$select = mysqli_query($conn, "SELECT * FROM `config_global` WHERE `name`='SERP_SCRAPE_IGNORE'") or die(mysqli_error($conn));
	if(mysqli_num_rows($select) > 0){
		while($row = mysqli_fetch_object($select)){
				$look = explode("\r\n", $row->value);
				foreach($look as $pm){
					if(preg_match("/".$pm."/i", domainmatch($url))){
						$blacklist_found = 1;
					}
				}
		}
		if($blacklist_found == 1){
			return "";
		} else {
			//if its not blacklisted
			return $url;
		}
	} else {
		//no settings found so return original
		return $url;
	}
}
	$websites = array();
	
	$getwebsites = mysqli_query($conn, "SELECT * FROM `websites`");
	while($website = mysqli_fetch_object($getwebsites)){
		$websites[$website->id] = $website->words;
		$title[$website->id] = $website->serptitle;
	}
	
	$sel = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `scraped`='1' and `result1`!='' and `url_scraped`='0' and `serp_checked`='1' and `built`='0' LIMIT 5");
	
	$ids = array();
	while($row = mysqli_fetch_object($sel)){
		mysqli_query($conn, "UPDATE `keywords` set `url_scraped`='2',`last_action`='serp_scraper', `last_action_date`='".date("Y-m-d H:i:s")."' where `idkeywords`='".$row->idkeywords."'");
		$ids[] = $row->idkeywords;
	}
	if(mysqli_num_rows($sel) > 0){
		$query = "";
		$i = 0;
		foreach($ids as $id){
			if($i == 0){
				$query.= "`idkeywords`='".$id."'";
			} else {
				$query.= " or `idkeywords`='".$id."'";
			}
			$i++;
		}
		$sel1 = mysqli_query($conn, "SELECT * FROM `keywords` WHERE ".$query);
		
		while($row = mysqli_fetch_object($sel1)){
			logger($row->idkeywords, $row->original, 'Serp scraping', $conn);
			$urls = array($row->result1,$row->result2,$row->result3,$row->result4,$row->result5,$row->result6,$row->result7,$row->result8,$row->result9,$row->result10);
			//random url order
			shuffle($urls);
			$newurls = array();
			foreach($urls as $url){
				$check_blacklist = ignore_url($url, $conn);
				if(!empty($check_blacklist)){
					$newurls[] = $url;
				}
			}
			
			$scrape = scraper($newurls, $websites[$row->website], $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
			$content = relative($scrape['content']);
			
			//if its set to use serp title
			if($title[$row->website] == 1){
				
				$uri = $newurls[$scrape['id']];					
						for($i1 = 1; $i1 <= 10; $i1++){
							if($uri == $row->{'result'.$i1}){
								$ptitle = $row->{'result'.$i1.'title'};
								//echo $ptitle." < page title";
								mysqli_query($conn, "INSERT INTO `serp_titles`(`idkeywords`, `title`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($conn, $ptitle)."')");
			
							}
						}
			}
			echo $content;
			//for source tag
			$source = $newurls[$scrape['id']];
			echo $source;
			logger($row->idkeywords, $row->original, 'Serp scraped', $conn);
			mysqli_query($conn, "INSERT INTO `scraped_content_serp`(`idkeywords`, `content`, `source`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($conn, $content)."', '".mysqli_real_escape_string($conn, $source)."')");
			mysqli_query($conn, "UPDATE `keywords` SET `url_scraped`='1' WHERE `idkeywords`='".$row->idkeywords."'");
			unset($urls);
			unset($newurls);
		}
	}
	
?>