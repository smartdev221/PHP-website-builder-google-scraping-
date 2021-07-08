<?php
class wordtune_check{

function __construct($link){
	$this->link = $link;
	echo "inside wordtune_check constructor<br>\n";

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
			
	$sel = mysqli_query($this->link, "SELECT * FROM `rewrite_steps` WHERE `wordtune`='2' LIMIT 1");
	
	while($row = mysqli_fetch_object($sel)){
		echo "<br>keyword id:".$row->idkeywords."<br>";
		
		$look = mysqli_query($this->link, "SELECT * FROM `rewrite_wordtune` where `idkeywords`='".mysqli_real_escape_string($this->link, $row->idkeywords)."'");
			$wordtune = array();
			$new_sentences = array();
			while($sentence = mysqli_fetch_object($look)){
					$wordtune[] = $sentence->done;
				echo $sentence->id." -> ".$sentence->done."<br>";
				$step = $sentence->step;
				$new_sentences[$sentence->id] = $sentence->content_done;
			}
			$wordtune_done = 1;
			foreach($wordtune as $elm){
				if($elm == 0 OR $elm == 3){
					$wordtune_done = 0;
				}
			}
		if($wordtune_done == 1){
			echo "final result:".$wordtune_done."<br>";
			
			$content_en = str_replace('<arizona>', 'Arizona', $row->{'step'.$step.'_text'});
			$content_en = str_replace('<arkansas>', 'Arkansas', $content_en);
			$content_en = str_replace('<maryland>', 'Maryland', $content_en);
			$content_en = str_replace('<florida>', 'Florida', $content_en);
			$content_en = str_replace('<texas>', 'Texas', $content_en);
			$content_en = str_replace('<hawaii>', 'Hawaii', $content_en);
			
			
			//replace inserted tags with rewritten content:
			preg_match_all('/\[#(\w*)#\]/U', $content_en, $matches);
			foreach($matches[1] as $tag){
				$content_en = str_replace('[#'.$tag.'#]', $new_sentences[$tag], $content_en);
			}
			//

			$differences = Diff::compare($row->original_text, $content_en);
			$difference = 100-(Diff::toTable1($differences[0], $differences[1]));
			mysqli_query($this->link, "UPDATE `rewrite_steps` SET `step".$step."_text_final`='".mysqli_real_escape_string($this->link, $content_en)."',`step".$step."_uniqueness`='".$difference."', `step".$step."_completed`='1', `wordtune`='".$wordtune_done."' WHERE `idkeywords`='".$row->idkeywords."'");
			
		}
		unset($wordtune);
		unset($new_sentences);
	}
////
}
}
?>