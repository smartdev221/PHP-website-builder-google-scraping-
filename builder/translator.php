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
//spinning
/*class Spintax
{
    public function process($text)
    {
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            array($this, 'replace'),
            $text
        );
    }
    public function replace($text)
    {
        $text = $this->process($text[1]);
        $parts = explode('|', $text);
        return $parts[array_rand($parts)];
    }
}*/
function isCapital($word){
	
	if(preg_match('#^\p{Lu}#u', $word)){
		return true;
	} else {
		return false;
	}
	
}
function checkspin($content, $spin){
	if($spin == 0){
		return $content;
	} else {
		$split = explode(" ", $content);
	
		if($spin > 1){
		for($i = rand(0,5); $i<= count($split); $i=$i+$spin){
			$word = $split[$i];
				$word1 = str_replace(".","",$word);
				$word1 = str_replace(",","",$word1);
				$word1 = str_replace("?","",$word1);
				$word1 = str_replace("!","",$word1);
				$word1 = str_replace("-","",$word1);
				$word1 = str_replace(":","",$word1);
							
				$word2 = str_replace($word1, "%word%", $word);
				//if the word is windows or error go to no 5
				if(strtolower($word1) == "windows" or strtolower($word1) == "error"){
					if(!isCapital($split[$i-1])){
						//check for capital
					$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($split[$i-1]))."'");
					if(mysql_num_rows($select) > 0){
						$row1 = mysql_fetch_object($select);
						//echo "{".$row1->spin."}";
						$split[$i-1]= "{".$row1->spin."}";
					} else {
						if(!isCapital($split[$i+1])){
							//if 5 is not in thesaurus go to no 7
							$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($split[$i+1]))."'");
							if(mysql_num_rows($select) > 0){
								$row1 = mysql_fetch_object($select);
								//echo "{".$row1->spin."}";
								$split[$i+1]= "{".$row1->spin."}";
							}
						}
					}
					}//end isCapital
				} else {
					//if its not Capital
					if(!isCapital($word1)){
						//select regular word
						$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($word1))."'");
						if(mysql_num_rows($select) > 0){
							$row1 = mysql_fetch_object($select);
							//echo "{".$row1->spin."}";
							$split[$i]= str_replace("%word%","{".$row1->spin."}", $word2);
						} else {
							/// if word not in thesaurus go to no 5
							if(!isCapital($split[$i-1])){
								//echo "%notfound% $word1";
								//$new[]="#not".$word1."found#";
								$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($split[$i-1]))."'");
								if(mysql_num_rows($select) > 0){
									$row1 = mysql_fetch_object($select);
									//echo "{".$row1->spin."}";
									$split[$i-1]= "{".$row1->spin."}";
								} else {
									/// if no 5 not in thesaurus, go to no 7
									if($i >= count($split)){
										//if 7 reached end of split go to no 4.
										if(!isCapital($split[$i-2])){
											$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($split[$i-2]))."'");
											if(mysql_num_rows($select) > 0){
												$row1 = mysql_fetch_object($select);
												//echo "{".$row1->spin."}";
												$split[$i-2]= "{".$row1->spin."}";
											}
										}//end isCapital
										//echo "final";
									} else {
										if(!isCapital($split[$i+1])){
											//if 7 not reached end of life.. continue with it.
											$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($split[$i+1]))."'");
											if(mysql_num_rows($select) > 0){
												$row1 = mysql_fetch_object($select);
												//echo "{".$row1->spin."}";
												$split[$i+1]= "{".$row1->spin."}";
											}
										}//end isCapital
									}	
								}
							}//end isCapital
						}
					}
				}
				
			
			//$i++;
		}
		} else {
			for($i = 0; $i<= count($split); $i=$i+1){
				$word = $split[$i];
				$word1 = str_replace(".","",$word);
				$word1 = str_replace(",","",$word1);
				$word1 = str_replace("?","",$word1);
				$word1 = str_replace("!","",$word1);
				$word1 = str_replace("-","",$word1);
				$word1 = str_replace(":","",$word1);
							
				$word2 = str_replace($word1, "%word%", $word);
				//if the word is windows or error
				if(strtolower($word1) != "windows" or strtolower($word1) != "error"){
					//if its not Capital
					if(!isCapital($word1)){
						//select regular word
						$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($word1))."'");
						if(mysql_num_rows($select) > 0){
							$row1 = mysql_fetch_object($select);
							//echo "{".$row1->spin."}";
							$split[$i] = str_replace("%word%","{".$row1->spin."}", $word2);
						}
					}
				}			
			}
		}
		$spintax = new Spintax();
		$spinned = $spintax->process(implode(" ", $split));
		
		return $spinned;
	}
	
}
//end spinning functions
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
	$content_de = "";
	$content_fr = "";
	$content_ru = "";
	$content_en = "";
	
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
	//$turn = rand(1,3);
	parse_str($argv[1], $params);
	$turn = $params['turn'];
	echo "Turn is now:".$turn;
	//translate scraped content
	$skip = false;
	if($turn == 1){
	$sel = mysqli_query($conn, "SELECT * FROM `scraped_content_serp` WHERE `translated`='0' LIMIT 3");
	if(mysqli_num_rows($sel) > 0){
	while($row = mysqli_fetch_object($sel)){
		logger($row->idkeywords, $row->original, 'Translating serp content', $conn);
		//select website
		$selectwebsite = mysqli_query($conn, "SELECT `website` FROM `keywords` WHERE `idkeywords`='".$row->idkeywords."'");
		$website = mysqli_fetch_object($selectwebsite);
			$settings = mysqli_query($conn, "SELECT `spin` FROM `websites` WHERE `id`='".$website->website."'");
			$spin = mysqli_fetch_object($settings);
		//
		$contentfortranslation = checkspin($row->content, $spin->spin);
		$langs = array('en|de', 'de|fr', 'fr|ru', 'ru|en');
		echo "keyword id: #".$row->idkeywords."<br>";
		$fortranslate = substr($contentfortranslation, 0, 9000);//2000
		$content = scraper($langs, $fortranslate, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
		
		//print_r($content);
		foreach($content as $lang=>$source){
			${$lang} = $source;
		}
		logger($row->idkeywords, $row->original, 'Translated serp content', $conn);
		mysqli_query($conn, "UPDATE `scraped_content_serp` SET `content_de`='".mysqli_real_escape_string($conn, $content_de)."', `content_fr`='".mysqli_real_escape_string($conn, $content_fr)."', `content_ru`='".mysqli_real_escape_string($conn, $content_ru)."', `content_en`='".mysqli_real_escape_string($conn, $content_en)."',`translated`='1' WHERE `idkeywords`='".$row->idkeywords."'");
		mysqli_query($conn, "UPDATE `keywords` SET `translated_serp`='1',`last_action`='translator_serp', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
		unset($langs);
		echo "<hr>";
	}
	$skip = true;
	}
	}
	//translate q&a
	//if($skip == false){
	if($turn == 2){
		
		$sel = mysqli_query($conn, "SELECT * FROM `qa` WHERE `translated`='0' LIMIT 5");
		if(mysqli_num_rows($sel) > 0){
		while($row = mysqli_fetch_object($sel)){
			logger($row->idkeywords, $row->original, 'Q&A translating', $conn);
			$langs = array('en|de', 'de|fr', 'fr|ru', 'ru|en');
			echo "q&a id: #".$row->id."<br>";
			//replace hex encoded e.g. \x27
			$fortranslate = preg_replace_callback(
			  "(\\\\x([0-9a-f]{2}))i",
			  function($a) {return chr(hexdec($a[1]));},
			  $row->answer
			);
			//
			$fortranslate = substr($fortranslate, 0, 9000);//2000
			$content = scraper($langs, $fortranslate, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);

			foreach($content as $lang=>$source){
				${$lang} = $source;
			}
			logger($row->idkeywords, $row->original, 'Q&A translated', $conn);
			mysqli_query($conn, "UPDATE `qa` SET `answer_de`='".mysqli_real_escape_string($conn, $content_de)."', `answer_fr`='".mysqli_real_escape_string($conn, $content_fr)."', `answer_ru`='".mysqli_real_escape_string($conn, $content_ru)."', `answer_en`='".mysqli_real_escape_string($conn, $content_en)."',`translated`='1' WHERE `id`='".$row->id."'");
			unset($langs);
			echo "<hr>";
		}
		$skip = true;
		}
		
	}
	//translate snippets
	//if($skip == false){
	if($turn == 3){
		
		$sel = mysqli_query($conn, "SELECT * FROM `featuredsnippets` WHERE `translated`='0' LIMIT 5");
		if(mysqli_num_rows($sel) > 0){
		while($row = mysqli_fetch_object($sel)){
			if($row->snippettype != '4'){
				logger($row->idkeywords, $row->original, 'Featured snippets translating', $conn);
			$langs = array('en|de', 'de|fr', 'fr|ru', 'ru|en');
			echo "snippet id: #".$row->id."<br>";
			//replace hex encoded e.g. \x27
			$fortranslate = preg_replace_callback(
			  "(\\\\x([0-9a-f]{2}))i",
			  function($a) {return chr(hexdec($a[1]));},
			  $row->snippetcontent
			);
			//
			$fortranslate = substr($fortranslate, 0, 9000);//2000
			$content = scraper($langs, $fortranslate, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
			
			//print_r($content);
			foreach($content as $lang=>$source){
				${$lang} = $source;
			}
			logger($row->idkeywords, $row->original, 'Featured snippets translated', $conn);
			mysqli_query($conn, "UPDATE `featuredsnippets` SET `snippetcontent_de`='".mysqli_real_escape_string($conn, $content_de)."', `snippetcontent_fr`='".mysqli_real_escape_string($conn, $content_fr)."', `snippetcontent_ru`='".mysqli_real_escape_string($conn, $content_ru)."', `snippetcontent_en`='".mysqli_real_escape_string($conn, $content_en)."',`translated`='1' WHERE `id`='".$row->id."'");
			unset($langs);
			echo "<hr>";
			} else {
				mysqli_query($conn, "UPDATE `featuredsnippets` SET `snippetcontent_en`='".mysqli_real_escape_string($conn, $row->snippetcontent)."',`translated`='1' WHERE `id`='".$row->id."'");
				echo "snippet id: #".$row->id."<br>";
				echo "skipping... its a video";
				echo "<hr>";
			}
			
		}
		}
		
	}
?>