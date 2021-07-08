<?php
class translated_posts_upload{

function __construct($link){
	$this->link = $link;
	echo "inside translated_posts_upload constructor<br>\n";
	
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
	$get_templates = mysqli_query($this->link, "SELECT * FROM `websites`");
	$templates = array();
	while($website = mysqli_fetch_object($get_templates)){
		for($i = 1; $i < 6; $i++){
			$ftp[$website->id] = $website->ftp;
			$ftpuser[$website->id] = $website->ftpuser;
			$ftppassword[$website->id] = $website->ftppassword;
			$wordpress[$website->id] = $website->wordpress_url;
			$wordpress_categories[$website->id] = $website->wordpress_categories;
			$wordpress_post_tags[$website->id] = $website->wordpress_post_tags;
			$mysql[$website->id] = array($website->db, $website->user, $website->pass);
		}
	}
	
	$select = mysqli_query($this->link, "SELECT * FROM `website_translation` WHERE `uploaded`='0' LIMIT 1");
	while($row = mysqli_fetch_object($select)){
		mysqli_query($this->link, "UPDATE `website_translation` SET `uploaded`='2' WHERE `id`='".$row->id."'");
		
		$content = $row->content;		
		//replace bad characters ? 
		$content = str_replace('�', ' ', $content);
		//replace hex encoded e.g. \x27
		$content = preg_replace_callback(
		  "(\\\\x([0-9a-f]{2}))i",
		  function($a) {return chr(hexdec($a[1]));},
		  $content
		);
		//
				//if website is not WORDPRESS
				if(empty($wordpress[$row->website])){
					//connect to remote database
					$conn1 = mysqli_connect("localhost", htmlentities($mysql[$row->website][1]), htmlentities($mysql[$row->website][2]), htmlentities($mysql[$row->website][0]));
					if($conn1 === false){
						//if failed to connect
						echo "Post ID: ".$row->id." FAILED WITH STATUS: ERROR: Could not connect to remote database.<br>";
						logger($row->idkeywords, $row->original, 'Upload '.$row->language.' failed: can\'t connect to remote db', $this->link);
					} else {
						//on successfull remote connection
						
						$exists = mysqli_query($conn1, "SELECT * FROM `pages` WHERE `identifier`='".$row->slug."'");
						//check if post exists
						if(mysqli_num_rows($exists) < 1){
							//insert into remote
							$ins = mysqli_query($conn1, "INSERT INTO `pages`(`idkeywords`,`language`,`keyword`,`identifier`,`author`,`title`,`html`,`excerpt`,`keywordtop1`,`keywordtop2`,`keywordtop3`,`keywordtop4`,`keywordtop5`,`keywordtop6`,`keywordtop7`,`keywordtop8`,`keywordtop9`,`keywordtop10`,`keywordtop11`,`keywordtop12`,`keywordtop13`,`keywordtop14`,`keywordtop15`,`keywordtop16`) VALUES('".$row->idkeywords."', '".$row->language."', '".$row->original."', '".$row->slug."', '".$row->author."','".mysqli_real_escape_string($conn1, $row->title)."', '".mysqli_real_escape_string($conn1, $content)."', '".mysqli_real_escape_string($conn1, $excerpt)."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop1))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop2))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop3))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop4))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop5))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop6))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop7))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop8))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop9))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop10))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop11))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop12))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop13))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop14))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop15))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop16))."')");
							if($ins){
								echo "Post ID: ".$row->idkeywords." SUCCESS.<br>";
								//if success to remote -> mark as uploaded
								mysqli_query($this->link, "UPDATE `website_translation` SET `uploaded`='1' WHERE `id`='".$row->id."'");
								logger($row->idkeywords, $row->original, 'Upload '.$row->language.' success.', $this->link);
							} else {
								//if fail to remote
								echo "Post ID: ".$row->id." FAILED WITH STATUS: Failed to add post to remote database. It doesn\'t exist, error at inserting.<br>";
								logger($row->idkeywords, $row->original, 'Upload '.$row->language.' failed: can\'t add to remote db, it doesn\'t exist there', $this->link);
							}
						} else {
							//if exists at remote
							echo "Post ID: ".$row->id." FAILED WITH STATUS: Post already exists into remote table.<br>";
							logger($row->idkeywords, $row->original, 'Upload '.$row->language.' failed: it exists into remote db', $this->link);
						}
						
						//close connection
						mysqli_close($conn1);
					}
				
				} else {
					//if website is wordpress
					if($wordpress_post_tags[$row->website] == 1){
						$post_tags = url_slug($row->keywordtop1).','.url_slug($row->keywordtop2).','.url_slug($row->keywordtop3).','.url_slug($row->keywordtop4).','.url_slug($row->keywordtop5).','.url_slug($row->keywordtop6).','.url_slug($row->keywordtop7).','.url_slug($row->keywordtop8).','.url_slug($row->keywordtop9).','.url_slug($row->keywordtop10).','.url_slug($row->keywordtop11).','.url_slug($row->keywordtop12).','.url_slug($row->keywordtop13).','.url_slug($row->keywordtop14).','.url_slug($row->keywordtop15).','.url_slug($row->keywordtop16);
					} else {
						$post_tags = "";
					}
					$add = $this->add_post($wordpress[$row->website].'/wp-content/add_posts.php', $ptitle, mysqli_real_escape_string($this->link, $content), $authorid_wordpress, url_slug($row->original), $post_tags, $wordpress_categories[$row->website], $filename);
					if($add == '1'){
							echo "Post ID: ".$row->idkeywords." SUCCESS.<br>";
							//if success to remote -> mark as uploaded
							mysqli_query($this->link, "UPDATE `website_translation` SET `uploaded`='1' WHERE `id`='".$row->id."'");
							logger($row->idkeywords, $row->original, 'Upload '.$row->language.' success wordpress.', $this->link);
						} else {
							//if fail to remote
							echo "Post ID: ".$row->idkeywords." FAILED WITH STATUS: ".$add."<br>";
							logger($row->idkeywords, $row->original, 'Upload '.$row->language.' failed wordpress: '.$add, $this->link);
						}					
				}
		}
	
}

function add_post($url, $ptitle, $content, $user_id, $slug, $post_tags, $categories, $file){
	  $ch = curl_init();
	  $fields = array( 'pass'=> 'damnpassword123!@#', 'ptitle'=>$ptitle, 'content'=>$content, 'userid'=>$user_id, 'slug'=>$slug, 'tags'=>str_replace('-', ' ', $post_tags), 'categories'=>$categories, 'file'=>$file);
	  $postvars = '';
	  foreach($fields as $key=>$value) {
		$postvars .= $key . "=" . $value . "&";
	  }
	  curl_setopt($ch,CURLOPT_URL, $url);
	  curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
	  curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);
	  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 3);
	  curl_setopt($ch,CURLOPT_TIMEOUT, 20);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	  $response = curl_exec($ch);
	  curl_close ($ch);	
	  
	  return $response;
}

}
	
	
?>