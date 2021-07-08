<?php
	include("../config.php");
//readability
include 'vendor/autoload.php';

use andreskrey\Readability\Readability;
use andreskrey\Readability\Configuration;
use andreskrey\Readability\ParseException;

//$readability = new Readability(new Configuration());
//

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
		//echo "SETTING ".$proxyip."\n<br>";
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
	//old method
	$html = "";
	//
	//random subheadings method
	$random_subheadings_c = "";
	//
	
	$content_urls = array();
	$count = array();
	
	$iurl = 0;
	
	//new method
	$intro_s = 0;
	$intro = "";
	$cause_s = 0;
	$cause = "";
	$random_subheadings_s = 0;
	$random_subheadings = "";
	$fix_s = 0;
	$fix = "";
	$headings = array("h2", "h3");
	$skip = array("div", "aside", "article", "picture", "svg", "figcaption");
	//
	foreach($urls as $url){
		//new method
		$intro_c = "";
		$cause_c = "";
		$fix_c = "";
		$random_subheadings_current = "";
		$found = 0;
		$found_r = 0;
		//
		if(!empty($url)){
			//ignore youtube videos
		if(!preg_match('/http(?:s)?:\/\/(?:www\.)?youtube/', $url)){
		for($i = 1; $i < 4; $i++){
			//echo "url:".$url."<br>TRY #".$i."<br>";
			$source = get_source($url, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
			if(!empty($source)){
				//ignore sources with captcha
				if(preg_match('/Our systems have detected unusual traffic activity from your network/', $source)){
					break;
				}
				//ignore sources with cloudflare
				if(preg_match('/Your browser will redirect to your requested content shortly/', $source)){
					break;
				}
				
				try {
					$readability = new Readability(new Configuration());
					$readability->parse($source);
					//echo $readability;
				
				
				
				
				
				$doc = new DOMDocument();
				libxml_use_internal_errors(true);
				
				$doc->loadHTML($readability);
				libxml_clear_errors();

				$xpath = new DOMXpath($doc);
				$elements = $xpath->query("//div");
				
				
				if (!is_null($elements)) {
				  foreach ($elements as $element) {
					//print_r($element);
				   //  echo "<br/>[". $element->nodeName. "]";

					$nodes = $element->childNodes;
					
					foreach ($nodes as $node) {
						
						if(!in_array($node->nodeName, $skip)){
						//echo " ".$node->nodeName.", ";
						
							if($intro == "" && $intro_s == 0){
								$found = 1;
								$intro_s = 1;
								$intro.=$doc->saveHTML($node);
								$intro_c.=remove($doc->saveHTML($node));
								//print_r($node);
							} elseif(in_array($node->nodeName, $headings) AND $intro_s == 1) {
								$intro_s = 2;
							} elseif($intro_s == 1) {
								$intro.=$doc->saveHTML($node);
								$intro_c.=remove($doc->saveHTML($node));
							}
							//random subheadings method
							if($random_subheadings == "" && in_array($node->nodeName, $headings) && $found_r == 0){
								$found_r = 1;
								$random_subheadings.=$doc->saveHTML($node);
								$random_subheadings_current.=remove($doc->saveHTML($node));
								$random_subheadings_s = 1;
							}elseif(in_array($node->nodeName, $headings) AND $random_subheadings_s == 1){
								$random_subheadings_s = 2;
							}elseif($random_subheadings_s == 1){
								$random_subheadings.=$doc->saveHTML($node);
								$random_subheadings_current.=remove($doc->saveHTML($node));
							}
							//random subheading method
							
							if($cause == "" && in_array($node->nodeName, $headings) && preg_match('/(cause|causes|why|what)/i', $node->textContent) && $found == 0){
								$found = 1;
								$cause.=$doc->saveHTML($node);
								$cause_c.=remove($doc->saveHTML($node));
								$cause_s = 1;
							}elseif(in_array($node->nodeName, $headings) AND $cause_s == 1){
								$cause_s = 2;
							}elseif($cause_s == 1){
								$cause.=$doc->saveHTML($node);
								$cause_c.=remove($doc->saveHTML($node));
							}
							
							if($fix == "" && in_array($node->nodeName, $headings) && preg_match('/(fix|repair|resolution|solution|method)/i', $node->textContent) && $found == 0){
								$found = 1;
								$fix.=$doc->saveHTML($node);
								$fix_c.=remove($doc->saveHTML($node));
								$fix_s = 1;
							}elseif(in_array($node->nodeName, $headings) && !preg_match('/(fix|repair|resolution|solution|method)/i', $node->textContent) AND $fix_s == 1){
								$fix_s = 2;
							}elseif($fix_s == 1){
								$fix.=$doc->saveHTML($node);
								$fix_c.=remove($doc->saveHTML($node));
							}
							
							
							//old method of content gathering - full article based on words
							if($node->nodeName == "p"){
								if(count(explode(" ",$node->nodeValue)) > 10){
									$html.= remove($doc->saveHTML($node))."\n";
								}
							} else {
								$html.= remove($doc->saveHTML($node))."\n";
							}
							//old method
						
					} else {
						//echo $node->nodeName.$node->textContent."<br>";
					}
					}
				  }
				} else {
					echo "I can't find an article inside DIV. You can check if its a mistake: ".$url."<br>";
				}
				
				//old
				$wordsnow = explode(" ", $html);
				if(count($wordsnow) > $words){
					$old = "\n".$html;
					$html = "";
				} else {
					$old = "";
				}
				//
				//random headings
					//if subheading is under 300 words, use it, else ignore
				if(count(explode(" ", $random_subheadings_current)) < 300){
					$random_subheadings_c.=$random_subheadings_current;
				}
				//if current word count over minimum/2
				$wordsnow_r = explode(" ", $random_subheadings_c);
				if(count($wordsnow_r) > ($words/2)){
					$random = "\n".$random_subheadings_c;
					$random_subheadings_c = "";
				} else {
					$random = "";
				}
				//
				//do not leave intro empty
				$check_intro = preg_replace('/\s+/', '\s', $intro_c);
				if(count(explode(" ",$check_intro)) > 10 OR strlen($intro_c) < 30){
					$intro = "";
					$intro_c = "";
					$intro_s = 0;
				}
				//
				$content_urls[] = array("id"=>$iurl, "intro"=>$intro_c, "cause"=>$cause_c, "fix"=>$fix_c, "old"=>$old, "random"=>$random);
				
				} catch (ParseException $e) {
					echo sprintf('Error processing text: %s', $e->getMessage());
				}
				
				break;
				
			}
		}
				
			
			
			
		}
		}
		
		
		
		$iurl++;
	}
	//print_r($content_urls);
	//
	
	return $content_urls;
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
	$current_website = "";
	
	$getwebsites = mysqli_query($conn, "SELECT * FROM `websites` where `skip`='0'");
	while($website = mysqli_fetch_object($getwebsites)){
		$websites[$website->id] = $website->words;
		$title[$website->id] = $website->serptitle;
		$not_matched[$website->id] = $website->serp_not_matched;
		if($website->built < $website->dripfeed_current AND $current_website == ""){
			$current_website = $website->id;
		}
	}

	//$sel = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `scraped`='1' and `result1`!='' and `url_scraped`='0' and `serp_checked`='1' and `built`='0' LIMIT 2");
	$sel = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `scraped`='1' and `result1`!='' and `url_scraped`='0' and `serp_checked`='1' and `built`='0' and `website`='".$current_website."' LIMIT 2");
	//$sel = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `idkeywords`='54372'");
	/* for debugging
	if(!empty($_GET['show'])){
		$sel = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `url_scraped`='1' and `built`='1' and `idkeywords`='".mysqli_real_escape_string($conn, $_GET['show'])."'");
	} else {
		$sel = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `url_scraped`='1' and `built`='1' order by rand() LIMIT 1");
	}
	*/
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
			//
			$content = "";
			$source = "";
			$source_id = "";
			$intro = "";
			$cause = "";
			$fix = "";
			$old = "";
			$old_source = "";
			$old_source_id = "";
			$serp_title = "";			
			///
			$matched = "-";
			logger($row->idkeywords, $row->original, 'Serp scraping', $conn);
			$urls = array($row->result1,$row->result2,$row->result3,$row->result4,$row->result5,$row->result6,$row->result7,$row->result8,$row->result9,$row->result10);
			//random url order
			//shuffle($urls);
			$newurls = array();
			foreach($urls as $url){
				$check_blacklist = ignore_url($url, $conn);
				if(!empty($check_blacklist)){
					$newurls[] = $url;
				}
			}
			
			$scrape = scraper($newurls, $websites[$row->website], $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
			//$content = relative($scrape['content']);
			
			
				echo "Keyword: ".$row->original."<br>";
				
				foreach($scrape as $sursa){
					if(!empty($_GET['show'])){
							echo "<br><a href=\"".$newurls[$sursa['id']]."\" target=\"_blank\">".$newurls[$sursa['id']]."</a><br>";
					}
					if(!empty($sursa['intro']) && empty($intro)){
						//echo "<br>Intro source: ".$newurls[$sursa['id']];
						$intro = $sursa['intro'];
					}
					if(!empty($sursa['cause']) && empty($cause)){
						//echo "<br>Cause source: ".$newurls[$sursa['id']];
						$cause = $sursa['cause'];
						$source = $newurls[$sursa['id']];
						$source_id = $sursa['id'];
					}
					if(!empty($sursa['fix']) && empty($fix)){
						//echo "<br>Fix source: ".$newurls[$sursa['id']];
						$fix = $sursa['fix'];
					}
					if(!empty($sursa['old']) && empty($old)){
						//echo "<br>Old source: ".$newurls[$sursa['id']];
						$old = $sursa['old'];
						$old_source = $newurls[$sursa['id']];
						$old_source_id = $sursa['id'];
					}
					if(!empty($sursa['random']) && empty($random)){
						//echo "<br>Random source: ".$newurls[$sursa['id']];
						$random = $sursa['random'];
						$random_source = $newurls[$sursa['id']];
						$random_source_id = $sursa['id'];
					}
					
				}
				
				//echo "<br><br>GENERATED ARTICLE:<textarea rows=\"100\" cols=\"60\">".$intro."<hr>\n\n".$cause."<hr>\n\n".$fix."</textarea><br><br><br>";
				
				
				
				
				if(empty($intro) OR empty($cause) OR empty($fix)){
					$matched = "no";
				} else {
					$matched = "yes";
				}
				if($not_matched[$row->website] == 1 && $matched == "no"){
					//echo "<h1>It fails to match intro, cause and fix. It is set to SKIP. Result: Skipping keyword!</h1>";
					logger($row->idkeywords, $row->original, 'No serp content for NEW method. SKIP!', $conn);
					mysqli_query($conn, "UPDATE `keywords` SET `built`='5',`reason`='No serp content for NEW method. SKIP!' WHERE `idkeywords`='".$row->idkeywords."'") or die(mysqli_error($conn));
					mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
				}elseif($not_matched[$row->website] == 2 && $matched == "no"){
					//echo "<h1>It fails to match intro, cause and fix. It is set to OLD method, counting words. Result: </h1>".$old;
					logger($row->idkeywords, $row->original, 'Serp scraped OLD method', $conn);
					mysqli_query($conn, "INSERT INTO `scraped_content_serp`(`idkeywords`, `content`, `source`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($conn, $old)."', '".mysqli_real_escape_string($conn, $old_source)."')");
					mysqli_query($conn, "UPDATE `keywords` SET `url_scraped`='1' WHERE `idkeywords`='".$row->idkeywords."'");
				
					//forserp title
					$serp_title = $old_source_id;
					//
				}elseif($not_matched[$row->website] == 3 && $matched == "no"){
					//echo "<h1>It fails to match intro, cause and fix. It is set to Random Subheadings method, counting words. Result: </h1>".$random;
					
					if(preg_match_all('/<h\d/', $random) <4 ){
						//echo "Not enough subheadings. SKIP!";
						logger($row->idkeywords, $row->original, 'No serp content for Random Subheadings method. SKIP!', $conn);
						mysqli_query($conn, "UPDATE `keywords` SET `built`='5',`reason`='No serp content for Random Subheadings method. SKIP!' WHERE `idkeywords`='".$row->idkeywords."'") or die(mysqli_error($conn));
						mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
						
					} else {
						logger($row->idkeywords, $row->original, 'Serp scraped Random Subheadings method', $conn);
						mysqli_query($conn, "INSERT INTO `scraped_content_serp`(`idkeywords`, `content`, `source`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($conn, $random)."', '".mysqli_real_escape_string($conn, $random_source)."')");
						mysqli_query($conn, "UPDATE `keywords` SET `url_scraped`='1' WHERE `idkeywords`='".$row->idkeywords."'");
					}
					//forserp title
					$serp_title = $random_source_id;
					//
				}elseif(!empty($intro) && !empty($cause) && !empty($fix)) {
					//echo "<h1>Success match intro, cause and fix. </h1>";
					$content = $intro."\n".$cause."\n".$fix;
					logger($row->idkeywords, $row->original, 'Serp scraped NEW method', $conn);
					mysqli_query($conn, "INSERT INTO `scraped_content_serp`(`idkeywords`, `content`, `source`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($conn, $content)."', '".mysqli_real_escape_string($conn, $source)."')");
					mysqli_query($conn, "UPDATE `keywords` SET `url_scraped`='1' WHERE `idkeywords`='".$row->idkeywords."'");
					
					//forserp title
					$serp_title = $source_id;
					//
				}
				
				//if its set to use serp title
				if($title[$row->website] == 1 && !empty($serp_title)){
					
					$uri = $newurls[$serp_title];					
							for($i1 = 1; $i1 <= 10; $i1++){
								if($uri == $row->{'result'.$i1}){
									$ptitle = $row->{'result'.$i1.'title'};
									//echo $ptitle." < page title";
									mysqli_query($conn, "INSERT INTO `serp_titles`(`idkeywords`, `title`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($conn, $ptitle)."')");
				
								}
							}
				}
			

			unset($urls);
			unset($newurls);
		}
	}
	
	
	
?>