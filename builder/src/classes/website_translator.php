<?php
class website_translator{

function __construct($link){
	$this->link = $link;
	echo "inside website translator constructor<br>\n";
	
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
			
			
	$sel = mysqli_query($this->link, "SELECT * FROM `website_translation` WHERE `translated`='0' LIMIT 1");
	if(mysqli_num_rows($sel) > 0){
	while($row = mysqli_fetch_object($sel)){
		//
		mysqli_query($this->link, "UPDATE `website_translation` set `translated`='2' where `id`='".$row->id."'");
		
		$select_info = mysqli_query($this->link, "SELECT * FROM `built` WHERE `idkeywords`='".$row->idkeywords."'");
		$info = mysqli_fetch_object($select_info);
		
		logger($row->id, $row->original, 'Translating website to '.$row->language, $this->link);
		// spin
		$settings = mysqli_query($conn, "SELECT `spin` FROM `websites` WHERE `id`='".$info->website."'");
		$spin = mysqli_fetch_object($settings);
		//
		$contentfortranslation = $this->checkspin($info->content, $spin->languages_spin);
		//
		$contentfortranslation = str_replace('%top%', 'Arizona Arizona', $contentfortranslation);
		$contentfortranslation = str_replace('%middle%', 'Arkansas Arkansas', $contentfortranslation);
		$contentfortranslation = str_replace('%bottom%', 'Maryland Maryland', $contentfortranslation);
		//for wordpress
		$contentfortranslation = str_replace('[includeme file="topfix.php"]', 'Arizona Arizona', $contentfortranslation);
		$contentfortranslation = str_replace('[includeme file="middlefix.php"]', 'Arkansas Arkansas', $contentfortranslation);
		$contentfortranslation = str_replace('[includeme file="bottomfix.php"]', 'Maryland Maryland', $contentfortranslation);
		//
		$contentfortranslation = str_replace('%date%', 'Florida Florida', $contentfortranslation);
		//
		$langs = array('en|'.$row->language);
		echo "keyword id: #".$row->idkeywords."<br>";
		$fortranslate = substr($contentfortranslation, 0, 9000);//2000
		$content = $this->scraper($langs, $fortranslate, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
		
		//print_r($content);
		foreach($content as $lang=>$source){
			${$lang} = $source;
		}
	
		if(!empty(${'content_'.$row->language})){
			
			logger($row->idkeywords, $row->original, 'Translated website to '.$row->language, $this->link);
			
			if(!empty($info->wordpress_url)){
				${'content_'.$row->language} = preg_replace('/Arizona Arizona/i', '[includeme file="topfix_'.$row->language.'.php"]', ${'content_'.$row->language});
				${'content_'.$row->language} = preg_replace('/Arkansas Arkansas/i', '[includeme file="middlefix_'.$row->language.'.php"]', ${'content_'.$row->language});
				${'content_'.$row->language} = preg_replace('/Maryland Maryland/i', '[includeme file="bottomfix_'.$row->language.'.php"]', ${'content_'.$row->language});
			} else {
				${'content_'.$row->language} = preg_replace('/Arizona Arizona/i', '%top_'.$row->language.'%', ${'content_'.$row->language});
				${'content_'.$row->language} = preg_replace('/Arkansas Arkansas/i', '%middle_'.$row->language.'%', ${'content_'.$row->language});
				${'content_'.$row->language} = preg_replace('/Maryland Maryland/i', '%bottom_'.$row->language.'%', ${'content_'.$row->language});	
			}
			${'content_'.$row->language} = preg_replace('/Florida Florida/i', '%date%', ${'content_'.$row->language});
			${'content_'.$row->language} = str_replace('toc-icon-toggle"></p>', 'toc-icon-toggle"></i>', ${'content_'.$row->language});
			mysqli_query($this->link, "UPDATE `website_translation` SET `content`='".mysqli_real_escape_string($this->link, ${'content_'.$row->language})."',`translated`='1' WHERE `id`='".$row->id."'");
			
		}
		unset($langs);
		echo "<hr>";
	}
	}
	$sel = mysqli_query($this->link, "SELECT * FROM `website_translation` WHERE `translated`='1' and `translated_slug`='0' LIMIT 1");
	if(mysqli_num_rows($sel) > 0){
	while($row = mysqli_fetch_object($sel)){
		//
		mysqli_query($this->link, "UPDATE `website_translation` set `translated_slug`='2' where `id`='".$row->id."'");
		
		$select_info = mysqli_query($this->link, "SELECT * FROM `keywords` WHERE `idkeywords`='".$row->idkeywords."'");
		$info = mysqli_fetch_object($select_info);
		
		logger($row->id, $row->original, 'Translating website to '.$row->language.' - url slug', $this->link);

		$contentfortranslation = $info->original;

		$langs = array('en|'.$row->language);
		echo "keyword id: #".$row->idkeywords."<br>";
		$fortranslate = substr($contentfortranslation, 0, 9000);//2000
		$content = $this->scraper($langs, $fortranslate, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
		
		//print_r($content);
		foreach($content as $lang=>$source){
			${$lang} = $source;
		}
	
		if(!empty(${'content_'.$row->language})){
			
			logger($row->idkeywords, $row->original, 'Translated website to '.$row->language.' - url slug', $this->link);
			
			mysqli_query($this->link, "UPDATE `website_translation` SET `slug`='".mysqli_real_escape_string($this->link, url_slug(${'content_'.$row->language}))."',`translated_slug`='1' WHERE `id`='".$row->id."'");
			
		}
		unset($langs);
		echo "<hr>";
	}
	}
	
	$sel = mysqli_query($this->link, "SELECT * FROM `website_translation` WHERE `translated_title`='0' and `translated_slug`='1' LIMIT 1");
	if(mysqli_num_rows($sel) > 0){
	while($row = mysqli_fetch_object($sel)){
		//
		mysqli_query($this->link, "UPDATE `website_translation` set `translated_title`='2' where `id`='".$row->id."'");
		
		$select_info = mysqli_query($this->link, "SELECT * FROM `built` WHERE `idkeywords`='".$row->idkeywords."'");
		$info = mysqli_fetch_object($select_info);
		
		logger($row->id, $row->original, 'Translating website title to '.$row->language, $this->link);

		$contentfortranslation = $info->title;

		$langs = array('en|'.$row->language);
		echo "keyword id: #".$row->idkeywords."<br>";
		$fortranslate = substr($contentfortranslation, 0, 9000);//2000
		$content = $this->scraper($langs, $fortranslate, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
		
		//print_r($content);
		foreach($content as $lang=>$source){
			${$lang} = $source;
		}
	
		if(!empty(${'content_'.$row->language})){
			
			logger($row->idkeywords, $row->original, 'Translated website title to '.$row->language, $this->link);
			
			mysqli_query($this->link, "UPDATE `website_translation` SET `title`='".mysqli_real_escape_string($this->link, ${'content_'.$row->language})."',`translated_title`='1' WHERE `id`='".$row->id."'");
			
		}
		unset($langs);
		echo "<hr>";
	}
	}

}
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
					if(!$this->isCapital($split[$i-1])){
						//check for capital
					$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($split[$i-1]))."'");
					if(mysql_num_rows($select) > 0){
						$row1 = mysql_fetch_object($select);
						//echo "{".$row1->spin."}";
						$split[$i-1]= "{".$row1->spin."}";
					} else {
						if(!$this->isCapital($split[$i+1])){
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
					if(!$this->isCapital($word1)){
						//select regular word
						$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($word1))."'");
						if(mysql_num_rows($select) > 0){
							$row1 = mysql_fetch_object($select);
							//echo "{".$row1->spin."}";
							$split[$i]= str_replace("%word%","{".$row1->spin."}", $word2);
						} else {
							/// if word not in thesaurus go to no 5
							if(!$this->isCapital($split[$i-1])){
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
										if(!$this->isCapital($split[$i-2])){
											$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($split[$i-2]))."'");
											if(mysql_num_rows($select) > 0){
												$row1 = mysql_fetch_object($select);
												//echo "{".$row1->spin."}";
												$split[$i-2]= "{".$row1->spin."}";
											}
										}//end isCapital
										//echo "final";
									} else {
										if(!$this->isCapital($split[$i+1])){
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
					if(!$this->isCapital($word1)){
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
		$sentences[0] = str_replace('z. B.', 'z.B.', $this->links($sentences[0]));
		$newtext.=str_replace("</i>", "</p>", str_replace("</ ", "</",$sentences[0]));
		
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
		//do 5 tries per language
		if(!empty($content)){
			for($i = 1; $i < 6; $i++){
				echo "from:".$from." to ".$to."<br>TRY #".$i."<br>";
				if($to != "en"){
					$source = $this->translate($from, $to, $content, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
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
							$source1 = $this->translate($from, $to, $chunk, $PROXY, $PROXY_USER_PASSWORD, $PROXY_IP_PORT);
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
	
	}
?>