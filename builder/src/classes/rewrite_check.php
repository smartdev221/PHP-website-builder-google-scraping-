<?php
class rewrite_check{

function __construct($link){
	$this->link = $link;
	echo "inside rewrite_check constructor<br>\n";
	
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
			
	$sel = mysqli_query($this->link, "SELECT * FROM `keywords` WHERE `rewrite_process`='2' LIMIT 10");
	
	while($row = mysqli_fetch_object($sel)){

		echo "<br><hr>keyword id:".$row->idkeywords."<br>";


		$select_rewrite = mysqli_query($this->link, "SELECT * FROM `rewrite_steps` WHERE `idkeywords`='".$row->idkeywords."'");
		if(mysqli_num_rows($select_rewrite) > 0){
			logger($row->idkeywords, $row->original, 'Rewrite check', $this->link);
			$row_rewrite = mysqli_fetch_object($select_rewrite);
			//make sure all steps are done
			if($row_rewrite->step1_completed == 1 && $row_rewrite->step2_completed == 1 && $row_rewrite->step3_completed == 1){
				//setting first step as most unique
				$uniqueness = $row_rewrite->step1_uniqueness;
				$text = $row_rewrite->step1_text_final;
				if($row_rewrite->step2_uniqueness > $uniqueness){
					//checking if any other step is more unique
					$uniqueness = $row_rewrite->step2_uniqueness;
					$text = $row_rewrite->step2_text_final;
				}elseif($row_rewrite->step3_uniqueness > $uniqueness){
					$uniqueness = $row_rewrite->step3_uniqueness;
					$text = $row_rewrite->step3_text_final;
				}
				
				// extract qa and snippet for the most unique step, same as serp content
				$linkages = explode(";", $row_rewrite->linkage);
				foreach($linkages as $linkage){						
						$expl = explode("|", $linkage);
						if(!empty($expl[0])){
							preg_match("/".$expl[0]."(.*)".$expl[0]."/sU", $text, $match);
							if(!empty($match[1]) && $expl[0]!= "Hawaii"){
								
								mysqli_query($this->link, "UPDATE `qa` SET `answer_en`='".mysqli_real_escape_string($this->link, $match[1])."', `translated`='1' WHERE `id`='".$expl[1]."'");
								$text = preg_replace("/".$expl[0]."(.*)".$expl[0]."/sU", "", $text);
								
							}elseif($expl[0] == "Hawaii" && !empty($match[1])){
								
								mysqli_query($this->link, "UPDATE `featuredsnippets` SET `snippetcontent_en`='".mysqli_real_escape_string($this->link, $match[1])."', `translated`='1' WHERE `id`='".$expl[1]."'");
								$text = preg_replace("/".$expl[0]."(.*)".$expl[0]."/sU", "", $text);
								
							}
						}
				}
				//
				mysqli_query($this->link, "UPDATE `keywords` SET `rewrite_process`='1', `translated_serp`='1', `last_action`='rewrite_check', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
				mysqli_query($this->link, "UPDATE `scraped_content_serp` SET `content_en`='".mysqli_real_escape_string($this->link, trim($text))."',`translated`='1' WHERE `idkeywords`='".$row->idkeywords."'");

			}
		}				
		}
				
	}
	

}

?>