<?php
class rewrite_step2{

function __construct($link){
	$this->link = $link;
	echo "inside rewrite_step2 constructor<br>\n";
	
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
			
	$sel = mysqli_query($this->link, "SELECT * FROM `rewrite_steps` WHERE `step1_completed`='1' and `step2_completed`='0' LIMIT 1");
	
	while($row = mysqli_fetch_object($sel)){
		
		mysqli_query($this->link, "UPDATE `rewrite_steps` SET `step2_completed`='2' WHERE `idkeywords`='".$row->idkeywords."'");
		
		logger($row->idkeywords, $row->original, 'Rewrite steps... 2', $this->link);
		echo "<hr>keyword id:".$row->idkeywords."<br>";
		
		$select_steps = mysqli_query($this->link, "SELECT * FROM `websites` WHERE `id`='".$row->website."'");
		$result = mysqli_fetch_object($select_steps);
		

		$serp_content = $row->step1_text_final;
		$original_text = $row->original_text;
		
		
		$step_2_setting = explode('|', $result->rewrite_step_2);
		$step_2 = $step_2_setting[0];
		$step_2_options = $step_2_setting[1];
		$step_2_langs = explode(';', $step_2_options);
		
		//if 2nd step is Wordtune
		if($step_2 == 1){
			if($step_2_options == 1){
				//add tags to qa and snippet so it can be detected
				foreach($inserts as $insert){
					$serp_content = str_replace($insert, "\n<".strtolower($insert).">", $serp_content);
				}
				//
				$detect_sentences = $this->html($serp_content);
				foreach($detect_sentences as $sentence){
					$insert = mysqli_query($this->link, "INSERT INTO `rewrite_wordtune`(`idkeywords`, `content`, `step`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($this->link, $sentence)."', '1')");
					$serp_content = str_replace($sentence.". ", '[#'.mysqli_insert_id($this->link).'#]', $serp_content);
					$serp_content = str_replace($sentence, '[#'.mysqli_insert_id($this->link).'#]', $serp_content);
					echo $sentence."<br>";
				}
			} elseif($step_2_options == 2){
				//first sentence per paragraph
				preg_match_all('/<p>(.*[.?!:])\s?/U', $serp_content, $matches);
				foreach($matches[1] as $match){
					$insert = mysqli_query($this->link, "INSERT INTO `rewrite_wordtune`(`idkeywords`, `content`, `step`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($this->link, $match)."', '2')");
					$serp_content = str_replace($match, '[#'.mysqli_insert_id($this->link).'#]', $serp_content);
					echo $match."<br>";
				}
			} elseif($step_2_options == 3){
				//just headings
				preg_match_all('/<h([2-3])>(.*)<\/h[2-3]>/sU', $serp_content, $matches);
				foreach($matches[2] as $match){
					$insert = mysqli_query($this->link, "INSERT INTO `rewrite_wordtune`(`idkeywords`, `content`, `step`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($this->link, $match)."', '2')");
					$serp_content = str_replace($match, '[#'.mysqli_insert_id($this->link).'#]', $serp_content);
					echo $match."<br>";
				}
			} elseif($step_2_options == 4){
				//first sentence per paragraph & headings
				preg_match_all('/<p>(.*[.?!:])\s?/U', $serp_content, $matches);
				foreach($matches[1] as $match){
					$insert = mysqli_query($this->link, "INSERT INTO `rewrite_wordtune`(`idkeywords`, `content`, `step`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($this->link, $match)."', '2')");
					$serp_content = str_replace($match, '[#'.mysqli_insert_id($this->link).'#]', $serp_content);
					echo $match."<br>";
				}
				preg_match_all('/<h([2-3])>(.*)<\/h[2-3]>/sU', $serp_content, $matches);
				foreach($matches[2] as $match){
					$insert = mysqli_query($this->link, "INSERT INTO `rewrite_wordtune`(`idkeywords`, `content`, `step`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($this->link, $match)."', '2')");
					$serp_content = str_replace($match, '[#'.mysqli_insert_id($this->link).'#]', $serp_content);
					echo $match."<br>";
				}
			} elseif($step_2_options == 5){
				//first intro paragraph /@old <p>(.*)<\/p>
				preg_match('/(<p>.*<\/p>)/Us', $serp_content, $matches);
				if(!empty($matches[1])){
					$detect_sentences = $this->html($matches[1]);
					foreach($detect_sentences as $sentence){
						$insert = mysqli_query($this->link, "INSERT INTO `rewrite_wordtune`(`idkeywords`, `content`, `step`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($this->link, $sentence)."', '1')");
						$serp_content = str_replace($sentence.". ", '[#'.mysqli_insert_id($this->link).'#]', $serp_content);
						$serp_content = str_replace($sentence, '[#'.mysqli_insert_id($this->link).'#]', $serp_content);
						echo $sentence."<br>";
					}
				}
				//echo $matches[1]."<br>";
				
			} elseif($step_2_options == 6){
				//"Just snippets/PAA"
				//complicated, left out for now
			}
			
			mysqli_query($this->link, "UPDATE `rewrite_steps` SET `step2_text`='".mysqli_real_escape_string($this->link, $serp_content)."', `wordtune`='2' WHERE `idkeywords`='".$row->idkeywords."'");
				
		} elseif($step_2 == 2){
			//if 2nd step is Spintax
			$text_final = $this->checkspin($serp_content, $step_2_options);
			
			$differences = Diff::compare($original_text, $text_final);
			$difference = 100-(Diff::toTable1($differences[0], $differences[1]));
			
						
			mysqli_query($this->link, "UPDATE `rewrite_steps` SET `step2_text`='".mysqli_real_escape_string($this->link, $serp_content)."', `step2_text_final`='".mysqli_real_escape_string($this->link, $text_final)."', `step2_uniqueness`='".$difference."', `step2_completed`='1' WHERE `idkeywords` = '".$row->idkeywords."'");
		} elseif($step_2 == 3){
			//if 2nd step is GTR
			mysqli_query($this->link, "UPDATE `rewrite_steps` SET `step2_text`='".mysqli_real_escape_string($this->link, $serp_content)."' WHERE `idkeywords` = '".$row->idkeywords."'");
			$insert = mysqli_query($this->link, "INSERT INTO `rewrite_translate`(`idkeywords`, `feed_text`, `lang1_code`, `lang2_code`, `lang3_code`, `step`) VALUES('".$row->idkeywords."', '".mysqli_real_escape_string($this->link, $serp_content)."', '".$step_2_langs[0]."', '".$step_2_langs[1]."', '".$step_2_langs[2]."', '2')");
			
		}
		
		}
				
	}


function html($html){
	$html = preg_replace("/(<[a-z0-9\/]+>)(\s+)(\w)/", "$1$3", $html); //replace new line after tag opening
	
	//original: /<.>(.*)<\/.>/U
	//it works but it is unsure of img src etc tags: /<.+>(.*)<\/?.+>/U 
	//unaware of <ul><li>: /<[a-z\/]+>(.*)<[a-z\/]+>/U
	//not working with tagspace: <a> text</a>:    preg_match_all('/<[a-z\/]+>(\w.*)<[a-z\/]+>/U', $html, $matches);
	preg_match_all('/<[a-z0-9\/]+>(\w.*)<[a-z0-9\/]+>/sU', $html, $matches);
	
	//print_r($matches);
	
	$sentences = array();
	foreach($matches[1] as $match){
		if($match != strip_tags($match)) {
			// contains HTML
		} else {
			if(strlen($match) > 30){
				$multiple_sentences = explode(". ", $match);
				foreach($multiple_sentences as $sentence){
					if(strlen($sentence) > 30){
						$sentences[] = $sentence;
					}
				}
			}
		}
	}
	
	//print_r($sentences);
	return $sentences;
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
}
?>